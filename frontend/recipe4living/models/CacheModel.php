<?php

/**
 * Cache Model
 *
 * @package BluApplication
 * @subpackage FrontendModels
 */
class ClientFrontendCacheModel extends BluModel
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
}

?>