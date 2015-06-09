<?php

/**
 *	Slideshows model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendSlideshowsModel extends ClientSlideshowsModel
{
	
	/**
	 *	Add slideshow
	 *
	 *	@access public
	 *  @param string title
	 *  @param string filename
	 *	@return bool result
	 */
	public function addSlideshow($title, $filename,$body)
	{
		$query = 'INSERT INTO slideshows (title,body,filename,live, added) VALUES ("'.$this->_db->escape($title).'","'.$this->_db->escape($body).'",'.($filename?'"'.$this->_db->escape($filename).'"':'NULL').', 0, NOW())';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Update slideshow
	 *
	 *	@access public
	 *  @param int slideshowId
	 *  @param string title
	 *  @param string filename
	 *	@return bool result
	 */
	public function updateSlideshow($slideshowId, $title, $filename,$body)
	{
		$cacheKey = 'slideshow_'.$slideshowId;
		$this->_cache->delete($cacheKey);
		$query = 'UPDATE slideshows SET title="'.$this->_db->escape($title).'", body="'.$this->_db->escape($body).'"'.($filename?',filename="'.$this->_db->escape($filename).'"':'').' WHERE id='.(int)$slideshowId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Delete slideshow
	 *
	 *	@access public
	 *  @param int slideshowId
	 *	@return bool result
	 */
	public function deleteSlideshow($slideshowId)
	{
		$cacheKey = 'slideshow_'.$slideshowId;
		$this->_cache->delete($cacheKey);
		$query = 'DELETE FROM slideshows WHERE id='.(int)$slideshowId;
		$this->_db->setQuery($query);
		$result1 = $this->_db->query();
		$query = 'DELETE FROM slideshowItems WHERE slideshowId='.(int)$slideshowId;
		$this->_db->setQuery($query);
		$result2 = $this->_db->query();
		return ($result1 && $result2);
	}
	
	/**
	 *	Delete slideshow image
	 *
	 *	@access public
	 *  @param int slideshowId
	 *	@return bool result
	 */
	public function deleteImage($slideshowId)
	{
		$cacheKey = 'slideshow_'.$slideshowId;
		$this->_cache->delete($cacheKey);
		$query = 'UPDATE slideshows SET filename=NULL WHERE id='.(int)$slideshowId;
		$this->_db->setQuery($query);
		$result = $this->_db->query();
		return $result;
	}
	
	/**
	 *	Set slideshow status
	 *
	 *	@access public
	 *  @param int slideshowId
	 *  @param int status
	 *	@return bool result
	 */
	public function setSlideshowStatus($slideshowId, $status)
	{
		$cacheKey = 'slideshow_'.$slideshowId;
		$this->_cache->delete($cacheKey);
		$query = 'UPDATE slideshows SET live='.(int)$status.' WHERE id='.(int)$slideshowId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Set slideshow featured status
	 *
	 *	@access public
	 *  @param int slideshowId
	 *  @param int status
	 *	@return bool result
	 */
	public function setSlideshowFeaturedStatus($slideshowId, $status)
	{
		$cacheKey = 'slideshow_'.$slideshowId;
		$this->_cache->delete($cacheKey);
		$query = 'UPDATE slideshows SET featured='.(int)$status.' WHERE id='.(int)$slideshowId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}

	/**
	 *	Update position of a slideshow
	 *
	 *	@access public
	 *  @param int slideshow ID
	 *	@return bool result
	 */
	public function moveSlideshow($slideshowId, $move)
	{
		$slideshow = $this->getSlideshow($slideshowId);
		if(!$slideshow) {
			return false;
		}
		
		$cacheKey = 'slideshow_'.$slideshowId;
		$this->_cache->delete($cacheKey);
		
		if($move=='down') {
			$query = 'SELECT COUNT(*) FROM slideshows';
			$this->_db->setQuery($query);
			$slideshowCount = $this->_db->loadResult();
			if($slideshow['sequence']>=$slideshowCount) {
				return false;
			}
			$sequence = $slideshow['sequence'] + 1;
			$query = 'UPDATE slideshows SET sequence=sequence-1 WHERE sequence='.$sequence;
			$this->_db->setQuery($query);
			$result = $this->_db->query();
		}
		elseif($move=='up') {
			if($slideshow['sequence']<=1) {
				return false;
			}
			$sequence = $slideshow['sequence'] - 1;
			$query = 'UPDATE slideshows SET sequence=sequence+1 WHERE sequence='.$sequence;
			$this->_db->setQuery($query);
			$result = $this->_db->query();
		}
		
		$query = 'UPDATE slideshows SET sequence='.$sequence.' WHERE id='.(int)$slideshowId;
		$this->_db->setQuery($query);
		return ($result && $this->_db->query());
	}
	
	/**
	 *	Add slideshow item
	 *
	 *	@access public
	 *  @param int slideshow ID
	 *  @param string title
	 *  @param string body
	 *  @param string filename
	 *	@return bool result
	 */
	public function addSlideshowItem($slideshowId, $title, $body, $filename)
	{
		$cacheKey = 'slideshow_'.$slideshowId;
		$this->_cache->delete($cacheKey);
		$cacheKey = 'slideshow_items_'.$slideshowId;
		$this->_cache->delete($cacheKey);
		$query = 'SELECT COUNT(*) FROM slideshowItems WHERE slideshowID='.(int)$slideshowId;
		$this->_db->setQuery($query);
		$itemCount = $this->_db->loadResult();
		$query = 'INSERT INTO slideshowItems (slideshowId, title, body, filename, sequence) VALUES ('.(int)$slideshowId.', "'.$this->_db->escape($title).'", "'.$this->_db->escape($body).'",'.($filename?' "'.$this->_db->escape($filename).'"':'NULL').', '.($itemCount+1).')';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}

	/**
	 *	Update slideshow item
	 *
	 *	@access public
	 *  @param int itemId
	 *  @param string title
	 *  @param string body
	 *  @param string filename
	 *	@return bool result
	 */
	public function updateSlideshowItem($itemId, $title, $body, $filename)
	{
		$item = $this->getSlideshowItem($itemId);
		if(!$item) {
			return false;
		}
		$slideshowId = $item['slideshowId'];
		
		$cacheKey = 'slideshow_'.$slideshowId;
		$this->_cache->delete($cacheKey);
		$cacheKey = 'slideshow_items_'.$slideshowId;
		$this->_cache->delete($cacheKey);
		
		$query = 'UPDATE slideshowItems SET title="'.$this->_db->escape($title).'", body="'.$this->_db->escape($body).'"'.($filename?', filename="'.$this->_db->escape($filename).'"':'').' WHERE id='.(int)$itemId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}

	/**
	 *	Delete slideshow item
	 *
	 *	@access public
	 *  @param int itemId
	 *	@return bool result
	 */
	public function deleteSlideshowItem($itemId)
	{
		$item = $this->getSlideshowItem($itemId);
		if(!$item) {
			return false;
		}
		$slideshowId = $item['slideshowId'];
		
		$cacheKey = 'slideshow_'.$slideshowId;
		$this->_cache->delete($cacheKey);
		$cacheKey = 'slideshow_items_'.$slideshowId;
		$this->_cache->delete($cacheKey);
		
		$query = 'DELETE FROM slideshowItems WHERE id='.(int)$itemId;
		$this->_db->setQuery($query);
		$result1 = $this->_db->query();
		
		$query = 'UPDATE slideshowItems SET sequence=sequence-1 WHERE slideshowId='.$slideshowId.' AND sequence>'.$item['sequence'];
		$this->_db->setQuery($query);
		$result2 = $this->_db->query();
		
		return ($result1 && $result2);
	}
	
	/**
	 *	Delete item image
	 *
	 *	@access public
	 *  @param int itemId
	 *	@return bool result
	 */
	public function deleteItemImage($itemId)
	{
		$cacheKey = 'slideshow_'.$itemId;
		$this->_cache->delete($cacheKey);
		$query = 'UPDATE slideshowItems SET filename=NULL WHERE id='.(int)$itemId;
		$this->_db->setQuery($query);
		$result = $this->_db->query();
		return $result;
	}

	/**
	 *	Update position of a slideshow item
	 *
	 *	@access public
	 *  @param int itemId
	 *	@return bool result
	 */
	public function moveSlideshowItem($itemId, $move)
	{
		$item = $this->getSlideshowItem($itemId);
		if(!$item) {
			return false;
		}
		$slideshowId = $item['slideshowId'];
		
		$cacheKey = 'slideshow_'.$slideshowId;
		$this->_cache->delete($cacheKey);
		$cacheKey = 'slideshow_items_'.$slideshowId;
		$this->_cache->delete($cacheKey);
		
		if($move=='down') {
			$query = 'SELECT COUNT(*) FROM slideshowItems WHERE slideshowId='.(int)$slideshowId;
			$this->_db->setQuery($query);
			$itemCount = $this->_db->loadResult();
			if($item['sequence']>=$itemCount) {
				return false;
			}
			$sequence = $item['sequence'] + 1;
			$query = 'UPDATE slideshowItems SET sequence=sequence-1 WHERE slideshowId='.(int)$slideshowId.' AND sequence='.$sequence;
			$this->_db->setQuery($query);
			$result = $this->_db->query();
		}
		elseif($move=='up') {
			if($item['sequence']<=1) {
				return false;
			}
			$sequence = $item['sequence'] - 1;
			$query = 'UPDATE slideshowItems SET sequence=sequence+1 WHERE slideshowId='.(int)$slideshowId.' AND sequence='.$sequence;
			$this->_db->setQuery($query);
			$result = $this->_db->query();
		}
		
		$query = 'UPDATE slideshowItems SET sequence='.$sequence.' WHERE id='.(int)$itemId;
		$this->_db->setQuery($query);
		return ($result && $this->_db->query());
	}

}

?>
