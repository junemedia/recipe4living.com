<?php

/**
 *	Items model
 *
 *	@package BluApplication
 *	@subpackage SharedModels
 */
class ClientItemsModel extends BluModel
{
	/**
	 *	Get an item
	 *
	 *	@todo store related items into database(or even cache for flexibility?) after generating
	 *	@todo do we really need to getItemMetaGroups?
	 *	@access public
	 *	@param int Item ID
	 *	@param string Language code
	 *	@param bool Rebuild
	 *	@return array
	 */
	public function getItem($itemId, $langCode = null, $forceRebuild = false)
	{
		// Short circuit
		if (!$itemId) {
			return false;
		}
		// To rebuild cache for item(s), simply update below array with article/recipe IDs.
		//$array_rebuild = array('61879');
		//if(in_array($itemId, $array_rebuild))$forceRebuild = true;
		// Get base details
		$cacheKey = 'item_'.$itemId;
		$item = $forceRebuild ? false : $this->_cache->get($cacheKey);
                if(LEON_DEBUG){
                    // Do not use cache in leon's debug mode
			//$item = false;
		}
		if ($item === false) {
			// Get from database
			$query = 'SELECT a.*
				FROM `articles` AS `a`
				WHERE a.id = '.(int) $itemId;
			$this->_db->setQuery($query, 0, 1);
			if (!$item = $this->_db->loadAssoc()) {
				$this->_cache->set($cacheKey, $item);
				return false;
			}

			// format go live date
			if ($item['goLiveDate']) {
				$item['goLiveDate'] = date('m/d/Y', strtotime($item['goLiveDate']));
			}

			// Do something speshal to recipe ingredients
			if ($item['ingredients']) {

				// New new-line separated ingredients
				if ($item['ingredients'] == strip_tags($item['ingredients'])) {
					$item['ingredients'] = explode("\n", $item['ingredients']);

				// Legacy HTML ingredients
				} else {
					$item['ingredients'] = explode('<br />', substr($item['ingredients'], 3, -4));
				}
				$ingredientsModel = BluApplication::getModel('ingredients');

				$item['tidyIngredients'] = $ingredientsModel->getTidyRecipeIngredients($itemId);
			}

			// Get images
			$query = 'SELECT ai.*
				FROM `articleImages` AS `ai`
				WHERE ai.articleId = '.(int) $item['id'].'
				ORDER BY ai.sequence ASC';
			$this->_db->setQuery($query);
			$item['images'] = $this->_db->loadAssocList('filename');
			if (!empty($item['images'])) {
				foreach ($item['images'] as $image) {
					if (empty($image['minidescription'])) {
						$image['minidescription'] = Text::trim($image['description']);
					}
					if(empty($image['filename']))
					{
						$image['filename'] = 'easy-recipes.png';
					}
					if(empty($image['alt']))
					{
						$image['alt'] = $item['title'];
					}
					switch($image['type']) {
						case 'default':
									$item['image'] = $image;
									$item['default_alt'] = $image['alt'];
									break;
						case 'thumbnail':
									$item['thumbnail'] = $image;
									$item['thumbnail_alt'] = $image['alt'];
									break;
						case 'featured':
									$item['featuredImage'] = $image;
									$item['featured_alt'] = $image['alt'];
									break;
					}
				}
			}else{
				$item['image']['filename'] = 'easy-recipes.png';
				$item['thumbnail']['filename'] = 'easy-recipes.png';
				$item['featuredImage']['filename'] = 'easy-recipes.png';
				$item['thumbnail_alt'] =$item['featured_alt']=$item['default_alt']= $item['title'];
			}

			$item['links'] = $this->getLinks($item['id']);

			// Get related articles
			$query = 'SELECT ar.*
				FROM `articleRelationships` AS `ar`
				WHERE ar.articleId = '.(int) $item['id'].'
				ORDER BY ar.sequence';
			$this->_db->setQuery($query);
			$related = $this->_db->loadAssocList();
			$item['related'] = array();
			foreach ($related as $relationship) {

				// Use new article ID if available
				if ($relationship['relatedArticleId']) {
					$relatedId = (int) $relationship['relatedArticleId'];

				// Try to search for old article ID if not.
				} else {
					$query = 'SELECT a.id
						FROM `articles` AS `a`
						WHERE a.oldArticleId = '.(int) $relationship['relatedOldArticleId'];
					$this->_db->setQuery($query);
					$relatedId = (int) $this->_db->loadResult();
				}
				$item['related'][$relatedId] = $relatedId;
			}

             $item['related'] = $this->filterLiveItems($item['related']);
             $item['related'] = $this->filterImageItems($item['related']);
             

			// Pad related articles up to expected number.
			$expectedRelatedItems = 5;
			if (count($item['related']) < $expectedRelatedItems) {

				// Generate related items, based on this title
				$related = $this->_fullTextSearch($item['title']);
				$related = $this->filterLiveItems($related);
				$related = $this->filterTypeItems($related, $item['type']);
                                $related = $this->filterImageItems($related);
				$failSafeCount = 0;

				// Total up
                                $related = array_slice($related, 0,5, true);
                                $si = 0;
                                foreach($related as $kk=>$vv){
                                    $si++;
                                    $item['related'][$kk] = $kk;
                                    $insertSql = "INSERT INTO `articleRelationships` (`articleId`, `relatedArticleId`, `sequence`, `relatedOldArticleId`) VALUES "
                                            . "(" . $item['id'] . ", $kk, $si, '');";
                                    //echo $insertSql . "\n";
                                    mysql_query($insertSql);
                                }
				// Don't bother trying for more.
			}

            
			// Haz meta values? Don't actually get any of the values here, only do it when we need to.
			$query = 'SELECT amv.*
				FROM `articleMetaValues` AS `amv`
				WHERE amv.articleId = '.(int) $item['id'];
			$this->_db->setQuery($query);
			$item['meta'] = (bool) $this->_db->loadResult();

			// Get link - maybe with a little help from the meta model?
			$item['link'] = '/';
			switch ($item['type']) {
				case 'article':
					$item['link'] .= 'articles';
					break;

				case 'recipe':
					$item['link'] .= 'recipes';
					break;

				case 'question':
					$item['link'] .= 'questions';
					break;

				case 'quicktip':
					if(SITEEND=='frontend') {
						$item['link'] .= 'articles/encyclopedia_of_tips';
					}
					else {
						$item['link'] .= 'quicktips/'.$item['slug'].'.htm';
					}
					break;

				case 'blog':
					$item['link'] .= 'blogs';
					break;
			}
            

            
			if($item['type']!='quicktip') {
				$item['link'] .= '/'.$item['slug'].'.htm';
			}

            // setup the slidearticles links
            if($item['isslide'] == 1){
                $item['link'] =  '/slidearticles/details/'.$item['slug'].'/1';
            }
            
			// Store in cache
			$this->_cache->set($cacheKey, $item, 48400);
		}

		// Get comments
		//$item['comments'] = $this->getItemComments($itemId);

		// Get article rating stats
		$cacheKey = 'item_'.$itemId.'_ratings';
		$item['ratings'] = $forceRebuild ? false : $this->_cache->get($cacheKey);
		if ($item['ratings'] === false) {

			// Get from database
			$query = 'SELECT ar.*
				FROM `articleRatings` AS `ar`
				WHERE ar.articleId = '.(int) $item['id'];
			$this->_db->setQuery($query);
			$item['ratings']['raw'] = $this->_db->loadAssocList('userId');

			// Calculate average, or something
			$item['ratings']['count'] = count($item['ratings']['raw']);
			$item['ratings']['average'] = empty($item['ratings']['raw']) ? 0 : (array_sum(Arrays::column($item['ratings']['raw'], 'rating')) / $item['ratings']['count']);

			// Store in cache
			$this->_cache->set($cacheKey, $item['ratings']);
		}

		// Grab author
		$userModel = BluApplication::getModel('user');
		$item['author'] = $userModel->getUser($item['author']);

		// Pull out image for convenience, use author's if none available.
		if (empty($item['images'])) {
			$item['image'] = array(
				'title' => $item['title'],
				'filename' => $item['author']['image']
			);
		} elseif(empty($item['image'])) { // if there is no default image
			$item['image'] = reset($item['images']);
		}

		$cacheKey = 'item_'.$itemId.'_views';
		$item['views'] = $forceRebuild ? false : $this->_cache->get($cacheKey);
		if ($item['views'] === false) {
			// Get view stats
			$query = 'SELECT av.views
				FROM `articleViews` AS `av`
				WHERE av.articleId = '.(int) $item['id'];
			$this->_db->setQuery($query);
			$item['views'] = (int) $this->_db->loadResult();
			$this->_cache->set($cacheKey, $item['views'], 300);
		}

		// Fix teaser
		// todo remove once clean in db
		$item['teaser'] = strip_tags($item['teaser']);

		// quicktip section
		if($item['type']=='quicktip') {
			if($section = $this->getQuicktipSection($item['id'])) {
				$item['section'] = $section;
			}
		}

		// Return
		return $item;
	}

	/**
	 *	Get an item's ID from its slug.
	 *
	 *	@access public
	 *	@param string Slug
	 *	@return int
	 */
	public function getItemId($slug)
	{
		$slugMapping = $this->_getItemSlugMapping();
		return isset($slugMapping[$slug]) ? $slugMapping[$slug] : false;
	}

	/**
	 *	Get an item's comments
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param bool Rebuild cache
	 *	@return array Comments
	 */
	public function getItemComments($itemId, $forceRebuild = false)
	{
		$cacheKey = 'item_'.$itemId.'_comments';
		$comments = $forceRebuild ? false : $this->_cache->get($cacheKey);
		if ($comments === false) {

			// Get from database
			$query = 'SELECT c.id, c.type
				FROM `comments` AS `c`
				WHERE c.objectType = "article"
					AND c.objectId = '.(int) $itemId.'
					AND c.live = 1
				ORDER BY c.date DESC';
			$this->_db->setQuery($query);
			$comments = $this->_db->loadGroupedAssocList('type', 'id', 'id');

			// Build data
			if (!empty($comments)) {
				foreach ($comments as $type => &$typeComments) {
					foreach ($typeComments as $commentId => &$comment) {
						$comment = $this->_getComment($commentId);
					}
					unset($comment);
				}
				unset($typeComments);
			}

			// Store in cache
			$this->_cache->set($cacheKey, $comments);
		}

		// Return
		return $comments;
	}

	/**
	 *	Get comments submitted by a user
	 *  (this function should be parto of some other model)
	 *
	 *	@access public
	 *	@param int User ID
	 *	@return array Comments
	 */
	public function getUserComments($userId)
	{
		$cacheKey = 'user_'.$userId.'_comments';
		$comments = $this->_cache->get($cacheKey);
		//if ($comments === false) {

			// Get from database
			$query = 'SELECT c.id, c.type
				FROM `comments` AS `c`
				WHERE c.objectType = "article"
					AND c.userId = '.(int) $userId.'
					AND c.live = 1
				ORDER BY c.date DESC';
			$this->_db->setQuery($query);
			$comments = $this->_db->loadGroupedAssocList('type', 'id', 'id');

			// Build data
			if (!empty($comments)) {
				foreach ($comments as $type => &$typeComments) {
					foreach ($typeComments as $commentId => &$comment) {
						$comment = $this->_getComment($commentId);
						$comment['item'] = $this->getItem($comment['objectId']);
					}
					unset($comment);
				}
				unset($typeComments);
			}

			// Store in cache
			$this->_cache->set($cacheKey, $comments);
		//}

		// Return
		return $comments;
	}

	/**
	 *	Get item ID to slug mapping
	 *
	 *	@access protected
	 *	@param bool Rebuild
	 *	@return array
	 */
	protected function _getItemSlugMapping($forceRebuild = false)
	{
		static $mapping;
		if ($forceRebuild || !$mapping) {
			$cacheKey = 'items_slugMapping';
			$mapping = $forceRebuild ? false : $this->_cache->get($cacheKey);
			if(LEON_DEBUG)$mapping = false;
			if ($mapping === false) {
				$query = 'SELECT a.id, a.slug
					FROM `articles` AS `a`';
				$this->_db->setQuery($query);
				$mapping = $this->_db->loadResultAssocArray('slug', 'id');
				$this->_cache->set($cacheKey, $mapping, 60 * 60 * 24 / 2, null, array('compress' => true));
			}
		}
		return $mapping;
	}

	/**
	 *	Get a comment's details
	 *
	 *	@access protected
	 *	@param int Comment ID
	 *	@return array
	 */
	protected function _getComment($commentId)
	{
		// Get base comment
		$query = 'SELECT c.*
			FROM `comments` AS `c`
			WHERE c.id = '.(int) $commentId;
		$this->_db->setQuery($query, 0, 1);
		$comment = $this->_db->loadAssoc();

		// Get comment rating stats
		if ($comment['type'] == 'review') {
			$query = 'SELECT cr.userId, cr.rating, cr.date, u.username
				FROM `commentRatings` AS `cr`, `users` AS `u`
				WHERE cr.commentId = '.(int) $commentId .' AND cr.userId = u.id';
			$this->_db->setQuery($query);
			$comment['ratings']['raw'] = $this->_db->loadAssocList('userId');

			$comment['ratings']['count'] = count($comment['ratings']['raw']);
			$comment['ratings']['average'] = empty($comment['ratings']['raw']) ? 0 : ceil(array_sum(Arrays::column($comment['ratings']['raw'], 'rating')) / $comment['ratings']['count']);
		}

		// Get reports
		$query = 'SELECT r.id, r.reporter, r.time, r.reason, r.status, u.username
			FROM `reports` AS `r`, `users` AS `u`
			WHERE r.objectType = "comment"
				AND r.objectId = '.(int) $comment['id'].'
				AND r.reporter = u.id
			ORDER BY r.status ASC, r.time DESC';
		$this->_db->setQuery($query);
		$comment['reports']['raw'] = $this->_db->loadGroupedAssocList('status', 'id');
		$comment['reports']['active'] = !(empty($comment['reports']['raw']['pending']) && empty($comment['reports']['raw']['viewed']));

		// Return
		return $comment;
	}

	/**
	 *	Get item ID to name mapping
	 *
	 *	@access public
	 *	@param bool Rebuild
	 *	@return array
	 */
	public function getItemNameMapping($forceRebuild = false)
	{
		static $mapping;
		if (!$mapping) {
			$cacheKey = 'items_nameMapping';
			$mapping = $forceRebuild ? false : $this->_cache->get($cacheKey);
			if ($mapping === false) {
				$query = 'SELECT a.id, a.title
					FROM `articles` AS `a`';
				$this->_db->setQuery($query);
				$mapping = $this->_db->loadResultAssocArray('id', 'title');
				$this->_cache->set($cacheKey, $mapping, 60 * 60 * 24 / 2, null, array('compress' => true));
			}
		}
		return $mapping;
	}

