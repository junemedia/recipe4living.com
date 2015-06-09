<?php

/**
 * Model base class
 *
 * @package BluApplication
 * @subpackage BluModels
 */
abstract class BluModel
{
	/**
	 * Reference to application database object
	 *
	 * @var Database
	 */
	protected $_db;

	/**
	 * Reference to application cache object
	 *
	 * @var Cache
	 */
	protected $_cache;

	/**
	 *	Used for storing current category name (full text).
	 *	@var string
	 */
	protected $_category;

	/**
	 *	Used for storing current search tags.
	 *	@var Array
	 */
	protected $_tags;

	/**
	 * Front end model constructor
	 */
	public function __construct()
	{
		// Get reference to global database object
		$this->_db = BluApplication::getDatabase();

		// Get reference to global cache object
		$this->_cache = BluApplication::getCache();
	}

	/**
	 * Get *all* site IDs.
	 *
	 * @access protected
	 * @return array Site IDs.
	 */
	protected function _getSites()
	{
		// Use multisite, or fake it using single-site.
		if (!$sites = BluApplication::getSetting('sites', false)) {
			$sites = array(BluApplication::getSetting('siteId') => '');
		}
		
		// Return
		return array_keys($sites);
	}

	/**
	 *	Basic mutator function.
	 */
	public function set($key, $value)
	{
		switch(strtolower($key)){
			case 'category':
				if (strtolower($value) != 'all' && strtolower($value) != 'view all'){
					$this->_category = $value;
				}
				break;

			case 'tags':
				$this->_tags = Utility::is_loopable($value) ? $value : array($value);
				break;

			default:
				break;
		}
		return $this;
	}

	/**
	 *	Basic accessor function
	 */
	public function get($key)
	{
		switch(strtolower($key)){
			case 'category':
				return $this->_category;
				break;

			default:
				return null;
				break;
		}
	}

	/**
	 *	Shortcut.
	 */
	final protected static function &getModel($name){
		return BluApplication::getModel($name);
	}

	/**
	 *	Shortcut.
	 */
	final protected static function getObject($type, $id){
		return BluApplication::getObject($type, $id);
	}

	/**
	 *	Fetches from database.
	 *
	 *	- Searches through cache.
	 *	- On cache miss, conducts a query against the database then stores in cache.
	 *
	 *	@args (string) query: the query to invoke (on cache miss or if no cache key provided).
	 *	@args (string) cacheKey: key to search in the cache.
	 *	@args (ints) offset, limit: the offset, limit to use for the query.
	 *
	 *	@return (array) An associative array representing the resultset.
	 */
	protected function _fetch($query, $cacheKey = null, $offset = null, $limit = null){

		/* Try fetching from cache. */
		if (!$cacheKey){
			$data = false;
		} else {
			if (!is_null($offset) || !is_null($limit)){
				$cacheKey .= '_' . (int) $offset . '_' . (int) $limit;
			}
			$data = $this->_cache->get($cacheKey);
		}

		/* Cache miss? */
		if ($data === false){

			/* Try fetching record(s) from database */
			$this->_db->setQuery($query, $offset, $limit);
			$data = $limit == 1 ? $this->_db->loadAssoc() : $this->_db->loadAssocList();

		    /* Store in cache if database succeeds. */
			if ($cacheKey){ $this->_cache->set($cacheKey, $data); }

		}

		/* Return data */
		return $data;

	}

	/**
	 *	Creates a record in the database.
	 *
	 *	@args (string) table: name of the table to insert into.
	 *	@args (array) args: an associative array with field name (key) and value to insert (value).
	 *	@args (array) reserved: an array of keys to replace in 'args' array. These may be special SQL functions, and hence aren't separated by single quotes, i.e. NOW() for current time.
	 *	@args (boolean) ignore: ignore SQL errors.
	 *
	 *	@return (int) the insert id.
	 */
	protected function _create($table, array $args, array $reserved = array(), $ignore = false) {

		/* Filter arguments */
		$vars = $this->_filterQuery($table, $args, $reserved, array());
		$ignore = (bool) $ignore;

		/* Build SQL query */
		$keys_quoted = "`" . implode("`, `", array_keys($vars['arguments'])) . "`";
		$keys_unquoted = Utility::is_loopable($vars['reserved']) ? "`" . implode("`, `", array_keys($vars['reserved'])) . "`" : "";

		$values_quoted = "'" . implode("', '", array_values($vars['arguments'])) . "'";
		$values_unquoted = Utility::is_loopable($vars['reserved']) ? implode(", ", array_values($vars['reserved'])) : "";

		$keys = $keys_quoted;
		if ($keys_unquoted) {
			$keys .= ", " . $keys_unquoted;
		}
		$values = $values_quoted;
		if ($values_unquoted) {
			$values .= ", " . $values_unquoted;
		}

		$query = "INSERT" . ($ignore ? ' IGNORE' : '') . " INTO `" . $table . "` (" . $keys . ") VALUES (" . $values . ")";

		/* Dump query */
		if (DEBUG){
			//v($query);
		}

		/* Run query and return insert ID */
		// Should ideally throw an exception if not created
		$this->_db->setQuery($query);
		$this->_db->query();
		return $this->_db->getInsertID();

	}

