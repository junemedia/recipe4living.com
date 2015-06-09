<?php

/**
 *	Slideshows controller
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class Recipe4livingSlideshowsController extends ClientBackendController
{

	protected $_menuSlug = 'slideshows';

	/**
	 * Base url
	 *
	 * @var string Base url
	 */
	protected $_baseUrl = '/slideshows';

	public function view()
	{
		// Get models
		$slideshowsModel = BluApplication::getModel('slideshows');

		$page = Request::getInt('page', 1);

		$limit = 20;
		$total = NULL;

		$slideshows = $slideshowsModel->getSlideshows($page, $limit, $total);
		
		$slideshowCount = $total;
		
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => '?page='
		));
		
		// Load template
		include(BLUPATH_TEMPLATES.'/slideshows/view.php');
	}
	
	public function slideshow() {
		// Get models
		$slideshowsModel = BluApplication::getModel('slideshows');
		
		$slideshowId = Request::getInt('slideshowId');
		
		if($slideshowId) {
			$slideshow = $slideshowsModel->getSlideshow($slideshowId);
		}
		
		// Load template
		include(BLUPATH_TEMPLATES.'/slideshows/slideshow.php');
	}
	
	public function submit_slideshow() {
		
		if(Request::getBool('cancel')) {
			return $this->_redirect('/slideshows');
		}
		
		// Get models
		$slideshowsModel = BluApplication::getModel('slideshows');
		
		$slideshowId = Request::getInt('slideshowId');
		$title = Request::getString('title');
		$body = Request::getString('body');
		$image = Request::getVar('image', null, 'files');
		
		$error = false;
		
		if(empty($title)) {
			Messages::addMessage('Title cannot be empty','error');
			$error = true;
		}
		if(empty($body)) {
			Messages::addMessage('Body cannot be empty','error');
			$error = true;
		}
		
		$allowed_extensions = array('jpg','gif','png');
		
		if($image['tmp_name']) {
			
			$fileExtension = Utility::getFileExtension($image['name']);
			$filename = basename($image['name'],'.'.$fileExtension).'_'.uniqid().'.'.$fileExtension;
			$path = BLUPATH_ASSETS.'/slideshowimages/';
			$uploadPath = $path.$filename;
			
			if(!in_array($fileExtension,$allowed_extensions)) {
				Messages::addMessage('Allowed file extensions are: '.implode(', ',$allowed_extensions),'error');
				$error = true;
			}
			elseif(!Upload::isValid($image)) {
				Messages::addMessage(Text::get('global_msg_upload_error'),'error');
				$error = true;
			}
			
		}
		
		if($error) {
			Template::set('title',$title);
			Template::set('body',$body);
			$this->slideshow();
			return false;
		}
		
		if($image['tmp_name']) {
			if(!file_exists($path)) {
				mkdir($path, 0777);
			}
			if(!move_uploaded_file($image['tmp_name'], $uploadPath)) {
				Messages::addMessage('Image could not be uploaded.','error');
			}
		}
		
		if($slideshowId) {
			if($slideshowsModel->updateSlideshow($slideshowId, $title, $filename,$body)) {
				Messages::addMessage('Slideshow updated successfully', 'info');
			}
		}
		else {
			if($slideshowsModel->addSlideshow($title, $filename,$body)) {
				Messages::addMessage('Slideshow added successfully', 'info');
			}
		}
		
		return $this->_redirect('/slideshows');
	}
	
	public function delete_slideshow() {
		// Get models
		$slideshowsModel = BluApplication::getModel('slideshows');
		
		$slideshowId = Request::getInt('slideshowId');
		
		if($slideshowsModel->deleteSlideshow($slideshowId)) {
			Messages::addMessage('Slideshow deleted successfully', 'info');
		}
		
		return $this->_redirect('/slideshows');
	}
	
	public function set_live() {
		// Get models
		$slideshowsModel = BluApplication::getModel('slideshows');
		
		$slideshowId = Request::getInt('slideshowId');
		
		if($slideshowsModel->setSlideshowStatus($slideshowId, 1)) {
			Messages::addMessage('Slideshow set live successfully', 'info');
		}
		
		return $this->_redirect('/slideshows');
	}
	
	public function unset_live() {
		// Get models
		$slideshowsModel = BluApplication::getModel('slideshows');
		
		$slideshowId = Request::getInt('slideshowId');
		
		if($slideshowsModel->setSlideshowStatus($slideshowId, 0)) {
			Messages::addMessage('Slideshow set offline successfully', 'info');
		}
		
		return $this->_redirect('/slideshows');
	}
	
	public function set_featured() {
		// Get models
		$slideshowsModel = BluApplication::getModel('slideshows');
		
		$slideshowId = Request::getInt('slideshowId');
		
		if($slideshowsModel->setSlideshowFeaturedStatus($slideshowId, 1)) {
			Messages::addMessage('Slideshow set featured successfully', 'info');
		}
		
		return $this->_redirect('/slideshows');
	}
	
	public function unset_featured() {
		// Get models
		$slideshowsModel = BluApplication::getModel('slideshows');
		
		$slideshowId = Request::getInt('slideshowId');
		
		if($slideshowsModel->setSlideshowFeaturedStatus($slideshowId, 0)) {
			Messages::addMessage('Slideshow unset featured successfully', 'info');
		}
		
		return $this->_redirect('/slideshows');
	}
	
	public function delete_image() {
		// Get models
		$slideshowsModel = BluApplication::getModel('slideshows');
		
		$slideshowId = Request::getInt('slideshowId');
		$slideshow = $slideshowsModel->getSlideshow($slideshowId);
		if(!$slideshow) {
			return false;
		}
		
		$assetPath = BLUPATH_ASSETS.'/slideshowimages/'.$slideshow['filename'];
		if($slideshowsModel->deleteImage($slideshowId) && unlink($assetPath)) {
			Messages::addMessage('Image deleted successfully', 'info');
		}
		
		return $this->_redirect('/slideshows/slideshow?slideshowId='.$slideshowId);
	}
	
	public function move_slideshow() {
		// Get models
		$slideshowsModel = BluApplication::getModel('slideshows');
		
		$slideshowId = Request::getString('slideshowId');
		$move = Request::getString('move');
		$slideshow = $slideshowsModel->getSlideshow($slideshowId);
		if(!$slideshow) {
			return $this->_redirect('/slideshows');
		}
		
		if(!$slideshowsModel->moveSlideshow($slideshowId, $move)) {
			Messages::addMessage('Slideshow could not be upadated.', 'error');
		}
		
		return $this->_redirect('/slideshows');
	}
	
	public function slideshowItems()
	{
		// Get models
		$slideshowsModel = BluApplication::getModel('slideshows');
		
		$slideshowId = Request::getInt('slideshowId');
		
		$slideshow = $slideshowsModel->getSlideshow($slideshowId);
		$slideshowItems = $slideshowsModel->getSlideshowItems($slideshowId);
		
		// Load template
		include(BLUPATH_TEMPLATES.'/slideshows/slideshow_items.php');
	}
	
	public function slideshowItem() {
		// Get models
		$slideshowsModel = BluApplication::getModel('slideshows');
		
		if($itemId = Request::getInt('itemId')) {
			$item = $slideshowsModel->getSlideshowItem($itemId);
			if(!$item) {
				return $this->_redirect('/slideshows');
			}
			$slideshowId = $item['slideshowId'];
		}
		else {
			$slideshowId = Request::getInt('slideshowId');
		}
		$slideshow = $slideshowsModel->getSlideshow($slideshowId);
		if(!$slideshow) {
			return $this->_redirect('/slideshows');
		}
		
		Template::set('tinyMce', true);
		
		// Load template
		include(BLUPATH_TEMPLATES.'/slideshows/slideshow_item.php');
	}
	
	public function submit_slideshow_item() {
		
		// Get models
		$slideshowsModel = BluApplication::getModel('slideshows');
		
		$itemId = Request::getInt('itemId');
		$title = Request::getString('title');
		$body = Request::getString('body', null, null, true);
		$image = Request::getVar('image', null, 'files');
		
		if($itemId) {
			$item = $slideshowsModel->getSlideshowItem($itemId);
			if(!$item) {
				return $this->_redirect('/slideshows');
			}
			$slideshowId = $item['slideshowId'];
		}
		else {
			$slideshowId = Request::getInt('slideshowId');
		}
		$redirectUrl = '/slideshows/slideshowitems?slideshowId='.$slideshowId;
		
		if(Request::getBool('cancel')) {
			return $this->_redirect($redirectUrl);
		}
		
		$error = false;
		
		if(empty($title)) {
			Messages::addMessage('Title cannot be empty', 'error');
			$error = true;
		}
		
		if(empty($body)) {
			Messages::addMessage('Body cannot be empty', 'error');
			$error = true;
		}
		
		$allowed_extensions = array('jpg','gif','png');
		
		if($image['tmp_name']) {
			
			$fileExtension = Utility::getFileExtension($image['name']);
			$filename = basename($image['name'],'.'.$fileExtension).'_'.uniqid().'.'.$fileExtension;
			$path = BLUPATH_ASSETS.'/slideshowimages/';
			$uploadPath = $path.$filename;
			
			if(!in_array($fileExtension,$allowed_extensions)) {
				Messages::addMessage('Allowed file extensions are: '.implode(', ',$allowed_extensions),'error');
				$error = true;
			}
			elseif(!Upload::isValid($image)) {
				Messages::addMessage(Text::get('global_msg_upload_error'),'error');
				$error = true;
			}
			
		}
		
		if($error) {
			Template::set('title',$title);
			Template::set('body',$body);
			$this->slideshowitem();
			return false;
		}
		
		if($image['tmp_name']) {
			if(!file_exists($path)) {
				mkdir($path, 0777);
			}
			if(!move_uploaded_file($image['tmp_name'], $uploadPath)) {
				Messages::addMessage('Image could not be uploaded.','error');
			}
		}
		
		if($itemId) {
			$item = $slideshowsModel->getSlideshowItem($itemId);
			if(!$item) {
				return $this->_redirect('/slideshows');
			}
			$slideshowId = $item['slideshowId'];
			if($slideshowsModel->updateSlideshowItem($itemId, $title, $body, $filename)) {
				Messages::addMessage('Slideshow item updated successfully', 'info');
			}
		}
		else {
			$slideshowId = Request::getInt('slideshowId');
			if($slideshowsModel->addSlideshowItem($slideshowId, $title, $body, $filename)) {
				Messages::addMessage('Slideshow item added successfully', 'info');
			}
		}
		
		return $this->_redirect($redirectUrl);
	}
	
	public function delete_slideshow_item() {
		// Get models
		$slideshowsModel = BluApplication::getModel('slideshows');
		
		$itemId = Request::getInt('itemId');
		$item = $slideshowsModel->getSlideshowItem($itemId);
		if(!$item) {
			return $this->_redirect('/slideshows');
		}
		
		if($slideshowsModel->deleteSlideshowItem($itemId)) {
			Messages::addMessage('Slideshow item deleted successfully', 'info');
		}
		
		return $this->_redirect('/slideshows/slideshowitems?slideshowId='.$item['slideshowId']);
	}
	
	public function delete_item_image() {
		// Get models
		$slideshowsModel = BluApplication::getModel('slideshows');
		
		$itemId = Request::getInt('itemId');
		
		if($slideshowsModel->deleteItemImage($itemId)) {
			Messages::addMessage('Image deleted successfully', 'info');
		}
		
		return $this->_redirect('/slideshows/slideshowitem?itemId='.$itemId);
	}
	
	public function move_slideshow_item() {
		// Get models
		$slideshowsModel = BluApplication::getModel('slideshows');
		
		$itemId = Request::getString('itemId');
		$move = Request::getString('move');
		$item = $slideshowsModel->getSlideshowItem($itemId);
		if(!$item) {
			return $this->_redirect('/slideshows');
		}
		
		if(!$slideshowsModel->moveSlideshowItem($itemId, $move)) {
			Messages::addMessage('Slideshow item could not be upadated.', 'error');
		}
		
		return $this->_redirect('/slideshows/slideshowitems?slideshowId='.$item['slideshowId']);
	}

}

?>
