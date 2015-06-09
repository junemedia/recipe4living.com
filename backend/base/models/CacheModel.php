<?php

/**
 * Cache Model
 *
 * @package BluApplication
 * @subpackage BackendModels
 */
class BackendCacheModel extends BluModel
{
	/**
	 * Get cache items (from the references, not the actual cache itself)
	 *
	 * @param array/string Site ID(s).
	 * @param array Options.
	 * @return array.
	 */
	public function getCacheItems($siteIds = null, array $options = array())
	{
		// Get references
		$cacheItems = $this->_cache->getReferences($siteIds, isset($options['like']) ? $options['like'] : null);
		
		// Sort if requested
		if (isset($options['order'])) {
			$sortBy = $options['order'];
			$sortOrder = isset($options['direction']) ? $options['direction'] : Utility::SORT_ASC;
			$cacheItems = Utility::quickSort($cacheItems, $sortBy, $sortOrder);
		}
		
		// Return
		return $cacheItems;
	}

	/**
	 * Get a cache entry for a single site
	 *
	 * @param string Cache key
	 * @param string Site ID
	 * @return mixed Cache contents
	 */
	public function getEntry($key, $siteId = null)
	{		
		return $this->_cache->get($key, $siteId, 0, false);
	}
	
	/**
	* Get statistics from main cache instance
	*/
	public function getStats($type = null)
	{
		return $this->_cache->getStats($type);
	}
	
	/**
	* Get statistics from sessioncache instance
	*/
	public function getSessionStats()
	{
		return $this->_cache->getSessionStats();
	}

	/**
	 * Delete a cache entry from all sites
	 *
	 * @param string Cache key
	 * @return bool Success
	 */
	public function deleteEntry($key)
	{
		$success = true;
		$siteIds = $this->_getSites();
		foreach ($siteIds as $siteId) {
			if (!$this->_cache->delete($key, $siteId)) {
				$success = false;
			}
		}
		return $success;
	}
	
	/**
	 *	Delete all cache entries like [comparison]
	 *
	 *	@param string The comparison string.
	 *	@param array/string Site ID(s).
	 *	@return array Deleted keys.
	 */
	public function deleteEntriesLike($comparison)
	{
		$deleted = array();

		// Get cache items like comparison string
		$cacheItems = $this->getCacheItems(null, array(
			'like' => $comparison
		));
		if (empty($cacheItems)) {
			return $deleted;
		}

		// Delete them.
		foreach ($cacheItems as $cacheItem) {
			if ($this->deleteEntry($cacheItem['humanKey'], $cacheItem['siteId'])) {
				$deleted[$cacheItem['siteId']][] = $cacheItem['humanKey'];
			}
		}
		
		// Return deleted items
		return $deleted;
	}

	/**
	 * 	Rebuild core data
	 *
	 *	@todo
	 *	@access public
	 *	@return bool Success
	 */
	public function rebuildCoreData()
	{
		return true;
	}
	
	/**
	 *	Flushes all queued cache objects
	 *
	 *	@todo
	 *	@return array Purged entries.
	 */
	public function purgeQueue()
	{
		$deleted = array();
		
		// Get from cache object table, and rebuild
		//todo
		
		// Rebuild core data
		$this->rebuildCoreData();
		
		// Return purged items
		return $deleted;	
	}

	/**
	 * Get all cache queue items of the given type
	 * 
	 * @param string $type type of object
	 * @return array list of references 
	 */
	public function getQueueItems($type)
	{
		$query = 'SELECT `id`
			FROM `cacheObjects`
			WHERE `type` = "'.$this->_db->escape($type).'"';
		$this->_db->setQuery($query);
		return $this->_db->loadResultArray();
	}
	
	/**
	 * Remove object from cache purge queue
	 * 
	 * @param string $id object id
	 * @param string $type object type
	 */
	public function removeQueueItem($id, $type)
	{
		$query = 'DELETE FROM `cacheObjects`
			WHERE `id` = "'.$this->_db->escape($id).'" 
				AND `type` = "'.$this->_db->escape($type).'"';
		$this->_db->setQuery($query);
		$this->_db->query();
	}
	
	/**
	 * Insert item into cache purge queue 
	 * 
	 * @param string $id object id
	 * @param string $type object type
	 */
	public function addQueueItem($id, $type)
	{
		$query = 'INSERT IGNORE INTO `cacheObjects`
			SET `id` = "'.$this->_db->escape($id).'",
				`type` = "'.$this->_db->escape($type).'"';
		$this->_db->setQuery($query);
		$this->_db->query();
	}

	/**
	 *	Get the time a cache key was set
	 *
	 *	@access public
	 *	@param string Human key
	 *	@param string Site ID
	 *	@return int Timestamp
	 */
	public function getTimeSet($humanKey, $siteId = null)
	{
		if (!$siteId) {
			$siteId = BluApplication::getSetting('siteId');
		}

		return $this->_cache->getTimeSet($humanKey, $siteId);
	}
}

?>
