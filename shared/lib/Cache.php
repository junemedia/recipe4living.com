<?php

/**
 * Cache Object
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class Cache
{
	/**
	 *	Default cache client.
	 */
	const DEFAULT_CACHE_CLIENT = 'memcache';
	
	/**
	 *	Build failed
	 */
	const MUTEX_STATUS_FAILED = 0;
	
	/**
	 *	Build in progress
	 */
	const MUTEX_STATUS_BUILDING = 1;
	
	/**
	 *	No mutex on this key
	 */
	const MUTEX_STATUS_EMPTY = 2;
	
	/**
	 *	Mutex polling interval (in seconds)
	 */
	const MUTEX_INTERVAL = 3;
	
	/**
	 *	Max size for an individual cache object
	 */
	//const MAX_OBJECT_SIZE = 141000000;
	//const MAX_OBJECT_SIZE = 1048576; // 1024 * 1024 = 1 MB
	const MAX_OBJECT_SIZE = 102400;
	/**
	 * The memcache object
	 *
	 * @var object
	 */
	private $_memcache;

	private $_mutexTryCount = 30;
	
	/**
	 * Reference to database object
	 * 
	 * @var object
	 */
	private $_db;

	/**
	 * The site-specific key prefix
	 *
	 * @var string
	 */
	private $_cachePrefix;
	
	/**
	 *	Cache client.
	 *
	 *	@var string.
	 */
	private $_cacheClient;
	
	/**
	 *	Build queue
	 *
	 *	@access protected
	 *	@var array
	 */
	protected $_building = array();
	
	/**
	 *	Mutex checks
	 *
	 *	@access protected
	 *	@var array
	 */
	protected $_mutexes = array();
	
	/**
	 *	Cache hits
	 *
	 *	@access protected
	 *	@var int
	 */
	protected $_debugHits;
	
	/**
	 *	Cache misses
	 *
	 *	@access protected
	 *	@var int
	 */
	protected $_debugMisses;
	
	/**
	 *	Cache builds
	 *
	 *	@access protected
	 *	@var int
	 */
	protected $_debugBuilds;
	
	/**
	 *	Cache calls
	 *
	 *	@access protected
	 *	@var array
	 */
	protected $_debugCalls;

	/**
	 * Cache object constructor
	 *
	 * @param string Host
	 * @param int Port
	 */
     
     private $_host;
     private $_port;
	private function __construct($host, $port, $multiHosts = null)
	{
		// Don't do a damn thing, just return false
		if (!CACHE) {
			return false;
		}
		
        $this->_host = $host;
        $this->_port = $port;
		if($multiHosts != null) $this->_port .=  print_r($multiHosts, true);
		
		// Positive outlook
		$this->_cacheHasFailed = false;
		$connectionSuccess = false;
		
		// Set cache client.
		$this->_cacheClient = defined('CACHECLIENT') ? CACHECLIENT : self::DEFAULT_CACHE_CLIENT;
		
		// Connect to memcache instance
		switch($this->_cacheClient) {
			case 'memcached':
				$this->_memcache = new Memcached('persist');
				
				// Use binary compression
				if (ULTRACACHE) {
					$this->_memcache->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
					$this->_memcache->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
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
				break;
				
			default:
				$this->_memcache = new Memcache;
				$connectionSuccess = $this->_memcache->connect($host, $port);
				break;
		}
		
		// Boo, rain
		if (!$connectionSuccess) {
			$this->_cacheHasFailed = true;
            		mail('r4l.tech@junemedia.com',
                	'Memcache Failed connection on '.$_SERVER['HTTP_HOST'] . ' - ' . $_SERVER['SERVER_ADDR'],
                	'Server IP: ' . $_SERVER['SERVER_ADDR'] . "\n".
                	'Client IP: ' . $_SERVER['REMOTE_ADDR'] . "\n".
                	'Database Info: ' . mysql_get_host_info() . "\n".
                	'From: r4l.tech <r4l.tech@junemedia.com>');
            		exit();
		}
		
		// Get reference to database instance
		$this->_db = BluApplication::getDatabase();

		// Set site key
		if (DEBUG) {
			$subDomains = explode('.', $_SERVER['HTTP_HOST']);
			//$this->_cachePrefix = 'DEBUG_'.$subDomains[0].'_';
		} else if (STAGING) {
			//$this->_cachePrefix = 'STAGING_';
		}
		
		// Set up debug
		if (DEBUG) {
			$this->_debugHits = 0;
			$this->_debugMisses = 0;
			$this->_debugBuilds = 0;
		}
	}

    public function getServerInfo(){
        return $this->_host .":". $this->_port;
    }

	/**
	 * Returns a reference to the global Cache object, only creating it
	 * if it doesn't already exist
	 *
	 * @param string Host
	 * @param int Port
	 * @return Cache A cache object
	 */
	public static function &getInstance($host, $port, $multiHosts = null)
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
			$instances[$signature] = new $c($host, $port, $multiHosts);
		}
		
		// Return
		return $instances[$signature];
	}

	/**
	 * Set an item.
	 *
	 * @param string Human key
	 * @param mixed Value
	 *	@param int Expiry time (seconds)
	 *	@param string Site ID.
	 *	@param array Options.
	 *	@return bool Success.
	 */
	public function set($humanKey, $value, $expiry = 0, $siteId = null, array $options = array())
	{
		// Bleurgh.
		if (!CACHE) {
			return false;
		}

		// Get (default) site prefix
		if (!$siteId) {
			$siteId = BluApplication::getSetting('siteId');
		}
		
		// Get options
		$compress = isset($options['compress']) ? (bool) $options['compress'] : false;
		
		if ($expiry !=0) {
			$jitter = mt_rand(0, round($expiry*0.5));
			$expiry += $jitter;
		}
		
		// Store in cache
		$result = $this->_setCached($humanKey, $value, $siteId, array(
			'compress' => $compress,
			'expiry' => $expiry
		));
		// Store the references to the keys, for selective updates of memcached without hosing the whole shebang
		if ($result) {
			$this->_setReference($humanKey, $value, $siteId);
		
			// Tidy up mutexes
			$this->_building[$siteId][$humanKey] = false;
			
			// Debug
			if (DEBUG) {
				$this->_debugBuilds++;
				
				if (QUERY_LIST) {
					$time = microtime(true);
					$size = $this->_getSize($value);
					$this->_debugCalls[] = '<font color=red>Cache ['.number_format($time, 2, '.', '').']: [' . md5($humanKey) . '] BUILT '.$humanKey.' ('.Text::formatBytes($size,2).' bytes'.($compress ? ' - <b>compressed</b>' : '').')</font>';
				
				}
			}

		// Something went wrong, mark it down
		} else {
			$this->_setStatus($humanKey, $siteId, 'outofdate');
		}
		
		// Return
		return $result;
	}
	
	/**
	 *	Set a reference to the cache object into the database.
	 *
	 *	@param string Human key
	 *	@param mixed Content
	 *	@param string Site ID
	 *	@return bool Success
	 */
	protected function _setReference($humanKey, $content, $siteId)
	{
		$key = md5($humanKey);
		$size = $this->_getSize($content);
		$result = $this->_setStatus($humanKey, $siteId, 'uptodate', $size);
		return $result;
	}
	
	/**
	 *	Set a key status
	 *
	 *	@param string Human key
	 *	@param string Site ID
	 *	@param string Status
	 *	@param int Uncompressed serialized object size
	 *	@return bool Success
	 */
	protected function _setStatus($humanKey, $siteId, $status, $size = 0)
	{
		// Skip to write into the database
		return true;

		// Scramble key.
		$key = md5($humanKey);
		
		// Store in database
		$query = 'REPLACE INTO memcacheReference SET
			humanKey = "'.$this->_db->escape($humanKey).'",
			memcacheKey = "'.$this->_db->escape($key).'",
			timeSet = NOW(),
			sizeBytes = '.(int) $size.',
			siteId = "'.$this->_db->escape($siteId).'",
			prefix = "'.$this->_db->escape($this->_cachePrefix).'",
			status = "'.$this->_db->escape($status).'"';
		$this->_db->setQuery($query);
		$result = $this->_db->query();
		
		// Return
		return (bool) $result;
	}

	public function increment ($humanKey, $siteId = null) {
		$key = md5($humanKey);
		$cacheKey = $this->_buildCacheKeyFragment($this->_cachePrefix, $siteId, $key);
		$this->_memcache->add($cacheKey, 0);
		return $this->_memcache->increment($cacheKey);
	}
	
	/**
	 *	Store content in cache.
	 *
	 *	@param string Human key.
	 *	@param mixed Content.
	 *	@param string Site ID.
	 *	@param array Options
	 *	@return bool Success.
	 */
	protected function _setCached($humanKey, $content, $siteId, array $options = array())
	{
		// Build cache key
		$key = md5($humanKey);
		$cacheKey = $this->_buildCacheKeyFragment($this->_cachePrefix, $siteId, $key);
		
		// Get options
		$compress = isset($options['compress']) ? (bool) $options['compress'] : false;
		$expiry = isset($options['expiry']) ? (int) $options['expiry'] : 0;
		
		// Set in cache
		$result = false;
		
		// Use file cache?
		if ($this->_getSize($content) > self::MAX_OBJECT_SIZE) {
			// Get a file cache reference
			$content = $this->_setFileCached($humanKey, $content, $expiry, $siteId);
		}
		
		// Push to memcache server
		switch ($this->_cacheClient) {
			case 'memcached':
				// Use compression?
				$compressionSetting = (bool) $compress;
				$this->_memcache->setOption(memcached::OPT_COMPRESSION, $compressionSetting);
				
				// Set content
				$result = $this->_memcache->set($cacheKey, $content, $expiry);
				break;
				
			default:
				// Use compression?
				$compressionSetting = $compress ? MEMCACHE_COMPRESSED : 0;
				
				// Set content
				$result = $this->_memcache->set($cacheKey, $content, $compressionSetting, $expiry);
				break;
		}
		
		// Return
		return $result;
	}
	
	/**
	 *	Set content in the file cache
	 *
	 *	@access protected
	 *	@param string Human key
	 *	@param mixed Content
	 *	@param int Expiry
	 *	@param string Site ID
	 *	@return FileCacheReference Reference to the object in filesystem
	 */
	protected function _setFileCached($humanKey, $content, $expiry, $siteId)
	{
		// Get content - convert to PHP code
		$content = var_export($content, true);
		$content = '<?php return '.$content.'; ?>';
		
		// Create the cache object.
		$filepath = $this->_getFileCachePath($humanKey, $siteId);
		file_put_contents($filepath, $content);
		// Create the reference
		$reference = new FileCacheReference($filepath, $expiry);
		return $reference;
	}

	/**
	 * Get an item.
	 *
	 * @param string/array Human key(s)
	 * @param string Site ID
	 * @param int Iteration counter (DEPRECATED)
	 * @param bool Build process is requred
	 * @return mixed Value, or false if not found
	 */
	public function get($humanKey, $siteId = null, $iterationCounter = 0, $buildNeeded = true)
	{
		// Disable cache
		if (!CACHE) {
			return false;
		}
		
		// Build-cache mode
		if (CACHEBUILD) {
			return false;
		}
		
		// Limit cache to site.
		if (!$siteId) {
			$siteId = BluApplication::getSetting('siteId');
		}
		
		// Fetch cached content
		try {
			$result = $this->_getCached($humanKey, $siteId);
		} catch (Exception $e) {
			trigger_error('Cache has failed', E_USER_ERROR);
		}
		
		// Intercept file cache references
		if ($result instanceof FileCacheReference) {
			$result = $result->getContent();
		}
		
		// Debug
		if (DEBUG) {
			
			// Get caller
			if (QUERY_LIST) {
				$trace = debug_backtrace();
				array_shift($trace);
				$caller = array_shift($trace);
				$parent = array_shift($trace);
				$text = $parent['class'].$parent['type'].$parent['function'].' ::: '.$caller['class'].$caller['type'].$caller['function'];
			}
			
			$time = microtime(true);
			if (($result === false) && ($this->_memcache->getResultCode() == Memcached::RES_NOTFOUND)) {
				$this->_debugMisses++;
				
				if (QUERY_LIST) {
					$this->_debugCalls[] = '<font color=gray>Cache ['.number_format($time, 2, '.', '').']: MISSED '.$humanKey.'  ['.$text.']</font>';
				
				}
			} else {
				$this->_debugHits++;
				
				if (QUERY_LIST) {
					$this->_debugCalls[] = '<font color=green>Cache ['.number_format($time, 2, '.', '').']: hit '.$humanKey.'  ['.$text.']</font>';
				}
			}
		}
		
		// Fall straight back out?
		if (!$buildNeeded) {
			return $result;
		}
		
		// New memcached client will allow us to do mutexes due to reliable status reporting. Old one will not, because false isn't necessarily false. Urgh
		switch ($this->_cacheClient) {
			case 'memcached':
				
				if ($this->_memcache->getResultCode() == Memcached::RES_NOTFOUND) {
					
					// Check the mutex
					switch ($this->_getMutex($humanKey, $siteId)) {
						case self::MUTEX_STATUS_FAILED:
							
							// Something went wrong
							$this->_setStatus($humanKey, $siteId, 'outofdate');
							return false;
							
						case self::MUTEX_STATUS_BUILDING:
							
							// Hang on, aren't we building this ourselves?
							if (!empty($this->_building[$siteId][$humanKey])) {
								$this->_setStatus($humanKey, $siteId, 'outofdate');
								return false;
							}
							
							// Someone else is building
							sleep(self::MUTEX_INTERVAL);
							$result = $this->get($humanKey, $siteId);
							break;
							
						case self::MUTEX_STATUS_EMPTY:
							
							// Let's start building
							$this->_setStatus($humanKey, $siteId, 'building');
							$this->_building[$siteId][$humanKey] = true;
							break;
					}
				}
				break;
		}
		
		// Return
		return $result;
	}

	public function setMutexTryCount($count = 2) {
		$this->_mutexTryCount = $count;
	}
	
	/**
	 *	Check mutex
	 *
	 *	@access protected
	 *	@param string Human key
	 *	@param string Site ID
	 *	@return int Mutex status
	 */
	protected function _getMutex($humanKey, $siteId)
	{
		// Give up after 60 tries
		if (empty($this->_mutexes[$siteId][$humanKey])) {
			$this->_mutexes[$siteId][$humanKey] = 0;
		}
		if (++$this->_mutexes[$siteId][$humanKey] == $this->_mutexTryCount) {
			return self::MUTEX_STATUS_FAILED;
		}
		
		// Poll status
		switch ($this->_getStatus($humanKey, $siteId)) {
			case 'building':
				// Someone else is building it, be nice and let them finish
				return self::MUTEX_STATUS_BUILDING;
				
			case 'outofdate':
			default:
				// We're going to start building it in the following code.
				return self::MUTEX_STATUS_EMPTY;
		}
	}
	
	/**
	 *	Fetch content from cache.
	 *
	 *	@param string/array Human key(s).
	 *	@param string Site ID.
	 *	@return mixed Cached content.
	 */
	protected function _getCached($humanKey, $siteId)
	{
		// Build cache key(s).
		$cacheKey = false;
		if (is_array($humanKey)){
			$cacheKey = array();
			foreach($humanKey as $index => $singleKey){
				$key = md5($singleKey);
//				$cacheKey[$index] = $this->_buildCacheKeyFragment($this->_cachePrefix, $siteId, $key);
			}
		} else {
			$key = md5($humanKey);
			$cacheKey = $this->_buildCacheKeyFragment($this->_cachePrefix, $siteId, $key);
		}
		
		// Prepare
		$result = false;
		
		// Different cache clients use different methods...
		switch($this->_cacheClient){
			case 'memcached':
				// Array of cache keys
				if (is_array($cacheKey)){
					$result = $this->_memcache->getMulti($cacheKey);
					
				// Single cache key
				} else {
					$result = $this->_memcache->get($cacheKey);
				}
				break;
				
			default:
				
				// Supports array of cache keys OR single cache key string.
				$result = $this->_memcache->get($cacheKey);
				break;
		}
		
		// Return
		return $result;
	}
	
	/**
	 * Query reference table to determine current status of the cache item.
	 *
	 * @param string/array Human key(s)
	 * @param string Site ID
	 * @return mixed Value, or false if not found
	 */
	protected function _getStatus($humanKey, $siteId)
	{
		$key = md5($humanKey);
		$query = 'SELECT m.status 
			FROM `memcacheReference` AS `m`
			WHERE m.memcacheKey = "'.$this->_db->escape($key).'"
				AND m.siteId = "'.$this->_db->escape($siteId).'"
				AND m.prefix = "'.$this->_db->escape($this->_cachePrefix).'"';
		$this->_db->setQuery($query);
		return $this->_db->loadResult($query);
	}

	/**
	 *	Get the time a key was set
	 *
	 *	@access public
	 *	@param string Human key
	 *	@param string Site ID
	 *	@return int Timestamp
	 */
	public function getTimeSet($humanKey, $siteId)
	{
		$memcacheKey = md5($humanKey);
		$query = 'SELECT UNIX_TIMESTAMP(mr.timeSet)
			FROM `memcacheReference` AS `mr`
			WHERE mr.memcacheKey = "'.$this->_db->escape($memcacheKey).'"
				AND mr.siteId = "'.$this->_db->escape($siteId).'"
				AND mr.prefix = "'.$this->_db->escape($this->_cachePrefix).'"';
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Delete an item
	 *
	 * @param string Human key
	 * @param string Site ID.
	 * @return bool Success.
	 */
	public function delete($humanKey, $siteId = null)
	{
		// More bleurgh-ing.
		if (!CACHE) {
			return false;
		}
		
		// Site setting.
		if (!$siteId) {
			$siteId = BluApplication::getSetting('siteId');
		}
		
		// Delete from cache
		$result = $this->_deleteCached($humanKey, $siteId);
		
		// Delete references
		if ($result) {
			$this->_deleteReference($humanKey, $siteId);
			$this->_deleteFileCached($humanKey, $siteId);
		}
		
		// Return
		return $result;
	}
	
	/**
	 *	Remove reference to cache object. Careful.
	 *
	 *	@param string Human key
	 *	@param string Site ID.
	 *	@return bool Success
	 */
	protected function _deleteReference($humanKey, $siteId)
	{
		// Scramble key
		$key = md5($humanKey);

		// Remove from memcache references table
		$query = 'DELETE FROM memcacheReference 
			WHERE memcacheKey = "'.$this->_db->escape($key).'" 
				AND siteId = "'.$this->_db->escape($siteId).'" 
				AND prefix = "'.$this->_db->escape($this->_cachePrefix).'"';
		$this->_db->setQuery($query);
		$result = $this->_db->query();
		
		// Return
		return $result;
	}
	
	/**
	 *	Remove cache content.
	 *
	 *	@param string Human key
	 *	@param string Site ID.
	 *	@return bool Success
	 */
	protected function _deleteCached($humanKey, $siteId)
	{
		// Build cache key
		$key = md5($humanKey);
		$cacheKey = $this->_buildCacheKeyFragment($this->_cachePrefix, $siteId, $key);
		
		// Remove from cache
		$result = $this->_memcache->delete($cacheKey);
				
		if ($result === false) {
			if (($this->_cacheClient == 'memcached') && ($this->_memcache->getResultCode() == Memcached::RES_NOTFOUND)) {
				$result = true;
			}
		}
		
		// Return
		return $result;
	}
	
	/**
	 *	Delete related file cache object
	 *
	 *	@access protected
	 *	@param string Human key
	 *	@param string Site ID
	 *	@return bool Success
	 */
	protected function _deleteFileCached($humanKey, $siteId)
	{
		$filepath = $this->_getFileCachePath($humanKey, $siteId);
		return file_exists($filepath) ? unlink($filepath) : true;
	}
	
	/**
	 * Flush the entire cache for all sites
	 *
	 * @return bool Success.
	 */
	public function flush()
	{
		// More bleurgh-ing.
		if (!CACHE) {
			return false;
		}
		
		// Flush cache
		$result = $this->_memcache->flush();
		
		// Delete all references
		if ($result) {
			$this->_flushReferences();
			$this->_flushFileCached();
		}
		
		// Return
		return $result;
	}
	
	/**
	 *	Flush all cache references
	 *
	 *	@access protected
	 *	@return bool Success
	 */
	protected function _flushReferences()
	{
		$query = 'TRUNCATE TABLE memcacheReference';
		$this->_db->setQuery($query);
		$result = $this->_db->query();
		
		return $result;
	}
	
	/**
	 *	Flush all file cache objects
	 *
	 *	@access protected
	 *	@return bool Success
	 */
	protected function _flushFileCached()
	{
		$success = true;
		
		$sites = BluApplication::getSetting('sites');
		foreach ($sites as $siteId) {
			if (!Utility::rmdir(BLUPATH_BASE.'/cache/'.$siteId.'/data')) {
				$success = false;
			}
		}
		
		return $success;
	}
	
	/**
	 *	Get all cache references.
	 *
	 *	@param array/string Site ID(s).
	 *	@param string 'like' comparison string for human keys.
	 *	@return array References.
	 */
	public function getReferences($siteIds = null, $like = null)
	{
		// Bye.
		if (!CACHE) {
			return false;
		}
		
		// Build query from options.
		$query = 'SELECT *
			FROM `memcacheReference` AS `mr`
			WHERE humanKey NOT LIKE "viewcache_%" AND '; 
		if ($siteIds) {
			$this->_db->escape($siteIds);
			if (is_array($siteIds)) {
				$query .= 'mr.siteId IN ("'.implode('", "', $siteIds).'")';
			} else {
				$query .= 'mr.siteId = "'.$siteIds.'"';
			}
			$query .= ' AND ';
		}
		$query .= 'mr.prefix = "'.$this->_db->escape($this->_cachePrefix).'"';
		if ($like) {
			$query .= '	AND mr.humanKey LIKE "%'.$this->_db->escape($like).'%"';
		}

		// Get directly from database
		$this->_db->setQuery($query);
		$references = $this->_db->loadAssocList('humanKey');
		
		// Return
		return $references;
	}

	/**
	 * Get session memcache stats
	 * 
	 * @return array Stats
	 */
	public function getSessionStats()
	{
		$sessionCache = new Memcached;
		$connectionSuccess = $sessionCache->addServer(BluApplication::getSetting('memcacheSessionHost'), BluApplication::getSetting('memcacheSessionPort'));
		return $sessionCache->getStats();
	}

	/**
	 * Get memcache stats
	 * 
	 * @param Stats type
	 * @return array Stats
	 */
	public function getStats($type = null)
	{
		// Bleurgh
		if (!CACHE) {
			return false;
		}
		
		// Get stats
		switch($this->_cacheClient){
			case 'memcached':
				$stats = $this->_memcache->getStats();
				break;
				
			default:
				$stats = $this->_memcache->getExtendedStats($type);
				break;
		}
		
		// Return
		return $stats;
	}
	
	/**
	 *	Get the (compressed?) size of an object to be cached.
	 *
	 *	@param mixed Object
	 */
	protected function _getSize($object)
	{
		$serializeMethod = ULTRACACHE ? 'igbinary_serialize' : 'serialize';
		//$serializeMethod = 'serialize';
		$serializedLength = strlen($serializeMethod($object));
		return $serializedLength;
	}
	
	/**
	 *	Algorithm for building a bit of a cache key.
	 *
	 *	@param string Prefix
	 *	@param string Site ID
	 *	@param string Key
	 *	@return string Cache key.
	 */
	protected function _buildCacheKeyFragment($prefix, $siteId, $key)
	{
		return $prefix.'_'.$siteId.'_'.$key;
	}
	
	/**
	 *	Return theoretical path in filesystem for cache object
	 *
	 *	@access protected
	 *	@param string Human key
	 *	@param string Site ID
	 *	@return string
	 */
	protected function _getFileCachePath($humanKey, $siteId)
	{
		//$directory = BLUPATH_BASE.'/cache/'.$siteId.'/data';
		$directory = '/data/r4l/cache/recipe4living/data';	
		// Ensure directory exists
		if (!file_exists($directory)) {
			mkdir($directory, 0777);
		}
		
		return $directory.'/'.md5($humanKey).'.cache';
	}
	
	/**
	 *	Get debug info: cache hits
	 *
	 *	@access public
	 *	@return int
	 */
	public function getHits()
	{
		return $this->_debugHits;
	}
	
	/**
	 *	Get debug info: cache misses
	 *
	 *	@access public
	 *	@return int
	 */
	public function getMisses()
	{
		return $this->_debugMisses;
	}
	
	/**
	 *	Get debug info: cache builds
	 *
	 *	@access public
	 *	@return int
	 */
	public function getBuilds()
	{
		return $this->_debugBuilds;
	}
	
	/**
	 *	Get debug info: all cache calls
	 *
	 *	@access protected
	 *	@return array
	 */
	public function getLog()
	{
		return $this->_debugCalls;
	}
}

?>
