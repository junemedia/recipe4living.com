<?php

/**
 * Permissions Model
 *
 * @package BluApplication
 * @subpackage BluModels
 */
class PermissionsModel extends BluModel
{
	/**
	 * Check if IP is banned
	 *
	 * @param string IP address
	 * @return bool True/false
	 */
	public function isBannedIP($ip)
	{
//		$query = 'SELECT * FROM ipblocking WHERE dcip1 <= "'.$ip.'" AND dcip2 >= "'.$ip.'"';
//		$this->_db->setQuery($query);
//		$this->_db->query();
//		return ($this->_db->getFoundRows() > 0);
return false;
	}
	
	/**
	 * Verify admin access
	 * 
	 * Uses HTTP basic auth, and potentially other stuff later.
	 * 
	 * @param string $controllerName name of the requested controller
	 * @param string $siteId Site ID
	 * @return bool True if allowed, false if not
	 */
	public function allowAdminAccess($controllerName = null, $siteId = null)
	{
		// Sometimes we need no permission at all
		$visitorIP = Request::getVisitorIPAddress();
		
		// Self
		if ($this->_isLocal($visitorIP)) {
			return true;
		}
		
		// Admin login
		if ($this->_isHttpAuthenticated()) {
			return true;
		}

		// Debug mode
		if ($this->_isDebug()) {
			//return true;
		}

		// Wrong answer.
		return false;
	}
	
	/**
	 *	Allow staging access
	 *
	 *	@access public
	 *	@return bool Allowed
	 */
	public function allowStagingAccess()
	{
		return $this->allowAdminAccess();
	}
	
	/**
	 *	Check if IP address is local
	 *
	 *	@access protected
	 *	@param string IP address
	 *	@return bool Local
	 */
	protected function _isLocal($ip)
	{
		if ($ip == '127.0.0.1') {
			//return true;
		}
		
		if ($ip == $_SERVER['SERVER_ADDR']) {
			//return true;
		}
		
		return false;
	}
	
	/**
	 *	Is HTTP authenticated?
	 *
	 *	@access public
	 *	@return bool Authenticated
	 */
	protected function _isHttpAuthenticated()
	{
		if (isset($_SERVER['PHP_AUTH_USER'])) {
			$username = strtolower($_SERVER['PHP_AUTH_USER']);
			$password = $_SERVER['PHP_AUTH_PW'];
			//echo $username . $password; exit;
			if ($this->_isAdmin($username, $password)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 *	Check admin credentials
	 *
	 *	@access protected
	 *	@param string Username
	 *	@param string Password
	 *	@return bool Success
	 */
	protected function _isAdmin($username, $password)
	{
		$admins = $this->_getSiteAdmins();
		if (isset($admins[$username])) {
			$userModel = BluApplication::getModel('user');
			
            //print_r($admins);exit;
            
			if ($admins[$username] == $userModel->hashPassword($username, $password)) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 *	Is debug
	 *	This may look silly, but it gets overriden.
	 *
	 *	@access protected
	 *	@return bool
	 */
	protected function _isDebug()
	{
		return DEBUG;
	}
	
	/**
	 *	Is Blubolt HQ
	 *
	 *	@access protected
	 *	@param string IP address
	 *	@return bool Blubolt IP
	 */
	protected function _isBluboltHQ($ip)
	{
		//return $ip == '87.80.43.97';
		return false;
	}
	
	/**
	 *	Is client IP address
	 *
	 *	@access protected
	 *	@param string IP address
	 *	@return bool Client IP
	 */
	protected function _isClientHQ($ip)
	{
		return false;
	}
	
	/**
	 *	Get administrator passwords
	 *
	 *	@access protected
	 *	@return array Usernames and passwords
	 */
	protected function _getSiteAdmins()
	{
		$lowerAdmins = $this->_cache->get('adminLogins');
        	//if(LEON_DEBUG)$lowerAdmins = false;
		if ($lowerAdmins === false) {
			
			$query = 'SELECT u.username, u.password
				FROM `users` AS `u`
				WHERE u.type = "admin"';
			$this->_db->setQuery($query);
			$admins = $this->_db->loadResultAssocArray('username', 'password');
            
            foreach($admins as $key=>$value){
                $lowerAdmins[strtolower($key)] = $value;
            }
			
			$this->_cache->set('adminLogins', $lowerAdmins);
		}
		return $lowerAdmins;
	}
	
	/**
	 *	Get HTTP authenticated user
	 *
	 *	@access public
	 *	@return array User
	 */
	public function getUser()
	{
		// Not authenticated
		if (!$this->_isHttpAuthenticated()) {
			return false;
		}
		
		// Get user
		$username = $_SERVER['PHP_AUTH_USER'];
		$userModel = BluApplication::getModel('user');
		if ((!$userId = $userModel->getUserId($username)) || (!$user = $userModel->getUser($userId))) {
			return false;
		}
		
		// Return
		return $user;
	}
}
