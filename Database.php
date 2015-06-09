<?php

/**
 * Database Object
 *
 * @package BluCommerce
 * @subpackage SharedLib
 */
class Database
{
	/**
	 *	MySQL-specific error codes
	 *
	 *	@var int
	 */
	const ERROR_TABLE_EXISTS = 1050;
	const ERROR_DUPLICATE_COLUMN = 1060;
	const ERROR_DUPLICATE_INDEX = 1061;
	const ERROR_DUPLICATE_ENTRY = 1062;
	const ERROR_COLUMN_OR_INDEX_NOT_EXISTS = 1091;

	/**
	 * The database connection handle
	 *
	 * @var resource
	 */
	protected $_dbh;

	/**
	 * The query sql string
	 *
	 * @var string
	 *
	 */
	private $_sql = '';

	/**
	 * The limit for the query
	 *
	 * @var int
	 */
	private $_limit = 0;

	/**
	 * The for offset for the limit
	 *
	 * @var int
	 */
	private $_offset = 0;

	/**
	 * The last query cursor
	 *
	 * @var resource
	 */
	private $_cursor;

	/**
	 * Count of number of queries performed
	 *
	 * @var int
	 */
	private $_querycount = 0;

	/**
	 * List of queries performed
	 *
	 * @var array
	 */
	private $_querylist;

	/**
	 * File to log all queries into on the fly
	 *
	 * @var array
	 */
	private $_logFile;

	/**
	 * Whether or not to continue on error
	 *
	 * @var bool
	 */
	private $_allowErrors = false;

	/**
	 * Allow complete failure to connect to db
	 *
	 * @var bool
	 */
	private $_allowDbFail = false;


	/**
	 * List of all errors
	 *
	 * @var array
	 */
	private $_errorStack;

	/**
	 * Array of all database hosts
	 *
	 * @var array
	 */
	private $_databases;

	/**
	 * The connected state of the database
	 *
	 * @var bool
	 */
	private $_connected = false;

	/**
	 * Specific database host to use from databases array - principally used for watchdog
	 *
	 * @var int
	 */
	private $_specificIndex = false;

	/**
	 * The database username
	 *
	 * @var string
	 */
	private $_user;

	/**
	 * The database password
	 *
	 * @var string
	 */
	private $_pass;

	/**
	 * The database host
	 *
	 * @var string
	 */
	private $_host;

	/**
	 * The database name
	 *
	 * @var string
	 */
	private $_name;

	/**
	 * @var string The timezone for use with this connection
	 */
	private $_timezone;

	/**
	 *	Database object constructor
	 *
	 *	@access protected
	 *	@param $databases Array Information for all db hosts
	 *	@param $specificIndex Int Are we trying to connect to a specific database host?
	 */
	protected function __construct($databases, $specificIndex = false)
	{

		// Do initial setup.
		$this->_allowErrors = false;
		$this->_allowDbFail = false;
		$this->_errorStack = Array();
		$this->_specificIndex = $specificIndex;

		if ($specificIndex === false) {
			shuffle($databases);
		}

		$this->_databases = $databases;

		if (defined(LOG_QUERIES)) {
			if (LOG_QUERIES == true) {
				$this->_logFile = fopen (BLUPATH_CACHE.'/DatabaseLog.txt', 'a+');
				fwrite($this->_logFile, '#################'.time().' - '.$_SERVER['REQUEST_URI'].'################'."\n");
			}
		}
	}

	/**
	 *	Connect to the server and do setup of various important things (timezone, charset, etc.)
	 *
	 *	@access protected
	 */
	protected function _connectSelectAndSet() {
		// Set active database.
		$this->_dbh = $this->_connect($this->_host, $this->_user, $this->_pass);

		if ($this->_dbh === false) {
			return false;
		}

		$selected = $this->_selectDatabase($this->_name);

		if ($selected === false) {
			return false;
		}


		$this->_connected = true;

		// Set encoding.
		$this->_setEncoding('utf8');

		// Set timezone to GMT here as a safe default. This will need to be manually set later
		// using the public method Database->setTimezone($timezone);
		$this->_setTimezone('GMT');

		return $this->_dbh;
	}

