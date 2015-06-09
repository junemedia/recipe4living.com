<?php

/**
 *	Giveaway model
 *
 *	@package BluApplication
 *	@subpackage SharedModels
 */
class ClientGiveawayModel extends BluModel
{
	
	/**
	 *	Get giveaways
	 *
	 *	@access public
	 *  @param int page
	 *  @param int limit
	 *  @param int total
	 *  @param int status
	 *	@return array giveaways
	 */
	public function getGiveaways($page = 1, $limit = 10, &$total = NULL, $status = NULL)
	{
		$where = '';

		if($status != NULL) {
			$where = ' AND status='.$status;
		}else
		{
			$where = ' AND status=1';
		}
		
		// Filter by start and end dates.
		$nullDate = '0000-00-00 00:00:00';
		$nowDate = date('Y-m-d H:i:s');
		//AND (endDate = "' . $nullDate . '" OR endDate >= "' . $nowDate . '")

		$query = 'SELECT id, articleid, type, title, description,image, createDate,publishDate,endDate, src, slug, guid,status,featured	FROM giveaways WHERE (publishDate ="'. $nullDate . '" OR publishDate <= "' . $nowDate . '") '.$where.' ORDER BY featured desc,publishDate desc';
		$this->_db->setQuery($query, ($page-1)*$limit, $limit, true);
		$giveaways = $this->_db->loadAssocList('id');
		$total = $this->_db->getFoundRows(); 
		if(!empty($giveaways))
		{
			foreach($giveaways as &$item)
			{
				$item['link'] = '/giveaway/'.$item['slug'].'.htm';
			}
		}
		return $giveaways;
	}
	
	public function getTaskLink($link, $task, $argument = null, $anotherArgument = null)
	{
		// Pull out slug
		$args = explode('/', $link);
		$slug = array_pop($args);

		// Inject task
		$args[] = $task;

		// Inject argument(s)
		if (strlen($argument)) {
			$args[] = $argument;
		}
		if (strlen($anotherArgument)) {
			$args[] = $anotherArgument;
		}

		// Reattach slug
		$args[] = $slug;

		// We are good
		return implode('/', $args);
	}
	
	public function getAllGiveaways($page = 1, $limit = 10, &$total = NULL)
	{
		$query = 'SELECT id, articleid, type, title, description,image, createDate,publishDate,endDate, src, slug, guid,status,featured	FROM giveaways WHERE status !=-1 ORDER BY featured desc,createDate desc';
		$this->_db->setQuery($query, ($page-1)*$limit, $limit, true);
		$giveaways = $this->_db->loadAssocList('id');
		$total = $this->_db->getFoundRows(); 
		if(!empty($giveaways))
		{
			foreach($giveaways as &$item)
			{
				$item['link'] = '/giveaway/'.$item['slug'].'.htm';
			}
		}
		return $giveaways;
	}
	
	/**
	 *	Get giveaway
	 *
	 *	@access public
	 *  @param int giveawayId
	 *	@return array giveaway
	 */
	public function getGiveaway($giveawayId,$forceRebuild=false)
	{
		$cacheKey = 'slideshow_'.$giveawayId;
		$giveaway = $this->_cache->get($cacheKey);
		if($forceRebuild){
			$giveaway = false;
		}
		
		if($giveaway===false) {
			$query = 'SELECT id, articleid, type, title, description,image, createDate,publishDate,endDate, src, slug, guid,status,featured	FROM giveaways WHERE id='.(int)$giveawayId;
			$this->_db->setQuery($query);
			$giveaway = $this->_db->loadAssoc();
			$this->_cache->set($cacheKey,$giveaway);
		}
		
		$giveaway['link'] = '/giveaway/'.$giveaway['slug'].'.htm';
		return $giveaway;
	}
	
	public function getLiveGiveaway($status = 1,$forceRebuild=false)
	{
		$cacheKey = 'slideshow_living';
		$giveaway = $this->_cache->get($cacheKey);
		if($forceRebuild){
			$giveaway = false;
		}
		
		if($giveaway===false) {
			$query = 'SELECT id, articleid, type, title, description,image, createDate,publishDate,endDate, src, slug, guid,status,featured FROM giveaways WHERE status='.(int)$status;
			$this->_db->setQuery($query);
			$giveaways = $this->_db->loadAssoc();
			$this->_cache->set($cacheKey,$giveaway);
		}
		
		$giveaway['link'] = '/giveaway/'.$giveaway['slug'].'.htm';
		return $giveaway;
	}
	
