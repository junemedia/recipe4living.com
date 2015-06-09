<?php

/**
 *	Giveaway controller
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class Recipe4livingGiveawayController extends ClientBackendController
{
	/**
	 *	Item view type
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_view = 'giveaway_listing';

	/**
	 *	Requested base url
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_baseUrl = null;

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
	protected $_menuSlug = 'giveaway_listing';
	
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
		$this->_baseUrl = '/giveaway/'.implode('/', $this->_args);
		
		// Show search form? (/search/?)
		$arg = end($this->_args);
		$this->_showSearch = (($arg == 'search') || Request::getBool('searchterm'));
		if ($arg == 'search') {
			array_pop($this->_args);
		}
		
		// View giveaway details? (.../*.htm)
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
			$giveawayModel = BluApplication::getModel('giveaway');
			$this->_itemId = $giveawayModel->getGiveawayId($slug);
		}
	}
	
	/**
	 *	List all items.
	 *
	 *	@access public
	 */
	public function view()
	{
		// Get models
		$itemsModel = BluApplication::getModel('items');
		$metaModel = BluApplication::getModel('meta');
		$giveawayModel = BluApplication::getModel('giveaway');
		
		// Load data
		switch ($this->_view) {
			case 'giveaway_listing':				
				// Set document meta
				$this->_doc->setTitle($this->_getTitle('pageTitle'));
				break;
				
			case 'giveaway_details':
				
				// Get article
				if (!$item = $itemsModel->getItem($this->_itemId)) {
					return $this->_errorRedirect();
				}
				
				// Set document meta
				$this->_doc->setTitle($item['title']);
				break;
		}
		
		// Load view
		switch ($this->_view) {
			case 'giveaway_listing':
				include(BLUPATH_TEMPLATES.'/giveaway/giveaway_listing.php');
				break;
				
			case 'giveaway_details':
				include(BLUPATH_TEMPLATES.'/giveaway/giveaway_details.php');
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
		// Get pagination values
		$total = 0;	
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$giveawayModel = BluApplication::getModel('giveaway');
		$items = $giveawayModel->getAllGiveaways($page, $limit, $total);
		
		// Add item details
		if (!empty($items)) {
			// Add live togglers 
			//http://www.recipe4living.com/oversight/articles/set_pending/asdf.htm
			foreach ($items as &$item) {
				$item['liveToggler'] = $giveawayModel->getTaskLink($item['link'], $item['status'] ? 'set_pending' : 'set_live');
				$item['featuredToggler'] = $giveawayModel->getTaskLink($item['link'], $item['featured'] ? 'unset_featured' : 'set_featured');	// For now, just use as toggler (sets to 0 or 1 only, no free entry.)
				$item['deleteToggler'] = $giveawayModel->getTaskLink($item['link'], $item['status']==-1 ? 'set_pending' : 'set_deleted');
				
				$item['editLink'] = $giveawayModel->getTaskLink($item['link'], 'edit');
			}
			unset($item);
		}
						
		// Get search options
		$showSearch = $this->_showSearch;
		$searchBaseUrl = null;
		$searchTerm = Request::getString('searchterm');
		
		// Build search base URL
		$searchBaseUrl = '&amp;searchterm='.urlencode($searchTerm);	
		
		// Get base URLs for listing updates
		$baseUrl = SITEURL.$this->_baseUrl;		
		$qsSep = '?';
		$paginationBaseUrl = $baseUrl.$qsSep.$searchBaseUrl.'&amp;page=';
		
		// Do pagination
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => $paginationBaseUrl
		));

		switch ($this->_view) {
			case 'giveaway_listing':
				include(BLUPATH_TEMPLATES.'/giveaway/items/giveaways.php');
				break;
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
		return 'All Giveaways';
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
		$giveawayModel = BluApplication::getModel('giveaway');
		if (!$item = $giveawayModel->getGiveaway($this->_itemId,true)) {
			return $this->_errorRedirect();
		}
		
		// Set live
		if ($success = $giveawayModel->updateStatus($item['id'],0)) {
			Messages::addMessage(ucfirst('Giveaway').' <code>'.$item['slug'].'</code> ('.$item['id'].') set to pending.', 'info');
			
		// Fail
		} else {
			Messages::addMessage(ucfirst('Giveaway').' <code>'.$item['slug'].'</code> ('.$item['id'].') not set to pending.', 'error');
		}
		
		// Output
		return $this->_redirect('/giveaway');
	}
	
	/**
	 *	Set the status of an item to live
	 *
	 *	@access public
	 */
	public function set_live()
	{
		// Get item
		$giveawayModel = BluApplication::getModel('giveaway');
		if (!$item = $giveawayModel->getGiveaway($this->_itemId,true)) {
			return $this->_errorRedirect();
		}
		
		// Set live
		if ($success = $giveawayModel->updateStatus($item['id'],1)) {
			Messages::addMessage(ucfirst('Giveaway').' <code>'.$item['slug'].'</code> ('.$item['id'].') set live.', 'info');
			
		// Fail
		} else {
			Messages::addMessage(ucfirst('Giveaway').' <code>'.$item['slug'].'</code> ('.$item['id'].') not set live.', 'error');
		}
		
		// Output
		return $this->_redirect('/giveaway');
	}
	
	/**
	 *	Set the status of an item to deleted
	 *
	 *	@access public
	 */
	public function set_deleted()
	{
		// Get item
		$giveawayModel = BluApplication::getModel('giveaway');
		if (!$item = $giveawayModel->getGiveaway($this->_itemId,true)) {
			return $this->_errorRedirect();
		}
		
		// Set live
		if ($success = $giveawayModel->updateStatus($item['id'],-1)) {
			Messages::addMessage(ucfirst('Giveaway').' <code>'.$item['slug'].'</code> ('.$item['id'].') set deleted.', 'info');
			
		// Fail
		} else {
			Messages::addMessage(ucfirst('Giveaway').' <code>'.$item['slug'].'</code> ('.$item['id'].') not set deleted.', 'error');
		}
		
		// Output
		return $this->_redirect('/giveaway');
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
		$giveawayModel = BluApplication::getModel('giveaway');
		if (!$item = $giveawayModel->getGiveaway($this->_itemId,true)) {
			return $this->_errorRedirect();
		}
				
		// Set live
		if ($success = $giveawayModel->updateFeatured($item['id'],$featureLevel)) {			
			if ($featureLevel) {
				Messages::addMessage(ucfirst('Giveaway').' <code>'.$item['slug'].'</code> ('.$item['id'].') set as featured.', 'info');
			} else {
				Messages::addMessage('<code>'.$item['slug'].'</code> ('.$item['id'].') set as not featured.', 'info');
			}
			
		// Fail
		} else {
			Messages::addMessage(ucfirst('Giveaway').' <code>'.$item['slug'].'</code> ('.$item['id'].') feature level not set.', 'error');
		}
		
		// Output
		return $this->_redirect('/giveaway');
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
	 *	Search giveaways
	 *
	 *	@access public
	 */
	public function search()
	{
		// Get items
		if ($search = Request::getString('search')) {
			$page = Request::getInt('page', 1);
			$limit = 10;
			$itemsModel = BluApplication::getModel('items');
			$offset = ($page-1)*$limit;
			$items = $itemsModel->getItems(null, null, null, array(), $search,false,true);
			// Do some final filtering (by live flag)
			$items = $itemsModel->filterLiveItems($items);
			$total = count($items);
			$items = array_slice($items, $offset, $limit, true);
			$itemsModel->addDetails($items);
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
	
	public function edit()
	{
		$giveawayModel = BluApplication::getModel('giveaway');
		$giveaway = $giveawayModel->getGiveaway($this->_itemId,true);
		Template::set('tinyMce', true);
		
		// Load template
		include(BLUPATH_TEMPLATES.'/giveaway/giveaway_edit.php');
	}
	
	public function add()
	{
		$this->edit();
	}
	
	public function save()
	{
		$oldGiveaway = array();
		$giveawayModel = BluApplication::getModel('giveaway');
		if($giveawayId = $this->_itemId) {
			$oldGiveaway = $giveawayModel->getGiveaway($giveawayId,true);
			if(!$oldGiveaway) {
				return $this->_redirect('/giveaway');
			}
		}
		
		$giveaway['title'] = $title = Request::getString('title');
		$giveaway['articleid'] = $articleId = Request::getString('review_id');
		$giveaway['publishDate'] = $startDate = Request::getString('start_date');
		$giveaway['endDate'] = $endDate = Request::getString('end_date');
		$giveaway['image'] = $image = Request::getString('product_image');		
		$giveaway['description'] = $description = Request::getString('description', null, null, true);
		
		$error = false;		
		
		if(empty($title)) {
			Messages::addMessage('Title cannot be empty.');
			$error = true;
		}
		if(empty($image)) {
			Messages::addMessage('Product image cannot be empty.');
			$error = true;
		}
		if(empty($description)) {
			Messages::addMessage('Description cannot be empty.');
			$error = true;
		}
		
		if(strtotime($startDate) > strtotime($endDate))
		{
			Messages::addMessage('The end date should not be earlier than start date.');
			$error = true;
		}
		
		$slug = Utility::slugify($title);
		$i = 1;
		while($giveawayModel->isSlugInUse($slug, $giveawayId)) {
			$i++;
			$slug = Utility::slugify($title).'_'.$i;
			if($i>=100) {
				Messages::addMessage('Could not generate slug.');
				$error = true;
				break;
			}
		}
		
		$giveaway['slug'] = $slug;
		$giveaway['src'] = preg_replace("/\s+/",'',$title);
		
		if($error) {
			$this->edit();
			return false;
		}
		
		if($giveawayId) {
			unset($giveaway['slug']);
			if(!$giveawayModel->updateGiveaway($giveawayId, $giveaway)) {
				$error = true;
			}
		}
		elseif (!$giveawayId = $giveawayModel->addGiveaway($giveaway)) {
			$error = true;
		}
		
		if (!$error) {
			Messages::addMessage('Giveaway <code>'.$title.'</code> has been saved.');
			return $this->_redirect('/giveaway');
		} else {
			Messages::addMessage('Could not save Giveaway, please try again.', 'error');
			$this->edit();
		}
	}
}

?>