	protected function _selectDbFromPoolAndConnect() {
		if ($this->_specificIndex) {
			$this->_databases = Array ($this->_databases[$this->_specificIndex]);
		}

		foreach ($this->_databases as $dbKey=>$server) {
			$this->_host = $server['databaseHost'];
			$this->_user = $server['databaseUser'];
			$this->_pass = $server['databasePass'];
			$this->_name = $server['databaseName'];
			$con = $this->_connectSelectAndSet();
			if (!($con === false)) {
				return true;
			}
		}

		if ((mysql_errno() == 2002 || mysql_errno() == 0)  && $this->_allowDbFail == true && CLI == true) { // This only ever happens with the watchdog
			Utility::irc_dump(BluCommerce::getEnvironment()->getSiteId().': Failed to connect to database, restarting...', '#suicidewatch');
			exec ('/etc/init.d/mysql restart');
			sleep(10);
			$handle = $this->_selectDbFromPoolAndConnect();
			if ($handle) {
				return true;
			}
			return false;
		}

		// If no servers are responding, throw an exception.
		trigger_error('Failed to connect to database', E_USER_ERROR);

		return false;
	}

	/**
	 *	Connect to a database server
	 *
	 *	@access protected
	 *	@param string Database host
	 *	@param string Database user
	 *	@param string Database password
	 *	@return resource Database connection
	 */
	protected function _connect($host, $user, $pass)
	{
		return mysql_pconnect($host, $user, $pass, true);
	}

	/**
	 *	Set active database
	 *
	 *	@access protected
	 *	@param string Database name
	 *	@return bool Success
	 */
	protected function _selectDatabase($name)
	{
		return mysql_select_db($name, $this->_dbh);
	}

	/**
	 *	Set database encoding
	 *
	 *	@access protected
	 *	@param string Encoding
	 *	@return bool Success
	 */
	protected function _setEncoding($encoding)
	{
		$query = $this->_query('SET NAMES '.$this->escape($encoding));
		$set = mysql_set_charset($encoding, $this->_dbh);
		return $query && $set;
	}

	/**
	 *	Set timezone
	 *
	 *	@access protected
	 *	@return bool Success
	 */
	protected function _setTimezone($timezone)
	{
		return $this->_query('SET time_zone = "'.$this->escape($timezone).'"');
	}

	/**
	 * Set the timezone which will be run as a query in the next one run by mysql
	 * @param string $timezone
	 * @return Database Self (fluid interface)
	 */
	public function setTimezone($timezone)
	{
		$this->_timezone = $timezone;
		return $this;
	}

	/**
	 * Returns a reference to the global Database object, only creating it
	 * if it doesn't already exist
	 *
	 * @param $databases Array Array of database hosts
	 * @param $specificIndex int Specific index of databases to use
	 * @return Database A database object
	 */
	public static function getInstance($databases, $specificIndex = false)
	{
		static $instances;
		if (!isset($instances)) {
			$instances = array();
		}

		$args = func_get_args();
		$signature = serialize($args);

		if (empty($instances[$signature])) {
			$c = __CLASS__;
			$instances[$signature] = new $c($databases, $specificIndex);
		}
		return $instances[$signature];
	}

	/**
	 * Sets the SQL query string for later execution
	 *
	 * @param string The SQL query
	 * @param string The offset to start selection
	 * @param string The number of results to return
	 * @return Database Self (fluid interface)
	 */
	public function setQuery($sql, $offset = 0, $limit = 0, $calcRows = false)
	{
		// Replace start of query (SELECT) with SELECT SQL_CALC_FOUND_ROWS
		if ($calcRows) {
			$sql = substr_replace($sql, 'SELECT SQL_CALC_FOUND_ROWS', 0, 6);
		}
		$this->_sql = $sql;
		$this->_limit = (int)$limit;
		$this->_offset = (int)$offset;
		return $this;
	}

