<?php

/**
 *	ItemsController.
 */
abstract class WorkitmomItemsController extends ClientBackendController {
	
	/**
	 *	Item type to filter by.
	 *
	 *	@param string Type of value to return
	 *	@return array/string.
	 */
	abstract protected function _getType($key = null);
	
	/**
	 *	List all items.
	 */
	public function view(){
		
		/* Set breadcrumbs */
		$breadcrumbs = BluApplication::getBreadcrumbs();
		$breadcrumbs->add($this->_getType('plural'), '/'.strtolower($this->_getType('plural')));
		
		/* Set page title */
		$this->_doc->setTitle($this->_getType('plural'));
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/items/landing.php');
		
	}
	
	/**
	 *	Listing.
	 */
	public function listing(){
		
		/* Get parameters */
		$limit = BluApplication::getSetting('backendListingLength');
		$page = Request::getInt('page', 1);
		
		/* Get model */
		$itemsModel = $this->getModel('newitems');
		
		/* Prepare sort */
		$options = array();
		
		/* Item type */
		$options['type'] = $type = $this->_getType('key');
		
		/* What to sort by */
		$sort = strtolower(Request::getCmd('sort'));
		if (!in_array($sort, array('id', 'date', 'title', 'category', 'comments'))){
			$sort = 'date';
		}
		$options['order'] = $sort;
		
		/* What direction to sort in */
		$direction = strtolower(Request::getCmd('direction'));
		if (!in_array($direction, array('asc', 'desc'))){
			$direction = 'asc';
		}
		$options['direction'] = $direction;
		
		/* Get data */
		$total = null;
		$items = $itemsModel->getItems(($page - 1) * $limit, $limit, $total, $options);
		
		/* Paginate */
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => '?sort='.$sort.'&amp;direction='.$direction.'&amp;page='
		));
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/items/listing.php');
		
	}
	
	/**
	 *	Listing: browse - individual rows.
	 */
	protected function listing_individual($item){
		
		/* Styling */
		static $alt = false;
		$alt = !$alt;
		$row = $alt ? 'odd' : 'even';
		
		if (!$item['live']){
			$priority = 'high';
		} else if ($item['featured']){
			$priority = 'low';
		} else {
			$priority = 'normal';
		}
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/items/listing/individual.php');
		
	}
	
	/**
	 *	Details page.
	 */
	public function details(){
		
		/* Get item ID */
		$args = $this->_args;
		if (empty($args)){
			return $this->_errorRedirect();
		}
		$itemId = (int) array_shift($args);
		
		/* Get item */
		$itemsModel = $this->getModel('newitems');
		$item = $itemsModel->getItem($itemId);
		if (empty($item)){
			return $this->_errorRedirect();
		}
		
		/* Get author */
		$author = $item['author'];
		
		/* Other display data */
		$categories = $itemsModel->getItemCategories($item['type']);
		$type = $this->_getType();
		
		/* Add breadcrumbs */
		$breadcrumbs = BluApplication::getBreadcrumbs();
		$breadcrumbs->add($this->_getType('plural'), '/'.strtolower($this->_getType('plural')));
		$breadcrumbs->add($this->_getType('singular').' #'.$item['id'], '/'.strtolower($this->_getType('plural')).'/details/'.$item['id']);
		
		/* Set page title */
		$this->_doc->setTitle($this->_getType('singular').' #'.$item['id'].' - '.$item['title']);
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/items/details.php');
		
	}
	
	/**
	 *	Update the item.
	 */
	public function edit()
	{
		// Get item ID
		if (empty($this->_args)) {
			return $this->_redirect('/'.strtolower($this->_getType('plural')));
		}
		$itemId = $this->_args[0];
		
		// Check item exists
		$itemsModel = $this->getModel('newitems');
		$item = $itemsModel->getItem($itemId);
		if (empty($item)) {
			return $this->_redirect('/'.strtolower($this->_getType('plural')));
		}
		
		// Get updates
		$edit['title'] = Request::getString('title', $item['title']);
		$edit['subtitle'] = Request::getString('subtitle', $item['subtitle']);
		$edit['body'] = Request::getString('body', $item['body'], null, true);
		
		// Commit to model
		$success = $itemsModel->edit($itemId, $edit);
		
		// Message
		if ($success){
			Messages::addMessage($this->_getType('singular').' #'.$itemId.' updated.');
		} else {
			Messages::addMessage($this->_getType('singular').' #'.$itemId.' was not changed.', 'error');
		}
		
		// Redirect to details page
		return $this->_redirect('/'.strtolower($this->_getType('plural')).'/details/'.$itemId);	
	}
	
	/**
	 *	Delete the item.
	 */
	public function delete(){
		
		/* Get arguments */
		$args = $this->_args;
		if (empty($args)){
			return $this->_redirect('/'.strtolower($this->_getType('plural')));
		}
		
		/* Delete item */
		$itemId = array_shift($args);
		$itemsModel = $this->getModel('newitems');
		$success = $itemsModel->deleteItem($itemId);
		
		/* Message */
		if ($success){
			Messages::addMessage($this->_getType('singular').' #'.$itemId.' deleted.');
		} else {
			Messages::addMessage($this->_getType('singular').' #'.$itemId.' was not changed.', 'error');
		}
		
		/* Redirect to listings page */
		return $this->_redirect('/'.strtolower($this->_getType('plural')));
		
	}
	
	/**
	 *	Set the status of an item to pending
	 */
	public function set_pending(){
		
		/* Get arguments */
		$args = $this->_args;
		if (empty($args)){
			return $this->_redirect('/'.strtolower($this->_getType('plural')));
		}
		
		/* Set status */
		$itemId = array_shift($args);
		$itemsModel = $this->getModel('newitems');
		$success = $itemsModel->setStatus($itemId, false);
		
		/* Message */
		if ($success){
			Messages::addMessage($this->_getType('singular').' #'.$itemId.' set to "<code>pending</code>"');
		} else {
			Messages::addMessage($this->_getType('singular').' #'.$itemId.' was not changed.', 'error');
		}
		
		/* Redirect to details page */
		return $this->_redirect('/'.strtolower($this->_getType('plural')).'/details/'.$itemId);
		
	}
	
	/**
	 *	Set the status of an item to live
	 */
	public function set_live(){
		
		/* Get arguments */
		$args = $this->_args;
		if (empty($args)){
			return $this->_redirect('/'.strtolower($this->_getType('plural')));
		}
		
		/* Set status */
		$itemId = array_shift($args);
		$itemsModel = $this->getModel('newitems');
		$success = $itemsModel->setStatus($itemId, true);
		
		/* Message */
		if ($success){
			Messages::addMessage($this->_getType('singular').' #'.$itemId.' set to "<code>live</code>"');
		} else {
			Messages::addMessage($this->_getType('singular').' #'.$itemId.' was not changed.', 'error');
		}
		
		/* Redirect to details page */
		return $this->_redirect('/'.strtolower($this->_getType('plural')).'/details/'.$itemId);
		
	}
	
	/**
	 *	Set the category for an item
	 */
	public function set_category(){
		
		/* Get arguments */
		$args = $this->_args;
		if (empty($args)){
			return $this->_redirect('/'.strtolower($this->_getType('plural')));
		}
		
		/* Set category */
		$itemId = array_shift($args);
		$categoryId = Request::getInt('categoryId');
		$itemsModel = $this->getModel('newitems');
		$success = $itemsModel->updateCategory($itemId, $categoryId);
		
		/* Get category */
		$categories = $itemsModel->getItemCategories($this->_getType('key'));
		$categoryName = Utility::multi_array_get($categories, $categoryId, false);
		
		/* Message */
		if ($success && $categoryName){
			Messages::addMessage($this->_getType('singular').' #'.$itemId.' category set to "<code>'.$categoryName.'</code>"');
		} else {
			Messages::addMessage($this->_getType('singular').' #'.$itemId.' was not changed.', 'error');
		}
		
		/* Redirect to details page */
		return $this->_redirect('/'.strtolower($this->_getType('plural')).'/details/'.$itemId);
		
	}
	
	/**
	 *	Add a tag to an item.
	 */
	public function add_tag(){
		
		/* Get arguments */
		$args = $this->_args;
		if (empty($args)){
			return $this->_redirect('/'.strtolower($this->_getType('plural')));
		}
		
		/* Try associating tag to item */
		$itemsModel = $this->getModel('newitems');
		$itemId = array_shift($args);
		$tag = Request::getString('tag');
		$success = $itemsModel->addTag($itemId, $tag);
		
		/* Message */
		if ($success){
			Messages::addMessage('<code>'.$tag.'</code> added as tag for '.$this->_getType('singular').' #'.$itemId);
		} else {
			Messages::addMessage($this->_getType('singular').' #'.$itemId.' was not changed.', 'error');
		}
		
		/* Redirect to details page */
		return $this->_redirect('/'.strtolower($this->_getType('plural')).'/details/'.$itemId);
		
	}
	
	/**
	 *	Delete a tag from an item.	TODO.
	 */
	public function delete_tag(){
		
		/* Get arguments */
		$args = $this->_args;
		if (empty($args)){
			return $this->_redirect('/'.strtolower($this->_getType('plural')));
		}
		
		/* Try associating tag to item */
		$itemsModel = $this->getModel('newitems');
		$itemId = array_shift($args);
		$tag = Request::getString('tag');
		$success = $itemsModel->deleteTag($itemId, $tag);
		
		/* Message */
		if ($success){
			Messages::addMessage('<code>'.$tag.'</code> tag removed from '.$this->_getType('singular').' #'.$itemId);
		} else {
			Messages::addMessage($this->_getType('singular').' #'.$itemId.' was not changed.', 'error');
		}
		
		/* Redirect to details page */
		return $this->_redirect('/'.strtolower($this->_getType('plural')).'/details/'.$itemId);
		
	}
	
	/**
	 *	Legacy hack: returns a link for the item image.
	 */
	public function legacy_template_image(){
		
		/* Set format */
		$this->_doc->setFormat('raw');
		
		/* Get item id */
		$args = $this->_args;
		if (!Utility::iterable($args)){ return false; }
		$itemId = (int) array_shift($args);
		if (!$itemId){ return false; }
		
		/* Get arguments */
		$width = Request::getInt('width', 60);
		$height = Request::getInt('height', $width);
		
		/* Get item */
		$itemsModel = $this->getModel('newitems');
		$item = $itemsModel->getItem($itemId);
		if (!Utility::iterable($item)){ return false; }
		
		/* Echo image URL */
		Template::image($item, $width, $height);
		
		/* Exit */
		return true;
		
	}
	
}

?>