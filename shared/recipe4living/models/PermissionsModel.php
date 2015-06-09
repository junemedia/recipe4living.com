<?php

/**
 *	Permissions Model
 *
 *	@package BluApplication
 *	@subpackage SharedModels
 */
class ClientPermissionsModel extends PermissionsModel
{
	/**
	 *	Allow staging access
	 *
	 *	@access public
	 *	@return bool Allowed
	 */
	public function allowStagingAccess()
	{
		// Admins have access, naturally
		if (parent::allowStagingAccess()) {
			return true;
		}
		
		// HQs
		$visitorIP = Request::getVisitorIPAddress();
		if ($this->_isBluboltHQ($visitorIP)) {
			return true;
		}
		if ($this->_isClientHQ($visitorIP)) {
			return true;
		}
		
		// Staging password
		if (isset($_SERVER['PHP_AUTH_USER'])) {
			if ($_SERVER['PHP_AUTH_USER'] == 'r4lpreview') {
				if (md5($_SERVER['PHP_AUTH_PW']) == '88d2d2246a7324e64b2af3b0ef6fe973') {
					return true;
				}
			}
		}
		
		return false;
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
		if (parent::_isLocal($ip)) {
			return true;
		}
		
		// Subnet mask (192.168.51.0/255.255.255.0)
		if (preg_match('/^192\.168\.51\.[0-9]{1,3}$/', $ip)) {
			return true;
		}
		
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
		return $ip == '216.48.124.31' || $ip == '198.63.247.2';
	}
}

?>