	/**
	 *	Allow errors to fall through
	 *
	 *	@access public
	 *	@param bool $allowErrors Allow
	 *	@param bool $alloTotalFail Allow Total DB failure
	 */
	public function allowErrors($allowErrors = true, $allowTotalFail = false)
	{
		$this->_allowErrors = $allowErrors;
		$this->_allowDbFail = $allowTotalFail;
	}

	/**
	 *	Get the error stack
	 *
	 *	@access public
	 *	@return array
	 */
	public function returnErrorStack()
	{
		return $this->_errorStack;
	}

	/**
	 *	Get last error code
	 *
	 *	@access public
	 *	@param string Key to return
	 *	@return mixed
	 */
	public function lastError($key = null)
	{
		// Get last error
		$stack = $this->_errorStack;
		if (!$lastError = end($stack)) {
			return false;
		}

		// Looking for something?
		if (isset($lastError[$key])) {
			return $lastError[$key];
		}

		// Return
		return $lastError;
	}

	/**
	 * Execute the query
	 *
	 * @return mixed A database resource if successful, FALSE if not.
	 */
	public function query()
	{
		// Increment query counter
		$this->_querycount++;

		// Add limit and offset if we have them
		if ($this->_limit > 0 || $this->_offset > 0) {
			$this->_sql.= ' LIMIT '.$this->_offset.', '.$this->_limit;
		}

		// Start timer
		if (DEBUG && DEBUG_INFO) {
			$time = microtime();
			$time = explode(' ', $time);
			$time = $time[1] + $time[0];
			$start = $time;
		}

		// Retrieve resource
		$this->_cursor = @$this->_query($this->_sql);

		if (DEBUG && DEBUG_INFO) {
			$time = microtime();
			$time = explode(' ', $time);
			$time = $time[1] + $time[0];
			$end = $time;
			$formattedSQL =  $this->_sql." <br/>#### ".round(($end - $start), 5);
			$this->_querylist[] = $formattedSQL;
			if (defined(LOG_QUERIES)) {
				if (LOG_QUERIES == true) {
					fwrite($this->_logFile, str_replace('<br/>', "\n", $formattedSQL));
				}
			}
		}

		if (!$this->_cursor) {
			if ($this->_allowErrors == true) {
				$this->_errorStack[] = array(
					'error' => $this->_error(),
					'code' => $this->_errno(),
					'query' => $this->_sql
				);
			} else {
				throw new DatabaseException('Database error: '.PHP_EOL.'	'.$this->_error().PHP_EOL.'	Query: '.$this->_sql.PHP_EOL, E_USER_ERROR);
			}
			return false;
		}
		return $this->_cursor;
	}

	/**
	 *	Query
	 *
	 *	@access protected
	 *	@param string Query
	 *	@return mixed Result
	 */
	protected function _query($statement)
	{
		// Just in time connection
		if ($this->_connected == false) {
			$success = $this->_selectDbFromPoolAndConnect();
			if ($success == false) {
				return false;
			}
		}

		// If timezone has been set, update it.
		if(isset($this->_timezone)) {
			$timezone = $this->_timezone;
			$this->_timezone = null;
			$this->_setTimezone($timezone);
		}

		$result = mysql_query($statement, $this->_dbh);

		if ($result == false) {
			$error = mysql_errno();
			if ($error == 2013 || $error == 2006 || $error == 2002) {
				$this->_connected = false;
				$success = $this->_selectDbFromPoolAndConnect();
				if ($success == false) {
					return false;
				}
				$result = mysql_query($statement, $this->_dbh);
			}
		}

		return $result;
	}

	/**
	 *	Error text from previous operation
	 *
	 *	@access protected
	 *	@return string
	 */
	protected function _error()
	{
		if ($this->_dbh) {
			return mysql_error($this->_dbh);
		} else {
			return false;
		}
	}

	/**
	 *	Error code from previous operation
	 *
	 *	@access protected
	 *	@return int
	 */
	protected function _errno()
	{
		if ($this->_dbh) {
			return mysql_errno($this->_dbh);
		} else {
			return false;
		}
	}

