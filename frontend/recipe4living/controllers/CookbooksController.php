<?php

/**
 *	Cookbooks
 *
 *	@package BluApplication
 *	@subpackage FrontendControllers
 */
class Recipe4livingCookbooksController extends ClientFrontendController
{
	/**
	 *	Cookbook
	 *
	 *	@access protected
	 *	@var int
	 */
	protected $_cookbookId;
	
	/**
	 *	View
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_view = 'cookbook_listing';
	
	/**
	 *	Can refine cookbook recipes listings
	 *
	 *	@access protected
	 *	@var bool
	 */
	protected $_enableRefineSearch = true;
	
	/**
	 *	Constructor
	 *
	 *	@access public
	 *	@param array Arguments
	 */
	public function __construct($args)
	{
		parent::__construct($args);
		

		// We will disable the cookbooks on live site
		// Just direct to the homepage
		$this->_redirect('/');
		exit();
		// View article details? (.../*.htm)
		$arg = end($this->_args);
		$urlSuffix = BluApplication::getSetting('urlSuffix', 'htm');
		if (substr($arg, -(strlen($urlSuffix) + 1)) == '.'.$urlSuffix) {
			array_pop($this->_args);
			
			// Get slug delim pos (allow dash in slug, unless that's the replacement char)
			$slugReplacementChar = BluApplication::getSetting('slugReplacementChar', '_');
			$pos = ($slugReplacementChar == '_') ? strrpos($arg, '-') : strpos($arg, '-');
			if ($pos === false) {
				$pos = -4;
			}
			
			// Extract slug
			$slug = substr($arg, 0, $pos);
			
			// Map to cookbook
			$itemsModel = BluApplication::getModel('items');
			$this->_cookbookId = $itemsModel->getCookbookId($slug);
			
			// Override view type
			if ($cookbook = $itemsModel->getCookbook($this->_cookbookId)) {
				$this->_view = 'cookbook_details';
				
				// Use quick view?
				if (reset($this->_args) == 'quick_view') {
					$this->_view = array_shift($this->_args);
				}
			}
		}
		
		// Set this for top search
		Template::set('searchType', 'cookbooks');
		Template::set('searchTerm', Request::getString('searchterm'));
		Template::set('recipeslug', Request::getString('recipeslug'));
		Template::set('articleslug', Request::getString('articleslug'));
	}
	
	/**
	 *	View
	 *
	 *	@access public
	 */
	public function view()
	{
		// Get models
		$itemsModel = BluApplication::getModel('items');
		
		switch ($this->_view) {
			case 'cookbook_listing':
				
				// Load template
				$itemsTask = 'view_items';
				include(BLUPATH_TEMPLATES.'/cookbooks/cookbook_listing.php');
				break;
				
			case 'cookbook_details':
				
				// Get cookbook
				$cookbook = $itemsModel->getCookbook($this->_cookbookId);
				extract($cookbook);
				
				// Get display data
				$total = count($cookbook['values']);
				$showSearchExtra = false;	// ...or true? Hmm..
				
				// Load template
				include(BLUPATH_TEMPLATES.'/cookbooks/cookbook_details.php');
				break;
		}
	}
	
	/**
	 *	View a list of cookbooks
	 *
	 *	@access public
	 */
	public function view_items()
	{
		$itemsModel = BluApplication::getModel('items');
		
		// Get search
		$searchString = $searchTerm = Request::getString('searchterm');
		if ($searchTermExtra = Request::getString('searchterm_extra')) {
			$searchString .= ' '.$searchTermExtra;
		}
		
		// Get/set sort
		$sort = $this->_getSort();
		Session::set('sort', $sort);
		
		// Get cookbooks
		$cookbooks = $itemsModel->getCookbooks(null, null, $sort, $searchString);

		// Save search term
		if ($searchTerm || $searchTermExtra) {
			$numItems = count($cookbooks);
			
			$searchTermModel = BluApplication::getModel('searchterms');
			$searchTermModel->saveSearchTerm('cookbook', $searchTerm, $searchTermExtra, $numItems);
		}
		
		// Cut short
		$page = Request::getInt('page', 1);
		$limit = $this->_getLimit();
		
		// Get/set layout and ordering
		$layout = $this->_getLayout();
		Session::set('layout', $layout);
		
		// Do display stuff
		$baseUrl = '/cookbooks';
		$pathway = $this->_getBreadcrumbs();
		$documentTitle = $listingTitle = 'All cookbooks';
		
		// Display
		return $this->_listItemGroups($cookbooks, $page, $limit, $baseUrl, $searchTerm, $searchTermExtra, $sort, $layout, $pathway, $documentTitle, $listingTitle);
	}
	
