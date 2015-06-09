<?php

/**
 *	MSSQL Database interface
 */
class MSSQLDatabase extends Database
{
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
		return mssql_connect($host, $user, $pass, true);
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
		return mssql_select_db($name, $this->_dbh);
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
		// Sorry, can't do that.
		return true;
	}
	
	/**
	 *	Set timezone
	 *
	 *	@access protected
	 *	@return bool Success
	 */
	protected function _setTimezone()
	{
		// Sorry, can't do this either
		return true;
	}

	/**
	 * Returns a reference to the global Database object, only creating it
	 * if it doesn't already exist
	 *
	 *	LOL PHP
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
	
	/**
	 *	Query
	 *
	 *	@access protected
	 *	@param string Query
	 *	@return mixed Result
	 */
	protected function _query($statement)
	{
		return mssql_query($statement, $this->_dbh);
	}
	
	/**
	 *	Error text from previous operation
	 *
	 *	@access protected
	 *	@return string
	 */
	protected function _error()
	{
		return mssql_get_last_message();
	}
	
	/**
	 *	Error code from previous operation
	 *
	 *	@todo
	 *	@access protected
	 *	@return int
	 */
	protected function _errno()
	{
		return false;
	}

	/**
	 *	Get ID of last insert
	 *
	 *	@todo
	 *	@access public
	 *	@return mixed Insert ID
	 */
	public function getInsertID()
	{
		return false;
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
		return mssql_free_result($result);
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
		return mssql_fetch_row($result);
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
		return mssql_fetch_assoc($result);
	}

	/**
	 *	Get number of row found with previous query
	 *
	 *	@todo
	 *	@access public
	 *	@return int Number of found rows
	 */
	public function getFoundRows()
	{
		return false;
	}

	/**
	 *	Get number of rows affected by the previous query
	 *
	 *	@todo
	 *	@access public
	 *	@return int Number of affected rows
	 */
	public function getAffectedRows()
	{
		return false;
	}
	
	/**
	 *	Escape a string
	 *
	 *	@todo
	 *	@access protected
	 *	@param string
	 *	@return string
	 */
	protected static function _escape($string)
	{
		return $string;
	}
}

?>