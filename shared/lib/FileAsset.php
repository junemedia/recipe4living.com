<?php

/**
 * File Asset Object
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class FileAsset
{
	/**
	 * Requested file
	 *
	 * @var string
	 */
	protected $_srcFile;

	/**
	 * Source file path
	 *
	 * @var string
	 */
	protected $_srcPath;

	/**
	 * Requested asset type
	 *
	 * @var string
	 */
	protected $_assetType;

	/**
	 * Image constructor
	 *
	 * @param string Asset type
	 * @param string Source file path
	 */
	public function __construct($assetType, $srcFile)
	{
		// Clean requested file name
		$srcFile = $this->_cleanSource($srcFile);

		// Store source image data
		$this->_assetType = $assetType;
		$this->_srcFile = $srcFile;
		$this->_srcPath = BLUPATH_ASSETS.'/'.$assetType.'/'.$srcFile;
	}

	/**
	 * Serve the requested file
	 */
	public function serve()
	{
		return $this->_serveFile($this->_srcPath);
	}

	/**
	 * Clean source file name
	 *
	 * @param string Source file name
	 * @return string Cleaned file name
	 */
	protected function _cleanSource($srcFile)
	{
		return Utility::cleanFilename($srcFile);
	}

	/**
	 * Serve the given file to the browser
	 *
	 * @param string File path
	 */
	protected function _serveFile($fileToServe)
	{
		// Get document
		$doc = BluApplication::getDocument();

		// Check file exists, and fall back to 404
		if (!is_file($fileToServe)) {
			$fileToServe = BLUPATH_ASSETS.'/404.htm';
		}

		// Get modified time
		$fileToServeTime = filemtime($fileToServe);

		// Get ETag
		$eTag = Utility::makeEtag($fileToServe, $fileToServeTime);

		// Browser has sent up-to-date ETag?
		if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
			$ifNoneMatch = str_replace('"', '', $_SERVER['HTTP_IF_NONE_MATCH']);
			if ($ifNoneMatch == $eTag) {
				$doc->setStatus('HTTP/1.1 304 Not Modified');
				return;
			}
		}

		// Browser has sent up-to-date modification stamp?
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			$ifModifiedSince = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
			$gmdateMod = gmdate('D, d M Y H:i:s', $fileToServeTime);
			if (strstr($ifModifiedSince, 'GMT')) {
				$gmdateMod .= ' GMT';
			}
			if ($ifModifiedSince == $gmdateMod) {
				$doc->setStatus('HTTP/1.1 304 Not Modified');
				return;
			}
		}

		// Set headers then display image
		$doc->setMimeType(Utility::getMimeType($fileToServe));
		$doc->setModifiedDate(filemtime($fileToServe));
		$doc->setETag($eTag);
		$doc->setMaxAge(864000);

		// Read file from disk
		$fh = fopen($fileToServe, 'rb');
		fpassthru($fh);
		fclose($fh);
	}
}
?>
