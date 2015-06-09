<?

/**
 *	Articles of type articleType = 'landingpage'
 */
class LandingpageObject extends ItemObject{

	public function __construct($id){

		parent::__construct((int) $id, 'landingpage');
		
	}
	
	public function getType($format){
		switch($format) {
			case 'single': return 'essential guide'; break;
			case 'plural': return 'essential guides'; break;
		}
	}
	
	/**
	 *	Gets a list of links associated with this landing page, divided into sections.
	 *
	 *	@return (2d array) an associative array:
	 *		key: section name of the section that the link falls in, i.e. article, slideshow (corresponds to the 'sectionType' column in the `xreflandingpagelinks` table)
	 *		value: array containing the LinkObject data in that section.
	 */
	public function getLinks(){
		
		/* Get data */
		$cacheKey = $this->_cacheObjectID . '_links';
		$links = $this->_cache->get($cacheKey);
		if ($links === false){
			
			/* Get raw data */
			$query = 'SELECT *
				FROM `links` AS `l`
					LEFT JOIN `xreflandingpagelinks` `x` ON l.linkID = x.linkID
				WHERE x.landingPageID = "' . $this->id . '"';
			$records = $this->_fetch($query, $cacheKey, null, null, false);
			
			/* Format data */
			$links = array();
			while(Utility::is_loopable($records) && $record = array_shift($records)){
				
				// Create new arrays for new section types.
				if (!array_key_exists($record['sectionType'], $links)){
					$links[$record['sectionType']] = array();
				}
				
				// Get rid of any '(.*)workitmom.com' domain bit in string.
				Router::parseLegacy($record['linkUrl']);
				
				// Append to array
				$links[$record['sectionType']][] = $record;
				
			}
			
			/* Cache data */
			$this->_cache->set($cacheKey, $links);
			
		}
		
		/* Return */
		return $links;
		
	}
	
	/**
	 *	Get a list of links by section
	 */
	public function getLinksBySection($section){
		
		// Get model 
		$linksModel = BluApplication::getModel('links');
		
		// Get links
		return $linksModel->getLandingpageLinksBySection($this->id, $section);
		
	}
	
	
	
}

?>