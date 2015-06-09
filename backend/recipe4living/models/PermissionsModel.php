<?php

/**
 *	Recipe4living Permissions Model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendPermissionsModel extends ClientPermissionsModel
{
	/**
	 *	Set admin name
	 *
	 *	@access protected
	 *	@param string IP address
	 *	@return bool
	 */
	protected function _isLocal($ip)
	{
		if (!parent::_isLocal($ip)) {
			return false;
		}
		
		Template::set('adminName', '[Local user]');
		return true;
	}
	
	/**
	 *	Set admin name
	 *
	 *	@access protected
	 *	@param string Username
	 *	@param string Password
	 *	@return bool
	 */
	protected function _isAdmin($username, $password)
	{
		if (!parent::_isAdmin($username, $password)) {
			return false;
		}
		
		Template::set('adminName', $username);
		return true;
	}
	
	/**
	 *	Is debug mode
	 *
	 *	@access protected
	 *	@return bool
	 */
	protected function _isDebug()
	{
		if (!parent::_isDebug()) {
			return false;
		}
		
		Template::set('adminName', '[Debug mode]');
		return true;
	}
}

?>
