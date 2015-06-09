<?php
/**
 *	Articles Controller
 *
 *	@package BluApplication
 *	@subpackage FrontendControllers
 */
class Recipe4livingArticlesController extends ClientFrontendController
{
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
	 *	Requested base url
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_baseUrl;

	/**
	 *	Enable search term refinement
	 *
	 *	@access protected
	 *	@var bool
	 */
	protected $_enableRefineSearch = true;
	
	/**
	 *	Do advanced search
	 *
	 *	@access protected
	 *	@var bool
	 */
	protected $_advancedSearch = false;
	
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
		$metaModel = BluApplication::getModel('meta');
		
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
			$this->_itemId = $itemsModel->getItemId($slug);
			
			// Override view type
			if ($item = $itemsModel->getItem($this->_itemId)) {
				switch ($item['type']) {
					case 'article':
						$this->_view = 'article_details';
						break;
						
					case 'recipe':
						$this->_view = 'recipe_details';
						break;
						
					case 'blog':
						$this->_view = 'blog_details';
						break;
					case 'quicktip':
						$this->_view = 'quicktip_details';
						break;
				}
				
				// Use quick view?
				if (reset($this->_args) == 'quick_view') {
					$this->_view = array_shift($this->_args);
				}
			}
		}
		
		// Apply remaining args as meta value slugs to filter.
		if (!empty($this->_args)) {
			$metaModel->applyFilters($this->_args);
		}
		$slugfilter = array();
		if(Request::getString('recipeslug') && Request::getString('controller') == 'recipes'){
			$slugfilter = array_filter(explode('/',Request::getString('recipeslug')));
		}
		
		if(Request::getString('articleslug') && Request::getString('controller') == 'articles'){
			$slugfilter = array_filter(explode('/',Request::getString('articleslug')));
		}
		
		if($slugfilter){
			$metaModel->applyFilters($slugfilter);
		}
		
		// Set default filters if first visit and none set.
		if (!$metaModel->filtersSet() && !Session::get('noDefaultFilters', false)) {
			$metaModel->applyDefaultFilters();
		}
		Session::set('noDefaultFilters', true);
		
		// Clear previous filters if requested
		if (Request::getBool('clearFilters')) {
			$metaModel->clearFilters();
		}
		
		// Set this for top search
		Template::set('searchType', 'articles');
		Template::set('searchTerm', Request::getString('searchterm'));
		Template::set('recipeslug', Request::getString('recipeslug'));
		Template::set('articleslug', Request::getString('articleslug'));		
		// Enable "browsing"
		$this->_enableBrowse = BluApplication::getSetting('enableBrowseListing');
		
		// Meta filter type
		$this->_advancedSearch = Request::getBool('advanced');
	}
	
	/**
	 *	Display home page
	 *
	 *	@access public
	 */
	public function view()
	{
		// Get models
		$itemsModel = BluApplication::getModel('items');
		$metaModel = BluApplication::getModel('meta');
		$userModel = BluApplication::getModel('user');
		Template::set('user', $userModel->getCurrentUser());
		
		// Get breadcrumbs
		$breadcrumbs = $this->_getBreadcrumbs();
		
		//Search or Recipe category
		$iscategory = !$this->_showSearch;
		
		// Load data
		switch ($this->_view) {
			case 'article_listing':
			case 'quicktip_listing':
			case 'recipe_listing':
			case 'recipe_rss':
			case 'blog_listing':
				// Set document meta
				$viewtitle = 
				$this->_doc->setTitle($this->_getTitle('pageTitle'));
				$this->_doc->setDescription($this->_getDescription());
				$this->_doc->setKeywords($metaModel->getKeywords());
				$this->_doc->setBreadcrumbs($breadcrumbs);
				break;
				
			case 'article_details':
			case 'quick_view':
			case 'recipe_details':
			case 'quicktip_details':
			case 'blog_details':
				
				// Get article
				if ((!$item = $itemsModel->getItem($this->_itemId)) || ($item['live']!=1 && !$item['canEdit'])) {
					return $this->_errorRedirect();
				}
				
				Template::set('curr_itemid', $this->_itemId);
				Template::set('curr_title', $item['title']);
				Template::set('default_img', $item['image']['filename']);
				
				// Get recent items
				$recent = $itemsModel->getRecentItems(0, BluApplication::getSetting('numRecentItems', 3), $item['id']);
				$itemsModel->addDetails($recent);
				
				// Get details for browse links
				if ($this->_enableBrowse) {
					
					$browseTotal = 0;
					if ($browseSession = Request::getBool('browse')) {
						
						// Get browse list
						$browseList = Session::get('browseList');
						$browseItems = Session::get('browseItems');
						
					} else {
						
						// Generate browse list
						$sort = BluApplication::getSetting('defaultItemsSort', 'name_asc');
						$sort = Session::get('sort', $sort);
						
						$browseList = $metaModel->getPageTitle();
						$browseItems = $itemsModel->getItems(null, null, null, $metaModel->getFilters());
					}

					if (!empty($browseItems)) {
						
						// Find current next and previous in haystack
						$browseTotal = count($browseItems);
						$browseCurrent = 1;
						$browsePrev = null;
						$browseItem = reset($browseItems);

						while ($browseItem && ($browseItem != $item['id'])) {
							$browseCurrent++;
							$browsePrev = current($browseItems);
							$browseItem = next($browseItems);
						}
						$browseNext = next($browseItems);
						
						// Add links
						$browseLinkPostfix = ($browseSession && !ISBOT) ? '?browse=1' : '';
						if ($browsePrev) {
							$browsePrev = $itemsModel->getItem($browsePrev);
							$browsePrev['link'] .= $browseLinkPostfix;
						} 
						if ($browseNext) {
							$browseNext = $itemsModel->getItem($browseNext);
							$browseNext['link'] .= $browseLinkPostfix;
						}
					}
				}
				
				// Get listings referer
				$referer = Session::get('referer');
				if (!$this->_enableBrowse) {
					Session::delete('referer');	// Stop it running away.
				}
				
				// Check whether item is in the users shopping list
				$userModel = BluApplication::getModel('user');
				$inRecipeBox = false;
				$recipeNote = '';
				if ($user = $userModel->getCurrentUser()) {
					$inRecipeBox = isset($user['saves']['recipebox'][$item['id']]);
					$recipeNote = isset($user['saves']['recipe_note'][$item['id']]['comment']) ? $user['saves']['recipe_note'][$item['id']]['comment'] : $recipeNote;
				}
				
				// Get links
				$shoppingListLink = $itemsModel->getTaskLink($item['link'], 'shopping_list_add');
				$shoppingListRemoveLink = $itemsModel->getTaskLink($item['link'], 'shopping_list_remove');
				$recipeBoxLink = $itemsModel->getTaskLink($item['link'], 'save_to_recipe_box');
				$recipeBoxRemoveLink = $itemsModel->getTaskLink($item['link'], 'remove_from_recipe_box');
				$recipeNoteLink = $itemsModel->getTaskLink($item['link'], 'save_recipe_note');
				$addToCookbookLink = $itemsModel->getTaskLink($item['link'], 'add_to_cookbook');
				//$addToShoppingListLink = $itemsModel->getTaskLink($item['link'], 'shopping_list_add');
				$imageGalleryLink = $itemsModel->getTaskLink($item['link'], 'image_gallery');
				
				// Add to recently viewed list
				$itemsModel->addRecentItem($item);
				
				// Set document meta
				$categories = $this->get_categories();
				if(!empty($categories[0]["name"])){
				$title = $categories[0]["name"]." | ".$item['title'];
				} else {
				$title = $item['title'];
				}
				switch ($item['type']) {
					case 'recipe':
						$title .= ' Recipe';
						break;
				}
				$this->_doc->setTitle($title);
				$this->_doc->setDescription($item['teaser']);
				$this->_doc->setKeywords($item['keywords'] ? $item['keywords'] : $item['title'].', '.$metaModel->getKeywords());
				$breadcrumbs[] = array($item['title'], $item['link']); 
				$this->_doc->setBreadcrumbs($breadcrumbs);
				
				// Increase view count for non-quickview.
				switch ($this->_view) {
					case 'article_details':
					case 'recipe_details':
					case 'blog_details':
						$itemsModel->incrementViews($item['id']);
						break;
						
					case 'quick_view':
						break;
				}
				break;
		}
		// Load view
		switch ($this->_view) {
			case 'article_listing':
			case 'quicktip_listing':
			case 'recipe_listing':
			case 'blog_listing':
//				$url = $this->_baseUrl;
//				$listUrl = array("/articles/", "/recipes/", "/recipes/appetizers" , "/recipes/crockpot", "/recipes/casseroles", "/recipes/desserts", "/recipes/main_courses", "/articles/hints_tips", "/articles/hints_tips/recipe_collections", "/articles/hints_tips/product_reviews");	
//				if(!in_array($url,$listUrl)){
//					$this->_errorRedirect();
//				} else {
					include(BLUPATH_TEMPLATES.'/articles/article_listing.php');
//				}
				break;
				
			case 'recipe_rss':
				$this->view_items();
				break;
				
			case 'article_details':
				include(BLUPATH_TEMPLATES.'/articles/article_details.php');
				break;
				
			case 'recipe_details':
				include(BLUPATH_TEMPLATES.'/recipes/recipe_details.php');
				break;
				
			case 'blog_details':
				include(BLUPATH_TEMPLATES.'/blogs/blog_details.php');
				break;
				
			case 'quick_view':
				include(BLUPATH_TEMPLATES.'/articles/quick_view.php');
				break;
			case 'quicktip_details':
				include(BLUPATH_TEMPLATES.'/quicktips/items/quicktips_details.php');
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
		// Get models
		$itemsModel = BluApplication::getModel('items');
		$metaModel = BluApplication::getModel('meta');
		// Get filtering options
		$searchTerm = Request::getString('searchterm');
		$searchTermExtra = Request::getString('searchterm_extra');
		
		//notes by leon
		//this is why the categories issue come out
		$filters = $metaModel->getFilters();

		if(Request::getString('recipeslug') && Request::getString('controller') == 'recipes'){
			$searchTermExtra = $searchTerm;
		}
		if(Request::getString('articleslug') && Request::getString('controller') == 'articles'){
				$searchTermExtra = $searchTerm;
		}
		
		// if the category being asked for is unpublished set the filters to empty;
		if ($metaModel->filtersSet()) {
			$currentFilter = $metaModel->getBottomHierarchyFilter();
			$currentFilterId = $currentFilter['id'];
			$metaValue = $metaModel->getValue($currentFilterId);
			if(!$metaValue['display']){
				$filters = array();
				$this->_filters = array();					
			}
		}
        $description = ""; //Initialize the description
		if(isset($metaValue) && isset($metaValue['description'])){
			$description = $metaValue['description'];
		}
		// Get/set sort
		$sort = $this->_getSort();
		Session::set('sort', $sort);
		if ($this->_view == 'recipe_rss') {
			$sort = 'date_desc';
		}
		// Get relevant items
		if ($this->_advancedSearch) {
			$items = $itemsModel->getItems(null, null, $sort, array(), $searchTerm.($searchTermExtra ? ' '.$searchTermExtra : ''));
			//$items = $metaModel->filterItems($items, $filters, true);
		} else {
			$items = $itemsModel->getItems(null, null, $sort, $filters, $searchTerm.($searchTermExtra ? ' '.$searchTermExtra : ''));
		}
		
		// Filter by type
		if (!$this->_showAll) {
			$items = $itemsModel->filterTypeItems($items, $this->_itemType);
		}
		// Save search term
		if ($searchTerm || $searchTermExtra) {
			$numItems = count($items);
			
			$searchTermModel = BluApplication::getModel('searchterms');
			$searchTermModel->saveSearchTerm($this->_itemType, $searchTerm, $searchTermExtra, $numItems);
		}
        
		// Get/set layout and ordering
		
		$layout = $this->_getLayout();

		Session::set('layout', $layout);
		
		// Do display stuff
		$baseUrl = $this->_baseUrl;
		if ($this->_advancedSearch) {
			$baseUrl .= '?advanced=1';
		}
		$pathway = $this->_getBreadcrumbs();
		$documentTitle = $this->_getTitle('pageTitle');
		if(!empty($filters) || !empty($searchTerm)){
			$listingTitle = $this->_getTitle('listingTitle');
		}else{
			if($this->_itemType == 'article'){
				$listingTitle = 'All Articles';
			}
			else if($this->_itemType == 'quicktip'){
				$listingTitle = 'All Quicktips';				
			}else{
				$listingTitle = 'All Recipes';
			}
		}
		$page = Request::getInt('page', 1);
		$limit = $this->_getLimit();
		
		// Set referer
		$referer = array($this->_baseUrl);
		if ($page > 1) {
			$referer[1][] = 'page='.$page;
		}
		if ($searchTerm) {
			$referer[1][] = 'searchterm='.urlencode($searchTerm);
			if ($searchTermExtra) {
				$referer[1][] = 'searchterm_extra='.urlencode($searchTermExtra);
			}
		}
		if (isset($referer[1])) {
			$referer[1] = implode('&', $referer[1]);
		}
		$referer = implode('?', $referer);
		Session::set('referer', $referer);
		//print_r($items);
		// Display
		return $this->_listItems($items, $page, $limit, $baseUrl, $searchTerm, $searchTermExtra, $sort, $layout, $pathway, $documentTitle, $listingTitle,$description,!$this->_showSearch);
	}
	
	/**
	 *	View page of article text
	 *
	 *	@access public
	 */
	public function view_page()
	{
		// Get item
		$itemsModel = BluApplication::getModel('items');
		if (!$item = $itemsModel->getItem($this->_itemId)) {
			return $this->_errorRedirect();
		}
		// Do recipe-specific stuff
		if ($this->_view == 'recipe_details') {
			$itemsModel->buildRecipeMeta($item);
			$item['readerloved'] = $itemsModel->getReaderLovedItems($item['id'],array_keys($item['related']));
		}
		
		// Build related recipes
		if (!empty($item['related'])) {
			$itemsModel->addDetails($item['related']);
		}
		
		// Get requested page number
		//$page = Request::getInt('page', $this->_page);
		
		// Load template with sliced text.
		ob_start();
		switch ($this->_view) {
			case 'article_details':
				include(BLUPATH_TEMPLATES.'/articles/details/body.php');
				break;
				
			case 'recipe_details':
				include(BLUPATH_TEMPLATES.'/recipes/details/body.php');
				break;
				
			case 'blog_details':
				include(BLUPATH_TEMPLATES.'/blogs/details/body.php');
				break;
		}
		$content = ob_get_clean();
		
		// Use pagination on it
		/* No, don't use pagination on it
		$limit = BluApplication::getSetting('articleLength', 450);
		$pagination = Pagination::text(array(
			'limit' => $limit,
			'content' => $content,
			'current' => (int) $page,
			'url' => '?page='
		));
		*/
		
		// Display results
		/*
		echo $pagination->get('content');
		echo $pagination->get('buttons', array(
			'pre' => '<strong class="fl">Pages: </strong>'
		));
		*/
		echo $content;
	}
	
	/**
	 *	Display reviews
	 *
	 *	@access public
	 */
	public function reviews()
	{
		// Get reviews
		$itemsModel = BluApplication::getModel('items');
		$item = $itemsModel->getItem($this->_itemId);
		$item['comments'] = $itemsModel->getItemComments($item['id']);
		$reviews = empty($item['comments']['review']) ? array() : $item['comments']['review'];
		$reviewsCount = count($reviews);
		
		// Build details
		if (!empty($reviews)) {
			$userModel = BluApplication::getModel('user');
			foreach ($reviews as $commentId => &$review) {
				$review['author'] = $userModel->getUser($review['userId']);
			}
			unset($review);
		}
		
		// Add report link
		$reportLink = $itemsModel->getTaskLink($item['link'], 'report_review');
		
		switch($this->_view) {
			case 'blog_details':
				$reviewsTitle = 'Comments';
			break;
			default:
				$reviewsTitle = 'Reviews';
		}
		
		// Load template
		switch ($this->_view) {
			case 'article_details':
			case 'recipe_details':
			case 'blog_details':
				include(BLUPATH_TEMPLATES.'/articles/details/reviews.php');
				break;
		}
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
	 *	Get sort order
	 *
	 *	@access protected
	 *	@return string
	 */
	protected function _getSort()
	{
		static $sort;
		
		if (!isset($sort)) {
			if ($this->_showSearch) {
				$sort = 'relevance';
			} else {
				$sort = 'date_desc';//'rating';
		 		//$sort = Session::get('sort', $sort);	// Recipes wants it to reset on every page.
			}
			$sort = Request::getString('sort', $sort);
		}
		
		return $sort;
	}

	/**
	 *	Get listing limit
	 *
	 *	@access protected
	 *	@return int
	 */
	protected function _getLimit()
	{
		// Custom RSS limit
		if ($this->_view == 'recipe_rss') {
			return BluApplication::getSetting('rssListingLength', 20);
		}
		return parent::_getLimit();
	}
	
	/**
	 *	Add a review
	 *
	 *	@access protected
	 */
	protected function _addReview()
	{
		return $this->_addFeedback('review');
	}
	
	/**
	 *	Add a rating
	 *
	 *	@access protected
	 */
	protected function _addRating()
	{
		return $this->_addFeedback('rating');
	}
	
	/**
	 *	Add a comment rating
	 *
	 *	@access protected
	 */
	protected function _addCommentRating()
	{
		return $this->_addFeedback('comment_rating');
	}
	
	/**
	 *	Add a vote
	 *
	 *	@access protected
	 */
	protected function _addVote()
	{
		return $this->_addFeedback('vote');
	}
	
	/**
	 *	Add feedback
	 *
	 *	@access protected
	 *	@param string Feedback type
	 */
	protected function _addFeedback($type)
	{
		// Get user
		$userModel = BluApplication::getModel('user');
		$user = $userModel->getCurrentUser();

		// Get permissions
		$reviewPermissions = BluApplication::getSetting('reviewPermissions');

		// Get article
		$itemsModel = BluApplication::getModel('items');
		$item = $itemsModel->getItem($this->_itemId);

		// Get data from request
		switch ($type) {
			case 'review':
				$comments = Request::getString('comments', 'Write your own comment here.');
				break;
				
			case 'rating':
				$rating = Request::getInt('rating');
				break;
				
			case 'comment_rating':
				$commentId = Request::getInt('comment');
				$rating = Request::getInt('rating');
				break;
				
			case 'vote':
				break;
		}
		
		// Get submit link
		switch ($type) {
			case 'review':
				$submitTask = 'save_review';
				break;
				
			case 'rating':
				$submitTask = 'save_rating';
				break;
				
			case 'comment_rating';
				$submitTask = 'save_comment_rating';
				break;
				
			case 'vote':
				$submitTask = 'doff_chef_hat';
				break;
		}
		$submitLink = $itemsModel->getTaskLink($item['link'], $submitTask);

		// Show form
		switch ($type) {
			case 'review':
				switch ($this->_view) {
					case 'article_details':
						include(BLUPATH_TEMPLATES.'/articles/details/add_review.php');
						break;
						
					case 'recipe_details':
						include(BLUPATH_TEMPLATES.'/recipes/details/add_review.php');
						break;
						
					case 'blog_details':
						include(BLUPATH_TEMPLATES.'/blogs/details/add_review.php');
						break;
				}
				break;
				
			case 'rating':
				switch ($this->_view) {	
					case 'article_details':
					case 'recipe_details':
					case 'blog_details':
						include(BLUPATH_TEMPLATES.'/articles/details/add_rating.php');
						break;
				}
				break;
				
			case 'comment_rating':
				switch ($this->_view) {
					case 'article_details':
						include(BLUPATH_TEMPLATES.'/articles/details/add_comment_rating.php');
						break;
						
					case 'recipe_details':
						include(BLUPATH_TEMPLATES.'/recipes/details/add_comment_rating.php');
						break;
				}
				break;
				
			case 'vote':
				switch ($this->_view) {
					case 'article_details':
					case 'recipe_details':
					case 'blog_details':
						include(BLUPATH_TEMPLATES.'/articles/details/add_vote.php');
						break;
				}
				break;
		}
	}

	/**
	 *	Save a review
	 *
	 *	@access public
	 */
	public function save_review()
	{
		return $this->_saveFeedback('review');
	}
	
	/**
	 *	Save a rating
	 *
	 *	@access public
	 */
	public function save_rating()
	{
		return $this->_saveFeedback('rating');
	}
	
	/**
	 *	Save a comment rating
	 *
	 *	@access public
	 */
	public function save_comment_rating()
	{
		return $this->_saveFeedback('comment_rating');
	}
	
	/**
	 *	Save a vote
	 *
	 *	@deprecated Use self::save_rating.
	 *	@access public
	 */
	public function doff_chef_hat()
	{
		return $this->_saveFeedback('vote');
	}
	
	/**
	 *	Save feedback
	 *
	 *	@access protected
	 *	@param string Feedback type
	 */
	protected function _saveFeedback($type)
	{
		// Get redirect
		switch ($type) {
			case 'review':
				$redirectTask = '_addReview';
				$currentTask = 'save_review';
				break;
				
			case 'rating':
				$redirectTask = '_addRating';
				$currentTask = 'save_rating';
				break;
				
			case 'comment_rating':
				$redirectTask = '_addCommentRating';
				$currentTask = 'save_comment_rating';
				break;
				
			case 'vote':
				$redirectTask = '_addVote';
				$currentTask = 'doff_chef_hat';
				break;
		}

		// Get article
		$itemsModel = BluApplication::getModel('items');
		if (!$item = $itemsModel->getItem($this->_itemId)) {
			return $this->_viewForm($redirectTask);
		}
		
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = $itemsModel->getTaskLink($item['link'], $currentTask);
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('item_feedback_login'), 'warn');
		}

		// Get permissions
		$reviewPermissions = BluApplication::getSetting('reviewPermissions');
		$canAddReview = $reviewPermissions & ($user ? Config::COMMENT_REGISTERED : Config::COMMENT_ANON);
		$requireCaptcha = $reviewPermissions & ($user ? Config::COMMENT_CAPTCHA_REGISTERED : Config::COMMENT_CAPTCHA_ANON);
		if ($type == 'vote') {
			$requireCaptcha = false;
		}
/*
		// Check permissions
		if (!$canAddReview) {
			Messages::addMessage(Text::get('item_msg_add_review_noperm'), 'error');
			return $this->_viewForm($redirectTask);
		}
*/
		// Get data from request
		switch ($type) {
			case 'review':
				$comments = Request::getString('comments');
				break;
				
			case 'rating':
				$rating = Request::getInt('rating');
				break;
				
			case 'comment_rating':
				$commentId = Request::getInt('comment');
				$rating = Request::getInt('rating');
				break;
				
			case 'vote':
				break;
		}
		if ($requireCaptcha) {
			$captcha = Request::getString('captcha');
		}

		// Validate (somewhat simplified as verbose messages will normally be shown client-side)
		$errors = false;

		// Required fields
		if ((($type == 'review') && !$comments) || (($type == 'rating') && !is_numeric($rating)) || (($type == 'comment_rating') && (!is_numeric($rating) || !$commentId)) || ($requireCaptcha && !$captcha)) {
			Messages::addMessage(Text::get('global_msg_complete_all_fields'), 'error');
			$errors = true;

		// Check captcha
		} elseif ($requireCaptcha && !Captcha::checkCode($captcha)) {
			Messages::addMessage(Text::get('global_captcha_msg_enter'), 'error');
			$errors = true;
		}
		if ($errors) {
			return $this->_showMessages($redirectTask, 'view');
		}

		// Add new comment
		switch ($type) {
			case 'review':
				$itemsModel->addReview($this->_itemId, $user['id'], $comments);
				Messages::addMessage(Text::get('item_msg_review_added'), 'info');
				break;
				
			case 'rating':
				$itemsModel->addRating($this->_itemId, $user['id'], $rating);
				Messages::addMessage(Text::get('item_msg_rating_added', array('rating' => $rating)), 'info');
				break;
				
			case 'comment_rating':
				$itemsModel->addCommentRating($commentId, $user['id'], $rating);
				Messages::addMessage(Text::get('item_msg_comment_rating_added'), 'info');
				break;
				
			case 'vote':
				$itemsModel->addVote($this->_itemId, $user['id']);
				Messages::addMessage(Text::get('item_msg_vote_added'), 'info');
				break;
		}
		
		// Return
		$itemsModel->getItem($this->_itemId, null, true);
		return $this->_showMessages($redirectTask, 'view', $type, true);
	}
	
	/**
	 *	Add current item to shopping list
	 *
	 *	@access public
	 */
	public function save_to_shopping_list()
	{
		return $this->_saveItem('shopping_list');
	}
	
	/**
	 *	Add current item to recipe box
	 *
	 *	@access public
	 */
	public function save_to_recipe_box()
	{
		return $this->_saveItem('recipebox');
	}
	
	/**
	 *	Add current item with a comment as recipe note
	 *
	 *	@access public
	 */
	public function save_recipe_note()
	{
		return $this->_saveItem('recipe_note');
	}
	
	/**
	 *	Save item against user
	 *
	 *	@access public
	 *	@param string Save type
	 */
	public function _saveItem($type)
	{
		// Get item
		$itemsModel = BluApplication::getModel('items');
		$item = $itemsModel->getItem($this->_itemId);
		
		// Got a logged in user?
		$userModel = BluApplication::getModel('user');
		if (!$user = $this->_requireUser()) {
			switch ($type) {
				case 'shopping_list':
					$task = 'save_to_shopping_list';
					break;
					
				case 'recipebox':
					$task = 'save_to_recipe_box';
					break;
					
				case 'recipe_note':
					$task = 'save_recipe_note';
					break;
			}
			$url = $itemsModel->getTaskLink($item['link'], $task);
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('item_msg_save_add_login', array('itemName' => $item['title'])), 'warn');
		}
		
		// Get request
		$comments = Request::getString('comments');

		// Add to saved items list
		switch ($type) {
			case 'shopping_list':
				$userModel->addToShoppinglist($item['id'], $user['id'], $comments);
				Messages::addMessage(Text::get('item_msg_save_added', array(
					'itemName' => $item['title'],
					'save_how' => 'to your shopping list'
				)), 'info');
				break;
				
			case 'recipebox':
				$userModel->addToRecipeBox($item['id'], $user['id'], $comments);
				Messages::addMessage(Text::get('item_msg_save_added', array(
					'itemName' => $item['title'],
					'save_how' => 'to your recipe box'
				)), 'info');
				break;
				
			case 'recipe_note':
				$userModel->saveRecipeNote($item['id'], $user['id'], $comments);
				$userModel->addToRecipeBox($item['id'], $user['id'], $comments);	// No point saving a note to find out you've forgotten what the damn recipe was called again, let's save it in the account area too.
				Messages::addMessage(Text::get('item_msg_save_added', array(
					'itemName' => $item['title'],
					'save_how' => 'with your recipe note'
				)), 'info');
				break;
		}

		// Display messages
		switch ($type) {
			case 'shopping_list':
				return $this->_redirect('/account/shopping_list');
				
			case 'recipebox':
				return $this->_redirect('/account/recipe_box');
				
			case 'recipe_note':
			default:
				return $this->_showMessages(null, 'view', 'saveitem', true);
		}
	}
	
	/**
	 *	Remove given item from shopping list
	 *
	 *	@access public
	 */
	public function remove_from_shopping_list()
	{
		return $this->_removeItem('shopping_list');
	}
	
	/**
	 *	Remove given item from recipe box
	 *
	 *	@access public
	 */
	public function remove_from_recipe_box()
	{
		return $this->_removeItem('recipebox');
	}
	
	/**
	 *	Remove a user-item mapping
	 *
	 *	@access protected
	 *	@param string Type
	 */
	protected function _removeItem($type)
	{
		// Get item
		$itemsModel = BluApplication::getModel('items');
		$item = $itemsModel->getItem($this->_itemId);
		$redirect = Request::getString('redirect');
		
		$userModel = BluApplication::getModel('user');
		if (!$user = $this->_requireUser()) {
			switch ($type) {
				case 'shopping_list':
					$task = 'remove_from_shopping_list';
					$how = 'from your shopping list';
					break;
					
				case 'recipebox':
					$task = 'remove_from_recipe_box';
					$how = 'from your recipe box';
					break;
			}
			$url = $itemsModel->getTaskLink($item['link'], $task);
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('item_msg_save_remove_login', array('itemName' => $item['title'])), 'warn');
		}
		
		switch ($type) {
			case 'shopping_list':
				$userModel->removeFromShoppingList($item['id'], $user['id']);
				Messages::addMessage(Text::get('item_msg_save_removed', array(
					'itemName' => $item['title'],
					'remove_how' => 'from your shopping list'
				)), 'info');
				break;
				
			case 'recipebox':
				$userModel->removeFromRecipeBox($item['id'], $user['id']);
				Messages::addMessage(Text::get('item_msg_save_removed', array(
					'itemName' => $item['title'],
					'remove_how' => 'from your recipe box'
				)), 'info');
				break;
		}
	
		// redirect the user, with a message, back to the page they were on if set
		if ($redirect) {
			return $this->_redirect(base64_decode($redirect));
		}

		// otherwise just return with the message
		return $this->_showMessages(null, 'view', 'saveitem', true);
	}
	
	/**
	 *	Interface for adding an article
	 *
	 *	@access public
	 */
	public function share()
	{
		// Get models
		$itemsModel = BluApplication::getModel('items');
		$metaModel = BluApplication::getModel('meta');
		
		$submitTask = 'share_submit';
		switch ($this->_itemType) {
			case 'article':
				$pageHeading = 'Write an article';
				$requireCategories = false;
				break;
				
			case 'recipe':
				$pageHeading = 'Share a recipe';
				$requireCategories = true;
				$step = 'Step 1: Enter recipe information';
				break;
				
			case 'blog':
				$pageHeading = 'Write a blog';
				$requireCategories = false;
				break;
		}
		
		// Selectable categories
		$categories = $this->_getMetaShareCategories();
		
		// Prefill categories
		$selectedCategories = array();
		if(Utility::iterable($categories)) {
			$selectedCategories = $categories;
			foreach ($selectedCategories as &$typeCategories) {
				$typeCategories = array();
			}
			unset($typeCategories);
		}
		
		// Get data from session
		$editedItems = Session::get('editedItems');
		if($editedItems && isset($editedItems['new'.$this->_itemType])) {
			$editedItem = $editedItems['new'.$this->_itemType];
			
			$title = $editedItem['title'];
			$body = $editedItem['body'];
			$teaser = $editedItem['teaser'];
			$video_js = $editedItem['video_js'];
			$goLiveDate = $editedItem['goLiveDate'];
			$keywords = $editedItem['keywords'];
			$description = $editedItem['description'];
			$selectedCategories = $editedItem['selectedCategories'];
			$terms = $editedItem['terms'];
			$default_alt = $editedItem['default_alt'];
			$thumbnail_alt = $editedItem['thumbnail_alt'];
			$featured_alt = $editedItem['featured_alt'];
			if(isset($editedItem['images']['default'])) {
				$image = basename($editedItem['images']['default']);
			}
			if(isset($editedItem['images']['thumbnail'])) {
				$thumbnail = basename($editedItem['images']['thumbnail']);
			}
			if(isset($editedItem['images']['featured'])) {
				$featuredImage = basename($editedItem['images']['featured']);
			}
			
			$slug = Utility::slugify($title);
			
			// Get type specific data
			switch ($this->_itemType) {
				case 'recipe':
					$ingredients = $editedItem['ingredients'];
					
					$outcome = $editedItem['outcome'];
					$servesPeople = $editedItem['servesPeople'];
					$yieldQuantity = $editedItem['yieldQuantity'];
					$yieldMeasure = $editedItem['yieldMeasure'];
					
					$preparationTimeQuantity = $editedItem['preparationTimeQuantity'];
					$preparationTimeMeasure = $editedItem['preparationTimeMeasure'];
					
					$cookingTimeQuantity = $editedItem['cookingTimeQuantity'];
					$cookingTimeMeasure = $editedItem['cookingTimeMeasure'];
					break;
			}
		
		}
		else {
		
			// Prefill inputs
			$title = Request::getString('title');
			$body = Request::getString('body', '', 'default', true);
			$teaser = Request::getString('teaser');
			$video_js = Request::getString('video_js', '', 'default', true);
			$goLiveDate = Request::getString('go_live_date');
			$keywords = Request::getString('keywords');
			$description = Request::getString('description');
			$selectedCategories = array_merge($selectedCategories, Request::getArray('categories', array()));
			$terms = Request::getBool('terms');
			$default_alt = Request::getString('default_alt','');
			$thumbnail_alt = $default_alt;//Request::getString('thumbnail_alt','');
			$featured_alt = $default_alt;//Request::getString('featured_alt','');
			// Type-specific display variables
			switch ($this->_itemType) {
				case 'recipe':
					$ingredients = Request::getString('ingredients');
					
					$outcome = Request::getString('outcome', 'serving');
					$servesPeople = Request::getString('serving');
					$yieldQuantity = Request::getString('yield_quantity');
					$yieldMeasure = Request::getString('yield_measure');
					
					$preparationTimeQuantity = Request::getString('preparation_time_quantity');
					$preparationTimeMeasure = Request::getString('preparation_time_measure');
					
					$cookingTimeQuantity = Request::getString('cooking_time_quantity');
					$cookingTimeMeasure = Request::getString('cooking_time_measure');
					break;
			}
		
		}
		
		Template::set('goLiveDate', $goLiveDate);
		Template::set('keywords', $keywords);
		Template::set('description', $description);
		
		// Tip for first-timers.
		if (!Request::getBool('submit')) {
			Messages::addMessage(Text::get('global_msg_fields_marked_required'));
		}
		
		// Cruelty to users
		$userModel = BluApplication::getModel('user');
		$user = $userModel->getCurrentUser();
		Template::set('tinyMce', $user && $user['type'] == 'admin');
		
		// God this is horrible
		$permissionsModel = BluApplication::getModel('permissions');
		$httpAuthenticated = $permissionsModel->canEdit();
		Template::set('adminPrivileges', $httpAuthenticated || ($user && $user['type'] != 'member'));
		
		// Link for deleting images
		$deleteImageLink = $this->_baseUrl.'delete_image';
		
		// Load template
		$this->_doc->setTitle($pageHeading);
		switch ($this->_itemType) {
			case 'article':
				include(BLUPATH_TEMPLATES.'/articles/article_write_new.php');
				break;
				
			case 'recipe':
				include(BLUPATH_TEMPLATES.'/recipes/recipe_write.php');
				break;
				
			case 'blog':
				Template::set('tinyMce', true); // always use TinyMCE
				include(BLUPATH_TEMPLATES.'/blogs/blog_write.php');
				break;
		}
	}
	
	/**
	 *	Preview submitted article before saving it
	 *
	 *	@access public
	 */
	public function share_normalize()
	{
		// Check for user
		$permissionsModel = BluApplication::getModel('permissions');
		if ((!$user = $permissionsModel->getUser()) && (!$user = $this->_requireUser())) {
			$url = '/share_submit';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('item_submit_login'), 'warn');
		}
		
		$previewTask = 'share_preview';
		$submitTask = 'share_preview';
		// Get data from session
		$editedItems = Session::get('editedItems');
		if($editedItems && isset($editedItems['new'.$this->_itemType])) {
			$item = $editedItems['new'.$this->_itemType];
			$changedIngredients = Request::getString('change_ingredients',null);
			if(!empty($changedIngredients)){
				$item['ingredients'] = $changedIngredients;
				$item['new'.$this->_itemType] = $item;
				Session::set('editedItems', $item);
			}
		}
		else {
			return $this->_redirect('/share_submit');
		}
		$item['author'] = $user;
		
		switch ($this->_itemType) {
			case 'recipe':
				$item['preparation_time']['quantity'] = $item['preparationTimeQuantity'];
				$item['preparation_time']['measure'] = $item['preparationTimeMeasure'];
				$item['cooking_time']['quantity'] = $item['cookingTimeQuantity'];
				$item['cooking_time']['measure'] = $item['cookingTimeMeasure'];				
				$item['ingredients'] = explode("\n", $item['ingredients']);
				$ingredientsModel = BluApplication::getModel('ingredients');
				$item['tidyIngredients'] = $ingredientsModel->normalizeIngredients($item['ingredients']);
				$step = 'Step 2: Review your ingredients';
				include(BLUPATH_TEMPLATES.'/recipes/recipe_normalize.php');
				break;
		}
	}	
	
	/**
	 *	Preview submitted article before saving it
	 *
	 *	@access public
	 */
	public function share_preview()
	{
		// Check for user
		$permissionsModel = BluApplication::getModel('permissions');
		if ((!$user = $permissionsModel->getUser()) && (!$user = $this->_requireUser())) {
			$url = '/share_submit';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('item_submit_login'), 'warn');
		}
		
		if(Request::getBool('back')) {
			switch ($this->_itemType) {
				case 'article':
					$redirectUrl = '/articles/share';
					break;
				case 'recipe':
					$redirectUrl = '/share';
					break;
			}
			return $this->_redirect($redirectUrl);
		}
		
		$previewTask = 'share_preview';
		$submitTask = 'share_submit';
		
		// Get data from session
		$editedItems = Session::get('editedItems');
		if($editedItems && isset($editedItems['new'.$this->_itemType])) {
			$item = $editedItems['new'.$this->_itemType];
		}
		else {
			return $this->_redirect('/share_submit');
		}
		$item['author'] = $user;
		
		// image
		if(!empty($item['images']) && is_array($item['images'])) {
			if($assetPath = reset($item['images'])) {
				$image = basename($assetPath);
			}
		}
		$userModel = BluApplication::getModel('user');
		$user = $userModel->getCurrentUser();
		switch ($this->_itemType) {
			case 'article':
				
				include(BLUPATH_TEMPLATES.'/articles/article_preview.php');
				break;
				
			case 'recipe':
				$previewTask = ($user['type'] == 'admin')?'share_normalize':'share_preview'; //change to normalize once done				
				$item['preparation_time']['quantity'] = $item['preparationTimeQuantity'];
				$item['preparation_time']['measure'] = $item['preparationTimeMeasure'];
				$item['cooking_time']['quantity'] = $item['cookingTimeQuantity'];
				$item['cooking_time']['measure'] = $item['cookingTimeMeasure'];
				$item['ingredients'] = explode("\n", $item['ingredients']);
				$step = 'Final step: Preview your recipe';
				$ingredientsModel = BluApplication::getModel('ingredients');
				$item['tidyIngredients'] = $ingredientsModel->normalizeIngredients($item['ingredients']);
				include(BLUPATH_TEMPLATES.'/recipes/recipe_preview.php');
				break;
		}
	}
	
	/**
	 *	Preview submitted article before saving it
	 *
	 *	@access public
	 */
	public function edit_preview()
	{
		// Get models
		$itemsModel = BluApplication::getModel('items');
		
		// Check for user
		$permissionsModel = BluApplication::getModel('permissions');
		if ((!$user = $permissionsModel->getUser()) && (!$user = $this->_requireUser())) {
			$url = '/edit_submit';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('item_submit_login'), 'warn');
		}
		// Get item
		$item = $itemsModel->getItem($this->_itemId);
		$slug = $item['slug']; 
		// Get data from session
		$editedItems = Session::get('editedItems');
		if($editedItems && isset($editedItems[$this->_itemId])) {
			$editedItem = $editedItems[$this->_itemId];
		}
		else {
			return $this->_redirect('/edit_submit');
		}		
		$submitUrl = $itemsModel->getTaskLink($editedItem['link'], 'edit');
		
