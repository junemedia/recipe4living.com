<?php


/**
 *	NOTE: PersonObjects are not cacheable, which is why they directly extend BluModel rather than BluObject.
 *	The reason why these are non cacheable is because they only consist of MemberObjects and/or ContentcreatorObjects, which themselves ARE cacheable already.
 */
class PersonObject extends BluModel {

	/**
	 *	Hold the member and/or content creator.
	 */
	private $_member;
	private $_contentCreator;

	/**
	 *	@args (array) id: takes an associative array:
	 *		'contentcreator': content creator id
	 *		'member': member id
	 *		'username': member username
	 *		For ONE ONLY (content creator takes priority, then member ID) never all, we grab the relevant data.
	 */
	public function __construct(array $idarray){

		parent::__construct();

		/* Get content creator and member IDs */
		$validCC = isset($idarray['contentcreator']) && (int) $idarray['contentcreator'] > 0;
		$validMember = isset($idarray['member']) && (int) $idarray['member'] > 0;
		if (Utility::iterable($idarray)){

			if ($validCC){

				/* We have the content creator ID, now get the member ID. */
				$idarray['member'] = $this->_getMemberFromContentCreator($idarray['contentcreator']);
				$validMember = (bool) $idarray['member'];

			} else if ($validMember){

				/* We have the member ID, now get the content creator ID. */
				$idarray['contentcreator'] = $this->_getContentCreatorFromMember($idarray['member']);
				$validCC = (bool) $idarray['contentcreator'];

			} else if (isset($idarray['username'])){

				/* We have the member username, get the member ID... */
				$query = "SELECT `UserID`
					FROM `users`
					WHERE `username` = '" . Database::escape($idarray['username']) . "'";
				$this->_db->setQuery($query, 0, 1);
				$idarray['member'] = (int) $this->_db->loadResult();
				$validMember = (bool) $idarray['member'];

				/* ...and then get the content creator ID. */
				$idarray['contentcreator'] = $this->_getContentCreatorFromMember($idarray['member']);
				$validCC = (bool) $idarray['contentcreator'];

			}

		}

		/* Create the objects. */
		$this->_contentCreator = $validCC ? BluApplication::getObject('contentcreator', $idarray['contentcreator']) : null;
		$this->_member = $validMember ? BluApplication::getObject('member', $idarray['member']) : null;

		/* Check for uselessness. */
		if (!$this->_contentCreator && !$this->_member){ throw new NoDataException($this); }

	}

	/**
	 *	Property overloading.
	 */
	public function __isset($var){
		$results = $this->$var;
		return !is_null($results);
	}

	/**
	 *	Property overloading.
	 */
	public function __get($var){
		switch($var){
			/* Content creator ID */
			case 'contentcreatorid':
				if (!isset($this->_contentCreator)){ return null; }
				return $this->_contentCreator->id;
				break;

			/* ContentcreatorObject */
			case 'byline':
				if (!isset($this->_contentCreator)){ return null; }
				return $this->_contentCreator->$var;
				break;

			/* Member ID */
			case 'userid':
				if (!isset($this->_member)){ return null; }
				return $this->_member->id;
				break;

			/* MemberObject */
			case 'about':
			case 'deleted':
			case 'describeyourself':
			case 'email':
			case 'firstname':
			case 'job':
			case 'joined':
			case 'lastname':
			case 'location':
			case 'password':	// Encrypted password
			case 'timezone':
			case 'username':
				if (!isset($this->_member)){ return null; }
				return $this->_member->$var;
				break;

			/* Custom */
			case 'image':
			case 'name':
			case 'profileURL':
				$method = '_get' . ucfirst($var);
				if (!method_exists($this, $method)){ return null; }
				return $this->$method();
				break;

			default:
				/* Not allowed. */
				return null;
				break;
		}
	}

