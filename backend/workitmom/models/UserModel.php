<?php

/**
 *	Workitmom User Model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendUserModel extends ClientUserModel
{
	/**
	 *	Reinstate a user's account.
	 */
	public function reinstate($userId){
		
		/* Sanitise */
		$userId = (int) $userId;
		
		/* Undelete member. */
		$changes = array('terminatedtime' => 0);
		$special = array('email' => 'REPLACE(`email`, "DELETED", "")');	// MySQL doesn't support regex replacement, grr.
		$criteria = array('UserID' => $userId);
		$memberUndeleted = $this->_edit('users', $changes, $special, $criteria);

		/* Undelete content creator too, not utmost important though. */
		$changes = array('isLive' => 1);
		$special = array();
		$criteria = array('contentCreatorUserID' => $userId);
		$ccUndeleted = $this->_edit('contentCreators', $changes, $special, $criteria);

		/* Success? */
		return (bool) $memberUndeleted;
		
	}
	
	/**
	 *	Reset and regenerate a user's password
	 *
	 *	@param int User ID of user to reset password for.
	 *	@return string The new password.
	 */
	public function regenerate_password($userId){
		
		/* Generate */
		$password = Utility::createRandomPassword();
		$this->updatePassword($password, $userId);
		
		/* Return password */
		return $password;
		
	}
}

?>