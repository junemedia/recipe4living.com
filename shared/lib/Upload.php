<?php

/**
 * Upload Object
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class Upload
{
	/**
	 * Check if a file is correctly uploaded and valid
	 *
	 * @param array File details
	 * @return bool True if succesfully uploaded, false otherwise
	 */
	public static function isValid($file)
	{
		return (is_uploaded_file($file['tmp_name']) && ($file['error'] == UPLOAD_ERR_OK));
	}

	/**
	 * Save an uploaded file for later access
	 *
	 * @param string Upload queue ID
	 * @param array File details
	 * @return string Unique upload ID
	 */
	public static function saveToQueue($queueId, $file, $fieldName = null)
	{
		// Generate unique upload ID
		$uploadId = uniqid();

		// Determine path to temporary upload location
		$uploadDir = BLUPATH_BASE.'/uploads';
		$uploadPath = $uploadDir.'/'.$uploadId;

		// Create upload directory if it doesn't exist
		if (!file_exists($uploadDir)) {
			mkdir($uploadDir, 0777, true);
		}

		// Move file to temporary location
		copy($file['tmp_name'], $uploadPath);

		// Clean filename
		$file['dirty'] = $file['name'];
		$file['name'] = self::obfuscate($file['name']);
		if($fieldName) {
			$file['field_name'] = $fieldName;
		}

		// Add to upload session upload queue
		$_SESSION['uploads'][$queueId][$uploadId] = $file;		
		
		return $uploadId;
	}

	/**
	 * Get upload queue files
	 *
	 * @param string Upload queue ID
	 * @return array Array of files in the queue
	 */
	public static function getQueue($queueId)
	{
		return isset($_SESSION['uploads'][$queueId]) ? $_SESSION['uploads'][$queueId] : false;
	}

	/**
	 * Clear upload queue
	 *
	 * @param string Upload queue ID
	 */
	public static function clearQueue($queueId)
	{
		unset($_SESSION['uploads'][$queueId]);
	}

	/**
	 * Move uploaded file to storage location
	 *
	 * @param string Upload ID
	 * @param string Destination path
	 * @return bool True on success, false otherwise
	 */
	public static function move($uploadId, $destPath)
	{
		// Create destination directory if it doesn't exist
		$destDir = dirname($destPath);
		if (!file_exists($destDir)) {
			mkdir($destDir, 0777, true);
		}

		// Determine path to temporary upload location
		$uploadDir = BLUPATH_BASE.'/uploads';
		$uploadPath = $uploadDir.'/'.$uploadId;

		// Move uploaded file into place
		return rename($uploadPath, $destPath);
	}
	
	/**
	 *	Obfuscate an upload's filename to avoid .htaccess exceptions.
	 *
	 *	@param string filename (not incl. directory names)
	 *	@return string .htaccess-friendly filename.
	 */
	public static function obfuscate($filename){
		
		/* .htaccess fixes */
		$fixes = array(
			'/files/' => 'f1l3s',
			'/wp-admin/' => 'wq-adm1n',
			'/^(.*)\.xml$/' => '$1.xxm1',
			'/^(.*)wp-comments-post\.php$/' => '$1wq-c0mm3nt5-p05t.php'
		);
		
		/* Replace */
		$filename = preg_replace(array_keys($fixes), $fixes, $filename);
		
		/* Return */
		return $filename;
		
	}
	
	/**
	 *	Hash an uploaded file's filename and move directly.
	 *
	 *	@access public
	 *	@param array File
	 *	@param string Upload directory
	 *	@return string Hashed filename.
	 */
	public static function saveFile($file, $uploadDir)
	{
		// Get destination path
		$hashedName = self::hashFilename($file['name']);
		$uploadPath = rtrim($uploadDir, '/').'/'.$hashedName;
		
		// Move file
		if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
			return false;
		}
		
		// Return saved name
		return $hashedName;
	}
	
	/**
	 *	Append microtime to the filename
	 *
	 *	Ripped out of tstoreadmin.
	 *
	 *	@static
	 *	@access public
	 *	@param string Filename
	 *	@return string Hashed filename
	 */
	public static function hashFilename($path)
	{
		$bits = pathinfo($path);
		$filename = strtolower($bits['filename']);
		$filename .= '_'.md5(time());
		$filename = substr($filename, 0, 128);
		$extension = $bits['extension'];
		return $filename.'.'.$extension;
	}
}

?>