/*		if(Request::getBool('back')) {
			return $this->_redirect('/edit/'.$slug.'.htm');
			return $this->_redirect($submitUrl);
		}*/
		
		if(Request::getBool('back')) {
			switch ($this->_itemType) {
				case 'article':
					$redirectUrl = '/articles/edit/'.$slug.'.htm';
					break;
				case 'recipe':
					$redirectUrl = '/edit/'.$slug.'.htm';
					break;
			}
			return $this->_redirect($redirectUrl);
		}
		
		// user
		$userModel = BluApplication::getModel('user');
		$user = $userModel->getCurrentUser();
//		$previewTask = ($user['type'] == 'admin')?'edit_normalize':'edit_preview'; //change edit to normalize once done to remove skiping		
		$previewTask = 'edit_preview'; //change edit to normalize once done to remove skiping
		$submitTask = 'edit_submit';
		
		$editedItem['author'] = $user;
		
		// image
		if(!empty($editedItem['images']) && is_array($editedItem['images'])) {
			if($assetPath = reset($editedItem['images'])) {
				$image = basename($assetPath);
			}
		}
		if(isset($item['image']['filename'])) {
			$editedItem['image'] = $item['image'];
		}
		
		switch ($this->_itemType) {
			case 'article':
				
				$item = $editedItem; // this is here because we are reusing templates
				
				include(BLUPATH_TEMPLATES.'/articles/article_preview.php');
				break;
				
			case 'recipe':
				$previewTask = ($user['type'] == 'admin')?'edit_normalize':'edit_preview'; //change edit to normalize once done to remove skiping
				$editedItem['preparation_time']['quantity'] = $editedItem['preparationTimeQuantity'];
				$editedItem['preparation_time']['measure'] = $editedItem['preparationTimeMeasure'];
				$editedItem['cooking_time']['quantity'] = $editedItem['cookingTimeQuantity'];
				$editedItem['cooking_time']['measure'] = $editedItem['cookingTimeMeasure'];
				$editedItem['ingredients'] = split("\n", $editedItem['ingredients']);
				$step = 'Final step: Preview your recipe';
				$ingredientsModel = BluApplication::getModel('ingredients');
				$editedItem['tidyIngredients'] = $ingredientsModel->normalizeIngredients($editedItem['ingredients']);
				$item = $editedItem; // this is here because we are reusing templates
				
				include(BLUPATH_TEMPLATES.'/recipes/recipe_preview.php');
				break;
		}
	}

	public function edit_normalize()
	{
		// Get models
		$itemsModel = BluApplication::getModel('items');
		
		// Check for user
		$permissionsModel = BluApplication::getModel('permissions');
		if ((!$user = $permissionsModel->getUser()) && (!$user = $this->_requireUser())) {
			$url = '/edit_submit';
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('item_submit_login'), 'warn');
		}

		// Get item
		$item = $itemsModel->getItem($this->_itemId);
		$slug = $item['slug']; 
		// Get data from session
		$editedItems = Session::get('editedItems');
		if($editedItems && isset($editedItems[$this->_itemId])) {
			$editedItem = $editedItems[$this->_itemId];
			$changedIngredients = Request::getString('change_ingredients',null);
			if(!empty($changedIngredients)){
				$editedItem['ingredients'] = $changedIngredients;
				$editedItem[$this->_itemId] = $editedItem; 
				Session::set('editedItems', $editedItem);
			}
		}
		else {
			return $this->_redirect('/edit_submit');
		}
	
		$submitUrl = $itemsModel->getTaskLink($editedItem['link'], 'edit');
		
		if(Request::getBool('back')) {
			return $this->_redirect('/edit_normalize/'.$slug.'.htm');
			return $this->_redirect($submitUrl);
		}
		
		$previewTask = 'edit_preview';
		$submitTask = 'edit_preview';
		
		$editedItem['author'] = $user;
		
		// image
		if(!empty($editedItem['images']) && is_array($editedItem['images'])) {
			if($assetPath = reset($editedItem['images'])) {
				$image = basename($assetPath);
			}
		}
		if(isset($item['image']['filename'])) {
			$editedItem['image'] = $item['image'];
		}
		
		switch ($this->_itemType) {
			case 'recipe':
				$editedItem['preparation_time']['quantity'] = $editedItem['preparationTimeQuantity'];
				$editedItem['preparation_time']['measure'] = $editedItem['preparationTimeMeasure'];
				$editedItem['cooking_time']['quantity'] = $editedItem['cookingTimeQuantity'];
				$editedItem['cooking_time']['measure'] = $editedItem['cookingTimeMeasure'];
				$editedItem['ingredients'] = split("\n", $editedItem['ingredients']);
				$ingredientsModel = BluApplication::getModel('ingredients');
				$editedItem['tidyIngredients'] = $ingredientsModel->normalizeIngredients($editedItem['ingredients']);
				$step = 'Step 2: Review your ingredients';
				$item = $editedItem; // this is here because we are reusing templates
				include(BLUPATH_TEMPLATES.'/recipes/recipe_normalize.php');
				break;
		}
	}
		
	/**
	 *	Add a new article
	 *
	 *	@access public
	 */
	public function share_submit()
	{
		// Get settings
		$autoSlug = true;
		switch ($this->_itemType) {
			case 'article':
				$requireCategories = false;
				break;
				
			case 'recipe':
				$requireCategories = true;
				break;
				
			case 'blog':
				$requireCategories = false;
				break;
		}
		
		// Get models
		$itemsModel = BluApplication::getModel('items');
		$userModel = BluApplication::getModel('user');
		$permissionsModel = BluApplication::getModel('permissions');
		
		// Check for user

		$userId = Session::get('UserID');
		$user = $userModel->getArticleAddUser($userId);
		if(( $user !== false) && ($this->_itemType == 'article'))
		{
		}else{
			if ((!$user = $permissionsModel->getUser()) && (!$user = $this->_requireUser())) {
				if($this->_itemType=='recipe') {
					$url = '/share_submit';
				}
				else {
					$url = $this->_baseUrl.'share';
				}
				$url = '/account/login?redirect='.base64_encode($url);
				return $this->_redirect($url, Text::get('item_submit_login'), 'warn');
			}
		}
		// Get data from session
		$editedItems = Session::get('editedItems');
		
		if(Request::getBool('save')) {
			if($editedItems && isset($editedItems['new'.$this->_itemType])) {
				$editedItem = $editedItems['new'.$this->_itemType];
			}
			else {
				return $this->_redirect('/share');
			}
			
			$title = $editedItem['title'];
			$body = $editedItem['body'];
			$teaser = $editedItem['teaser'];
			$video_js = $editedItem['video_js'];
			$goLiveDate = $editedItem['goLiveDate'];
			$keywords = $editedItem['keywords'];
			$description = $editedItem['description'];
			$selectedCategories = $editedItem['selectedCategories'];
			$terms = $editedItem['terms'];
			$images = $editedItem['images'];
			$default_alt = $editedItem['default_alt'];
			$thumbnail_alt = $default_alt;//$editedItem['thumbnail_alt'];
			$featured_alt = $default_alt;//$editedItem['featured_alt'];
			// Get type specific data
			switch ($this->_itemType) {
				case 'recipe':
					$ingredients = $editedItem['ingredients'];
					$outcome = $editedItem['outcome'];
					$servesPeople = $editedItem['servesPeople'];
					$yieldQuantity = $editedItem['yieldQuantity'];
					$yieldMeasure = $editedItem['yieldMeasure'];
					
					$preparationTimeQuantity = $editedItem['preparationTimeQuantity'];
					$preparationTimeMeasure = $editedItem['preparationTimeMeasure'];
					
					$cookingTimeQuantity = $editedItem['cookingTimeQuantity'];
					$cookingTimeMeasure = $editedItem['cookingTimeMeasure'];
					break;
			}
		
		}
		else {
			
			// Get data from request
			$title = trim(Request::getString('title'));
			$body = trim(Request::getString('body', null, 'default', true));
			$teaser = trim(Request::getString('teaser'));
			$video_js = trim(Request::getString('video_js', null, 'default', true));
			$goLiveDate = trim(Request::getString('go_live_date'));
			$keywords = trim(Request::getString('keywords'));
			$description = trim(Request::getString('description'));
			$selectedCategories = Request::getArray('categories');
			$queueId = Request::getString('queueid', md5(uniqid()));
			$terms = Request::getBool('terms');
			$default_alt = trim(Request::getString('default_alt'));
			$thumbnail_alt = $default_alt;//trim(Request::getString('thumbnail_alt'));
			$featured_alt = $default_alt;//trim(Request::getString('featured_alt'));
			// Get type specific data
			switch ($this->_itemType) {
				case 'recipe':
					$ingredients = trim(Request::getString('ingredients'));
					
					$outcome = Request::getString('outcome');
					$servesPeople = Request::getString('serving');
					$yieldQuantity = Request::getString('yield_quantity');
					$yieldMeasure = Request::getString('yield_measure');
					
					$preparationTimeQuantity = Request::getString('preparation_time_quantity');
					$preparationTimeMeasure = Request::getString('preparation_time_measure');
					
					$cookingTimeQuantity = Request::getString('cooking_time_quantity');
					$cookingTimeMeasure = Request::getString('cooking_time_measure');
					break;
			}
		
		}
		
		$slug = Utility::slugify($title);
		
		// Validate
		$errors = false;
		
		// Required fields
		if (!$title || !trim(strip_tags($body)) || ($requireCategories && empty($selectedCategories))) {
			Messages::addMessage(Text::get('global_msg_complete_all_fields'), 'error');
			$errors = true;
			
		// The generated slug was not allowed
		} elseif ($itemsModel->isSlugInUse($slug) && !$autoSlug) {
			Messages::addMessage(Text::get('item_msg_title_in_use'), 'error');
			$errors = true;
			
		// Terms and conditions
		} else if (!$terms && $this->_itemType!='blog') {
			Messages::addMessage(Text::get('item_submit_terms'), 'error');
			$errors = true;
			
		// Type specific validation
		} else {
			switch ($this->_itemType) {
				case 'recipe':
					
					// Some variables...
					$requireSize = true;
					$requireTime = false;
					$sizeGiven = !(($outcome == 'serving' && !$servesPeople) || ($outcome == 'yield' && (!$yieldQuantity || !$yieldMeasure)));
					$timeGiven = !(!strlen($preparationTimeQuantity) || !$preparationTimeMeasure || !strlen($cookingTimeQuantity) || !$cookingTimeMeasure);
					
					// Required fields
					if (!$ingredients || !$outcome || ($requireSize && !$sizeGiven) || ($requireTime && !$timeGiven)) {
						Messages::addMessage(Text::get('global_msg_complete_all_fields'), 'error');
						$errors = true;
					}
					break;
			}
		}
		// Go Live Date if entered
		$goLiveTimestamp = strtotime($goLiveDate);
		if($goLiveDate && (!$goLiveTimestamp || $goLiveTimestamp<mktime(0, 0, 0, date('m')  , date('d'), date('Y')))) {
			Messages::addMessage(Text::get('item_submit_golivedate'), 'error');
			$errors = true;
		}
		
		// Auto-generate the slug
		if ($itemsModel->isSlugInUse($slug) && $autoSlug) {
			$i = 2;
			while ($itemsModel->isSlugInUse($slug.'_'.$i)) {
				$i++;
			}
			$slug .= '_'.$i;
		}
		// Validation error redirect
		if ($errors) {
			Template::set('goLiveDate', $goLiveDate);
			Template::set('keywords', $keywords);
			Template::set('description', $description);
			return $this->_showMessages('share', 'share');
		}
		// After validation redirect to preview
		elseif(!Request::getBool('save')) {

			// Upload image
			$result = $this->_saveUpload($queueId, 'default', false, array('png', 'jpg', 'jpeg', 'gif', 'bmp'));
			$uploadStatus = '';
			$errorMsg = '';
			if(isset($result['result']) && $result['result'] =='success')
			{
				$uploadStatus = 'success';
			}
			
			if (isset($result['error'])) {
				$uploadStatus = 'failed';
				$errorMsg = $result['error'];
				Messages::addMessage($result['error'], 'error');
			}			
			
			/*$result = $this->_saveUpload($queueId, 'thumbnail', false, array('png', 'jpg', 'jpeg', 'gif', 'bmp'));
			if (isset($result['error'])) {
				Messages::addMessage($result['error'], 'error');
			}
			$result = $this->_saveUpload($queueId, 'featured', false, array('png', 'jpg', 'jpeg', 'gif', 'bmp'));
			if (isset($result['error'])) {
				Messages::addMessage($result['error'], 'error');
			}*/
			$assets = Upload::getQueue($queueId);
			
			if (!empty($assets)) {
				
				$tempDir = BLUPATH_ASSETS.'/tempimages/';
				if (!file_exists($destDir)) {
					mkdir($tempDir, 0777, true);
				}
				foreach ($assets as $uploadId => $file) {
					// move images to temporary location until the article is really saved
					$origFileName = basename($file['name']);
					$assetFileName = $file['field_name'].'_'.md5(microtime().mt_rand(0, 250000)).'_'.$origFileName;					
					$assetPath = BLUPATH_ASSETS.'/tempimages/'.$assetFileName;
					if(Upload::move($uploadId, $assetPath)) {
						$images[$file['field_name']] = $assetPath;
					}
				}
				Upload::clearQueue($queueId);
				Session::set('origFileName', $origFileName);
				Session::set('assetFileName', $assetFileName);
				Session::set('uploadStatus', $uploadStatus);
				Session::set('errorMsg', $errorMsg);
			}
			
			if(Request::getBool('preview') || Request::getBool('normalize')) {
				
				// Merge previously uploaded files
				if($editedItems['new'.$this->_itemType]['images'] && is_array($editedItems['new'.$this->_itemType]['images'])) {
					foreach($editedItems['new'.$this->_itemType]['images'] as $imageType=>$assetPath) {
						if(!isset($images[$imageType])) {
							$images[$imageType] = $assetPath;
						}
					}
				}
				
				// Save submitted data to sesssion
				$editedItem = array();
				$editedItem['title'] = $title;
				$editedItem['teaser'] = $teaser;
				$editedItem['video_js'] = $video_js;
				$editedItem['goLiveDate'] = $goLiveDate;
				$editedItem['body'] = $body;
				$editedItem['keywords'] = $keywords;
				$editedItem['description'] = $description;
				$editedItem['selectedCategories'] = $selectedCategories;
				$editedItem['terms'] = $terms;
				$editedItem['images'] = $images;
				$editedItem['default_alt'] = $default_alt;
				$editedItem['thumbnail_alt'] = $thumbnail_alt;
				$editedItem['featured_alt'] = $featured_alt;
				switch ($this->_itemType) {
					case 'recipe':
						$editedItem['ingredients'] = $ingredients;
						$ingredientsModel = BluApplication::getModel('ingredients');
						$editedItem['outcome'] = $outcome;
						$editedItem['servesPeople'] = $servesPeople;
						$editedItem['yieldQuantity'] = $yieldQuantity;
						$editedItem['yieldMeasure'] = $yieldMeasure;
						$editedItem['preparationTimeQuantity'] = $preparationTimeQuantity;
						$editedItem['preparationTimeMeasure'] = $preparationTimeMeasure;
						$editedItem['cookingTimeQuantity'] = $cookingTimeQuantity;
						$editedItem['cookingTimeMeasure'] = $cookingTimeMeasure;
						$editedItem['tidyIngredients'] = $ingredientsModel->normalizeIngredients($editedItem['ingredients']);
					break;
				}
				
				$editedItems = Session::get('editedItems');
				if(!$editedItems) {
					$editedItems = array();
				}
				$editedItems['new'.$this->_itemType] = $editedItem;
				
				Session::set('editedItems', $editedItems);
				
				// Redirect
				if(Request::getBool('preview')){
					switch ($this->_itemType) {
						case 'article':
							return $this->_redirect('/articles/share_preview');
							break;
						case 'recipe':
							$step = 'Final step: Preview your recipe';
							return $this->_redirect('/share_preview');
							break;
					}
				}else if(Request::getBool('normalize')){
					switch ($this->_itemType) {
						case 'recipe':
							$step = 'Step 2: Review your ingredients';
							return $this->_redirect('/share_normalize');
							break;
					}					
				}
				
			}
			
		}
		
		if($goLiveTimestamp) {
			$goLiveDate = date('Y-m-d', $goLiveTimestamp);
		}
		else {
			$goLiveDate = null;
		}
		
		// Save article
		$requireReview = BluApplication::getSetting('requireSubmissionReview', false);
		switch ($this->_itemType) {
			case 'article':
				$itemId = $itemsModel->addArticle($title, $user['id'], $body, $teaser, $goLiveDate, $keywords, $description, $slug, !$requireReview,$video_js);
				break;
				
			case 'recipe':
				$itemId = $itemsModel->addRecipe($title, $user['id'], $body, $teaser, $goLiveDate, $keywords, $description, $slug, !$requireReview,$video_js);
				break;
				
			case 'blog':
				$requireReview = true;
				$itemId = $itemsModel->addBlog($title, $user['id'], $body, $teaser, $goLiveDate, $keywords, $description, $slug, !$requireReview);
				break;
		}
		
		// Set as current item
		$this->_itemId = $itemId;		
		if(Session::get('uploadStatus') !='')
		{
			$logResult = $itemsModel->logArticleImageActivities($user['id'],$itemId,Session::get('origFileName'),Session::get('assetFileName'),BLUPATH_ASSETS.'/itemimages/',Session::get('uploadStatus'),Session::get('errorMsg'));
			Session::set('origFileName', '');
			Session::set('assetFileName', '');
			Session::set('uploadStatus', '');
			Session::set('errorMsg','');
		}
		
		// Assign images/links/meta values to the item.
		if ($this->_itemId) {
			
			if(!empty($images)) {
				if(isset($images['default'])) {
					$itemsModel->deleteAllImages($this->_itemId,'default');
					$itemsModel->copyUnsavedImage($this->_itemId, $images['default']);
					$itemsModel->addImage($this->_itemId, basename($images['default']), null, null, null, null, 'default', $user['id'],$default_alt);
				}
				if(isset($images['thumbnail'])) {
					$itemsModel->deleteAllImages($this->_itemId,'thumbnail');
					$itemsModel->copyUnsavedImage($this->_itemId, $images['thumbnail']);
					$itemsModel->addImage($this->_itemId, basename($images['thumbnail']), null, null, null, null, 'thumbnail', $user['id'],$thumbnail_alt);
				}
				if(isset($images['featured'])) {
					$itemsModel->deleteAllImages($this->_itemId,'featured');
					$itemsModel->copyUnsavedImage($this->_itemId, $images['featured']);
					$itemsModel->addImage($this->_itemId, basename($images['featured']), null, null, null, null, 'featured', $user['id'],$featured_alt);
				}
			}
				
				// Delete all categories
				$this->_deleteItemCategories($this->_itemId);
				/**
				 * added by leon
				 * only deal with the create article
				 */
				if (!empty($selectedCategories) && $this->_itemType == 'article')
				{
					//echo $this->_itemId;
					//print_r($selectedCategories);
					$itemsModel->saveArticleItem($selectedCategories['main_ingredients'],$this->_itemId);
				}else{
				
					/*----------------  begins -------------------*/
					
					if (!empty($selectedCategories)) {
						
						// Reapply category assignments
						foreach ($selectedCategories as $categoryType => $submittedCategories) {
							$itemsModel->assignItemCategory($this->_itemId, $categoryType);
							
							foreach ($submittedCategories as $category) {
							
								$itemsModel->assignItemCategory($this->_itemId, $category);
							}
						}
					}
				}
					/*----------------  end -------------------*/


            // add by leon
            // save to solr search engine
            require_once(BLUPATH_BASE . '/leon/solr/solr.config.search.php');
            global $solrSearch, $solrSearchFlag;
            if(isset($solrSearchFlag) && $solrSearchFlag){
                
                $doc = new Apache_Solr_Document();
                $doc->addField('id', $this->_itemId);
                $doc->addField('title', htmlspecialchars($title));
                $doc->addField('body', htmlspecialchars($body));
                $doc->addField('teaser', htmlspecialchars($teaser));
                $doc->addField('keywords', htmlspecialchars($keywords));
                $doc->addField('description', htmlspecialchars($description));
		$doc->addField('username', htmlspecialchars($user["username"]));

                //echo $rawPost;
                $solrSearch->addDocument($doc);
                $solrSearch->commit();
            }

			
			// Type specific additions
			switch ($this->_itemType) {
				case 'recipe':
					
					// Ingredients - propose ingredients to use
					$ingredients = explode("\n", $ingredients);
					$ingredients = array_filter($ingredients);
					$itemsModel->proposeIngredients($this->_itemId, $ingredients, null,  false);
					// Ingredients - propose ingredients to use
					// Ingredients - normalized ingredients to use
					$ingredientsModel = BluApplication::getModel('ingredients');
					$tidyIngredients = $ingredientsModel->normalizeIngredients($ingredients);
					$ingredientsModel->setRecipeIngredients($this->_itemId, $tidyIngredients);
					// Ingredients - normalized ingredients to use
										
					// Outcome
					switch ($outcome) {
						case 'serving':
							$itemsModel->setRecipeYield($this->_itemId, $servesPeople, 'people', false);
							break;
							
						case 'yield':
							$itemsModel->setRecipeYield($this->_itemId, $yieldQuantity, $yieldMeasure, false);
							break;
					}
					
					// Preparation time
					$itemsModel->setRecipePreparationTime($this->_itemId, $preparationTimeQuantity, $preparationTimeMeasure, false);
					
					// Cooking time
					$itemsModel->setRecipeCookingTime($this->_itemId, $cookingTimeQuantity, $cookingTimeMeasure, false);
					break;
			}
			
			$editedItems = Session::get('editedItems');
			unset($editedItems['new'.$this->_itemType]);
			Session::set('editedItems', $editedItems);
			
			// Output message and continue with template.
			if ($requireReview) {
				Messages::addMessage(Text::get('item_submission_needs_review'), 'info');
				
			// Output message and redirect to page.
			} else {
				Messages::addMessage(Text::get('item_submission_success'), 'info');
				$item = $itemsModel->getItem($this->_itemId, null, true);
				return $this->_redirect($item['link']);
			}
			
		// Failed
		} else {
			
			// Show error message and continue with template.
			switch ($this->_itemType) {
				case 'article':
				case 'recipe':
				case 'blog':
					Messages::addMessage(Text::get('item_submission_failed', array('itemType' => $this->_itemType)), 'error');
					break;
			}
		}

		
		// Redirect
		return $this->_showMessages('share', 'share');
	}
	
	/**
	 *	Editing an article
	 *
	 *	@access public
	 */
	public function edit()
	{
		// Get models
		$itemsModel = BluApplication::getModel('items');
		$metaModel = BluApplication::getModel('meta');
		
		// Check edit permissions
		$item = $itemsModel->getItem($this->_itemId);
		if (!$item['canEdit']) {
			Messages::addMessage(Text::get('item_edit_noperm', array('itemType' => $item['type'])), 'error');
			return $this->_showMessages('share', 'share', 'default', true);
		}
		$itemsModel->buildRecipeMeta($item,true);		
		
		$submitTask = 'edit_submit';
		switch ($this->_itemType) {
			case 'article':
				$pageHeading = 'Edit your article';
				$requireCategories = false;
				break;
				
			case 'recipe':
				$pageHeading = 'Edit your recipe';
				$requireCategories = true;
				break;
				
			case 'blog':
				$pageHeading = 'Edit your blog post';
				$requireCategories = true;
				break;
		}
		
		// Selectable categories
		$categories = $this->_getMetaShareCategories();
		
		// Intersect item's categories with selectable categories
		// Oh god this is such a horrible horrible hack, but I'm so tired.
		$itemMetaGroups = $metaModel->getItemMetaGroups($item['id']);
		//print_r($itemMetaGroups);
		$selectedCategories = array();
		$metaValueGroupMapping = $metaModel->getMetaValueGroupMapping();
		if(Utility::iterable($categories)) {
			foreach ($categories as $categoryType => $typeCategories) {
				$categoryType = $typeCategories['slug'];

				$selectedCategories[$categoryType] = array();
				foreach ($typeCategories['values'] as $category) {
					
					// Get meta value ID and its meta group ID
					$valueId = $metaModel->getValueIdBySlug($category['slug']);
					$groupId = $metaValueGroupMapping[$valueId];
					// Extract accordingly
					if (isset($itemMetaGroups[$groupId]['values'][$valueId])) {
						$selectedCategories[$categoryType][$category['slug']] = $category['slug'];
					}
				}
			}
		}
	
		// override item if the user comes back from the preview page
		$editedItems = Session::get('editedItems');
		if($editedItems && isset($editedItems[$this->_itemId])) {
			$editedItem = $editedItems[$this->_itemId];
			
			$item['title'] = $editedItem['title'];
			$item['body'] = $editedItem['body'];
			$item['teaser'] = $editedItem['teaser'];
			$item['video_js'] = $editedItem['video_js'];
			$item['goLiveDate'] = $editedItem['goLiveDate'];
			$item['keywords'] = $editedItem['keywords'];
			$item['description'] = $editedItem['description'];
			$item['selectedCategories'] = $editedItem['selectedCategories'];
			$item['terms'] = $editedItem['terms'];
			$item['default_alt'] = $editedItem['default_alt'];
			$item['thumbnail_alt'] = $editedItem['default_alt'];//$editedItem['thumbnail_alt'];
			$item['featured_alt'] = $editedItem['default_alt'];//$editedItem['featured_alt']; 
			if(isset($editedItem['images']['default'])) {
				$image = basename($editedItem['images']['default']);
			}
			if(isset($editedItem['images']['thumbnail'])) {
				$thumbnail = basename($editedItem['images']['thumbnail']);
			}
			if(isset($editedItem['images']['featured'])) {
				$featuredImage = basename($editedItem['images']['featured']);
			}
			
			// Get type specific data
			switch ($this->_itemType) {
				case 'recipe':
					$item['ingredients'] = explode("\n", $editedItem['ingredients']);
					
					$item['outcome'] = $editedItem['outcome'];
					$item['servesPeople'] = $editedItem['servesPeople'];
					$item['yieldQuantity'] = $editedItem['yieldQuantity'];
					$item['yieldMeasure'] = $editedItem['yieldMeasure'];
					
					$item['preparationTimeQuantity'] = $editedItem['preparationTimeQuantity'];
					$item['preparationTimeMeasure'] = $editedItem['preparationTimeMeasure'];
					
					$item['cookingTimeQuantity'] = $editedItem['cookingTimeQuantity'];
					$item['cookingTimeMeasure'] = $editedItem['cookingTimeMeasure'];
					break;
			}
			
		}
		
		// Prefill inputs
		$title = Request::getString('title', $item['title']);
		$body = Request::getString('body', $item['body'], 'default', true);
		$teaser = Request::getString('teaser', $item['teaser']);
		$video_js = Request::getString('video_js', $item['video_js'], 'default', true);
		$goLiveDate = Request::getString('go_live_date', $item['goLiveDate']);
		$keywords = Request::getString('keywords', $item['keywords']);
		$description = Request::getString('description', $item['description']);
		$tempArray = (!empty($item['selectedCategories']))?$item['selectedCategories']:array();
		$selectedCategories = array_merge($selectedCategories, Request::getArray('categories', $tempArray));
		$terms = Request::getBool('terms', true);
		$default_alt = Request::getString('default_alt', $item['default_alt']);
		$thumbnail_alt = $default_alt;//Request::getString('thumbnail_alt', $item['thumbnail_alt']);
		$featured_alt = $default_alt;//Request::getString('featured_alt', $item['featured_alt']);
		// Auto-generate keywords from description, if none exist
		if (!$keywords) {
			$keywordsBase = $item['body'];
			
			if (isset($item['ingredients']) && count($item['ingredients'])) {
				$keywordsBase = implode(' ',$item['ingredients']);

				// Work out category names
				$categories = $this->_getMetaShareCategories();
				$keywordCategories = array();
				foreach ($selectedCategories as $catGroupSlug => $catGroup) {
					foreach ($catGroup['values'] as $catValue) {
						foreach ($categories[$catGroup['slug']] as $cat) {
							if ($cat['slug'] == $catValue) {
								$keywordCategories[] = $cat['name'];
								break;
							}
						}
					}
				}
				$keywordsBase .= ' '.implode(' ',$keywordCategories);
			}
			$keywords = Text::filterCommonWords(strip_tags(strtolower($keywordsBase)));
			/*
			Here is some frequency analysis stuff.  Could work nicely, but only when we filter out even more common words
			Set $uncommonWords to the value of $keywords above to make it work
			$keywords = array();
			foreach ($uncommonWords as $word) {
				$keywords[$word]++;
			}
			asort($keywords);
			$keywords = array_keys($keywords);
			*/
			$keywords = array_slice($keywords,0, 20);
			$keywords = implode(', ', $keywords);
		}
		
		// Type-specific display variables
		switch ($this->_itemType) {
			case 'recipe':
				
				// Get ingredients
				if ($ingredients = $item['ingredients']) {
					$ingredients = implode("\n", $ingredients);
				}
				$ingredients = Request::getString('ingredients', $ingredients);
				
				// Pull out serving_size, or yield, then set $outcome accordingly
				if ($item['yield']['measure'] == 'people') {
					$outcome = 'serving';
					$servesPeople = $item['yield']['quantity'];
					$yieldQuantity = '';
					$yieldMeasure = '';
				} else {
					$outcome = 'yield';
					$servesPeople = '';
					$yieldQuantity = $item['yield']['quantity'];
					$yieldMeasure = $item['yield']['measure'];
				}
				
				// Replace with previous request
				$outcome = Request::getString('outcome', isset($item['outcome'])?$item['outcome']:$outcome);
				$servesPeople = Request::getString('serving', isset($item['servesPeople'])?$item['servesPeople']:$servesPeople);
				$yieldQuantity = Request::getString('yield_quantity', isset($item['yieldQuantity'])?$item['yieldQuantity']:$yieldQuantity);
				$yieldMeasure = Request::getString('yield_measure', isset($item['yieldMeasure'])?$item['yieldMeasure']:$yieldMeasure);
				
				// Pull out preparation/cooking time quantity/measure and parse
				$preparationTimeQuantity = Request::getString('preparation_time_quantity', isset($item['preparationTimeQuantity'])?$item['preparationTimeQuantity']:$item['preparation_time']['quantity']);
				$preparationTimeMeasure = Request::getString('preparation_time_measure', isset($item['preparationTimeMeasure'])?$item['preparationTimeMeasure']:$item['preparation_time']['measure']);
				
				$cookingTimeQuantity = Request::getString('cooking_time_quantity', isset($item['cookingTimeQuantity'])?$item['cookingTimeQuantity']:$item['cooking_time']['quantity']);
				$cookingTimeMeasure = Request::getString('cooking_time_measure', isset($item['cookingTimeMeasure'])?$item['cookingTimeMeasure']:$item['cooking_time']['measure']);
				break;
		}
		
		Template::set('goLiveDate', $goLiveDate);
		Template::set('keywords', $keywords);
		Template::set('description', $description);
		
		// Tip
		if (!Request::getBool('submit')) {
			Messages::addMessage(Text::get('global_msg_fields_marked_required'));
		}
		
		// Link for editing relationships
		$editRelatedLink = $itemsModel->getTaskLink($item['link'], 'edit_related');
        
        // Set for the slideArticles
        if(strpos($item['link'], 'slidearticles') !== false){
            $changeLink = str_replace('slidearticles/details', 'articles', $item['link']);
            $linka = explode('/', $changeLink);
            array_pop($linka);
            $changeLink = implode('/', $linka);
            $editRelatedLink = $itemsModel->getTaskLink($changeLink, 'edit_related');
            $editRelatedLink = $editRelatedLink . '.htm';
        }
        
		// Link for deleting images
		$deleteImageLink = $itemsModel->getTaskLink($item['link'], 'delete_image');
		
		// Cruelty to users
		$userModel = BluApplication::getModel('user');
		$user = $userModel->getCurrentUser();
		Template::set('adminPrivileges', $user && $user['type'] == 'admin');
		if (!Template::get('adminPrivileges')) {
			$permissionsModel = BluApplication::getModel('permissions');
			if ($permissionsModel->canEdit()) {
				Template::set('adminPrivileges', true);
			}
		}
		Template::set('tinyMce', Template::get('adminPrivileges'));
		Template::set('user', $user);

		// Load template
		switch ($this->_itemType) {
			case 'article':
				include(BLUPATH_TEMPLATES.'/articles/article_write.php');
				break;
				
			case 'recipe':
				include(BLUPATH_TEMPLATES.'/recipes/recipe_write.php');
				break;
				
			case 'blog':
				include(BLUPATH_TEMPLATES.'/blogs/blog_write.php');
				break;
		}
	}
	
	/**
	 *	Edit an article
	 *
	 *	@access public
	 */
	public function edit_submit()
	{
		// Get settings
		$autoSlug = true;
		switch ($this->_itemType) {
			case 'article':
			case 'blog':
				$requireCategories = false;
				break;
				
			case 'recipe':
				$requireCategories = true;
				break;
		}
		
		// Get models
		$itemsModel = BluApplication::getModel('items');
		$userModel = BluApplication::getModel('user');
		$permissionsModel = BluApplication::getModel('permissions');
		
		// Check edit permissions
		$item = $itemsModel->getItem($this->_itemId);
		if (!$item || !$item['canEdit']) {
			Messages::addMessage(Text::get('item_edit_noperm', array('itemType' => $item['type'])), 'error');
			return $this->_showMessages('share', 'share', 'default', true);
		}
		$itemsModel->buildRecipeMeta($item);
		
		// Get data from session
		$editedItems = Session::get('editedItems');
		
		if(Request::getBool('save')) {
			
			if($editedItems && isset($editedItems[$this->_itemId])) {
				$editedItem = $editedItems[$this->_itemId];
			}
			else {
				return $this->_redirect('/share');
			}
			
			$title = $editedItem['title'];
			$body = $editedItem['body'];
			$teaser = $editedItem['teaser'];
			$video_js = $editedItem['video_js'];
			$goLiveDate = $editedItem['goLiveDate'];
			$keywords = $editedItem['keywords'];
			$description = $editedItem['description'];
			$selectedCategories = $editedItem['selectedCategories'];
			$terms = $editedItem['terms'];
			$images = $editedItem['images'];
			$default_alt = $editedItem['default_alt'];
			$thumbnail_alt = $default_alt;//$editedItem['thumbnail_alt'];
			$featured_alt = $default_alt;//$editedItem['featured_alt'];
			// Get type specific data
			switch ($this->_itemType) {
				case 'recipe':
					$ingredients = $editedItem['ingredients'];
					$outcome = $editedItem['outcome'];
					$servesPeople = $editedItem['servesPeople'];
					$yieldQuantity = $editedItem['yieldQuantity'];
					$yieldMeasure = $editedItem['yieldMeasure'];
					
					$preparationTimeQuantity = $editedItem['preparationTimeQuantity'];
					$preparationTimeMeasure = $editedItem['preparationTimeMeasure'];
					
					$cookingTimeQuantity = $editedItem['cookingTimeQuantity'];
					$cookingTimeMeasure = $editedItem['cookingTimeMeasure'];
					break;
			}
		
		}
		else {
			
			// Get current user
			if ((!$user = $permissionsModel->getUser()) && (!$user = $this->_requireUser())) {
				$url = $itemsModel->getTaskLink($item['link'], 'edit_submit');
				$url = '/account/login?redirect='.base64_encode($url);
				return $this->_redirect($url, Text::get('item_submit_login'), 'warn');
			}
			
			// Get data from request
			$title = trim(Request::getString('title'));
			$body = trim(Request::getString('body', null, 'default', true));
			$teaser = trim(Request::getString('teaser'));
			if ($user['type'] == 'admin') { // only admin can edit meta keywords and description
				$video_js = trim(Request::getString('video_js', null, 'default', true));
				$goLiveDate = trim(Request::getString('go_live_date'));
				$keywords = trim(Request::getString('keywords'));
				$description = Request::getString('description');
				$default_alt = Request::getString('default_alt');
				$thumbnail_alt = $default_alt;//Request::getString('thumbnail_alt');
				$featured_alt = $default_alt;//Request::getString('featured_alt');
			}
			else {
				$video_js = null;
				$goLiveDate = null;
				$keywords = null;
				$description = null;
				$default_alt = null;
				$thumbnail_alt = null;
				$featured_alt = null;
			}
			$queueId = Request::getString('queueid', md5(uniqid()));
			$selectedCategories = Request::getArray('categories');
			$terms = Request::getBool('terms');
			// Get type specific data
			switch ($this->_itemType) {
				case 'recipe':
					$ingredients = trim(Request::getString('ingredients'));
					
					$outcome = trim(Request::getString('outcome'));
					$servesPeople = Request::getString('serving');
					$yieldQuantity = Request::getString('yield_quantity');
					$yieldMeasure = Request::getString('yield_measure');
					
					$preparationTimeQuantity = Request::getString('preparation_time_quantity');
					$preparationTimeMeasure = Request::getString('preparation_time_measure');
					
					$cookingTimeQuantity = Request::getString('cooking_time_quantity');
					$cookingTimeMeasure = Request::getString('cooking_time_measure');
					break;
			}
			
		}
			
		$slug = Utility::slugify($title);
		
		// Validate
		$errors = false;
		// Required fields
		if (!$title || !trim(strip_tags($body)) || ($requireCategories && empty($selectedCategories))) {
			Messages::addMessage(Text::get('global_msg_complete_all_fields'), 'error');
			$errors = true;
			
		// The generated slug was not allowed
		} elseif ($itemsModel->isSlugInUse($slug, $this->_itemId) && !$autoSlug) {
			Messages::addMessage(Text::get('item_msg_title_in_use'), 'error');
			$errors = true;
			
		// Terms and conditions
		} else if (!$terms && $this->_itemType!='blog') {
			Messages::addMessage(Text::get('item_submit_terms'), 'error');
			$errors = true;
			
		// Type specific validation
		} else {
			switch ($this->_itemType) {
				case 'recipe':
					
					// Some variables
					$requireSize = true;
					$requireTime = false;
					$sizeGiven = !(($outcome == 'serving' && !$servesPeople) || ($outcome == 'yield' && (!$yieldQuantity || !$yieldMeasure)));
					$timeGiven = !(!strlen($preparationTimeQuantity) || !$preparationTimeMeasure || !strlen($cookingTimeQuantity) || !$cookingTimeMeasure);
					
					// Required fields
					if (!$ingredients || !$outcome || ($requireSize && !$sizeGiven) || ($requireTime && !$timeGiven)) {
						Messages::addMessage(Text::get('global_msg_complete_all_fields'), 'error');
						$errors = true;
					}
					break;
			}
		}
		// Go Live Date if entered
		$goLiveTimestamp = strtotime($goLiveDate);
		if($goLiveDate && !$goLiveTimestamp) {
			Messages::addMessage(Text::get('item_submit_golivedate'), 'error');
			$errors = true;
		}
		
		// Auto-generate the slug
		if ($itemsModel->isSlugInUse($slug, $this->_itemId) && $autoSlug) {
			$i = 2;
			while ($itemsModel->isSlugInUse($slug.'_'.$i, $this->_itemId)) {
				$i++;
			}
			$slug .= '_'.$i;
		}
		
		// Validation error redirect
		if ($errors) {
			Template::set('goLiveDate', $goLiveDate);
			Template::set('keywords', $keywords);
			Template::set('description', $description);
			return $this->_showMessages('edit', 'edit');
		}
		// After validation redirect to preview
		elseif(!Request::getBool('save')) {

			// Upload images
			$result = $this->_saveUpload($queueId, 'default', false, array('png', 'jpg', 'jpeg', 'gif', 'bmp'));
			
			$uploadStatus = '';
			$errorMsg = '';
			if(isset($result['result']) && $result['result'] =='success')
			{
				$uploadStatus = 'success';
			}
			
			if (isset($result['error'])) {
				$uploadStatus = 'failed';
				$errorMsg = $result['error'];
				Messages::addMessage($result['error'], 'error');
			}
			/*$result = $this->_saveUpload($queueId, 'thumbnail', false, array('png', 'jpg', 'jpeg', 'gif', 'bmp'));
			if (isset($result['error'])) {
				Messages::addMessage($result['error'], 'error');
			}
			$result = $this->_saveUpload($queueId, 'featured', false, array('png', 'jpg', 'jpeg', 'gif', 'bmp'));
			if (isset($result['error'])) {
				Messages::addMessage($result['error'], 'error');
			}*/
			$assets = Upload::getQueue($queueId); 
			
			if (!empty($assets)) {
				
				$tempDir = BLUPATH_ASSETS.'/tempimages/';
				if (!file_exists($destDir)) {
					mkdir($tempDir, 0777, true);
				}
				
				foreach ($assets as $uploadId => $file) {
					// move images to temporary location until the article is really saved
					$origFileName = basename($file['name']);
					$assetFileName = $file['field_name'].'_'.md5(microtime().mt_rand(0, 250000)).'_'.$origFileName;
					$assetPath = BLUPATH_ASSETS.'/tempimages/'.$assetFileName;
					if(Upload::move($uploadId, $assetPath)) {
						$images[$file['field_name']] = $assetPath;
					}
				}
				Upload::clearQueue($queueId);
				Session::set('origFileName', $origFileName);
				Session::set('assetFileName', $assetFileName);
				Session::set('uploadStatus', $uploadStatus);
				Session::set('errorMsg',$errorMsg);
			}					
			
			if(Request::getBool('preview')) {
				
				// Merge previously uploaded files
				if($editedItems[$this->_itemId]['images'] && is_array($editedItems[$this->_itemId]['images'])) {
					foreach($editedItems[$this->_itemId]['images'] as $imageType=>$assetPath) {
						if(!isset($images[$imageType])) {
							$images[$imageType] = $assetPath;
						}
					}
				}
				
				// Save submitted data to sesssion
				$editedItem = array();
				$editedItem['title'] = $title;
				$editedItem['teaser'] = $teaser;
				$editedItem['video_js'] = $video_js;
				$editedItem['goLiveDate'] = $goLiveDate;
				$editedItem['body'] = $body;
				$editedItem['keywords'] = $keywords;
				$editedItem['description'] = $description;
				$editedItem['selectedCategories'] = $selectedCategories;
				$editedItem['terms'] = $terms;
				$editedItem['images'] = $images;
				$editedItem['default_alt'] = $default_alt;
				$editedItem['thumbnail_alt'] = $thumbnail_alt;
				$editedItem['featured_alt'] = $featured_alt;
				switch ($this->_itemType) {
					case 'recipe':
						$step = 'Final step: Preview your recipe';
						$editedItem['ingredients'] = $ingredients;
						$editedItem['outcome'] = $outcome;
						$editedItem['servesPeople'] = $servesPeople;
						$editedItem['yieldQuantity'] = $yieldQuantity;
						$editedItem['yieldMeasure'] = $yieldMeasure;
						$editedItem['preparationTimeQuantity'] = $preparationTimeQuantity;
						$editedItem['preparationTimeMeasure'] = $preparationTimeMeasure;
						$editedItem['cookingTimeQuantity'] = $cookingTimeQuantity;
						$editedItem['cookingTimeMeasure'] = $cookingTimeMeasure;
					break;
				}
				$editedItem['link'] = $item['link'];
				
				$editedItems = Session::get('editedItems');
				if(!$editedItems) {
					$editedItems = array();
				}
				$editedItems[$this->_itemId] = $editedItem;
				
				Session::set('editedItems', $editedItems);
				
				// Redirect to preview page
				$redirectUrl = $itemsModel->getTaskLink($item['link'], 'edit_preview');
				return $this->_redirect($redirectUrl);
				
			}
			if(Request::getBool('normalize')) {
				
				// Merge previously uploaded files
				if($editedItems[$this->_itemId]['images'] && is_array($editedItems[$this->_itemId]['images'])) {
					foreach($editedItems[$this->_itemId]['images'] as $imageType=>$assetPath) {
						if(!isset($images[$imageType])) {
							$images[$imageType] = $assetPath;
						}
					}
				}
				
				// Save submitted data to sesssion
				$editedItem = array();
				$editedItem['title'] = $title;
				$editedItem['teaser'] = $teaser;
				$editedItem['video_js'] = $video_js;
				$editedItem['goLiveDate'] = $goLiveDate;
				$editedItem['body'] = $body;
				$editedItem['keywords'] = $keywords;
				$editedItem['description'] = $description;
				$editedItem['selectedCategories'] = $selectedCategories;
				$editedItem['terms'] = $terms;
				$editedItem['images'] = $images;
				$editedItem['default_alt'] = $default_alt;
				$editedItem['thumbnail_alt'] = $thumbnail_alt;
				$editedItem['featured_alt'] = $featured_alt;
				switch ($this->_itemType) {
					case 'recipe':
						$step = 'Step 2: Review your ingredients';
						$editedItem['ingredients'] = $ingredients;
						$editedItem['outcome'] = $outcome;
						$editedItem['servesPeople'] = $servesPeople;
						$editedItem['yieldQuantity'] = $yieldQuantity;
						$editedItem['yieldMeasure'] = $yieldMeasure;
						$editedItem['preparationTimeQuantity'] = $preparationTimeQuantity;
						$editedItem['preparationTimeMeasure'] = $preparationTimeMeasure;
						$editedItem['cookingTimeQuantity'] = $cookingTimeQuantity;
						$editedItem['cookingTimeMeasure'] = $cookingTimeMeasure;
					break;
				}
				$editedItem['link'] = $item['link'];
				
				$editedItems = Session::get('editedItems');
				if(!$editedItems) {
					$editedItems = array();
				}
				$editedItems[$this->_itemId] = $editedItem; 
				
				Session::set('editedItems', $editedItems);
				
				// Redirect to preview page
				$redirectUrl = $itemsModel->getTaskLink($item['link'], 'edit_normalize');
				return $this->_redirect($redirectUrl);
				
			}
			
		}
		
		// Save article
		$requireReview = BluApplication::getSetting('requireSubmissionReview', false);
		
		if($goLiveTimestamp) {
			$goLiveDate = date('Y-m-d', $goLiveTimestamp);
		}
		else {
			$goLiveDate = null;
		}
		
		$user = $permissionsModel->getUser();
		if(!$user) {
			$user = $userModel->getCurrentUser();
		}
		$itemsModel->editItem($this->_itemId, $title, $body, $teaser, $goLiveDate, $keywords, $description, $slug, $user['id'],$video_js);
		
		if(Session::get('uploadStatus') !='')
		{
			$logResult = $itemsModel->logArticleImageActivities($user['id'],$this->_itemId,Session::get('origFileName'),Session::get('assetFileName'),BLUPATH_ASSETS.'/itemimages/',Session::get('uploadStatus'),Session::get('errorMsg'));
			Session::set('origFileName', '');
			Session::set('assetFileName', '');
			Session::set('uploadStatus', '');
			Session::set('errorMsg','');
		}
		
		// Assign images/links/meta values to the item.
		if ($this->_itemId) {
			if(!empty($images)) {
				if(isset($images['default'])) {
					$itemsModel->deleteAllImages($this->_itemId,'default');
					$itemsModel->copyUnsavedImage($this->_itemId, $images['default']);
					$itemsModel->addImage($this->_itemId, basename($images['default']), null, null, null, null, 'default', $user['id'],$default_alt);
				}
				if(isset($images['thumbnail'])) {
					$itemsModel->deleteAllImages($this->_itemId,'thumbnail');
					$itemsModel->copyUnsavedImage($this->_itemId, $images['thumbnail']);
					$itemsModel->addImage($this->_itemId, basename($images['thumbnail']), null, null, null, null, 'thumbnail', $user['id'],$thumbnail_alt);
				}
				if(isset($images['featured'])) {
					$itemsModel->deleteAllImages($this->_itemId,'featured');
					$itemsModel->copyUnsavedImage($this->_itemId, $images['featured']);
					$itemsModel->addImage($this->_itemId, basename($images['featured']), null, null, null, null, 'featured', $user['id'],$featured_alt);
				}
			}
			
				// Delete all categories
				$this->_deleteItemCategories($this->_itemId);
				if (!empty($selectedCategories)) {
					
					// Reapply category assignments.
					foreach ($selectedCategories as $categoryType => $submittedCategories) {
						$itemsModel->assignItemCategory($this->_itemId, $categoryType);
						
						foreach ($submittedCategories as $category) {
							$itemsModel->assignItemCategory($this->_itemId, $category);
						}
					}
				}

			// Type specific additions
			switch ($this->_itemType) {
				case 'recipe':

					// Ingredients - propose ingredients
					$ingredients = explode("\n", $ingredients);
					$ingredients = array_filter($ingredients);
					$itemsModel->proposeIngredients($this->_itemId, $ingredients, $user['id'], true);
					
					// Ingredients - normalized ingredients to use
					$ingredientsModel = BluApplication::getModel('ingredients');
					$tidyIngredients = $ingredientsModel->normalizeIngredients($ingredients);
					$ingredientsModel->setRecipeIngredients($this->_itemId, $tidyIngredients);
					// Ingredients - normalized ingredients to use
					// Outcome
					switch ($outcome) {
						case 'serving':
							$itemsModel->setRecipeYield($this->_itemId, $servesPeople, 'people', false);
							break;

						case 'yield':
							$itemsModel->setRecipeYield($this->_itemId, $yieldQuantity, $yieldMeasure, false);
							break;
					}

					// Preparation time
					$itemsModel->setRecipePreparationTime($this->_itemId, $preparationTimeQuantity, $preparationTimeMeasure, false);

					// Cooking time
					$itemsModel->setRecipeCookingTime($this->_itemId, $cookingTimeQuantity, $cookingTimeMeasure, false);
					break;
			}
			
			$editedItems = Session::get('editedItems');
			unset($editedItems[$this->_itemId]);
			Session::set('editedItems', $editedItems);

			// Output message and redirect to page.
			Messages::addMessage(Text::get('item_edit_success'), 'info');
			$item = $itemsModel->getItem($this->_itemId, null, true);
			return $this->_redirect($item['link']);

		// Failed
		} else {

			// Show error message and continue with template.
			switch ($this->_itemType) {
				case 'article':
				case 'recipe':
					Messages::addMessage(Text::get('item_edit_failed', array('itemType' => $this->_itemType)), 'error');
					break;
			}
		}
		// Redirect
		return $this->_showMessages('edit', 'edit');
	}
	
	/**
	 *	Delete all item categories available
	 *
	 *	@todo only delete categories from _getShareCategories().
	 *	@access protected
	 *	@param int Item ID
	 */
	protected function _deleteItemCategories($itemId)
	{
		// Get all of the item's current categories
		$metaModel = BluApplication::getModel('meta');
		$itemMetaGroups = $metaModel->getItemMetaGroups($itemId);
		
		// Get relevant categories' meta value IDs
		$categories = $this->_getShareCategories();
		$categoryIds = array();
		if(Utility::iterable($categories)) {
			foreach ($categories as $categoryParentSlug => $childCategories) {
				// Get parent category ID, if in hierarchy
				if ($categoryId = $metaModel->getValueIdBySlug($categoryParentSlug)) {
					if ($metaModel->inHierarchy($categoryId)) {
						$categoryIds[$categoryId] = $categoryId;
					}
				}
				
				// Get children category IDs, if in hierarchy
				foreach ($childCategories as $childCategory) {
					if ($categoryId = $metaModel->getValueIdBySlug($childCategory['slug'])) {
						if ($metaModel->inHierarchy($categoryId)) {
							$categoryIds[$categoryId] = $categoryId;
						}
					}
				}
			}
		}
		
		// Intersect with relevant categories
		foreach ($itemMetaGroups as $groupId => $group) {
			
			// Definitely not in hierarchy
			if ($group['type'] != 'pick') {
				unset($itemMetaGroups[$groupId]);
				continue;
			}
			
			$group['values'] = array_intersect_key($group['values'], $categoryIds);
			
			// Nothing to do
			if (empty($group['values'])) {
				unset($itemMetaGroups[$groupId]);
				continue;
			}
			
			// Delete item-meta values
			foreach ($group['values'] as $valueId => $value) {
				$metaModel->deleteItemMetaValue($itemId, $groupId, $valueId, true);
			}
		}
		
		// Return
	}
	
	/**
	 *	Edit related items
	 *
	 *	@access public
	 */
	public function edit_related()
	{
		$itemsModel = BluApplication::getModel('items');
		$userModel = BluApplication::getModel('user');
		
		// Check edit permissions
		$user = $userModel->getCurrentUser();
		$item = $itemsModel->getItem($this->_itemId);
		if (!$item['canEdit'] || $user['type'] == 'member') {
			Messages::addMessage(Text::get('item_edit_noperm', array('itemType' => $item['type'])), 'error');
			return $this->_showMessages('share', 'share', 'default', true);
		}
		$listingBaseUrl = $itemsModel->getTaskLink($this->_baseUrl, 'edit_related');
		
		// Load template
		switch ($this->_itemType) {
			case 'article':
				$pageHeading = 'Edit related articles';
				break;
				
			case 'recipe':
				$pageHeading = 'Edit related recipes.';
				break;
		}
		include(BLUPATH_TEMPLATES.'/recipes/recipe_related.php');
	}
	
	/**
	 *	View search results when adding a new related article
	 *
	 *	@access public
	 */
	public function view_related_articles_search_results()
	{
		$itemsModel = BluApplication::getModel('items');
		$userModel = BluApplication::getModel('user');
		
		// Check edit permissions
		$user = $userModel->getCurrentUser();
		$item = $itemsModel->getItem($this->_itemId);
		if (!$item['canEdit'] || $user['type'] == 'member') {
			Messages::addMessage(Text::get('item_edit_noperm', array('itemType' => $item['type'])), 'error');
			return $this->_showMessages('share', 'share', 'default', true);
		}

		$searchTerm = trim(Request::getString('searchterm'));
		$searchTermExtra = trim(Request::getString('searchterm_extra'));
		Template::set('searchTermExtra',$searchTermExtra);
		$relatedArticles = $itemsModel->getRelatedArticles($this->_itemId);
		Template::set('relatedArticles',$relatedArticles);
		
		$this->_view = 'related_articles_search';
		$this->_baseUrl = $itemsModel->getTaskLink($this->_baseUrl, 'edit_related');

		$this->view_items();

	}

	/**
	 *	View list of related articles
	 *
	 *	@access public
	 */
	public function view_related_articles()
	{
		$itemsModel = BluApplication::getModel('items');
		$userModel = BluApplication::getModel('user');
		
		// Check edit permissions
		$user = $userModel->getCurrentUser();
		$item = $itemsModel->getItem($this->_itemId);
		if (!$item['canEdit'] || $user['type'] == 'member') {
			Messages::addMessage(Text::get('item_edit_noperm', array('itemType' => $item['type'])), 'error');
			return $this->_showMessages('share', 'share', 'default', true);
		}
		
		$page = Request::getInt('page', 1);
		
		$removeRelatedArticleBaseUrl = $itemsModel->getTaskLink($this->_baseUrl, 'removeRelatedArticle');
		Template::set('removeRelatedArticleBaseUrl', $removeRelatedArticleBaseUrl);
		
		$this->_view = 'related_articles';
		
		$relatedArticles = $itemsModel->getRelatedArticles($this->_itemId);
		$this->_listItems($relatedArticles,$page,5);
	}
	
	/**
	 *	Add selected related articles
	 *
	 *	@access public
	 */
	public function add_related_articles() 
	{
		$itemsModel = BluApplication::getModel('items');
		$userModel = BluApplication::getModel('user');
		
		// Check edit permissions
		$user = $userModel->getCurrentUser();
		$item = $itemsModel->getItem($this->_itemId);
		if (!$item['canEdit'] || $user['type'] == 'member') {
			Messages::addMessage(Text::get('item_edit_noperm', array('itemType' => $item['type'])), 'error');
			return $this->_showMessages('share', 'share', 'default', true);
		}

		$relatedArticles = Request::getArray('related_articles');
		if($relatedArticles) {
			foreach($relatedArticles as $relatedArticleId=>$value) {
				$itemsModel->addRelatedArticle($this->_itemId,$relatedArticleId);
			}
		}
		
		// Add message
		Messages::addMessage(Text::get('item_msg_related_added', array('itemTypeAndHave' => $item['type'].(count($relatedArticles)==1 ? ' has' : 's have'))), 'info');
		
		// Redirect
		$redirectUrl = $itemsModel->getTaskLink($this->_baseUrl, 'edit_related');
		return $this->_redirect($redirectUrl);
	}
	
	
	
	/**
	 *	Calls to remove related article
	 *
	 *	@access public
	 */
	public function removeRelatedArticle() 
	{
		$this->remove_related_article();
	}
	
	/**
	 *	Remove related article
	 *
	 *	@access public
	 */
	public function remove_related_article() 
	{
		$itemsModel = BluApplication::getModel('items');
		$userModel = BluApplication::getModel('user');
		
		// Check edit permissions
		$user = $userModel->getCurrentUser();
		$item = $itemsModel->getItem($this->_itemId);
		if (!$item['canEdit'] || $user['type'] == 'member') {
			Messages::addMessage(Text::get('item_edit_noperm', array('itemType' => $item['type'])), 'error');
			return $this->_showMessages('share', 'share', 'default', true);
		}
		
		$relatedArticleId = Request::getInt('relatedArticleId');
		$result = $itemsModel->deleteRelatedArticle($this->_itemId, $relatedArticleId);
		
		// flush cache only for this item...
		$itemsModel->getItem($this->_itemId,null,true);
		
		// Add message
		Messages::addMessage(Text::get('item_msg_related_removed', array('itemType' => $item['type'])), 'info');
		
		// Redirect
		$redirectUrl = $itemsModel->getTaskLink($item['link'], 'edit_related');
		return $this->_redirect($redirectUrl);
	}
	
	/**
	 *	Bleurgh
	 *
	 *	@access private
	 *	@return array
	 */
	private function _getMetaShareCategories()
	{
		$metaModel = BluApplication::getModel('meta');
		$groups = $metaModel->getHierarchy();
		if ($this->_itemType == 'recipe') {
			foreach ($groups as $groupId => $group) {
				if ($group['slug'] == 'recipes') {
					$groups = $group['values'];
					$group['name'] = 'Categories'; 
					array_unshift($groups,$group);
					break;
				}
			}
		}
		else {
			foreach ($groups as $groupId => $group) {
				if ($group['slug'] == 'recipes') {
					unset($groups[$groupId]);
					break;
				}
			}
			unset($groups['recipes']);
		}	
		foreach ($groups as $groupId => &$group) {
			if(!empty($group['values'])){
				foreach($group['values'] as $subGroupId => &$subGroup){
					if(!$subGroup['display']){
						unset($group['values'][$subGroupId]);
					}
				}
			}
			if ($group['display'] && !(isset($group['hidden']) && $group['hidden']) && isset($group['values']) && (count($group['values'])) ) continue;
			unset($groups[$groupId]);
		}
		return $groups;
	}
	private function _getShareCategories() 
	{
		switch ($this->_itemType) {
			case 'article':
				return array(
					'a_dash_of_fun' => array(
						array(
							'name' => 'Culinary Travel',
							'slug' => 'culinary_travel'
						),
						array(
							'name' => 'Current Contests',
							'slug' => 'currnet_contests'
						),
						array(
							'name' => 'Editor\'s Picks',
							'slug' => 'editor_s_picks'
						),
						array(
							'name' => 'Food Events',
							'slug' => 'food_events'
						),
						array(
							'name' => 'Fun Food Videos',
							'slug' => 'fun_food_videos'
						),
						array(
							'name' => 'Gifts',
							'slug' => 'gifts'
						),
						array(
							'name' => 'Girlawhirl',
							'slug' => 'girlawhirl'
						),
						array(
							'name' => 'Holiday Traditions',
							'slug' => 'holiday_traditions'
						),
						array(
							'name' => 'Just for Fun',
							'slug' => 'just_for_fun'
						),
						array(
							'name' => 'Kitchen Humor',
							'slug' => 'kitchen_humor'
						),
						array(
							'name' => 'Party Guide',
							'slug' => 'party_guide'
						)
					),
					'hints_tips' => array(
						array(
							'name' => '7 Day Meal Planners',
							'slug' => '7_day_meal_planners'
						),
						array(
							'name' => 'Budget Cooking',
							'slug' => 'budget_cooking_2'
						),
						array(
							'name' => 'Busy Cooks',
							'slug' => 'busy_cooks'
						),
						array(
							'name' => 'Cooking Basics',
							'slug' => 'cooking_basics'
						),
						array(
							'name' => 'Cooking Gadgets',
							'slug' => 'cooking_gadgets'
						),
						array(
							'name' => 'Crock Pot Cooking',
							'slug' => 'crock_pot_cooking'
						),
						array(
							'name' => 'Eco-Friendly Living',
							'slug' => 'eco_friendly_living'
						),
						array(
							'name' => 'Entertaining',
							'slug' => 'entertaining'
						),
						array(
							'name' => 'Favorite Foods',
							'slug' => 'favorite_foods'
						),
						array(
							'name' => 'Grocery Shopping',
							'slug' => 'grocery_shopping'
						),
						array(
							'name' => 'How To Guides',
							'slug' => 'how_to_guides'
						),
						array(
							'name' => 'How To Videos',
							'slug' => 'how_to_videos'
						),
						array(
							'name' => 'Menus',
							'slug' => 'menus'
						),
						array(
							'name' => 'Product Reviews',
							'slug' => 'product_reviews'
						),
						array(
							'name' => 'Recipe Collections',
							'slug' => 'recipe_collections'
						),
						array(
							'name' => 'Top 10 Recipe Lists',
							'slug' => 'top_10_recipe_lists'
						),
						array(
							'name' => 'Wine',
							'slug' => 'wine'
						),
						array(
							'name' => 'Work It, Mom!',
							'slug' => 'work_it_mom'
						)
					),
					'thinking_healthy' => array(
						array(
							'name' => 'Beauty',
							'slug' => 'beauty_2'
						),
						array(
							'name' => 'Dieting',
							'slug' => 'dieting'
						),
						array(
							'name' => 'Dining Out',
							'slug' => 'dining_out'
						),
						array(
							'name' => 'Exercise',
							'slug' => 'exercise'
						),
						array(
							'name' => 'Meal Planning',
							'slug' => 'meal_planning'
						),
						array(
							'name' => 'Nutrition',
							'slug' => 'nutrition'
						),
						array(
							'name' => 'Strength of Mind',
							'slug' => 'strength_of_mind'
						)
					)
				);
				
			case 'recipe':
				return array(
					'main_ingredients' => array(
						array(
							'name' => 'Beans &amp; Legumes',
							'slug' => 'beans_and_legumes'
						),
						array(
							'name' => 'Beef',
							'slug' => 'beef_2'
						),
						array(
							'name' => 'Cheese &amp; Dairy',
							'slug' => 'cheese_and_dairy'
						),
						array(
							'name' => 'Chicken',
							'slug' => 'chicken_5'
						),
						array(
							'name' => 'Chocolate',
							'slug' => 'chocolate_2'
						),
						array(
							'name' => 'Duck',
							'slug' => 'duck'
						),
						array(
							'name' => 'Eggs',
							'slug' => 'eggs_2'
						),
						array(
							'name' => 'Fish',
							'slug' => 'fish_3'
						),
						array(
							'name' => 'Fruit',
							'slug' => 'fruit_3'
						),
						array(
							'name' => 'Grains',
							'slug' => 'grains'
						),
						array(
							'name' => 'Lamb',
							'slug' => 'lamb_3'
						),
						array(
							'name' => 'Pastas',
							'slug' => 'pastas_2'
						),
						array(
							'name' => 'Pork',
							'slug' => 'pork_3'
						),
						array(
							'name' => 'Shellfish',
							'slug' => 'shellfish'
						),
						array(
							'name' => 'Soy/Tofu',
							'slug' => 'soy_tofu'
						),
						array(
							'name' => 'Turkey',
							'slug' => 'turkey_3'
						),
						array(
							'name' => 'Vegetables',
							'slug' => 'vegetables_4'
						),
						array(
							'name' => 'Wild Game',
							'slug' => 'wild_game'
						)
					),
					'recipes' => array(
						array(
							'name' => 'Appetizers',
							'slug' => 'appetizers'
						),
						array(
							'name' => 'Beauty',
							'slug' => 'beauty'
						),
						array(
							'name' => 'Drink',
							'slug' => 'beverages'
						),
						array(
							'name' => 'Brand',
							'slug' => 'brand'						
						),
						array(
							'name' => 'Breads',
							'slug' => 'breads'
						),
						array(
							'name' => 'Breakfast',
							'slug' => 'breakfast'
						),
						array(
							'name' => 'Cheap',
							'slug' => 'budget_cooking'
						),
						array(
							'name' => 'Casseroles',
							'slug' => 'casseroles'
						),
						array(
							'name' => 'Comfort Foods',
							'slug' => 'comfort_foods'						
						),
						array(
							'name' => 'Copycat',
							'slug' => 'copycat'
						),
						array(
							'name' => 'Crockpot',
							'slug' => 'crockpot'
						),
						array(
							'name' => 'Desserts',
							'slug' => 'desserts'
						),
						array(
							'name' => 'Diabetic',
							'slug' => 'diabetic'
						),
						array(
							'name' => 'Fruits',
							'slug' => 'fruits'
						),
						array(
							'name' => 'Healthy',
							'slug' => 'healthy'
						),
						array(
							'name' => 'Jams, Jellies &amp; Butters',
							'slug' => 'jams_jellies_butters'
						),
						array(
							'name' => 'Kid-Friendly',
							'slug' => 'kid_friendly'
						),
						array(
							'name' => 'Lunch',
							'slug' => 'lunch'
						),
						array(
							'name' => 'Main Courses',
							'slug' => 'main_courses'
						),
						array(
							'name' => 'Pastas',
							'slug' => 'pastas'
						),
						array(
							'name' => 'Pizzas',
							'slug' => 'pizzas'
						),
						array(
							'name' => 'Salads',
							'slug' => 'salads'
						),
						array(
							'name' => 'Sandwiches',
							'slug' => 'sandwiches'
						),
						array(
							'name' => 'Sauces &amp; Seasonings',
							'slug' => 'sauces_and_seasonings'
						),
						array(
							'name' => 'Sides',
							'slug' => 'sides'
						),
						array(
							'name' => 'Snacks',
							'slug' => 'snacks'
						),
						array(
							'name' => 'Soups &amp; Stews',
							'slug' => 'soups_and_stews'
						),
						array(
							'name' => 'Meatless',
							'slug' => 'vegetarian_vegan'
						)

					),
					'healthy' => array(
						array(
							'name' => 'Egg-free',
							'slug' => 'egg_free'
						),
						array(
							'name' => 'High Fiber',
							'slug' => 'high_fiber'
						),
						array(
							'name' => 'Low Calorie',
							'slug' => 'low_calorie'
						),
						array(
							'name' => 'Low Carb',
							'slug' => 'low_carb'
						),
						array(
							'name' => 'Low Fat',
							'slug' => 'low_fat'
						),
						array(
							'name' => 'Low Sodium',
							'slug' => 'low_sodium'
						),
						array(
							'name' => 'Low Sugar/Sugar Free',
							'slug' => 'low_sugar_sugar_free'
						),
						array(
							'name' => 'Vegan',
							'slug' => 'vegan'
						),
						array(
							'name' => 'Vegetarian',
							'slug' => 'vegetarian'
						),
						array(
							'name' => 'Wheat/Gluten-free',
							'slug' => 'wheat_gluten_free'
						)
					),
					'preparation' => array(
						array(
							'name' => 'Bake',
							'slug' => 'bake'
						),
						array(
							'name' => 'Crockpot',
							'slug' => 'crockpot_2'
						),
						array(
							'name' => 'Fry',
							'slug' => 'fry'
						),
						array(
							'name' => 'Grill',
							'slug' => 'grill'
						),
						array(
							'name' => 'Microwave',
							'slug' => 'microwave'
						),
						array(
							'name' => 'No Cook',
							'slug' => 'no_cook'
						),
						array(
							'name' => 'Pressure Cooker',
							'slug' => 'pressure_cooker'
						),
						array(
							'name' => 'Quick &amp; Easy',
							'slug' => 'quick'
						),
						array(
							'name' => 'Stir Fry',
							'slug' => 'stir_fry_2'
						)
					),
					'global_cuisines' => array(
						array(
							'name' => 'African',
							'slug' => 'african'
						),
						array(
							'name' => 'Cajun/Creole',
							'slug' => 'cajun_creole'
						),
						array(
							'name' => 'Central/South American',
							'slug' => 'central_south_american'
						),
						array(
							'name' => 'Chinese',
							'slug' => 'chinese'
						),
						array(
							'name' => 'Eastern European',
							'slug' => 'eastern_european'
						),
						array(
							'name' => 'English',
							'slug' => 'english'
						),
						array(
							'name' => 'French',
							'slug' => 'french'
						),
						array(
							'name' => 'German',
							'slug' => 'german'
						),
						array(
							'name' => 'Indian',
							'slug' => 'indian'
						),
						array(
							'name' => 'Irish',
							'slug' => 'irish'
						),
						array(
							'name' => 'Italian',
							'slug' => 'italian'
						),
						array(
							'name' => 'Japanese',
							'slug' => 'japanese'
						),
						array(
							'name' => 'Korean',
							'slug' => 'korean'
						),
						array(
							'name' => 'Mediterranean',
							'slug' => 'mediterranean'
						),
						array(
							'name' => 'Mexican',
							'slug' => 'mexican'
						),
						array(
							'name' => 'Middle Eastern',
							'slug' => 'middle_eastern'
						),
						array(
							'name' => 'Scandinavian',
							'slug' => 'scandinavian'
						),
						array(
							'name' => 'Thai',
							'slug' => 'thai'
						),
						array(
							'name' => 'Vietnamese',
							'slug' => 'vietnamese'
						)
					),
					'special_occasion' => array(
						array(
							'name' => 'Christmas',
							'slug' => 'christmas'
						),
						array(
							'name' => 'Easter',
							'slug' => 'easter'
						),
						array(
							'name' => 'Fourth of July',
							'slug' => 'fourth_of_july'
						),
						array(
							'name' => 'Halloween',
							'slug' => 'halloween'
						),
						array(
							'name' => 'Jewish Holidays',
							'slug' => 'jewish_holidays'
						),
						array(
							'name' => 'Kwanzaa',
							'slug' => 'kwanzaa'
						),
						array(
							'name' => 'New Years',
							'slug' => 'new_years'
						),
						array(
							'name' => 'St. Patrick\'s Day',
							'slug' => 'st_patrick_s_day'
						),
						array(
							'name' => 'Super Bowl',
							'slug' => 'super_bowl'
						),
						array(
							'name' => 'Thanksgiving',
							'slug' => 'thanksgiving'
						),
						array(
							'name' => 'Valentine\'s Day',
							'slug' => 'valentine_s_day'
						)
					),
					'seasons' => array(
						array(
							'name' => 'Spring',
							'slug' => 'spring'
						),
						array(
							'name' => 'Summer',
							'slug' => 'summer'
						),
						array(
							'name' => 'Fall',
							'slug' => 'fall'
						),
						array(
							'name' => 'Winter',
							'slug' => 'winter'
						)
					),
					'taste_and_texture' => array(
						array(
							'name' => 'Cheesy',
							'slug' => 'cheesy'
						),
						array(
							'name' => 'Creamy',
							'slug' => 'creamy'
						),
						array(
							'name' => 'Crispy',
							'slug' => 'crispy'
						),
						array(
							'name' => 'Crunchy',
							'slug' => 'crunchy'
						),
						array(
							'name' => 'Light &amp; Fluffy',
							'slug' => 'light_fluffy'
						),
						array(
							'name' => 'Rich',
							'slug' => 'rich'
						),
						array(
							'name' => 'Salty',
							'slug' => 'salty'
						),
						array(
							'name' => 'Smooth',
							'slug' => 'smooth'
						),
						array(
							'name' => 'Spicy',
							'slug' => 'spicy'
						),
						array(
							'name' => 'Sweet',
							'slug' => 'sweet_2'
						),
						array(
							'name' => 'Tart',
							'slug' => 'tart'
						)
					),
					'beauty' => array(
						array(
							'name' => 'Bath',
							'slug' => 'bath'
						),
						array(
							'name' => 'Hair',
							'slug' => 'hair'
						),
						array(
							'name' => 'Lotions',
							'slug' => 'lotions'
						),
						array(
							'name' => 'Makeup',
							'slug' => 'makeup'
						)
					)
				);
		}
	}
	
	/**
	 *	Report a review
	 *
	 *	@access public
	 */
	public function report_review()
	{
		return $this->_report('review');
	}
	
	/**
	 *	File a report
	 *
	 *	@access protected
	 *	@access string Object type
	 */
	protected function _report($type, $item = Array())
	{
		// Get model
		$itemsModel = BluApplication::getModel('items');
		
		// Get request
		switch ($type) {
			case 'review':
				$reviewId = Request::getInt('review');
				$task = 'report_review';
				break;
		}
		$reason = Request::getString('reason');
		if (!isset($item['link'])) $item['link'] = '';	
		// Get current user
		if (!$user = $this->_requireUser()) {
			$url = $itemsModel->getTaskLink($item['link'], $task);
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('item_report_login'), 'warn');
		}
		
		// Add report
		$reportsModel = BluApplication::getModel('reports');
		switch ($type) {
			case 'review':
				$reportsModel->reportReview($reviewId, $user['id'], $reason);
				break;
		}
		
		// Output message
		Messages::addMessage('Your report was filed successfully.', 'info');
		return $this->view();
	}
	
	/**
	 *	Left navigation
	 *
	 *	@access public
	 *	@param array Links
	 */
	public function leftnav(array $links = array())
	{
		$metaModel = BluApplication::getModel('meta');
		
		// Get hierarchy from current perspective
		$currentFilter = false;
		$currentFilterId = false;
		if ($metaModel->filtersSet()) {
			$currentFilter = $metaModel->getBottomHierarchyFilter();
			$currentFilterId = $currentFilter['id'];
			
			$subHierarchy = isset($currentFilter['values']) ? $currentFilter['values'] : false;
			
		}
		
		// Use defaults
		if (empty($subHierarchy)) {
			
			// Go up a level, and use siblings
			if ($currentFilterId) {
				$subHierarchy = $metaModel->getHierarchySiblings($currentFilterId);
				
			// Use top levels
			} else {
				switch ($this->_view) {
					case 'article_listing':
						$currentFilterId = 128302; // "A Dash Of Fun" meta value ID
						break;
						
					case 'recipe_listing':
					default:
						$currentFilterId = 4; // "Appetizers" meta value ID
						break;
				}
				$metaValue = $metaModel->getValue($currentFilterId);
				$metaGroup = $metaModel->getGroup($metaValue['groupId']);
				$subHierarchy = $metaGroup['values'];
			}
		}
		
		// Check whether parent exists
		if ($parentFilter = $metaModel->getParent($currentFilterId)) {
			$parentFilter['link'] = $metaModel->getFullLink($parentFilter['id']);
		}
		switch ($this->_view) {
			case 'article_listing':
				Template::set('displayParent', !empty($parentFilter));
				break;
				
			case 'recipe_listing':
			default:
				// Recipes have it different, because it's one level into the hierarchy.
				$grandParentFilter = $metaModel->getParent($parentFilter['id']);
				Template::set('displayParent', !empty($grandParentFilter) && ($parentFilter['id'] != $grandParentFilter['id']));
				break;
		}
		
		// Get full links, regardless of current filters
		foreach ($subHierarchy as $id => &$element) {
			$element['link'] = $metaModel->getFullLink($element['id']);
			if (!empty($element['values'])) {
				foreach ($element['values'] as &$child) {
					$child['link'] = $metaModel->getFullLink($child['id']);
				}
				unset($value);
			}
			if(!$element['display']){
				unset($subHierarchy[$id]);
			}
		}
		unset($element);
		
		// Load template
		switch ($this->_view) {
			case 'article_listing':
				$title = 'Browse Articles';
				$titleLink = '/articles';
				break;
				
			case 'recipe_listing':
			default:
				$title = 'Browse Recipes';
				$titleLink = '/recipes';
				break;
		}
        $links = $this->_getRecipeCategoryLinks();
		include(BLUPATH_TEMPLATES.'/nav/left.php');
	}
	
	/**
	 *	Alphabetical list of recipes and articles
	 *
	 *	@access public
	 */
	public function alphabeticalList()
	{
		// Get request
		$letter = Request::getString('letter', 'A');
		$type = Request::getString('type',null);
		// Get items
		$itemsModel = BluApplication::getModel('items');
		$items = $itemsModel->getAllArticles(ISBOT ? null : $letter, $type);
		foreach($items as &$tmpitem){
			if($tmpitem['type']=='recipe')
			{
				$tmpitem['type'] = 'recipes';
			}else if($tmpitem['type']=='article'){
				$tmpitem['type'] = 'articles';
			}
		}
		unset($tmpitem);
		// Load template
		if($type == 'recipe'){
			Template::set('title', 'All Recipes');
		}else if($type == 'article'){
			Template::set('title', 'All Articles');				
		}else{
			Template::set('title', 'All Recipes and Articles');	
		}
		include(BLUPATH_TEMPLATES.'/articles/alphabetical_list.php');
		
	}
	
	/**
	 *	Image gallery
	 *
	 *	@access public
	 */
	public function image_gallery() {
		// Get item
		$itemsModel = BluApplication::getModel('items');
		$item = $itemsModel->getItem($this->_itemId);
		if(!$item) {
			return false;
		}

		$userModel = BluApplication::getModel('user');
		$user = $userModel->getCurrentUser();
		$permissionsModel = BluApplication::getModel('permissions');
		$httpAuthenticated = $permissionsModel->canEdit();
		Template::set('adminPrivileges', $httpAuthenticated || ($user && $user['type'] != 'member'));
		
		$limit = 20;
		$total = null;
		$page = Request::getInt('page', 1);
		$offset = ($page-1) * $limit;

		$galleryImages = $itemsModel->getGalleryImages($this->_itemId, $total, $offset, $limit);
		
		$imageGalleryUrl = $itemsModel->getTaskLink($item['link'], 'image_gallery');
		$paginationBaseUrl = $imageGalleryUrl.'?page=';
		
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => $paginationBaseUrl
		));
		
		// Link for deleting images
		$deleteImageLink = $itemsModel->getTaskLink($item['link'], 'delete_image');
		
		// Load template
		Template::set('title', 'Image Gallery');
		switch ($this->_itemType) {
			case 'article':
				include(BLUPATH_TEMPLATES.'/articles/image_gallery.php');
				break;
				
			case 'recipe':
				include(BLUPATH_TEMPLATES.'/recipes/image_gallery.php');
				break;
		}
	}
	
	/**
	 *	Upload gallery image
	 *
	 *	@access public
	 */
	public function upload_gallery_image() {
		// Get item
		$itemsModel = BluApplication::getModel('items');
		$userModel = BluApplication::getModel('user');
		$item = $itemsModel->getItem($this->_itemId);
		if(!$item) {
			return false;
		}
		
		$imageGalleryUrl = $itemsModel->getTaskLink($item['link'], 'image_gallery');
		
		if($user = $userModel->getCurrentUser()) {
			$userId = $user['id'];
		}
		else {
			$userId = null;
		}
		
		$queueId = Request::getString('queueid', md5(uniqid()));
		$result = $this->_saveUpload($queueId, 'gallery_image', false, array('png', 'jpg', 'jpeg', 'gif', 'bmp'));
		
		if(isset($result['result']) && $result['result']=='empty') {
			return $this->_redirect($imageGalleryUrl);
		}
		
		if (isset($result['error'])) {
			Messages::addMessage($result['error'], 'error');
		}
		$assets = Upload::getQueue($queueId);
		
		if (!empty($assets)) {
			
			$assetDir = BLUPATH_ASSETS.'/itemimages/';
			if (!file_exists($destDir)) {
				mkdir($assetDir, 0777, true);
			}
			foreach ($assets as $uploadId => $file) {
				// move images to temporary location until the article is really saved
				$origFileName = basename($file['name']);
				$assetFileName = md5(microtime().mt_rand(0, 250000)).'_'.$origFileName;
				$assetPath = $assetDir.$assetFileName;
				if(Upload::move($uploadId, $assetPath)) {
					$images[$file['field_name']] = $assetPath;
				}
			}
			Upload::clearQueue($queueId);
			
			if(empty($item['image']['filename'])) {
				$imageType = 'default';
			}
			else {
				$imageType = 'gallery';
			}
			$itemsModel->addImage($this->_itemId, basename($images['gallery_image']), null, null, null, null, $imageType, $userId);
			
			Messages::addMessage('Image uploaded successfully', 'info');
			
		}
		return $this->_redirect($imageGalleryUrl);
	}
	
	/**
	 *	Set image as default image
	 *
	 *	@access public
	 */
	public function set_default_image() {
		$userModel = BluApplication::getModel('user');
		$user = $userModel->getCurrentUser();
		$permissionsModel = BluApplication::getModel('permissions');
		$httpAuthenticated = $permissionsModel->canEdit();
		$adminPrivileges = $httpAuthenticated || ($user && $user['type'] != 'member');
		if(!$adminPrivileges) {
			return false;
		}
		
		$itemsModel = BluApplication::getModel('items');
		$item = $itemsModel->getItem($this->_itemId);
		if(!$item) {
			return false;
		}
		$defaultImage = Request::getString('default_image');
		
		if(!array_key_exists($defaultImage,$item['images']) || $item['images'][$defaultImage]['type']!='gallery') {
			return $this->_redirect($item['link']);
		}
		
		if(!empty($item['image']['filename'])) {
			$itemsModel->setImage($this->_itemId,$item['image']['filename'],'gallery');
		}
		$itemsModel->setImage($this->_itemId,$defaultImage,'default');
		
		Messages::addMessage('Default image was updated', 'info');
		return $this->_redirect($item['link']);
	}
	
	/**
	 *	Delete image
	 *
	 *	@access public
	 */
	public function delete_image() {
		$userModel = BluApplication::getModel('user');
		$user = $userModel->getCurrentUser();
		$permissionsModel = BluApplication::getModel('permissions');
		$httpAuthenticated = $permissionsModel->canEdit();
		$adminPrivileges = $httpAuthenticated || ($user && $user['type'] != 'member');
		if(!$adminPrivileges) {
			return false;
		}
		
		$itemsModel = BluApplication::getModel('items');
		
		$type = Request::getString('type');
		$deleteSession = Request::getBool('session', false);
		$file = Request::getString('file', null);
		
		if(!in_array($type,array('default','thumbnail','featured','gallery'))) {
			return false;
		}
		
		if($this->_itemId) {
		
			$item = $itemsModel->getItem($this->_itemId);
			if(!$item) {
				return false;
			}
			
			if($type == 'gallery') {
			
				if(isset($item['images'][$file])) {
					
					if($itemsModel->deleteImage($this->_itemId,$file)) {
						// remove file
						$imagePath = BLUPATH_ASSETS.'/itemimages/'.$item['images'][$file];
						if(file_exists($imagePath)) {
							unlink($imagePath);
						}
						$itemsModel->flushItemImages($this->_itemId);
						Messages::addMessage('Image was deleted','info');
					}
				
				}
				
				$redirectLink = $itemsModel->getTaskLink($item['link'], 'image_gallery');
			
			}
			else {
			
				switch($type) {
					case 'default': $index = 'image'; break;
					case 'thumbnail': $index = 'thumbnail'; break;
					case 'featured': $index = 'featuredImage'; break;
				}
				
				$imgTypes = array('default','thumbnail','featured');
				
				if(isset($item[$index]['filename'])) {
					if($itemsModel->deleteAllImages($this->_itemId,$type)) {
						
						foreach($imgTypes as $typeItem)
						{
							// remove file
							$imagePath = BLUPATH_ASSETS.'/itemimages/'.$item[$typeItem]['filename'];
							if(file_exists($imagePath)) {
								unlink($imagePath);
							}
						}						
						$itemsModel->flushItemImages($this->_itemId);
						Messages::addMessage('Image was deleted','info');
					}
				}
			
				$redirectLink = $itemsModel->getTaskLink($item['link'], 'edit');
			
			}
			
		}
		else {
			
			$redirectLink = $this->_baseUrl.'share';
			
		}
		
		if($deleteSession) {
			$editedItems = Session::get('editedItems');
			$editedItemId = $this->_itemId ? $this->_itemId : 'new'.$this->_itemType;
			if(isset($editedItems[$editedItemId]['images'][$type])) {
				$imagePath = $editedItems[$editedItemId]['images'][$type];
				unset($editedItems[$editedItemId]['images'][$type]);
				Session::set('editedItems',$editedItems);
				// remove file
				if(file_exists($imagePath)) {
					unlink($imagePath);
				}
			}
		}
		
		return $this->_redirect($redirectLink);
		
	}
	
	/**
	 *	Display encyclopedia of tips
	 *
	 *	@access public
	 */
	public function encyclopedia_of_tips() {
		$itemsModel = BluApplication::getModel('items');
		$this->_itemId = $itemsModel->getItemId("encyclopedia_of_tips");
		$item = $itemsModel->getItem($this->_itemId);
		
		/*$quicktipsByFirstLetters = $itemsModel->getQuicktipsByMetaGroup('encyclopedia_of_tips_first_letters');
		$quicktipFirstLetters = array();
		foreach($quicktipsByFirstLetters as $firstLetter=>$letterQuicktips) {
			foreach($letterQuicktips as $quicktipId=>$data) {
				$quicktipFirstLetters[$quicktipId] = $firstLetter;
			}
		}
		$quicktipsBySections = $itemsModel->getQuicktipsByMetaGroup('encyclopedia_of_tips_sections');
		$quicktipSections = array();
		foreach($quicktipsBySections as $section=>$sectionQuicktips) {
			foreach($sectionQuicktips as $quicktipId=>$data) {
				$quicktipSections[$quicktipId] = $section;
			}
		}
		$quicktips = $itemsModel->getQuicktips();
		$groupedQuicktips = array();
		foreach($quicktips as $quicktip) {
			if(!isset($quicktipFirstLetters[$quicktip['id']])) {
				continue;
			}
			$firstLetter = $quicktipFirstLetters[$quicktip['id']];
			if(isset($quicktipSections[$quicktip['id']])) {
				$section = $quicktipSections[$quicktip['id']];
				$groupedQuicktips[$firstLetter][$section][$quicktip['id']] = $quicktip;
			}
			else {
				$groupedQuicktips[$firstLetter][$quicktip['id']] = $quicktip;
			}
		}
		ksort($groupedQuicktips);*/
		$tabs = Request::getString('tabs','0');
		include(BLUPATH_TEMPLATES.'/quicktips/encyclopedia_of_tips.php');
		return;
	}
	
	function category_hubs(){
        // Get item
        $itemsModel = BluApplication::getModel('items');
        if (!$item = $itemsModel->getItem($this->_itemId)) {
            return $this->_errorRedirect();
        }

        // Get categories
		$categories = array();
        /*$metaModel = BluApplication::getModel('meta');
        $itemMetaGroups = $metaModel->getItemMetaGroups($item['id']);
        
        foreach ($itemMetaGroups as $metaGroup) {
            if ($metaGroup['slug'] == 'top_levels'){
                continue;
            } 
            if ($metaGroup['excludeValues'] == 'show_available'){ //hack to skip the categories choosed from USDA
                continue;
            } 
            if ($metaGroup['slug'] == 'author'){ //hack to skip the author
                continue;
            }            
            if($metaGroup['values']){
                foreach ($metaGroup['values'] as $metaValue) {
                    if (!is_array($metaValue) || !$metaValue['display']){ 
                        continue;
                    }
                    $categories[] = Array(
                        'parent' => $metaGroup['name'],
                        'link' => $metaValue['slug'],
                        'name' => $metaValue['name']
                    );
                }
            }
        }*/
        include(BLUPATH_TEMPLATES.'/recipes/details/category_hubs.php');
                
    }
    
	function get_categories(){
		// Get item
        $itemsModel = BluApplication::getModel('items');
        if (!$item = $itemsModel->getItem($this->_itemId)) {
            return $this->_errorRedirect();
        }

        // Get categories
		$metaModel = BluApplication::getModel('meta');
        $itemMetaGroups = $metaModel->getItemMetaGroups($item['id']);
        $categories = array();
        foreach ($itemMetaGroups as $metaGroup) {
            if (isset($metaGroup['slug']) && $metaGroup['slug'] == 'top_levels'){
                continue;
            } 
            if (isset($metaGroup['excludeValues']) && $metaGroup['excludeValues'] == 'show_available'){ //hack to skip the categories choosed from USDA
                continue;
            } 
            if (isset($metaGroup['author']) && $metaGroup['slug'] == 'author'){ //hack to skip the author
                continue;
            }            
            if($metaGroup['values']){
                foreach ($metaGroup['values'] as $metaValue) {
                    if (!is_array($metaValue) || !$metaValue['display']){ 
                        continue;
                    }
                    $categories[] = Array(
                        'parent' => $metaGroup['name'],
                        'link' => $metaValue['slug'],
                        'name' => $metaValue['name']
                    );
                }
            }
		}
		return $categories;
	}
}

?>
