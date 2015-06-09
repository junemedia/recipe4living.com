<?php

/**
 *	This is a user that has credentials to log into the site.
 */
class MemberObject extends BluObject implements CommentsInterface {

	/**
	 *	Non-essential data. Only gets created when needed, using Singleton pattern.
	 */
	private $_userinfo;
	private $_uservariables;
	private $_userprivacy;

	/**
	 *	@args (array) id: the member id
	 */
	public function __construct($id){

		//get the database and cache
		parent::__construct();
		$this->id = (int)$id;
		$this->_cacheObjectID = 'member_'.$this->id;

		/* Build object */
		$query = "SELECT *
			FROM `users` AS `u`
			WHERE u.UserID = ".$this->id;
		$this->_buildObject($query);

	}

	/**
	 *	Accessor.
	 */
	public function __get($var){
		switch($var){
			/* Aliases */
			case 'image':
				return $this->userImage;
				break;
			case 'privacy_friends':
				return $this->userProfileMemberNetwork;
				break;

			/* User info */
			case 'bestadvice':
			case 'bestjobthing':
			case 'book':
			case 'childrenAge':
			case 'describeyourself':
			case 'destress':
			case 'dreamjob':
			case 'employerName':
			case 'employmentType':
			case 'industry';
			case 'interests':
			case 'jobhours';
			case 'joblike';
			case 'jobstress':
			case 'jobTitle':
			case 'kidactivity':
			case 'locationID':
			case 'numChildren':
			case 'movie':
			case 'parentadvice':
			case 'parentignore':
			case 'relationship':
			case 'statement':
			case 'url':
			case 'worstjobthing':
				$this->_getUserInfo();
				return isset($this->_userinfo->$var) ? $this->_userinfo->$var : null;
				break;

			/* User variables */
			case 'userImage':
				$this->_getUserVariables();
				return isset($this->_uservariables->$var) ? $this->_uservariables->$var : null;
				break;

			/* User privacy */
			case 'userProfileShow':
			case 'userAllowMessage':
			case 'userProfileFamily':
			case 'userProfileWork':
			case 'userProfileLife':
			case 'userProfileGroups':
			case 'userProfileMemberNetwork':
			case 'userProfileArticles':
			case 'userProfileQuestions':
			case 'userProfileComments':
			case 'userRecEmail':
			case 'userProfilePhotos':
				$this->_getPrivacy();
				return isset($this->_userprivacy->$var) ? $this->_userprivacy->$var : null;
				break;

			/* Data */
			case 'email':
			case 'firstname':
			case 'lastname':
			case 'password':	// Encrypted password
			case 'timezone':
			case 'username':
				return isset($this->_data->$var) ? $this->_data->$var : null;
				break;

			/* Date */
			case 'joined':
				return isset($this->_data->$var) ? Utility::formatDate($this->_data->$var) : null;
				break;

			/* Custom */
			case 'deleted':
			case 'fullname':
			case 'location':
			case 'job':
			case 'about':
				$method = '_get' . ucfirst($var);
				if (!method_exists($this, $method)){ return null; }
				return $this->$method();
				break;

			/* Aliases */

			default:
				return null;
				break;
		}
	}

	/**
	 *	Update last login time.
	 */
	public function login(){

		$specialArgs = array(
			'lastlogin' => '`mostrecentlogin`',
			'mostrecentlogin' => 'UNIX_TIMESTAMP()',
			'timesLoggedIn' => '`timesLoggedIn` + 1'
		);
		return $this->_edit('userVariables', array(), $specialArgs, array('userID' => $this->id));

	}

	/**
	 *	Overrides BluObject.
	 */
	public function flushCached(){
		$user_flushed = parent::flushCached();
		$user_info_flushed = $this->_cache->delete($this->_cacheObjectID . '_userinfo');
		$user_variables_flushed = $this->_cache->delete($this->_cacheObjectID . '_uservariables');
		$user_privacy_flushed = $this->_cache->delete($this->_cacheObjectID . '_userprivacy');
		return $user_flushed && $user_info_flushed && $user_variables_flushed && $user_privacy_flushed;
	}

	/**
	 *	Returns the user's info.
	 */
	public function getUserInfo(){

		/* Ensures user info data exists */
		$this->_getUserInfo();

		/* Returns it */
		return $this->_userinfo;

	}

	/**
	 *	Returns the user's privacy settings.
	 */
	public function getPrivacy(){

		/* Ensures user privacy data exists */
		$this->_getPrivacy();

		/* Returns it */
		return $this->_userprivacy;

	}

