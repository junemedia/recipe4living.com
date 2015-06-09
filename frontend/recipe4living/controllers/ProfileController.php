<?php

/**
 * Users Controller
 *
 * @package BluApplication
 * @subpackage FrontendControllers
 */
class Recipe4livingProfileController extends ClientFrontendController
{
	/**
	 *	Viewed user's ID
	 *
	 *	@access protected
	 *	@var int
	 */
	protected $_userId = null;

	/**
	 *	Is self?
	 *
	 *	@access protected
	 *	@var bool
	 */
	protected $_self;

	/**
	 *	Is friend?
	 *
	 *	@access protected
	 *	@var bool
	 */
	protected $_friend;

	/**
	 *	Constructor
	 *
	 *	@access public
	 *	@param array Arguments
	 */
	public function __construct($args)
	{
		parent::__construct($args);

		// Get model
		$userModel = BluApplication::getModel('user');

		// Store base URL
		$this->_baseUrl = '/'.implode('/', $this->_args);
		// Set this for top search
		Template::set('searchType', 'profile');
		Template::set('searchTerm', Request::getString('searchterm'));
		Template::set('recipeslug', Request::getString('recipeslug'));
		Template::set('articleslug', Request::getString('articleslug'));
		// Get viewed user
/*		if($username = Request::getVar('searchterm')){
			if ($userId = $userModel->getUserId($username)) {
				$this->_userId = $userId;
			}
		}
		else*/
		if ($username = end($this->_args)) {
			$arg = explode('.', $username);
			if ($userId = $userModel->getUserId(reset($arg))) {
				$this->_userId = $userId;
			}
		}

		// Check relationship with logged in user
		if ($currentUser = $userModel->getCurrentUser()) {

			// Self?
			$this->_self = $currentUser['id'] == $this->_userId;

			// Friend?
			// Not implemented.
			$this->_friend = false;
		}
	}

	/**
	 *	View a user's profile
	 *
	 *	@access public
	 */
	public function view()
	{
		// Get model
		$userModel = BluApplication::getModel('user');

		// Get user being viewed
		if ((!$user = $userModel->getUser($this->_userId)) || Request::getVar('searchterm')) {
			return $this->view_users();
		}

		// Check permissions
		$currentUser = $userModel->getCurrentUser();
		if ($user['private'] && !$this->_friend && !($currentUser && $currentUser['type'] == 'admin')) {
			Messages::addMessage('You do not have permission to view this Profile', 'warn');
			return $this->view_users();
		}

		// Load users' recipes
		// set up pagination
		$page = Request::getInt('page', 1);
		$viewall = Request::getBool('viewall', false);
		$limit = $viewall ? null : 4;
		$offset = $viewall ? 0 : (($page - 1) * $limit);
		$articles = $userModel->getArticlesForUser($user, 'recipe', $offset, $limit);

		// Get base URLs for listing updates
		$baseUrl = '';

		$paginationBaseUrl = $baseUrl.'?page=';

		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => count($user['articles']['recipe']),
			'current' => $page,
			'url' => $paginationBaseUrl
		));

		// Set document meta
		$this->_doc->setTitle(($this->_self ? 'My' : $user['fullname'].'\'s').' Profile');
		$this->_doc->setDescription($user['about']);
		$this->_doc->setKeywords(implode(', ', explode(' ', $user['fullname'])));
		$this->_doc->setBreadcrumbs(array(
			array($user['fullname'], $this->_baseUrl)
		));

		// Load template
		Template::set('self', $this->_self);
		include(BLUPATH_TEMPLATES.'/profile/details.php');
	}