	/**
	 *	Inject a task into an item link.
	 *
	 *	@access public
	 *	@param string Item/itemgroup link
	 *	@param string Task
	 *	@param string Argument
	 *	@param string Argument 2
	 *	@return string New link
	 */
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

	/**
	 *	Get full item details and add to item IDs
	 *
	 *	@access public
	 *	@param array
	 */
	public function addDetails(&$items,$forceRebuild = false)
	{
		if (!empty($items)) {
			$items = array_flip($items);
			foreach ($items as $itemId => &$item) {
				$item = $this->getItem($itemId);
				if($forceRebuild)
				{
					$item['lastUsed'] = $this->getLastUsedDate($itemId);
				}
			}
			unset($item);
		}
	}
	
	public function getLastUsedDate($contentId)
	{
		$query = "SELECT a.lastUsed FROM articles as a WHERE a.id=".(int)$contentId;
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		return $result;
	}
	
	/**
	 *	Filter out blocked items
	 *
	 *	@access public
	 *	@param array Item IDs
	 *	@return array
	 */
	public function filterBlockedItems($items)
	{
		return $items;
	}
    
    /**
    * Filter image items
    * 
    * @param bool $items
    * @param string $type
    * @param bool $invert
    * @return bool
    */
    public function filterImageItems($items, $invert = false)
    {
        // Get all live items
        static $ImageItems;
        if (!isset($ImageItems)) {
            $cacheKey = 'items_image';
            $ImageItems = $this->_cache->get($cacheKey);
            // force to rebuild every time;
            if(LEON_DEBUG){
                $ImageItems = false;
            }
            if ($ImageItems === false) {
                $query = 'SELECT articleId FROM `articleImages` where filename!="" GROUP BY articleId';
                $this->_db->setQuery($query);
                $ImageItems = $this->_db->loadResultAssocArray('articleId', 'articleId');
                $this->_cache->set($cacheKey, $ImageItems, 60 * 60 * 24 / 2, null, array('compress' => true));
            }
        }
      //print_r($ImageItems);exit;
        // Return all?
        if (empty($ImageItems)) {
            return /*$invert ? array() :*/ $items;
        }

        // Intersect (or diff) keys
        $items = /*$invert ? array_diff_key($items, $liveItems) :*/ array_intersect_key($items, $ImageItems);

        // Return
        return $items;
    }   
    

	/**
	 *	Filter items by item type
	 *
	 *	@access public
	 *	@param array Items (keyed by Item ID)
	 *	@param string Item type
	 *	@param bool Return items NOT of given item type
	 *	@return array Filtered Items
	 */
	public function filterTypeItems($items, $type, $invert = false)
	{
		// Get *all* featured items
		$typeItems = $this->_getTypeItems($type, null, null, false);

		// Return all items?
		if (empty($typeItems)) {
			return $invert ? $items : array();
		}

		// Intersect (or diff) keys
		$items = $invert ? array_diff_key($items, $typeItems) : array_intersect_key($items, $typeItems);

		// Return
		return $items;
	}

	/**
	 *	Get items of a particular type
	 *
	 *	@access public
	 *	@param string Item type
	 *	@param int Offset
	 *	@param int Limit
	 *	@param bool Add details
	 *	@param string Sort
	 *	@return array
	 */
	protected function _getTypeItems($type, $offset = null, $limit = null, $addDetails = true, $sort = null, $liveOnly = false)
	{
		// Load item type indices
		static $indices = array();
		if (empty($indices)) {
			$cacheKey = ($liveOnly)?'items_types_live':'items_types';
			$indices = $this->_cache->get($cacheKey);
			//$indices = false;
			if ($indices === false) {
				$query = 'SELECT a.id, a.type
					FROM `articles` AS `a` ';
				if($liveOnly){
					$query .= 'WHERE a.live = 1';
				}
				$this->_db->setQuery($query);
				$indices = $this->_db->loadGroupedAssocList('type', 'id', 'id');

				$this->_cache->set($cacheKey, $indices, 60 * 60 * 24 / 2, null, array('compress' => true));
			}
		}

		// Fetch the required type
		if (empty($indices[$type])) {
			return false;
		}
		$items = $indices[$type];

		// Sort
		if ($sort) {
			$items = $this->sortItems($items, $sort);
		}

		// Slice accordingly
		$items = ($offset || $limit) ? array_slice($items, $offset, $limit, true) : $items;

		// Add details
		if ($addDetails) {
			$this->addDetails($items);
		}

		// Return
		return $items;
	}

	/**
	 *	Filter items by live flag
	 *
	 *	@todo could consider using array_chunk and remerging?
	 *	@access public
	 *	@param array Items (Item IDs as key)
	 *	@param bool Return non-live items
	 *	@return array Filtered Items
	 */
	public function filterLiveItems($items/*, $invert = false*/)
	{
		// Get all live items
		static $liveItems;
		if (!isset($liveItems)) {
			$cacheKey = 'items_live';
			$liveItems = $this->_cache->get($cacheKey);
			// force to rebuild every time;
			if(LEON_DEBUG){
				$liveItems = false;
			}
			if ($liveItems === false) {
				$query = 'SELECT a.id
					FROM `articles` AS `a`
					WHERE a.live = 1 AND (goLiveDate IS NULL OR goLiveDate<=NOW())';
				$this->_db->setQuery($query);
				$liveItems = $this->_db->loadResultAssocArray('id', 'id');
				$this->_cache->set($cacheKey, $liveItems, 60 * 60 * 24 / 2, null, array('compress' => true));
			}
		}

		// Return all?
		if (empty($liveItems)) {
			return /*$invert ? array() :*/ $items;
		}

		// Intersect (or diff) keys
		$items = /*$invert ? array_diff_key($items, $liveItems) :*/ array_intersect_key($items, $liveItems);

		// Return
		return $items;
	}

	/**
	 *	Filter items by live flag (2=deleted)
	 *
	 *	@todo could consider using array_chunk and remerging?
	 *	@access public
	 *	@param array Items (Item IDs as key)
	 *	@return array Filtered Items
	 */
	public function filterDeletedItems($items, $invert = false)
	{
		// Get all deleted items
		static $deletedItems;
		if (!isset($deletedItems)) {
			$cacheKey = 'items_deleted';
			$deletedItems = $this->_cache->get($cacheKey);
			if ($deletedItems === false) {
				$query = 'SELECT a.id
					FROM `articles` AS `a`
					WHERE a.live = 2';
				$this->_db->setQuery($query);
				$deletedItems = $this->_db->loadResultAssocArray('id', 'id');
				$this->_cache->set($cacheKey, $deletedItems, 60 * 60 * 24 / 2, null, array('compress' => true));
			}
		}

		// Return all?
		if (empty($deletedItems)) {
			return $invert ? $items : array();
		}

		// Intersect (or diff) keys
		$items = $invert ? array_diff_key($items, $deletedItems) : array_intersect_key($items, $deletedItems);

		// Return
		return $items;
	}

	/**
	 *	Get items that fulfill the criteria, sorts by relevance by default.
	 *
	 *	@access public
	 *	@param int Offset
	 *	@param int Limit
	 *	@param string Sort
	 *	@param array Meta filters
	 *	@param string Search term
	 *	@param bool Force Rebuild
	 *	@return array Item IDs
	 */
	public function getItems($offset = null, $limit = null, $sort = null, array $filters = array(), $search = null, $forceRebuild = false, $backend = false)
	{
		// Fewer cache entries
		if ($search) {
			$search = strtolower(trim($search));
		}

		// Get items from cache, filtered but unsorted
		$cacheKey = 'items_'.serialize(array(
			'filters' => $filters,
			'search' => $search
		));
		// AS $cacheKey1 can expire as $cacheKey hasn't yet
		// Get language
		$language = BluApplication::getLanguage();
		$langCode = $language->getLanguageCode();
		//$cacheKey1 = 'items_quicksearch_'.$langCode;
		//$quickSearchIndices1 = $this->_cache->get($cacheKey1);
		//
		$items = $forceRebuild ? false : $this->_cache->get($cacheKey);
		// added by leon
		// rebuild all the time, do not use cache here
		if(LEON_DEBUG){
			$items = false;
		}
		if ($items === false) {
			if(strlen($search) != 0){
				$searchFlag = true;
			}else{
				$searchFlag = false;
			}
			// Do search
			if ($searchFlag) {
				//$items = $this->_quickSearch($search);
				if($backend)
				{
					$items = $this->_backendSearch($search);
				}
				else
				{
					$items = $this->_fullTextSearch($search);
				}

			// Get all items
			} else {
						$excludeCategory = array();
						if(count($filters) == 0 && SITEEND != 'backend'){
							$excludeCategory = array(128240,128242);//128240:Product Reviews,128242:Recipe Collections
						}
                		$items = $this->getAllIds($excludeCategory);
                		/**
                		* @desc  This will cause problems when there are millions of request
                		* Just replace it with Leon's method - to cache the ids
                		*/
                		/*
					$query = 'SELECT a.id
						FROM `articles` AS `a`';
					$this->_db->setQuery($query);
					$items = $this->_db->loadResultAssocArray('id', 'id'); 
                		*/
			}

			// Filter
			//There is no blocked items. just get ride of it!
			//$items = $this->filterBlockedItems($items);

			if (count($filters) > 0) {
				$metaModel = BluApplication::getModel('meta');
				/* added by leon
				 * disable the old way of filter
				 */
				//$items = $metaModel->filterItems($items, $filters,true);
				$items = $metaModel->getCorrectItems($items,$filters, $searchFlag);
				//echo count($items);
			}
			// Store in cache, deplete every 6 hours.
			$this->_cache->set($cacheKey, $items, 60 * 60 * 12, null, array('compress' => true));
		}

		// Sort
		if ($sort) {
			$items = $this->sortItems($items, $sort);
		}

		// Get total and slice as required
		if ($limit !== null) {
			$items = array_slice($items, $offset, $limit, true);
		}
		// Return
		return $items;
	}

	private function _backendSearch($search)
	{
		$items = array();
			
		// Do search
		//$articleMatch = 'MATCH(a.title, a.body, a.teaser, a.keywords, a.slug) AGAINST ("'.$this->_db->escape($search).'")';
		$query = 'SELECT a.id FROM `articles` AS `a` LEFT JOIN articleImages as ai ON ai.articleId=a.id WHERE ai.filename !="" and a.title like "%'.$this->_db->escape($search).'%" order by a.lastUsed DESC,a.id DESC';
		$this->_db->setQuery($query);
		if ($someItems = $this->_db->loadResultAssocArray('id', 'id')) {
			$items = $someItems;
		}
		return $items;
	}
	
	/**
	 *	Perform quicksearch
	 *
	 *	@access private
	 *	@param string Search term
	 *	@param string Language code
	 *	@return array Item IDs
	 */
	private function _quickSearch($search, $langCode = null)
	{
		$index = $this->_getItemsQuicksearchIndex($langCode);
		return Utility::quickSearch($search, $index);
	}

	/**
	 *	Get the full quicksearch index
	 *
	 *	@access private
	 *	@param string Language code
	 *	@return array Indices
	 */
	private function _getItemsQuicksearchIndex($langCode = null)
	{
		// Get language
		if (!$langCode) {
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
		}

		// Get greppable index
		$cacheKey = 'items_quicksearch_'.$langCode;
		$quickSearchIndices = $this->_cache->get($cacheKey);
		if ($quickSearchIndices === false) {
			//ini_set('max_execution_time', 180);
			// Get from database
			$query = 'SELECT a.id, a.title, a.keywords, u.username, a.description
				FROM `articles` AS `a`
					LEFT JOIN `users` AS `u` ON a.author = u.id';
			$this->_db->setQuery($query);
			$quickSearchIndices = $this->_db->loadAssocList('id');
			$metaModel = BluApplication::getModel('meta');
			foreach ($quickSearchIndices as &$item) {

				// Compact keywords
				if ($item['keywords']) {
					$item['keywords'] = Text::filterCommonWords($item['keywords'], $this->_getCommonWords());
					$item['keywords'] = implode(' ', array_unique($item['keywords']));
				}

				// Compact description
				if ($item['description']) {
					$item['description'] = Text::filterCommonWords($item['description'], $this->_getCommonWords());
					$item['description'] = implode(' ', array_unique($item['description']));
				}

				// Build filterable meta values
				$metaGroups = $metaModel->getItemMetaGroups($item['id'], false, $langCode);
				$metaGroups = $metaModel->filterFilterableGroups($metaGroups);
				$item['metaValues'] = array();
				if (!empty($metaGroups)) {
					foreach ($metaGroups as $metaGroup) {
						$item['metaValues'] = array_merge($item['metaValues'], array_values($metaGroup['values']));
					}
					$item['metaValues'] = Arrays::column($item['metaValues'], 'name');
				}
				$item['metaValues'] = implode(' ', $item['metaValues']);
				unset($item['id']);
			}
			unset($item);

			// Set in cache, deplete daily
			$expiry = strtotime('3AM +1 day EST') - time();
 			$this->_cache->set($cacheKey, $quickSearchIndices, 60 * 60 * 48, null, array('compress' => true));
		}
		// Return
		return $quickSearchIndices;
	}

	/**
	 *	Get common words to filter by
	 *
	 *	@access protected
	 *	@return array Words
	 */
	protected function _getCommonWords()
	{
		return array('article', 'articles', 'recipe', 'recipes');
	}

