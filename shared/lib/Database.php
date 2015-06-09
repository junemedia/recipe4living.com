<?php

/**
 * Database Object
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class Database
{
	/**
	 *	The database connection handle
	 *
	 *	@access protected
	 *	@var resource
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
	 *	Whether or not to continue on error 
	 *
	 *	@access protected
	 *	@var bool
	 */
	protected $_allowErrors;

	/**
	 *	List of all errors 
	 *
	 *	@access protected
	 *	@var array
	 */
	protected $_errorStack;

	/**
	 *	Database object constructor
	 *
	 *	@access protected
	 *	@param string Database host
	 *	@param string Database user
	 *	@param string Database password
	 *	@param string Database name
	 */
	protected function __construct($host, $user, $pass, $name)
	{

		$this->_readOnly = false;
		// Connect to server.
		$this->_dbh = $this->_connect($host, $user, $pass) or trigger_error('Failed to connect to database', E_USER_ERROR);
		if ($this->_dbh === true) trigger_error('Failed to connect to database', E_USER_ERROR);
		$this->_allowErrors = false;
		$this->_errorStack = array();
		
		// Set active database.
		$this->_selectDatabase($name);
		
		// Set encoding.
		$this->_setEncoding('utf8');
		
		// Set timezone
		$this->_setTimezone();
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
	protected static function _connect($host, $user, $pass)
	{
		return mysql_connect($host, $user, $pass);
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
		$query = mysql_query('SET NAMES '.$this->escape($encoding), $this->_dbh);
		$set = mysql_set_charset($encoding, $this->_dbh);
		return $query && $set;
	}
	
	/**
	 *	Set timezone
	 *
	 *	@access protected
	 *	@return bool Success
	 */
	protected function _setTimezone()
	{
		$timezone = BluApplication::getSetting('timeZone', 'GMT');
		return mysql_query('SET time_zone = "'.$this->escape($timezone).'"', $this->_dbh);
	}

	/**
	 * Returns a reference to the global Database object, only creating it
	 * if it doesn't already exist
	 *
	 * @param string Database host
	 * @param string Database user
	 * @param string Database password
	 * @param string Database name
	 * @return Database A database object
	 */
	public static function &getInstance($host, $user, $pass, $name)
	{
		static $instances;
		if (!isset($instances)) {
			$instances = array();
		}

		$args = func_get_args();
		$signature = serialize($args);

		if (empty($instances[$signature])) {
			$c = __CLASS__;
			$instances[$signature] = new $c($host, $user, $pass, $name);
		}
		return $instances[$signature];
	}

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
	 * Sets the SQL query string for later execution
	 *
	 * @param string The SQL query
	 * @param string The offset to start selection
	 * @param string The number of results to return
	 */
	public function setQuery($sql, $offset = 0, $limit = 0, $calc_rows = false)
	{
		if ($calc_rows){
			$parts = explode(" ", $sql);
			$instruction = array_shift($parts);
			if ($instruction == 'SELECT'){ $instruction .= ' SQL_CALC_FOUND_ROWS'; }
			array_unshift($parts, $instruction);
			$sql = implode(" ", $parts);
		}
		$this->_sql = $sql;
		$this->_limit = (int)$limit;
		$this->_offset = (int)$offset;
	}

	/**
	 *	Allow errors to fall through
	 *
	 *	@access public
	 *	@param bool Allow
	 */
	public function allowErrors($allow = true)
	{
		$this->_allowErrors = $allow;
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
	 * Execute the query
	 *
	 * @param bool Log the query.
	 * @return mixed A database resource if successful, FALSE if not.
	 */
	public function query($log = false)
	{
		// Increment query counter
		$this->_querycount++;

		// Add limit and offset if we have them
		if ($this->_limit > 0 || $this->_offset > 0) {
			$this->_sql.= ' LIMIT '.$this->_offset.', '.$this->_limit;
		}

		// Start timer
		if (DEBUG) {
			$time = microtime();
			$time = explode(' ', $time);
			$time = $time[1] + $time[0];
			$start = $time;
		}

		// Retrieve resource
		$this->_cursor = @$this->_query($this->_sql);

		if (DEBUG) {
			$time = microtime();
			$time = explode(' ', $time);
			$time = $time[1] + $time[0];
			$end = $time;
			if (QUERY_LIST) {
                $exacuteTime = round(($end - $start), 5);
                if($exacuteTime > 0.01){
                    $this->_querylist[] = "<font style='color:red;font-weight:bold;'>" . $this->_sql." <br/>#### ". $exacuteTime . "</font>";
                }else{
				    $this->_querylist[] = $this->_sql." <br/>#### ". $exacuteTime;
                }
			}
		}
		
		// Log 
		if ($log){
			$this->_log(trim($this->_sql));
		}

		if (!$this->_cursor) {
			if ($this->_allowErrors) {
				$this->_errorStack[] = array(
					'error' => $this->_error(),
					'code' => $this->_errno(),
					'query' => $this->_sql
				);
			} else {
				$errorString = 'Database error: '.PHP_EOL.' '.$this->_error().PHP_EOL.' Query: '.$this->_sql.PHP_EOL;
				$this->_log($errorString);
				exit($errorString);
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

		if ($this->_readOnly == true) {
			if (preg_match('/INSERT|REPLACE|UPDATE|DELETE/i', $statement)) {
				return true;
			}
		}
		global $queryNum;
		$queryNum++;
		if($queryNum%2 == 0){
			$color = "red";
		}else{
			$color = "blue";
		}
		//echo "<p style=\"color:$color;\">$queryNum: " . $statement . "</p></ hr>";
		return mysql_query($statement, $this->_dbh);
	}
	
	/**
	 *	Error text from previous operation
	 *
	 *	@access protected
	 *	@return string
	 */
	protected function _error()
	{
		return mysql_error($this->_dbh);
	}
	
	/**
	 *	Error code from previous operation
	 *
	 *	@access protected
	 *	@return int
	 */
	protected function _errno()
	{
		return mysql_errno($this->_dbh);
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
	 * Insert multiple rows into a table
	 *
	 * @param string Table name to update
	 * @param array Associative array of key-value pairs
	 * @param array Associative array of column data types (where primary(s) are used as the update key(s))
	 * 				key => array('datatype' => 'varchar(255)',
	 * 							 'primary' => bool)
	 */
	public function updateMany($tableToUpdate, $updateData, $dataTypes)
	{
		$keys = array_keys(reset($updateData));
		$tempTableName = $tableToUpdate.time();

		// Create the temporary table according to the provided schema
		$columns = Array();
		foreach ($keys as $key) {
			$columns[] = $key.' '.$dataTypes[$key]['datatype'];
		}

		$this->_sql = 'CREATE TEMPORARY TABLE '.$tempTableName.' (';
		$this->_sql .= implode(' ,', $columns).')';

		$this->query();


		// Smack our data into the temporary table
		foreach ($updateData as $dataRow) {
			$dataRows[] = '("'.implode ('","', $dataRow).'")';
		}

		$this->_sql = 'INSERT INTO '.$tempTableName.' ('.implode(',',$keys).') VALUES ';
		$this->_sql .= implode (',', $dataRows);

		$this->query();

		// Update the db from the temporary table
		$updateElements = array();
		foreach ($dataTypes as $keyName => $dataTypeRow) {
			if ($dataTypeRow['primary'] == false) {
				$updateElements[] = 'm.'.$keyName.' = t.'.$keyName.' ';
			} else {
				$whereClauses[] = 'm.'.$keyName.' = t.'.$keyName;
			}
		}

		$this->_sql = 'UPDATE '.$tableToUpdate.' m, '.$tempTableName.' t SET ';
		$this->_sql .= implode(',', $updateElements).' WHERE '.implode (' AND ', $whereClauses);
		$this->query();

		// And tidy up after ourselves
		$this->_sql = 'DROP TEMPORARY TABLE '.$tempTableName;
		$this->query();
	}

	/**
	 *	Get ID of last insert
	 *
	 *	@access public
	 *	@return mixed Insert ID
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
	
	/*
	 * @author by leon
	 * replace the poor loadAssocList()
	 */
	public function loadArrayById($sql,$key)
	{
	
		if($key == false || $sql == false){
			return false;
		}else{
			$returnArray = array();
			$t = mysql_query($sql);
			while($r = mysql_fetch_array($t))
			{
				$returnArray[$r[$key]] = $r[$key];
			}
		}
		return $returnArray;
	}


	/*
	 * @author by leon
	 * replace the poor loadAssocArray()
	 */
	public function loadArray($sql)
	{
	
		if($sql == false){
			return false;
		}else{
			$returnArray = array();
			$t = mysql_query($sql);
			while($r = mysql_fetch_array($t))
			{
				$returnArray[] = $r;
			}
		}
		return $returnArray;
	}

	
	/**
	 *	Load associative arrays, grouped by one of its keys.
	 *
	 *	@access public
	 *	@param string Field to group by
	 *	@param string Field to key off, within grouping
	 *	@param bool Use row value, rather than whole row
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
	 *	Success of modification query.
	 *
	 *	@return int corresponding to mysql_affected_rows.
	 */
	public function loadSuccess()
	{
		if (!($cur = $this->query())) {
			return false;
		}
		return $this->getAffectedRows();
	}

	/**
	 *	Get number of row found with previous query
	 *
	 *	@access public
	 *	@return int Number of found rows
	 */
	public function getFoundRows()
	{
		$query = 'SELECT FOUND_ROWS()';
		return mysql_result(mysql_query($query, $this->_dbh), 0);
	}

	/**
	 *	Get number of rows affected by the previous query
	 *
	 *	@access public
	 *	@return int Number of affected rows
	 */
	public function getAffectedRows()
	{
		return mysql_affected_rows($this->_dbh);
	}

	/**
	 * Get a database escaped, trimmed string
	 *
	 * @param (array/string) The (array of) string(s) to be escaped
	 * @return (array/string) Escaped (array of) string(s)
	 */
	public static function escape($input)
	{
		if (is_array($input)) {
			$output = array();
			foreach ($input as $key => $element){
				$output[$key] = self::escape($element);
			}
		} else {
			$output = self::_escape($input);
		}
		return $output;
	}
	
	/**
	 *	Escape a string
	 *
	 *	N.B. Should use above, ideally (would take into account database's character encoding), 
	 *	but just can't be bothered to change all the templates.
	 *
	 *	@access protected
	 *	@param string
	 *	@return string
	 */
	protected static function _escape($string)
	{
		//return mysql_real_escape_string($string, $this->_dbh);
		return mysql_real_escape_string($string);
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
		return preg_replace('/([_%])/', '\\\$1', $this->_escape($input));
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
	
	/**
	 *	Log query.
	 *
	 *	@return bool Success value of log append.
	 */
	protected function _log($text){
		
		/* Sanitise text */
		$text = str_replace(array("\r", "\n", "\t"), ' ', $text);
		
		/* Define log filepath */
		$path = BLUPATH_BASE.'/log.sql';
		
		/* Write to file */
		$handle = fopen($path, 'at');
		$written = fwrite($handle, "\n".'['.date('d:m:y H:i:s.u').'] '.$text);
		fclose($handle);
		
		/* Return */
		return (bool) $written;
		
	}
    /**
     *  @desc Close the db connection.
     *  @author Leon Zhao
     *  @return bool result
     */    
    function close()
    {
        $result = mysql_close($this->_dbh);
        $this->_dbh = false;
        return $result;
    }
}
?>