	/**
	 *	View a cookbook's recipes
	 *
	 *	@access public
	 */
	public function view_recipes()
	{
		// Get cookbook
		$itemsModel = BluApplication::getModel('items');
		if (!$cookbook = $itemsModel->getCookbook($this->_cookbookId)) {
			return $this->_errorRedirect();
		}
		$user = BluApplication::getModel('user')->getCurrentUser();
		if($cookbook['private'] == 1 && $cookbook['author']['id'] != $user['id']) {
			Messages::addMessage(Text::get('cookbook_view_noperm'), 'warn');
			return $this->_redirect('/cookbooks');
		}
		if ($recipes = array_keys($cookbook['values'])) {
			$recipes = array_combine($recipes, $recipes);
		}
		
		// Search (damnit, this is intensive...), lets only do the "filter" search
		if ($searchTermExtra = Request::getString('searchterm_extra')) {
			$searchRecipes = $itemsModel->getItems(null, null, null, array(), $searchTermExtra);
			$recipes = array_intersect_key($recipes, $searchRecipes);
		}
		
		// Get/set sort
		$sort = $this->_getSort();
		Session::set('sort', $sort);
		$recipes = $itemsModel->sortItems($recipes, $sort);
		
		// Get/set layout and ordering
		$layout = $this->_getLayout();
		Session::set('layout', $layout);
		
		// Set as referer
		Session::set('referer', $cookbook['link']);
		
		// Do display stuff
		$pathway = $this->_getBreadcrumbs();
		$documentTitle = $cookbook['title'].' | Cookbooks';
		$listingTitle = $cookbook['title'];
		$description = $cookbook['description'];
		$page = Request::getInt('page', 1);
		$format = Request::getVar('format');
		$limit = ($format == 'print')?null:1;
		//$limit = 1;//trying to make it book//$this->_getLimit();
		
		return $this->_listItems($recipes, $page, $limit, $cookbook['link'], null, $searchTermExtra, $sort, $layout, $pathway, $documentTitle, $listingTitle, $description);
	}
	
	/**
	 *	Get title when filters are barren.
	 *
	 *	@access protected
	 *	@return string
	 */
	protected function _getEmptyFilterTitle()
	{
		return 'All cookbooks';
	}
	
