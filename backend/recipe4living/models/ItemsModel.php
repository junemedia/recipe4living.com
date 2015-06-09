<?php

/**
 *	Items model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendItemsModel extends ClientItemsModel
{
	/**
	 *	Set an item live.
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param bool Skip cache
	 *	@return bool Success
	 */
	public function setLive($itemId, $skipCache = false, $skipCacheCategoryBased = false)
	{
		// Edit DB
		$query = 'UPDATE `articles`
			SET `live` = 1
			WHERE `id` = '.(int) $itemId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		$user = $this->getAuthor($itemId);
		// Edit cache
		if (!$skipCache) {
			$this->flushItem($itemId);
			if (!$skipCacheCategoryBased) {
				$this->_cache->delete('items_live');
				
				$cacheModel = BluApplication::getModel('cache');
				$cacheModel->deleteEntriesLike('itemsTotal');
				$cacheModel->deleteEntriesLike('user_'.$user['author']);
				$cacheModel->deleteEntriesLike('items_quicksearch_');				
				$this->_cache->delete('recentlyAddedRecipes');
				$this->_cache->delete('items_types_live');
				$this->_cache->delete('items_imaged');
			}
		}
		
		// Return
		return true;
	}
	
	/**
	 *	Take an item offline.
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param bool Skip cache
	 *	@return bool Success
	 */
	public function unsetLive($itemId, $skipCache = false, $skipCacheCategoryBased = false)
	{
		// Edit DB
		$query = 'UPDATE `articles`
			SET `live` = 0
			WHERE `id` = '.(int) $itemId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		$user = $this->getAuthor($itemId);		
		// Edit cache
		if (!$skipCache) {
			$this->flushItem($itemId);
			if (!$skipCacheCategoryBased) {
				$this->_cache->delete('items_live');
				$this->_cache->delete('items_deleted');
				
				$cacheModel = BluApplication::getModel('cache');
				$cacheModel->deleteEntriesLike('itemsTotal');
				$cacheModel->deleteEntriesLike('user_'.$user['author']);
				$cacheModel->deleteEntriesLike('items_quicksearch_');				
				$this->_cache->delete('recentlyAddedRecipes');
				$this->_cache->delete('items_types_live');
				$this->_cache->delete('items_imaged');
			}
		}
		
		// Return
		return true;
	}
	
	/**
	 *	Set an item as featured
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param int Feature level
	 *	@param bool Skip cache
	 *	@return bool Success
	 */
	public function setFeatured($itemId, $featureLevel = 1, $skipCache = false)
	{
		// Edit DB
		$query = 'UPDATE `articles`
			SET `featured` = '.(int) $featureLevel.'
			WHERE `id` = '.(int) $itemId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Edit cache
		if (!$skipCache) {
			$this->flushItem($itemId);
			$this->_cache->delete('items_featured');
			$this->_cache->delete('items_sortable_featured');
		}
		
		// Return
		return true;
	}
	
	/**
	 *	Set an item as not featured
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param bool Skip cache
	 *	@return bool Success
	 */
	public function unsetFeatured($itemId, $skipCache = false)
	{
		return $this->setFeatured($itemId, 0,$skipCache);
	}
	
	/**
	 *	Set an item deleted.
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param bool Skip cache
	 *	@return bool Success
	 */
	public function setDeleted($itemId, $skipCache = false)
	{
		// Edit DB
		$query = 'UPDATE `articles`
			SET `live` = 2
			WHERE `id` = '.(int) $itemId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		$user = $this->getAuthor($itemId);
		// Edit cache
		if (!$skipCache) {
			$this->flushItem($itemId);
			$this->_cache->delete('items_deleted');
			
			$cacheModel = BluApplication::getModel('cache');
			$cacheModel->deleteEntriesLike('itemsTotal');
			$cacheModel->deleteEntriesLike('user_'.$user['author']);
			$cacheModel->deleteEntriesLike('items_quicksearch_');			
			$this->_cache->delete('recentlyAddedRecipes');
			$this->_cache->delete('items_types_live');
			$this->_cache->delete('items_imaged');
		}
		
		// Return
		return true;
	}
	
	/**
	 *	Set the author of an item.
	 *	Honestly don't know why you'd want to...
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param int User ID
	 *	@return bool Success
	 */
	public function setAuthor($itemId, $userId)
	{
		$query = 'UPDATE `articles`
			SET `author` = '.(int) $userId.'
			WHERE `id` = '.(int) $itemId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Get all comments
	 *
	 *	@access public
	 *	@param int Page
	 *	@param int Number of records per page
	 *	@param int Total number of all comments
	 *	@return array Comments
	 */
	public function getComments($page = 1, $limit = 10, &$total = NULL, $filterArray = NULL)
	{
		// Get models
		$userModel = BluApplication::getModel('user');
		
		// Prepare search criteria
		$where = array();
		$join_string = '';
		$having_string = '';
		if(isset($filterArray['date_from'])) {
			$where[] = 'UNIX_TIMESTAMP(`c`.`date`)>='. strtotime($filterArray['date_from']);
		}
		if(isset($filterArray['date_to'])) {
			$where[] = 'UNIX_TIMESTAMP(`c`.`date`)<'. (strtotime($filterArray['date_to']) + 60*60*24);
		}
		if(isset($filterArray['content'])) {
			$where[] = 'MATCH(`c`.`body`) AGAINST("'. Database::escape($filterArray['content']) .'")';
		}
		if(isset($filterArray['recipe'])) {
			$where[] = '`a`.`title` = "'. Database::escape($filterArray['recipe']) .'"';
			$join_string .= 'INNER JOIN `articles` AS `a` ON `c`.`objectType` = "article" AND `c`.`objectId` = `a`.`id`';
		}
		if(isset($filterArray['user'])) {
			$where[] = '`u`.`username` = "'. Database::escape($filterArray['user']) .'"';
			$join_string .= 'INNER JOIN `users` AS `u` ON `c`.`userId` = `u`.`id`';
		}
		if(isset($filterArray['flagged'])) {
			$having_string = 'COUNT(`r`.`objectId`)'. ($filterArray['flagged']==2 ? ' > ' : ' = ') .'0';
		}
		if(isset($filterArray['live'])) {
			$where[] = '`c`.`live` = '. ($filterArray['live'] == 2 ? '1' : '0');
		}
		if($where) {
			$where_string = ' AND ' . implode(' AND ', $where);
		}
		else {
			$where_string = '';
		}
	
		$query = 'SELECT `c`.`id`, `c`.`body`, `c`.`objectType`, `c`.`objectId`, `c`.`userId`, DATE_FORMAT(`c`.`date`, "%e/%b/%Y %r") AS `date`, `c`.`live`, SUM(`cr`.`rating`) AS `rating`, COUNT(`r`.`objectId`) AS `reportCount`, `c`.`ipaddr`
			FROM `comments` AS `c`
			LEFT JOIN `reports` AS `r` ON `c`.`id` = `r`.`objectId` AND `r`.`objectType` = "comment" AND status != "resolved"
			LEFT JOIN `commentRatings` AS `cr` ON `c`.`id` = `cr`.`commentId`
			'. $join_string .'
			WHERE `c`.`type` = "review" '. $where_string .'
			GROUP BY `c`.`id`, `r`.`objectId`
			'. ($having_string ? 'HAVING '.$having_string : '') .'
			ORDER BY `c`.`date` DESC';
		$this->_db->setQuery($query, ($page-1)*$limit, $limit, true);
		$comments = $this->_db->loadAssocList();
		$total = $this->_db->getFoundRows();
		
		foreach($comments as $id=>$comment) {

			$objectType = $comment['objectType'];
			switch($objectType) {
				case 'article': 
						// Get article details
						$object = $this->getItem($comment['objectId']);
						break;
			}
			$comments[$id][$objectType] = $object;
			
			// Get user details
			$user = $userModel->getUser($comment['userId']);
			$comments[$id]['user'] = $user;
			
		}
		
		// Return
		return $comments;
	}
	
	/**
	 *	Get comment details
	 *
	 *	@access public
	 *	@param int Comment ID
	 *	@return array Comment detailss
	 */
	public function getComment($commentId)
	{
		// Get models
		$userModel = BluApplication::getModel('user');

		$comment = $this->_getComment($commentId);
		// set article
		if($comment['objectType'] == 'article') {
			$comment['article'] = $this->getItem($comment['objectId']);
		}
		// set user
		$comment['user'] = $userModel->getUser($comment['userId']);

		// Return
		return $comment;
	}
	
	/**
	 *	Set comment status
	 *
	 *	@access public
	 *	@param int Comment ID
	 *	@param int Status (0 or 1)
	 *	@return bool Success
	 */
	public function setCommentStatus($commentId, $status)
	{
		// Edit DB
		$query = 'UPDATE `comments`
			SET `live` = ' . (int)$status . '
			WHERE `id` = '.(int) $commentId;
		$this->_db->setQuery($query);
		$success = $this->_db->query();
		
		// Clear cache
		$comment = $this->_getComment($commentId);
		$this->flushItemComments($comment['objectId']);
		
		// Return
		return $success;
	}

	/**
	 *	Set the ingredients for a recipe
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param array Ingredient meta values, grouped by meta group
	 *	@param bool Skip cache flush
	 *	@return bool Success
	 */
	public function setIngredients($itemId, $ingredients, $skipCache = false)
	{
		// Get ingredients groups
		$metaModel = BluApplication::getModel('meta');
		$ingredientMetaGroups = $metaModel->getIngredientMetaGroups();

		// Clear out existing ingredient-article mappings
		$query = 'DELETE FROM `articleMetaValues`
			WHERE `articleId` = '.(int) $itemId.'
				AND `groupId` IN ('.implode(', ', array_keys($ingredientMetaGroups)).')';
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}

		// Add new ones, skipping the cache
		$success = true;
		foreach ($ingredients as $groupId => $valueIds) {
			if (!$metaModel->addItemMetaValues($itemId, $groupId, $valueIds, $skipCache)) {
				$success = false;
			}
		}

		// Return
		return $success;
	}
	
	/**
	 *	Add a quicktip
	 *
	 *	@access public
	 *	@param string Title
	 *	@param string Body
	 *	@param string Teaser (to override Text::trim(body))
	 *	@param string Go Live Date
	 *	@param mixed Keywords (array or string)
	 *	@param string Description
	 *	@param string Slug
	 *	@param bool Go live
	 *	@return int Item ID
	 */
	public function addQuicktip($title, $body, $teaser = null, $goLiveDate = null, $keywords = null, $description = null, $slug = null, $live = true)
	{
		// Add the recipe
		if (!$itemId = $this->_addItem($title, null, $body, $teaser, $goLiveDate, $keywords, $description, $slug, 'quicktip', $live)) {
			return false;
		}
		
		// Return
		return $itemId;
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
	 *	Update related link
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param string URL
	 *	@param string Title
	 *	@param string Description
	 *	@param int Priority
	 *	@return bool Success
	 */
	public function updateLink($itemId, $linkId, $link, $title = null, $description = null, $priority = 0)
	{
		// Remove from database
		if (!parent::updateLink($itemId, $linkId, $link, $title, $description, $priority)) {
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
	public function deleteLink($itemId, $linkId)
	{
		// Remove from database
		if (!parent::deleteLink($itemId, $linkId)) {
			return false;
		}
		
		// Refresh cache
		$this->flushItemLinks($itemId);
		
		// Return
		return true;
	}
	
	/**
	 *	Gets the author ID of the article 
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@return $user
	 */
	public function getAuthor($itemId)
	{
		$query = 'SELECT `author` 
			FROM `articles` 
			WHERE `id` = '.(int) $itemId;
		$this->_db->setQuery($query);
		$user = $this->_db->loadAssoc();
		
		// Return
		return $user;
	}
}

?>
