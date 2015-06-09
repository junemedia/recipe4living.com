<?php

/**
 *	Hacks. DEBUG ONLY!
 */
if (DEBUG){
class HacksController extends ClientBackendController {
	
	/**
	 *	Log in as any user.
	 */
	public function login(){
	
		/* Get username */
		$args = $this->_args;
		if (!Utility::iterable($args)){
			return false;
		}
		$username = array_shift($args);
		if (!$username){
			return false;
		}
		
		/* Get user (thereby checking it exists) */
		$personModel = BluApplication::getModel('newperson');
		$user = $personModel->getPerson(array('username' => $username));
		if (!Utility::iterable($user)){
			return false;
		}
		
		// Regenerate session id to prevent session fixation
		Session::regenerateID();

		// Store ID in session
		Session::set('UserID', $user['userid']);

		/* Exit */
		switch($this->_doc->getFormat()){
			case 'json':
				echo json_encode(true);
				break;
				
			default:
				echo 'HACKED! Logged in as <code>'.$user['username'].'</code>';
				break;
		}
		return true;
		
	}
	
	/**
	 *	Serialize something.
	 */
	public function serialize(){
		
		/* Get variable */
		$input = Request::getVar('q', 'something');
		if ($cast = Request::getString('cast')) {
			settype($input, $cast);
		}
		
		/* Serialize */
		$output = serialize($input);
		
		/* Output */
		var_dump($output);
		
		// Form
		include(BLUPATH_BASE_TEMPLATES.'/hacks/serialize.php');
		
	}
	/**
	 *	Unserialize something.
	 */
	public function unserialize(){
		
		/* Get variable */
		$input = Request::getVar('q', serialize('something'));
		
		/* Serialize */
		if ($input === serialize(false)) {
			var_dump(false);
		} else {
			try {
				$output = unserialize($input);
				var_dump($output);
			} catch (Exception $e) {
				echo 'Not unserialisable.';
			}
		}
		
		// Form
		include(BLUPATH_BASE_TEMPLATES.'/hacks/unserialize.php');
		
	}

	
}
}

?>
