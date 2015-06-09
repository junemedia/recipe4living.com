<?php

/**
 *	Permissions Model
 *
 *	@package BluApplication
 *	@subpackage FrontendModels
 */
class ClientFrontendPermissionsModel extends ClientPermissionsModel
{
	/**
	 *	Can edit articles/recipes
	 *
	 *	@access public
	 *	@param string Username
	 *	@return bool Editable
	 */
	public function canEdit()
	{
		if ($this->_isHttpAuthenticated()) {
			return true;
		}
		
		return false;
	}

}

?>