    /**
     *    Solr Search
     *
     *    @access protected
     *    @return array Words
     */
	protected function _fullTextSolrSearch($search)
	{
		$items = array();
		require_once(BLUPATH_BASE . '/leon/solr/solr.config.search.php');
		global $solrSearch;
		//$fields = array("keywords","title","teaser","description","body");
		$fields = array("title", "keywords", "description", "teaser", "username");
		$searchRaw = "";
		//set the escapes in the Solr Raw urls
		//blubolt use fulltextsearch also for related search, which will cause a very long text input search
		$escapes = array(':', ',',  '+', '-', '&&', '||', '!', '(', ')', '{', '}', '[', ']', '^', '"', '~', '*', '?', '\'');
		//set the max length to 30, in case for hacking search to run over the search buffer
		$search = substr($search, 0, 30);
		//echo $search;


		$search = str_replace($escapes, ' ', $search);
		$search = explode(' ', $search);
		foreach($search as $k=>$v){
			if($v == '')unset($search[$k]);
		}

        $stitle = "";
        $sall = "";
        if(count($search) == 1) {
            $search =  $search[0] . '^4 ' . $search[0] . '*  ' . $search[0] . '~0.75' ;
            $stitle = $sall = $search;
        }else{

		    foreach($search as $k){
                $stitle .= " +$k~";
                $sall .= "$k~ $k ";
            }
		    //echo $search;
		    //$search = str_replace(' ', ' +', $search);
		    $search = $stitle;
        }
		//$search = '*' . $search . '*';
		//$search = str_replace($escapes, ',', $search);
		
		//let's search in the title first and then search all the other fields:
		$searchTitle = "title:(" . $stitle . ")";
		$resultsTitle = $solrSearch->search($searchTitle, 0, 10000);
        //echo "search title : " . $searchTitle . "<br>";
		$itemsTitle = array();
		if($resultsTitle){
			foreach ($resultsTitle->response->docs as $doc)
			{
				$s = $doc->getField('id');
				$itemsTitle[] = $s["value"];
			}			
		}		
		
		foreach($fields as $field)
		{
			$searchRaw .= $field . ":(" . $sall . ")";
			//if($field == 'title') $searchRaw .= "^2";
			$searchRaw .= " OR ";
			//$searchRaw .= $field . ":(\"" . $search . "\") Or ";
		}
		//get ride of the last 'or '
		$searchRaw = substr($searchRaw, 0, -3);

		//echo $searchRaw;
		
		//echo $searchRaw . "<br>";
		$results = false;
		$results = $solrSearch->search($searchRaw, 0, 10000);
		$itemsAll = array();
		if(!$results){
			return $itemsAll = array();
		}
		foreach ($results->response->docs as $doc)
		{
			//print_r($doc->getFieldValues());
			//echo $this->valueEscape($doc->getField('id'));
			//echo $this->valueEscape($doc->getField('Long_Desc'));
			$s = $doc->getField('id');
			//echo $s["value"] . "|";
			$itemsAll[] = $s["value"];
		}
		//echo "<pre>";
		//print_r($itemsTitle);
		
		//print_r($itemsAll);
		

		
		if(count($itemsAll) > 0)$itemsAll = array_diff($itemsAll, $itemsTitle);
		//print_r($itemsAll);
		//echo count($itemsAll);
		if(count($itemsAll) > 0)
		{
			foreach($itemsAll as $k=>$v)
			{
				array_push($itemsTitle, $v);
			}
		}
		if(count($itemsTitle) > 0)
		{
			foreach($itemsTitle as $k=>$v)
			{
				$items[$v] = $v;
			}
		}
		
        $items = $this->_searchRefine($items);
        
		//print_r($items);echo count($items);
		//$items = $itemsTitle + $itemsAll;
		//var_dump($items);
		//echo "<pre>";
		//exit;		
		return $items;
	}

	/**
	 *	Do full-text database search
	 *
	 *	@access private
	 *	@param string Search term
	 *	@param string Language code
	 *	@return array Item IDs
	 */
	private function _fullTextSearch($search, $langCode = null)
	{
		//echo $search . "<br>";
		// Get language
		if (!$langCode) {
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
		}

		$items = array();
		
                
                // Let's cache the search results from SOLR
                $cacheKey = 'solr_' . md5(trim($search));
                $items = $this->_cache->get($cacheKey);
                
                // Disable the search cache for interns.
                $local = array(
                    '60.216.3.163',             // E5 Jinan Office
                    '66.54.186.254'
                    );
                if(isset($_SERVER['REMOTE_ADDR']) && array_search($_SERVER['REMOTE_ADDR'], $local)!== false){
                    // Locally, we will disalbe search cache here
                    $items = false;
                }
                
                if ($items) {
                    //echo "find " . count($items) . " in cache<br>";
                    return $items;
                }
                
                // We didn't find it, let's build it from SOLR
                
		/**
		 * @author by leon
		 * @solr search
		 */
		 require_once(BLUPATH_BASE . '/leon/solr/solr.config.search.php');
		 global $solrSearch, $solrSearchFlag;
                 //$solrSearchFlag = false;
		 if(isset($solrSearchFlag) && $solrSearchFlag)
		 {
			$items = $this->_fullTextSolrSearch($search);
		}else{
			/* ------------------------- original search block begins --------------------------------*/
			$items = array();
			
			// Do search
			$articleMatch = 'MATCH(a.title, a.body, a.teaser, a.keywords, a.slug) AGAINST ("'.$this->_db->escape($search).'")';
			$query = 'SELECT a.id, '.$articleMatch.' AS `relevance`
				FROM `articles` AS `a`
				WHERE '.$articleMatch;
			$this->_db->setQuery($query,0,50);
			if ($someItems = $this->_db->loadResultAssocArray('id', 'relevance')) {
				$items += $someItems;
			}

			$authorMatch = 'MATCH(u.username, u.firstname, u.lastname, u.displayname) AGAINST ("'.$this->_db->escape($search).'")';
			$query = 'SELECT a.id, (0.3 * '.$authorMatch.') AS `relevance`
				FROM `articles` AS `a`
					LEFT JOIN `users` AS `u` ON a.author = u.id
				WHERE '.$authorMatch;
			$this->_db->setQuery($query,0,50);
			if ($someItems = $this->_db->loadResultAssocArray('id', 'relevance')) {
				$items += $someItems;
			}

			$imageMatch = 'MATCH(ai.filename, ai.title, ai.description, ai.minidescription) AGAINST ("'.$this->_db->escape($search).'")';
			$query = 'SELECT ai.articleId, (0.2 * '.$imageMatch.') AS `relevance`
				FROM `articleImages` AS `ai`
				WHERE '.$imageMatch;
			$this->_db->setQuery($query,0,50);
			if ($someItems = $this->_db->loadResultAssocArray('articleId', 'relevance')) {
				$items += $someItems;
			}

			$metaMatch = 'MATCH(lmv.name, lmv.description, lmv.keywords) AGAINST ("'.$this->_db->escape($search).'")';
			$query = 'SELECT amv.articleId, (0.3 * '.$metaMatch.') AS `relevance`
				FROM `articleMetaValues` AS `amv`
					LEFT JOIN `languageMetaValues` AS `lmv` ON amv.valueId = lmv.id
				WHERE '.$metaMatch.'
					AND lmv.lang = "'.$this->_db->escape($langCode).'"';
			$this->_db->setQuery($query,0,50);
			if ($someItems = $this->_db->loadResultAssocArray('articleId', 'relevance')) {
				$items += $someItems;
			}
			
			/* ---------------------------- Original search block ends   ---------------------------------*/
			// Sort by relevance
			//arsort($items);

	/*	COULD TRY AND DO IT ALL IN ONE QUERY.... HMM AMBITIOUS.
			###
			$query = 'SELECT a.id, '.$articleMatch.' + (0.3 * '.$authorMatch.') + (0.2 * '.$imageMatch.') + (0.3 * '.$metaMatch.') AS `relevance`
				FROM `articles` AS `a`
					LEFT JOIN `users` AS `u` ON a.author = u.id
					LEFT JOIN `articleImages` AS `ai` ON a.id = ai.articleId
					LEFT JOIN `articleMetaValues` AS `amv` ON a.id = amv.articleId
					LEFT JOIN `languageMetaValues` AS `lmv` ON amv.valueId = lmv.id
				WHERE '.$articleMatch.'
					OR '.$authorMatch.'
					OR '.$imageMatch.'
					OR '.$metaMatch.'
				GROUP BY a.id
				ORDER BY `relevance` DESC';
			$this->_db->setQuery($query);
			$items = (array) $this->_db->loadResultAssocArray('id', 'relevance');
			###
	*/
			// Item IDs only.
			//if (!empty($items)) {
			//	$items = array_combine(array_keys($items), array_keys($items));
			//}

			// Return
			
		}
                //echo "build " . count($items) . "<br>";
                $this->_cache->set($cacheKey, $items, 60 * 60 * 24 * 7, null, array('compress' => true));
		return $items;
	}

	/**
	 *	Sort a list of items
	 *
	 *	@access public
	 *	@param array Item IDs
	 *	@param string Sort
	 *	@return array Item IDs
	 */
	public function sortItems($items, $sort = 'name_asc')
	{
		// Empty
		if (empty($items)) {
			return $items;
		}

		// Generate sort index
		switch ($sort) {
			case 'name_asc':
				$index = $this->_getSortIndex('name', Utility::SORT_ASC);
				break;

			case 'name_desc':
				$index = $this->_getSortIndex('name', Utility::SORT_DESC);
				break;

			case 'rating':
				$index = $this->_getSortIndex('rating', Utility::SORT_DESC);
				break;

			case 'date_desc':
				$index = $this->_getSortIndex('date', Utility::SORT_DESC);
				break;

			case 'date_asc':
				$index = $this->_getSortIndex('date', Utility::SORT_ASC);
				break;

			case 'views_asc':
				$index = $this->_getSortIndex('views', Utility::SORT_ASC);
				break;

			case 'views_desc':
				$index = $this->_getSortIndex('views', Utility::SORT_DESC);
				break;

			case 'reviews_asc':
				$index = $this->_getSortIndex('reviews', Utility::SORT_ASC);
				break;

			case 'reviews_desc':
				$index = $this->_getSortIndex('reviews', Utility::SORT_DESC);
				break;

			case 'featured':
				$index = $this->_getSortIndex('featured', Utility::SORT_DESC);
				break;

			case 'votes_desc':
				// Deprecated
				//$index = $this->_getSortIndex('votes', Utility::SORT_DESC);
				$index = null;
				break;

			case 'relevance':
			default:
				$index = null;
				break;
		}

		// Pair up with those we're interested in
		if ($index) {
			$items = array_flip($items);
			$items = array_intersect_key($index, $items);

			// Use IDs only
			$items = array_keys($items);
			$items = array_combine($items, $items);
		}

		// Return
		return $items;
	}

	/**
	 *	Load sorting index
	 *
	 *	@access protected
	 *	@param string Criteria
	 *	@param int Direction
	 *	@return array Item ID => Relative statistic (only used for sorting)
	 */
	protected function _getSortIndex($criteria, $direction)
	{
		// Get index, sorted in ascending order
		// added by leon
		// to switch enable/disable the cache
		$cacheSwitch = true;

		if(LEON_DEBUG){
			$cacheSwitch = false;
		}

		switch ($criteria) {
			case 'name':
				$cacheKey = 'items_sortable_name';
				$index = $cacheSwitch?$this->_cache->get($cacheKey):false;
				if ($index === false) {
					$query = 'SELECT a.id, a.title
						FROM `articles` AS `a`
						ORDER BY a.title ASC';
					$this->_db->setQuery($query);
					$index = $this->_db->loadResultAssocArray('id', 'title');

					$this->_cache->set($cacheKey, $index, 60 * 60 * 24 / 2, null, array('compress' => true));
				}
				$sorted = Utility::SORT_ASC;
				break;

			case 'rating':
				$cacheKey = 'items_sortable_rating';
				$index = $cacheSwitch?$this->_cache->get($cacheKey):false;
				if ($index === false) {

					// Order by number of stars (the average, rounded to nearest integer), then order by number of people that voted.
					// Don't order by strict average, because people only ever see the rounded one anyway, and so confuses the hell out of them.
					$query = 'SELECT a.id, ROUND(AVG(ar.rating)) AS `roundedAverage`
						FROM `articles` AS `a`
							LEFT JOIN `articleRatings` AS `ar` ON a.id = ar.articleId
						GROUP BY a.id
						ORDER BY `roundedAverage` DESC,
							SUM(ar.rating) DESC';
					$this->_db->setQuery($query);
					$index = $this->_db->loadResultAssocArray('id', 'roundedAverage');

					// Ints are smaller than strings - plus, we don't really need the .000000000000's, do we?
					foreach ($index as &$rating) {
						$rating = (int) $rating;
					}
					unset($rating);

					$this->_cache->set($cacheKey, $index, 60 * 60 * 24 / 2, null, array('compress' => true));
				}
				$sorted = Utility::SORT_DESC;
				break;

			case 'date':
				$cacheKey = 'items_sortable_date';
				$index = $cacheSwitch?$this->_cache->get($cacheKey):false;
				if (SITEEND == "backend")  $index = false; 
				if ($index === false) {
					$query = 'SELECT a.id, UNIX_TIMESTAMP(a.date) AS `date`
						FROM `articles` AS `a`
						ORDER BY `date` DESC';
					$this->_db->setQuery($query);
					$index = $this->_db->loadResultAssocArray('id', 'date');

					// We need to save as much space as possible... SHAVE SHAVE SHAVE
					$query = 'SELECT UNIX_TIMESTAMP(a.date) AS `oldest`, AVG(UNIX_TIMESTAMP(a.date)) AS `average`
						FROM `articles` AS `a`
						WHERE a.date != 0
						ORDER BY a.date ASC';
					$this->_db->setQuery($query, 0, 1);
					$specialDates = $this->_db->loadAssoc();
					$specialDates['average'] = round($specialDates['average']);	// Doesn't need to be accurate at all, just a benchmark (rounded so that we store with fewer decimal places)

					foreach ($index as &$date) {
						$date = max($specialDates['oldest'], $date) - $specialDates['average'];						// *Relatively normalised* unix timestamps are smaller than datetimes
					}
					unset($date);

					$this->_cache->set($cacheKey, $index, 60 * 60 * 24 / 2, null, array('compress' => true));
				}
				$sorted = Utility::SORT_DESC;
				break;

			case 'views':
				// Shouldn't be cached really, but oh god, look at those hits! 5 minutes it is then.
				$cacheKey = 'items_sortable_views';
				$index = $cacheSwitch?$this->_cache->get($cacheKey):false;
				if ($index === false) {
					$query = 'SELECT av.articleId, av.views
						FROM `articleViews` AS `av`
						ORDER BY av.views DESC';
					$this->_db->setQuery($query);
					$index = $this->_db->loadResultAssocArray('articleId', 'views');

					// Ints are smaller than strings
					foreach ($index as &$views) {
						$views = (int) $views;
					}
					unset($views);

					$this->_cache->set($cacheKey, $index, 60 * 5, null, array('compress' => true));
				}
				$sorted = Utility::SORT_DESC;
				break;

			case 'reviews':
				// Shouldn't be cached really, but oh god, look at those hits! 5 minutes it is then.
				$cacheKey = 'items_sortable_reviews';
				$index = $cacheSwitch?$this->_cache->get($cacheKey):false;
				if ($index === false) {
					$query = 'SELECT a.id, COUNT(c.id) AS `reviews`
						FROM `articles` AS `a`
							LEFT JOIN `comments` AS `c` ON a.id = c.objectId
								AND c.type = "review"
								AND c.objectType = "article"
								AND c.live = 1
						GROUP BY a.id
						ORDER BY `reviews` DESC';
					$this->_db->setQuery($query);
					$index = $this->_db->loadResultAssocArray('id', 'reviews');

					// Ints are smaller than strings
					foreach ($index as &$reviews) {
						$reviews = (int) $reviews;
					}
					unset($reviews);

					$this->_cache->set($cacheKey, $index, 60 * 5, null, array('compress' => true));
				}
				$sorted = Utility::SORT_DESC;
				break;

			case 'featured':
				$cacheKey = 'items_sortable_featured';
				$index = $cacheSwitch?$this->_cache->get($cacheKey):false;
				if ($index === false) {
					$query = 'SELECT a.id, a.featured
						FROM `articles` AS `a`
						ORDER BY a.featured DESC';
					$this->_db->setQuery($query);
					$index = $this->_db->loadResultAssocArray('id', 'featured');

					// Ints are smaller than strings
					foreach ($index as &$featured) {
						$featured = (int) $featured;
					}
					unset($featured);

					$this->_cache->set($cacheKey, $index, 60 * 60 * 24 / 2, null, array('compress' => true));
				}
				$sorted = Utility::SORT_DESC;
				break;

			case 'votes':
				// Shouldn't be cached really, but oh god, look at those hits! 5 minutes it is then.
				$cacheKey = 'items_sortable_votes';
				$index = $cacheSwitch?$this->_cache->get($cacheKey):false;
				if ($index === false) {
					$query = 'SELECT avo.articleId, avo.votes
						FROM `articleVotes` AS `avo`
						ORDER BY avo.votes DESC';
					$this->_db->setQuery($query);
					$index = $this->_db->loadResultAssocArray('articleId', 'votes');

					// Ints are smaller than strings
					foreach ($index as &$votes) {
						$votes = (int) $votes;
					}
					unset($votes);

					$this->_cache->set($cacheKey, $index, 60 * 5, null, array('compress' => true));
				}
				$sorted = Utility::SORT_DESC;
				break;
		}

		// Flip, if necessary
		if ($direction != $sorted) {
			$index = array_reverse($index, true);
		}

		// Return
		return $index;
	}

