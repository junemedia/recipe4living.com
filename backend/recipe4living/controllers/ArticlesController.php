<?php

/**
 *	Articles controller
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class Recipe4livingArticlesController extends ClientBackendController
{
	/**
	 *	Item view type
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_view = 'article_listing';

	/**
	 *	Requested base url
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_baseUrl = null;
	
	/**
	 *	Show all articles
	 *
	 *	@access protected
	 *	@var bool
	 */
	protected $_showAll = false;

	/**
	 *	Show search form?
	 *
	 *	@access protected
	 *	@var bool
	 */
	protected $_showSearch = false;

	/**
	 *	Current item id
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_itemId = null;
	
	/**
	 *	Current item type
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_itemType = 'article';
	
	/**
	 *	Current page number
	 *
	 *	@access protected
	 *	@var int
	 */
	protected $_page = 1;
	
	/**
	 *	Menu slug
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_menuSlug = 'article_listing';
	
	/**
	 *	Send confirmation email to item author when setting item live
	 *
	 *	@access protected
	 *	@var bool
	 */
	protected $_sendSubmissionEmail = false;
	
	/**
	 *	Constructor
	 *
	 *	@access public
	 *	@param array Arguments
	 */
	public function __construct($args)
	{
		parent::__construct($args);
		
		// Store base URL
		$this->_baseUrl = '/articles/'.implode('/', $this->_args);
		
		// Show search form? (/search/?)
		$arg = end($this->_args);
		$this->_showSearch = (($arg == 'search') || Request::getBool('searchterm'));
		if ($arg == 'search') {
			array_pop($this->_args);
		}

		// Show all articles? (.../_all)
		$arg = end($this->_args);
		if ($arg == '_all') {
			array_pop($this->_args);
			$this->_showAll = true;
		}
		
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
			
			// Map to item ID
			$itemsModel = BluApplication::getModel('items');
			if (($this->_itemId = $itemsModel->getItemId($slug)) && ($item = $itemsModel->getItem($this->_itemId))) {
				
				// Override view type
				switch ($item['type']) {
					case 'article':
						$this->_view = 'article_details';
						break;
						
					case 'recipe':
						$this->_view = 'recipe_details';
						break;
						
					case 'quicktip':
						$this->_view = 'quicktip_details';
						break;
						
					case 'blog':
						$this->_view = 'blog_details';
						break;
				}
			}
		}
	}
	
	/**
	 *	List all items.
	 *
	 *	@access public
	 */
	public function view()
	{
		if(Request::getBool('clear')) {
			switch($this->_itemType) {
				case 'article':
					$redirectUrl = '/articles';
					break;
				case 'recipe':
					$redirectUrl = '/recipes';
					break;
				case 'blog':
					$redirectUrl = '/blogs';
					break;
				case 'quicktip':
					$redirectUrl = '/quicktips';
					break;
			}
			return $this->_redirect($redirectUrl);
		}
		
		// Get models
		$itemsModel = BluApplication::getModel('items');
		$metaModel = BluApplication::getModel('meta');
		
		// Get breadcrumbs
		//$breadcrumbs = $this->_getBreadcrumbs();
		
		// Load data
		switch ($this->_view) {
			case 'article_listing':
			case 'recipe_listing':
			case 'quicktip_listing':
			case 'blog_listing':
				
				// Set document meta
				$this->_doc->setTitle($this->_getTitle('pageTitle'));
				//$this->_doc->setBreadcrumbs($breadcrumbs);
				break;
				
			case 'article_details':
			case 'recipe_details':
				
				// Get article
				if (!$item = $itemsModel->getItem($this->_itemId)) {
					return $this->_errorRedirect();
				}
				
				// Set document meta
				$this->_doc->setTitle($item['title']);
				//$breadcrumbs[] = array($item['title'], $item['link']);
				//$this->_doc->setBreadcrumbs($breadcrumbs);
				break;
		}
		
		// Load view
		switch ($this->_view) {
			case 'article_listing':
			case 'recipe_listing':
			case 'quicktip_listing':
			case 'blog_listing':
				include(BLUPATH_TEMPLATES.'/articles/article_listing.php');
				break;
				
			case 'article_details':
			case 'recipe_details':
				include(BLUPATH_TEMPLATES.'/articles/article_details.php');
				break;
		}
	}
	
	/**
	 *	Article listing
	 *
	 *	@access public
	 */
	public function view_items()
	{
		// Get requested options
		$page = Request::getInt('page', 1);
		$viewall = ISBOT ? true : Request::getBool('viewall', false);
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$format = $this->_doc->getFormat();
		
		// Get models
		$itemsModel = BluApplication::getModel('items');
		$metaModel = BluApplication::getModel('meta');
		
		// Get breadcrumbs, set document title
		//$pathway = $this->_getBreadcrumbs();
		$this->_doc->setTitle($this->_getTitle('pageTitle'));
		
		// Get filter info
		//$filterInfo = $metaModel->getDisplayInfo();
		$listingTitle = $this->_getTitle('listingTitle');
		
		// Get search options
		$showSearch = $this->_showSearch;
		$searchBaseUrl = null;
		$searchTerm = Request::getString('searchterm');

		// Get layout and ordering
		$defaultLayout = BluApplication::getSetting('defaultItemsView', 'grid');
		$layout = ISBOT ? 'list' : Request::getString('layout', Session::get('layout', $defaultLayout));
		$sort = $this->_getSort();
		
		// Store current layout and ordering
		Session::set('layout', $layout);
		if (!$this->_showSearch) {
			Session::set('adminsort', $sort);
		}
		// Override sort if we want latest items
		$viewType = Request::getString('view');
		if ($viewType == 'latest') {
			$sort = 'date_desc';
		}

		// Build search base URL
		$searchBaseUrl = '&amp;searchterm='.urlencode($searchTerm);

		// Get items, filtered using users meta filter, then filtered by type.
		$items = $itemsModel->getItems(null, null, $sort, array(), $searchTerm, true);
		if (!$this->_showAll) {
			$items = $itemsModel->filterTypeItems($items, $this->_itemType);
		}
		$items = $itemsModel->filterDeletedItems($items, $viewType == 'deleted' ? false : true);
		$numItems = count($items);
        
		// Add item details
		if (!empty($items)) {
			if (!$viewall) {
				$items = array_slice($items, $offset, $limit, true);
			}
			$itemsModel->addDetails($items);
			
			// Add live togglers                                                              http://leon.recipe4living.com/oversight/articles/set_pending/asdf.htm
			foreach ($items as &$item) {
				$item['liveToggler'] = $itemsModel->getTaskLink('/' . $item['type'] . '/' . $item['slug'] . '.htm', $item['live'] ? 'set_pending' : 'set_live');
				$item['featuredToggler'] = $itemsModel->getTaskLink($item['link'], $item['featured'] ? 'unset_featured' : 'set_featured');	// For now, just use as toggler (sets to 0 or 1 only, no free entry.)
				if($item['live']==0 || $item['live']==2) {
					$item['deleteToggler'] = $itemsModel->getTaskLink($item['link'], $item['live']==2 ? 'set_pending' : 'set_deleted').($viewType=='deleted'?'?view=deleted':'');
				}
				$item['ingredientsLink'] = $itemsModel->getTaskLink($item['link'], 'ingredients');
				
				$item['link'] = $itemsModel->getTaskLink($item['link'], 'edit');
			}
			unset($item);
		}

		// Get pagination values
		$total = $numItems;
		if ($viewType == 'latest') {
			$total = min($total, BluApplication::getSetting('numLatestItemsListing', $limit * 3));
		}
		if ($viewall) {
			$start = 1;
			$end = $total;
		} else {
			$start = $offset + 1;
			$end = min($offset + $limit, $total);
		}

		// Get base URLs for listing updates
		$baseUrl = SITEURL.$this->_baseUrl;
		
		if ($viewType == 'latest') {
			$baseUrl .= '?view=latest';
			$qsSep = '&amp;';
		} else {
			$qsSep = '?';
		}
		$layoutBaseUrl = $baseUrl.$qsSep.'sort='.$sort.$searchBaseUrl.'&amp;page='.$page.'&amp;layout=';
		$paginationBaseUrl = $baseUrl.$qsSep.($viewType == 'deleted' ? 'view=deleted&amp;' : '').'layout='.$layout.'&amp;sort='.$sort.$searchBaseUrl.'&amp;page=';
		
		// Do pagination
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => $paginationBaseUrl
		));

		// Output
		if ($format == 'json') {
			$response = array();
			
			// Load template
			ob_start();
		}
		switch ($this->_view) {
			case 'article_listing':
			case 'recipe_listing':
			case 'quicktip_listing':
			case 'blog_listing':
				include(BLUPATH_TEMPLATES.'/articles/items/articles.php');
				break;
		}
		if ($format == 'json') {
			$response['items'] = ob_get_clean();
			$response['numItems'] = $numItems;
			
			// Site title
			$response['documentTitle'] = $this->_doc->getSiteTitle();
			
			echo json_encode($response);
		}
	}
	
	/**
	 *	Get title
	 *
	 *	@param string Title type
	 *	@return string
	 */
	protected function _getTitle($titleType)
	{
		static $title;
		
		// Build title
		if (empty($title[$titleType])) {
			if ($this->_showSearch) {
				if ($titleType == 'listingTitle') {
					$title[$titleType] = 'Search for &#145;'.Request::getString('searchterm').'&#146;';
				} else {
					$title[$titleType] = 'Search';
				}
			} else {
				$title[$titleType] = $this->_getEmptyFilterTitle();
			}
		}
		
		return $title[$titleType];
	}
	
	/**
	 *	Get title when filters are barren.
	 *
	 *	@access protected
	 *	@return string
	 */
	protected function _getEmptyFilterTitle()
	{
		return 'All articles';
	}
	
	/**
	 * Get sort order
	 */
	protected function _getSort()
	{
		static $sort;
		
		if (!isset($sort)) {
			if ($this->_showSearch) {
				$sort = 'relevance';
			} else {
		 		$sort = 'date_desc';
		 		$sort = Session::get('adminsort', $sort);
			}
			$sort = Request::getString('sort', $sort);
		}
		
		return $sort;
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

		// Get items
		$itemsModel = BluApplication::getModel('items');
		$items = $itemsModel->getItems(0, $limit, 'relevance', array(), $searchTerm);
		$itemsModel->addDetails($items);

		// Load template
		switch ($this->_doc->getFormat()) {
			case 'json':
				$response = array();
				if (!empty($items)) {
					foreach ($items as $item) {
						ob_start();
						include(BLUPATH_BASE_TEMPLATES.'/items/quick_search_item.php');
						
						$response[] = array(
							'value' => $item['slug'],
							'html' => ob_get_clean()
						);
					}
				}
				echo json_encode($response);
				break;

			default:
				include(BLUPATH_BASE_TEMPLATES.'/items/quick_search.php');
				break;
		}
	}
	
	/**
	 *	Set the status of an item to pending
	 *
	 *	@access public
	 */
	public function set_pending()
	{
		// Get item
		$itemsModel = BluApplication::getModel('items');
		if (!$item = $itemsModel->getItem($this->_itemId)) {
			return $this->_errorRedirect();
		}
		
		$viewType = Request::getString('view');
		
		// Unset live
		$success = $itemsModel->unsetLive($item['id']);
		
		// Output
		switch ($this->_doc->getFormat()) {
			case 'json':
				echo json_encode($success);
				break;
				
			default:
				if ($success) {
					Messages::addMessage('<code>'.$item['slug'].'</code> ('.$item['id'].') set to pending.', 'info');
				} else {
					Messages::addMessage('<code>'.$item['slug'].'</code> ('.$item['id'].') not set to pending.', 'error');
				}
				switch ($item['type']) {
					case 'article':
						return $this->_redirect('/articles'.($viewType=='deleted'?'?view=deleted':''));
						
					case 'recipe':
						return $this->_redirect('/recipes'.($viewType=='deleted'?'?view=deleted':''));
						
					case 'quicktip':
						$itemsModel->flushQuicktips();
						return $this->_redirect('/quicktips'.($viewType=='deleted'?'?view=deleted':''));
						
					case 'blog':
						return $this->_redirect('/blogs'.($viewType=='deleted'?'?view=deleted':''));
				}
		}
	}
	
	/**
	 *	Set the status of an item to live
	 *
	 *	@access public
	 */
	public function set_live()
	{
		// Get item
		$itemsModel = BluApplication::getModel('items');
		if (!$item = $itemsModel->getItem($this->_itemId)) {
			return $this->_errorRedirect();
		}
		
		// Set live
		if ($success = $itemsModel->setLive($item['id'])) {
			Messages::addMessage(ucfirst($item['type']).' <code>'.$item['slug'].'</code> ('.$item['id'].') set live.', 'info');
			
			// Send email to the author
			if ($this->_sendSubmissionEmail) {
				$this->_sendSubmissionEmail($item);
			}
			
		// Phail
		} else {
			Messages::addMessage(ucfirst($item['type']).' <code>'.$item['slug'].'</code> ('.$item['id'].') not set live.', 'error');
		}
		
		// Output
		switch ($this->_doc->getFormat()) {
			case 'json':
				echo json_encode($success);
				break;
				
			default:
				switch ($item['type']) {
					case 'article':
						return $this->_redirect('/articles');
						
					case 'recipe':
						return $this->_redirect('/recipes');
						
					case 'quicktip':
						$itemsModel->flushQuicktips();
						return $this->_redirect('/quicktips');
						
					case 'blog':
						return $this->_redirect('/blogs');
				}
		}
	}
	
	/**
	 *	Set the status of an item to deleted
	 *
	 *	@access public
	 */
	public function set_deleted()
	{
		// Get item
		$itemsModel = BluApplication::getModel('items');
		if (!$item = $itemsModel->getItem($this->_itemId)) {
			return $this->_errorRedirect();
		}
		
		// Set deleted
		if ($success = $itemsModel->setDeleted($item['id'])) {
			Messages::addMessage(ucfirst($item['type']).' <code>'.$item['slug'].'</code> ('.$item['id'].') set deleted.', 'info');
			
		// Phail
		} else {
			Messages::addMessage(ucfirst($item['type']).' <code>'.$item['slug'].'</code> ('.$item['id'].') not set deleted.', 'error');
		}
		
		// Output
		switch ($this->_doc->getFormat()) {
			case 'json':
				echo json_encode($success);
				break;
				
			default:
				switch ($item['type']) {
					case 'article':
						return $this->_redirect('/articles');
						
					case 'recipe':
						return $this->_redirect('/recipes');
						
					case 'quicktip':
						$itemsModel->flushQuicktips();
						return $this->_redirect('/quicktips');
						
					case 'blog':
						return $this->_redirect('/blogs');
				}
		}
	}
	
	/**
	 *	Send confirmation email to item author
	 *
	 *	@access protected
	 *	@param array Item
	 *	@return bool Success
	 */
	protected function _sendSubmissionEmail($item)
	{
		// No email address? Then you don't get an email, boo hoo.
		if (empty($item['author']['email'])) {
			return true;
		}
		
		// Prepare email
		$email = new Email();
		$vars = array(
			'fullName' => $item['author']['fullname'],
			'recipeTitle' => $item['title'],
			'recipeUrl' => $item['link'],
		);
		$toAddress = $item['author']['email'];
		$toName = $item['author']['fullname'];
		$subject = Text::get('global_recipe_submission_notification');
		$email->addBcc('R4LRecipeEmails@gmail.com', 'Recipe4living Recipe Confirmations');
		
		// Send email
		if ($success = $email->quickSend($toAddress, $toName, $subject, 'recipesubmission', $vars)) {
			Messages::addMessage('Confirmation email was sent to <code>'.$toAddress.'</code>.', 'info');
		}
		
		// Return
		return $success;
	}
	
	/**
	 *	Set featured level of an item
	 *
	 *	@access public
	 *	@param int Default feature level
	 */
	public function set_featured($featureLevel = 1)
	{
		// Get item
		$itemsModel = BluApplication::getModel('items');
		if (!$item = $itemsModel->getItem($this->_itemId)) {
			return $this->_errorRedirect();
		}
		
		// Get request
		$featureLevel = Request::getInt('feature', $featureLevel);
		
		// Unset live
		$success = $itemsModel->setFeatured($item['id'], $featureLevel);
		
		// Output
		switch ($this->_doc->getFormat()) {
			case 'json':
				echo json_encode($success);
				break;
				
			default:
				if ($success) {
					if ($featureLevel) {
						//Messages::addMessage('<code>'.$item['slug'].'</code> ('.$item['id'].') set to feature level '.$featureLevel.'.', 'info');
						Messages::addMessage('<code>'.$item['slug'].'</code> ('.$item['id'].') set as featured.', 'info');	// Don't allow feature levels yet.
					} else {
						Messages::addMessage('<code>'.$item['slug'].'</code> ('.$item['id'].') set as not featured.', 'info');
					}
				} else {
					Messages::addMessage('<code>'.$item['slug'].'</code> ('.$item['id'].') feature level not set.', 'error');
				}
				switch ($item['type']) {
					case 'article':
						return $this->_redirect('/articles');
						
					case 'recipe':
						return $this->_redirect('/recipes');
				}
		}
	}
	
	/**
	 *	Unset feature level of an item
	 *
	 *	@access public
	 */
	public function unset_featured()
	{
		return $this->set_featured(0);
	}
	
	/**
	 *	Delete a review
	 *
	 *	@access public
	 */
