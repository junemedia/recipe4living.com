<?php

/**
 * Account Controller
 *
 * @package BluApplication
 * @subpackage FrontendControllers
 */
class Recipe4livingAccountController extends ClientFrontendController
{
	/**
	 *	Login page
	 *
	 *	@access public
	 */
	public function login()
	{
		// Get request
		$identifier = Request::getString('form_identifier');
		$password = Request::getString('form_password');
		$redirect = Request::getCmd('redirect');
		
		// Attempt login?
		$userModel = BluApplication::getModel('user');
		if ($password) {
			if ($identifier && ($userId = $userModel->getUserIdFromIdentifier($identifier))) {
				
				// Login
				if ($userModel->login($userId, $password)) {
					
					// Redirect to account, or wherever they last were
					return $this->_redirect($redirect ? base64_decode($redirect) : '/account');
				}
				
				// Legacy - users with no passwords, because no we can't invert md5 hashes, and even if we could we can't because we don't have anything to invert.
				$user = $userModel->getUser($userId);
				if (!$user['password']) {
					
					// We have an email address, lets spam it
					if ($user['email']) {
						$this->_sendReminder($userId);
						Messages::addMessage(Text::get('global_msg_forgotpass_sent'), 'info');
						
					// We have nothing, what are we supposed to do?!
					} else {
						Messages::addMessage(Text::get('global_msg_legacy_login_no_details'), 'error');
					}
					
				// Message
				} else {
					Messages::addMessage(Text::get('global_msg_incorrect_login'), 'error');
				}
				
			// Don't bother checking
			} else {
				Messages::addMessage(Text::get('global_msg_incorrect_login'), 'error');
			}
		}
		
		// Load template
		$this->_doc->setTitle('Login');
		include(BLUPATH_TEMPLATES.'/account/login.php');
	}
	
	/**
	 *	Logout and redirect to homepage
	 *
	 *	@access public
	 */
	public function logout()
	{
		// Logout
		$userModel = BluApplication::getModel('user');
		$userModel->logout();

		/* Clear session variables */
		Session::clear(array('messages'));

		// Redirect to homepage
		return $this->_redirect('/');
	}
	
	/**
	 *	Overview page
	 *
	 *	@access public
	 */
	public function view()
	{
		$tab = Request::getCmd('tab', 'details_basic');
		
		// Load template
		include(BLUPATH_TEMPLATES.'/account/details.php');
	}
	
