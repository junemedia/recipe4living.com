<?php

/**
 *	Items model
 *
 *	@package BluApplication
 *	@subpackage FrontendModels
 */
class ClientFrontendItemsModel extends ClientItemsModel
{
	/**
	 *	Get an item
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param string Language code
	 *	@param bool Refresh my memory
	 *	@return array
	 */
	public function getItem($itemId, $langCode = null, $refresh = false)
	{
		// Load from memory
		static $items = array();
		if ($refresh || !isset($items[$itemId])) {
			
			// Get standard item
			if (!$item = parent::getItem($itemId, $langCode, $refresh)) {
				return false;
			}

			// Get current logged in user's rating, for convenience.
			$userModel = BluApplication::getModel('user');
			$user = $userModel->getCurrentUser();
			$item['ratings']['currentUser'] = isset($item['ratings']['raw'][$user['id']]) ? $item['ratings']['raw'][$user['id']] : false;
			
			// Set permissions based on current user
			$item['canEdit'] = $user && ($item['author']['id'] == $user['id'] || $user['type'] == 'admin');
			if (!$item['canEdit']) {
				$permissionsModel = BluApplication::getModel('permissions');
				$item['canEdit'] = $permissionsModel->canEdit();
			}
			
			// Stored against user?
			$item['inRecipeBox'] = isset($user['saves']['recipebox'][$item['id']]);
			$item['recipe_note'] = isset($user['saves']['recipe_note'][$item['id']]['comment']) ? $user['saves']['recipe_note'][$item['id']]['comment'] : false;

			// In shopping list?
			$item['inShoppingList'] = false;
			$shoppingList = Session::get('shoppinglist');
			if (is_array($shoppingList)) {
				foreach ($shoppingList as $listItem) {
					if ($listItem['id'] == $item['id']) {
						$item['inShoppingList'] = true;
						break;
					}
				}
			}
			
			// Store in memory
			$items[$itemId] = $item;
		}
		return $items[$itemId];
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
		if (!$comments = parent::getItemComments($itemId, $forceRebuild)) {
			return false;
		}
		
		if (!empty($comments['raw'])) {
			
			// Get current user
			$userModel = BluApplication::getModel('user');
			$user = $userModel->getCurrentUser();
			
			// Set permissions
			foreach ($comments as $type => &$typeComments) {
				foreach ($typeComments as &$comment) {
					$comment['canDelete'] = $user && ($comment['userId'] === $user['id']);
				}
				unset($comment);
			}
			unset($typeComments);
		}
		
		return $comments;
	}
	
	/**
	 *	Append recipe meta details (i.e. ingredients, yield, preparation/cooking time)
	 *
	 *	@access public
	 *	@param array Item
	 *	@param bool Refresh
	 */
	public function buildRecipeMeta(&$item, $refresh = false)
	{
		if (!empty($item['meta'])) {
			
			// Get model
			$metaModel = BluApplication::getModel('meta');
			$itemMetaGroups = $metaModel->getItemMetaGroups($item['id'], $refresh);
			
			// Get yield
			$yieldGroupIds = array(
				'quantity' => $metaModel->getGroupIdBySlug('yield_quantities'),
				'measure' => $metaModel->getGroupIdBySlug('yield_measures')
			);
			$item['yield'] = array(
				'quantity' => false,
				'measure' => false
			);
			if (!empty($itemMetaGroups[$yieldGroupIds['quantity']]['values'])) {
				$item['yield']['quantity'] = reset($itemMetaGroups[$yieldGroupIds['quantity']]['values']);
			}
			if (!empty($itemMetaGroups[$yieldGroupIds['measure']]['values'])) {
				$measure = reset($itemMetaGroups[$yieldGroupIds['measure']]['values']);
				$item['yield']['measure'] = $measure;
			}
			
			// Get preparation time
			$preparationTimeGroupIds = array(
				'quantity' => $metaModel->getGroupIdBySlug('preparation_time_quantities'),
				'measure' => $metaModel->getGroupIdBySlug('preparation_time_measures')
			);
			$item['preparation_time'] = array(
				'quantity' => false,
				'measure' => false
			);
			if (!empty($itemMetaGroups[$preparationTimeGroupIds['quantity']]['values'])) {
				$item['preparation_time']['quantity'] = reset($itemMetaGroups[$preparationTimeGroupIds['quantity']]['values']);
			}
			if (!empty($itemMetaGroups[$preparationTimeGroupIds['measure']]['values'])) {
				$measure = reset($itemMetaGroups[$preparationTimeGroupIds['measure']]['values']);
				$item['preparation_time']['measure'] = $measure;
			}
			
			// Get cooking time
			$cookingTimeGroupIds = array(
				'quantity' => $metaModel->getGroupIdBySlug('cooking_time_quantities'),
				'measure' => $metaModel->getGroupIdBySlug('cooking_time_measures')
			);
			$item['cooking_time'] = array(
				'quantity' => false,
				'measure' => false
			);
			if (!empty($itemMetaGroups[$cookingTimeGroupIds['quantity']]['values'])) {
				$item['cooking_time']['quantity'] = reset($itemMetaGroups[$cookingTimeGroupIds['quantity']]['values']);
			}
			if (!empty($itemMetaGroups[$cookingTimeGroupIds['measure']]['values'])) {
				$measure = reset($itemMetaGroups[$cookingTimeGroupIds['measure']]['values']);
				$item['cooking_time']['measure'] = $measure;
			}
		}
	}
	