	/**
	 * Insert multiple rows into a table
	 *
	 *
	 *
	 * @param string Table name to update
	 * @param array Associative array of key-value pairs
	 * @param array Associative array of column data types (where primary(s) are used as the update key(s))
	 * 				key => array('datatype' => 'varchar(255)',
	 * 							 'primary' => bool)
	 */
	public function updateMany($tableToUpdate, $updateData, $dataTypes)
	{
		if (empty($updateData)) {
			return false;
		}

		$keys = array_keys(reset($updateData));
		$tempTableName = $tableToUpdate.time();

		// Clear any existing limits and offsets
		$this->_limit = 0;
		$this->_offset = 0;

		// Create the temporary table according to the provided schema
		$columns = Array();
		foreach ($keys as $key) {
			$columns[] = $key.' '.$dataTypes[$key]['datatype'];
		}

		$this->_sql = 'CREATE TEMPORARY TABLE '.$tempTableName.' (';
		$this->_sql .= implode(' ,', $columns).')';
		if (!$this->query()) {
			return false;
		}

		// Smack our data into the temporary table
		foreach ($updateData as $dataRow) {
			$dataRows[] = '("'.implode ('","', $dataRow).'")';
		}

		$this->_sql = 'INSERT INTO '.$tempTableName.' ('.implode(',',$keys).') VALUES ';
		$this->_sql .= implode (',', $dataRows);
		if (!$this->query()) {
			return false;
		}

		// Update the db from the temporary table
		$updateElements = Array();
		foreach ($dataTypes as $keyName => $dataTypeRow) {
			if ($dataTypeRow['primary'] == false) {
				$updateElements[] = 'm.'.$keyName.' = t.'.$keyName.' ';
			} else {
				$whereClauses[] = 'm.'.$keyName.' = t.'.$keyName;
			}
		}

		$this->_sql = 'UPDATE '.$tableToUpdate.' m, '.$tempTableName.' t SET ';
		$this->_sql .= implode(',', $updateElements).' WHERE '.implode (' AND ', $whereClauses);
		if (!$this->query()) {
			return false;
		}

		// And tidy up after ourselves
		$this->_sql = 'DROP TEMPORARY TABLE '.$tempTableName;
		if (!$this->query()) {
			return false;
		}

		// All done, ma!
		return true;
	}

	/**
	 * Get ID of last insert
	 *
	 * @return mixed Insert ID
	 */
	public function getInsertID()
	{
		return mysql_insert_id($this->_dbh);
	}

	/**
	 *	Free result memory
	 *
	 *	@access protected
	 *	@param resource Resultset
	 *	@return bool Success
	 */
	protected function _freeResult($result)
	{
		return mysql_free_result($result);
	}

	/**
	 *	Fetch the next enumerated row of results
	 *
	 *	@access protected
	 *	@param resource Resultset
	 *	@return array
	 */
	protected function _fetchRow($result)
	{
		return mysql_fetch_row($result);
	}

	/**
	 *	Fetch the next row of results as an associative array
	 *
	 *	@access protected
	 *	@param resource Resultset
	 *	@return array
	 */
	protected function _fetchAssoc($result)
	{
		return mysql_fetch_assoc($result);
	}

	/**
	 * This method loads the first field of the first row returned by the query.
	 *
	 * @return The value returned in the query or null if the query failed.
	 */
	public function loadResult()
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($row = $this->_fetchRow($cur)) {
			$ret = $row[0];
		}
		$this->_freeResult($cur);