	/**
	 *	Add an item
	 *
	 *	@access protected
	 *	@param string Title
	 *	@param int User ID of author
	 *	@param string Body
	 *	@param string Teaser (to override Text::trim(body))
	 *	@param string Go Live Date
	 *	@param mixed Keywords (array or string)
	 *	@param string Description
	 *	@param string Slug
	 *	@param string Type
	 *	@param bool Set live
	 *	@param bool Set featured
	 *	@param bool Skip cache flushing
	 *	@return int Item ID
	 */
	protected function _addItem($title, $userId, $body, $teaser = null, $goLiveDate = null, $keywords = null, $description = null, $slug = null, $type = 'recipe', $live = false, $featured = false, $skipCache = false,$video_js= null)
	{
		// Process images in the body of the article
		// Note: variable $body is passed by reference
		if($images = Utility::parseImageTags($body, 'tempimages')) {
			// mkdir
			$destDir = BLUPATH_ASSETS.'/itemimages/';
			if (!file_exists($destDir)) {
				mkdir($destDir, 0777, true);
			}
			// Copy
			foreach($images as $image) {
				if($image['tempFile'] && file_exists($image['filePath'])) {
					copy($image['filePath'], $image['newFilePath']);
				}
			}
			// Delete temporary images
			if($uploads = Session::get('uploads')) {
				foreach($uploads as $imageUploads) {
					foreach($imageUploads as $imageUpload) {
						if(file_exists(BLUPATH_ASSETS.'/tempimages/'.$imageUpload['file_name'])) {
							unlink(BLUPATH_ASSETS.'/tempimages/'.$imageUpload['file_name']);
							// to do: delete file cache for these images
						}
					}
				}
			}
			// unset uploads in session
			Session::set('uploads',null);
		}

		// Deal with other images
		Utility::parseImageTags($body,'tempimages','itemimages');

		// Build query
		$params = array(
			'type' => '"'.$this->_db->escape($type).'"',
			'title' => '"'.$this->_db->escape($title).'"',
			'author' => (int) $userId,
			'body' => '"'.$this->_db->escape($body).'"',
			'date' => 'NOW()',
			'goLiveDate' => is_null($goLiveDate) ? 'NULL' : '"'.$this->_db->escape($goLiveDate).'"',
			'live' => (int) (bool) $live,
			'featured' => (int) (bool) $featured,
			'slug' => '"'.$this->_db->escape(empty($slug) ? Utility::slugify($title) : $slug).'"'
		);
		if (!is_null($teaser)) {
			$params['teaser'] = '"'.$this->_db->escape($teaser).'"';
		}
		if (!is_null($video_js)) {
			$params['video_js'] = '"'.$this->_db->escape($video_js).'"';
		}
		if (!is_null($keywords)) {
			$params['keywords'] = '"'.$this->_db->escape(is_array($keywords) ? implode(', ', $keywords) : $keywords).'"';
		}
		if (!is_null($description)) {
			$params['description'] = '"'.$this->_db->escape($description).'"';
		}

		// Format parameters
		foreach ($params as $field => &$param) {
			$param = '`'.$field.'` = '.$param;
		}
		unset($param);

		// Execute query
		$query = 'INSERT INTO `articles`
			SET '.implode(', ', $params);
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		$itemId = $this->_db->getInsertID();

		// Start counting views!
		$query = 'INSERT INTO `articleViews`
			SET `articleId` = '.(int) $itemId.',
				`views` = 0,
				`date` = NOW()';
		$this->_db->setQuery($query);
		$this->_db->query();

		// Start counting votes too
		$query = 'INSERT INTO `articleVotes`
			SET `articleId` = '.(int) $itemId.',
				`votes` = 0';
		$this->_db->setQuery($query);
		$this->_db->query();

		// Save inline images
		if($images) {
			$priority = 0;
			foreach($images as $image) {
				$priority++;
				$this->addImage($itemId, basename($image['newFilePath']), null, null, null, $priority, 'inline', $userId);
			}
		}

		// Flush cache
		if (!$skipCache) {

			// Don't need to flush item listings, they will expire naturally.

			// Do some mappings
			$this->_cache->delete('items_slugMapping');
			$this->_cache->delete('items_types');
			$this->_cache->delete('items_sortable_name');
			$this->_cache->delete('items_sortable_date');
			$this->_cache->delete('items_live');
			// Flush user articles
			$userModel = BluApplication::getModel('user');
			$userModel->flushUserSubmissions($userId);
		}

		// Return
		return $itemId;
	}

	/**
	 *	Edit an item
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param string Title
	 *	@param string Body
	 *	@param string Teaser
	 *	@param string Go Live Date
	 *	@param mixed Keywords (array or string)
	 *	@param string Description
	 *	@param string Slug
	 *	@return bool Success
	 */
	public function editItem($itemId, $title = null, $body = null, $teaser = null, $goLiveDate = null, $keywords = null, $description = null, $slug = null, $userId = null,$video_js = null)
	{
		// Process images in the body of the article
		// Note: variable $body is passed by reference
		if($images = Utility::parseImageTags($body, 'tempimages')) {
			// mkdir
			$destDir = BLUPATH_ASSETS.'/itemimages/';
			if (!file_exists($destDir)) {
				mkdir($destDir, 0777, true);
			}
			// Copy
			foreach($images as $image) {
				if($image['tempFile'] && file_exists($image['filePath'])) {
					copy($image['filePath'], $image['newFilePath']);
				}
			}
			// Delete temporary images
			if($uploads = Session::get('uploads')) {
				foreach($uploads as $imageUploads) {
					foreach($imageUploads as $imageUpload) {
						if(file_exists(BLUPATH_ASSETS.'/tempimages/'.$imageUpload['file_name'])) {
							unlink(BLUPATH_ASSETS.'/tempimages/'.$imageUpload['file_name']);
							// to do: delete file cache for these images
						}
					}
				}
			}
			// unset uploads in session
			Session::set('uploads',null);
		}

		// Deal with other images
		Utility::parseImageTags($body,'tempimages','itemimages');

		// Build query
		$params = array();
		if (!is_null($title)) {
			$params['title'] = $title;
		}
		if (!is_null($body)) {
			$params['body'] = $body;
		}
		if (!is_null($teaser)) {
			$params['teaser'] = $teaser;
		}
		
		if (!is_null($video_js)) {
			$params['video_js'] = $video_js;
		}		

		$params['goLiveDate'] = $goLiveDate;
		if (!is_null($keywords)) {
			$params['keywords'] = is_array($keywords) ? implode(', ', $keywords) : $keywords;
		}
		if (!is_null($description)) {
			$params['description'] = $description;
		}
		if (!is_null($slug)) {
			$params['slug'] = $slug;
		} else if (!empty($params['title'])) {
			$params['slug'] = Utility::slugify($params['title']);
		}

		// Format parameters
		if (empty($params)) {
			return false;
		}
		foreach ($params as $field => &$param) {
			if(is_null($param)) {
				$param = '`'.$field.'` = NULL';
			}
			else {
				$param = '`'.$field.'` = "'.$this->_db->escape($param).'"';
			}
		}
		unset($param);

		// get item before update
		$item = $this->getItem($itemId);

		// Execute query
		$query = 'UPDATE `articles`
			SET '.implode(', ', $params).'
			WHERE `id` = '.(int) $itemId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}

		// save article history
		if(!is_null($title) && $title!=$item['title']) {
			$query = 'INSERT INTO articleHistory SET userId='.(int)$userId.', articleId='.(int)$itemId.', type="title", oldValue="'.$this->_db->escape($item['title']).'", newValue="'.$this->_db->escape($title).'", date=NOW()';
			$this->_db->setQuery($query);
			$this->_db->query();
			//echo $query.'<br />';
		}
		if(!is_null($teaser) && $teaser!=$item['teaser']) {
			$query = 'INSERT INTO articleHistory SET userId='.(int)$userId.', articleId='.(int)$itemId.', type="teaser", oldValue="'.$this->_db->escape($item['teaser']).'", newValue="'.$this->_db->escape($teaser).'", date=NOW()';
			$this->_db->setQuery($query);
			$this->_db->query();
			//echo $query.'<br />';
		}
		if(!is_null($body) && $body!=$item['body']) {
			$query = 'INSERT INTO articleHistory SET userId='.(int)$userId.', articleId='.(int)$itemId.', type="body", oldValue="'.$this->_db->escape($item['body']).'", newValue="'.$this->_db->escape($body).'", date=NOW()';
			$this->_db->setQuery($query);
			$this->_db->query();
			//echo $query.'<br />';
		}

		// Delete all inline images first
		$this->deleteAllImages($itemId, 'inline');

		// Save inline images
		if($images) {
			$priority = 0;
			foreach($images as $image) {
				$priority++;
				$this->addImage($itemId, basename($image['newFilePath']), null, null, null, $priority, 'inline', $userId);
			}
		}

		// Flush cache
		$this->flushItem($itemId);
		$this->_cache->delete('items_slugMapping');

		// Return
		return true;
	}

	/**
	 *	Propose ingredients (as a user)
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param array Ingredients
	 *	@return bool Success
	 */
	public function proposeIngredients($itemId, $ingredients, $userId = null, $skipFlush = false)
	{
		// Format ingredients
		$ingredients = strip_tags(implode("\n", $ingredients));

		// get item before update
		$item = $this->getItem($itemId);

		// Store in item
		$query = 'UPDATE `articles`
			SET `ingredients` = "'.$this->_db->escape($ingredients).'"
			WHERE `id` = '.(int) $itemId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}

		// save article history
		// $userId is NULL only when adding a new recipe
		$oldIngredients = $item['ingredients'] ? implode("\n",$item['ingredients']) : '';
		if($userId && $ingredients && $ingredients!=$oldIngredients) {
			//var_dump($item['ingredients']);
			$query = 'INSERT INTO articleHistory SET userId="'.(int)$userId.'", articleId="'.(int)$itemId.'", type="ingredients", oldValue="'.$this->_db->escape($oldIngredients).'", newValue="'.$this->_db->escape($ingredients).'", date=NOW()';
			$this->_db->setQuery($query);
			$this->_db->query();
		}

		// Flush cache
		if (!$skipFlush) $this->flushItem($itemId);

		// Return
		return true;
	}

	public function addImageFromUpload($uploadId, $file) {
		// Determine path to asset file
		$origFileName = basename($file['name']);
		$assetFileName = md5(microtime().mt_rand(0, 250000)).'_'.$origFileName;
		$assetPath = BLUPATH_ASSETS.'/itemimages/'.$assetFileName;
		// Move uploaded file into place
		if (!Upload::move($uploadId, $assetPath)) {
			return false;
		}
		// Delete and then add default image
		$this->deleteAllImages($itemId, 'default');
		return $this->addImage($itemId, $assetFileName, null, null, null, 0, 'default');
	}

	function copyUnsavedImage($itemId, $fileName) {
		// mkdir
		$destDir = BLUPATH_ASSETS.'/itemimages/';
		if (!file_exists($destDir)) {
			mkdir($destDir, 0777, true);
		}
		$assetFileName = basename($fileName);
		$sourceAssetPath = BLUPATH_ASSETS.'/tempimages/'.$assetFileName;
		$destinationAssetPath = $destDir.$assetFileName;
		// Copy
		if(!copy($sourceAssetPath, $destinationAssetPath)) {
			return false;
		}
		// Delete
		unlink($sourceAssetPath);
		// Update user details
		return true;
	}

	/**
	 *	Add an image to an item
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param string Filename
	 *	@param string Title
	 *	@param string Description
	 *	@param string Mini description (to override Text::trim(description))
	 *	@param int Priority
	 *	@param string Type (default, inline, thumbnail or featured)
	 *	@return bool Success
	 */
	public function addImage($itemId, $filename, $title = null, $description = null, $miniDescription = null, $priority = 0, $type = null, $userId = null,$alt = null)
	{
		// Query builder
		$params = array(
			'articleId' => (int) $itemId,
			'filename' => '"'.$this->_db->escape(basename($filename)).'"',
			'sequence' => (int) $priority
		);
		if (!is_null($title)) {
			$params['title'] = '"'.$this->_db->escape($title).'"';
		}
		if (!is_null($description)) {
			$params['description'] = '"'.$this->_db->escape($description).'"';
		}
		if (!is_null($miniDescription)) {
			$params['minidescription'] = '"'.$this->_db->escape($miniDescription).'"';
		}
		if (!is_null($type)) {
			$params['type'] = '"'.$this->_db->escape($type).'"';
		}
		if (!is_null($userId)) {
			$params['userId'] = (int)$userId;
		}
		if (!is_null($alt)) {
			$params['alt'] = '"'.$this->_db->escape($alt).'"';
		}

		// Format
		foreach ($params as $field => &$param) {
			$param = '`'.$field.'` = '.$param;
		}
		unset($param);

		// Execute query
		$query = 'INSERT INTO `articleImages`
			SET '.implode(', ', $params);
		$this->_db->setQuery($query);
		$added = $this->_db->query();

		// Return
		return $added;
	}