	/**
	 *	Fetch a To Do List
	 */
	public function getToDoList()
	{
		$query = 'SELECT m.myId, m.myText, um.myAnswer, um.myPublic, um.myUpdated, um.myTellFriend
			FROM myday AS m
				LEFT JOIN users_myday AS um ON um.myId = m.myId
					AND um.user_id = '.(int) $this->id.'
			WHERE m.myType = "snapshot"
			ORDER BY m.myOrder';
		$this->_db->setQuery($query);
		return $this->_db->loadAssocList('myId');
	}

	/**
	 *	Get friends.
	 */
	public function getFriends($offset = null, $limit = null, &$total = null, $randomise = false){

		/* Get parameters */
		//$offset = (int) $offset;
		//$limit = (int) ($limit ? $limit : BluApplication::getSetting('listingLength', 9));

		/* Build query */
		$friend_cacheObjectID = $this->_cacheObjectID . '_friends_' . $offset . '_' . $limit;
		$query = 'SELECT SQL_CALC_FOUND_ROWS IF(x.userID = '.(int)$this->id.', x.contactorID, x.userID) AS userid
			FROM xrefusercontactor AS x
			WHERE x.userID = '.(int)$this->id.'
				OR x.contactorID = '.(int)$this->id;
		if ($randomise) {
			$query .= '
				ORDER BY RAND()';
		}

		/* Fetch data */
		$this->_db->setQuery($query, $offset, $limit);
		$records = $this->_db->loadAssocList('userid');
		//$records = $this->_fetch($query, $friend_cacheObjectID, $offset, $limit, false);
		$total = $this->_db->getFoundRows();

		/* Format data */
		$personModel = BluApplication::getModel('person');
		$friends = array();
		if (Utility::is_loopable($records)){
			foreach($records as $record){
				if ($friend = $personModel->getPerson(array('member' => $record['userid']))){
					$friends[] = $friend;
				}
			}
		}

		/* Dump */
		return $friends;

	}

	/**
	 *	Profile: Get the data needed for the member's life.
	 */
	public function getLife(){

		$life = new stdClass();

		/* Check privacy */
		if ($this->userProfileLife){

			/* Get data */
			foreach(array('statement', 'interests', 'destress', 'book', 'movie') as $var){
				$life->$var = $this->$var;
			}
			$life->website = $this->url;
			$life->advice = $this->bestadvice;
			$life->adjective = $this->describeyourself;

		} else {

			/* User chose to keep information hidden. */
			$life = false;

		}

		/* Return data */
		return $life;

	}
	
	/**
	 *	Profile: Get the data needed for the member's work details.
	 */
	public function getWork(){

		$work = new stdClass();

		/* Check privacy */
		if ($this->userProfileWork){

			/* Get data */
			foreach(array('employmentType', 'jobTitle', 'employerName', 'industry', 'jobhours', 'joblike', 'jobstress', 'bestjobthing', 'worstjobthing', 'dreamjob') as $var){
				$work->$var = $this->$var;
			}
			
			$work->employment = $this->employmentType;
			$work->employer = $this->employerName;
			$work->job = $this->jobTitle;
			$industries = BluApplication::getModel('user')->getIndustries();
			$work->industry = $industries[$this->industry];
			
			switch($this->jobhours) {
				case '0-20': $work->hours = '0 to 20 hours a week'; break;
				case '20-40': $work->hours = '20 to 40 hours a week'; break;
				case '40+': $work->hours = 'More than 40 hours a week'; break;
				default: $work->hours = null; break;
			}
			switch($this->joblike) {
				case 'love': $work->like = 'I love it'; break;
				case 'like': $work->like = 'I like it'; break;
				case 'paysbills': $work->like = 'It pays the bills'; break;
				case 'notverymuch': $work->like = 'Not very much'; break;
				default: $work->like = null; break;
			}
			switch($this->jobstress) {
				case 'not': $work->stress = 'Not very stressful'; break;
				case 'pretty': $work->stress = 'Pretty stressful'; break;
				case 'extremely': $work->stress = 'Extremely stressful'; break;
				default: $work->stress = null; break;
			}
			$work->best = $this->bestjobthing;
			$work->worst = $this->worstjobthing;
			$work->dream = $this->dreamjob;

		} else {

			/* User chose to keep information hidden. */
			$work = false;

		}

		/* Return data */
		return $work;

	}

