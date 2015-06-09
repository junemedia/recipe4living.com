<?php

/**
 * Cache Object
 *
 * @package BluCommerce
 * @subpackage SharedLib
 */
class Cache implements ICache
{
	/**
	 * The memcache object
	 *
	 * @var Memcache
	 */
	public $_memcache;

	/**
	 * Reference to database object
	 *
	 * @var Database
	 */
	private $_db;

	/**
	 * Reference to the settings object
	 * @var Settings
	 */
	private $_config;

	/**
	 * @var string The name spaced cache key prefix to prefix all entry keys with
	 */
	private $_cachePrefix;

	/**
	 * @var string The base cache prefix without the name space attached
	 */
	private $_baseCachePrefix;

	/**
	 * Array of cache lock keys which we can clear on a fatal
	 *
	 * @var array
	 */
	private $_locks = array();

	/**
	 * Cache object constructor
	 *
	 * @param Config The application config object
	 */
	private function __construct(Config $appConfig)
	{
		$this->_config = $appConfig;

		$multiHosts = isset($appConfig->caches) ? $appConfig->caches['data'] : null;
		$host = $appConfig->memcacheHost;
		$port = $appConfig->memcachePort;
		$databaseName = $appConfig->databaseName;

		// Positive outlook
		$this->_cacheHasFailed = false;
		$connectionSuccess = false;

		// Connect to memcache instance
		$this->_memcache = new Memcached('bluCommerce_'.$databaseName.'_'.DEBUG.'_'.STAGING.'_'.SITEEND);

		// Use binary compression
		if (defined('ULTRACACHE') && ULTRACACHE) {
			$this->_memcache->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
			$this->_memcache->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);

		//	$this->_memcache->setOption(Memcached::OPT_TCP_NODELAY, true);
			if (SITEEND != 'backend') {
//				$this->_memcache->setOption(Memcached::OPT_BUFFER_WRITES, true);
				$this->_memcache->setOption(Memcached::OPT_NO_BLOCK, true);
			} 
		}

		if (defined('DEBUG_CACHE')) {
			$this->_pid = posix_getpid();
		}

		// Test for server connections
		if (count($this->_memcache->getServerList()) > 0) {
			$connectionSuccess = true;
		// Initiate connections
		} else {
			if (is_array($multiHosts)) {
				$connectionSuccess = $this->_memcache->addServers($multiHosts);
			} else {
				$connectionSuccess = $this->_memcache->addServer($host, $port);
			}
		}

		// Boo, rain
		if (!$connectionSuccess) {
			$this->_cacheHasFailed = true;
		}

		// Get reference to database instance
		$this->_db = BluCommerce::getDatabase();

		// Get the current namespace based on the cachePrefix so far
		$generalKey = '';

		// Build the initial cache prefix based on which type of server we're on
		if (DEBUG == true) {
			$subDomains = explode('.', $_SERVER['HTTP_HOST']);
			$generalKey = 'DEBUG_'.$subDomains[0];
		} elseif (STAGING === true) {
			$generalKey = 'STAGING';
		}

		// Now make it database specific
		$this->_baseCachePrefix = $generalKey.'_'.$this->_config->databaseName;

