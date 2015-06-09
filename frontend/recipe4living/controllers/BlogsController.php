<?php

/**
 *	Blogs Controller
 *
 *	@package BluApplication
 *	@subpackage FrontendControllers
 */
class Recipe4livingBlogsController extends Recipe4livingArticlesController
{
	/**
	 *	Default view
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_view = 'blog_listing';

	/**
	 *	Current item type
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_itemType = 'blog';
	
	/**
	 *	Prepend to base URL
	 *
	 *	@access public
	 *	@param array Arguments
	 */
	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->_baseUrl = '/blogs/'.implode('/', $args);

		if ($this->_view == 'blog_listing') {
			Template::set(array(
				'rssUrl' => '/rss/'.implode('/', $args),
				'rssTitle' => $this->_getTitle('listingTitle')
			));
		}
		
		Template::set('searchType', 'blogs');
	}
	
	/**
	 *	Get title when filters are barren.
	 *
	 *	@access protected
	 *	@return string
	 */
	protected function _getEmptyFilterTitle()
	{
		return 'All blog posts';
	}

	/**
	 *	RSS Feeds
	 *
	 *	@access public
	 */
	public function rss()
	{
		$this->_doc->setFormat('xml');
		$this->_view = 'blog_rss';
		return $this->view();
	}

	/**
	 *	User's blog posts
	 *
	 *	@access public
	 */
	public function posts()
	{
		// Load template
		include(BLUPATH_TEMPLATES.'/blogs/posts.php');
	}
	
	/**
	 *	User's blog post items
	 *
	 *	@access public
	 */
	public function blog_post_items()
	{
		$userModel = BluApplication::getModel('user');
		
		$username = end($this->_args);
		$userId = $userModel->getUserId($username);
		if (!$userId) {
			$url = '/blogs';
			return $this->_redirect($url);
		}
		$user = $userModel->getUser($userId);
		
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
		$documentTitle = $listingTitle = $user['fullname'].'\'s Blog Posts';
		$page = Request::getInt('page', 1);
		$limit = $this->_getLimit();
		
		return $this->_listItems($blogs, $page, $limit, $baseUrl, null, null, $sort, $layout, $pathway, $documentTitle, $listingTitle);
	}
	
	/**
	 *	Advanced search
	 *
	 *	@access public
	 */
/*
	public function advanced_search()
	{
		// Do redirect
		if (Request::getBool('submit')) {
			$url = '';
			
			// Build redirect URL
			switch (Request::getString('searchtype')) {
				case 'cookbook':
					$url .= '/cookbooks';
					break;
					
				case 'recipe':
				default:
					
					// Add meta filter slugs
					if ($metaFilters = Request::getArray('filters')) {
						$metaFilters = array_filter($metaFilters);
					}
					if (empty($metaFilters)) {
						$url .= '/recipes';
					} else {
						sort($metaFilters);
						foreach ($metaFilters as $slug) {
							$url .= '/'.$slug;
						}
					}
					break;
			}
			
			$url .= '?advanced=1';	// Such a bodge....
			if ($searchTerm = Request::getString('searchterm')) {
				$url .= '&searchterm='.urlencode($searchTerm);
			}
			
			// Kthxbai
			return $this->_redirect($url);
		}
		
		// Get meta filters - this is something of a cludge.
		$metaModel = BluApplication::getModel('meta');
		$hierarchy = $metaModel->getHierarchy();
		$filters = array();	
		foreach ($hierarchy[2]['values'] as $filterId => $subHierarchy) {
			if (!empty($subHierarchy['values'])) {
				$filters[$filterId] = $subHierarchy;
			}
		}
		
		// Load template
		$this->_doc->setTitle('Advanced Search');
		include(BLUPATH_TEMPLATES.'/recipes/advanced_search.php');
	}
*/
	/**
	 *	Recipe count for given filters
	 *
	 *	@access public
	 */
/*
	public function advanced_search_count()
	{
		// Prepare
		$itemsModel = BluApplication::getModel('items');
		$count = null;
		$searchTerm = Request::getString('searchterm');
		
		// Get thingymabobs
		switch (Request::getString('searchtype')) {
			case 'cookbook':
				
				// Get all cookbooks
				$cookbooks = $itemsModel->getCookbooks(null, null, null, $searchTerm);
				
				// Return
				$count = count($cookbooks);
				break;
				
			case 'recipe':
			default:
				
				// Apply filters, if given
				if ($metaFilters = Request::getArray('filters')) {
					$metaFilters = array_filter($metaFilters);
				}
				if (empty($metaFilters)) {
					$metaFilters = array();	// So that it fits in with ItemsModel::getItems.
				} else {
					
					// Parse out filter IDs
					$metaModel = BluApplication::getModel('meta');
					$metaModel->applyFilters($metaFilters);
					$metaFilters = $metaModel->getFilters();
				}
				
				// Get recipes
				$items = $itemsModel->getItems(null, null, null, $metaFilters, $searchTerm);
				$items = $itemsModel->filterTypeItems($items, 'recipe');
				
				// Return
				$count = count($items);
				break;
		}
		
		// Output
		echo json_encode($count);
	}
*/
}

?>