	/**
	 *	Profile: Get the data needed for the member's family.
	 */
	public function getFamily(){

		$family = new stdClass();

		/* Check privacy */
		if ($this->userProfileFamily){

			/* Get data */
			$family->childrenCount = $this->numChildren;
			$family->childrenAge = $this->childrenAge;
			switch($this->relationship){
				case 'single':
					$family->relationship = 'Single';
					break;

				case 'married':
					$family->relationship = 'Married';
					break;

				case 'dating':
					$family->relationship = 'In a relationship';
					break;

				case 'partner':
					$family->relationship = 'Partnered/In a civil union';
					break;

				case 'notsay':
					/* Same as default */

				default:
					$family->relationship = 'Would rather not say';
					break;
			}
			$family->advice = $this->parentadvice;
			$family->ignore = $this->parentignore;
			$family->activity = $this->kidactivity;

		} else {

			/* User chose to keep information hidden. */
			$family = false;

		}

		/* Return data */
		return $family;

	}

	/**
	 *	Profile: get the user's photos.
	 */
	public function getPhotos($offset = null, $limit = null, &$total = null, $randomise = false){

		/* Prepare */
		$photos = array();

		/* Check privacy */
		if ($this->userProfilePhotos){

			/* Prepare data */
			$query = "SELECT SQL_CALC_FOUND_ROWS i.imageID AS `id`
				FROM `images` AS `i`
				WHERE i.imageOwner = " . $this->id;

			/* Get data */
			$this->_db->setQuery($query, $offset, $limit);
		$records = $this->_db->loadAssocList();

			//$records = $this->_fetch($query, $this->_cacheObjectID . '_photos', $offset, $limit, false);
$total = $this->_db->getFoundRows();

			/* Wrap */
			$photosModel = BluApplication::getModel('photos');
			foreach($records as $record){
				if ($photo = $photosModel->getPhoto('member', $record['id'])){
					$photos[] = $photo;
				}
			}

		} else {
			/* User chose to keep information hidden. */
			$photos = false;
		}

		/* Return */
		return $photos;

	}


	/**
	 *	Get tags.
	 */
	public function getTags(){

		/* Prepare */
		$tags_cacheObjectID = $this->_cacheObjectID . '_tags';
		$query = "SELECT t.tagName AS `tag`
			FROM `tags` AS `t`
				LEFT JOIN `userTags` `ut` ON ut.tagId = t.tagId
			WHERE ut.userId = " . $this->id;

		/* Get tags */
		$records = $this->_fetch($query, $tags_cacheObjectID, null, null, false);

		/* Format */
		$tags = array();
		foreach($records as $record){
			$tags[] = $record['tag'];
		}

		/* Return */
		return $tags;

	}

	/**
	 *	Required by CommentsInterface.
	 */
	public function getComments(){

		if (!isset($this->comments)){
			$personModel = BluApplication::getModel('person');
			$me = $personModel->getPerson(array('member' => $this->id));

			$commentsModel = BluApplication::getModel('comments');
			$this->comments = $commentsModel->getUser($me);
		}

		/* Return */
		return $this->comments;

	}

	/**
	 *	Required by CommentsInterface.
	 */
	public function getCommentCount(){
		return count($this->getComments());
	}

	/**
	 *	Required by CommentsInterface.
	 */
	public function addComment(array $args){

		/* Append (overwrite) item ID, and comment type. */
		$args['commentTypeObjectId'] = $this->id;
		$args['commentType'] = 'userpage';

		/* Delegate to Comments model */
		$commentsModel = BluApplication::getModel('comments');
		$commentsModel->addComment($args);

		// Add alert
		$alertsModel = BluApplication::getModel('alerts');
		$alertId = $alertsModel->createAlert('profilereply', array(), $args['commentOwner']);
		$alertsModel->applyAlert($alertId, $this->id);

		/* Return */
		return $this;

	}

	/**
	 *	Add user tags.
	 */
	public function applyTags(array $tags)
	{
		/* Prepare */
		$success = true;

		/* Get model */
		$metaModel = BluApplication::getModel('meta');

		/* Add tags */
		foreach($tags as $tag) {

			// Get tag id
			$tagId = $metaModel->addTag($tag);

			/* Add to this item. */
			$args = array(
				'userId' => $this->id,
				'tagId' => $tagId
			);
			$success = $this->_create('userTags', $args, array(), true) && $success;

		}

		/* Exit */
		return $success;
	}

