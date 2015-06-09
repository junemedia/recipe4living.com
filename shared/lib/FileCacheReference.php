<?php

/**
 *	File cache reference
 *
 *	@package BluApplication
 *	@subpackage SharedLib
 */
class FileCacheReference
{
	/**
	 *	File location
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_location;
	
	/**
	 *	Best before date (timestamp)
	 *
	 *	@access protected
	 *	@var int
	 */
	protected $_expiry;
	
	/**
	 *	Constructor
	 *
	 *	@access public
	 *	@param string Full filepath
	 *	@param int Expiry in seconds
	 */
	public function __construct($location, $expiry = 0)
	{
		$this->_location = $location;
		$this->_expiry = $expiry ? ($expiry + time()) : 0;
	}
	
	/**
	 *	Has expired
	 *
	 *	@access protected
	 *	@return bool
	 */
	protected function _expired()
	{
		return $this->_expiry && ($this->_expiry < time());
	}
	
	/**
	 *	Get file cache content
	 *
	 *	@access public
	 *	@return mixed Cache object
	 */
	public function getContent()
	{
		// If expired, offer to clear up
		if ($this->_expired()) {
			$this->_unlink();
			return false;
		}
		
		// Object exists and readable?
		if (!is_readable($this->_location)) {
			return false;
		}
		ini_set('memory_limit','512M');	
		// Get the cache content (don't use brackets around the include!)
//$us1 = memory_get_usage();
		$content = include $this->_location;
//$us2 = memory_get_usage()-$us1;
//Utility::irc_dump($this->_location.' took '.$us2, 'max');
		
		// Return
		return $content;
	}
	
	/**
	 *	Remove the data from the filesystem
	 *
	 *	@access protected
	 *	@return bool Success
	 */
	protected function _unlink()
	{
		return file_exists($this->_location) ? unlink($this->_location) : true;
	}
}

?>