	/**
	 *	Method overloading
	 *
	 *	Doesn't do methods with parameters passed by reference - delegate them manually.
	 */
	public function __call($method, $args){
		switch($method){
			/* MemberObject */
			case 'addComment':
			case 'applyTags':
			case 'getComments':
			case 'getCommentCount':
			case 'getFamily':
			case 'getLife':
			case 'getMyDays':
			case 'getPrivacy':
			case 'getSnapshot':
			case 'getTags':
			case 'getToDoList':
			case 'getUserInfo':
			case 'getWork':
			case 'login':
			case 'updateTags':
				if (!isset($this->_member)){ return null; }
				try{
					$methodObj = new ReflectionMethod($this->_member, $method);
					return $methodObj->invokeArgs($this->_member, $args);
				} catch (ReflectionException $e){
					return false;
				}
				break;

			/* Not allowed */
			default:
				return null;
				break;
		}
	}

	/**
	 *	Check for equality of two PersonObject objects.
	 */
	public function equals(PersonObject $person){
		return $person->userid == $this->userid;
	}
	
	/**
	 *	Checks if this person is an admin.
	 */
	public function isAdmin(){
		
		/* Get list of admins */
		$admins = explode(',', BluApplication::getSetting('admins'));
		
		/* Check */
		return isset($this->username) && in_array($this->username, $admins);
		
	}

	/**
	 *	Update a person's details, crossing over several database tables.
	 *
	 *	@args (array) args: changes to make.
	 *	@args (array) special: changes that don't need values quoted (i.e. SQL functions)
	 *	@args (array) tables: specific tables to update, as opposed to all.
	 */
	public function edit(array $args, array $special = array(), array $tables = array()){

		/* Prepare */
		$successes = array();

		/* Edit relevant fields in `user` table. */
		if (!Utility::iterable($tables) || array_search('users', $tables) !== false){
			$successes['users'] = $this->_edit('users', $args, $special, array('UserID' => $this->userid));
		}

		/* Edit relevant fields in `user_info` table. */
		if (!Utility::iterable($tables) || array_search('user_info', $tables) !== false){
			$successes['user_info'] = $this->_edit('user_info', $args, $special, array('userid' => $this->userid));
		}

		/* Edit relevant fields in `userVariables` table. */
		if (!Utility::iterable($tables) || array_search('userVariables', $tables) !== false){
			$successes['userVariables'] = $this->_edit('userVariables', $args, $special, array('userID' => $this->userid));
		}

		/* Edit relevant fields in 'userPrivacyPrefs' table. */
		if (!Utility::iterable($tables) || array_search('userPrivacyPrefs', $tables) !== false){
			$successes['userPrivacyPrefs'] = $this->_edit('userPrivacyPrefs', $args, $special, array('userID' => $this->userid));
		}

		/* Flush cached MemberObject */
		if (array_reduce($successes, create_function('$a,$b', 'return $a || $b;'), true)){
			$this->flushCached('member');
		}

		// POSSIBLY CONTENT CREATOR AS WELL?
		// Content creator name.
		if (isset($args['firstname']) || isset($args['lastname'])){
			$fullname = array();
			if (isset($args['firstname'])){
				$fullname[] = $args['firstname'];
			}
			if (isset($args['lastname'])){
				$fullname[] = $args['lastname'];
			}
			$fullname = trim(implode(' ', $fullname));
			$this->_edit('contentCreators', array('fullName' => $fullname), array(), array('contentCreatoruserID' => $this->userid));
		}

		/* Return */
		return array_reduce($successes, create_function('$a,$b', 'return $a && $b;'), true);

	}
	
	/**
	 *	Checks if is guest user.
	 */
	public function isGuest(){
		
		/* Get guest username */
		$guestUsername = BluApplication::getSetting('guestUsername');
		
		/* Test */
		return isset($this->username) && $guestUsername == $this->username;
		
	}

	/**
	 *	Set a content creator
	 */
	public function setContentCreator(ContentcreatorObject $cc){

		/* Update DB */
		$cc->setUserID($this->userid);

		/* Update $this. */
		$this->_contentCreator = $cc;

		/* Return */
		return $this;

	}

	/**
	 *	Profile: get replies for questions.
	 */
	public function getCommentedItems($type, $offset = null, $limit = null){
		return isset($this->_member) ? $this->_member->getCommentedItems($type, $offset, $limit) : null;
	}