	/**
	 *	Add an article
	 *
	 *	@access public
	 *	@param string Title
	 *	@param int User ID of author
	 *	@param string Body
	 *	@param string Teaser (to override Text::trim(body))
	 *	@param string Go Live Date
	 *	@param mixed Keywords (array or string)
	 *	@param string Slug
	 *	@param bool Go live
	 *	@return int Item ID
	 */
	public function addArticle($title, $userId, $body, $teaser = null, $goLiveDate = null, $keywords = null, $description = null, $slug = null, $live = false,$video_js = null)
	{
		// Add the article
		if (!$itemId = $this->_addItem($title, $userId, $body, $teaser, $goLiveDate, $keywords, $description, $slug, 'article', $live,false,false,$video_js)) {
			return false;
		}
		
		// Return
		return $itemId;
	}
	
	/**
	 *	Add a question
	 *
	 *	@access public
	 *	@param string Title
	 *	@param int User ID of author
	 *	@param string Body
	 *	@param string Teaser (to override Text::trim(body))
	 *	@param string Go Live Date
	 *	@param mixed Keywords (array or string)
	 *	@param string Description
	 *	@param string Slug
	 *	@param bool Go live
	 *	@return int Item ID
	 */
	public function addQuestion($title, $userId, $body, $teaser = null, $goLiveDate = null, $keywords = null, $description = null, $slug = null, $live = false)
	{
		// Add the question
		if (!$itemId = $this->_addItem($title, $userId, $body, $teaser, $goLiveDate, $keywords, $description, $slug, 'question', $live)) {
			return false;
		}
		
		// Return
		return $itemId;
	}
	
	/**
	 *	Add a recipe
	 *
	 *	@access public
	 *	@param string Title
	 *	@param int User ID of author
	 *	@param string Body
	 *	@param string Teaser (to override Text::trim(body))
	 *	@param string Go Live Date
	 *	@param mixed Keywords (array or string)
	 *	@param string Description
	 *	@param string Slug
	 *	@param bool Go live
	 *	@return int Item ID
	 */
	public function addRecipe($title, $userId, $body, $teaser = null, $goLiveDate = null, $keywords = null, $description = null, $slug = null, $live = false,$video_js = null)
	{
		// Add the recipe
		if (!$itemId = $this->_addItem($title, $userId, $body, $teaser, $goLiveDate, $keywords, $description, $slug, 'recipe', $live,false,false,$video_js)) {
			return false;
		}
		
		// Return
		return $itemId;
	}
	
	/**
	 *	Add a Blog
	 *
	 *	@access public
	 *	@param string Title
	 *	@param int User ID of author
	 *	@param string Body
	 *	@param string Teaser (to override Text::trim(body))
	 *	@param string Go Live Date
	 *	@param mixed Keywords (array or string)
	 *	@param string Slug
	 *	@param bool Go live
	 *	@return int Item ID
	 */
	public function addBlog($title, $userId, $body, $teaser = null, $goLiveDate = null, $keywords = null, $description = null, $slug = null, $live = false)
	{
		// Add the article
		if (!$itemId = $this->_addItem($title, $userId, $body, $teaser, $goLiveDate, $keywords, $description, $slug, 'blog', $live)) {
			return false;
		}
		// Return
		return $itemId;
	}	
	