/*
	public function delete_review()
	{
		// Get request
		$reviewId = Request::getInt('review');
		
		// Delete review
		$itemsModel = BluApplication::getModel('items');
		$success = $itemsModel->deleteComment($reviewId);
		
		// Output
		switch ($this->_doc->getFormat()) {
			case 'json':
				echo json_encode($success);
				break;
				
			default:
				if ($success) {
					Messages::addMessage('Review '.$reviewId.' successfully deleted.', 'info');
				} else {
					Messages::addMessage('Review '.$reviewId.' not deleted.', 'error');
				}
				switch ($item['type']) {
					case 'article':
						return $this->_redirect('/articles');
						
					case 'recipe':
						return $this->_redirect('/recipes');
				}
		}
	}
*/
	
	public function links() {
		// Get models
		$itemsModel = BluApplication::getModel('items');
		
		$urlPart = $this->_itemType.'s';
		
		$articleId = end($this->_args);
		$article = $itemsModel->getItem($articleId);
		if(!$article) {
			return $this->_redirect();
		}
		
		$links = $itemsModel->getLinks($articleId);
		
		// Load template
		include(BLUPATH_TEMPLATES.'/articles/links.php');
	}

	public function link() {
		// Get models
		$itemsModel = BluApplication::getModel('items');
		
		$urlPart = $this->_itemType.'s';
		
		$articleId = end($this->_args);
		$article = $itemsModel->getItem($articleId);
		if(!$article) {
			return $this->_redirect();
		}
		
		$linkId = Request::getString('linkId');
		
		$links = $itemsModel->getLinks($articleId);
		
		if($linkId) {
			if(isset($links[$linkId])) {
				$link = $links[$linkId];
			}
			else {
				return $this->_redirect('/'.$urlPart.'/links/'.$articleId);
			}
		}
		
		// Load template
		include(BLUPATH_TEMPLATES.'/articles/link.php');
	}

	public function add_link() {
		// Get models
		$itemsModel = BluApplication::getModel('items');
		
		$urlPart = $this->_itemType.'s';
		
		$articleId = end($this->_args);
		$article = $itemsModel->getItem($articleId);
		if(!$article) {
			return $this->_redirect();
		}
		
		$title = Request::getString('title');
		$href = Request::getString('href');
		$description = Request::getString('description');
		
		if($itemsModel->addLink($articleId, $href, $title, $description)) {
			Messages::addMessage('Link added successfully','info');
		}
		
		return $this->_redirect('/'.$urlPart.'/links/'.$articleId);
	}

	public function update_link() {
		// Get models
		$itemsModel = BluApplication::getModel('items');
		
		$urlPart = $this->_itemType.'s';
		
		$articleId = end($this->_args);
		$article = $itemsModel->getItem($articleId);
		if(!$article) {
			return $this->_redirect();
		}
		
		$linkId = Request::getInt('linkId');
		$title = Request::getString('title');
		$href = Request::getString('href');
		$description = Request::getString('description');
		
		if($itemsModel->updateLink($articleId, $linkId, $href, $title, $description)) {
			Messages::addMessage('Link updated successfully','info');
		}
		
		return $this->_redirect('/'.$urlPart.'/links/'.$articleId);
	}

	public function delete_link() {
		// Get models
		$itemsModel = BluApplication::getModel('items');
		
		$urlPart = $this->_itemType.'s';
		
		$articleId = end($this->_args);
		$article = $itemsModel->getItem($articleId);
		if(!$article) {
			return $this->_redirect();
		}
		
		$linkId = Request::getInt('linkId');
		
		if($itemsModel->deleteLink($articleId,$linkId)) {
			Messages::addMessage('Link deleted successfully','info');
		}
		
		return $this->_redirect('/'.$urlPart.'/links/'.$articleId);
	}
	
	public function new_list()
	{
		include(BLUPATH_TEMPLATES.'/articles/new_list.php');
	}
	
	public function new_list_left()
	{
		$metaModel = BluApplication::getModel('meta');
		$metaModel->getNewCategory();
	}
	
	public function new_list_right()
	{
		$type = false;
		$mgid = Request::getString('mgid');
		$mvid = Request::getString('mvid');
		if(!$mgid || !$mvid) 
		{
			echo "Please select the category from the left!";
			return false;
		}
		
		if($mgid == 4){
			$type = "recipe";
		}else{
			$type = "article";
		}
		$metaModel = BluApplication::getModel('meta');
		$metaModel->getNewCategoryArticles($mgid,$mvid,$type);
	}

    public function new_push_live()
    {
        $metaModel = BluApplication::getModel('meta');
        $id = Request::getString('id');
        $action = Request::getString('action');
        if($action == '1'){
            $metaModel->pushOffline($id);
        }else{
            $metaModel->pushOnline($id);
        }
        echo "<script>window.history.back();</script>";
    }
    	
	public function new_delete_from_category()
	{
		$metaModel = BluApplication::getModel('meta');
		$id = Request::getString('id');
		$cid = Request::getString('cid');
		$metaModel->removeFromCategory($id,$cid);
		echo "<script>window.history.back();</script>";
	}
	
		/**
	 *	Search articles or recipes
	 *
	 *	@access public
	 */
	public function search()
	{
		// Get items
		if ($search = Request::getString('search')) {
			$page = Request::getInt('page', 1);
			$limit = 20;
			$itemsModel = BluApplication::getModel('items');
			$offset = ($page-1)*$limit;
			$items = $itemsModel->getItems(null, null, null, array(), $search,false,true);
            // Get the latest live items first
            $_cache = BluApplication::getCache();
            $r = $liveItems = $_cache->delete('items_live');
            //var_dump($r);
			// Do some final filtering (by live flag)
			$items = $itemsModel->filterLiveItems($items);
			$total = count($items);
			$items = array_slice($items, $offset, $limit, true);
			$itemsModel->addDetails($items,true);
			$pagination = Pagination::simple(array(
				'limit' => $limit,
				'total' => $total,
				'current' => $page,
				'url' => '?search='.$search.'&amp;page='
			));
		}

		// Load template
		include (BLUPATH_TEMPLATES.'/articles/article_slideshow/slideShowSearch.php');
	}
	
	/**
	 *	Display slideshow details
	 *
	 *	@access public
	 */
	public function details()
	{
		// Get models
		$itemsModel = BluApplication::getModel('items');
		$articleId = end($this->_args);
		$article = $itemsModel->getItem($articleId);

		if (!$article) {
			Messages::addMessage('Could not find your article #'.$articleId.'.', 'error');
		}

		Template::set('search', Request::getString('search'));

		// Get *raw* slideshow contents too		
		$articleSlides = $itemsModel->getSlideArticleIdByArticleId($articleId);
		$name = $article['title'];
		Template::set('slide_slug', $article['slug']);

		if(empty($articleSlides))
		{
			$itemsModel->updateArticleSlideStatus($articleId,0);
		}
		else{
			$itemsModel->updateArticleSlideStatus($articleId,1);
		}
		/*echo "<pre>";
		print_r($articleSlides);
		echo "</pre>";exit;*/
        
        $itemsModel->flushItem($articleId);

		include(BLUPATH_TEMPLATES.'/articles/slideshows.php');
	}
	
	/**
	 *	Add/edit slide content details
	 *
	 *	@access public
	 */
	public function content()
	{
		// Get models
		$itemsModel = BluApplication::getModel('items');
		$articleId = end($this->_args);
		// Add/edit box content
		if (Request::getBool('save')) {

			// Get details for all types of boxes, can't be arsed to do a switch-case.
			$link = Request::getString('link', null);
			$sequence = Request::getInt('order', 0);
			$title = Request::getString('title', null);
			$desc = Request::getString('desc', null,'default',true);
			$info = Request::getArray('info', null);
			$langCode = Request::getString('langCode', null);
			
			if(empty($sequence))
			{
				Messages::addMessage('Please make sure the "Priority" is not 0 or empty.', 'error');
				return $this->details();
			}
			else
			{
				$contentId = Request::getInt('contentId', 0);
				$contentOrder = $itemsModel->getSlideOrderBySlidePageArticleId($articleId,$contentId);
				//If the slide content order is changed, then check if it is unique.
				if($contentOrder != $sequence)
				{
					$isExist = $itemsModel->checkArticleSlideshowOrder($articleId,$sequence);
					if(!empty($isExist))
					{
						Messages::addMessage('Please make sure the "Priority" is unique.', 'error');
						return $this->details();
					}
				}
			}			

			// Edit existing?
			if ($contentId = Request::getInt('contentId')) {
				if ($itemsModel->updateArticleSlideshow($articleId,$contentId, $desc, $sequence)) {
					Messages::addMessage('Slideshow content updated.', 'info');
				} else {
					Messages::addMessage('Could not update slideshow content.', 'error');
				}

			// Add new?
			} else if ($this->_itemId = Request::getInt('articleId')) {
				//$contentId = Request::getInt('slideId', null);
				$contentIds = Request::getArray('slideIds', null);
				if(empty($contentIds))
				{
					Messages::addMessage('Please choose at least one recipe.', 'error');
					return $this->details();
				}
				$startSequence = $sequence;
				$addResult = true;
				foreach($contentIds as $contentId)
				{
					//Check if the slide is already in this slide article.
					if($existSlide = $itemsModel->checkArticleSlideshow($this->_itemId,$contentId))
					{
						continue;
					}
					if ($articleId = $itemsModel->addArticleSlideshow($this->_itemId,$contentId , $startSequence)) {
						$startSequence++;
					} else {
						$addResult = false;
					}					
				}	

				if ($addResult) {
					Messages::addMessage('Slideshow content added.', 'info');
				} else {
					Messages::addMessage('Could not add slideshow content.', 'error');
				}	
			}
			
			// Redirect to box details
			return $this->details();

		// Delete box content
		} else if (Request::getBool('delete')) {

			// Get request
			$contentId = Request::getInt('contentId');

			// Delete from database
			if ($itemsModel->deleteArticleSlideshow($articleId,$contentId)) {
				Messages::addMessage('Slideshow content deleted.', 'info');
			} else {
				Messages::addMessage('Could not delete slideshow content, please try again.', 'error');
			}
			
			return $this->details();

		// View box content
		} else {
			return $this->_content($articleId,$contentId);
		}
	}

	/**
	 *	Display slide show content details
	 *
	 *	@access protected
	 *	@param int slideshow content ID, if editing
	 */
	protected function _content($articleId = null,$contentId = null)
	{
		// Get models
		$itemsModel = BluApplication::getModel('items');

		// Editing?
		if ($contentId) {

			// Get box content
			$articleSlide = $itemsModel->getItem($contentId);		
			$articleSlideDetail = $itemsModel->getSlideArticleDetailArticleId($articleId,$contentId);

			// Format for view
			$contentId = $articleSlide['id'];
			$link = $articleSlide['link'];
			$sequence = $itemsModel->getSlideOrderBySlidePageArticleId($articleId,$contentId);
			$title = $articleSlide['title'];
			$langCode = 'EN';
			$desc = $articleSlide['description'];
			if(!empty($articleSlideDetail['description']))
			{
				$desc = $articleSlideDetail['description'];
			}
			$imageName = $articleSlide['image']['filename'];

		// Adding new
		} else {

			// Get article to die/add for
			$article = $itemsModel->getItem($articleId);

			// Format for view
			$link = '';
			$sequence = $itemsModel->getMaxSlideshowOrder($articleId)+1;
			$title = '';
			$desc ='';
			$langCode = 'EN';
			$imageName = '';
			$articleId = $article['id'];
		}

		// Load template
		include(BLUPATH_TEMPLATES.'/articles/article_slideshow/slideShowItems.php');
	}

	/**
	 *	Empty box content template
	 *
	 *	@access public
	 */
	public function add_content($articleId)
	{
		return $this->_content($articleId);
	}
}

?>
