<?php

/**
 *	Box admin
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class BoxController extends ClientBackendController
{
	/**
	 *	Current box ID
	 *
	 *	@access protected
	 *	@var int
	 */
	protected $_boxId;

	/**
	 *	Menu slug
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_menuSlug = 'box';

	/**
	 *	Constructor
	 *
	 *	@access public
	 */
	public function __construct($args)
	{
		parent::__construct($args);

		// Get box/box content ID
		if (!empty($this->_args[0])) {
			$this->_boxId = (int) $this->_args[0];
		}
	}

	/**
	 *	Default overview
	 *
	 *	@access public
	 */
	public function view()
	{
		// Load template
		include(BLUPATH_BASE_TEMPLATES.'/box/view.php');
	}

	/**
	 *	Display box details
	 *
	 *	@access public
	 */
	public function details()
	{
		// Get box
		$boxModel = BluApplication::getModel('boxout');
		if (!$box = $boxModel->getBox($this->_boxId)) {
			Messages::addMessage('Could not find your box #'.$this->_boxId.'.', 'error');
			return $this->_showMessages('view');
		}

		Template::set('search', Request::getString('search'));

		// Get *raw* box contents too
		$boxContents = $boxModel->getBoxContents($box['id']);

		// Format for view
		$canAdd = Request::getBool('canAdd', $box['canAdd']);
		$name = $box['internalName'];
		Template::set('box_slug', $box['slug']);

		// Display
		switch ($box['type']) {
			case 'dailyTip':
				// Load "encyclopedia" article body
				$itemsModel = BluApplication::getModel('items');
				$encyclopedia = $itemsModel->getItem(37221);
				break;
			case 'linkList':
				$linkCategories = $boxModel->getLinkCategories();
				Template::set('linkCategories',$linkCategories);
				break;
			case 'staticContent':
				if(count($boxContents)==1 && $box['slug'] != 'about_us') {
					$canAdd = false;
					if($box['slug'] == 'about_us') {
						$canDelete = true;
					}
				}
				Template::set('tinyMce', true);
				break;
		}
		include(BLUPATH_BASE_TEMPLATES.'/box/details.php');
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
		include (BLUPATH_BASE_TEMPLATES.'/box/content/featuredItemsSearch.php');
	}

	/**
	 *	Add/edit box content details
	 *
	 *	@access public
	 */
	public function content()
	{
		// Get model
		$boxModel = BluApplication::getModel('boxout');

		// Add/edit box content
		if (Request::getBool('save')) {

			// Get details for all types of boxes, can't be arsed to do a switch-case.
			$link = Request::getString('link', null);
			$sequence = Request::getInt('order', null);
			$title = Request::getString('title', null);
			$subtitle = Request::getString('subtitle', null);
			$text = Request::getString('text', null, null, true);	// Allow HTML
			$info = Request::getArray('info', null);
			$langCode = Request::getString('langCode', null);

			$subtitleLink = Request::getString('subtitleLink', null);
			$date = Request::getString('date', null);
			$linkCategory = Request::getString('linkCategory', null);

			if($subtitleLink || $linkCategory) {
				$linkCategories = $boxModel->getLinkCategories();
				$info = array();
				$info['subtitleLink'] = $subtitleLink;
				$info['date'] = $date;
				if(isset($linkCategories[$linkCategory])) {
					$info['linkCategory'] = $linkCategories[$linkCategory];
				}
			}

			// Edit existing?
			if ($contentId = Request::getInt('contentId')) {
				if ($boxModel->updateBoxContent($contentId, $langCode, $title, $subtitle, $text, $link, $info, $sequence)) {
					Messages::addMessage('Box content updated.', 'info');
				} else {
					Messages::addMessage('Could not update box content.', 'error');
				}

				// Get box ID for redirection
				$box = $boxModel->getBoxFromContent($contentId);
				$this->_boxId = $box['id'];

			// Add new?
			} else if ($this->_boxId = Request::getInt('boxId')) {
				if ($contentId = $boxModel->addBoxContent($this->_boxId, $langCode, $title, $subtitle, $text, $link, $info, $sequence)) {
					Messages::addMessage('Box content added.', 'info');
				} else {
					Messages::addMessage('Could not add box content.', 'error');
				}
			}

			// Update image for it?
			if ($image = Request::getFile('image')) {
				if ($imageName = Upload::saveFile($image, BLUPATH_ASSETS.'/indeximages/')) {

					// Update database
					if (!$boxModel->setBoxContentImage($contentId, $imageName)) {
						Messages::addMessage('Could not save image '.$image['name'].' to database.', 'error');
					}

				} else {
					Messages::addMessage('Could not store uploaded image '.$image['name'].'.', 'error');
				}
			}

			// Update Extra image if added
			if($extraImage = Request::getFile('extraImage')){
				if($extraImageName = Upload::saveFile($extraImage, BLUPATH_ASSETS.'/indeximages/')){
					// Update database
					if (!$boxModel->setBoxExtraImage($contentId, $extraImageName)) {
						Messages::addMessage('Could not save image '.$extraImage['name'].' to database.', 'error');
					}
				} else {
					Messages::addMessage('Could not store uploaded image '.$extraImage['name'].'.', 'error');
				}
			}

			// Flush box content
			$boxModel->flushBoxContent($contentId);

			// Redirect to box details
			return $this->details();

		// Delete box content
		} else if (Request::getBool('delete')) {

			// Get request
			$contentId = Request::getInt('contentId');

			// Get box for flushing
			$box = $boxModel->getBoxFromContent($contentId);

			// Delete from database
			if ($boxModel->deleteBoxContent($contentId)) {
				Messages::addMessage('Box content deleted.', 'info');
			} else {
				Messages::addMessage('Could not delete box content, please try again.', 'error');
			}

			// Flush box
			$boxModel->flushBox($box['id']);

			// Redirect to box details
			$this->_boxId = $box['id'];
			return $this->details();

		// View box content
		} else {
			return $this->_content($contentId);
		}
	}

	/**
	 *	Display box content details
	 *
	 *	@access protected
	 *	@param int Box content ID, if editing
	 */
	protected function _content($contentId = null)
	{
		// Get model
		$boxModel = BluApplication::getModel('boxout');

		// Editing?
		if ($contentId) {

			// Get box content
			$box = $boxModel->getBoxFromContent($contentId);
			$boxContent = $boxModel->getBoxContent($contentId);

			// Format for view
			$contentId = $boxContent['id'];
			$link = $boxContent['link'];
			$sequence = $boxContent['sequence'];
			$title = $boxContent['title'];
			$subtitle = $boxContent['subtitle'];
			$text = $boxContent['text'];
			$langCode = 'EN';
			$imageName = $boxContent['imageName'];
			$info = $boxContent['info'];

			if(isset($info['subtitleLink'])) {
				$subtitleLink = $info['subtitleLink'];
			}
			if(isset($info['date'])) {
				$date = $info['date'];
			}
			if(isset($info['linkCategory'])) {
				$linkCategory = $info['linkCategory'];
			}

		// Adding new
		} else {

			// Get box to die/add for
			$box = $boxModel->getBox($this->_boxId);

			// Format for view
			$link = '';
			$sequence = 0;
			$title = '';
			$subtitle = '';
			$text = '';
			$langCode = 'EN';
			$imageName = '';
			$boxId = $box['id'];
			$info = array();
		}

		$canAdd = Request::getBool('canAdd', $box['canAdd']);
		$canDelete = Request::getBool('canDelete', $box['canDelete']);

		// Type-specific display variables
		switch ($box['type']) {
			case 'featuredItems':
				$title = null;
				$image = null;
				$link = null;
				$itemId = null;

				if (isset($info['item'])) {

					// Get item
					$itemsModel = BluApplication::getModel('items');
					$item = $itemsModel->getItem($info['item']);

					$title = $item['title'];
					if(isset($item['featuredImage']['filename'])) {
						$image = $item['featuredImage']['filename'];
					}
					else {
						$image = $item['image']['filename'];
					}
					$link = $item['link'];
					$itemId = $item['id'];
				}
				break;

			case 'featuredUsers':
				$username = null;
				if (isset($info['user'])) {
					$userModel = BluApplication::getModel('user');
					$user = $userModel->getUser($info['user']);
					$username = $user['username'];
				}
				break;

			default:
				break;
		}

		// Load template
		include(BLUPATH_BASE_TEMPLATES.'/box/content/'.$box['type'].'.php');
	}

	/**
	 *	Empty box content template
	 *
	 *	@access public
	 */
	public function add_content()
	{
		return $this->_content();
	}
}

?>