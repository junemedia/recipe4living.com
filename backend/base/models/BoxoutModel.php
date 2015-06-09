<?php

/**
 * BoxOut Model
 *
 * @package BluApplication
 * @subpackage BluModels
 */
class BackendBoxoutModel extends ClientBoxoutModel
{
	/**
	 *	Get a list of all boxes
	 *
	 *	@access public
	 *	@return array
	 */
	public function getBoxes()
	{
		// Get IDs
		$query = 'SELECT bo.id
			FROM `boxOut` AS `bo`';
		$this->_db->setQuery($query);
		$boxes = $this->_db->loadResultArray();
		
		// Get boxes
		$this->addDetails($boxes);
		
		// Return
		return $boxes;
	}
	
	/**
	 *	Get box content. Live. Raw.
	 *
	 *	@access public
	 *	@param int Box content ID
	 *	@return array
	 */
	public function getBoxContent($contentId)
	{
		// Query
		$query = 'SELECT boc.*
			FROM `boxOutContent` AS `boc`
			WHERE boc.id = '.(int) $contentId;
		$this->_db->setQuery($query);
		$boxContent = $this->_db->loadAssoc();
		$boxContent['info'] = unserialize($boxContent['info']);
		
		// Return
		return $boxContent;
	}
	
	/**
	 *	Get all content of a box
	 *
	 *	@access public
	 *	@param int Box ID
	 *	@return array
	 */
	public function getBoxContents($boxId)
	{
		$query = 'SELECT boc.*
			FROM `boxOutContent` AS `boc`
			WHERE boc.boxId = '.(int) $boxId.'
				ORDER BY boc.sequence, 
					boc.date DESC,
					boc.id ASC';
		$this->_db->setQuery($query);
		$boxContents = $this->_db->loadAssocList('id');
		if (!empty($boxContents)) {
			foreach ($boxContents as &$content) {
				$content['info'] = unserialize($content['info']);
			}
			unset($content);
		}
		return $boxContents;
	}
	

	
	/**
	 *	Add a new box content entry
	 *
	 *	@access public
	 *	@param int Box ID
	 *	@parma string Language Code
	 *	@param string Title
	 *	@param string Subtitle
	 *	@param string Body text
	 *	@param string Link
	 *	@param array Box-type-specific info
	 *	@param int Sequence
	 *	@return int Content ID
	 */
	public function addBoxContent($boxId, $langCode, $title = null, $subtitle = null, $text = null, $link = null, $info = null, $sequence = null)
	{
		if (empty($boxId)) {
			return false;
		}
		
		// Query builder
		$params = array();
		if (!is_null($title)) {
			$params['title'] = '"'.$this->_db->escape($title).'"';
		}
		if (!is_null($subtitle)) {
			$params['subtitle'] = '"'.$this->_db->escape($subtitle).'"';
		}
		if (!is_null($text)) {
			$params['text'] = '"'.$this->_db->escape($text).'"';
		}
		if (!is_null($link)) {
			$params['link'] = '"'.$this->_db->escape($link).'"';
		}
		if (!is_null($info)) {
			$box = $this->getBox($boxId);
			$info = $this->_validateInfo($box, $info);
			$params['info'] = '"'.$this->_db->escape(serialize($info)).'"';
		}
		if (!is_null($sequence)) {
			$params['sequence'] = (int) $sequence;
		}
		if (empty($params)) {
			return false;
		}
		
		// Build query string
		$params['lang'] = '"'.$this->_db->escape($langCode).'"';
		$params['boxId'] = (int) $boxId;
		$params['date'] = 'NOW()';
		foreach ($params as $field => &$value) {
			$value = '`'.$field.'` = '.$value;
		}
		unset($value);
		
		// Execute
		$query = 'INSERT INTO `boxOutContent`
			SET '.implode(', ', $params);
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Return
		return $this->_db->getInsertID();
	}
	
