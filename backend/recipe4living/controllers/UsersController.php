<?php

/**
 *	Users
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class Recipe4livingUsersController extends ClientBackendController
{

	/**
	 * Base url
	 *
	 * @var string Base url
	 */
	protected $_baseUrl = '/users';
	
	/**
	 *	Menu slug
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_menuSlug = 'user_listing';

	/**
	 *	View
	 *
	 *	@access public
	 */
	public function view()
	{
		// Get models
		$userModel = BluApplication::getModel('user');
		
		// Page
		$page = Request::getInt('page', 1);
		
		// Clear search
		if(Request::getBool('clear')) {
			return $this->_redirect($this->_baseUrl);
		}
		
		// Set search parameters
		$urlArgsArray = array();
		if($username = Request::getString('username')) {
			$urlArgsArray['username'] = $username;
		}
		if($email = Request::getString('email')) {
			$urlArgsArray['email'] = $email;
		}
		if($fullName = Request::getString('full_name')) {
			$urlArgsArray['fullName'] = $fullName;
		}
		$live = Request::getInt('live');
		if($live==1 || $live==2) {
			$urlArgsArray['live'] = $live;
		}

		$baseUrl = $this->_baseUrl;
		$filterArray = $urlArgsArray;
		$urlArgsArray['page'] = '';
		$paginationUrl = SITEURL . $baseUrl . '?' . http_build_query($urlArgsArray);
		$urlArgsArray['page'] = $page;
		//$pageUrl = SITEURL . $baseUrl . '?' . http_build_query($urlArgsArray);

		$limit = 20;
		$offset = ($page - 1) * $limit;
		$total = null;
		$users = $userModel->getUsers($offset, $limit, null, $filterArray, $total);
		
		Session::set('userListDisplayArgs', $urlArgsArray);
		
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => $paginationUrl
		));
		
		// Base url
		$detailsPageBaseUrl = $this->_baseUrl . '/userdetails';
		
		// Load template
		include(BLUPATH_TEMPLATES.'/users/view.php');
		
	}
	
	/**
	 *	View user details
	 *
	 *	@access public
	 */
	public function userDetails()
	{
		// Get models
		$userModel = BluApplication::getModel('user');
		$itemsModel = BluApplication::getModel('items');
		
		// Get user ID
		if(isset($this->_args[0])) {
			$userId = (int)$this->_args[0];
		}
		else {
			return $this->_redirect($this->_baseUrl);
		}
		
		// Get user
		$user = $userModel->getUser($userId);
		
		// Back button
		$urlArgsArray = Session::get('userListDisplayArgs');
		$backButtonUrl = SITEURL . $this->_baseUrl . '?' . http_build_query($urlArgsArray);
		
		$baseUrl = $this->_baseUrl;
		
		//var_dump($user);
		
		$limit = 10;
		$page = 1;
		// Recipe box items
		if(!empty($user['saves']['recipebox'])) {
			$itemType = 'recipebox';
			if(Request::getString('type') == $itemType) {
				$page = Request::getInt('page', 1);
			}
			$total = count($user['saves']['recipebox']);
			$offset = ($page - 1) * $limit;
			$recipeboxItems = array_slice($user['saves']['recipebox'], $offset, $limit, true);
			foreach($recipeboxItems as $objectId => $recipeboxItem) {
				$item = $itemsModel->getItem($objectId);
				$recipeboxItems[$objectId]['link'] = $item['link'];
				$recipeboxItems[$objectId]['title'] = $item['title'];
			}
			$paginationBaseUrl = SITEURL.$this->_baseUrl.'/userdetails/'.$userId.'?type='.$itemType.'&amp;page=';
			$recipeboxPagination = Pagination::simple(array(
				'limit' => $limit,
				'total' => $total,
				'current' => $page,
				'url' => $paginationBaseUrl
			));
		}
		// Recipe notes
		if(!empty($user['saves']['recipe_note'])) {
			$itemType = 'recipe_note';
			if(Request::getString('type') == $itemType) {
				$page = Request::getInt('page', 1);
			}
			$total = count($user['saves'][$itemType]);
			$recipeNoteItems = array_slice($user['saves'][$itemType], $offset, $limit, true);
			foreach($recipeNoteItems as $objectId => $recipeNoteItem) {
				$item = $itemsModel->getItem($objectId);
				$recipeNoteItems[$objectId]['link'] = $item['link'];
				$recipeNoteItems[$objectId]['title'] = $item['title'];
			}
			$paginationBaseUrl = SITEURL.$this->_baseUrl.'/userdetails/'.$userId.'?type='.$itemType.'&amp;page=';
			$recipeNotePagination = Pagination::simple(array(
				'limit' => $limit,
				'total' => $total,
				'current' => $page,
				'url' => $paginationBaseUrl
			));
		}
		
		// Cookbooks
		if(!empty($user['saves']['cookbook'])) {
			$itemType = 'cookbook';
			if(Request::getString('type') == $itemType) {
				$page = Request::getInt('page', 1);
			}
			$total = count($user['saves'][$itemType]);
			$cookbookItems = array_slice($user['saves'][$itemType], $offset, $limit, true);
			foreach($cookbookItems as $objectId => $cookbookItem) {
				$cookbook = $itemsModel->getCookbook($objectId);
				$cookbookItems[$objectId]['title'] = $cookbook['title'];
				$cookbookItems[$objectId]['description'] = $cookbook['description'];
				$cookbookItems[$objectId]['date'] = $cookbook['date'];
				$cookbookItems[$objectId]['live'] = $cookbook['live'];
				$cookbookItems[$objectId]['link'] = $cookbook['link'];
			}
			$paginationBaseUrl = SITEURL.$this->_baseUrl.'/userdetails/'.$userId.'?type='.$itemType.'&amp;page=';
			$cookbookPagination = Pagination::simple(array(
				'limit' => $limit,
				'total' => $total,
				'current' => $page,
				'url' => $paginationBaseUrl
			));
		}
		
		// Load template
		include(BLUPATH_TEMPLATES.'/users/details.php');
	}
	
	/**
	 *	Disable user
	 *
	 *	@access public
	 */
	public function disableUser()
	{
		// Get models
		$userModel = BluApplication::getModel('user');
		// Get user ID
		if(isset($this->_args[0])) {
			$userId = (int)$this->_args[0];
		}
		else {
			return $this->_redirect($this->_baseUrl);
		}
		
		$deleteReason = Request::getString('deleteReason');
		
		$currentUser = $userModel->getCurrentUser();
		if($currentUser['id'] == $userId) {
			Messages::addMessage('You cannot disable yourself !!!', 'error');
			return $this->userDetails();
		}
		// Update status
		elseif($userModel->setUserStatus($userId, 1, $deleteReason)) {
			Messages::addMessage('User disabled successfully', 'info'); 
		}
		
		// Redirect
		$urlArgsArray = Session::get('userListDisplayArgs');
		$redirectUrl = $this->_baseUrl . '?' . http_build_query($urlArgsArray);
		return $this->_redirect($redirectUrl);
	}
	
	/**
	 *	Enable user
	 *
	 *	@access public
	 */
	public function enableUser()
	{
		// Get models
		$userModel = BluApplication::getModel('user');
		// Get user ID
		if(isset($this->_args[0])) {
			$userId = (int)$this->_args[0];
		}
		else {
			return $this->_redirect($this->_baseUrl);
		}
		
		// Update status
		if($userModel->setUserStatus($userId, 0)) {
			Messages::addMessage('User enabled successfully', 'info'); 
		}
		
		// Redirect
		$urlArgsArray = Session::get('userListDisplayArgs');
		$redirectUrl = $this->_baseUrl . '?' . http_build_query($urlArgsArray);
		return $this->_redirect($redirectUrl);
	}
	
	/**
	 *	Update / Reset password
	 *
	 *	@access public
	 */
	public function setPassword()
	{
		// Get models
		$userModel = BluApplication::getModel('user');
		// Get user ID
		if(isset($this->_args[0])) {
			$userId = (int)$this->_args[0];
		}
		else {
			return $this->_redirect($this->_baseUrl);
		}
		
		$user = $userModel->getUser($userId);
		
		$success = false;
		// Update password
		if(Request::getBool('update')) {
			$newPassword = Request::getString('newPassword');
			$sendEmail = Request::getBool('updateSendEmail');
			$validation = true;
			if(strlen($newPassword)<6) {
				$validation = false;
				Messages::addMessage('Password cannot be shorter than 6 characters', 'error'); 
			}
			if($validation) {
				$success = $userModel->editUser($userId, $newPassword);
				if($success) {
					Messages::addMessage('Password updated successfully', 'info'); 
				}
			}
		}
		// Reset password
		elseif(Request::getBool('reset')) {
			// Generate new password and apply
			$newPassword = Utility::createRandomPassword();
			$sendEmail = Request::getBool('resetSendEmail');
			$success = $userModel->editUser($userId, $newPassword);
			if($success) {
				Messages::addMessage('Password reset successfully', 'info'); 
			}
		}
		
		if($success && $sendEmail && $user['email']) {
			// Send email
			$email = new Email();
			$vars = array(
				'firstName' => $user['firstname'],
				'lastName' => $user['lastname'],
				'email' => $user['email'],
				'password' => $newPassword
			);
			//$email->addBcc('R4Lfpwemails@gmail.com', 'Recipe4living Forgot Password: '.$user['username']);
			if($email->quickSend($user['email'], $user['fullname'], Text::get('global_forgotpass_email_subject'), 'passwordreminder', $vars)) {
				Messages::addMessage('New password was sent to '.$user['email'], 'info'); 
			}
		}
		
		if($success) {
			// Redirect
			$urlArgsArray = Session::get('userListDisplayArgs');
			$redirectUrl = $this->_baseUrl . '?' . http_build_query($urlArgsArray);
			return $this->_redirect($redirectUrl);
		}
		else {
			if(Request::getBool('update')) {
				Template::set('newPassword', $newPassword);
				Template::set('updateSendEmail', $sendEmail);
			}
			$this->userDetails();
		}
	}
	
	/**
	 *	Disable user
	 *
	 *	@access public
	 */
	public function updateUserType()
	{
		// Get models
		$userModel = BluApplication::getModel('user');
		// Get user ID
		if(isset($this->_args[0])) {
			$userId = (int)$this->_args[0];
		}
		else {
			return $this->_redirect($this->_baseUrl);
		}
		
		$userType = Request::getString('type','regular');		

		$currentUser = $userModel->getCurrentUser();
		if($currentUser['id'] == $userId) {
			Messages::addMessage('You cannot edit yourself !!!', 'error');
			return $this->userDetails();
		}
		// Update status
		elseif($userModel->setPrivileges($userId, $userType)) {
			Messages::addMessage('User updated successfully', 'info'); 
		}
		
		// Redirect
		$urlArgsArray = Session::get('userListDisplayArgs');
		$redirectUrl = $this->_baseUrl . '?' . http_build_query($urlArgsArray);
		return $this->_redirect($redirectUrl);
	}
	
	
	/**
	 *	Quicksearch
	 *
	 *	@access public
	 */
	public function quicksearch()
	{
		// Get data from request
		$searchTerm = Request::getString('searchterm');
		
		// Get results limit
		$limit = BluApplication::getSetting('quickSearchLimit', 5);
		
		// Get users
		$userModel = BluApplication::getModel('user');
		$users = $userModel->getUsers(0, $limit, 'relevance', $searchTerm);
		$userModel->addDetails($users);
		
		// Load template
		switch ($this->_doc->getFormat()) {
			case 'json':
				$response = array();
				if (!empty($users)) {
					foreach ($users as $user) {
						
						ob_start();
						include(BLUPATH_BASE_TEMPLATES.'/users/quick_search_user.php');
						
						$response[] = array(
							'value' => $user['username'],
							'html' => ob_get_clean()
						);
					}
				}
				echo json_encode($response);
				break;
				
			default:
				include (BLUPATH_BASE_TEMPLATES.'/users/quick_search.php');
				break;
		}
	}
}

?>