	/**
	 *	Delegate to MemberObject.
	 */
	public function getFriends($offset = null, $limit = null, &$total = null, $randomise = false){
		$method = __FUNCTION__;
		return isset($this->_member) ? $this->_member->$method($offset, $limit, $total, $randomise) : null;
	}

/**
	 *	Delegate to MemberObject.
	 */
	public function getPhotos($offset = null, $limit = null, &$total = null, $randomise = false){
		$method = __FUNCTION__;
		return isset($this->_member) ? $this->_member->$method($offset, $limit, $total, $randomise) : null;
	}

	/**
	 *	Flush cached object.
	 */
	public function flushCached($specific = null){

		/* Prepare */
		$toFlush = array();
		$success = true;
		$method = __FUNCTION__;

		/* What to flush? */
		switch($specific){
			case 'member':
				if (isset($this->_member)){
					$toFlush[] = $this->_member;
				}
				break;

			case 'contentcreator':
				if (isset($this->_contentCreator)){
					$toFlush[] = $this->_contentCreator;
				}
				break;

			default:
				/* Flush both. */
				if (isset($this->_member)){
					$toFlush[] = $this->_member;
				}
				if (isset($this->_contentCreator)){
					$toFlush[] = $this->_contentCreator;
				}
				break;
		}

		/* Try to flush everything */
		if (!Utility::is_loopable($toFlush)){ return false; }
		foreach($toFlush as $thing){
			if (!$thing->$method()){
				$success = false;
			}
		}

		/* Return */
		return $success;

	}




	###							PRIVATE CONVENIENCE FUNCTIONS							###

	/**
	 *	Return the person's name. Content creator name takes priority over member name.
	 */
	private function _getName()
	{
		/* Prepare */
		$name = '';

		/* Try content creator */
		if (isset($this->_contentCreator)){
			$name = $this->_contentCreator->fullname;
		}
		/* Try user name */
		if (!$name && isset($this->_member)){
			$name = $this->_member->fullname;
		}
		/* Try username */
		if (!$name && isset($this->_member)){
			$name = $this->_member->username;
		}

		/* Trim white space and return */
		return trim($name);
	}

	/**
	 *	The URL for the person's profile page.
	 *	This shouldn't include SITEURL, but should include '/'
	 */
	private function _getProfileurl()
	{
		return '/profile/' . $this->username . '/';
	}

	/**
	 *	Get the user's image
	 *	This should be just the filename plus extension, no filepaths.
	 */
	private function _getImage()
	{
		// Try member image
		if (isset($this->_member)){
			$image = $this->_member->image;
			if ($image){
				return $image;
			}
		}
		
		// Try content creator image
		if (isset($this->_contentCreator)){
			$image = $this->_contentCreator->image;
			if ($image) {
				return $image;
			}
		}

		// Fail miserably
		return false;
	}

	/**
	 *	@args (int) id: 	the content creator id
	 *	@return (int) returns the member id corresponding to the content creator.
	 */
	private function _getMemberFromContentCreator($id)
	{
		$id = (int) $id;
		if (!$id){ return null; }
		$query = 'SELECT cc.contentCreatoruserID
			FROM `contentCreators` AS `cc`
			WHERE cc.contentCreatorID = '.$id.'
				AND cc.contentCreatoruserID IS NOT NULL 
				AND cc.contentCreatoruserID > 0';
		$record = $this->_fetch($query, 'member_contentcreator_'.$id, 0, 1);
		$result = Utility::is_loopable($record) ? (int) array_pop($record) : 0;
		return $result == 0 ? null : $result;
	}

	/**
	 *	@args (int) id: 	the member id
	 *	@return (int) returns the content creator id corresponding to the member.
	 */
	private function _getContentCreatorFromMember($id){
		$id = (int) $id;
		if (!$id){ return null; }
		$query = 'SELECT cc.contentCreatorID
			FROM `contentCreators` AS `cc`
			WHERE cc.contentCreatoruserID = '.$id;
		$record = $this->_fetch($query, 'contentcreator_member_'.$id, 0, 1);
		$result = Utility::is_loopable($record) ? (int) array_pop($record) : 0;
		return $result == 0 ? null : $result;
	}

}

?>