	/**
	 *	Update user tags.
	 */
	public function updateTags(array $tags)
	{
		/* Get model */
		$metaModel = BluApplication::getModel('meta');

		/* Delete all old tags */
		foreach($this->getTags() as $old_tag) {
			$metaModel->decrementTagCount($old_tag);
		}
		$this->_delete('userTags', array('userId' => $this->id));

		/* Add fresh tags */
		return $this->applyTags($tags);
	}

	/**
	 * Fetch all recent My Days for this member
	 */
	public function getMyDays($type = 'day', $days = 365)
	{
		$query = 'SELECT m.myId, m.myText, um.myDate, um.myAnswer, um.myPublic, um.myUpdated, um.myTellFriend
			FROM myday AS m
				LEFT JOIN users_myday AS um ON um.myId = m.myId
					AND um.user_id = "'.(int) $this->id.'"
					AND DATE_SUB(CURDATE(), INTERVAL '.$days.' DAY) <= um.myDate
			WHERE m.myType = "'.Database::escape($type).'"
			ORDER BY m.myOrder';
		$this->_db->setQuery($query);
		$records = $this->_db->loadAssocList();
		if (!$records) {
			return false;
		}

		// Otherwise push into proper hierarchy and serve
		$myDays = array();
		foreach ($records as $record) {
			$record['myDate'] = strtotime($record['myDate']);
			$myDays[$record['myDate']][$record['myId']] = $record;
		}

		krsort($myDays);
		return $myDays;
	}


	###							PRIVATE CONVENIENCE FUNCTIONS BELOW							###

	/**
	 *	@return (String) the member's name.
	 */
	private function _getFullname(){
		$name = '';
		if ($this->firstname){
			$name .= $this->firstname;
		}
		if ($this->lastname){
			if (strlen($name) > 0){
				$name .= ' ';
			}
			$name .= $this->lastname;
		}
		return $name;
	}

	/**
	 *	The geographical location of the person.
	 */
	private function _getLocation(){

		/* Prepare data */
		if (!$this->locationID){ return null; }

		/* Get data */
		$cacheKey = 'location_' . $this->locationID;
		$location = $this->_cache->get($cacheKey);
		if ($location === false){
			$locationsModel = BluApplication::getModel('locations');
			$location = $locationsModel->getLocation($this->locationID);
			$this->_cache->set($cacheKey, $location);
		}

		/* Format */
		$name = $location['locationLongName'];

		/* Return */
		return $name;

	}

	/**
	 *	Get job string.
	 */
	private function _getJob(){

		/* Prepare */
		$employer = $this->employerName;
		$title = $this->jobTitle ? $this->jobTitle : 'Works';

		/* Return */
		if (!$employer){
			return 'N/A';
		}
		return $title . ' at ' . $employer;

	}

	/**
	 *	Get statement.
	 */
	private function _getAbout()
	{
		return $this->statement ? $this->statement : 'N/A';
	}

	/**
	 *	@return (stdClass) everything from the 'user_info' table. Cacheable.
	 */
	private function _getUserInfo(){

		if (!isset($this->_userinfo)){

			/* Fetch object */
			$query = "SELECT *
				FROM `user_info`
				WHERE `userid` = ".$this->id;
			$this->_userinfo = $this->_fetch($query, $this->_cacheObjectID . '_userinfo', 0, 1);

		}

		// Exit
		return $this->_userinfo;

	}

	/**
	 *	@return (stdClass) everything from the 'userVariables' table.
	 *	Don't cache, because the data is SUPPOSED to change quite often.
	 */
	private function _getUserVariables(){

		if (!isset($this->_uservariables)){

			/* Fetch object */
			$query = "SELECT *
				FROM `userVariables`
				WHERE `userID` = ".$this->id;
			$this->_uservariables = $this->_fetch($query, $this->_cacheObjectID . '_uservariables', 0, 1);

		}

		// Exit
		return $this->_uservariables;

	}

	/**
	 *	Get privacy preferences
	 */
	private function _getPrivacy(){

		if (!isset($this->_userprivacy)){

			/* Fetch object */
			$query = "SELECT *
				FROM `userPrivacyPrefs`
				WHERE `userID` = ".$this->id;
			$this->_userprivacy = $this->_fetch($query, $this->_cacheObjectID . '_userprivacy', 0, 1);

		}

		// Exit
		return $this->_userprivacy;

	}
	
	/**
	 *	Whether user has been deleted or not.
	 *
	 *	@return bool. 
	 */
	private function _getDeleted(){
		return isset($this->_data->terminatedtime) && (int) $this->_data->terminatedtime;
	}

}

?>