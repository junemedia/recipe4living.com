<?

/**
 *	Articles of type articleType = 'slideshow'
 */
class SlideshowObject extends ItemObject{

	public function __construct($id){
			
		parent::__construct((int) $id, 'slideshow');		
		//$this->_setVariables();
		
	}
	
	public function getType($format){
		switch($format) {
			case 'single': return 'slideshow'; break;
			case 'plural': return 'slideshows'; break;
		}
	}
	
	/**
	 *	Get the next slide from the given one.
	 *
	 *	@args (SlideObject) current: the current slide.
	 *	@return (SlideObject) the next slide.
	 */
	public function getNextSlide($current){
		if (!Utility::is_loopable($this->slides)){ return null; }
		for ($i = 0; $i < count($this->slides); $i++){
			if ($this->slides[$i]->id == (int)$current->id && $i < count($this->slides) - 1){ return $this->slides[++$i]; }
		}
		return null;
	}
		
	/**
	 *	Get the previous slide from the given one.
	 *
	 *	@args (SlideObject) current: the current slide.
	 *	@return (SlideObject) the previous slide.
	 */
	public function getPreviousSlide($current){
		if (!Utility::is_loopable($this->slides)){ return null; }
		for ($i = 0; $i < count($this->slides); $i++){
			if ($this->slides[$i]->id == (int)$current->id && $i > 0){ return $this->slides[--$i]; }
		}
		return null;
	}
	
	
	
	
	###							PRIVATE CONVENIENCE FUNCTIONS							###
	
	/**
	 *	Publicly available variables
	 */
	protected function _setVariables(){
		parent::_setVariables();
		$this->slides = $this->_getSlides();
		$this->image = $this->_getImage();
		$this->links = $this->_getLinks();
		return $this;
	}
	
	/**
	 *	Gets all slides allocated to this slideshow.
	 *
	 *	@return (array) a list of SlideObject objects.
	 */
	private function _getSlides(){
		
		// Get article details from cache/DB
		$records = $this->_cache->get('slideshow_'.$this->id.'_slides');
		if ($records === false){
		
			// Get from DB
			$query = "SELECT `imageID` 
				FROM `xrefslideshowimages` 
				WHERE `slideshowID` = ".$this->id." 
				ORDER BY `slideshowimageXREF` ASC";		/* <-- Keep the ORDER BY here, to ensure consistency with the order that the slides are displayed in. */
			$this->_db->setQuery($query);
			$records = $this->_db->loadAssocList();
		
		    // Store in cache
			$this->_cache->set('slideshow_'.$this->id.'_slides', $records);
			
		}
		
		//spit out
		return $this->_wrapSlides($records);
		
	}	
	
	/**
	 *	What this does is takes the first slide's image to use as the slideshow image.
	 *
	 *	Overrides parent ItemObject method.
	 */
	private function _getImage(){
		return isset($this->slides) && Utility::is_loopable($this->slides) ? $this->slides[0]->image : null;
	}
	
	/**
	 *	If we have an arbitrary recordset (2d array) wrap them into SlideObjects.
	 *
	 *	@args (Array) recordset: an array containing records, which are themselves arrays containing keys...
	 *		imageID: id of slide
	 *	@return (Array) an array of LinkObjects
	 */
	private function _wrapSlides(array $recordset){
		$wrappeditems = array();
		if (Utility::is_loopable($recordset)){
			foreach($recordset as $record){
				$wrappeditems[] = BluApplication::getObject('slide', (int)$record['imageID']);
			}
		}
		return $wrappeditems;
	}
	
	/**
	 *	Get link details.
	 *
	 *	@return array Links
	 */
	private function _getLinks() 
	{
		$cacheKey = 'slideshow_'.$this->id.'_links';
		$links = $this->_cache->get($cacheKey);
		if ($links === false) {
			
			// Get from database
			$query = 'SELECT l.*
				FROM `xrefArticleRelatedlink` AS `x`
					LEFT JOIN `links` AS `l` ON x.relatedlinkID = l.linkID
				WHERE x.articleID = '.(int) $this->id.'
				ORDER BY x.articleRelatedlinkXREF ASC';
			$this->_db->setQuery($query);
			$links = $this->_db->loadAssocList();
			
			// Store
			if (!empty($links)) {
				foreach ($links as &$link) {
					$link = array(
						'id' => $link['linkID'],
						'title' => $link['linkTitle'],
						'url' => 'http://'.$link['linkUrl']
					);
				}
				unset($link);
				$this->_cache->set($cacheKey, $links);
			}
		}
		return $links;
	}
	
}

?>