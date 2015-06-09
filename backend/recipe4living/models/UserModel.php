<?php

/**
 *	User model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendUserModel extends ClientUserModel
{
	/**
	 *	Ban/delete a user
	 *
	 *	@access public
	 *	@param int User ID
	 *	@return bool Success
	 */
	public function deleteUser($userId)
	{
		// Delete from database
		$query = 'UPDATE `users`
			SET `deleted` = 1
			WHERE `id` = '.(int) $userId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Return
		return $true;
	}
	
	/**
	 *	Set user type (thereby privileges)
	 *
	 *	@access public
	 *	@param int User ID
	 *	@param string User type
	 *	@return bool
	 */
	public function setPrivileges($userId, $type)
	{
		// Update database
		$query = 'UPDATE `users`
			SET `type` = "'.$this->_db->escape($type).'"
			WHERE `id` = '.(int) $userId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Clear admin login cache, so the user can log in
		$this->_cache->delete('adminLogins');
		$this->_cache->delete('user_'.$userId);

		// Return
		return true;
	}
	
	/**
	 *	Get all users
	 *
	 *	@access public
	 *	@param int Page
	 *	@param int Number of records per page
	 *	@param int Total number of all users
	 *	@return array Users
	 */
	public function getUsers($offset = null, $limit = null, $sort = null, $filterArray = null, &$total = null)
	{
		// Prepare search criteria
		$where = array();
		if(isset($filterArray['username'])) {
			$where[] = '`u`.`username` LIKE "%'. Database::escape(str_replace('_', '\_', str_replace('%', '\%', $filterArray['username']))) .'%"';
		}
		if(isset($filterArray['email'])) {
			$where[] = '`u`.`email` LIKE "%'. Database::escape(str_replace('_', '\_', str_replace('%', '\%', $filterArray['email']))) .'%"';
		}
		if(isset($filterArray['fullName'])) {
			$where[] = 'MATCH(`u`.`username`,`u`.`firstname`,`u`.`lastname`,`u`.`displayname`) AGAINST ("'. Database::escape($filterArray['fullName']) .'")';
		}
		if(isset($filterArray['live'])) {
			$where[] = '`u`.`deleted` = '. ($filterArray['live'] == 2 ? '0' : '1');
		}
		if($where) {
			$where_string = 'WHERE ' . implode(' AND ', $where);
		}
		else {
			$where_string = '';
		}
	
		$query = 'SELECT `u`.`id`, `u`.`username`, `u`.`email`, `u`.`firstname`, `u`.`lastname`, `u`.`location`, `u`.`deleted`, `u`.`deleted`, `u`.`type`, `u`.`lastLoggedin`,  `ui`.`ipaddr`
			FROM `users` AS `u`
			INNER JOIN userInfo as `ui` 
			ON ui.userid=u.id
			'. $where_string .'
			ORDER BY `u`.`username`';
		$this->_db->setQuery($query, $offset, $limit, true);
		$users = $this->_db->loadAssocList();
		$total = $this->_db->getFoundRows();
		
		// Return
		return $users;
	}
	
	/**
	 *	Set user status
	 *
	 *	@access public
	 *	@param int User ID
	 *	@param int Status (0 or 1)
	 *	@param string Reason for deletion
	 *	@return bool Success
	 */
	public function setUserStatus($userId, $status, $deleteReason = NULL)
	{
		// Edit DB
		$query = 'UPDATE `users`
			SET `deleted` = ' . (int)$status . ',
			`deleteReason` = '.($deleteReason ? ' "' . Database::escape($deleteReason) . '"' : 'NULL').'
			WHERE `id` = '.(int) $userId;
		$this->_db->setQuery($query);
		$success = $this->_db->query();
		
		// Clear cache
		$this->_cache->delete('user_'.$userId);
		
		// Return
		return $success;
	}
	
	/**
	 *	Set a user as logged in
	 *
	 *	@access public
	 *	@param string Username
	 *	@return bool Success
	 */
	public function login($username)
	{
		$userId = $this->getUserId($username);
		return $this->_login($userId);
	}
}

?>
