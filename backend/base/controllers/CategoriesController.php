<?php

/**
 *	Categories admin
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class CategoriesController extends ClientBackendController
{
	/**
	 *	Current category (meta value ID)
	 *
	 *	@access protected
	 *	@var int
	 */
	protected $_category;
	
	/**
	 *	Menu slug
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_menuSlug = 'category_listing';
	
	/**
	 *	Constructor
	 *
	 *	@access public
	 */
	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->_category = false;
		if (!empty($args)) {
			$metaModel = BluApplication::getModel('meta');
			$this->_category = $metaModel->getValueIdBySlug(reset($args));
		}
	}
	
	/**
	 *	Default view
	 *
	 *	@access public
	 */
	public function view()
	{
		// Get current category
		if ($this->_category) {
			$metaModel = BluApplication::getModel('meta');
			$category = $metaModel->getValue($this->_category);
			extract($category);
		}
		
		// Load template
		include(BLUPATH_BASE_TEMPLATES.'/categories/view.php');
	}
	
	/**
	 *	View category tree
	 *
	 *	@access public
	 */
	public function tree()
	{
		// Get categories
		$metaModel = BluApplication::getModel('meta');
		$hierarchy = $metaModel->getHierarchy();
		
		// Load view
		foreach ($hierarchy as $category) { 
			if (empty($category['selector'])) {
				$this->_category($category);
			}
		}
	}
	
	/**
	 *	View category
	 *
	 *	@access protected
	 *	@param array Hierarchy element
	 */
	protected function _category($category)
	{
		// Check if displayable
		if ($category['internal'] ){//|| !$category['display']) {
			return false;
		}
		
		// Check if category is current one
		Template::set('current', $category['id'] == $this->_category);
		
		// Set template variables
		extract($category);
		Template::set('selector', !empty($selector));
		Template::set('hasChildren', empty($selector) && !empty($values));
		
		// Load template
		include(BLUPATH_BASE_TEMPLATES.'/categories/category.php');
	}
	
	/**
	 *	Display task panel
	 *
	 *	@access public
	 */
	public function panel()
	{
		// Load homepage
		if (!$this->_category) {
			return include(BLUPATH_BASE_TEMPLATES.'/categories/landing.php');
		}
		Session::set('categories_clearing', false);
		
		// Load bottom hierarchy filter
		$metaModel = BluApplication::getModel('meta');
		$category = $metaModel->getValue($this->_category);
		$category['link'] = $metaModel->getFullLink($category['id']);
		
		// Get parent
		$ancestry = $metaModel->getHierarchyElementAncestry($this->_category);
		if (!empty($ancestry)) {
			foreach ($ancestry as $valueId => &$ancestor) {
				$ancestor = $metaModel->getValue($valueId);
			}
			unset($ancestor);
		}
		
		// Load template
		extract($category);
		include(BLUPATH_BASE_TEMPLATES.'/categories/tasks.php');
	}
	
	/**
	 *	Add a subcategory to a category
	 *
	 *	@access public
	 */
	public function add_category()
	{
		$metaModel = BluApplication::getModel('meta');
		$category = $metaModel->getValue($this->_category);
		extract($category);
		
		include(BLUPATH_BASE_TEMPLATES.'/categories/add_category.php');
	}
	
	/**
	 *	Add a subcategory to a category
	 *
	 *	@access public
	 */
	public function add_category_save()
	{
		$metaModel = BluApplication::getModel('meta');
		$category = $metaModel->getValue($this->_category);
		
		if (Request::getBool('submit')) {
			$subcategoryName = Request::getString('category');
			
			// Check if meta value slug exists
			if (!$metaModel->metaValueSlugAvailable(Utility::slugify($subcategoryName), 'EN')) {
				Messages::addMessage('The name <code>'.$subcategoryName.'</code> is already in use, please choose another.', 'warn');
				
			// Try to add hierarchy element
			} else if ($valueId = $metaModel->addHierarchyElement($category['id'], $subcategoryName)) {
				Messages::addMessage('Added subcategory <code>'.$subcategoryName.'</code>.');
				$category = $metaModel->getValue($valueId);
				
			// Fail
			} else {
				Messages::addMessage('Could not add subcategory, please try again.', 'error');
			}
		}
		
		return $this->_redirect('/categories/'.$category['slug']);
	}
	
	/**
	 *	Add an item to a category
	 *
	 *	@access public
	 */
	public function add_item()
	{
		$metaModel = BluApplication::getModel('meta');
		$category = $metaModel->getValue($this->_category);
		extract($category);
		
		// Do search, if requested
		if ($searchTerm = Request::getString('searchterm')) {
			
			// Get request
			$page = Request::getInt('page', 1);
			$limit = 10;

			// Get items
			$itemsModel = BluApplication::getModel('items');
			$items = $itemsModel->getItems(null, null, 'relevance', array(), $searchTerm);
			
			$total = count($items);
			
			$items = array_slice($items, ($page - 1) * $limit, $limit, true);
			$itemsModel->addDetails($items);
			
			// Get pagination
			$pagination = Pagination::simple(array(
				'limit' => $limit,
				'total' => $total,
				'current' => $page,
				'url' => '?searchterm='.urlencode($searchTerm).'&page='
			));
		}

		// Load template
		switch ($this->_doc->getFormat()) {
			case 'json':
				ob_start();
				break;
		}
		include(BLUPATH_BASE_TEMPLATES.'/categories/add_item.php');
		switch ($this->_doc->getFormat()) {
			case 'json':
				$response = array();
				$response['items'] = ob_get_clean();
				echo json_encode($response);
				break;
		}
	}
	
	/**
	 *	Add an item to a category
	 *
	 *	@access public
	 */
	public function add_item_save()
	{
		$metaModel = BluApplication::getModel('meta');
		$category = $metaModel->getValue($this->_category);
		
		$itemSlug = end($this->_args);

		$this->_add_item_save($category,$itemSlug);
		
		return $this->view();
	}

	/**
	 *	Add many items to a category
	 *
	 *	@access public
	 */
	public function add_item_save_multi()
	{
		$metaModel = BluApplication::getModel('meta');
		$category = $metaModel->getValue($this->_category);
		
		$itemSlugs = Request::getArray('item');

		foreach ($itemSlugs as $itemSlug) {
			$this->_add_item_save($category,$itemSlug);
		}
		return $this->view();
	}

	/**
	 * Add a single item to a category
	 */
	private function _add_item_save($category,$itemSlug)
	{
		$itemsModel = BluApplication::getModel('items');
		$metaModel = BluApplication::getModel('meta');

		// Find item ID
		if (!$itemId = $itemsModel->getItemId($itemSlug)) {
			Messages::addMessage('Could not find this recipe/article, please try again.', 'warn');
			
		} else {
			$item = $itemsModel->getItem($itemId);
			$valueGroupMapping = $metaModel->getMetaValueGroupMapping();

			// Already added
			if (isset($item['meta'][$valueGroupMapping[$category['id']]]['values'][$category['id']])) {
				Messages::addMessage('<code>'.$item['title'].'</code> already added.');
				
			// Add item to hierarchy element
			} else if ($metaModel->addHierarchyElementItem($itemId, $category['id'], true)) {
				Messages::addMessage('Added <code>'.$item['title'].'</code> to <code>'.$category['name'].'</code> category.');
				
			// Fail
			} else {
				Messages::addMessage('Could not assign <code>'.$item['title'].'</code>, please try again.', 'error');
			}
		}
	}

	public function pushChangesLive()
	{
		if (Session::get('categories_clearing', false)) {
                        include BLUPATH_BASE_TEMPLATES.'/categories/cache_cleared.php';
                        return;
                }
		Session::set('categories_clearing', true);
		$itemsModel = BluApplication::getModel('items');
                $metaModel = BluApplication::getModel('meta');
		  // Rebuild hierarchy
                        // EDIT: I give up
                $cacheModel = BluApplication::getModel('cache');
                $cacheModel->deleteEntriesLike('meta');
echo 'clearing';
                // Flush search results
                $itemsModel->flushItemSearches();
		include BLUPATH_BASE_TEMPLATES.'/categories/cache_cleared.php';
	}
	
	/**
	 *	Edit a category
	 *
	 *	@access public
	 */
	public function edit_category()
	{
		$metaModel = BluApplication::getModel('meta');
		$category = $metaModel->getValue($this->_category);
		extract($category);
		
		include(BLUPATH_BASE_TEMPLATES.'/categories/edit_category.php');
	}
	
	/**
	 *	Edit a category
	 *
	 *	@access public
	 */
	public function edit_category_save()
	{
		$metaModel = BluApplication::getModel('meta');
		$category = $metaModel->getValue($this->_category);
		
		if (Request::getBool('submit')) {
			
			// Get request
			$name = Request::getString('name');
			$keywords = Request::getString('keywords');
			$pageDescription = Request::getString('pageDescription');
			$description = Request::getString('description');
			
			// Save it
			if ($metaModel->updateLanguageMetaValue($category['id'], 'EN', $name, $description, $keywords, null, null, null, $pageDescription)) {
				Messages::addMessage('Category details saved.');
			} else {
				Messages::addMessage('Category details could not be saved, please try again.', 'error');
			}
		}
		
		return $this->_redirect('/categories/'.$category['slug']);
	}
	
	/**
	 *	Remove a category
	 *
	 *	@access public
	 */
	public function remove_category()
	{
		$metaModel = BluApplication::getModel('meta');
		$category = $metaModel->getValue($this->_category);
		extract($category);
		
		include(BLUPATH_BASE_TEMPLATES.'/categories/remove_category.php');
	}
	
	/**
	 *	Remove a category
	 *
	 *	@access public
	 */
	public function remove_category_confirm()
	{
		$metaModel = BluApplication::getModel('meta');
		$category = $metaModel->getValue($this->_category);
		
		if (Request::getBool('submit')) {
			
			// Get hierarchy parent for redirect link
			$parentFilters = $metaModel->getHierarchyElementAncestry($this->_category);
			$parentCategory = $metaModel->getValue(end($parentFilters));
			
			// Unlink hierarchy element
			if ($metaModel->unlinkHierarchyElement($category['id'])) {
				Messages::addMessage('Category <code>'.$category['name'].'</code> deleted.');
				$category = $parentCategory;
				
			// Fail
			} else {
				Messages::addMessage('Category could not be deleted, please try again.', 'error');
				return $this->view();
			}
		}
		
		return $this->_redirect('/categories/'.$category['slug']);
	}
	
	/**
	 *	Unpublish a category
	 *
	 *	@access public
	 */
	public function unpublish_category()
	{
		$metaModel = BluApplication::getModel('meta');
		$category = $metaModel->getValue($this->_category);
		extract($category);
		
		include(BLUPATH_BASE_TEMPLATES.'/categories/unpublish_category.php');
	}

	/**
	 *	unpublish a category
	 *
	 *	@access public
	 */
	public function unpublish_category_confirm()
	{
		$metaModel = BluApplication::getModel('meta');
		$category = $metaModel->getValue($this->_category);
		$unpublisharticles = Request::getBool('unpublisharticles');
		if (Request::getBool('submit')) {
			// set the category and articles as not live
			if ($metaModel->unPublishCategory($category['id'],$unpublisharticles)) {
				Messages::addMessage('Category <code>'.$category['name'].'</code> unpublished.');
			// Fail
			} else {
				Messages::addMessage('Category could not be unpublished, please try again.', 'error');
				return $this->view();
			}
		}
		
		return $this->_redirect('/categories/'.$category['slug']);
	}
	
	/**
	 *	Publish a category
	 *
	 *	@access public
	 */
	public function publish_category()
	{
		$metaModel = BluApplication::getModel('meta');
		$category = $metaModel->getValue($this->_category);
		extract($category);
		
		include(BLUPATH_BASE_TEMPLATES.'/categories/publish_category.php');
	}	
	
	/**
	 *	publish a category
	 *
	 *	@access public
	 */
	public function publish_category_confirm()
	{
		$metaModel = BluApplication::getModel('meta');
		$category = $metaModel->getValue($this->_category);
		$publisharticles = Request::getBool('publisharticles');
		if (Request::getBool('submit')) {
			// set the category and articles as live
			if ($metaModel->publishCategory($category['id'],$publisharticles)) {
				Messages::addMessage('Category <code>'.$category['name'].'</code> published.');
			// Fail
			} else {
				Messages::addMessage('Category could not be published, please try again.', 'error');
				return $this->view();
			}
		}
		
		return $this->_redirect('/categories/'.$category['slug']);
	}	
	
	/**
	 *	Display items under a category
	 *
	 *	@access public
	 */
	public function items()
	{
		$metaModel = BluApplication::getModel('meta');
		$category = $metaModel->getValue($this->_category);
		
		// Get request
		$page = Request::getInt('page', 1);
		$limit = 20;
		$refresh = Request::getBool('refresh');
		$searchTerm = Request::getString('searchterm');
		
		// Get parent filters
		$parentFilters = $metaModel->getHierarchyElementAncestry($this->_category);
		$allFilters = array();
		$valueGroupMapping = $metaModel->getMetaValueGroupMapping();
		if (!empty($parentFilters)) {
			foreach ($parentFilters as $valueId) {
				$groupId = $valueGroupMapping[$valueId];
				$allFilters[$groupId][$valueId] = $valueId;
			}
		}
		$allFilters[$valueGroupMapping[$this->_category]][$this->_category] = $this->_category;
		
		// Get items
		$itemsModel = BluApplication::getModel('items');
		$items = $itemsModel->getItems(null, null, 'name_asc', $allFilters, $searchTerm, $refresh);
		$total = count($items);
		$items = array_slice($items, ($page - 1) * $limit, $limit, true);
		$itemsModel->addDetails($items);
		foreach ($items as &$item) {
			$item['editLink'] = $itemsModel->getTaskLink($item['link'], 'edit');
			$item['editRelatedLink'] = $itemsModel->getTaskLink($item['link'], 'edit_related');
			$item['setFeaturedLink'] = $itemsModel->getTaskLink($item['link'], 'set_featured');
		}
		unset($item);
		
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => '?'.($searchTerm ? 'searchterm='.$searchTerm.'&' : '').'page='
		));
		
		// Load template
		extract($category);
		switch ($this->_doc->getFormat()) {
			case 'json':
				ob_start();
				break;
		}
		include(BLUPATH_BASE_TEMPLATES.'/categories/items.php');
		switch ($this->_doc->getFormat()) {
			case 'json':
				$response = array();
				$response['content'] = ob_get_clean();
				echo json_encode($response);
				break;
		}
	}
	
	/**
	 *	Remove an item from a category
	 *
	 *	@access public
	 */
	public function remove_item()
	{
		$metaModel = BluApplication::getModel('meta');
		$category = $metaModel->getValue($this->_category);
		
		// Get item
		$itemsModel = BluApplication::getModel('items');
		$itemSlug = end($this->_args);
		$itemId =  $itemsModel->getItemId($itemSlug);
		$item = $itemsModel->getItem($itemId);

		
		// Find item
		if (!$itemId) {
			Messages::addMessage('Could not find recipe/article, please try again.', 'warn');
			
		// Remove from category
		} else if ($metaModel->deleteHierarchyElementItem($itemId, $category['id'], false)) {
			Messages::addMessage('Removed <code>'.$item['title'].'</code> from <code>'.$category['name'].'</code>.');
			
		// Phail
		} else {
			Messages::addMessage('Could not remove <code>'.$item['title'].'</code> from <code>'.$category['name'].'</code>, please try again.', 'error');
		}
		
		// Redirect
		return $this->view();
	}

	/**
	 * Export language meta values
	 *
	 * @access public
	 */
	public function export()
	{
		// Get data
		$metaModel = BluApplication::getModel('meta');
		$exportData = $metaModel->exportLanguageMetaValues();

		// Build CSV
		$exportFile = new Csv(array('Category ID', 'Name', 'Description', 'Keywords (comma separated)'));
		if (!empty($exportData)) {
			foreach ($exportData as $valueId => $langValue) {
				$exportFile->appendRow($langValue);
			}
		}

		// Output
		$this->_doc->setFormat('raw');
		$this->_doc->setMimeType('text/csv');
		$this->_doc->setDisposition('attachment; file="languageMetaValuesExport-'.date('Ymd').'.csv"');
		$exportFile->output();
	}

	/**
	 * Import language meta values
	 *
	 * @access public
	 */
	public function import()
	{
		// Read in POSTed file
		$importFileDetails = Request::getFile('datafile');
		if ($importFileDetails) {
			$importFile = Csv::read($importFileDetails['tmp_name']);
			$importFileData = $importFile->get();
			
			if (!empty($importFileData)) {
				$metaModel = BluApplication::getModel('meta');
				$metaModel->importLanguageMetaValues($importFileData);

				Messages::addMessage('Imported category data!');
			}
		}

		// Load template (form)
		include(BLUPATH_BASE_TEMPLATES.'/categories/import.php');
	}
}