	public function getRecentGiveaways($currentId,$offset=0,$limit=1)
	{
		$query = 'SELECT id, articleid, type, title, description,image, createDate,publishDate,endDate, src, slug, guid,status,featured	FROM giveaways WHERE status !=-1 and id != '.(int)$currentId.' ORDER BY publishDate desc,createDate desc';
		$this->_db->setQuery($query, $offset, $limit, true);
		$giveaways = $this->_db->loadAssocList('id');
		if(!empty($giveaways))
		{
			foreach($giveaways as &$item)
			{
				$item['link'] = '/giveaway/'.$item['slug'].'.htm';
			}
		}
		return $giveaways;
	}
	
	public function getGiveawayBySlug($slug)
	{
		$cacheKey = 'slideshow_'.$slug;
		$giveaway = $this->_cache->get($cacheKey);
		if($giveaway===false) {
			$query = 'SELECT id, articleid,type, title, description,image, createDate,publishDate,endDate, src, slug, guid,status,featured	FROM giveaways WHERE slug="'.$slug.'"';
			$this->_db->setQuery($query);
			$giveaway = $this->_db->loadAssoc();
			$this->_cache->set($cacheKey,$giveaway);
		}
		return $giveaway;
	}
	
	public function updateStatus($itemId,$status)
	{
		// Edit DB
		$query = 'UPDATE `giveaways`
			SET `status` = '.$status.
			' WHERE `id` = '.(int) $itemId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		return true;
	}
	
	public function updateFeatured($itemId,$featured)
	{
		// Edit DB
		$query = 'UPDATE `giveaways`
			SET `featured` = '.$featured.
			' WHERE `id` = '.(int) $itemId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		return true;
	}
	
	public function getGiveawayId($slug)
	{
		$slugMapping = $this->_getGiveawaySlugMapping(true);
		return isset($slugMapping[$slug]) ? $slugMapping[$slug] : false;
	}
	
	protected function _getGiveawaySlugMapping($forceRebuild = false)
	{
		static $mapping;
		if ($forceRebuild || !$mapping) {
			$cacheKey = 'giveaways_slugMapping';
			$mapping = $forceRebuild ? false : $this->_cache->get($cacheKey);
			if(LEON_DEBUG)$mapping = false;
			if ($mapping === false) {
				$query = 'SELECT g.id, g.slug
					FROM `giveaways` AS `g`';
				$this->_db->setQuery($query);
				$mapping = $this->_db->loadResultAssocArray('slug', 'id');
				$this->_cache->set($cacheKey, $mapping, 60 * 60 * 24 / 2, null, array('compress' => true));
			}
		}
		return $mapping;
	}
	
	public function isSlugInUse($slug, $itemId = null)
	{
		// Check database
		$query = 'SELECT g.id
			FROM `giveaways` AS `g`
			WHERE g.slug = "'.$this->_db->escape($slug).'"';
		if ($itemId) {
			// It's OK if slug-holder is myself.
			$query .= '
				AND g.id != '.(int) $itemId;
		}
		$this->_db->setQuery($query);
		return (bool) $this->_db->loadResult();
	}
	
	public function addGiveaway($giveaway=null)
	{
		if(is_array($giveaway)&&!empty($giveaway))
		{
			$giveaway['createDate'] = 'NOW()';
			foreach ($giveaway as $field => &$value) {
				if($field != 'createDate'){				
					$value = '`'.$field.'` = "'.$value.'"';
				}else
				{
					$value = '`'.$field.'` = '.$value;
				}
			}
			unset($value);
			
			// Execute
			$query = 'INSERT INTO `giveaways`
				SET '.implode(', ', $giveaway);
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				return false;
			}
		}
		else
		{
			return false;
		}

		return true;		
	}
	
	public function updateGiveaway($giveawayId=null,$fields=null)
	{
		if(is_array($fields)&&!empty($fields) && $giveawayId !=null)
		{
			foreach ($fields as $field => &$value) {
				$value = '`'.$field.'` = "'.$value.'"';
			}
			unset($value);
			
			// Execute
			$query = 'UPDATE `giveaways`
				SET '.implode(', ', $fields).'
			WHERE `id` = '.(int) $giveawayId;
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				return false;
			}
		}
		else
		{
			return false;
		}

		return true;	
	}
}

?>