	/**
	 *	Increment view count.
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@return bool Success
	 */
	public function incrementViews($itemId)
	{
		$query = 'UPDATE `articleViews`
			SET `views` = `views` + 1,
				`date` = NOW()
			WHERE `articleId` = '.(int) $itemId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}

	/**
	 * Add item to list of recently viewed
	 *
	 * @param int Item details array
	 */
	public function addRecentItem($item)
	{
		// Store last 20 unique items in the order viewed
		$recentlyViewed = Session::get('recentlyViewed', array());
		$recentlyViewed[$item['id']] = $item['id'];
		$recentlyViewed = array_slice($recentlyViewed, 0, 20, true);
		Session::set('recentlyViewed', $recentlyViewed);
	}

	/**
	 *	Get recently viewed items list
	 *
	 *	@param int Offset
	 *	@param int Limit
	 *	@param int Item to omit from list
	 * @return array Array of item IDs
	 */
	public function getRecentItems($offset = 0, $limit = 3, $omit = null)
	{
		// Get list
		if (!$recentlyViewed = Session::get('recentlyViewed', false)) {
			return false;
		}
		
		// Remove omittal
		if ($omit) {
			unset($recentlyViewed[$omit]);
		}
		
		// Return last x item IDs
		$recentlyViewed = array_reverse($recentlyViewed);
		$recentlyViewed = array_slice($recentlyViewed, $offset, $limit, true);
		
		// Return
		return $recentlyViewed;
	}
	
	/**
	 *	Add a review
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param int User ID
	 *	@param string Text
	 *	@param bool Set live immediately
	 *	@return int Comment ID
	 */
	public function addReview($itemId, $userId, $text, $live = true)
	{
		return $this->_addComment('review', $itemId, $userId, $text, $live);
	}
	
	/**
	 *	Add a user rating
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param int User ID
	 *	@param int Rating (from 1 to 5)
	 *	@param bool Set live immediately
	 *	@return bool Success
	 */
	public function addRating($itemId, $userId, $rating, $live = true)
	{
		// Add rating
		$rating = max(1, min((int) $rating, 5));
		$query = 'INSERT INTO `articleRatings`
			SET `articleId` = '.(int) $itemId.',
				`userId` = '.(int) $userId.',
				`rating` = '.(int) $rating.',
				`date` = NOW()
			ON DUPLICATE KEY UPDATE
				`rating` = '.(int) $rating.',
				`date` = NOW()';
		$this->_db->setQuery($query);
		$updated = $this->_db->query();
		
		// Flush cache
		if ($live) {
			$this->flushItemRatings($itemId);
		}
		
		// Return
		return $updated;
	}
	
	/**
	 *	Add a user's comment rating
	 *
	 *	@access public
	 *	@param int Comment ID
	 *	@param int User ID
	 *	@param int Rating (from 1 to 5)
	 *	@param bool Set live immediately
	 *	@return bool Success
	 */
	public function addCommentRating($commentId, $userId, $rating, $live = true)
	{
		// Add rating
		$rating = max(1, min((int) $rating, 5));
		$query = 'INSERT INTO `commentRatings`
			SET `commentId` = '.(int) $commentId.',
				`userId` = '.(int) $userId.',
				`rating` = '.(int) $rating.',
				`date` = NOW()
			ON DUPLICATE KEY UPDATE
				`rating` = '.(int) $rating.',
				`date` = NOW()';
		$this->_db->setQuery($query);
		$updated = $this->_db->query();
		
		// Flush cache
		if ($live) {
			$this->flushItemComments($itemId);
		}
		
		// Return
		return $updated;
	}
	
	/**
	 *	Add a comment
	 *
	 *	@access protected
	 *	@param string Comment type
	 *	@param int Item ID
	 *	@param int User ID
	 *	@param string Text
	 *	@param bool Set live immediately
	 *	@return int Comment ID
	 */
	protected function _addComment($type, $itemId, $userId, $text, $live = true)
	{
		$visitorIP = Request::getVisitorIPAddress();
		// Add comment
		$query = 'INSERT INTO `comments`
			SET `type` = "'.$this->_db->escape($type).'",
				`body` = "'.$this->_db->escape($text).'",
				`objectType` = "article",
				`objectId` = '.(int) $itemId.',
				`userId` = '.(int) $userId.',
				`date` = NOW(),
				`ipaddr` = "'.$this->_db->escape($visitorIP).'",
				`live` = 0';//.(int) (bool) $live;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		$commentId = $this->_db->getInsertID();
		
		// Flush cache
		if ($live) {
			$this->flushItemComments($itemId);
		}
		
		//Send notice email
		$this->_sendEmail($this->_db->escape($text));
		
		// Return
		return $commentId;
	}
	
	/**
	 *	Send email to editor when a comment is reported
	 *
	 *	@access protected
	 *	@param string Message
	 */
	protected function _sendEmail($message)
	{
		$userModel = BluApplication::getModel('user');
		$user = $userModel->getCurrentUser();
		$current = BluApplication::getSetting("adminEmail");		
		$to = '';
		foreach($current as $val)
		{
			$to .= $val.',';
		}
		$to = substr($to,0,-1);
		$subject = "New comment reported by: ".$user['username'];
		$message = "Comment content:\r\n".$message;
		mail($to,$subject,$message);
	}
	
	/**
	 *	Add a vote to an item
	 *
	 *	Doesn't actually use User ID, but require it in case we ever want to do something sensible...
	 *
	 *	@deprecated Use self::addRating.
	 *	@access public
	 *	@param int Item ID
	 *	@param int User ID
	 *	@return bool Success
	 */
	public function addVote($itemId, $userId)
	{
		$query = 'UPDATE `articleVotes`
			SET `votes` = `votes` + 1
			WHERE `articleId` = '.(int) $itemId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Filter items by featured flag
	 *
	 *	@access public
	 *	@param array Items (Item IDs as key)
	 *	@param bool Return non-featured items
	 *	@return array Filtered items
	 */
	public function filterFeaturedItems($items, $invert = false)
	{
		// Get *all* featured items
		$featuredItems = $this->getFeaturedItems(null, null, false);
		
		// Return all?
		if (empty($featuredItems)) {
			return $invert ? array() : $items;
		}
		
		// Intersect (or diff) keys
		$items = $invert ? array_diff_key($items, $featuredItems) : array_intersect_key($items, $featuredItems);
		
		// Return
		return $items;
	}
	
	/**
	 *	Get featured items
	 *
	 *	@access public
	 *	@param int Offset
	 *	@param int Limit
	 *	@param bool Add details
	 *	@return array
	 */
	public function getFeaturedItems($offset = null, $limit = null, $addDetails = true)
	{
		// Get all featured items
		static $featuredItems;
		
		if (empty($featuredItems)) {
			$cacheKey = 'items_featured';
			$featuredItems = $this->_cache->get($cacheKey);
			if ($featuredItems === false) {
				$query = 'SELECT a.id
					FROM `articles` AS `a`
					WHERE a.featured > 0';
				$this->_db->setQuery($query);
				$featuredItems = $this->_db->loadResultAssocArray('id', 'id');
				$this->_cache->set($cacheKey, $featuredItems);
			}
		}
		
		// Slice accordingly
		$items = ($offset || $limit) ? array_slice($featuredItems, $offset, $limit, true) : $featuredItems;
		
		// Add details
		if ($addDetails) {
			$this->addDetails($items);
		}
		
		// Return
		return $items;
	}
	
	public function getRandomFeaturedRecipes($offset = null, $limit = null, $addDetails = true)
	{
		// Get all featured items
		$items = array();
		$query = 'SELECT a.id
			FROM `articles` AS `a`
			WHERE a.featured > 0 AND a.type="recipe" AND a.live=1 ORDER BY RAND()';
		$this->_db->setQuery($query);
		$featuredItems = $this->_db->loadResultAssocArray('id', 'id');
		
		// Slice accordingly
		$items = ($offset || $limit) ? array_slice($featuredItems, $offset, $limit, true) : $featuredItems;
		
		// Add details
		if ($addDetails) {
			$this->addDetails($items);
		}
		
		// Return
		return $items;
	}
	
	/**
	 *	Filter items by those with images
	 *
	 *	@access public
	 *	@param array Items (Item IDs as key)
	 *	@param bool Return items with no images
	 *	@return array Filtered Items
	 */
	public function filterImagedItems($items, $invert = false)
	{
		// Get all imaged items
		static $imagedItems;
		if (!isset($imagedItems)) {
			$cacheKey = 'items_imaged';
			$imagedItems = $this->_cache->get($cacheKey);
			
			if ($imagedItems === false) {
				$query = "SELECT ai.articleId
					FROM `articleImages` AS `ai`
					WHERE ai.filename != '' 
					GROUP BY ai.articleId ORDER BY ai.articleId";
				$this->_db->setQuery($query);
				$imagedItems = $this->_db->loadResultAssocArray('articleId', 'articleId');
				$this->_cache->set($cacheKey, $imagedItems, 60 * 60 * 24 /2);
			}
		}
		
		// Return all?
		if (empty($imagedItems)) {
			return $invert ? array() : $items;
		}
		
		// Intersect (or diff) keys
		$items = $invert ? array_diff_key($items, $imagedItems) : array_intersect_key($items, $imagedItems);
		
		// Return
		return $items;
	}
	
	/**
	 *	Get articles
	 *
	 *	@access public
	 *	@param int Offset
	 *	@param int Limit
	 *	@param bool Add details
	 *	@param string Sort
	 *	@return array
	 */
	public function getArticles($offset = null, $limit = null, $addDetails = true, $sort = null)
	{
		return $this->_getTypeItems('article', $offset, $limit, $addDetails, $sort);
	}
	
	/**
	 *	Get LIVE articles
	 *
	 *	@access public
	 *	@param int Offset
	 *	@param int Limit
	 *	@param bool Add details
	 *	@param string Sort
	 *	@return array
	 */
	public function getLiveArticles($offset = null, $limit = null, $addDetails = true, $sort = null)
	{
		return $this->_getTypeItems('article', $offset, $limit, $addDetails, $sort,true);
	}
	
	/**
	 *	Get recipes
	 *
	 *	@access public
	 *	@param int Offset
	 *	@param int Limit
	 *	@param bool Add details
	 *	@param string Sort
	 *	@return array
	 */
	public function getRecipes($offset = null, $limit = null, $addDetails = true, $sort = null, $liveOnly = false)
	{
		return $this->_getTypeItems('recipe', $offset, $limit, $addDetails, $sort, $liveOnly);
	}

	public function getRecentRecipes() {

		$cacheKey = 'recentlyAddedRecipes';
		$recentlyAddedRecipes = $this->_cache->get($cacheKey);
		
		if ($recentlyAddedRecipes === false) {
			$recentlyAddedRecipes = $this->getRecipes(null, null, false, 'date_desc', true);
			$recentlyAddedRecipes = $this->filterImagedItems($recentlyAddedRecipes);
			$recentlyAddedRecipes = array_slice($recentlyAddedRecipes, 0, 30);
			$this->addDetails($recentlyAddedRecipes);
			$this->_cache->set($cacheKey, $recentlyAddedRecipes, 60 * 60 * 24 /2);
		}
		return $recentlyAddedRecipes;
	}
	
	/**
	 *	Get member questions
	 *
	 *	@access public
	 *	@param int Offset
	 *	@param int Limit
	 *	@param bool Add details
	 *	@param string Sort
	 *	@return array
	 */
	public function getQuestions($offset = null, $limit = null, $addDetails = true, $sort = null)
	{
		return $this->_getTypeItems('question', $offset, $limit, $addDetails, $sort);
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
		// Remove from database
		if (!parent::addImage($itemId, $filename, $title, $description, $miniDescription, $priority, $type, $userId, $alt)) {
			return false;
		}
		
		// Refresh cache
		$this->flushItemImages($itemId);
		
		// Return
		return true;
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
		// Remove from database
		if (!parent::deleteImage($itemId, $imageName)) {
			return false;
		}
		
		// Refresh cache
		$this->flushItemImages($itemId);
		
		// Return
		return true;
	}
	
	public function setImage($itemId, $filename, $type) {
		$query = 'UPDATE articleImages SET type="'.$this->_db->escape($type).'" WHERE filename="'.$this->_db->escape($filename).'" AND articleId='.(int)$itemId;
		$this->_db->setQuery($query);
		if(!$this->_db->query()) {
			return false;
		}
		$this->flushItemImages($itemId);
		return true;
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
		// Remove from database
		if (!parent::addLink($itemId, $link, $title, $description, $priority)) {
			return false;
		}
		
		// Refresh cache
		$this->flushItemLinks($itemId);
		
		// Return
		return true;
	}
	
	/**
	 *	Remove a related link
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param string URL
	 *	@return bool Success
	 */
	public function deleteLink($itemId, $link)
	{
		// Remove from database
		if (!parent::deleteLink($itemId, $link)) {
			return false;
		}
		
		// Refresh cache
		$this->flushItemLinks($itemId);
		
		// Return
		return true;
	}
	
	/**
	 *	Get a review's item ID
	 *
	 *	@deprecated
	 *	@access public
	 *	@param int Review ID
	 *	@return int Item ID
	 */
	private function _getReviewItemId($reviewId)
	{
		return $this->getCommentItemId($reviewId);
	}
	
	/**
	 *	Get number of available items
	 *
	 *	@access public
	 *	@param string Item type
	 *	@return int
	 */
	public function getTotal($type = null)
	{
		// Get from cache
		$cacheKey = 'itemsTotal'.($type ? '_'.$type : '');
		$total = $this->_cache->get($cacheKey);
		if ($total === false) {
			
			// Get items from database
			$items = $this->getItems();
			$items = $this->filterLiveItems($items);
			if ($type) {
				$items = $this->filterTypeItems($items, $type);
			}
			
			// Get count
			$total = count($items);
			
			// Store in cache
			$this->_cache->set($cacheKey, $total, 1800);
		}
		
		// Return
		return $total;
	}
	
	/**
	 *	Set yield for a recipe
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param string Yield value
	 *	@param string Yield measure
	 *	@param bool Flush cache
	 *	@return bool Success
	 */
	public function setRecipeYield($itemId, $yieldQuantity, $yieldMeasure, $flushCache = true)
	{
		// Get meta groups details
		$metaModel = BluApplication::getModel('meta');
		$yieldQuantityGroupId = $metaModel->getGroupIdBySlug('yield_quantities');
		$yieldMeasureGroupId = $metaModel->getGroupIdBySlug('yield_measures');
		
		// Remove existing data
		$metaModel->deleteItemMetaValues($itemId, $yieldQuantityGroupId, true);
		$metaModel->deleteItemMetaValues($itemId, $yieldMeasureGroupId, true);
		
		// Add the one we want to the recipe
		$metaModel->addItemMetaValues($itemId, $yieldQuantityGroupId, $yieldQuantity, !$flushCache);
		$metaModel->addItemMetaValues($itemId, $yieldMeasureGroupId, $yieldMeasure, !$flushCache);
		
		// Return
		return true;
	}
	
	/**
	 *	Add an ingredient to a recipe
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param string Ingredient name
	 *	@return success
	 */
	public function assignRecipeIngredient($itemId, $ingredient)
	{
		// Get model
		$metaModel = BluApplication::getModel('meta');
		
		// Get ingredients group ID
		$ingredientsGroupId = $metaModel->getGroupIdBySlug('ingredients');
		
		// Assign ingredient to recipe
		return $metaModel->addItemMetaValues($itemId, $ingredientsGroupId, $ingredient);
	}
	
	/**
	 *	Unassign all ingredients that are assigned to a recipe
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param bool Skip cache flushing
	 *	@return bool Success
	 */
	public function deleteRecipeIngredients($itemId, $skipCache = false)
	{
		// Get model
		$metaModel = BluApplication::getModel('meta');
		
		// Get ingredients group ID
		$ingredientsGroupId = $metaModel->getGroupIdBySlug('ingredients');
		
		// Delete ingredients
		return $metaModel->deleteItemMetaValues($itemId, $ingredientsGroupId, $skipCache);
	}
	
	/**
	 *	Set the preparation time for a recipe
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param string Timing value
	 *	@param string Timing measure
	 *	@param bool Flush cache
	 *	@return bool Success
	 */
	public function setRecipePreparationTime($itemId, $preparationTimeQuantity, $preparationTimeMeasure, $flushCache = true)
	{
		return $this->_setRecipeTiming($itemId, 'preparation', $preparationTimeQuantity, $preparationTimeMeasure, $flushCache);
	}
	
	/**
	 *	Set the cooking time for a recipe
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param string Timing value
	 *	@param string Timing measure
	 *	@param bool Flush cache
	 *	@return bool Success
	 */
	public function setRecipeCookingTime($itemId, $cookingTimeQuantity, $cookingTimeMeasure, $flushCache = true)
	{
		return $this->_setRecipeTiming($itemId, 'cooking', $cookingTimeQuantity, $cookingTimeMeasure, $flushCache);
	}
	
	/**
	 *	Set timing for a recipe
	 *
	 *	@access protected
	 *	@param int Item ID
	 *	@param string Timing type. Supports 'preparation' or 'cooking'.
	 *	@param string Timing value
	 *	@param string Timing measure
	 *	@param bool Flush cache
	 *	@return bool Success
	 */
	protected function _setRecipeTiming($itemId, $timingType, $timingQuantity, $timingMeasure, $flushCache = true)
	{
		// Get model
		$metaModel = BluApplication::getModel('meta');
		
		// Which timing type are we using?
		switch ($timingType) {
			case 'preparation':
				$quantityGroupId = $metaModel->getGroupIdBySlug('preparation_time_quantities');
				$measureGroupId = $metaModel->getGroupIdBySlug('preparation_time_measures');
				break;
				
			case 'cooking':
				$quantityGroupId = $metaModel->getGroupIdBySlug('cooking_time_quantities');
				$measureGroupId = $metaModel->getGroupIdBySlug('cooking_time_measures');
				break;
				
			default:
				return false;
		}
		
		// Remove existing data
		$metaModel->deleteItemMetaValues($itemId, $quantityGroupId, true);
		$metaModel->deleteItemMetaValues($itemId, $measureGroupId, true);
		
		// Assign timing to recipe
		$metaModel->addItemMetaValues($itemId, $quantityGroupId, $timingQuantity, !$flushCache);
		$metaModel->addItemMetaValues($itemId, $measureGroupId, $timingMeasure, !$flushCache);
		
		// Return
		return true;
	}
	
	/**
	 *	Assign an item to a category
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param string Category slug (meta value slug)
	 *	@return bool Success
	 */
	public function assignItemCategory($itemId, $categorySlug)
	{
		// Assume category we're adding exists as a meta value.
		$metaModel = BluApplication::getModel('meta');
		$categoryId = $metaModel->getValueIdBySlug($categorySlug);
		
		// Get the group holding the meta value
		$valueGroupMapping = $metaModel->getMetaValueGroupMapping();
		$categoryGroupId = $valueGroupMapping[$categoryId];
		
		// Assign to item
		return $metaModel->addItemMetaValues($itemId, $categoryGroupId, $categoryId);
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
		// Edit database and cache
		if (!parent::editItem($itemId, $title, $body, $teaser, $goLiveDate, $keywords, $description, $slug, $userId,$video_js)) {
			return false;
		}
		
		// Flush memory
		$this->getItem($itemId, null, true);
		
		// Return
		return true;
	}
	
	/**
	 *	Refresh all of an item
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@return bool
	 */
	public function flushItem($itemId)
	{
		return parent::flushItem($itemId) && $this->getItem($itemId, null, true);
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
		return parent::flushItemComments($itemId) && $this->getItem($itemId, null, true);
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
		return parent::flushItemRatings($itemId) && $this->getItem($itemId, null, true);
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
		return parent::flushItemMeta($itemId) && $this->getItem($itemId, null, true);
	}
	
	/**
	 *	Get related article
	 *
	 *	@access public
	 *	@param int Article ID
	 *	@return array Related articles
	 */
	public function getRelatedArticles($articleId)
	{
		// Select
		$query = 'SELECT `relatedArticleId` 
				FROM `articleRelationships`
				WHERE `articleId` = '.(int) $articleId.'
				ORDER BY `sequence`';
		$this->_db->setQuery($query);
		$relatedArticleIds = $this->_db->loadResultAssocArray('relatedArticleId', 'relatedArticleId');
		$relatedArticles = $relatedArticleIds;

		// Return
		return $relatedArticles;
	}
	
	/**
	 *	Add related article to another article
	 *
	 *	@access public
	 *	@param int Article ID
	 *	@param array Related Article ID
	 *	@return bool Success
	 */
	public function addRelatedArticle($articleId, $relatedArticleId, $sequence=NULL)
	{
		// get sequence
		if(!$sequence) {
			$query = 'SELECT COUNT(*) 
						FROM `articleRelationships` 
						WHERE `articleId` = '.(int) $articleId .' AND `relatedArticleId` != '.(int) $relatedArticleId;
			$this->_db->setQuery($query);
			$sequence = 1 + $this->_db->loadResult();
		}
		
		// Insert
		$query = 'REPLACE INTO `articleRelationships`
				SET `articleId` = '.(int) $articleId.',
					`relatedArticleId` = '.(int) $relatedArticleId.',
					`sequence` = '.(int) $sequence;
		$this->_db->setQuery($query);
		$updated = $this->_db->query();
		
		// flush cache only for this item...
		$this->getItem($articleId, null, true);
		
		// Return
		return $updated;
	}
	
	/**
	 *	Delete related article
	 *
	 *	@access public
	 *	@param int Article ID
	 *	@param int Related articles
	 *	@return bool Success
	 */
	public function deleteRelatedArticle($articleId,$relatedArticleId)
	{
		// get sequence
		$query = 'SELECT COUNT(*) 
					FROM `articleRelationships` 
					WHERE `articleId` = '.(int) $articleId .' AND `relatedArticleId` = '.(int) $relatedArticleId;
		$this->_db->setQuery($query);
		$sequence = $this->_db->loadResult();
		
		// Delete
		$query = 'DELETE FROM `articleRelationships`
				WHERE `articleId` = '.(int) $articleId .' AND `relatedArticleId` = '.(int) $relatedArticleId;
		$this->_db->setQuery($query);
		$deleted = $this->_db->query();
		
		// Update sequence
		$query = 'UPDATE `articleRelationships`
				SET `sequence` = `sequence` -1
				WHERE `sequence` > '. (int)$sequence;
		$this->_db->setQuery($query);
		$updated = $this->_db->query();
		
		// Return
		$success = ($deleted && $updated);
		return $success;
	}
	
	/**
	 *	Get itemgroup
	 *
	 *	@access public
	 *	@param int Itemgroup ID
	 *	@return array Itemgroup details
	 */
	public function getItemGroup($itemGroupId)
	{
		// Get itemgroup
		if (!$itemGroup = parent::getItemGroup($itemGroupId)) {
			return false;
		}
		
		// Append user-specific data
		$itemGroup['canEdit'] = false;
		if ($itemGroup['type'] == 'cookbook') {
			$itemGroup['comment'] = false;
		}
		
		$userModel = BluApplication::getModel('user');
		if ($user = $userModel->getCurrentUser()) {	
			
			// Admins can edit anything they want
			if ($user['type'] == 'admin') {
				$itemGroup['canEdit'] = true;
				
			// Author can edit
			} else if ($user['id'] == $itemGroup['author']['id']) {
				$itemGroup['canEdit'] = true;
			}
			
			// Comments (if saved against user)
			if ($itemGroup['type'] == 'cookbook') {
				if (isset($user['saves']['cookbook'][$itemGroup['id']])) {
					$itemGroup['comment'] = $user['saves']['cookbook'][$itemGroup['id']]['comment'];
				}
			}
		}
		
		// Return
		return $itemGroup;
	}
	
	/**
	 *	Get all articles for alphabetical list
	 *
	 *	@access public
	 *	@param stirng First letter or character group
	 *	@return array articles
	 */
	public function getAllArticles($letter = null, $type = null) {
		$cacheKey = 'all_articles'.($letter ? '_'.$letter : '').($type ? '_'.$type : '');
		$articles = $this->_cache->get($cacheKey);
		if($articles === false) {
			$where = '';
			if($letter) {
				if(preg_match('/^[A-Z]{1}$/i',$letter)) { // letters A - Z
					$where = 'WHERE title LIKE "'.$this->_db->escape($letter).'%"';
				}
				elseif($letter == 'numeral') { // numerals 0 - 9
					$where = 'WHERE title RLIKE "^[0-9]"';
				}
				else { // other characters
					$where = 'WHERE title RLIKE "^[^a-zA-Z0-9]"';
				}
			}
			if($type){
				$where .= ($where == '')? '':' AND ';
				$where .= 'type = "'.$type.'" ';
			}
			$query = 'SELECT title, type, slug, SUBSTR(title,1,1) REGEXP "[a-zA-Z0-9]" AS isAlphaNumeric
					FROM articles 
					'.$where.'
					AND live=1 ORDER BY isAlphaNumeric, title';
			$this->_db->setQuery($query);
			$articles = $this->_db->loadAssocList();
			$expiry = mktime(6, 0, 0, date('m') , date('d') + 1, date('Y')) - time(); // cache will expire at 6 o'clock in the morning next day
			$this->_cache->set($cacheKey,gzcompress(serialize($articles)),$expiry);
		}
		else {
			$articles = unserialize(gzuncompress($articles));
		}
		return $articles;
	}
	
	/**
	 *	Get all article images
	 *
	 *	@access public
	 *	@param int Item ID
	 *  @param int total number of images (passed by ref)
	 *  @param int offset 
	 *  @param int limit 
	 *	@return array articles
	 */
	public function getGalleryImages($itemId, &$total = null, $offset = null, $limit = null) {
		$query = 'SELECT SQL_CALC_FOUND_ROWS type,filename 
					FROM articleImages 
					WHERE articleId = '.(int)$itemId.' AND type = "gallery"
					ORDER BY sequence';
		$this->_db->setQuery($query, $offset, $limit);
		$galleryImages = $this->_db->loadAssocList();
		$total = $this->_db->getFoundRows();
		return $galleryImages;
	}
	
	/**
	 *	Get all quicktips
	 *
	 *	@access public
	 *	@return array Quicktips
	 */
	public function getQuicktips() {
		$cacheKey = 'quicktips';
		$quicktips = $this->_cache->get($cacheKey);
		if ($quicktips === false) {
			$query = 'SELECT a.id, a.title, a.body 
						FROM articles AS a
						WHERE a.type="quicktip" AND a.live=1
						ORDER BY a.id';
			$this->_db->setQuery($query);
			$quicktips = $this->_db->loadAssocList('id');
			$this->_cache->set($cacheKey, $quicktips);
		}
		return $quicktips;
	}
	
	/**
	 *	Get count of recipes added during the last 7 days
	 *
	 *	@access public
	 *	@return array Quicktips
	 */
	public function getLatestRecipeCount() {
		$cacheKey = 'latestRecipeCount';
		$latestRecipeCount = $this->_cache->get($cacheKey);
		if ($latestRecipeCount === false) {
			$query = 'SELECT COUNT(*)
						FROM articles AS a
						WHERE a.type="recipe" AND a.live=1 AND ((a.goLiveDate IS NULL AND DATEDIFF(NOW(),date)<=7) OR (a.goLiveDate<=NOW() AND DATEDIFF(NOW(),goLiveDate)<=7))';
			$this->_db->setQuery($query);
			$latestRecipeCount = $this->_db->loadResult();
			$expiry = mktime(6, 0, 0, date('m') , date('d') + 1, date('Y')) - time(); // cache will expire at 6 o'clock in the morning next day
			$this->_cache->set($cacheKey,$latestRecipeCount,$expiry);
		}
		return $latestRecipeCount;
	}
    
    public function getSurveyItemsById($surveyId){
        $cacheKey = "survey_" . $surveyId;
        $surveyItem = $this->_cache->get($cacheKey);
        if(LEON_DEBUG) $surveyItem = false;
        if($surveyItem === false){
            $query = "SELECT s.itemsId FROM `survey` as s WHERE s.articleId = $surveyId LIMIT 0 , 1";
            //echo $query;
            $this->_db->setQuery($query);
            $surveyItem = $this->_db->loadResult();
            $surveyItem = explode(',',$surveyItem);
            $expiry = mktime(6, 0, 0, date('m') , date('d') + 1, date('Y')) - time(); // cache will expire at 6 o'clock in the morning next day
            $this->_cache->set($cacheKey,$surveyItem,$expiry);
        }
        return $surveyItem;
    }
    
    public function saveSurveyVote($recipeId, $articleId){
        $ip = $_SERVER['REMOTE_ADDR'];
        $query = "INSERT INTO `survey_vote` (
        `id`, `articleId`, `voteId`, `ipaddress`) VALUES (
        NULL , '$articleId', '$recipeId', '$ip');";
        $r = mysql_query($query);
        return $r;
    }
    
    public function getSurveyResult($articleId){
        $query = "SELECT sv.voteId, count( * ) AS voteNumber FROM `survey_vote` AS sv WHERE `articleId` = $articleId GROUP BY `voteId` ";
        $this->_db->setQuery($query);
        $result = $this->_db->loadResultAssocArray('voteId', 'voteNumber');
        return $result;
    }
	
}

?>