	/**
	 *	Update a box content entry
	 *
	 *	@access public
	 *	@param int Content ID
	 *	@param string Language code
	 *	@param string Title
	 *	@param string Subtitle
	 *	@param string Body text
	 *	@param string Link
	 *	@param array Box-type-specific info
	 *	@param int Sequence
	 *	@return bool Success
	 */
	public function updateBoxContent($contentId, $langCode = null, $title = null, $subtitle = null, $text = null, $link = null, $info = null, $sequence = null)
	{
		if (empty($contentId)) {
			return false;
		}
		
		// Query builder
		$params = array();
		if (!is_null($langCode)) {
			$params['lang'] = '"'.$this->_db->escape($langCode).'"';
		}
		if (!is_null($title)) {
			$params['title'] = '"'.$this->_db->escape($title).'"';
		}
		if (!is_null($subtitle)) {
			$params['subtitle'] = '"'.$this->_db->escape($subtitle).'"';
		}
		if (!is_null($text)) {
			$params['text'] = '"'.$this->_db->escape($text).'"';
		}
		if (!is_null($link)) {
			$params['link'] = '"'.$this->_db->escape($link).'"';
		}
		if (!is_null($info)) {
			$box = $this->getBoxFromContent($contentId);
			$info = $this->_validateInfo($box, $info);
			$params['info'] = '"'.$this->_db->escape(serialize($info)).'"';
		}
		if (!is_null($sequence)) {
			$params['sequence'] = (int) $sequence;
		}
		if (empty($params)) {
			return false;
		}
		
		// Build query string
		$params['date'] = 'NOW()';
		foreach ($params as $field => &$value) {
			$value = '`'.$field.'` = '.$value;
		}
		unset($value);
		
		// Execute
		$query = 'UPDATE `boxOutContent`
			SET '.implode(', ', $params).'
			WHERE `id` = '.(int) $contentId;
		$this->_db->setQuery($query);
		$result = $this->_db->query();
		
		// Return
		return $result;
	}
	
	/**
	 *	Validate info array, depending on box (type, usually)
	 *
	 *	@access protected
	 *	@param array Box
	 *	@param array Info
	 *	@return array Validated info
	 */
	protected function _validateInfo($box, $info)
	{
		switch ($box['type']) {
			case 'featuredItems':
				$info = array_intersect_key($info, array_flip(array('item')));
				if (isset($info['item'])) {
					$info['item'] = (int) $info['item'];
				}
				break;
				
			case 'featuredUsers':
				$info = array_intersect_key($info, array_flip(array('user')));
				if (isset($info['user'])) {
					$info['user'] = (int) $info['user'];
				}
				break;
		}
		return $info;
	}
	
	/**
	 *	Set the image for a box content entry
	 *
	 *	@access public
	 *	@param int Content ID
	 *	@param string Image filename
	 *	@return bool Success
	 */
	public function setBoxContentImage($contentId, $imageName)
	{
		$query = 'UPDATE `boxOutContent`
			SET `imageName` = "'.$this->_db->escape($imageName).'"
			WHERE `id` = '.(int) $contentId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	* Save extra image to the info column
	*
	* @access public
	* @param int Content Id
	* @param string Image filename
	* @return bool Success
	*/
	public function setBoxExtraImage($contentId, $imageName)
	{
		$query = 'SELECT info FROM boxOutContent WHERE id='.(int)$contentId;
		$this->_db->setQuery($query);
		$content = unserialize($this->_db->loadResult());
		$content['extraImage'] = $imageName;

		$query = 'UPDATE boxOutContent SET info="'.$this->_db->escape(serialize($content)).'" WHERE id='.(int)$contentId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Delete a box content entry
	 *
	 *	@access public
	 *	@param int Content ID
	 *	@return bool Success
	 */
	public function deleteBoxContent($contentId)
	{
		$query = 'DELETE FROM `boxOutContent`
			WHERE `id` = '.(int) $contentId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Flush a box
	 *
	 *	@access public
	 *	@param int Box ID
	 *	@return bool Success
	 */
	public function flushBox($boxId)
	{
		$cacheModel = BluApplication::getModel('cache');
		return $cacheModel->deleteEntriesLike('box_'.$boxId);
	}
	
	/**
	 *	Flush a box content entry
	 *
	 *	@access public
	 *	@param int Box content ID
	 *	@return bool Success
	 */
	public function flushBoxContent($contentId)
	{
		// Just flush its box. 
		$box = $this->getBoxFromContent($contentId);
        
        // Refresh the homepage slider
        if($box['id'] == 9){
            /**
            *  Add by Leon
            * Let's delete the hompage slider cache item manually
            */
            
            // Delete the cached items
            $cache = BluApplication::getCache();
            $cache->delete("boxSlugMapping");
            $cache->delete('box_9_recipe4living_EN_a:1:{s:5:"limit";i:7;}');
            
            // Refresh the homepage view
            $cache->delete("viewcache_");
        }
        
		return $this->flushBox($box['id']);
	}
	
	/**
	 *	Get link categories
	 *
	 *	@access public
	 *	@return array Link categories
	 */
	 function getLinkCategories() {
		// static
		$linkCategories = array();
		$linkCategories[1] = 'Press Releases';
		$linkCategories[2] = 'Press Coverage';
		$linkCategories[3] = 'Recipe4Living Articles on the Web';
		return $linkCategories;
	 }
	
	
}

?>