	/**
	 *	Edit a SINGLE record in the database.
	 *
	 *	Effectively:
	 *		- makes an SQL UPDATE query to the database.
	 *		- flushes the cached object, if supplied a key.
	 *
	 *	@args (string) table
	 *	@args (array) changes: arguments to update.
	 *	@args (array) reserved: special arguments, not to be automatically single-quoted.
	 *	@args (array) k => v: primary key => id of the record in the table to update.
	 *	@args (string) cacheKey: the cache object to flush on database update success.
	 *
	 *	@return (bool) success.
	 */
	protected function _edit($table, array $changes, array $reserved = array(), array $primaryKeys, $cacheKey = null){

		/* Filter arguments */
		$vars = $this->_filterQuery($table, $changes, $reserved, $primaryKeys);

		/* Catch trivialities */
		if (!Utility::is_loopable($vars['arguments']) && !Utility::is_loopable($vars['reserved'])){
			return true;
		}

		/* Build SQL query */
		$query = 'UPDATE `'.$table.'` SET ';

		// Update fields
		$update = array();
		foreach((array) $vars['arguments'] as $key => $value){
			$update[] = '`'.$key.'` = "'.$value.'"';
		}
		foreach((array) $vars['reserved'] as $key => $value){
			$update[] = '`'.$key.'` = '.$value;
		}
		$query .= implode(', ', $update);

		// Condition fields
		$conditions = array('1 = 1');
		foreach((array) $vars['primary'] as $key => $value){
			$conditions[] = '`'.$key.'` = "'.$value.'"';
		}
		$query .= ' WHERE '.implode(' AND ', $conditions);

		/* Dump query */
		if (DEBUG){
			//v($query);
		}

		/* Run query */
		// Should ideally throw an exception if not successful
		$this->_db->setQuery($query);
		$success = (bool) $this->_db->loadSuccess();

		/* Flush cache object */
		if ($success && $cacheKey) {
			$this->_cache->delete($cacheKey);
		}

		/* Return success value */
		return $success;
	}

	/**
	 *	Deletes a record from a table
	 *
	 *	@args (string) table, the table to delete from
	 *	@args (array) args, the criteria to satisfy in order to be deleted.
	 *
	 *	@return (int) the number of deleted (affected) rows.
	 */
	protected function _delete($table, array $args){

		/* Filter arguments */
		$vars = $this->_filterQuery($table, $args, array(), array());

		/* No conditions? Try directly dropping a table instead... */
		if (!Utility::is_loopable($vars['arguments'])){ return null; }

		/* Build query */
		$criteria = array();
		foreach($vars['arguments'] as $key => $value){
			$criteria[] = "`" . $key . "` = '" . $value . "'";
		}
		$query = 'DELETE FROM `' . $table . '` WHERE ' . implode(" AND ", $criteria);

		/* Dump query */
		if (DEBUG){
			//v($query);
		}

		/* Run query and return affected rows */
		// Should ideally throw an exception if not created
		$this->_db->setQuery($query);
		$this->_db->query();
		return (int) $this->_db->loadSuccess();

	}

	/**
	 *	Sanitises query arguments, and filters arguments by the available fields in the given table.
	 */
	protected function _filterQuery(&$table, array $args, array $reserved = array(), array $primaryKeys = array()){

		/* Sanitise table name, just in case... */
		$table = Database::escape($table);

		/* Get available fields from the table */
		$this->_db->setQuery("DESC `" . $table . "`");
		$records = $this->_db->loadAssocList();
		$fields = array();
		foreach((array) $records as $record){
			$fields[] = $record['Field'];
		}

		/* Filter arguments by these fields, and by forbidden fields, too. */
		$filteredArgs = array();
		if (Utility::is_loopable($args)){
			foreach($args as $key => $value){
				if (!in_array($key, $fields)){ continue; }
				if (in_array($key, array_keys($reserved))){ continue; }
				$filteredArgs[$key] = $value;
			}
		}

		/* Filter out null exception arguments too. */
		$filteredExceptions = array();
		if (Utility::is_loopable($reserved)){
			foreach($reserved as $key => $value){
				if (!in_array($key, $fields)){ continue; }
				if (is_null($value)){ continue; }
				$filteredExceptions[$key] = $value;
			}
		}

		/* Filter primary keys */
		$filteredPrimaryKeys = array();
		if (Utility::is_loopable($primaryKeys)){
			foreach($primaryKeys as $key => $value){
				if (!in_array($key, $fields)){ continue; }
				$filteredPrimaryKeys[$key] = $value;
			}
		}

		/* Sanitise */
		$filteredArgs = Database::escape($filteredArgs);
		//$filteredExceptions = Database::escape($filteredExceptions);
		$filteredPrimaryKeys = Database::escape($primaryKeys);

		/* Return */
		return array(
			'arguments'		=> 	$filteredArgs,
			'reserved'		=>	$filteredExceptions,
			'primary'		=>	$filteredPrimaryKeys
		);

	}

	/**
	 *	Gets the enum values of a field in a table.
	 */
	protected function _getEnums($table, $field){

		/* Fetch from cache */
		$cacheKey = 'enums_' . $table . '_' . $field;
		$enums = $this->_cache->get($cacheKey);
		if ($enums === false){

			/* Build query */
			$query = 'DESC `' . $table . '` `' . $field . '`';

			/* Get row */
			$row = $this->_fetch($query);

			/* Parse row */
			$enums = explode("','", preg_replace('/^enum\(\'(.*)\'\)$/', '\1', $row[0]['Type']));

			/* Store in cache */
			$this->_cache->set($cacheKey, $enums);

		}

		/* Return enums */
		return $enums;

	}

}
?>
