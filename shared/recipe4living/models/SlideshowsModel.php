<?php

/**
 *	Slideshows model
 *
 *	@package BluApplication
 *	@subpackage SharedModels
 */
class ClientSlideshowsModel extends BluModel
{
	
	/**
	 *	Get slideshows
	 *
	 *	@access public
	 *  @param int page
	 *  @param int limit
	 *  @param int total
	 *  @param bool featured
	 *	@return array slideshows
	 */
	public function getSlideshows($page = 1, $limit = 10, &$total = NULL, $live = NULL, $featured = false)
	{
		$where = '';
		if($live || $featured) {
			$where = 'WHERE ';
		}
		if($live) {
			$where .= 'live=1';
		}
		if($featured) {
			if($live) {
				$where .= ' AND ';
			}
			$where .= 'featured=1';
		}
		$query = 'SELECT id, title, body,filename, live, featured, sequence, UNIX_TIMESTAMP(added) AS added
					FROM slideshows
					'.$where.'
					ORDER BY sequence';
		$this->_db->setQuery($query, ($page-1)*$limit, $limit, true);
		$slideshows = $this->_db->loadAssocList('id');
		$total = $this->_db->getFoundRows();
		return $slideshows;
	}
	
	/**
	 *	Get slideshow
	 *
	 *	@access public
	 *  @param int slideshowId
	 *	@return array slideshow
	 */
	public function getSlideshow($slideshowId)
	{
		$cacheKey = 'slideshow_'.$slideshowId;
		$slideshow = $this->_cache->get($cacheKey);
		if($slideshow===false) {
			$query = 'SELECT id,title, filename,body, live, featured, sequence, UNIX_TIMESTAMP(added) AS added
						FROM slideshows
						WHERE id='.(int)$slideshowId;
			$this->_db->setQuery($query);
			$slideshow = $this->_db->loadAssoc();
			$this->_cache->set($cacheKey,$slideshow);
		}
		return $slideshow;
	}
	
	/**
	 *	Get slideshow items
	 *
	 *	@access public
	 *  @param int slideshowId
	 *	@return array slideshow
	 */
	public function getSlideshowItems($slideshowId, $page = null, $limit = null, &$total = null)
	{
		$cacheKey = 'slideshow_items_'.$slideshowId;
		$slideshowItems = $this->_cache->get($cacheKey);
		if($slideshowItems===false) {
			$query = 'SELECT SQL_CALC_FOUND_ROWS id, title, body, filename, sequence
						FROM slideshowItems
						WHERE slideshowId='.(int)$slideshowId.'
						ORDER BY sequence';
			$this->_db->setQuery($query, $limit ? ($page-1)*$limit : null, $limit);
			$slideshowItems = $this->_db->loadAssocList();
			$total = $this->_db->getFoundRows();
			$this->_cache->set($cacheKey,$slideshowItems);
		}
		return $slideshowItems;
	}

	/**
	 *	Get slideshow item
	 *
	 *	@access public
	 *  @param int itemId
	 *	@return array item
	 */
	public function getSlideshowItem($itemId)
	{
		$query = 'SELECT id, slideshowId, title, body, filename, sequence
					FROM slideshowItems
					WHERE id='.(int)$itemId;
		$this->_db->setQuery($query);
		$slideshowItem = $this->_db->loadAssoc();
		return $slideshowItem;
	}

}

?>
