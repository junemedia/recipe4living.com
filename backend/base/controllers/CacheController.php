<?php

/**
 * Cache Controller
 *
 * @package BluApplication
 * @subpackage BackendControllers
 */
class CacheController extends ClientBackendController
{
	/**
	 *	Cache overview
	 *
	 *	@param bool Force show listing
	 */
	public function view($showListing = false)
	{
		// No.
		if (!CACHE) {
			return include (BLUPATH_BASE_TEMPLATES.'/cache/disabled.php');
		}

		// Get site ID
		$siteId = BluApplication::getSetting('siteId');

		// Get model
		$cacheModel = BluApplication::getModel('cache');

		// View individual item
		if ((!$showListing) && $key = Request::getString('key')) {
			
			// Get entry
			$item = $cacheModel->getEntry($key, $siteId);
			$size = strlen(serialize($item));
			$date = $cacheModel->getTimeSet($key, $siteId);
			$date = date('d/m/y H:i:s', $date);
			
			// Display entry
			switch ($this->_doc->getFormat()) {
				case 'json':
					echo json_encode($item);
					break;
					
				default:
					echo '<p>Variable is (uncompressed) '.Template::fileSize($size).' in memcached, set on '.$date.'</p>';
					switch (Request::getString('mode')) {
						case 'export':
							echo '<pre>';
							var_export($item);
							echo '</pre>';
							break;
							
						default:
							var_dump($item);
							break;
					}
					break;
			}

		// Show overview
		} else {
			echo Messages::getMessages();
			$cacheStats = $cacheModel->getStats();
			$sessionStats = $cacheModel->getSessionStats();
			include(BLUPATH_BASE_TEMPLATES.'/cache/stats.php');
			include(BLUPATH_BASE_TEMPLATES.'/cache/actions.php');
		}
	}
	
	/**
	 * View cache key entries.
	 */
	public function listing()
	{
		// No.
		if (!CACHE) {
			return include (BLUPATH_BASE_TEMPLATES.'/cache/disabled.php');
		}

		// Get site ID
		$siteId = BluApplication::getSetting('siteId');

		// Get cache items
		$cacheModel = BluApplication::getModel('cache');
		$cacheItems = $cacheModel->getCacheItems($siteId, array(
			'order' => 'humanKey',
			'direction' => Utility::SORT_ASC
		));
		$cacheStats = $cacheModel->getStats();
		$sessionStats = $cacheModel->getSessionStats();
		
		// Template
		switch ($this->_doc->getFormat()) {
			case 'json':
				// Items only
				echo json_encode($cacheItems);
				break;
				
			default:
				// Items and stats
				$count = count($cacheItems);
				include (BLUPATH_BASE_TEMPLATES.'/cache/actions.php');
				include (BLUPATH_BASE_TEMPLATES.'/cache/keys.php');
				break;
		}
	}

	/**
	 * Delete keys by comparison
	 */
	public function deleteEntriesLike()
	{
		// Get list of keys
		if ($keys = Request::getString('keys')) {
			$keys = explode("\n", $keys);
		}

		// Delete all matching keys
		$cacheModel = BluApplication::getModel('cache');
		$updated = array();
		if (!empty($keys)) {
			foreach ($keys as $key) {
				$updated += $cacheModel->deleteEntriesLike($key);
			}
		}
	
		// Output list of deleted items
		switch ($this->_doc->getFormat()) {
			case 'json':
				echo json_encode($updated);
				break;
			default:
				$updateType = 'deleted';
				include_once(BLUPATH_BASE_TEMPLATES.'/cache/updates.php');
				$this->view();
				break;
		}
	}
	
	/**
	 * Queue a list of items for next cache purge
	 */
	public function queueItems()
	{
		$ids = Request::getString('ids');
		$type = Request::getCmd('type', 'product');
		
		// Queue items
		$updated = array();
		if (($ids = explode("\n", $ids)) && !empty($ids)) {
			$cacheModel = BluApplication::getModel('cache');
			foreach ($ids as $id) {
				$updated['All sites'][] = $id;
				$cacheModel->addQueueItem($id, $type);
			}
		}

		// Output list of items
		switch ($this->_doc->getFormat()) {
			case 'json':
				echo json_encode($updated);
				break;
			default:
				$updateType = 'queued';
				include_once(BLUPATH_BASE_TEMPLATES.'/cache/updates.php');
				$this->view();
				break;
		}
	}
	
	/**
	 * Purges the cache deletion queue
	 */
	public function purgeQueue()
	{
		// Purge cache objects queue
		$cacheModel = BluApplication::getModel('cache');
		$updated = $cacheModel->purgeQueue();
		
		// Output list of purged items
		switch ($this->_doc->getFormat()) {
			case 'json':
				echo json_encode($deleted);
				break;
			default:
				$updateType = 'deleted';
				include_once(BLUPATH_BASE_TEMPLATES.'/cache/updates.php');
				$this->view();
				break;
		}
	}
	
	/**
	 *	Delete individual cache entry
	 *
	 *	@access public
	 */
	public function deleteEntry()
	{
		// Get request
		$key = Request::getString('key');
		
		// Get model
		$cacheModel = BluApplication::getModel('cache');
		
		// Remove
		$deleted = $cacheModel->deleteEntry($key);
		
		// Output
		switch ($this->_doc->getFormat()) {
			case 'json':
				echo json_encode($deleted);
				break;
				
			default:
				if ($deleted) {
					Messages::addMessage('Deleted entry <code>'.$key.'</code>');
				}
				$this->view(true);
				break;
		}
	}
}

?>