		return $ret;
	}

	/**
	 * Load an array of single field results into an array
	 */
	public function loadResultArray($numinarray = 0)
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = $this->_fetchRow($cur)) {
			$array[] = $row[$numinarray];
		}
		$this->_freeResult($cur);

		return $array;
	}

	/**
	 * Load an array of single field results into an array by key
	 */
	public function loadResultAssocArray($keyColumn, $valueColumn)
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = $this->_fetchAssoc($cur)) {
			$array[$row[$keyColumn]] = $row[$valueColumn];
		}
		$this->_freeResult($cur);

		return $array;
	}

	/**
	 * Fetch a result row as an associative array
	 *
	 * @return array
	 */
	public function loadAssoc()
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($array = $this->_fetchAssoc($cur)) {
			$ret = $array;
		}
		$this->_freeResult($cur);

		return $ret;
	}

	/**
	 * Load a assoc list of database rows
	 *
	 * @param string The field name of a primary key
	 * @return array List of returned records (optionally indexed by key)
	 */
	public function loadAssocList($key = '')
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = $this->_fetchAssoc($cur)) {
			if ($key) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}
		$this->_freeResult($cur);
		reset($array);

		return $array;
	}

	/**
	 *	Load associative arrays, grouped by one of its keys.
	 *
	 *	@access public
	 *	@param string Field to group by
	 *	@param string Field to key off, within grouping
	 *	@param bool|string Use row value, rather than whole row if given, or false to disable
	 *	@return array Grouped records
	 */
	public function loadGroupedAssocList($groupIndex, $keyField = false, $valueField = false)
	{
		if (!$cur = $this->query()) {
			return null;
		}
		$groups = array();
		while ($row = $this->_fetchAssoc($cur)) {
			$value = $valueField ? $row[$valueField] : $row;	// Thing to append, whole row or single row representative.
			if ($keyField) {
				$groups[(string) $row[$groupIndex]][(string) $row[$keyField]] = $value;
			} else {
				$groups[(string) $row[$groupIndex]][] = $value;
			}
		}
		$this->_freeResult($cur);

		return $groups;
	}

	/**
	 * Get number of row found with previous query
	 *
	 * @return int Number of found rows
	 */
	public function getFoundRows()
	{
		$query = 'SELECT FOUND_ROWS()';
		return mysql_result($this->_query($query), 0);
	}

	/**
	 * Get number of rows affected by the previous query
	 *
	 * @return int Number of affected rows
	 */
	public function getAffectedRows()
	{
		return mysql_affected_rows($this->_dbh);
	}

	/**
	 * Get current mysql stats
	 *
	 * @return array statistics in a kv array
	 */
	public function getStats()
	{
		$rawStats = mysql_stat();
		$stats = explode('  ', $rawStats);
		foreach ($stats as $stat) {
			list($key, $value) = explode(":", $stat);
			$output[$key] = trim($value);
		}
		$output['raw'] = $rawStats;
		return $output;
	}

	/**
	 * Get a database escaped, trimmed string
	 *
	 * @param (array/string) The (array of) string(s) to be escaped
	 * @return (array/string) Escaped (array of) string(s)
	 */
	public function escape($input)
	{
		if ($this->_connected == false) {
			$success = $this->_selectDbFromPoolAndConnect();
			if ($success == false) {
				return false;
			}
		}

		// Don't convert null values to empty strings
		if ($input === null) {
			return null;
		}

		if (is_array($input)) {
			$output = array();
			foreach ($input as $key => $element){
				$output[$key] = $this->escape($element);
			}
		} else {
			$output = mysql_real_escape_string($input, $this->_dbh);
		}
		return $output;
	}

	/**
	 *	Escape a string for a LIKE string
	 *
	 *	@access public
	 *	@param string To be escaped
	 *	@return string Escaped string
	 */
	public function escapeLike($input)
	{
		return preg_replace('/([_%])/', '\\\$1', $this->escape($input));
	}

	/**
	 * Get formatted database safe field updates
	 *
	 * @param array Array of details
	 * @return array Array of database safe field updates
	 */
	public function generateFieldUpdates($details)
	{
		foreach ($details as $key => &$value) {
			if ($value === null) {
				$value = '`'.$key.'` = NULL';
			} else {
				$value = '`'.$key.'` = "'.$this->escape($value).'"';
			}
		}
		unset($value);
		return implode(', ', $details);
	}

	/**
	 * Get database query count
	 *
	 * @return int Number of queries executed by this object
	 */
	public function getQueryCount()
	{
		return $this->_querycount;
	}

	/**
	 * Get list of queries performed
	 *
	 * @return array List of queries executed by this object
	 */
	public function getQueryList()
	{
		return $this->_querylist;
	}

}
?>
