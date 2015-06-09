<?php

/**
 * Assets Controller
 *
 * @package BluApplication
 * @subpackage SharedControllers
 */
class AssetsController extends ClientFrontendController
{
	/**
	 *	Constructor.
	 *
	 *	@access public
	 *	@param array Arguments
	 */
	public function __construct($args)
	{
		parent::__construct($args);
		
		// Set document format
		$this->_doc->setFormat('asset');
	}
	
	/**
	 * View asset
	 */
	public function view()
	{
		$args = $this->_args;

		// Asset type is first argument
		$assetType = array_shift($args);

		// Cropping and sizing params are in the middle
		$width = array_shift($args);
		$height = array_shift($args);
		$zoomCrop = array_shift($args);

		// Image path is remaining args
		$sourceImage = implode('/', $args);
		$sourceImage = urldecode($sourceImage);

		// Create image and serve
		$image = new ImageAsset($assetType, $sourceImage);
		$image->serve($width, $height, $zoomCrop);
	}

	/**
	 * Video asset
	 */
	public function video()
	{
		// Create file and serve
		$file = new FileAsset('video', array_shift($this->_args));
		$file->serve();
	}

	/**
	 *	PDF files
	 *
	 *	@access public
	 */
	public function pdf()
	{
		// Get requested file name
		$sourceFile = implode('/', $this->_args);
		$sourceFile = urldecode($sourceFile);

		// Spawn object
		$file = new FileAsset('pdf', $sourceFile);
		$file->serve();
	}

	/**
	 *	Upload temporary images.
	 */
	public function upload()
	{
		/* Get queue ID */
		$queueId = Request::getString('queueid', md5(uniqid()));

		/* Get previous uploads */
		$uploads = Request::getArray('uploads',array());
		foreach ($uploads as $key => &$url){
			if (!$url){
				Utility::array_pop($uploads, $key);
				continue;
			}
			$filename = explode('/', $url);
			$filename = array_pop($filename);
			$url = array(
				'dir'	=>	ASSETURL . '/tempimages',
				'file'	=>	$filename
			);
		}

		/* Determine upload type. */
		$defaultUploadType = 'temp';
		$type = Request::getString('type', $defaultUploadType);
		if (!in_array($type, array('item', 'slideshow', 'user'))){
			$type = $defaultUploadType;
		}
		
		/* Get new data from request */
		$file = Request::getVar('fileupload', null, 'files');
		if ($file['tmp_name']) {
			$error = false;

			// Check file uploaded correctly
			if (!Upload::isValid($file)) {
				$error = Text::get('global_msg_upload_error');
			}

			if ($error){
				$result['result'] = 'failed';
				$result['error'] = $error;
			} else {
				$result['result'] = 'success';
				$result['size'] = '';

				if($sizeInfo = getimagesize($file['tmp_name'])) {
					$file['image_info'] = array('width'=>$sizeInfo[0],'height'=>$sizeInfo[1]);
				}

				$filename = md5(microtime()) . '_' . $file['name'];
				$file['file_name'] = $filename;
				
				// Save upload to queue
				$uploadId = Upload::saveToQueue($queueId, $file);

				// Save with specified directory and filename.
				$imagePath = BLUPATH_ASSETS.'/'.$type.'images/'.$filename;
				Upload::move($uploadId, $imagePath);

				// Generate URL parts for image.
				$uploads[] = array(
					'dir'	=>	ASSETURL.'/'.$type.'images',
					'file'	=>	$filename
				);

			}
		} else {
			$result['result'] = 'empty';
		}
		
		if($uploadedImages = Session::get('uploads')) {
			$uploadedImages = array_reverse($uploadedImages);
		}
		
		/* Load uploader template. */
		$this->_doc->setFormat('simple');
		include(BLUPATH_BASE_TEMPLATES . '/assets/upload.php');
	}
	
	/**
	 *	Obfuscate an uploaded file's filename.
	 */
	public function obfuscate(){
		
		/* Get argument */
		$filename = Request::getString('filename');
		
		/* Obfuscate */
		$filename = Upload::obfuscate($filename);
		
		/* Output */
		$this->_doc->setFormat('raw');
		echo $filename;
		
	}
	
}

?>
