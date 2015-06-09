<?php

/**
 *	Recipe4living Box Model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendBoxoutModel extends BackendBoxoutModel
{
	/**
	 *	Validate box content info
	 *
	 *	@access protected
	 *	@param array Box
	 *	@param array Info
	 *	@return array Clean info
	 */
	protected function _validateInfo($box, $info)
	{
		// Do something speshal for daily chef - allow usernames...
		if ($box['slug'] == 'daily_chef') {
			
			$userModel = BluApplication::getModel('user');
			$info['user'] = $userModel->getUserId($info['user']);
		}
		
		// Continue
		return parent::_validateInfo($box, $info);
	}
}

?>