	/**
	 * Account details: basic information tab.
	 */
	public function details_basic()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/account/details_basic';
			return $this->_redirect('/account/login?redirect='.base64_encode($url), Text::get('acc_login'), 'warn');
		}
		
		$pageHeading = 'My profile details';
	
		// Load fixed data
		$userModel = BluApplication::getModel('user');

		// Get user data, if possible, to prefill form
		$user = $userModel->getCurrentUser();

		// Get data from request, falling back to user info
		$queueId = Request::getString('queueid', md5(uniqid()));
		$displayname = Request::getString('form_display_name', $user['displayname']);
		$firstname = Request::getString('form_first_name', $user['firstname']);
		$lastname = Request::getString('form_last_name', $user['lastname']);
		$email = Request::getString('form_email', $user['email']);
		$location = Request::getString('form_location', $user['location']);
		$timezone = Request::getFloat('form_timezone', 'gmt');
		$about = Request::getString('form_about', $user['about']);
		$favouriteFoods = Request::getString('form_favourite_foods', $user['favouriteFoods']);
		$private = Request::getBool('form_private', $user['private']);
		
		$dob = $user['dob'];
		if(!empty($dob)) {
			list($year, $month, $day) = explode('-', $dob);
		} else {
			$year = $month = $day = null;
		}
		$dobDay = Request::getInt('form_dob_day', $day);
		$dobMonth = Request::getInt('form_dob_month', $month);
		$dobYear = Request::getInt('form_dob_year', $year);
		
		/*if(Request::getBool('submit')) {
			// Required fields
			if (!$username || !$firstName || !$lastName || !$email || !$password) {
				Messages::addMessage(Text::get('global_msg_complete_all_fields'), 'error');
				$errors = true;
				
			// Check username is not already in use
			} elseif ($userModel->isUsernameInUse($username)) {
				Messages::addMessage(Text::get('global_msg_username_in_use'), 'error');
				$errors = true;

			// Check for valid e-mail address
			} elseif (!Email::isEmailAddress($email)) {
				Messages::addMessage(Text::get('global_msg_enter_valid_email'), 'error');
				$errors = true;

			// Check e-mail address is not already in use
			} elseif ($userModel->isEmailInUse($email)) {
				Messages::addMessage(Text::get('global_msg_email_in_use'), 'error');
				$errors = true;

			// Check for valid password
			} elseif ($password != $password2) {
				Messages::addMessage(Text::get('form_password_not_match'), 'error');
				$errors = true;
			}
		}*/
		// Load template
		$monthNames = array('', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		include(BLUPATH_TEMPLATES.'/account/details/basic.php');
	}
	
	/**
	 * Save basic details after form submission
	 */
	public function details_basic_save()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/account/details_basic_save';
			return $this->_redirect('/account/login?redirect='.base64_encode($url), Text::get('acc_login_edit'), 'warn');
		}
		
		// Get model
		$userModel = BluApplication::getModel('user');

		// Get data from request
		$queueId = Request::getString('queueid');

		// Build arguments
		$args = array();
		$special = array();

		// Start validating
		$validation = array();

		// Names
		$lastname = Request::getString('form_last_name');
		$firstname = Request::getString('form_first_name');

		// Email address
		$email = Request::getString('form_email');
		
		$validation['email'] = $this->_validateWithMessage(
			$email,
			'validate-email',
			'Please enter a valid e-mail address.'
		) && ($email == $user['email'] || $this->_validateWithMessage(
			$email,
			'email_used',
			'Sorry, that e-mail already appears to be in use.'
		));
		if ($validation['email']){
			$email = $email;
		}

		// Check for consistent passwords
		$password = Request::getString('form_password');
		$password2 = Request::getString('form_password_confirm');
		if ($password == $password2 && $password != '') {
			$validation['password'] = $password && $password2 && $this->_validateWithMessage(
				array($password, $password2),
				'validate-passwordconfirm',
				'The passwords you entered did not match. Please check that you typed them correctly. (Remember that passwords are case sensitive.)'
			);
		}

		// Location
		$locationName = $location = Request::getString('form_location');
		/*$locationID = $this->_validateWithMessage(
			$locationName,
			'location',
			'You need to enter your nearest location.'
		);
		$validation['location'] = (bool) $locationID;
		if ($validation['location']){
			$args['locationID'] = $locationID;
		}*/
		
		// Not valid
		if (in_array(false, $validation)) {
			return $this->_showMessages('details_basic', 'view');
		}
		
		// Date of birth
		$dobDay = Request::getInt('form_dob_day');
		$dobMonth = Request::getInt('form_dob_month');
		$dobYear = Request::getInt('form_dob_year');
		$dob = null;
		if(!empty($dobDay) && !empty($dobMonth) && !empty($dobYear) && mktime(0,0,0,$dobMonth, $dobDay, $dobYear) !== false) {
			$dob = $dobYear . '-' . $dobMonth . '-' . $dobDay;
		}

		// Time zone
		$timezone = Request::getInt('form_timezone', 0);
		
		// About
		$about = Request::getString('form_about');
		
		// Favourite foods
		$favouriteFoods = Request::getString('form_favourite_foods');
			
		//$dob = Request::getString('form_dob', null);
		
		// Private?
		$private = Request::getInt('form_private', 0);

		// Save new password, if one has been provided
		if (!$validation['password']) {
			$password = null;
		}

		// Save users ARGS & user_info ARG
		$userModel->setEmailAddress($user['id'], $email);
		$userModel->editUser(array(
			'userId'			=> $user['id'],
			'type'              => $user['type'],
			'password' 			=> $password,
			'firstName'			=> $firstname,
			'lastName'			=> $lastname,
			'location'			=> $locationName,
			'about'				=> $about,
			'favouriteFoods'	=> $favouriteFoods,
			'dob'				=> $dob
		));
		$userModel->setPrivate($user['id'], $private);
		// Upload new photo (if any)
		$result = $this->_saveUpload($queueId, 'photoupload', false, array('png', 'jpg', 'jpeg', 'gif', 'bmp'));
		if (isset($result['error'])) {
			Messages::addMessage($result['error'], 'error');
			return $this->_showMessages('details_basic', 'details');
		}

		// Move uploaded photos to their correct location
		$assets = Upload::getQueue($queueId);
		if (!empty($assets)) {
			foreach ($assets as $uploadId => $file) {
				$userModel->setProfileImageFromUpload($user['id'], $uploadId, $file);
			}
			Upload::clearQueue($queueId);

		// Use default avatar
		} else {
			$avatar = Request::getString('avatar');
			$userModel->setProfileImage($user['id'], 'avatar'.$avatar.'.png');
		}

		// Redirect
		$this->_redirect('/account/details?tab=basic', 'Basic information updated.');
	}

	/**
	 *	Left navigation
	 *
	 *	@access public
	 *	@param array Links
	 */
	public function leftnav(array $links = array())
	{
		// Set title
		Template::set('leftNavTitle', 'My Kitchen');
		
		// Get links
		$links = $this->_getMyKitchenLinks();
		
		// Load template
		return parent::leftnav($links);
	}
	
	/**
	 *	Send password reminder - by email
	 *
	 *	@access public
	 */
	public function password_reminder()
	{
		// Get data from request
		$email = Request::getString('form_identifier');
		
		// Get user details
		$userModel = BluApplication::getModel('user');
		if ($user = $userModel->getUserByEmail($email)) {
			
			// Renew password
			$this->_sendReminder($user['id']);
			Messages::addMessage(Text::get('global_msg_forgotpass_sent'), 'info');
			
		// Can't find your email address, mate.
		} else {
			Messages::addMessage(Text::get('global_msg_email_not_registered', array('email' => htmlspecialchars($email))), 'error');
		}
		
		// View login page
		return $this->_redirect('/account/login');
	}
	
	/**
	 *	Send a password reminder for a user
	 *
	 *	@access protected
	 *	@param int User ID
	 *	@return bool Success
	 */
	protected function _sendReminder($userId)
	{
		// Get user
		$userModel = BluApplication::getModel('user');
		if (!$user = $userModel->getUser($userId)) {
			return false;
		}
		
		// Generate new password and apply
		$password = Utility::createRandomPassword();
		if (!$userModel->editUser($userId, $password)) {
			return false;
		}
		
		// Send email
		$email = new Email();
		$vars = array(
			'firstName' => $user['firstname'],
			'lastName' => $user['lastname'],
			'email' => $user['email'],
			'password' => $password
		);
		$email->addBcc('R4Lfpwemails@gmail.com', 'Recipe4living Forgot Password: '.$user['username']);
		//$email->addBcc('alan@blubolt.com', 'Recipe4living Forgot Password: '.$user['username']);
		return $email->quickSend($user['email'], $user['fullname'], Text::get('global_forgotpass_email_subject'), 'passwordreminder', $vars);
	}
	
	public function details_delete()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/account/details_delete';
			return $this->_redirect('/account/login?redirect='.base64_encode($url), Text::get('acc_login'), 'warn');
		}
		
		// Load template
		$pageHeading = 'Delete your account';
		include(BLUPATH_TEMPLATES.'/account/details/delete.php');
	}
	
	public function details_delete_save()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/account/details_delete_save';
			return $this->_redirect('/account/login?redirect='.base64_encode($url), Text::get('acc_login_delete'), 'warn');
		}
		$reason = Request::getString('reason');
		
		$userModel = BluApplication::getModel('user');
		if(!$userModel->deleteUser($user['id'], $reason)) {
			return $this->_redirect('/account/details?tab=delete', Text::get('global_delete_account_fail'), 'error');
		} else {
			Messages::addMessage(Text::get('global_delete_account_success'), 'info');
			return $this->logout();
		}
	}
	
	/**
	 *	Recipe Box
	 *
	 *	@access public
	 */
	public function recipe_box()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/account/recipe_box';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('acc_login'), 'warn');
		}
		
		// Load template
		include(BLUPATH_TEMPLATES.'/account/recipe_box.php');
	}
	
	/**
	 *	Recipe box items
	 *
	 *	@access public
	 */
	public function recipe_box_items()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/account/recipe_box';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('acc_login'), 'warn');
		}
		
		// Get recipe box items
		$recipes = array();
		if (!empty($user['saves']['recipebox'])) {
			$recipes = array_keys($user['saves']['recipebox']);
			$recipes = array_combine($recipes, $recipes);
		}
		
		// Get/set sort
		$sort = $this->_getSort();
		Session::set('sort', $sort);
		$itemsModel = BluApplication::getModel('items');
		$recipes = $itemsModel->sortItems($recipes, $sort);

		// Get/set layout and ordering
		$layout = $this->_getLayout();
		Session::set('layout', $layout);
		
		// Do display stuff
		$this->_view = 'recipe_box';
		
		$baseUrl = '/account/recipe_box';
		$pathway = $this->_getBreadcrumbs();
		$documentTitle = $listingTitle = 'My Recipe Box';
		$page = Request::getInt('page', 1);
		$limit = $this->_getLimit();
		
		return $this->_listItems($recipes, $page, $limit, $baseUrl, null, null, $sort, $layout, $pathway, $documentTitle, $listingTitle);
	}
	
	/**
	 *	Get title when filters are barren.
	 *
	 *	@access protected
	 *	@return string
	 */
	protected function _getEmptyFilterTitle()
	{
		return 'My Recipes';
	}
	
	/**
	 *	Shopping list
	 *
	 *	@access public
	 */
	public function shopping_list()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/account/shopping_list';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('acc_login'), 'warn');
		}
		
		// Load template
		include(BLUPATH_TEMPLATES.'/account/shopping_list.php');
	}
	
	/**
	 *	Shopping list items
	 *
	 *	@access public
	 */
	public function shopping_list_items()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/account/shopping_list';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('acc_login'), 'warn');
		}
		
		// Get shopping list items
		$recipes = array();
		if (!empty($user['saves']['shopping_list'])) {
			$recipes = array_keys($user['saves']['shopping_list']);
			$recipes = array_combine($recipes, $recipes);
		}
		
		// Get/set sort
		$sort = $this->_getSort();
		Session::set('sort', $sort);
		$itemsModel = BluApplication::getModel('items');
		$recipes = $itemsModel->sortItems($recipes, $sort);

		// Get/set layout and ordering
		$layout = $this->_getLayout();
		Session::set('layout', $layout);
		
		// Do display stuff
		$this->_view = 'shopping_list';
		
		$baseUrl = '/account/shopping_list';
		$pathway = $this->_getBreadcrumbs();
		$documentTitle = $listingTitle = 'My Shopping List';
		$page = Request::getInt('page', 1);
		$limit = $this->_getLimit();
		
		return $this->_listItems($recipes, $page, $limit, $baseUrl, null, null, $sort, $layout, $pathway, $documentTitle, $listingTitle);
	}
	
	/**
	 *	My recipes
	 *
	 *	@access public
	 */
	public function my_recipes()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/account/my_recipes';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('acc_login'), 'warn');
		}
		
		// Load template
		include(BLUPATH_TEMPLATES.'/account/my_recipes.php');
	}
	
	/**
	 *	My recipe items
	 *
	 *	@access public
	 */
	public function my_recipes_items()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/account/my_recipes';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('acc_login'), 'warn');
		}
		
		// Get user's submitted recipes
		$recipes = array();
		if (!empty($user['articles']['recipe'])) {
			$recipes = $user['articles']['recipe'];
		}
		
		// Get/set sort
		$sort = $this->_getSort();
		Session::set('sort', $sort);
		$itemsModel = BluApplication::getModel('items');
		$recipes = $itemsModel->sortItems($recipes, $sort);
		
		// Get/set layout and ordering
		$layout = $this->_getLayout();
		Session::set('layout', $layout);
		
		// Do display stuff
		$this->_view = 'my_recipes';
		
		$baseUrl = '/account/my_recipes';
		$pathway = $this->_getBreadcrumbs();
		$documentTitle = $listingTitle = 'My Recipes';
		$page = Request::getInt('page', 1);
		$limit = $this->_getLimit();
		
		return $this->_listItems($recipes, $page, $limit, $baseUrl, null, null, $sort, $layout, $pathway, $documentTitle, $listingTitle);
	}
	
	/**
	 *	My cookbooks
	 *
	 *	@access public
	 */
	public function my_cookbooks()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/account/my_cookbooks';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('acc_login'), 'warn');
		}
		
		// Load template
		$itemsTask = 'cookbooks_items';
		include(BLUPATH_TEMPLATES.'/cookbooks/cookbook_listing.php');
	}
	
	/**
	 *	My cookbooks items
	 *
	 *	@access public
	 */
	public function cookbooks_items()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/account/my_cookbooks';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('acc_login'), 'warn');
		}
		
		// Get user's submitted cookbooks...
		$itemsModel = BluApplication::getModel('items');
		$cookbooks = $itemsModel->getCookbooks();
		$cookbooks = $itemsModel->filterAuthorCookbooks($cookbooks, $user['id']);
		
		// Get/set sort
		$sort = $this->_getSort();
		Session::set('sort', $sort);
		$cookbooks = $itemsModel->sortCookbooks($cookbooks, $sort);
		
		// Get/set layout and ordering
		$layout = $this->_getLayout();
		Session::set('layout', $layout);
		
		// Do display stuff
		$baseUrl = '/account/my_cookbooks';
		$pathway = $this->_getBreadcrumbs();
		$documentTitle = $listingTitle = 'My cookbooks';
		$page = Request::getInt('page', 1);
		$limit = $this->_getLimit();
		$description = Text::get('acc_cookbook_description');
		
		$this->_view = 'my_cookbooks';
		return $this->_listItemGroups($cookbooks, $page, $limit, $baseUrl, null, null, $sort, $layout, $pathway, $documentTitle, $listingTitle, $description);
	}
	
	/**
	 *	My blog posts
	 *
	 *	@access public
	 */
	public function blog_posts()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/account/blogs';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('acc_login'), 'warn');
		}
		
		// Load template
		include(BLUPATH_TEMPLATES.'/blogs/posts.php');
	}
	
	/**
	 *	My blog post items
	 *
	 *	@access public
	 */
	public function blog_post_items()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/account/blog_posts';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('acc_login'), 'warn');
		}
		
		// Get user's submitted blogs
		$blogs = array();
		if (!empty($user['articles']['blog'])) {
			$blogs = $user['articles']['blog'];
		}
		
		// Get/set sort
		$sort = $this->_getSort();
		Session::set('sort', $sort);
		$itemsModel = BluApplication::getModel('items');
		$blogs = $itemsModel->sortItems($blogs, $sort);
		
		// Get/set layout and ordering
		$layout = $this->_getLayout();
		Session::set('layout', $layout);
		
		// Do display stuff
		$this->_view = 'blogs';
		
		$baseUrl = '/account/blog_posts';
		$pathway = $this->_getBreadcrumbs();
		$documentTitle = $listingTitle = 'My Blog Posts';
		$page = Request::getInt('page', 1);
		$limit = $this->_getLimit();
		
		return $this->_listItems($blogs, $page, $limit, $baseUrl, null, null, $sort, $layout, $pathway, $documentTitle, $listingTitle);
	}

	/**
	 *	Messages overview page.
	 */
	public function messages()
	{
		if (!$this->_requireUser('Please sign in to see your messages.')) {
			return false;
		}
		$user = BluApplication::getUser();

		// Add breadcrumb
		$breadcrumbs = BluApplication::getBreadcrumbs();
		$breadcrumbs->add('My Account', '/account/');
		$breadcrumbs->add('My Messages', '/account/messages');

		// Set page title
		$this->_doc->setTitle('My Messages');

		// Get folder
		$folder = Request::getCmd('folder', 'inbox');

		// Load current user's messages
		include(BLUPATH_TEMPLATES . '/account/messages.php');
	}

	/**
	 *	Messages overview: listing.
	 */
	public function messages_listing()
	{
		$folder = Request::getCmd('folder', 'inbox');
		$page = Request::getInt('page', 1);
		$limit = 20;
		$offset = ($page - 1) * $limit;

		// Get messages
		$user = BluApplication::getUser();
		$messagesModel = BluApplication::getModel('messages');
		$total = true;
		$messages = $messagesModel->getUserMessages($user['id'], $folder, $offset, $limit, $total);

		// Build pagination
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => '/account/messages/?folder='.$folder.'&amp;page='
		));

		// Load template
		include(BLUPATH_TEMPLATES.'/account/messages/listing.php');
	}

	/**
	 * Message detail page
	 */
	public function message()
	{
		if (!$user = $this->_requireUser('Please sign in view your messages.')) {
			return false;
		}

		// Get message ID
		if (!isset($this->_args[0])) {
			return $this->_redirect('/account/messages/');
		}
		$messageId = (int)$this->_args[0];

		// Get message and set as read
		$messagesModel = BluApplication::getModel('messages');
		$message = $messagesModel->getMessage($messageId, $user['id']);
		if (!$message) {
			return $this->_redirect('/account/messages/', 'Sorry, that message could not be found.');
		}
		$messagesModel->setRead($messageId, $user['id']);

		// Get message history
		$secondaryUserId = ($message['type'] == 'sent') ? $message['toID'] : $message['fromID'];
		$messageHistory = $messagesModel->getMessageHistory($user['id'], $secondaryUserId, $message['sent'], 0, 5);

		// What folder are we in?
		$folder = $message['type'];
		
		// Add breadcrumbs
		$breadcrumbs = BluApplication::getBreadcrumbs();
		$breadcrumbs->add('My Messages', '/account/messages/?folder='.$folder.'?folder=sent');
		$breadcrumbs->add($message['subject'], '/account/message/'.$messageId);

		// Set page title
		$this->_doc->setTitle($message['subject'].BluApplication::getSetting('titleSeparator').'My Messages');

		// Display message
		include(BLUPATH_TEMPLATES .'/account/message.php');
	}

	/**
	 * Messages write page
	 */
	public function write_message()
	{
		// Require logged-in user (the sender)
		if (!$user = $this->_requireUser('Please sign in to send a message.')) {
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('Please sign in to send a message.'), 'warn');
		}
		
		// get models
		$userModel = BluApplication::getModel('user');
		
		// Get data from request
		$subject = Request::getString('subject');
		$message = Request::getString('message');
		$replyId = Request::getInt('reply');
		$recipe = Request::getString('recipe');
		$recipeType = Request::getString('type');		

		// Reply to a message?
		$replyMessage = null;
		if ($replyId) {
			$messagesModel = BluApplication::getModel('messages');
			$replyMessage = $messagesModel->getMessage($replyId, $user['id']);
		} 
		else if($recipe && $recipeType){
			$subject = $user['username'].' has shared a recipe with you';
			$message = '<a href="'.SITEINSECUREURL.'/profile/'.$user['username'].'">'.$user['username'].'</a> has shared a recipe with you. To view click on the link.';
			$message .= '<a href="'.SITEINSECUREURL.'/'.$recipeType.'s/'.$recipe.'.htm"> View Recipe</a>'; 
		}
		// Send to specific user?
		else {
			$recipientUsername = end($this->_args);
			$recipientId = $userModel->getUserId($recipientUsername);
			if($recipientId) { 
				$recipientUser = $userModel->getUser($recipientId);
			}
		}

		// Got a message to reply to?
		if ($replyMessage) {
			$messageHistory = $messagesModel->getMessageHistory($user['id'], $replyMessage['sender']['id'], $replyMessage['sent']+1, 0, 5);

			// Set default subject
			if (!$subject) {
				$subject = 'RE: '.$replyMessage['subject'];
			}
		}

		// Add breadcrumbs
		$breadcrumbs = BluApplication::getBreadcrumbs();
		$breadcrumbs->add('My Messages', '/account/messages');
		$breadcrumbs->add('Write a message', '/account/write_message');

		// Set page title
		$this->_doc->setTitle('Write a message'.BluApplication::getSetting('titleSeparator').'My Messages');

		// Load the template
		include(BLUPATH_TEMPLATES . '/account/write_message.php');
	}

	/**
	 * Send a message
	 */
	public function write_message_send()
	{
		if (!$user = $this->_requireUser('Please sign in to send a message.')) {
			return false;
		}
		
		// get models
		$userModel = BluApplication::getModel('user');

		// Get data from request
		$recipients = Request::getVar('recipients');
		$recipientsbyname = Request::getVar('recipientsbyname');
		$subject = Request::getString('subject');
		$message = Request::getVar('message'); 
		if($recipientsbyname){
			$recipientId = $userModel->getUserId($recipientsbyname);
			if(!$recipientId){

			}
			$recipientUser = $userModel->getUser($recipientId);
			$recipients[] = $recipientUser['id'];
		}
		// Validate
		$validation = array();

		// Require at least one recipient
		$validation['recipients'] = $this->_validateWithMessage(
			$recipients,
			'required',
			'You must select at least one recipient.'
		);

		// Require message content
		$validation['message'] = $this->_validateWithMessage(
			array($subject, $message),
			'required',
			'You need to enter a subject and a message.'
		);

		// Show errors
		if (in_array(false, $validation)) {
			return $this->_showMessages('write_message', 'write_message');
		}

		// Send message(s)
		$messagesModel = BluApplication::getModel('messages');
		foreach ($recipients as $recipientId) {
			$messagesModel->sendMessage($user['id'], $recipientId, $subject, $message);
		}

		// Go to sent messages
		return $this->_redirect('/account/messages?folder=inbox', 'Your message has been sent.');
	}


	/**
	 *	Delete a message.
	 */
	public function delete_message()
	{
		if (!$user = $this->_requireUser('Please sign in to see your messages.')) {
			return false;
		}

		// Get data from request
		$messageId = Request::getInt('id');

		// Delete
		$messagesModel = BluApplication::getModel('messages');
		$deleted = $messagesModel->deleteMessage($messageId, $user['id']);

		// Display info message
		if ($deleted){
			Messages::addMessage('The message has been deleted.');
		} else {
			Messages::addMessage('The message could not be deleted, please try again.', 'error');
		}

		// Display inbox page
		return $this->messages();
	}
}

?>