		// Get the current namespace
		$nameSpace = $this->getCurrentNameSpace();
		$this->_cachePrefix = $this->_baseCachePrefix.'_'.$nameSpace;
	}

	/**
	 * Cache object destructor
	 */
	public function __destruct()
	{
		$this->removeLocks();
	}

	/**
	 * Returns a reference to the global Cache object, only creating it
	 * if it doesn't already exist
	 *
	 * @param Config The config object containing the settings required to set up the cache object
	 * @return Cache A cache object
	 */
	public static function getInstance(Config $appConfig)
	{
		// Existing instances
		static $instances;
		if (!isset($instances)) {
			$instances = array();
		}

		// Get instance signature
		$args = func_get_args();
		$signature = serialize($args);

		// Fetch instance
		if (empty($instances[$signature])) {
			$c = __CLASS__;
			$instances[$signature] = new $c($appConfig);
		}

		// Return
		return $instances[$signature];
	}

	/**
	 * Get a cache key for a given item type and key
	 *
	 * @param string Item type
	 * @param string Site ID
	 * @param string Item human key
	 * @return string Cache key
	 */
	private function _getCacheKey($type, $siteId, $humanKey)
	{
		$prefix = ($type == 'data') ? $this->_cachePrefix : $this->_baseCachePrefix;
		return md5($prefix.'_'.$type.'_'.$siteId.'_'.$humanKey);
	}

	/**
	 * Set an item.
	 *
	 * @param string $humanKey Human key
	 * @param mixed $content Content
	 * @param int $expiry Expiry time (seconds)
	 * @param string Site ID.
	 * @param array Options.
	 * @return bool Success.
	 */
	public function set($humanKey, $content, $expiry = 0, $siteId = null, array $options = array(), $type = 'data')
	{
		if (defined('DEBUG_CACHE')) Utility::irc_dump($this->_pid.' SET: '.$humanKey.' '.($siteId ? $siteId : 'ALL').':'.$type, DEBUG_CACHE);

		// Get options
		$compress = isset($options['compress']) ? (bool) $options['compress'] : false;

		// Build cache key
		$cacheKey = $this->_getCacheKey($type, $siteId, $humanKey);

		// Store in cache
		$this->_memcache->setOption(memcached::OPT_COMPRESSION, $compress);
		if ($result = $this->_memcache->set($cacheKey, $content, (int) $expiry)) {
			if (defined('DEBUG_CACHE')) Utility::irc_dump($this->_pid.' SET RESULT : '.$humanKey.' '.($siteId ? $siteId : 'ALL').':'.$type.' - '.$result, DEBUG_CACHE);
			// Store reference in db for backend keys (TO BE KILLED ASAP)
			if (SITEEND == 'backend') {
				$query = 'REPLACE INTO memcacheReference SET
					humanKey = "'.$this->_db->escape($humanKey).'",
					memcacheKey = "'.$this->_db->escape($cacheKey).'"';
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}

		// Clear the mutex, as we're done one way or the other
		unset($this->_locks[$cacheKey.'_lock']);
		$this->_memcache->delete($cacheKey.'_lock');

		// Return
		return $result;
	}

	/**
	 * Append to a cache key (or set if it doesn't exist)
	 *
	 * @param string $humanKey Human key
	 * @param mixed $content Content
	 * @return bool Success
	 */
	public function append($humanKey, $content, $expiry = 0, $siteId = null, array $options = array(), $type = 'data')
	{
		if (defined('DEBUG_CACHE')) Utility::irc_dump($this->_pid.' APPEND: '.$humanKey.' '.($siteId ? $siteId : 'ALL').':'.$type, DEBUG_CACHE);

		// Build cache key
		$cacheKey = $this->_getCacheKey($type, $siteId, $humanKey);

		// Store in cache
		$this->_memcache->setOption(memcached::OPT_COMPRESSION, false);
		if (!$result = $this->_memcache->append($cacheKey, $content)) {
			$result = $this->_memcache->set($cacheKey, $content, $expiry);
		}
		return $result;
	}

	/**
	 * Get an item.
	 *
	 * @param string|array Human key(s)
	 * @param string Site ID
	 * @param string $type The sub-namespace to look for the key in
	 * @param bool 	$buildNeeded Whether the build process is required. If not, the key
	 * 			   is not set to building if it doesn't exist.
	 * @return mixed Value, or false if not found
	 */
	public function get($humanKey, $siteId = null, $type = 'data', $buildNeeded = true)
	{
		if (defined('DEBUG_CACHE')) Utility::irc_dump($this->_pid.' GET: '.$humanKey.' '.($siteId ? $siteId : 'ALL').':'.$type, DEBUG_CACHE);

		// Build cache key
		$cacheKey = $this->_getCacheKey($type, $siteId, $humanKey);

		// Loop while building
		$i = 0;
		while (++$i < 100) {

			// Get key(s)
			try {
				$result = $this->_memcache->get($cacheKey);
			} catch (Exception $e) {
				trigger_error('Cache has failed', E_USER_ERROR);
			}

			// Mutex build if required, otherwise just return the result
			if ($buildNeeded && ($result === false) && ($this->_memcache->getResultCode() == Memcached::RES_NOTFOUND)) {
				$lock = $this->_memcache->add($cacheKey.'_lock', true, 180);
				if ($lock && ($this->_memcache->getResultCode() != Memcached::RES_NOTSTORED)) {

					if (defined('DEBUG_CACHE')) Utility::irc_dump($this->_pid.' MISS: '.$humanKey.' '.($siteId ? $siteId : 'ALL').':'.$type, DEBUG_CACHE);

					// Entry not found, and no mutex
					return false;
				} else {

					if (defined('DEBUG_CACHE')) Utility::irc_dump($this->_pid.' WAIT '.$i.': '.$humanKey.' '.($siteId ? $siteId : 'ALL').':'.$type, DEBUG_CACHE);

					// Build in progress, try again momentarily
					sleep(1);
					$this->_locks[$cacheKey.'_lock'] = true;
				}

			// Found, or no build required
			} else {
				if (defined('DEBUG_CACHE')) Utility::irc_dump($this->_pid.' GET SUCCESS: '.$humanKey.' '.($siteId ? $siteId : 'ALL').':'.$type, DEBUG_CACHE);
				return $result;
			}
		}

		// Get failed
		if (defined('DEBUG_CACHE')) Utility::irc_dump($this->_pid.' GET FAILED: '.$humanKey.' '.($siteId ? $siteId : 'ALL').':'.$type, DEBUG_CACHE);
		return false;
	}

	/**
	 * Get the current name space in use for the given site end, and regenerate if none is found
	 *
	 * @param string $siteEnd The site end (front end of backend) to get the namespace for
	 * @return string The name space
	 */
	public function getCurrentNameSpace($siteEnd = SITEEND)
	{
		if (defined('DEBUG_CACHE')) Utility::irc_dump($this->_pid.' NAMESPACE GET: '.$siteEnd, DEBUG_CACHE);

		// Build cache key
		$cacheKey = md5($this->_baseCachePrefix.'_'.$siteEnd.'_namespace');

		// Loop while building
		$i = 0;
		while (++$i < 100) {

			// Try and get namespace
			try {
				$nameSpace = $this->_memcache->get($cacheKey);
			} catch (Exception $e) {
				trigger_error('Cache has failed', E_USER_ERROR);
			}

			// If not found, wait for build/regenerate as appropriate
			if (($nameSpace === false) && ($this->_memcache->getResultCode() == Memcached::RES_NOTFOUND)) {
				$lock = $this->_memcache->add($cacheKey.'_lock', true, 60);
				if ($lock && ($this->_memcache->getResultCode() != Memcached::RES_NOTSTORED)) {

					if (defined('DEBUG_CACHE')) Utility::irc_dump($this->_pid.' NAMESPACE MISS: '.$siteEnd, DEBUG_CACHE);

					// Entry not found, generate namespace
					return $this->regenerateNameSpace($siteEnd);
				} else {

					if (defined('DEBUG_CACHE')) Utility::irc_dump($this->_pid.' NAMESPACE WAIT '.$i.': '.$siteEnd, DEBUG_CACHE);

					// Build in progress, try again momentarily
					sleep(1);
					$this->_locks[$cacheKey.'_lock'] = true;
				}

			// Found, return namespace
			} else {
				if (defined('DEBUG_CACHE')) Utility::irc_dump($this->_pid.' NAMESPACE GET SUCCESS: '.$siteEnd, DEBUG_CACHE);
				return $nameSpace;
			}
		}
		if (defined('DEBUG_CACHE')) Utility::irc_dump($this->_pid.' NAMESPACE GET FAILED: '.$siteEnd, DEBUG_CACHE);

		// Get failed
		return false;
	}

	/**
	 * Regenerate the name space and save it to the namespace key
	 *
	 * @param string $siteEnd The site end (front end of backend) to regenerate the namespace for
	 * @return string Namespace identifier
	 */
	public function regenerateNameSpace($siteEnd = SITEEND)
	{
		// Generate and set new namespace for given siteend
		$nameSpace = substr(md5(mt_rand(0,time()).time()), mt_rand(0,20), 8);
		return $this->setNameSpace($siteEnd, $nameSpace);
	}

	/**
	 * Set the name space and save it to the namespace key
	 *
	 * @param string $siteEnd The site end (front end of backend) to regenerate the namespace for
	 * @return string Namespace identifier
	 */
	public function setNameSpace($siteEnd, $nameSpace)
	{
		if (defined('DEBUG_CACHE')) Utility::irc_dump($this->_pid.' NAMESPACE SET: '.$siteEnd.' ('.$nameSpace.')', DEBUG_CACHE);

		$cacheKey = md5($this->_baseCachePrefix.'_'.$siteEnd.'_namespace');
		$result = $this->_memcache->set($cacheKey, $nameSpace, 0);

		// Clear namespace mutex
		$this->_memcache->delete($cacheKey.'_lock');

		// Check set was succesful
		if ($result === false) {
			trigger_error('Cache has failed', E_USER_ERROR);
			return false;
		}

		// Has our namespace changed? Update the primary cache prefix
		if ($siteEnd == SITEEND) {
			$this->_cachePrefix = $this->_baseCachePrefix.'_'.$nameSpace;
		}

		// Truncate memcachereference if we've changed the backend namespace
		if ($siteEnd == 'backend') {
			$query = 'TRUNCATE TABLE memcacheReference';
			$this->_db->setQuery($query);
			$result = $this->_db->query();
		}

		return $nameSpace;
	}

	/**
	 * Delete an item
	 *
	 * @param string Human key
	 * @param string Site ID.
	 * @return bool Success.
	 */
	public function delete($humanKey, $siteId = null, $type = 'data')
	{
		if (defined('DEBUG_CACHE')) Utility::irc_dump($this->_pid.' DELETE: '.$humanKey.' '.($siteId ? $siteId : 'ALL').':'.$type, DEBUG_CACHE); 

		// Build cache key
		$cacheKey = $this->_getCacheKey($type, $siteId, $humanKey);

		// Remove from cache, including key just incase it's still there
		unset($this->_locks[$cacheKey.'_lock']);
		$this->_memcache->delete($cacheKey.'_lock');
		$result = $this->_memcache->delete($cacheKey);
		if (($result === false) && ($this->_memcache->getResultCode() == Memcached::RES_NOTFOUND)) {
			$result = true;
		}

		// Delete reference in db for backend keys (TO BE KILLED ASAP)
		if ($result && (SITEEND == 'backend')) {
			$query = 'DELETE FROM memcacheReference
				WHERE memcacheKey = "'.$this->_db->escape($cacheKey).'"';
			$this->_db->setQuery($query);
			$result = $this->_db->query();
		}

		// Return
		return $result;
	}

	/**
	 * Get an application level mutex
	 *
	 * @param string Mutex name
	 * @param string Expiry in seconds (defaults to 10 minutes)
	 * @return bool True if mutex was acquired, false otherwise
	 */
	public function getMutex($mutexName, $siteId = null, $expiry = 600)
	{
		$cacheKey = $this->_getCacheKey('mutex', $siteId, $mutexName);
		$mutex = $this->_memcache->add($cacheKey, true, $expiry);
		$this->_locks[$cacheKey] = true;
		return ($mutex && ($this->_memcache->getResultCode() != Memcached::RES_NOTSTORED));
	}

	/**
	 * Clear application level mutex
	 *
	 * @param string Mutex name
	 * @return bool Success
	 */
	public function clearMutex($mutexName, $siteId = null)
	{
		$cacheKey = $this->_getCacheKey('mutex', $siteId, $mutexName);
		unset($this->_locks[$cacheKey]);
		return $this->_memcache->delete($cacheKey);
	}

	/**
	 * Get memcache stats
	 *
	 * @param Stats type
	 * @return array Stats
	 */
	public function getStats($type = null)
	{
		return $this->_memcache->getStats();
	}

	/**
	 * Remove all locks created by this class. Usefull if we hit a fatal error to stop the site
	 * from being unable to build again.
	 *
	 * @return true
	 */
	public function removeLocks()
	{
		if (defined('DEBUG_CACHE')) Utility::irc_dump($this->_pid.' REMOVING '.count($this->_locks).' LOCKS', DEBUG_CACHE);

		foreach ($this->_locks as $key => $isLocked) {
			$this->_memcache->delete($key);
		}
	}
}
