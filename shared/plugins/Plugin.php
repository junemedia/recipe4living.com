<?php

/**
 * Plugin structure base class
 *
 * @package BluApplication
 * @subpackage Plugin
 */
abstract class Plugin
{
	/**
	 * Plugin id
	 *
	 * @var string
	 */
	protected $_id;

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
	 * Settings for the plugin, from the database
	 *
	 * @var array
	 */
	protected $_settings;

	/**
	 * Active plugins array
	 *
	 * @var array
	 */
	protected $_activePlugins;

	/**
	 * Generic Plugin constructor
	 */
	public function __construct($id, $type)
	{
		// Get reference to global database object
		$this->_db = BluApplication::getDatabase();

		// Get reference to global cache object
		$this->_cache = BluApplication::getCache();

		// Get siteId
		$siteId = BluApplication::getSetting('siteId');
		
		// Load settings from database
		$cacheKey = 'pluginsettings_'.$type.'_'.$id;
		$this->_settings = $this->_cache->get($cacheKey);
		if (!$this->_settings) {
			$query = 'SELECT settings
				FROM plugins
				WHERE type = "'.Database::escape($type).'"
					AND id = "'.Database::escape($id).'"
					AND siteId ="'.Database::escape($siteId).'"';
			$this->_db->setQuery($query);
			$this->_settings = unserialize($this->_db->loadResult());
			
			// Store in cache
			$this->_cache->set($cacheKey, $this->_settings);
		}
		
		// Set cache path
		$this->setCachePath($type.ucfirst($id));
	}

	/**
	 * Get Plugin setting
	 *
	 * @param string Settting name
	 * @return mixed Setting value
	 */
	public function getSetting($item)
	{
		return $this->_settings[$item];
	}

	/**
	 * Set the cache path for the plugin, and create it if it isn't there
	 *
	 * @param string plugin name
	 */
	protected function setCachePath($id)
	{
		$this->_cachePath = BLUPATH_CACHE.'/plugins/'.$id;
		if (!is_dir($this->_cachePath)) {
			mkdir ($this->_cachePath, 0700, true);
		}
	}

	/**
	 * Make a post query string from an array
	 *
	 * @param array Array of post fields
	 * @return string Post string
	 */
	protected function buildPostString($arr,$encode=true)
	{
		$ret = '';
		foreach($arr as $k => $v) {
			if ($ret) $ret.= '&';
			if($encode){
				$ret.= $k.'='.urlencode($v);
			}else{
			        $ret.= $k.'='.$v;
			}
		}
		return $ret;
	}

	/**
	 * Parse a result string into an array
	 *
	 * @param string Result string
	 * @return array array of result fields
	 */
	protected function parseResultString($str, $delim = '&')
	{
		$arr = explode($delim, trim($str));
		$ret = array();
		foreach($arr as $v) {
			$pos = strpos($v, '=');
			$ret[trim(substr($v, 0, $pos)) ] = urldecode(trim(substr($v, $pos + 1)));
		}
		return $ret;
	}

	//This lot should really be in a controller, but until we\ve gpt the shared controllers set up, sticking them here

	/**
	 * Get all active plugins outside of payment providers
	 */
	static protected function getActivePlugins()
	{
		$activePlugins = unserialize(BluApplication::getSetting('pluginsActive'));
		return $activePlugins;
	}

	/**
	 * Fires actions for each active plugin
	 *
	 * @param string action to trigger
	 * @param string id of action triggerer
	 */
	static public function fireAction($action, $id)
	{
		$activePlugins = Plugin::getActivePlugins();
		if (!empty($activePlugins)) {
			foreach ($activePlugins as $activePlugin) {
				$$activePlugin = new $activePlugin;
				if (method_exists($$activePlugin, $action)) {
					$$activePlugin->$action($id);
				}
			}
		}
	}

	/**
	 * Connect to and FTP server
	 *
	 * @return bool True on success, false otherwise
	 */
	protected function connectToFTP()
	{
		$this->_ftpConnection = ftp_connect($this->_settings['ftpHost']);
		return ftp_login($this->_ftpConnection, $this->_settings['ftpUser'], $this->_settings['ftpPass']);
	}

	/**
	 * List the contents of a directory using the current FTP connection
	 *
	 * @param string Directory path
	 * @param string File match pattern
	 * @return array Array of matched file details, indexed by name
	 */
	protected function listFtpDir ($directory, $pattern = null)
	{
		// Get raw directory listing
		$filesRaw = ftp_rawlist($this->_ftpConnection, $directory);

		// Transform listing into readable form
		foreach ($filesRaw as $fileRaw) {

			// Remove excess whitespace and explode to array of details
			$fileRaw = preg_replace('/  +/', ' ', $fileRaw);
			$fileRawArr = explode(' ', $fileRaw);

			// Skip files which do not match pattern
			if ($pattern && !preg_match($pattern, $fileRawArr[8])) {
				continue;
			}

			// Add file details to list
			$files[$fileRawArr[8]] = array(
				'filename'		=> $fileRawArr[8],
				'permissions'	=> $fileRawArr[0],
				'size'			=> $fileRawArr[4],
				'date'			=> strtotime($fileRawArr[5].' '.$fileRawArr[6].' '.$fileRawArr[7])
			);
		}
		return $files;
	}

}
?>