/**
	 *	View a list of profiles
	 *
	 *	@access public
	 */
	public function view_users()
	{
		// Display
		// Load template
		Template::set('self', $this->_self);
		include(BLUPATH_TEMPLATES.'/profile/listing.php');
	}

	private function _listUsers(){
		$userModel = BluApplication::getModel('user');

		// Get search
		$searchTerm = Request::getVar('searchterm');

		// Get/set sort
		$sort = $this->_getSort();
		Session::set('sort', $sort);
		$showSearch = true;
		// Cut short
		$page = Request::getInt('page', 1);
		$limit = $this->_getLimit();
		$offset = ($page - 1) * $limit;
		// Get Users
		$total = 0;
		$usersIds = $userModel->getUsersLike($offset, $limit, $sort, $searchTerm,$total);
		$currentUser = $userModel->getCurrentUser();

		foreach($usersIds as $id => $user){
			$details = $userModel->getUser($id);
			if($details['private'] == '0' && $details['deleted'] == '0'){
				$users[$id] = $details;
			}
			else if ($details['private'] && !$this->_friend && ($currentUser && $currentUser['type'] == 'admin')) {
				$users[$id] = $details;
			}else{
				$total--;
			}
		}

		$searchBaseUrl = $searchTerm ? '&amp;searchterm='.urlencode($searchTerm) : '';
		// Get pagination values
		if($total == 0){
			Messages::addMessage('This user does not exist', 'warn');
		}
		$start = $offset + 1;
		$end = min($offset + $limit, $total);

		// Get base URLs for listing updates
		$paginationBaseUrl = '/search?controller=profile'.$searchBaseUrl.'&amp;page=';

		// Do pagination
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => $paginationBaseUrl
		));
		// Do display stuff
		$documentTitle = $listingTitle = (!empty($searchTerm))?$searchTerm:'All Users';
		$description = '';
		include(BLUPATH_TEMPLATES.'/profile/users/heading.php');
		include(BLUPATH_TEMPLATES.'/profile/users/list.php');
	}

	/**
	 *	Left navigation
	 *
	 *	@access public
	 *	@param array Links
	 */
	public function leftnav(array $links = array())
	{
		return parent::leftnav(array_merge($this->_getRecipeCategoryLinks(), $links));
	}

	/**
	 *	RSS feeds
	 *
	 *	@access public
	 */
	public function rss()
	{
		// Get model
		$userModel = BluApplication::getModel('user');

		// Get user being viewed
		if (!$user = $userModel->getUser($this->_userId)) {
			Messages::addMessage('This user does not exist', 'warn');
			return $this->view_users();
		}

		// Check permissions
		$currentUser = $userModel->getCurrentUser();
		if ($user['private'] && !$this->_friend && !($currentUser && $currentUser['type'] == 'admin')) {
			Messages::addMessage('You do not have permission to view this Profile', 'warn');
			return $this->view_users();
		}

		$channel = Request::getString('channel','recipes');

		switch($channel) {

			case 'recipes':
				$page = 1;
				$limit = 10;
				$offset = (($page - 1) * $limit);
				$userRecipes = $userModel->getArticlesForUser($user, 'recipe', $offset, $limit);
				$userRecipesLink = '/profile/'.urlencode($user['username']);
				$userRecipesTitle = $user['fullname'].'\'s Recipes';
				$userRecipesDescription = 'Recipes submitted by '.$user['fullname'].'.';
				break;
			case 'reviews':
				$itemsModel = BluApplication::getModel('items');
				$userComments = $itemsModel->getUserComments($this->_userId);
				$userCommentsTitle = $user['fullname'].'\'s Reviews';
				$userCommentsDescription = 'Reviews submitted by '.$user['fullname'].'.';
				break;
			case 'images':
				$assetsModel = BluApplication::getModel('assets');
				$userArticleImages = $assetsModel->getUserArticleImages($this->_userId);
				$userArticleImagesTitle = $user['fullname'].'\'s Images';
				$userArticleImagesDescription = 'Images submitted by '.$user['fullname'].'.';
				break;
			case 'blogposts':
				$page = 1;
				$limit = 10;
				$offset = (($page - 1) * $limit);
				$userBlogPosts = $userModel->getArticlesForUser($user, 'blog', $offset, $limit);
				$userBlogPostsTitle = $user['fullname'].'\'s Blog Posts';
				$userBlogPostsLink = '/blogs/posts/'.$user['username'];
				$userBlogPostsDescription = 'Blog posts submitted by '.$user['fullname'].'.';
				break;
			default:
				return $this->_errorRedirect();

		}

		$locale = 'en-us';	// Bodge

		$this->_doc->setFormat('xml');
		include(BLUPATH_TEMPLATES.'/profile/rss/'.$channel.'.php');

	}

}

?>
