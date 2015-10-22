<?php

/**
 *	Workitmom Person model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendNewpersonModel extends ClientNewpersonModel
{
	/**
	 *	Enable listing deleted users.
	 *
	 *	Overrides WorkitmomNewpersonModel.
	 *
	 *	@param &array Query to update.
	 *	@param string Option key.
	 *	@param string Option value.
	 */
	protected function _parseGetPeopleOption(array &$query, $key, $value){
		switch($key){
			case 'order':
				switch($value){
					case 'date_terminated':
						$query['order'] = 'u.terminatedtime';
						break;
						
					default:
						parent::_parseGetPeopleOption($query, $key, $value);
						break;
				}
				break;
				
			case 'deleted':
				/* Ignore */
				if (!$value){
					break;
				}
				
				/* Take out anything regarding u.terminatedtime from WHERE clauses */
				foreach($query['where'] as $clause_key => $clause){
					
					/* Unrelated clause? */
					if (strpos($clause, 'u.terminatedtime') === false){
						continue;
					}
					
					/* Remove clause */
					unset($query['where'][$clause_key]);
					
				}
				
				/* Add new u.terminatedtime clause */
				$query['where'][] = 'u.terminatedtime > 0';
				break;
				
			default:
				/* Default to parent */
				parent::_parseGetPeopleOption($query, $key, $value);
				break;
		}
	}
	
	/**
	 *	Wipe a user from existence.
	 *
	 *	@access public
	 *	@param int User ID
	 *	@return bool Success
	 */
	public function burnUser($userId)
	{
		// User exists?
		$query = 'SELECT u.*
			FROM `users` AS `u`
			WHERE u.UserID = '.(int) $userId;
		$this->_db->setQuery($query);
		if (!$this->_db->loadResult()) {
			return true;
		}
		
		// Prepare
		$success = true;
		
		// Get content creator
		$query = 'SELECT cc.contentCreatorID
			FROM `contentCreators` AS `cc`
			WHERE cc.contentCreatoruserID = '.(int) $userId;
		$this->_db->setQuery($query);
		if ($contentCreatorId = $this->_db->loadResult()) {
			
			$query = 'SELECT a.articleID
				FROM `article`
				WHERE a.articleAuthor = '.(int) $contentCreatorId;
			$this->_db->setQuery($query);
			$articleIds = $this->_db->loadResultArray();
			foreach ($articleIds as $articleId) {
				
				$articleQueries = array();
				$articleQueries[] = 'DELETE FROM `articleCategory`
					WHERE `articleId` = '.(int) $articleId;
				$articleQueries[] = 'DELETE FROM `articleRatings`
					WHERE `articleId` = '.(int) $articleId;
				$articleQueries[] = 'DELETE FROM `articleTags`
					WHERE `articleId` = '.(int) $articleId;
				$articleQueries[] = 'DELETE FROM `xrefarticlealert`
					WHERE `articleID` = '.(int) $articleId;
				$articleQueries[] = 'DELETE FROM `xrefArticleRelatedlink`
					WHERE `articleID` = '.(int) $articleId;
				$articleQueries[] = 'DELETE FROM `xreflandingpagelinks`
					WHERE `landingPageID` = '.(int) $articleId;
				$articleQueries[] = 'DELETE FROM `xrefslideshowimages`
					WHERE `slideshowID` = '.(int) $articleId;
				$articleQueries[] = 'DELETE FROM `xrefusersubscread`
					WHERE `xarticleid` = '.(int) $articleId;
					
				foreach ($articleQueries as $query) {
					$this->_db->setQuery($query);
					if (!$this->_db->query()) {
						$success = false;
					}
				}
				if (!$success) {
					return false;
				}
				
				$query = 'DELETE FROM `article`
					WHERE `articleID` = '.(int) $articleId;
				$this->_db->setQuery($query);
				$success = $this->_db->query();
			}
			if (!$success) {
				return false;
			}
			
			$query = 'DELETE FROM `contentCreators`
				WHERE `contentCreatorID` = '.(int) $contentCreatorId;
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				return false;
			}
		}
		
		// Get groups todo
		// Get group topics todo
		
		// Get group posts
		$query = 'SELECT p.id
			FROM `groupPosts` AS `p`
			WHERE p.userId = '.(int) $userId;
		$this->_db->setQuery($query);
		$posts = $this->_db->loadResultArray();
		foreach ($posts as $postId) {
			$postQueries = array();
			$postQueries[] = 'DELETE FROM `groupPostReports`
				WHERE `postId` = '.(int) $postId;
			
			foreach ($postQueries as $query) {
				$this->_db->setQuery($query);
				if (!$this->_db->query()) {
					$success = false;
				}
			}
			if (!$success) {
				return false;
			}
			
			$query = 'DELETE FROM `groupPosts`
				WHERE `id` = '.(int) $postId;
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				$success = false;
			}
		}
		if (!$success) {
			return false;
		}
		
		// Get group photos
		$query = 'SELECT gph.groupPhotoID AS `id`, gph.groupPhotoName AS `file`
			FROM `groupPhotos` AS `gph`
			WHERE gph.userID = '.(int) $userId;
		$this->_db->setQuery($query);
		$photos = $this->_db->loadAssocList();
		foreach ($photos as $photo) {
			//unlink(ASSET);
			$query = 'DELETE FROM `groupPhotos`
				WHERE `groupPhotoID` = '.(int) $photo['id'];
			$this->_db->setQuery($query);
			$this->_db->query();
		}
		
		// Goodbye
		$queries = array();
		$queries[] = 'DELETE FROM `articleRatings`
			WHERE `articleRatingUser` = '.(int) $userId;
		$queries[] = 'DELETE FROM `contactrequests`
			WHERE `fromID` = '.(int) $userId.'
				OR `toID` = '.(int) $userId;
		$queries[] = 'DELETE FROM `groupInvites`
			WHERE `fromId` = '.(int) $userId.'
				OR `toId` = '.(int) $userId;
		$queries[] = 'DELETE FROM `groupPostReports`
			WHERE `userId` = '.(int) $userId;
		$queries[] = 'DELETE FROM `groupSubscriptions`
			WHERE `userId` = '.(int) $userId;
		$queries[] = 'DELETE FROM `groupTopicSubscriptions`
			WHERE `userId` = '.(int) $userId;
		$queries[] = 'DELETE FROM `messages`
			WHERE `fromID` = '.(int) $userId.'
				OR `toID` = '.(int) $userId;
		$queries[] = 'DELETE FROM `pollResponse`
			WHERE `pollResponseUserId` = '.(int) $userId;
		$queries[] = 'DELETE FROM `reports`
			WHERE `reporter` = '.(int) $userId;
		$queries[] = 'DELETE FROM `user_info`
			WHERE `userid` = '.(int) $userId;
		$queries[] = 'DELETE FROM `userAlertPrefs`
			WHERE `userID` = '.(int) $userId;
		$queries[] = 'DELETE FROM `userAlerts`
			WHERE `alertUser` = '.(int) $userId;
		$queries[] = 'DELETE FROM `userPrivacyPrefs`
			WHERE `userID` = '.(int) $userId;
		$queries[] = 'DELETE FROM `users_myday`
			WHERE `user_id` = '.(int) $userId;
		$queries[] = 'DELETE FROM `userSaves`
			WHERE `userID` = '.(int) $userId;
		$queries[] = 'DELETE FROM `userTags`
			WHERE `userId` = '.(int) $userId;
		$queries[] = 'DELETE FROM `userVariables`
			WHERE `userID` = '.(int) $userId;
		$queries[] = 'DELETE FROM `xrefuseralert`
			WHERE `userID` = '.(int) $userId;
		$queries[] = 'DELETE FROM `xrefusercontactor`
			WHERE `contactorID` = '.(int) $userId.'
				OR `userID` = '.(int) $userId;
		$queries[] = 'DELETE FROM `xrefusergroup`
			WHERE `userID` = '.(int) $userId;
		$queries[] = 'DELETE FROM `xrefusergrouppending`
			WHERE `userID` = '.(int) $userId;
		$queries[] = 'DELETE FROM `xrefuserseenstress`
			WHERE `ruserid` = '.(int) $userId.'
				OR `rstressed` = '.(int) $userId;
		$queries[] = 'DELETE FROM `xrefusersubscread`
			WHERE `xuserid` = '.(int) $userId;
		$queries[] = 'DELETE FROM `xrefusersubscribe`
			WHERE `xuserid` = '.(int) $userId.'
				OR `xauthor` = '.(int) $userId;
		
		foreach ($queries as $query) {
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				$success = false;
			}
		}
		if (!$success) {
			return false;
		}
		
		$query = 'DELETE FROM `users`
			WHERE `UserID` = '.(int) $userId;
		$this->_db->setQuery($query);
		$success = $this->_db->query();
		
		// Finish.
		return $success;
	}
}

?>