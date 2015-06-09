<?php

class facebook_account {
	
	/**
	 *	Add a user, along with their basic details
	 *
	 *	@access public
	 *	@param string $username Username
	 *	@param string $password The raw password (unhashed)
	 *	@param string $email Email
	 *	@param string $firstName First name
	 *	@param string $lastName Last name
	 *	@param bool Skip cache flushing
	 *	@return int|bool The ID of the newly created user, or false on failure
	 */
	public function addUser($username, $password, $email, $firstName, $lastName, $skipCache = false)
	{
		$visitorIP = trim($_SERVER['REMOTE_ADDR']);
		
		$query = 'SELECT u.* FROM `users` AS `u` WHERE u.username = "'.$this->_db->escape($username).'" OR u.email = "'.$this->_db->escape($email).'"';
		$this->_db->setQuery($query);
		if ($this->_db->loadAssoc()) {
			return false;
		}
		
		// Hash password
		$password = self::_hashPassword($username, $password);
		
		// Add base details
		$query = 'INSERT INTO `users`
			SET `type` = "member",
				`username` = "'.$this->_db->escape($username).'",
				`password` = "'.$this->_db->escape($password).'",
				`email` = "'.$this->_db->escape($email).'",
				`firstname` = "'.$this->_db->escape($firstName).'",
				`lastname` = "'.$this->_db->escape($lastName).'",
				`lastLoggedin` = NOW(),
				`deleted` = 0,
				`rating` = 0';
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		$userId = $this->_db->getInsertID();
		
		// Add extra info
		$query = 'INSERT INTO `userInfo`
			SET `userId` = '.(int) $userId.',
				`image` = "",
				`private` = 0,
				`joined` = NOW(),
				`about` = "",
				`favouriteFoods` = "",
				`ipaddr` = "'.$this->_db->escape($visitorIP).'"';
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Renew indices
		if (!$skipCache) {
			$cacheModel = BluApplication::getModel('cache');
			$cacheModel->deleteEntriesLike('users\_');
		}
		
		// Return
		return $userId;
	}
	
	
	
	
}





?>