	/**
	 *	Remove an image
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param string Image name
	 *	@return bool Success
	 */
	public function deleteImage($itemId, $imageName)
	{
		$query = 'DELETE FROM `articleImages`
			WHERE `articleId` = '.(int) $itemId.'
				AND `filename` = "'.$this->_db->escape($imageName).'"';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}

	/**
	 *	Remove all images
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param string Type of image (default, inline, featured or thumbnail)
	 *	@return bool Success
	 */
	public function deleteAllImages($itemId,$type=null)
	{
		$query = 'DELETE FROM `articleImages`
			WHERE `articleId` = '.(int) $itemId;
		if($type) {
			$query .= ' AND `type` = "'.$this->_db->escape($type).'"';
		}
		$this->_db->setQuery($query);
		return $this->_db->query();
	}

	public function getLinks($itemId) {

		// Get links
		$query = 'SELECT al.*
			FROM `articleLinks` AS `al`
			WHERE al.articleId = '.(int) $itemId.'
			ORDER BY al.sequence DESC, al.id ASC';
		$this->_db->setQuery($query);
		return $this->_db->loadAssocList('id');
	}

	/**
	 *	Add a related link to an item
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param string URL
	 *	@param string Title
	 *	@param string Description
	 *	@param int Priority
	 *	@return bool Success
	 */
	public function addLink($itemId, $link, $title = null, $description = null, $priority = 0)
	{
		// Query builder
		$params = array(
			'articleId' => (int) $itemId,
			'href' => '"'.$this->_db->escape($link).'"',
			'sequence' => (int) $priority
		);
		if (!is_null($title)) {
			$params['title'] = '"'.$this->_db->escape($title).'"';
		}
		if (!is_null($description)) {
			$params['description'] = '"'.$this->_db->escape($description).'"';
		}

		// Format
		foreach ($params as $field => &$param) {
			$param = '`'.$field.'` = '.$param;
		}
		unset($param);

		// Execute query
		$query = 'INSERT INTO `articleLinks`
			SET '.implode(', ', $params);
		$this->_db->setQuery($query);
		$added = $this->_db->query();

		// Return
		return $added;
	}

	public function updateLink($itemId, $linkId, $href, $title = null, $description = null, $priority = 0) {
		$query = 'UPDATE articleLinks
					SET href="'.$this->_db->escape($href).'",title="'.$this->_db->escape($title).'",description="'.$this->_db->escape($description).'",sequence='.(int)$priority.'
					WHERE id='.(int)$linkId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}

	/**
	 *	Remove a related link
	 *
	 *	@access public
	 *	@param int Link ID
	 *	@return bool Success
	 */
	public function deleteLink($itemId,$linkId)
	{
		$query = 'DELETE FROM `articleLinks`
			WHERE `id` = '.(int) $linkId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}

	/**
	 *	Flush cached indexes
	 *
	 *	@access public
	 *	@return bool Success
	 */
	public function rebuildCoreData()
	{
		$cacheModel = BluApplication::getModel('cache');
		return $cacheModel->deleteEntriesLike('items');
	}

	/**
	 *	Check if slug is in use
	 *
	 *	@access public
	 *	@param string Slug
	 *	@param int Item ID exception
	 *	@return bool
	 */
	public function isSlugInUse($slug, $itemId = null)
	{
		// Check database
		$query = 'SELECT a.id
			FROM `articles` AS `a`
			WHERE a.slug = "'.$this->_db->escape($slug).'"';
		if ($itemId) {
			// It's OK if slug-holder is myself.
			$query .= '
				AND a.id != '.(int) $itemId;
		}
		$this->_db->setQuery($query);
		return (bool) $this->_db->loadResult();
	}

	/**
	 *	Get comment's item ID
	 *
	 *	@access public
	 *	@param int Comment ID
	 *	@return int Item ID
	 */
	public function getCommentItemId($commentId)
	{
		$query = 'SELECT c.objectId
			FROM `comments` AS `c`
			WHERE c.id = '.(int) $commentId.'
				AND c.objectType = "article"';
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 *	Flush an item's cache.
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@return bool
	 */
	public function flushItem($itemId)
	{
		$this->getItem($itemId, null, true);
		return true;
	}

	/**
	 *	Refresh all of an item's comments' cache
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@return bool
	 */
	public function flushItemComments($itemId)
	{
		return $this->_cache->delete('item_'.$itemId.'_comments');
	}

	/**
	 *	Refresh a comment's cache
	 *
	 *	@access public
	 *	@param int Comment ID
	 *	@return bool
	 */
	public function flushComment($commentId)
	{
		return $this->flushItemComments($this->getCommentItemId($commentId));
	}

	/**
	 *	Refresh all of an item's images' cache
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@return bool
	 */
	public function flushItemImages($itemId)
	{
		return $this->flushItem($itemId);	// Item images are built into item.
	}

	/**
	 *	Refresh all of an item's links' cache
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@return bool
	 */
	public function flushItemLinks($itemId)
	{
		return $this->flushItem($itemId);	// Item links are built into item.
	}

	/**
	 *	Refresh all of an item's ratings' cache
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@return bool
	 */
	public function flushItemRatings($itemId)
	{
		return $this->_cache->delete('item_'.$itemId.'_ratings');
	}

	/**
	 *	Refresh all of an item's meta values cache
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@return bool
	 */
	public function flushItemMeta($itemId)
	{
		$cacheModel = BluApplication::getModel('cache');
		return $cacheModel->deleteEntriesLike('itemMetaGroups_'.$itemId.'_');
	}

	/**
	 *	Refresh all of an item's relationships
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@return bool
	 */
	public function flushItemRelationships($itemId)
	{
		return $this->flushItem($itemId);	// Item relationships are built into item.
	}

	/**
	 *	Flush item searches
	 *
	 *	@access public
	 *	@return bool Success
	 */
	public function flushItemSearches()
	{
		$cacheKeyLike = 'items\_a:';

		$cacheModel = BluApplication::getModel('cache');
		$flushed = $cacheModel->deleteEntriesLike($cacheKeyLike);

		return $flushed;
	}

	/**
	 *	Get a group of items
	 *
	 *	@access public
	 *	@param int Itemgroup ID
	 *	@return array Itemgroup details
	 */
	public function getItemGroup($itemGroupId)
	{
		// Short circuit
		if (!$itemGroupId) {
			return false;
		}

		// Get base details
		$cacheKey = 'itemGroup_'.$itemGroupId;
		$itemGroup = $this->_cache->get($cacheKey);
		if ($itemGroup === false) {

			// Fetch base details
			$query = 'SELECT ag.*
				FROM `articleGroups` AS `ag`
				WHERE ag.id = '.(int) $itemGroupId;
			$this->_db->setQuery($query);
			if ($itemGroup = $this->_db->loadAssoc()) {

				// Fetch item mapping
				$query = 'SELECT aga.*
					FROM `articleGroupArticles` AS `aga`
					WHERE aga.groupId = '.(int) $itemGroup['id'];
				$this->_db->setQuery($query);
				$itemGroup['values'] = $this->_db->loadAssocList('articleId');

				// Fetch images
				$query = 'SELECT agi.*
					FROM `articleGroupImages` AS `agi`
					WHERE agi.groupId = '.(int) $itemGroup['id'].'
					ORDER BY agi.sequence ASC';
				$this->_db->setQuery($query);
				$itemGroup['images'] = $this->_db->loadAssocList('filename');
			}

			// Generate link
			switch ($itemGroup['type']) {
				case 'cookbook':
					$itemGroup['link'] = '/cookbooks/'.$itemGroup['slug'].'.htm';
					break;
			}

			// Store in cache
			$this->_cache->set($cacheKey, $itemGroup);
		}

		// Append author details
		$userModel = BluApplication::getModel('user');
		$itemGroup['author'] = $userModel->getUser($itemGroup['author']);

		// Pull out image for convenience, use author's if none available.
		if (empty($itemGroup['images'])) {
			$itemGroup['image'] = array(
				'title' => $itemGroup['title'],
				'filename' => $itemGroup['author']['image']
			);
		} else {
			$itemGroup['image'] = reset($itemGroup['images']);
		}

		// Return
		return $itemGroup;
	}

	/**
	 *	Get a cookbook
	 *
	 *	@access public
	 *	@param int Cookbook ID
	 *	@return array Cookbook details
	 */
	public function getCookbook($cookbookId)
	{
		$itemGroup = $this->getItemGroup($cookbookId);

		// This is not a cookbook!
		if ($itemGroup['type'] != 'cookbook') {
			return false;
		}

		// Return
		return $itemGroup;
	}

	/**
	 *	Add an itemgroup
	 *
	 *	@access protected
	 *	@param string Type
	 *	@param int User ID
	 *	@param string Title
	 *	@param string Description
	 *	@param string Slug
	 *	@return int Itemgroup ID
	 */
	protected function _addItemGroup($type, $userId, $title, $description, $slug = null, $private = false)
	{
		// Generate slug
		if (!$slug) {
			$slug = Utility::slugify($title);
		}

		// Validate slug
		if ($this->isItemGroupSlugInUse($slug)) {
			$i = 2;
			while ($this->isItemGroupSlugInUse($slug.'_'.$i)) {
				$i++;
			}
			$slug .= '_'.$i;
		}

		// Update database
		$query = 'INSERT INTO `articleGroups`
			SET `type` = "'.$this->_db->escape($type).'",
				`title` = "'.$this->_db->escape($title).'",
				`description` = "'.$this->_db->escape($description).'",
				`author` = '.(int) $userId.',
				`private` = '.(int) $private.',
				`date` = NOW(),
				`live` = 1,
				`slug` = "'.$this->_db->escape($slug).'"';
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		$itemGroupId = $this->_db->getInsertID();

		// Refresh sort indices
		$this->_cache->delete('itemGroups_live');
		$this->_cache->delete('itemGroups_slugMapping');
		$this->_cache->delete('itemGroups_typeMapping');
		$this->_cache->delete('itemGroups_authorMapping');
		$this->_cache->delete('itemGroups_sortable_name');
		$this->_cache->delete('itemGroups_sortable_date');
		$this->_cache->delete('itemGroups_quicksearch');

		// Return
		return $itemGroupId;
	}

	/**
	 *	Check if itemgroup slug in use
	 *
	 *	@access public
	 *	@param string Slug
	 *	@param int Itemgroup ID exception
	 *	@return bool Used
	 */
	public function isItemGroupSlugInUse($slug, $itemGroupId = null)
	{
		// Check database
		$query = 'SELECT ag.id
			FROM `articleGroups` AS `ag`
			WHERE ag.slug = "'.$this->_db->escape($slug).'"';

		// ...except if it's ourself
		if ($itemGroupId) {
			$query .= '
				AND ag.id != '.(int) $itemGroupId;
		}

		// Query
		$this->_db->setQuery($query);
		return (bool) $this->_db->loadResult();
	}

	/**
	 *	Add a cookbook
	 *
	 *	@access public
	 *	@param int User ID
	 *	@param string Title
	 *	@param string Description
	 *	@param string Slug
	 *	@return int Cookbook ID
	 */
	public function addCookbook($userId, $title, $description, $slug = null, $private = null)
	{
		return $this->_addItemGroup('cookbook', $userId, $title, $description, $slug, $private);
	}

	/**
	 *	Edit an itemgroup
	 *
	 *	@access protected
	 *	@param int Itemgroup ID
	 *	@param string Title
	 *	@param string Description
	 *	@param string Slug
	 *	@return bool Success
	 */
	protected function _updateItemGroup($itemGroupId, $title = null, $description = null, $slug = null, $private = null)
	{
		// Build query
		$query = array();
		if ($title) {
			$query[] = '`title` = "'.$this->_db->escape($title).'"';
		}
		if (!is_null($description)) {
			$query[] = '`description` = "'.$this->_db->escape($description).'"';
		}
		if ($slug) {
			$query[] = '`slug` = "'.$this->_db->escape($slug).'"';
		}
		if ($private !== null) {
			$query[] = '`private` = '.($private ? 1 : 0);
		}

		// Update database
		if (empty($query)) {
			return true;
		}
		$query = 'UPDATE `articleGroups`
			SET '.implode(', ', $query).'
			WHERE `id` = '.(int) $itemGroupId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}

		// Update cache
		$this->_clearItemGroup($itemGroupId);
		if ($title) {
			$this->_cache->delete('itemGroups_sortable_name');
		}
		if ($slug) {
			$this->_cache->delete('itemGroups_slugMapping');
		}

		// Return
		return true;
	}

	/**
	 *	Edit a cookbook
	 *
	 *	@access public
	 *	@param int Cookbook ID
	 *	@param string Title
	 *	@param string Description
	 *	@param string Slug
	 *	@return bool Success
	 */
	public function updateCookbook($cookbookId, $title = null, $description = null, $slug = null, $private = null)
	{
		return $this->_updateItemGroup($cookbookId, $title, $description, $slug, $private);
	}

	/**
	 *	Delete an itemgroup
	 *
	 *	@access protected
	 *	@param int Itemgroup ID
	 *	@return bool Success
	 */
	protected function _deleteItemGroup($itemGroupId)
	{
		// Update database
		$query = 'UPDATE `articleGroups`
			SET `live` = 0
			WHERE `id` = '.(int) $itemGroupId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}

		// Flush cache
		$this->_cache->delete('itemGroups_live');

		// Return
		return true;
	}

	/**
	 *	Delete a cookbook
	 *
	 *	@access public
	 *	@param int Cookbook ID
	 *	@return bool Success
	 */
	public function deleteCookbook($cookbookId)
	{
		return $this->_deleteItemGroup($cookbookId);
	}

	/**
	 *	Add an item to an itemgroup (or update existing comment)
	 *
	 *	@access protected
	 *	@param int Itemgroup ID
	 *	@param int Item ID
	 *	@param string Extra comment
	 *	@return bool Success
	 */
	protected function _addItemGroupItem($itemGroupId, $itemId, $comment = null)
	{
		// Update database
		$query = 'INSERT INTO `articleGroupArticles`
			SET `groupId` = '.(int) $itemGroupId.',
				`articleId` = '.(int) $itemId.',
				`comment` = "'.$this->_db->escape($comment).'"
			ON DUPLICATE KEY UPDATE
				`comment` = "'.$this->_db->escape($comment).'"';
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}

		// Update cache
		$this->_clearItemGroup($itemGroupId);

		// Return
		return true;
	}

	/**
	 *	Add a recipe to a cookbook
	 *
	 *	@access public
	 *	@param int Cookbook ID
	 *	@param int Recipe ID
	 *	@param string Comment
	 *	@return bool Success
	 */
	public function addCookbookRecipe($cookbookId, $recipeId, $comment = null)
	{
		return $this->_addItemGroupItem($cookbookId, $recipeId, $comment);
	}

	/**
	 *	Delete an item from an itemgroup
	 *
	 *	@access protected
	 *	@param int Itemgroup ID
	 *	@param int Item ID
	 *	@return bool Success
	 */
	protected function _deleteItemGroupItem($itemGroupId, $itemId)
	{
		// Update database
		$query = 'DELETE FROM `articleGroupArticles`
			WHERE `groupId` = '.(int) $itemGroupId.'
				AND `articleId` = '.(int) $itemId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}

		// Update cache
		$this->_clearItemGroup($itemGroupId);

		// Return
		return true;
	}

	/**
	 *	Remove a recipe from a cookbook
	 *
	 *	@access public
	 *	@param int Cookbook ID
	 *	@param int Recipe ID
	 *	@return bool Success
	 */
	public function deleteCookbookRecipe($cookbookId, $recipeId)
	{
		return $this->_deleteItemGroupItem($cookbookId, $recipeId);
	}

	/**
	 *	Flush the cache entry for an item group
	 *
	 *	@access protected
	 *	@param int Itemgroup ID
	 *	@return bool Success
	 */
	protected function _clearItemGroup($itemGroupId)
	{
		$cacheKey = 'itemGroup_'.$itemGroupId;
		$this->_cache->delete('itemGroups_quicksearch');
		$this->_cache->delete('itemGroups_private');
		$this->_cache->delete($cacheKey);

		return true;
	}

	/**
	 *	Add itemgroup image
	 *
	 *	@access protected
	 *	@param int Itemgroup ID
	 *	@param array Upload details (from $_FILES)
	 *	@param string Title
	 *	@param string Description
	 *	@param int Sequence
	 *	@return string Filename
	 */
	protected function _addItemGroupImage($itemGroupId, $image, $title = null, $description = null, $sequence = 0)
	{
		// Save file to filestore
		if (!$imageName = Upload::saveFile($image, BLUPATH_ASSETS.'/itemimages')) {
			return false;
		}

		// Save to database
		$query = 'INSERT INTO `articleGroupImages`
			SET `groupId` = '.(int) $itemGroupId.',
				`filename` = "'.$this->_db->escape($imageName).'",
				`title` = "'.$this->_db->escape($title).'",
				`description` = "'.$this->_db->escape($description).'",
				`sequence` = '.(int) $sequence;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}

		// Flush cache
		$this->_clearItemGroup($itemGroupId);

		// Return
		return $imageName;
	}

	/**
	 *	Add a cookbook image
	 *
	 *	@access public
	 *	@param int Cookbook ID
	 *	@param array Upload details (from $_FILES)
	 *	@param string Title
	 *	@param string Description
	 *	@param int Sequence
	 *	@return string Filename
	 */
	public function addCookbookImage($cookbookId, $image, $title = null, $description = null, $sequence = 0)
	{
		return $this->_addItemGroupImage($cookbookId, $image, $title, $description, $sequence);
	}

	/**
	 *	Edit an itemgroup image
	 *
	 *	@access protected
	 *	@param int Itemgroup ID
	 *	@param string Filename
	 *	@param string Title
	 *	@param string Description
	 *	@param int Sequence
	 *	@return bool Success
	 */
	protected function _updateItemGroupImage($itemGroupId, $filename, $title = null, $description = null, $sequence = 0)
	{
		// Update database
		$query = 'UPDATE `articleGroupImages`
			SET `title` = "'.$this->_db->escape($title).'",
				`description` = "'.$this->_db->escape($description).'",
				`sequence` = '.(int) $sequence.'
			WHERE `groupId` = '.(int) $itemGroupId.'
				AND `filename` = "'.$this->_db->escape($filename).'"';
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}

		// Update cache
		$this->_clearItemGroup($itemGroupId);

		// Return
		return true;
	}

	/**
	 *	Edit a cookbook image
	 *
	 *	@access public
	 *	@param int Itemgroup ID
	 *	@param string Filename
	 *	@param string Title
	 *	@param string Description
	 *	@param int Sequence
	 *	@return bool Success
	 */
	public function updateCookbookImage($cookbookId, $filename, $title = null, $description = null, $sequence = 0)
	{
		return $this->_updateItemGroupImage($cookbookId, $filename, $title, $description, $sequence);
	}

	/**
	 *	Delete an itemgroup image
	 *
	 *	@access protected
	 *	@param int Itemgroup ID
	 *	@param string Filename
	 *	@return bool Success
	 */
	protected function _deleteItemGroupImage($itemGroupId, $filename)
	{
		// Update database
		$query = 'DELETE FROM `articleGroupImages`
			WHERE `groupId` = '.(int) $itemGroupId.'
				AND `filename` = "'.$this->_db->escape($filename).'"';
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}

		// Update filestore
		$filepath = BLUPATH_ASSETS.'/itemimages/'.$filename;
		if (file_exists($filepath)) {
			if (!unlink($filepath)) {
				return false;
			}
		}

		// Update cache
		$this->_clearItemGroup($itemGroupId);

		// Return
		return true;
	}

	/**
	 *	Delete a cookbook image
	 *
	 *	@access public
	 *	@param int Cookbook ID
	 *	@param string Filename
	 *	@return bool Success
	 */
	public function deleteCookbookImage($cookbookId, $filename)
	{
		return $this->_deleteItemGroupImage($cookbookId, $filename);
	}

	/**
	 *	Sort itemgroups
	 *
	 *	@access protected
	 *	@param array Itemgroup IDs
	 *	@param string Sort
	 *	@return array Itemgroup IDs
	 */
	protected function _sortItemGroups($itemGroups, $sort = 'name_asc')
	{
		// Empty
		if (empty($itemGroups)) {
			return $itemGroups;
		}

		// Generate sort index
		switch ($sort) {
			case 'name_asc':
				$index = $this->_getItemGroupSortIndex('name', Utility::SORT_ASC);
				break;

			case 'date_desc':
				$index = $this->_getItemGroupSortIndex('date', Utility::SORT_DESC);
				break;

			case 'saved':
				$index = $this->_getItemGroupSortIndex('saves', Utility::SORT_DESC);
				break;

			case 'relevance':
			default:
				$index = null;
				break;
		}

		// Pair up with those we're interested in
		if ($index) {
			$itemGroups = array_intersect_key($index, array_flip($itemGroups));

			// Use IDs only
			$itemGroups = array_keys($itemGroups);
			$itemGroups = array_combine($itemGroups, $itemGroups);
		}

		// Return
		return $itemGroups;
	}

	/**
	 *	Sort cookbooks
	 *
	 *	@access public
	 *	@param array Cookbook IDs
	 *	@param string Sort
	 *	@return array Cookbook IDs
	 */
	public function sortCookbooks($cookbooks, $sort = 'name_asc')
	{
		return $this->_sortItemGroups($cookbooks, $sort);
	}

	/**
	 *	Load sorting index for itemgroups
	 *
	 *	@access public
	 *	@param string Criteria
	 *	@param int Direction
	 *	@return array Itemgroup ID => Relative statistic (only used for sorting)
	 */
	protected function _getItemGroupSortIndex($criteria, $direction)
	{
		// Get index, sorted in ascending order
		switch ($criteria) {
			case 'name':
				$cacheKey = 'itemGroups_sortable_name';
				$index = $this->_cache->get($cacheKey);
				if ($index === false) {
					$query = 'SELECT ag.id, ag.title
						FROM `articleGroups` AS `ag`
						ORDER BY ag.title ASC';
					$this->_db->setQuery($query);
					$index = $this->_db->loadResultAssocArray('id', 'title');

					$this->_cache->set($cacheKey, $index, 60 * 60 * 24 / 2, null, array('compress' => true));
				}
				$sorted = Utility::SORT_ASC;
				break;

			case 'date':
				$cacheKey = 'itemGroups_sortable_date';
				$index = $this->_cache->get($cacheKey);
				if ($index === false) {
					$query = 'SELECT ag.id, UNIX_TIMESTAMP(ag.date) as `date`
						FROM `articleGroups` AS `ag`
						ORDER BY `date` DESC';
					$this->_db->setQuery($query);
					$index = $this->_db->loadResultAssocArray('id', 'date');

					$this->_cache->set($cacheKey, $index, 60 * 60 * 24 / 2, null, array('compress' => true));
				}
				$sorted = Utility::SORT_DESC;
				break;

			case 'saves':
				$cacheKey = 'itemGroups_sortable_saves';
				$index = $this->_cache->get($cacheKey);
				if ($index === false) {
					$query = 'SELECT ag.id, COUNT(ag.id) AS `saves`
						FROM `articleGroups` AS `ag`
							LEFT JOIN `userSaves` AS `us` ON ag.id = us.objectId
								AND us.objectType = "cookbook"
						GROUP BY ag.id
						ORDER BY `saves` DESC';
					$this->_db->setQuery($query);
					$index = $this->_db->loadResultAssocArray('id', 'saves');

					$this->_cache->set($cacheKey, $index, 60 * 60 * 24 / 2, null, array('compress' => true));
				}
				$sorted = Utility::SORT_DESC;
				break;
		}

		// Flip, if necessary
		if ($direction != $sorted) {
			$index = array_reverse($index, true);
		}

		// Return
		return $index;
	}

	/**
	 *	Filter itemgroups by live flag
	 *
	 *	@access public
	 *	@param array Itemgroups (Itemgroup IDs as key)
	 *	@param bool Return non-live itemgroups
	 *	@return array Filtered itemgroups
	 */
	public function filterLiveItemGroups($itemGroups, $invert = false)
	{
		// Get all live itemgroups
		static $liveItemGroups;
		if (!isset($liveItemGroups)) {
			$cacheKey = 'itemGroups_live';
			$liveItemGroups = $this->_cache->get($cacheKey);
			if ($liveItemGroups === false) {
				$query = 'SELECT ag.id
					FROM `articleGroups` AS `ag`
					WHERE ag.live = 1';
				$this->_db->setQuery($query);
				$liveItemGroups = $this->_db->loadResultAssocArray('id', 'id');
				$this->_cache->set($cacheKey, $liveItemGroups, 60 * 60 * 24 / 2, null, array('compress' => true));
			}
		}

		// Return all?
		if (empty($liveItemGroups)) {
			return $invert ? array() : $itemGroups;
		}

		// Intersect (or diff) keys
		$itemGroups = $invert ? array_diff_key($itemGroups, $liveItemGroups) : array_intersect_key($itemGroups, $liveItemGroups);

		// Return
		return $itemGroups;
	}

	public function filterPrivateItemGroups($itemGroups)
	{
		static $privateItemGroups;
		if (!isset($privateItemGroups)) {
                        $cacheKey = 'itemGroups_private';
                        $privateItemGroups = $this->_cache->get($cacheKey);
                        if ($privateItemGroups === false) {
                                $query = 'SELECT ag.id, ag.author
                                        FROM `articleGroups` AS `ag`
                                        WHERE ag.private = 1';
                                $this->_db->setQuery($query);
                                $privateItemGroups = $this->_db->loadResultAssocArray('id', 'author');
                                $this->_cache->set($cacheKey, $privateItemGroups, 60 * 60 * 24 / 2, null, array('compress' => true));
                        }
                }

                // Return all?
                if (empty($privateItemGroups)) {
                        return $itemGroups;
                }

		$userModel = BluApplication::getModel('user');
		$user = $userModel->getCurrentUser();
		foreach ($privateItemGroups as $id => $author) {
			if ($author == $user['id']) {
				unset($privateItemGroups[$id]);
			}
		}

                // Intersect (or diff) keys
                $itemGroups = array_diff_key($itemGroups, $privateItemGroups);

                // Return
                return $itemGroups;
	}

	/**
	 *	Filter cookbooks by live flag
	 *
	 *	@access public
	 *	@param array Cookbooks (IDs as key)
	 *	@param bool Return non-live cookbooks
	 *	@return array Filtered cookbooks
	 */
	public function filterLiveCookbooks($cookbooks, $invert = false)
	{
		return $this->filterLiveItemGroups($cookbooks, $invert);
	}

	/**
	 *	Get itemgroup-slug mapping
	 *
	 *	@access protected
	 *	@return array Mapping
	 */
	public function _getItemGroupSlugMapping()
	{
		$cacheKey = 'itemGroups_slugMapping';
		$mapping = $this->_cache->get($cacheKey);
        $mapping = false;
		if ($mapping === false) {
			$query = 'SELECT ag.id, ag.slug
				FROM `articleGroups` AS `ag`';
			$this->_db->setQuery($query);
			$mapping = $this->_db->loadResultAssocArray('slug', 'id');

			$this->_cache->set($cacheKey, $mapping, 60 * 60 * 24 / 2, null, array('compress' => true));
		}
		return $mapping;
	}

	/**
	 *	Get an itemgroup ID
	 *
	 *	@access protected
	 *	@param string Slug
	 *	@return int Itemgroup ID
	 */
	protected function _getItemGroupId($slug)
	{
		$mapping = $this->_getItemGroupSlugMapping();
		return isset($mapping[$slug]) ? $mapping[$slug] : false;
	}

	/**
	 *	Get a cookbook ID
	 *
	 *	@access public
	 *	@param string Slug
	 *	@return int Cookbook ID
	 */
	public function getCookbookId($slug)
	{
		return $this->_getItemGroupId($slug);
	}

	/**
	 *	Get cookbooks that fulfill the criteria, sorts by relevance by default.
	 *
	 *	@access public
	 *	@param int Offset
	 *	@param int Limit
	 *	@param string Sort
	 *	@param string Search term
	 *	@param bool Force Rebuild
	 *	@return array Cookbook IDs
	 */
	public function getCookbooks($offset = null, $limit = null, $sort = null, $search = null, $forceRebuild = false)
	{
		// Get all itemgroups
		$itemGroups = $this->_getItemGroups(null, null, $sort, $search, $forceRebuild);

		// Filter by cookbook type
		$itemGroups = $this->_filterTypeItemGroups($itemGroups, 'cookbook');

		// Slice
		if ($limit !== null) {
			$itemGroups = array_slice($itemGroups, $offset, $limit, true);
		}

		// Return
		return $itemGroups;
	}

	/**
	 *	Get itemgroups that fulfill the criteria, sorts by relevance by default.
	 *
	 *	@access protected
	 *	@param int Offset
	 *	@param int Limit
	 *	@param string Sort
	 *	@param string Search term
	 *	@param bool Force Rebuild
	 *	@return array Itemgroup IDs
	 */
	protected function _getItemGroups($offset = null, $limit = null, $sort = null, $search = null, $forceRebuild = false)
	{
		// Fewer cache entries
		if ($search) {
			$search = strtolower(trim($search));
		}

		// Search
		if (strlen($search)) {
			$index = $this->_getItemGroupsQuicksearchIndex();
			$itemGroups = Utility::quickSearch($search, $index);

		// Just grab everything
		} else {
			$userModel = BluApplication::getModel('user');
			$user = $userModel->getCurrentUser();
			$query = 'SELECT ag.id
				FROM `articleGroups` AS `ag` where private = 0 or author = '.(int) $user['id'];

			$this->_db->setQuery($query);
			$itemGroups = $this->_db->loadResultAssocArray('id', 'id');
		}

		// Sort
		if ($sort) {
			$itemGroups = $this->_sortItemGroups($itemGroups, $sort);
		}

		// Get total and slice as required
		if ($limit !== null) {
			$itemGroups = array_slice($itemGroups, $offset, $limit, true);
		}

		// Return
		return $itemGroups;
	}

	/**
	 *	Filter itemgroups by type
	 *
	 *	@todo split cachekey into individual keys (per type)
	 *	@access protected
	 *	@param array Itemgroup IDs
	 *	@param string Type
	 *	@param bool Return itemgroups NOT of given type
	 *	@return array Itemgroup IDs
	 */
	protected function _filterTypeItemGroups($itemGroups, $type, $invert = false)
	{
		// Get mapping
		static $typeItemGroups;
		if (!$typeItemGroups) {
			$cacheKey = 'itemGroups_typeMapping';
			$typeItemGroups = $this->_cache->get($cacheKey);
			if ($typeItemGroups === false) {
				$query = 'SELECT ag.id, ag.type
					FROM `articleGroups` AS `ag`';
				$this->_db->setQuery($query);
				$typeItemGroups = $this->_db->loadResultAssocArray('id', 'type');
				$this->_cache->set($cacheKey, $typeItemGroups);
			}
		}

		// Return all itemgroups?
		if (empty($typeItemGroups)) {
			return $invert ? $itemGroups : array();
		}

		// Intersect (or diff) keys
		$itemGroups = $invert ? array_diff_key($itemGroups, $typeItemGroups) : array_intersect_key($itemGroups, $typeItemGroups);

		// Return
		return $itemGroups;
	}

	/**
	 *	Filter itemgroups by author
	 *
	 *	@access protected
	 *	@param array Itemgroup IDs
	 *	@param int User ID
	 *	@return array Itemgroup IDs
	 */
	protected function _filterAuthorItemGroups($itemGroups, $userId)
	{
		// Get mapping
		$cacheKey = 'itemGroups_authorMapping';
		$authorItemGroups = $this->_cache->get($cacheKey);
		if ($authorItemGroups === false) {
			$query = 'SELECT ag.id, ag.author
				FROM `articleGroups` AS `ag`';
			$this->_db->setQuery($query);
			$authorItemGroups = $this->_db->loadResultAssocArray('id', 'author');
			$this->_cache->set($cacheKey, $authorItemGroups);
		}

		// Restrict mapping to user
		$authorItemGroups = array_intersect($authorItemGroups, array($userId));

		// Return all itemgroups?
		if (empty($authorItemGroups)) {
			return array();
		}

		// Intersect (or diff) keys
		$itemGroups = array_intersect_key($itemGroups, $authorItemGroups);

		// Return
		return $itemGroups;
	}

	/**
	 *	Filter cookbooks by author
	 *
	 *	@access public
	 *	@param array Cookbook IDs
	 *	@param int User ID
	 *	@return array Cookbook IDs
	 */
	public function filterAuthorCookbooks($cookbooks, $userId)
	{
		return $this->_filterAuthorItemGroups($cookbooks, $userId);
	}

	/**
	 *	Filter itemgroups by those with images
	 *
	 *	@access protected
	 *	@param array Itemgroups (Itemgroup IDs as key)
	 *	@param bool Return itemgroups with no images
	 *	@return array Filtered itemgroups
	 */
	protected function _filterImagedItemGroups($itemGroups, $invert = false)
	{
		// Get all imaged itemgroups
		static $imagedItemGroups;
		if (!isset($imagedItemGroups)) {
			$cacheKey = 'itemGroups_imaged';
			$imagedItemGroups = $this->_cache->get($cacheKey);
			if ($imagedItemGroups === false) {
				$query = 'SELECT agi.groupId
					FROM `articleGroupImages` AS `agi`
					GROUP BY agi.groupId';
				$this->_db->setQuery($query);
				$imagedItemGroups = $this->_db->loadResultAssocArray('groupId', 'groupId');
				$this->_cache->set($cacheKey, $imagedItemGroups);
			}
		}

		// Return all?
		if (empty($imagedItemGroups)) {
			return $invert ? array() : $itemGroups;
		}

		// Intersect (or diff) keys
		$itemGroups = $invert ? array_diff_key($itemGroups, $imagedItemGroups) : array_intersect_key($itemGroups, $imagedItemGroups);

		// Return
		return $itemGroups;
	}

	/**
	 *	Filter cookbooks by those with images
	 *
	 *	@access protected
	 *	@param array Itemgroups IDs
	 *	@param bool Return cookbooks with no images
	 *	@return array Filtered cookbooks
	 */
	public function filterImagedCookbooks($cookbooks, $invert = false)
	{
		return $this->_filterImagedItemGroups($cookbooks, $invert);
	}

	/**
	 *	Get the full quicksearch index for itemgroups
	 *
	 *	@access private
	 *	@param string Language code
	 *	@return array Indices
	 */
	private function _getItemGroupsQuicksearchIndex()
	{
		// Get greppable index
		$cacheKey = 'itemGroups_quicksearch';
		$quickSearchIndices = $this->_cache->get($cacheKey);
		if ($quickSearchIndices === false) {

			// Get from database
			$query = 'SELECT ag.id, ag.title, ag.description
				FROM `articleGroups` AS `ag`';
			$this->_db->setQuery($query);

			$quickSearchIndices = $this->_db->loadAssocList('id');

			foreach ($quickSearchIndices as &$itemGroup) {

				// Compact title
				if ($itemGroup['title']) {
					$itemGroup['title'] = Text::filterCommonWords($itemGroup['title'], $this->_getCommonWords());
					$itemGroup['title'] = implode(' ', array_unique($itemGroup['title']));
				}

				// Compact description
				if ($itemGroup['description']) {
					$itemGroup['description'] = Text::filterCommonWords($itemGroup['description'], $this->_getCommonWords());
					$itemGroup['description'] = implode(' ', array_unique($itemGroup['description']));
				}

				unset($itemGroup['id']);
			}
			unset($itemGroup);

			// Set in cache, deplete daily
			$expiry = strtotime('3AM +1 day EST') - time();
			$this->_cache->set($cacheKey, $quickSearchIndices, $expiry, null, array('compress' => true));
		}

		// Return
		return $quickSearchIndices;
	}

	/**
	 *	Get quicktip section
	 *
	 *	@access public
	 *	@return string Quicktip section
	 */
	public function getQuicktipSection($quicktipId) {
		$query = 'SELECT lmv.name AS section
					FROM articleMetaValues AS amv
					INNER JOIN languageMetaGroups AS lmg ON lmg.slug="encyclopedia_of_tips_sections"
					INNER JOIN metaValues AS mv ON lmg.id=mv.groupId
					INNER JOIN languageMetaValues AS lmv ON mv.id=lmv.id
					WHERE amv.articleId='.(int)$quicktipId.' AND amv.valueId=lmv.id AND amv.groupId=lmg.id';
		$this->_db->setQuery($query, 0, 1);
		$quicktipSection = $this->_db->loadResult();
		return $quicktipSection;
	}

	/**
	 *	Get random quicktip
	 *
	 *	@access public
	 *	@return array Quicktip
	 */
	public function getRandomQuicktip() {
		$cacheKey = 'randomQuicktip';
		$randomQuicktip = $this->_cache->get($cacheKey);
		if ($randomQuicktip === false) {
			$query = 'SELECT a.id, a.title, a.body
						FROM articles AS a
						WHERE a.type="quicktip" AND a.live=1
						ORDER BY RAND()';
			$this->_db->setQuery($query, 0, 1);
			$randomQuicktip = $this->_db->loadAssoc();
			if($quicktipSection = $this->getQuicktipSection($randomQuicktip['id'])) {
				$randomQuicktip['section'] = $quicktipSection;
			}
			$expiry = mktime(3, 0, 0, date('m') , date('d') + 1, date('Y')) - time(); // cache will expire at 3 o'clock in the morning next day
			$this->_cache->set($cacheKey, $randomQuicktip, $expiry);
		}
		return $randomQuicktip;
	}

	/**
	 *	Get random items
	 *
	 *	@access public
	 *  @param string type
	 *  @param int limit
	 *	@return array items
	 */
	public function getRandomItems($type, $limit = 3) {
		$cacheKey = 'randomItems_'.$type.'_'.$limit;
		$randomItems = $this->_cache->get($cacheKey);
		if ($randomItems === false) {
			switch($type) {
				case 'top_recipes':
					$query = 'SELECT a.id
								FROM articles AS a
								LEFT JOIN articleImages AS ai ON a.id=ai.articleId
								LEFT JOIN articleRatings AS ar ON a.id=ar.articleId
								WHERE a.type="recipe" AND a.live=1 AND (a.goLiveDate IS NULL OR a.goLiveDate<=NOW()) AND ai.articleId IS NOT NULL AND ar.rating>=4
								GROUP BY a.id
								ORDER BY RAND()';
					break;
				case 'featured_articles':
					$query = 'SELECT a.id
								FROM articles AS a
								LEFT JOIN articleImages AS ai ON a.id=ai.articleId
								WHERE a.featured = 1 AND a.live=1 AND (a.goLiveDate IS NULL OR a.goLiveDate<=NOW()) AND ai.articleId IS NOT NULL
								GROUP BY a.id
								ORDER BY RAND()';
					break;
				default:
					$this->_cache->set($cacheKey, false);
					return false;
			}
			$this->_db->setQuery($query, 0, $limit);
			$randomItems = $this->_db->loadAssocList();
			$expiry = mktime(3, 0, 0, date('m') , date('d') + 1, date('Y')) - time(); // cache will expire at 3 o'clock in the morning next day
			$this->_cache->set($cacheKey, $randomItems, $expiry);
		}
		return $randomItems;
	}

	/**
	 *	Get quicktip IDs in meta group
	 *
	 *	@access public
	 *	@return array Sections
	 */
	public function getQuicktipsByMetaGroup($metaGroup) {
		$cacheKey = 'quicktipsByMetaGroup_'.$metaGroup;
		$quicktipsByMetaGroup = $this->_cache->get($cacheKey);
		if ($quicktipsByMetaGroup === false) {
			$query = 'SELECT a.id AS quicktipId, lmv.name AS metaGroup
						FROM articles AS a
						INNER JOIN languageMetaGroups AS lmg ON lmg.slug="'.$this->_db->escape($metaGroup).'"
						INNER JOIN metaValues AS mv ON lmg.id=mv.groupId
						INNER JOIN languageMetaValues AS lmv ON mv.id=lmv.id
						INNER JOIN articleMetaValues AS amv ON a.id=amv.articleId AND amv.valueId=lmv.id AND amv.groupId=lmg.id
						WHERE a.type="quicktip" AND a.live=1
						ORDER BY lmv.name,a.id';
			$this->_db->setQuery($query);
			$quicktipsByMetaGroup = $this->_db->loadGroupedAssocList('metaGroup','quicktipId');
			$this->_cache->set($cacheKey, $quicktipsByMetaGroup);
		}
		return $quicktipsByMetaGroup;
	}

	/**
	 *	Flush quicktip cache
	 *
	 *	@access public
	 */
	public function flushQuicktips() {
		$this->_cache->delete('quicktips');
		$this->_cache->delete('quicktipsByMetaGroup_encyclopedia_of_tips_first_letters');
		$this->_cache->delete('quicktipsByMetaGroup_encyclopedia_of_tips_sections');
	}
	/** 
	 * @author by leon
	 * @return array
	 * @date 2011-10-13
	 * @desc To get the mappings of all the metaValues (categoreis for Recipe/Articles)
	 */
	public function getCorrectArticleGroupsMappings($inverse = false)
	{
		
		$metaMappings = array();
		$sql = "SELECT mg.id as mgid,mg.internalName as mgname,mg.type,mv.id as mvid,mv.groupId,mv.internalName as mvname FROM `metaGroups` as mg 
				 left join metaValues as mv on mg.id=mv.groupId 
				 WHERE mg.id in ('2','50','52','54')";
		$r = mysql_query($sql);
		while($t = mysql_fetch_array($r))
		{
			if(!$inverse)
			{
				$metaMappings[$t['mgid']][] = $t['mvid'];
			}else{
				$metaMappings[$t['mvid']] = $t['mgid'];
			}
			
		}
		return $metaMappings;
	}

	/** 
	 * @author by leon
	 * @return boolean
	 * @date 2011-10-13
	 * @save article categories
	 */
	public function saveArticleItem($selectedCategories,$id)
	{
		$hintTips = false;
		$aDishOfFun = false;
		$thinkingHealthy = false;
		$categories = $this->getCorrectArticleGroupsMappings(true);
		$insertSql = "INSERT INTO `articleMetaValues` (`articleId`, `groupId`, `valueId`, `rawValue`) VALUES 
					 ( '" . $id . "', '4',  '36', NULL ), ";
		foreach($selectedCategories as $k=>$v)
		{
			$insertSql .= " ( '" . $id . "',  '" . $categories[$v] . "',  '" . $v ."', NULL ), ";
			//if($categories[$v] == '50') $hintTips = true;
			//if($categories[$v] == '52') $aDishOfFun = true;
			//if($categories[$v] == '54') $thinkingHealthy = true;
		}
		//if($hintTips) $insertSql .=  " ( '" . $id . "', '2',  '128208', NULL ), ";
		//if($aDishOfFun) $insertSql .=  " ( '" . $id . "', '2',  '128302', NULL ), ";
		//if($thinkingHealthy) $insertSql .=  " ( '" . $id . "', '2',  '128338', NULL ), ";
		
		$insertSql = substr($insertSql, 0, -2) . ";";
		//echo $insertSql; exit;
		$r = mysql_query($insertSql);
		return $r;
	}
	
	/** 
	 * @author by leon
	 * @return array
	 * @date 2011-10-27
	 * @des add details information
	 */
	public function addItemInformation($items)
	{
		if(!is_array($items) || count($items)<1) return false;
		$itemStr = '';
		foreach($items as $item)
		{
			$itemStr .= "'" . $item . "', ";
		}
		$itemStr = substr($itemStr, 0, -2);
		$sql = "SELECT a.id as article_id,u.id as user_id,a.*,u.*,av.*,am.* FROM articles as a 
				 left join users as u on a.author=u.id
				 left join articleViews as av on av.articleId=a.id
				 left join articleImages as am on am.articleId=a.id
				 WHERE a.id in (" . $itemStr . ")";
		//echo $sql;
		$r = mysql_query($sql);
		$returnItem = array();
		while($row = mysql_fetch_array($r))
		{
			$returnItem[$row['article_id']]['link'] = $row['slug'] . 'htm';
			$returnItem[$row['article_id']]['title'] = $row['title'];
			$returnItem[$row['article_id']]['teaser'] = $row['teaser'];
			$returnItem[$row['article_id']]['author']['username'] = $row['username'];
			$returnItem[$row['article_id']]['views'] = $row['views'];
			$returnItem[$row['article_id']]['ratings'] = $row['rating'];
			$returnItem[$row['article_id']]['type'] = $row['type'];
			$returnItem[$row['article_id']]['thumbnail']['filename'] = $row['filename'];
			$returnItem[$row['article_id']]['thumbnail_alt'] = $row['alt'];
			$returnItem[$row['article_id']]['image'] = $row['filename'];
		}
		//exit;
		return $returnItem;
	}
    
    public function buildAllItems()
    {
        $liveItems = $this->_cache->get('items_live');
        foreach($liveItems as $k=>$itemId)
        {
            $this->getItem($itemId,null,true);
        }
    }
    /** 
     * @author by leon
     * @return array
     * @date 2011-10-13
     * @desc To get the mappings of all the metaValues (categoreis for Recipe/Articles)
     */
    public function getCorrectRecipeGroupsMappings($inverse = false)
    {
        
        $metaMappings = array();
        $sql = "SELECT mg.id as mgid,mg.internalName as mgname,mg.type,mv.id as mvid,mv.groupId,mv.internalName as mvname FROM `metaGroups` as mg 
                 left join metaValues as mv on mg.id=mv.groupId 
                 WHERE mg.id in ('4')";
        $r = mysql_query($sql);
        while($t = mysql_fetch_array($r))
        {
            if(!$inverse)
            {
                $metaMappings[$t['mgid']][] = $t['mvid'];
            }else{
                $metaMappings[$t['mvid']] = $t['mgid'];
            }
            
        }
        return $metaMappings;
    }    
    public function buildRecipeGroup()
    {
        $groupArray = $this->getCorrectRecipeGroupsMappings($inverse = false);
        return $groupArray; 
    }
    public function textSearch($search, $langCode = null)
    {
        $cacheKey = "searchTerm_" . md5(trim($search));
        $items = $this->_cache->get($cacheKey);
        if(!$items){
            $items = $this->_fullTextSearch($search, $langCode);
            $this->_cache->set($cacheKey, $items, 60 * 60 * 12, null, array('compress' => true)); 
        }
        return $items;
    }
    /**
    * @desc To fetch all the ids. The old system takes more than 200 seconds to run this script when millions of request come in
    * @author Leon Zhao
    * @return array $items
    */
    public function getAllIds($excludeCategory = array(),$forceRebuild = false)
    {
        $cacheKey = "All_Ids";
        if(DEBUG || SITEEND == "backend" ) $forceRebuild = true;
        $items = $forceRebuild ? false : $this->_cache->get($cacheKey);
	$items = false;
	
		$excludeSql = '';
		if(!empty($excludeCategory))
		{
			$excludeStr = implode(',',$excludeCategory);
			$excludeSql = 'WHERE a.id NOT IN(SELECT amv.articleId FROM articleMetaValues as amv WHERE amv.valueId IN ('.$excludeStr.'))';
		}
        if($items === false){
            $query = 'SELECT a.id
                FROM `articles` AS `a` '.$excludeSql;
            $this->_db->setQuery($query);
            $items = $this->_db->loadResultAssocArray('id', 'id');
            $this->_cache->set($cacheKey, $items, 60 * 60 * 1, null, array('compress' => true));
        }
        return $items;
    }
    
    /**
    * @desc search Extra to refine the search result
    */
    private function _searchRefine($items)
    {
     /**
        * @desc To refine the search result
        */ 
        $searchExtra = Request::getString('searchterm_extra');
        if($searchExtra && $searchExtra != '')
        {
            $searchExtra = trim($searchExtra);
            $searchExtraArray = explode(' ', $searchExtra);
            if(count($searchExtraArray) > 1){
                $searchString = implode("%' AND a.title like '%", $searchExtraArray);
                $searchString = " where a.title like '%" . $searchString . "%'";
            }else{
                $searchString = " where a.title like '%$searchExtra%'";
            }
            $sql = "SELECT a.id from articles as a $searchString";
            $this->_db->setQuery($sql);
            $refineItems = $this->_db->loadResultAssocArray('id', 'id');
            $return = array_intersect($items,$refineItems);
            return $return;
        }else{
            return $items;
        }  
    }
	
	public function getItemCategoriesId($itemId){
        // Get categories
		$metaModel = BluApplication::getModel('meta');
        $itemMetaGroups = $metaModel->getItemMetaGroups($itemId);
        $categories = array();
		
        foreach ($itemMetaGroups as $metaGroup) {
            if (isset($metaGroup['slug']) && $metaGroup['slug'] == 'top_levels'){
                continue;
            } 
            if (isset($metaGroup['excludeValues']) && $metaGroup['excludeValues'] == 'show_available'){ //hack to skip the categories choosed from USDA
                continue;
            } 
            if (isset($metaGroup['author']) && $metaGroup['slug'] == 'author'){ //hack to skip the author
                continue;
            }            
            if($metaGroup['values']){
                foreach ($metaGroup['values'] as $metaValue) {
                    if (!is_array($metaValue) || !$metaValue['display']){ 
                        continue;
                    }
					$categories[] = $metaValue['id'];
                    /*$categories[] = Array(
						'id'=>$metaValue['id'],
                        'parent' => $metaGroup['name'],
                        'link' => $metaValue['slug'],
                        'name' => $metaValue['name']
                    );*/
                }
            }
		}
		return $categories;
	}
	
	public function getReaderLovedItems($itemId = null,$relateItem=array(),$random = false,$limit=3)
	{
        /**
        * oh come on. It takes 0.5 seconds to load this every time. Damn it!
        * We have to cache it        * 
        * @var mixed
        */
        
        // Where to start? Let's setup the cacheId by the params
        
        //var_dump($itemId) .'|'. var_dump($relateItem) .'|'. var_dump($random) .'|'. var_dump($limit);   exit;
        
        $cid = isset($itemId)? $itemId:"noneItemId";
        $crelatedItem = isset($relateItem) ? "emptyrelated": implode('_',$relateItem);
        $crandom = $random? "random" : "falserandom";
        $cacheKey =  "readerLovedItems_" . $cid . "_" . $crelatedItem . "_" . $crandom . "_" . $limit;

        $items = $this->_cache->get($cacheKey);
        if ($items === false) {
		    $itemCategories = $this->getItemCategoriesId($itemId);
		    $catJoin = '';
		    $catIdWhere = '';
		    if(!empty($itemCategories))
		    {
			    $catJoin = ' LEFT JOIN articleMetaValues AS amv ON amv.articleId = a.id ';
			    $catIdWhere = ' AND amv.valueId IN ('.implode(',',$itemCategories).') ';
		    }
		    
		    $randomWhere = '';
		    if($random)
		    {
			    $randomWhere = ' ORDER BY RAND() ';
		    }
		    else
		    {
			    $randomWhere = ' ORDER BY a.date DESC ';
		    }
		    
		    $idWhere = '';
		    if(!empty($itemId))
		    {
			    $relateItem[] = $itemId;
		    }
		    
		    if(!empty($relateItem))
		    {
			    $idWhere = ' AND a.id NOT IN ('.implode(',',$relateItem).') ';
		    }
		    $query = 'SELECT a.id
						FROM `articles` AS `a`
						LEFT JOIN (
						SELECT ar.articleId, ROUND( AVG( ar.rating ) ) AS rating
						FROM articleRatings AS ar
						GROUP BY ar.articleId
						) AS arm ON arm.articleId = a.id
						LEFT JOIN `articleImages` AS `ai` ON ai.articleId = a.id '.$catJoin.
						' WHERE a.live =1
						AND ai.filename != ""
						AND arm.rating >=4 '.$idWhere.$catIdWhere.						
						' GROUP BY a.id '.$randomWhere;
		    //$query = 'SELECT a.id FROM `articles` AS `a` LEFT JOIN articleRatings AS ar ON ar.articleId = a.id LEFT JOIN `articleImages` AS `ai` ON ai.articleId = a.id '.$catJoin.' WHERE a.live=1 AND ai.filename != "" '.$idWhere.$catIdWhere.' GROUP BY a.id HAVING ROUND(AVG(ar.rating))>=4 '.$randomWhere;			
		    
		    $this->_db->setQuery($query, 0, $limit);
		    $items = $this->_db->loadAssocList('id');		
		    foreach($items as $key=>$item)
		    {
			    $items[$key] = $key;
		    }
		    if(!empty($items))
		    {
			    $this->addDetails($items);
		    }
            $this->_cache->set($cacheKey, $items);
        }
		return $items;
	}
    
    public function getSlideArticleByArticleId($articleId){
        $sql = "SELECT sa.id,sa.articleId,sa.sequence,sa.articleIdPage,sa.description FROM slidearticles as sa where sa.articleId=$articleId ORDER BY  sa.sequence";
        $this->_db->setQuery($sql);
        $items = $this->_db->loadResultAssocArray('articleIdPage','articleIdPage');
        $this->addDetails($items);
        return $items;
    }
    
    public function getSlideArticleByOrder($articleId,$order){
        $sql = "SELECT sa.id,sa.articleId,sa.sequence,sa.articleIdPage,sa.description from slidearticles as sa where sa.articleId=$articleId AND sa.sequence=$order";
        $this->_db->setQuery($sql);
        $result = $this->_db->loadAssoc('articleIdPage');
        if(isset($result['articleIdPage'])){
            $item_id = $result['articleIdPage'];
            $item[$item_id] = $item_id;
            $return = $this->addDetails($item);
            $item[$item_id]['slide_desc'] = $result['description'];
            return $item[$item_id];
        }else{
            return false; 
        }      
    }    

    public function getSlideOrderBySlidePageArticleId($articleId, $slideArticleId){
        $sql = "SELECT sa.id,sa.articleId,sa.sequence,sa.articleIdPage,sa.description FROM slidearticles as sa where sa.articleIdPage=$slideArticleId AND sa.articleId=$articleId";
        $this->_db->setQuery($sql);
        $result = $this->_db->loadAssoc('sequence');
        $order = $result["sequence"];
        return $order;
    }    
	
	public function getSlideArticleIdByArticleId($articleId){
        $sql = "SELECT sa.id,sa.articleId,sa.sequence,sa.articleIdPage,sa.description FROM slidearticles as sa where sa.articleId=$articleId ORDER BY  sa.sequence";
        $this->_db->setQuery($sql);
        $items = $this->_db->loadResultAssocArray('articleIdPage','articleIdPage');
		if(empty($items)) return array();
        return $items;
    }
	
	public function getSlideArticleDetailArticleId($articleId,$slideArticleId){
        $sql = "SELECT sa.id,sa.articleId,sa.sequence,sa.articleIdPage,sa.description FROM slidearticles as sa where sa.articleIdPage=$slideArticleId AND sa.articleId=$articleId";
        $this->_db->setQuery($sql);
        $item = $this->_db->loadAssoc();
        return $item;
    }
	
	public function addArticleSlideshow($itemId,$articleIdPage,$order = 0)
	{
		if (empty($itemId)) {
			return false;
		}
		
		// Query builder
		$params = array();

		if ($order == 0) {
			//Add the max order as default
			$order = $this->getMaxSlideshowOrder($itemId)+1;
		}
		else if(!is_null($order))
		{
			//Check if the order is already exist.
			$slideId = $this->checkArticleSlideshowOrder($itemId,$order);
			if(!empty($slideId))
			{
				return false;
			}
			$params['sequence'] = (int) $order;
		}
		
		$params['articleIdPage'] = (int)$articleIdPage;
		
		if (empty($params)) {
			return false;
		}
		$params['articleId'] = (int) $itemId;
		foreach ($params as $field => &$value) {
			$value = '`'.$field.'` = '.$value;
		}
		unset($value);
		// Execute
		$query = 'INSERT INTO `slidearticles`
			SET '.implode(', ', $params);
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		$insertId = $this->_db->getInsertID();
		
		$updateResult = $this->updateLastUsedDate($articleIdPage);
		
		// Return
		return $insertId;
	}
	
	public function updateLastUsedDate($contendId)
	{
		$query = "UPDATE articles as a SET a.lastUsed = NOW() WHERE a.id=".(int)$contendId;
		$this->_db->setQuery($query);
		$result = $this->_db->query();
		return $result;
	}
	
	public function getMaxSlideshowOrder($articleId)
	{
		$sql = "SELECT MAX(sa.sequence) as sequence from slidearticles as sa where sa.articleId=$articleId";
		$this->_db->setQuery($sql);
		$result = $this->_db->loadAssoc('sequence');
		return isset($result['sequence'])?$result['sequence']:0;
	}
	
	public function checkArticleSlideshowOrder($articleId,$order)
	{
		//Check the order if it is unique
		$sql = "SELECT articleIdPage from slidearticles as sa where sa.articleId=$articleId AND sa.sequence=$order";
        $this->_db->setQuery($sql);
        $result = $this->_db->loadAssoc('articleIdPage');	
		return $result;
	}
	
	public function checkArticleSlideshow($articleId,$articleIdPage)
	{
		//Check the order if it is unique
		$sql = "SELECT articleIdPage from slidearticles as sa where sa.articleId=$articleId and sa.articleIdPage=$articleIdPage";
        $this->_db->setQuery($sql);
        $result = $this->_db->loadAssoc('articleIdPage');	
		return $result;
	}
	
	public function updateArticleSlideshow($itemId, $articleIdPage, $description = null, $order = 1) {
		$query = 'UPDATE slidearticles
					SET description="'.$this->_db->escape($description).'",sequence='.(int)$order.'
					WHERE articleIdPage='.(int)$articleIdPage. ' AND articleId ='.(int)$itemId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	public function deleteArticleSlideshow($itemId, $articleIdPage) {
		$query = 'DELETE FROM slidearticles WHERE articleIdPage='.(int)$articleIdPage. ' AND articleId ='.(int)$itemId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	public function updateArticleSlideStatus($articleId,$status)
	{
		$query = 'UPDATE articles
					SET isslide='.(int)$status.' WHERE id='.(int)$articleId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}	
	
	public function logArticleImageActivities($userId,$articleId,$sourceImage,$finalImage,$imgSrc,$status,$errorMsg='')
	{
		// Execute
		$query = 'INSERT INTO `log_articleImages` (userId,articleId,sourceImageName,finalImageName,savedSrc,uploadDate,status,errorMsg) VALUES ('.(int)$userId.','.(int)$articleId.',"'.$sourceImage.'","'.$finalImage.'","'.$imgSrc.'",NOW(),"'.$status.'","'.$errorMsg.'")';			
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		return true;
	}
}

?>
