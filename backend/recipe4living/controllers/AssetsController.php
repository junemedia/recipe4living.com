<?php

/**
 *	Assets controller
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class Recipe4livingAssetsController extends ClientBackendController
{
	
	protected $_menuSlug = 'images';

	private $folder;
	
	/**
	 * Base url
	 *
	 * @var string Base url
	 */
	protected $_baseUrl = '/assets';

	public function view()
	{
		// Get models
		$assetsModel = BluApplication::getModel('assets');
		
		$page = Request::getInt('page', 1);
		$sort = Request::getString('sort', 'article_asc');
		
		// Clear search
		if(Request::getBool('clear')) {
			return $this->_redirect($this->_baseUrl);
		}
		
		// Set search parameters
		$urlArgsArray = array();
		if($filename = Request::getString('filename')) {
			$urlArgsArray['filename'] = $filename;
		}
		if($title = Request::getString('title')) {
			$urlArgsArray['title'] = $title;
		}
		if($articleTitle = Request::getString('article_title')) {
			$urlArgsArray['article_title'] = $articleTitle;
		}
		if ($this->_doc->getFormat() != 'site') {
			$urlArgsArray['format'] = $this->_doc->getFormat();
		}
		
		$baseUrl = $this->_baseUrl;
		
		$sortPageUrl = SITEURL . $baseUrl . '?' . http_build_query($urlArgsArray);
		$urlArgsArray['sort'] = $sort;
		
		$filterArray = $urlArgsArray;
		$urlArgsArray['page'] = '';
		$paginationUrl = SITEURL . $baseUrl . '?' . http_build_query($urlArgsArray);
		$urlArgsArray['page'] = $page;
		$pageUrl = SITEURL . $baseUrl . '?' . http_build_query($urlArgsArray);
		
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$total = null;
		$articleImages = $assetsModel->getArticleImages($offset, $limit, $total, $sort, $filterArray);
		
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => $paginationUrl
		));
		
		Session::set('articleImageListDisplayArgs', $urlArgsArray);
		
		// Load template
		include(BLUPATH_TEMPLATES.'/assets/view_images.php');
	}

	public function imageDetails()
	{
		// Get models
		$assetsModel = BluApplication::getModel('assets');
		$itemsModel = BluApplication::getModel('items');
		
		$filename = Request::getString('filename');
		$articleId = Request::getInt('articleId');
		
		$articleImage = $assetsModel->getArticleImageDetails($filename, $articleId);
		if(!$articleImage) {
			$this->_redirect($this->_baseUrl);
		}
		
		if($articleImage['articleId']) {
			$articleImage['item'] = $itemsModel->getItem($articleImage['articleId']);
		}
		
		// Back button
		$urlArgsArray = Session::get('articleImageListDisplayArgs');
		$backButtonUrl = SITEURL . $this->_baseUrl . '?' . http_build_query($urlArgsArray);
		
		// Load template
		include(BLUPATH_TEMPLATES.'/assets/image_details.php');
	}

	public function deleteImage()
	{
		// Get models
		$assetsModel = BluApplication::getModel('assets');
		
		$filename = Request::getString('filename');
		$articleId = Request::getInt('articleId');
		
		$result = $assetsModel->deleteArticleImage($filename, $articleId);
		
		$this->_redirect('/assets/view');
	}

	public function pdf() {
		
		$this->_menuSlug = 'files';
		$this->_setTopNav();
		
		$this->_baseUrl = '/assets/pdf';
		
		// Get models
		$assetsModel = BluApplication::getModel('assets');
		
		$sortField = Request::getString('sort_field', 'filename');
		$sortAsc = Request::getString('sort_asc', 'true');
		
		// Set search parameters
		$urlArgsArray = array();
		if($path = Request::getString('path')) {
			$urlArgsArray['path'] = $path;
		}
		if($filename = Request::getString('filename')) {
			$urlArgsArray['filename'] = $filename;
		}
		
		$baseUrl = $this->_baseUrl;
		
		$url_query_str = http_build_query($urlArgsArray);
		$pageUrl = SITEURL . $baseUrl . '?' . $url_query_str . ($url_query_str ? '&amp;' : '');
		$urlArgsArray['sort_field'] = $sortField;
		$urlArgsArray['sort_asc'] = $sortField;
		
		$baseFolder = '/pdf/';
		if($path) {
			$folder = $path.'/';
		}
		else {
			$folder = $baseFolder;
		}
		$dir = realpath(BLUPATH_ASSETS.$folder).'/';
		$folder = str_replace(BLUPATH_ASSETS,'',$dir);
		if(strpos($folder,$baseFolder)!==0) {
			$folder = $baseFolder;
			$dir = realpath(BLUPATH_ASSETS.$baseFolder).'/';
		}
		
		// Clear search
		if(Request::getBool('clear')) {
			return $this->_redirect($this->_baseUrl.'?path='.urlencode($folder));
		}
		
		$files = scandir($dir);
		
		// filter files
		$dirFiles = $assetsModel->getFileDetails($files,$dir,$folder,$baseFolder,$filename);
		
		// filter files
		$dirFiles = $assetsModel->sortFiles($dirFiles,$sortField,$sortAsc);

		Session::set('fileListDisplayArgs', $urlArgsArray);
		
		// Load template
		include(BLUPATH_TEMPLATES.'/assets/view_files.php');
	}
	
	public function uploadFile() {
		
		$allowed_extensions = array('pdf','doc');
		
		$this->_baseUrl = '/assets/pdf';
		$baseFolder = '/pdf/';
		
		$path = Request::getString('path');
		
		if($path) {
			$folder = $path.'/';
		}
		else {
			$folder = $baseFolder;
		}
		$dir = realpath(BLUPATH_ASSETS.$folder).'/';
		$folder = str_replace(BLUPATH_ASSETS,'',$dir);
		if(strpos($folder,$baseFolder)!==0) {
			$folder = $baseFolder;
			$dir = realpath(BLUPATH_ASSETS.$baseFolder).'/';
		}
		
		$file = Request::getVar('file', null, 'files');
		$uploadPath = $dir.$file['name'];
		$fileExtension = Utility::getFileExtension($file['name']);
		
		$error = false;
		
		if(is_file($uploadPath) && file_exists($uploadPath)) {
			$error = 'File <i>'.$file['name'].'</i> already exists! Please delete this file first.';
		}
		elseif(!in_array($fileExtension,$allowed_extensions)) {
			$error = 'Allowed file extensions are: '.implode(', ',$allowed_extensions);
		}
		
		if (!$error && $file['tmp_name']) {
		
			// Check file uploaded correctly
			if (!Upload::isValid($file)) {
				$error = Text::get('global_msg_upload_error');
			}
			
			if (!$error) {
			
				// Move file to temporary location
				if(move_uploaded_file($file['tmp_name'], $uploadPath)) {
					Messages::addMessage('File uploaded successfully.', 'info');
					$this->_redirect($this->_baseUrl.'?path='.urlencode($folder));
				}
				else {
					$error = 'File could not be uploaded.';
				}
			
			}
		
		}
		
		if($error) {
			Messages::addMessage($error, 'error');
			$this->_redirect($this->_baseUrl.'?path='.urlencode($folder));
		}
		
	}
	
	public function deleteFile() {
		
		$this->_baseUrl = '/assets/pdf';
		$baseFolder = '/pdf/';
		
		$path = Request::getString('path');
		
		if($path) {
			$folder = $path.'/';
		}
		else {
			$folder = $baseFolder;
		}
		$dir = realpath(BLUPATH_ASSETS.$folder).'/';
		$folder = str_replace(BLUPATH_ASSETS,'',$dir);
		if(strpos($folder,$baseFolder)!==0) {
			$folder = $baseFolder;
			$dir = realpath(BLUPATH_ASSETS.$baseFolder).'/';
		}
		
		$filename = Request::getString('filename');
		
		if(file_exists($dir.$filename) && is_file($dir.$filename) && unlink($dir.$filename)) {
			Messages::addMessage('File deleted successfully.', 'info');
		}
		else {
			Messages::addMessage('File could not be deleted.', 'info');
		}
		
		$this->_redirect($this->_baseUrl.'?path='.urlencode($folder));
		
	}

}

?>