	/**
	 *	My favorite cookbooks
	 *
	 *	@access public
	 */
	public function favorites()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/cookbooks/favorites';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('cookbook_fav_view_login'), 'warn');
		}
		
		// Load template
		$itemsTask = 'favorites_items';
		include(BLUPATH_TEMPLATES.'/cookbooks/cookbook_listing.php');
	}
	
	/**
	 *	My favorite cookbooks items
	 *
	 *	@access public
	 */
	public function favorites_items()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/cookbooks/favorites';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('cookbook_fav_view_login'), 'warn');
		}
		
		$itemsModel = BluApplication::getModel('items');
		
		// Get user's favourite cookbooks.
		$cookbooks = array();
		if (isset($user['saves']['cookbook'])) {
			$cookbooks = array_keys($user['saves']['cookbook']);
			$cookbooks = array_combine($cookbooks, $cookbooks);
		}
		
		// Get/set sort
		$sort = $this->_getSort();
		Session::set('sort', $sort);
		$cookbooks = $itemsModel->sortCookbooks($cookbooks, $sort);
		
		// Get/set layout and ordering
		$layout = $this->_getLayout();
		Session::set('layout', $layout);
		
		// Do display stuff
		$baseUrl = '/cookbooks/favorites';
		$pathway = $this->_getBreadcrumbs();
		$documentTitle = $listingTitle = Text::get('cookbook_fav_title');
		$page = Request::getInt('page', 1);
		$limit = $this->_getLimit();
		$description = Text::get('cookbook_fav_desc');
		
		$this->_view = 'cookbook_listing';
		return $this->_listItemGroups($cookbooks, $page, $limit, $baseUrl, null, null, $sort, $layout, $pathway, $documentTitle, $listingTitle, $description);
	}
	
	/**
	 *	Create a cookbook
	 *
	 *	@access public
	 */
	public function create()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/cookbooks/create';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('cookbook_create_login'), 'warn');
		}
		
		// Get previous request
		$id = null;
		$title = Request::getString('title');
		$description = Request::getString('description');
		$private = Request::getBool('private', false);
		$image = null;
		$queueId = Request::getString('queueid', md5(uniqid()));
		
		// Load template
		Template::set('pageTitle', Template::text('cookbook_create_title'));
		Template::set('pageDescription', Template::text('cookbook_create_desc'));
		Template::set('pageButtonText', Template::text('cookbook_create_button'));
		$taskUrl = '/cookbooks/create_save';
		include(BLUPATH_TEMPLATES.'/cookbooks/create.php');
	}
	
	/**
	 *	Create a cookbook
	 *
	 *	@access public
	 */
	public function create_save()
	{
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = '/cookbooks/create_save';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('cookbook_create_login'), 'warn');
		}
		
		// Got summat t'save
		if (Request::getBool('submit')) {
			
			// Get request
			if ($title = Request::getString('title')) {
				$description = Request::getString('description');
				$private = Request::getBool('private', false);
				
				// Save cookbook
				$itemsModel = BluApplication::getModel('items');
				if ($cookbookId = $itemsModel->addCookbook($user['id'], $title, $description, null, $private)) {
					$cookbook = $itemsModel->getCookbook($cookbookId);
					
					// Do image
					if ($image = Request::getFile('image')) {
						$itemsModel->addCookbookImage($cookbook['id'], $image);
					}
					
					// Redirect
					return $this->_redirect($cookbook['link'], Text::get('cookbook_add_success', array('cookbook' => $cookbook['title'])));
					
				// Phail
				} else {
					Messages::addMessage(Text::get('cookbook_add_fail'), 'error');
				}
				
			// Phail
			} else {
				Messages::addMessage(Text::get('cookbook_add_title'), 'warn');
			}
		}
		
		// Return
		return $this->create();
	}
	
	/**
	 *	Edit a cookbook
	 *
	 *	@access public
	 */
	public function edit()
	{
		$itemsModel = BluApplication::getModel('items');
		
		// Get cookbook
		if (!$cookbook = $itemsModel->getCookbook($this->_cookbookId)) {
			return $this->_errorRedirect();
		}
		
		// Check credentials
		if (!$cookbook['canEdit']) {
			Messages::addMessage(Text::get('cookbook_edit_noperm'), 'warn');
			return $this->view();
		}
		
		// Get previous request, falling back to original cookbook
		$title = Request::getString('title', $cookbook['title']);
		$description = Request::getString('description', $cookbook['description']);
		$private = Request::getBool('private', $cookbook['private']);
		$image = $cookbook['image']['filename'];
		$queueId = Request::getString('queueid', md5(uniqid()));
		
		// Load template
		Template::set('pageTitle', Template::text('cookbook_edit_title'));
		Template::set('pageDescription', Template::text('cookbook_edit_desc'));
		Template::set('pageButtonText', Template::text('cookbook_edit_button'));
		
		$taskUrl = $itemsModel->getTaskLink($cookbook['link'], 'edit_save');
		include(BLUPATH_TEMPLATES.'/cookbooks/create.php');
	}
	
	/**
	 *	Edit a cookbook
	 *
	 *	@access public
	 */
	public function edit_save()
	{
		$itemsModel = BluApplication::getModel('items');
		
		// Get cookbook
		if (!$cookbook = $itemsModel->getCookbook($this->_cookbookId)) {
			return $this->_errorRedirect();
		}
		
		// Check credentials
		if (!$cookbook['canEdit']) {
			Messages::addMessage(Text::get('cookbook_edit_noperm'), 'warn');
			return $this->view();
		}
		
		// Save changes
		if (Request::getBool('submit')) {
			$error = false;
			
			// Get data
			$title = Request::getString('title');
			$description = Request::getString('description');
			$private = Request::getBool('private', false);
	
			// Validate
			if (!$title) {
				Messages::addMessage(Text::get('cookbook_add_title'), 'warn');
				$error = true;
				
			// Update
			} else if ($itemsModel->updateCookbook($cookbook['id'], $title, $description, null, $private)) {
				Messages::addMessage(Text::get('cookbook_edit_success'));
				
				// Do image
				if ($image = Request::getFile('image')) {
					foreach ($cookbook['images'] as $filename => $image) {
						$itemsModel->deleteCookbookImage($cookbook['id'], $filename);
					}
					
					// Replace with new
					$itemsModel->addCookbookImage($cookbook['id'], $image);
				}
				
			// Phail
			} else {
				Messages::addMessage(Text::get('cookbook_edit_fail'), 'error');
				$error = true;
			}
			
			// Redirect to form
			if ($error) {
				return $this->_showMessages('edit', 'edit');
			}
		}
		
		// Display cookbook
		return $this->_showMessages();
	}
	
	/**
	 *	Delete a cookbook
	 *
	 *	@access public
	 */
	public function delete()
	{
		$itemsModel = BluApplication::getModel('items');
		
		// Get cookbook
		if (!$cookbook = $itemsModel->getCookbook($this->_cookbookId)) {
			return $this->_errorRedirect();
		}
		
		// Check credentials
		if (!$cookbook['canEdit']) {
			Messages::addMessage(Text::get('cookbook_delete_noperm'), 'warn');
			return $this->view();
		}
		
		// Load template
		$taskUrl = $itemsModel->getTaskLink($cookbook['link'], 'delete_confirm');
		include(BLUPATH_TEMPLATES.'/cookbooks/delete.php');
	}
	
	/**
	 *	Delete a cookbook
	 *
	 *	@access public
	 */
	public function delete_confirm()
	{
		$itemsModel = BluApplication::getModel('items');
		
		// Get cookbook
		if (!$cookbook = $itemsModel->getCookbook($this->_cookbookId)) {
			return $this->_errorRedirect();
		}
		
		// Check credentials
		if (!$cookbook['canEdit']) {
			Messages::addMessage(Text::get('cookbook_delete_noperm'), 'warn');
			return $this->view();
		}
		
		// Delete cookbook
		if (!$itemsModel->deleteCookbook($cookbook['id'])) {
			return $this->_redirect('/account/my_cookbooks', Text::get('cookbook_delete_fail'), 'error');
		}
		
		// Redirect to listings
		return $this->_redirect('/cookbooks', Text::get('cookbook_delete_success', array('cookbook' => $cookbook['title'])));
	}
	
	/**
	 *	Add a recipe to the current cookbook
	 *
	 *	@access public
	 */
	public function add_recipe()
	{
		$itemsModel = BluApplication::getModel('items');
		
		// Get cookbook
		if (!$cookbook = $itemsModel->getCookbook($this->_cookbookId)) {
			return $this->_errorRedirect();
		}
		
		// Check credentials
		if (!$cookbook['canEdit']) {
			Messages::addMessage(Text::get('cookbook_edit_noperm'), 'warn');
			return $this->view();
		}
		
		// Get recipe
		$itemSlug = reset($this->_args);
		if (!$itemId = $itemsModel->getItemId($itemSlug)) {
			return $this->_errorRedirect();
		} else if (!$item = $itemsModel->getItem($itemId)) {
			return $this->_errorRedirect();
		}
		
		// Get extra comments, if any
		$comment = Request::getString('comment');
		
		// Add recipe to cookbook
		if ($itemsModel->addCookbookRecipe($cookbook['id'], $item['id'], $comment)) {
			return $this->_redirect($cookbook['link'], Text::get('cookbook_add_recipe_success', array(
				'cookbook' => $cookbook['title'],
				'recipe' => $item['title']
			)));
			
		// Phail, go back to recipe to try again
		} else {
			return $this->_redirect($item['link'], Text::get('cookbook_add_recipe_fail'), 'error');
		}
	}
	
	/**
	 *	Remove a recipe from the current cookbook
	 *
	 *	@access public
	 */
	public function delete_recipe()
	{
		$itemsModel = BluApplication::getModel('items');
		
		// Get cookbook
		if (!$cookbook = $itemsModel->getCookbook($this->_cookbookId)) {
			return $this->_errorRedirect();
		}
		
		// Check credentials
		if (!$cookbook['canEdit']) {
			Messages::addMessage(Text::get('cookbook_edit_noperm'), 'warn');
			return $this->view();
		}
		
		// Get recipe
		$itemSlug = reset($this->_args);
		if (!$itemId = $itemsModel->getItemId($itemSlug)) {
			return $this->_errorRedirect();
		} else if (!$item = $itemsModel->getItem($itemId)) {
			return $this->_errorRedirect();
		}
		
		// Delete recipe from cookbook
		if ($itemsModel->deleteCookbookRecipe($cookbook['id'], $item['id'])) {
			Messages::addMessage(Text::get('cookbook_delete_recipe_success', array(
				'recipe' => $item['title']
			)));
		} else {
			Messages::addMessage(Text::get('cookbook_delete_recipe_fail'), 'error');
		}
		
		// Redirect to cookbook
		return $this->view();
	}
	
	/**
	 *	Save the cookbook against the current user
	 *
	 *	@access public
	 */
	public function add_favorite()
	{
		$itemsModel = BluApplication::getModel('items');
		
		// Get cookbook
		if (!$cookbook = $itemsModel->getCookbook($this->_cookbookId)) {
			return $this->_errorRedirect();
		}
		
		// Get user
		if (!$user = $this->_requireUser()) {
			$url = $itemsModel->getTaskLink($cookbook['link'], __FUNCTION__);
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('cookbook_fav_add_login', array('cookbook' => $cookbook['title'] )), 'warn');
		}
		
		// Get remainder of request
		$comment = Request::getString('comment');
		
		// Save cookbook against user
		$userModel = BluApplication::getModel('user');
		if ($userModel->saveCookbook($cookbook['id'], $user['id'], $comment)) {
			return $this->_redirect('/cookbooks/favorites', Text::get('cookbook_fav_add_success', array('cookbook' => $cookbook['title'])));
			
		// Fail
		} else {
			Messages::addMessage(Text::get('cookbook_fav_add_fail', array('cookbook' => $cookbook['title'])), 'error');
			return $this->_showMessages();
		}
	}
	
	/**
	 *	Remove the cookbook from the current user
	 *
	 *	@access public
	 */
	public function remove_favorite()
	{
		$itemsModel = BluApplication::getModel('items');
		
		// Get cookbook
		if (!$cookbook = $itemsModel->getCookbook($this->_cookbookId)) {
			return $this->_errorRedirect();
		}
		
		// Get user
		if (!$user = $this->_requireUser()) {
			$url = $itemsModel->getTaskLink($cookbook['link'], __FUNCTION__);
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('cookbook_fav_del_login', array('cookbook' => $cookbook['title'] )), 'warn');
		}
		
		// Save cookbook against user
		$userModel = BluApplication::getModel('user');
		if ($userModel->removeCookbook($cookbook['id'], $user['id'])) {
			return $this->_redirect('/cookbooks/favorites', Text::get('cookbook_fav_del_success', array('cookbook' => $cookbook['title'])));
			
		// Fail
		} else {
			Messages::addMessage(Text::get('cookbook_fav_del_fail', array('cookbook' => $cookbook['title'])), 'error');
			return $this->_showMessages();
		}
	}

	/**
	 *	My Kitchen left-nav
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
}

?>
