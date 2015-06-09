<?php

/**
 * Utilities Library
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class Utility
{
	const SORT_ASC = 1;
	const SORT_DESC = 2;
	
	const VALID_URL = '(((http|https|ftp|rmtp|mms):\/\/)?(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?([A-Z0-9-_\/]*)(\?([A-Z0-9-_&=;]*))?)';
	const LAZY_URL = '(http:\/\/)?([A-Za-z0-9-_.]+\.(com|net|org|co\.uk|biz|edu)|www\.[A-Za-z0-9-_.]+)';

	/**
	 * Check if URL is valid
	 *
	 * @param string URL
	 * @return bool True if valid, false otherwise
	 */
	public static function isValidURL($url, $allowNonHTTP = false)
	{
		// Allow non http urls?
		if ($allowNonHTTP) {
			$protocol = 'http|https|ftp|rmtp|mms';
		} else {
			$protcol = 'http|https';
		}

		return preg_match('/^'.self::VALID_URL.'/i', $url);
	}

	/**
	 * Array quick-sort
	 *
	 * @param array Array to sort
	 * @param mixed Index on which to sort
	 * @param int Sort direction
	 * @param int Use natural sort?
	 * @param int Case sensitive natural sort?
	 */
	public static function quickSort($data, $column, $order = self::SORT_ASC, $natsort = false, $case_sensitive = false)
	{
		// Kill off the rejects
		if (empty($data) || !is_array($data)) {
			return $data;
		}
		
		// Get sorting index
		foreach ($data as $key => $value) {
			$index[$key] = $value[$column];
		}
		
		// Sort the index
		if (!$natsort) {
			($order == self::SORT_ASC) ? asort($index) : arsort($index);
		} else {
			($case_sensitive) ? natsort($index) : natcasesort($index);
			if ($order != self::SORT_ASC) {
				$index = array_reverse($index, true);
			}
		}
		
		// Merge back the full data
		$sorted = array();
		foreach ($index as $key => $value) {
			//(is_numeric($key))? $sorted[]=$data[$key] :
			$sorted[$key] = $data[$key];
		}
		
		// Return
		return $sorted;
	}
	
	/**
	 *	Quicksearch
	 *
	 *	@static
	 *	@access public
	 *	@param string Search term
	 *	@param array 2D array of text (ordered by priority) to search
	 *	@param array Extra callbacks to array_filter search terms by
	 *	@return array Keys of the matching text
	 */
	public static function quickSearch($search, array $things, array $filters = array())
	{
		// Build greppable strings
		foreach ($things as &$thing) {
			$thing = ' '.implode('  ', $thing);	// Two spaces.
		}
		unset($thing);
		
		// Prepare search terms
		$terms = preg_split('/[^A-Za-z0-9]+/', $search);
		$terms = array_filter($terms);
		$terms = array_unique($terms);
		foreach ($filters as $filter) {
			$terms = array_filter($terms, $filter);
		}
		
		// Start grepping
		$grepResults = $things;
		foreach ($terms as $term) {
			$grepResults = preg_grep('/'.$term.'/i', $grepResults);
		}
		
		// Build search matches
		$searchResults = array();
		foreach ($terms as $term) {
			$term = trim($term);
			foreach ($grepResults as $key => $match) {
				$match = preg_replace('/[^a-zA-Z0-9]/', ' ', $match); //special characters e.g '_','-',',' etc were problem			
				// Get position of search term within grep string
				$index = stripos($match, ' '.$term);	// One space.
				if ($index === false) {
					
					// Ignore this grep result
					unset($grepResults[$key]);
					
					// Ignore this item
					unset($searchResults[$key]);
					
				// Pull out item and assign relevance value
				} else {
					if (!isset($searchResults[$key])) {
						$searchResults[$key] = 0;
					}
					$searchResults[$key] = ($index + $searchResults[$key]) / 2;
				}
			}
		}
		
		// Sort by relevance
		asort($searchResults);
		
		// Keys only
		if (!empty($searchResults)) {
			$keys = array_keys($searchResults);
			$searchResults = array_combine($keys, $keys);
		}
		
		// Return
		return $searchResults;
	}
	
	/**
	 * cURL wrapper with common options (for plugins/payment providers etc.)
	 *
	 * @param string URL to post to
	 * @param string Data (post fields, XML, etc.)
	 * @param string The name of a file containing a PEM formatted certificate
	 * @param bool The maximum number of seconds to allow cURL functions to execute
	 * @param array External header to go with curl
	 * @return string cURL response on success, false otherwise
	 */
	public static function curl($url, $data = null, $cert = '', $timeout = 120, $header = null, $auth = null, $method = null, &$info = null, &$error = null)
	{
		// Init cURL
		if (!$ch = curl_init()) {
			Messages::addMessage('cURL package not installed in PHP. Set curlPath setting in config', 'error');
			return false;
		}

		// Default to POST if data given, GET otherwise
		if (!$method) {
			$method = $data ? 'POST' : 'GET';
		}

		// Add data
		switch ($method) {
			case 'POST':

				// Set post fields
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				break;

			case 'PUT':

				/*// Write data to temp file
				$putFile = tmpfile();
				fwrite($putFile, $data);
				fseek($putFile, 0);

				// Set file to put
				curl_setopt($ch, CURLOPT_PUT, 1);
				curl_setopt($ch, CURLOPT_INFILE, $putFile);
				curl_setopt($ch, CURLOPT_INFILESIZE, strlen($data));*/

				// Put via post fields
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				break;

			case 'GET':

				// Append data to url as query string if we have any
				if ($data) {
					$url .= '?'.$data;
				}
				break;

			default:

				// Set custom request type
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
				break;
		}

		// Set request URL
		curl_setopt($ch, CURLOPT_URL, $url);

		// Add SSL certificate
		if ($cert) {
			curl_setopt($ch, CURLOPT_SSLCERT, $cert);
		}

		// Don't do SSL verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		
		// Fake user agent, because some sites test for legitimacy
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)'); 

		// Add http auth creds
		if ($auth) {
			if (is_array($auth)) {
				
				// Determine authentication method
				switch ($auth['method']) {
					case 'basic':
						$auth['method'] = CURLAUTH_BASIC;
						break;
						
					case 'digest':
					default:
						$auth['method'] = CURLAUTH_DIGEST;
						break;
				}
				
				curl_setopt($ch, CURLOPT_HTTPAUTH, $auth['method']);
				curl_setopt($ch, CURLOPT_USERPWD, $auth['username'].':'.$auth['password']);
			} else {
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
				curl_setopt($ch, CURLOPT_USERPWD, $auth);
			}
		}

		// Customer header fields
		if ($header) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}

		// Set transfer and return options
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if ($timeout) {
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		}

		// Debug?
		/*if (DEBUG) {
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
			$stdErr = tmpfile();
			curl_setopt($ch, CURLOPT_STDERR, $stdErr);
		}*/

		// Post
		$res = curl_exec($ch);
                
		// Output standard error
		/*if (DEBUG) {
			fseek($stdErr, 0);
			while ($str = fgets($stdErr)) {
				echo $str;
			}
		}*/

		// Check for errors
		if ($error = curl_error($ch)) {
			if (($cert) && !file_exists($cert)) {
				Messages::addMessage('Certificate file not found: '.$cert, 'error');
			} else {
				Messages::addMessage('cURL error: '.$error, 'error');
			}
			return false;
		}

		// Get info, response headers
		$info = curl_getinfo($ch);

		// Close cURL
		curl_close($ch);

		return $res;
	}

	/**
	 * Make an ETag for a given file name and timestamp
	 *
	 * @param string File name
	 * @param int Modification timestamp
	 * @return string Etag
	 */
	public static function makeETag($fileName, $mTime) {
		return (md5($fileName.$mTime));
	}

	/**
	 * Get a mime type based on the file name
	 *
	 * @param string File name
	 * @return string MIME type
	 */
	public static function getMimeType($filename)
	{
		preg_match('|\.([a-z0-9]{2,4})$|i', $filename, $fileSuffix);

		switch(strtolower($fileSuffix[1]))
		{
			case 'js':
				return 'application/x-javascript';

			case 'json':
				return 'application/json';

			case 'jpg':
			case 'jpeg':
			case 'jpe':
				return 'image/jpg';

			case 'png':
			case 'gif':
			case 'bmp':
			case 'tiff':
				return 'image/'.strtolower($fileSuffix[1]);

			case 'css':
				return 'text/css';

			case 'xml':
				return 'application/xml';

			case 'doc':
			case 'docx':
				return 'application/msword';

			case 'xls':
			case 'xlt':
			case 'xlm':
			case 'xld':
			case 'xla':
			case 'xlc':
			case 'xlw':
			case 'xll':
				return 'application/vnd.ms-excel';

			case 'ppt':
			case 'pps':
				return 'application/vnd.ms-powerpoint';

			case 'rtf':
				return 'application/rtf';

			case 'pdf':
				return 'application/pdf';

			case 'html':
			case 'htm':
			case 'php':
				return 'text/html';

			case 'txt':
				return 'text/plain';

			case 'mpeg':
			case 'mpg':
			case 'mpe':
				return 'video/mpeg';

			case 'mp3':
				return 'audio/mpeg3';

			case 'wav':
				return 'audio/wav';

			case 'aiff':
			case 'aif':
				return 'audio/aiff';

			case 'avi':
				return 'video/msvideo';

			case 'wmv':
				return 'video/x-ms-wmv';

			case 'mov':
				return 'video/quicktime';

			case 'zip':
				return 'application/zip';

			case 'tar':
				return 'application/x-tar';

			case 'swf':
				return 'application/x-shockwave-flash';

			default:
				if (function_exists('mime_content_type')) {
					$fileSuffix = mime_content_type($filename);
				}
				return 'unknown/' . trim($fileSuffix[0], '.');
		}
	}

	/**
	 * Create a random password
	 *
	 * @param int Password length
	 * @return string Random password
	 */
	public static function createRandomPassword($len = 7)
	{
		// Possible chars (1 and l ommitted as they look the same)
		$chars = 'abcdefghijkmnopqrstuvwxyz023456789';

		// Get random characters up to requested length
		$pass = '';
		for ($i = 0; $i <= $len; $i++) {
			$pass .= substr($chars, mt_rand(0, 33), 1);
		}

		return $pass;
	}

	/**
	 * Return the distance between two latitude/longitude locations
	 *
	 * @param float Latitude 1
	 * @param float Longitude 1
	 * @param float Latitude 2
	 * @param float Longitude 2
	 * @return string Distance in kilometers to two decimal points
	 */
	public static function distanceBetween($lat1, $lng1, $lat2, $lng2)
	{
		// Radius of earth in km
		$radius = 6371;

		//This is the Haversine formula
		$lat1 = deg2rad($lat1);
		$lat2 = deg2rad($lat2);
		$lng2 = deg2rad($lng2);
		$lng1 = deg2rad($lng1);
		$latdiff = $lat2-$lat1;
		$lngdiff = $lng2-$lng1;
		$sinlat = sin($latdiff/2);
		$sinlng = sin($lngdiff/2);
		$a = ($sinlat * $sinlat) + cos($lat1) * cos($lat2) * ($sinlng * $sinlng);
		$c = 2 * asin(min(1, sqrt($a)));
		$d = ($radius * $c);

		return number_format($d, 2, '.', '');
	}

	/**
	 * Flatten a multi-dimensional array so that each key
	 * contains a serialized string of it's values
	 *
	 * in:
	 * array('key1' => array('child1' => 'val1', 'child2' => 'val2'),
	 *       'key2' => array('child2' => 'val3', 'child4' => 'val4'))
	 *
	 * out:
	 * array('key1' => serialize(array('val1', 'val2')),
	 *       'key2' => serialize(array('val3', 'val4')),
	 *
	 * @param array Array to flatten
	 * @return array Flattened array
	 */
	/*public static function flattenArray($array)
	{
		foreach ($array as $key => $child) {
			$child = array_values($child);
			$newArray[$key] = serialize($child);
	 	}
		return $newArray;
	}*/

    public static function regExpFile($regExp, $dir)
    {
    	static $dirFiles;

    	$dirSig = md5($dir);

    	if (!isset($dirFiles[$dirSig])) {
			$open = opendir($dir);

			while ($file = readdir($open)) {
				$dirFiles[$dirSig][] = $file;
			}
    	}

		return preg_grep($regExp,$dirFiles[$dirSig]);
	}
	
	/**
	 *	Generate a slug
	 *
	 *	@access public
	 *	@param string String
	 *	@param string Replacement character for illegals
	 */
	public static function slugify($str, $replacementChar = null)
	{
		// Default
		if (!$replacementChar) {
			$replacementChar = BluApplication::getSetting('slugReplacementChar', '_');
		}
		
		// Replace illegal
		$str = strtolower($str);
		$str = preg_replace('/[^a-z0-9]+/', $replacementChar, $str);
		$str = trim($str, $replacementChar);
		return $str;
	}
	
	/**
	 *	Generate a SEO-friendly string from a raw string.
	 */
	public static function seo($string)
	{
		return self::slugify($string, '-');
	}

	public static function getShoeSizes() {
		static $sizes;

		if (!is_array($sizes)) {
			$cache = BluApplication::getCache();
			$sizes = $cache->get('shoeSizes');
			if ($sizes === false) {
				// Get sizes out of DB, mangle, store.
				$query = 'SELECT * FROM shoeSizeMap';
				$db = BluApplication::getDatabase();
				$db->setQuery($query);
				$sizesRaw = $db->loadAssocList();
				foreach ($sizesRaw as $rawSizeRow) {
					$sizes['b'.$rawSizeRow['brandId']][$rawSizeRow['gender']]['UK'][$rawSizeRow['internalId']] = rtrim(trim($rawSizeRow['sizeUK'], '0'), '.');
					$sizes['b'.$rawSizeRow['brandId']][$rawSizeRow['gender']]['US'][$rawSizeRow['internalId']] = rtrim(trim($rawSizeRow['sizeUS'], '0'), '.');
					$sizes['b'.$rawSizeRow['brandId']][$rawSizeRow['gender']]['EU'][$rawSizeRow['internalId']] = rtrim(trim($rawSizeRow['sizeEU'], '0'), '.');
					$sizes['b'.$rawSizeRow['brandId']][$rawSizeRow['gender']]['stockist'][$rawSizeRow['internalId']] = $rawSizeRow['internalId']; // Not redundant.
				}
				$cache->set('shoeSizes', $sizes);
			}
		}

		return $sizes;
	}

	/**
	 * Convert a shoe size to the given type
	 *
	 * @param int Base shoe size
	 * @param string Sex (Mens/womens)
	 * @param string Base shoe size scale
	 * @param string Desired shoe size scale
	 * @return mixed Array of sizes in all scales, or individual size in requested scale
	 */
	public static function convertShoeSize($fromSize, $sex, $fromScale, $toScale, $brandId)
	{
		$sizes = self::getShoeSizes();

		$index = array_search($fromSize, $sizes[$brandId][$sex][$fromScale]);

		if ($toScale == 'ALL') {
			return array (
				'UK' => $sizes[$brandId][$sex]['UK'][$index],
				'EU' => $sizes[$brandId][$sex]['EU'][$index],
				'US' => $sizes[$brandId][$sex]['US'][$index]
			);
		}

		return $sizes[$brandId][$sex][$toScale][$index];
	}


	/**
	 * Checks if an array is loopable, i.e. is an array and is not empty
	 * (For the purposes of avoiding warnings when looping empty/non-existent arrays)
	 *
	 * @param (array) Array to check
	 * @retrun (boolean) Array loopable or not
	 */
	public static function is_loopable(&$ar){
		return isset($ar) && is_array($ar) && !empty($ar);
	}
	
	/**
	 *	Alias of self::is_loopable.
	 */
	public static function iterable(&$ar){
		return self::is_loopable($ar);
	}

	public static function ArrayToXML($array, $nestingLevel=0){
		$text = '';
		foreach($array as $key => $value){
			if(!is_array($value)){
				$text .= str_repeat("\t",$nestingLevel);
				$text .= "<$key>$value</$key>\n";
			} else {
				if (!is_int($key)) {
					$text .= str_repeat("\t",$nestingLevel);
					$text.= "<$key>\n";
				}
				$text.= Utility::ArrayToXML($value, $nestingLevel+1);
				if (!is_int($key)) {
					$text .= str_repeat("\t",$nestingLevel);
					$text.= "</$key>";
				}
				$text.= "\n";
			}
		}
		return $text;

	}

	/**
	 *	Takes an associative array and sets each element as a variable inside the object.
	 *
	 *	@args (array) data to store as variables
	 *	@return (mixed) the resulting object.
	 */
	public static function toObject($array){
		$object = new stdClass();
		if (self::is_loopable($array)){ foreach($array as $k=>$v){ $object->$k = self::is_loopable($v) ? self::toObject($v) : $v; } }
		return $object;
	}

	/**
	 *	Generates the date/time. TO BE FINISHED
	 *
	 *	@args (string - unix timestamp) date: the date
	 *	@args (string) (optional) format: the format to use for formatting the timestamp.
	 *	@return (string)  the formatted date.
	 */
	public static function formatDate($date, $format = null){
		$format = $format ? $format : 'jS F Y';
		return date($format, strtotime($date));
	}

	/**
	 *	Pop an element from an array, BY KEY.
	 */
	public static function array_pop(array &$array, $key)
	{
		if (array_key_exists($key, $array)){
			$element = $array[$key];
			unset($array[$key]);
			return $element;
		}
		return null;
	}
	
	/**
	 *	Fetch an element from an (multi-dimensional) array, if it exists, or return a default.
	 *
	 *	Usage: multi_array_get($anArray, 'alice', 'bob', 'carlos', $dave) will basically return:
	 *		isset($anArray['alice']['bob']['carlos']) ? $anArray['alice']['bob']['carlos'] : $dave;
	 *
	 *	@param array Array to search through.
	 *	@param string Key to search for.
	 *	@param string ...optionally more keys (for multidimensional arrays)...
	 *	@param mixed Default value.
	 *	@return mixed Element of array, or default value.
	 */
	public static function multi_array_get(){
		
		/* This method's arguments. */
		$args = func_get_args();
		
		/* Get default value: last argument */
		$default = self::iterable($args) ? array_pop($args) : null;
		
		/* Get array: first argument */
		if (!self::iterable($args)){
			return $default;
		}
		$array = array_shift($args);
		
		/* Next key to check */
		if (!self::iterable($args)){
			
			// We are done.
			return $array;
			
		}
		
		/* Grab (and remove) key from list of arguments */
		$nextKey = array_shift($args);
		
		/* Key exists? */
		if (self::iterable($array) && array_key_exists($nextKey, $array)){
			
			// Go one level deeper
			$array = $array[$nextKey];
			
			// Rebuild arguments
			array_unshift($args, $array);
			array_push($args, $default);
			
			// Recurse
			return call_user_func_array(array('self', __FUNCTION__), $args);
			
		}
		
		/* Fail */
		return $default;
		
	}
	
	/**
	 *	Rename the keys for an array.
	 *
	 *	N.B. Overwrites elements with the same key.
	 *
	 *	@param array Array to rename keys for.
	 *	@param array List of keys to rename from/to.
	 *	@param bool Include unrenamed keys in output.
	 *	@return array Renamed array.
	 */
	public static function array_rename(array $original, array $mapping, $includeUnrenamed = true){
		
		/* Output */
		$output = array();
		
		/* Shift data about */
		if (!empty($mapping)){
			foreach($mapping as $from => $to){
				if (array_key_exists($from, $original)){
					$output[$to] = self::array_pop($original, $from);
				}
			}
		}
		
		/* Merge with unrenamed elements? */
		if ($includeUnrenamed){
			$output = array_merge($original, $output);
		}
		
		/* Return */
		return $output;
		
	}

	/**
	 *	Puts 'a' or 'an' on the front of a noun as required.
	 */
	public static function prefixIndefiniteArticle($str)
	{
		return 'a'.(($str[0] == 'a' || $str[0] == 'e' || $str[0] == 'i' || $str[0] == 'o' || $str[0] == 'u')?'n':'').' '.$str;
	}

	/**
	 * Clean a filename
	 *
	 * @param string Dirty filename
	 * @return string Clean filename
	 */
	public static function cleanFilename($srcFile)
	{
		// Don't allow off site srcFile to be specified via http/https/ftp
		$srcFile = preg_replace('/^((ht|f)tp(s|):\/\/)/i', '', $srcFile);

		// Remove domain name from the source url
		$srcFile = str_replace($_SERVER['HTTP_HOST'], '', $srcFile);

		// Don't allow users the ability to use '../' in order to gain access to files above document root
		$srcFile = preg_replace('/\.\.+\//', '', $srcFile);

		return $srcFile;
	}

	/**
	 * Get a file extension
	 *
	 * @param string File name (e.g. test.jpg)
	 * @return string File extension (.e. jpg)
	 */
	public static function getFileExtension($fileName)
	{
		return strtolower(substr(strrchr($fileName, '.'), 1));
	}

	/**
	 *	Flatten an array (multi-dimensional into a 1D).
	 *
	 *	Only works for arrays with numeric keys.
	 *	(String keys overwrite each other when using array_merge).
	 */
	public static function flatten(array $array){

		/* Master array */
		$master = array();
		if (self::iterable($array)){
			foreach($array as $key => $element){

				/* Append lower-level (flattened) elements */
				if (self::iterable($element)){
					$master = array_merge($master, self::flatten($element));
				} else {
					$master[$key] = $element;
				}

			}
		}

		/* Return */
		return $master;

	}

	/**
	 *	Get adjacent items of an element from an array.
	 */
	public static function adjacent($element, array $array){

		/* Set pointer to element's position */
		$currentKey = array_search($element, $array);
		if ($currentKey === false){ return null; }

		/* Get key position */
		$arrayKeys = array_keys($array);
		$arrayKeysPosition = array_search($currentKey, $arrayKeys);

		/* Adjacent */
		$previousKeyPosition = $arrayKeysPosition > 0 ? $arrayKeysPosition - 1 : null;
		$nextKeyPosition = $arrayKeysPosition < count($arrayKeys) - 1 ? $arrayKeysPosition + 1 : null;

		/* Return */
		return array(
			'previous' => !is_null($previousKeyPosition) ? $array[$arrayKeys[$previousKeyPosition]] : null,
			'next' => !is_null($nextKeyPosition) ? $array[$arrayKeys[$nextKeyPosition]] : null
		);

	}

	/**
	 *	Pick random entries out of an array.
	 */
	public static function random(array $input, $num_req = 1){

		/* Empty arrays, the buggers they are */
		if (!self::iterable($input)){ return null; }

		/* Format */
		$num_req = min((int) $num_req, count($input));

		/* Get array of random keys */
		$keys = array_flip((array) array_rand($input, $num_req));

		/* Intersect with input array */
		$output = array_intersect_key($input, $keys);

		/* Return */
		return $output;

	}
	
	/**
	 *	Get the last entry of an array.
	 */
	public static function &getLast(array $array){
		
		/* Empty arrays */
		if (!self::iterable($array)){
			return null;
		}
		
		/* Get last entry */
		$entry =& $array[count($array) - 1];
		
		/* Return */
		return $entry;
		
	}
	
	/**
	 *	Custom implode function.
	 */
	public static function implode($glue, array $pieces, $callback){
		
		/* Check implodable */
		if (!Utility::iterable($pieces)){
			return false;
		}
		
		/* Apply custom transform */
		foreach($pieces as $key => &$piece){
			$piece = $callback($piece, $key);	// Order of arguments arranged as in Mootools.
		}
		
		/* Return implosion */
		return implode($glue, $pieces);
		
	}
	
	/**
	 *	Replace sets of tags.
	 *
	 *	Useful for stripping <div>s (which sometimes f*ck up layouts).
	 *
	 *	@param array/string Disabled tags.
	 *	@param array/string Replacement tags.
	 *	@param string Subject.
	 *	@return string Clean string.
	 */
	public static function tag_replace($toReplace, $replacements, $subject){
		
		/* Format */
		$toReplace = Utility::iterable($toReplace) ? $toReplace : array($toReplace);
		$replacements = Utility::iterable($replacements) ? $replacements : array($replacements);
		if (count($toReplace) != count($replacements)){
			// Error
			return $subject;
		}
		
		/* Prepare regexs */
		foreach($toReplace as &$needle){
			// Open/closed tags, with/without attributes.
			$needle = '/<(\/)?'.$needle.'( [^>]*)?>/i';
		}
		unset($needle);
		foreach($replacements as &$replace){
			if ($replace){
				// Replace tag.
				$replace = '<$1'.strtolower($replace).'$2>';
			} else {
				// Remove tag.
				$replace = '';
			}
		}
		unset($replace);
		
		/* Replace */
		return preg_replace($toReplace, $replacements, $subject);
		
	}
	
	/**
	 *	Recursive coalesce operator (worthy of a lolphp).
	 *
	 *	Goes through each parameter: 
	 *		if not empty, return it; 
	 *		otherwise, check the next parameter.
	 *
	 *	@param Variable to check.
	 *	@return mixed.
	 */
	public static function coalesce(){
		
		/* Get function arguments */
		$args = func_get_args();
		
		/* The default default */
		if (empty($args)){
			return null;
		}
		
		/* Bump off the value to check */
		$value = array_shift($args);
		
		/* Recurse */
		return empty($value) ? call_user_func_array(array('self', 'coalesce'), $args) : $value;
		
	}
	
	/**
	 *	Fetch encoding type, through detecting the BOM.
	 *
	 *	@static
	 *	@access public
	 *	@param string Text
	 *	@return string Encoding
	 */
	public static function detect_encoding($string)
	{
		// BOM types
		$boms = array(
			'UTF-8' => b"\xEF\xBB\xBF",
			'UTF-16/BE' => b"\xFE\xFF",
			'UTF-16/LE' => b"\xFF\xFE",
			'UTF-32/BE' => b"\x00\x00\xFE\xFF",
			'UTF-32/LE' => b"\xFF\xFE\x00\x00"
		);
		
		// Test
		foreach ($boms as $key => $bom) {
			if (substr($string, 0, strlen($bom)) == $bom) {
				//return $key;
			}
		}
		
		// Fail - default to inbuilt multibyte string function
		return mb_detect_encoding($string, mb_detect_order(), true);
	}
	
	/**
	 *	Convert encoding
	 *
	 *	@static
	 *	@access public
	 *	@param mixed Text
	 *	@param string Destination encoding
	 *	@param array Options
	 *	@return mixed Output.
	 */
	public static function convert_encoding($input, $destEncoding, array $options = array())
	{
		// Recurse
		if (!empty($input) && is_array($input)) {
			
			$output = array();
			foreach ($input as $k => $v) {
				$output[$k] = self::convert_encoding($v, $destEncoding, $options);
			}
			
		// Do conversion
		} else {
			
			// Detect current encoding
			$currentEncoding = self::detect_encoding($input);
			if ($currentEncoding == $destEncoding) {
				$output = $input;
				
			} else {
				
				// Fix excel missing first byte (we assume the beginning of the string isn't a non-ascii character)
				if (!empty($options['fix_excel']) && $currentEncoding == 'UTF-16' && !is_int(strlen($input) / 2)) {
					$input = b"\x00".$input;
				}
				
				// Convert
				if ($currentEncoding){
					$output = mb_convert_encoding($input, $destEncoding, $currentEncoding);
				} else {
					$output = mb_convert_encoding($input, $destEncoding);
				}
			}
		}
		
		// Return
		return $output;
	}
	
	/**
	 *	Remove a directory
	 *
	 *	@static
	 *	@access public
	 *	@param string Directory
	 *	@param array Options
	 *	@return bool Success
	 */
	public static function rmdir($directory, array $options = array())
	{
		// Directories only
		if (!is_dir($directory)) {
			return false;
		}
		
		// Get options
		$recursive = isset($options['recursive']) ? (bool) $options['recursive'] : false;
		$verbose = isset($options['verbose']) ? (bool) $options['verbose'] : false;
		
		// Use wrapper object
		$directoryObject = dir($directory);
		
		// Remove directory contents
		while (($file = $directoryObject->read()) !== false) {
			
			// Never go up the tree
			if ($file == '.' || $file == '..') {
				continue;
			}
			
			// Use absolute path
			$file = $directory.DIRECTORY_SEPARATOR.$file;
			
			// Delete files
			if (is_file($file)) {
				if (!unlink($file)) {
					if ($verbose) {
						echo 'Could not remove file '.$file;
					}
					return false;
				}
				if ($verbose) {
					echo 'Removed file '.$file;
				}
			}
			
			// Delete links
			if (is_link($file)) {
				if (!unlink($file)) {
					if ($verbose) {
						echo 'Could not remove link '.$file;
					}
					return false;
				}
				if ($verbose) {
					echo 'Removed link '.$file;
				}
			}
			
			// Delete subdirectories
			if ($recursive && is_dir($file)) {
				if (!self::rmdir($file, $options)) {
					return false;
				}
				if ($verbose) {
					echo 'Removed subdirectory '.$file;
				}
			}
		}
		
		// Remove the directory itself
		if (!rmdir($directory)) {
			if ($verbose) {
				echo 'Could not remove directory '.$directory;
			}
			return false;
		}
		if ($verbose) {
			echo 'Removed directory '.$directory;
		}
		
		// Return
		return true;
	}
	
	/**
	 *	Extract image tags from the html and change relative paths to absolute paths in the html
	 *
	 *	@static
	 *	@access public
	 *	@param string Html
	 *	@param string Temporary folder (used when uploading new inline images)
	 *	@param string Permanent folder
	 *	@return array Image tags
	 */
	public static function parseImageTags(&$html, $temporaryFolder = 'itemimages', $permanentFolder = 'itemimages') {
		$images = array();
		if(preg_match_all('/\<img(.*)\>/siU',$html,$imageTags,PREG_SET_ORDER)) {
			foreach($imageTags as $tags) {
				$img = $tags[0];
				$width = null;
				if(preg_match('/width="\d+"/',$img,$widthAttribute)) {
					$width = preg_replace('/[^\d]/','',$widthAttribute[0]);
				}
				$height = null;
				if(preg_match('/height="\d+"/',$img,$heightAttribute)) {
					$height = preg_replace('/[^\d]/','',$heightAttribute[0]);
				}
				if(preg_match('/src="([^"]*)"/s',$img,$srcAttributeArray)) {
					$srcAttribute = $srcAttributeArray[0];
					$src = $srcAttributeArray[1];
					$newSrc = $src;
					if($width && $height) {
						$newSrc = preg_replace('/\/('.$temporaryFolder.'|'.$permanentFolder.')\/\d+\/\d+\/([0-9]+)\//','/'.$permanentFolder.'/'.(int)$width.'/'.(int)$height.'/0/',$newSrc);
					}
					else {
						$newSrc = preg_replace('/\/('.$temporaryFolder.'|'.$permanentFolder.')\//','/'.$permanentFolder.'/',$newSrc);
					}
					$srcBase = preg_replace('/^(\.\.\/)*/','',$src);
					if(preg_match('/^'.preg_replace('/^\//','',ASSETURL).'/',$srcBase)) {
						$filePath = preg_replace('/^'.preg_replace('/^\//','',ASSETURL).'/',BLUPATH_ASSETS,$srcBase);
					}
					else {
						$filePath = str_replace('http://'.$_SERVER['SERVER_NAME'].ASSETURL,BLUPATH_ASSETS,$srcBase);
					}
					$newSrcBase = preg_replace('/^(\.\.\/)*/','',$newSrc);
					if(preg_match('/^'.preg_replace('/^\//','',ASSETURL).'/',$newSrcBase)) {
						$newFilePath = preg_replace('/^'.preg_replace('/^\//','',ASSETURL).'/',BLUPATH_ASSETS,$newSrcBase);
						$newSrc = 'http://'.$_SERVER['SERVER_NAME'].'/'.$newSrcBase;
					}
					else {
						$newFilePath = str_replace('http://'.$_SERVER['SERVER_NAME'].ASSETURL,BLUPATH_ASSETS,$newSrcBase);
					}
					$filePath = urldecode(preg_replace('/\/\d+\/\d+\/0\//','/',$filePath));
					$newFilePath = preg_replace('/\/\d+\/\d+\/0\//','/',$newFilePath);
					$newFilePath = urldecode($newFilePath);
					$newImg = preg_replace('/src="([^"]*)"/s','src="'.$newSrc.'"',$img);
				}
				else {
					$newImg = $img;
					$src = $newSrc = $filePath = $newFilePath = null;
				}
				if(strpos($filePath,BLUPATH_ASSETS.'/'.$temporaryFolder.'/')===0) {
					$tempFile = true;
				}
				else {
					$tempFile = false;
				}
				$images[] = array(	'img'=>$img,
									'newImg'=>$newImg,
									'src'=>$src,
									'newSrc'=>$newSrc,
									'filePath'=>$filePath,
									'newFilePath'=>$newFilePath,
									'tempFile'=>$tempFile
								);
			}
			if($images) {
				foreach($images as $image) {
					$html = str_replace($image['img'],$image['newImg'],$html);
				}
			}
		}
		return $images;
	}
	
	/**
	 *	Calculate difference between 2 stings
	 *
	 *	@static
	 *	@access public
	 *	@param string Old text
	 *	@param string New text
	 *	@return string Diff
	 */
	public static function getDiff($text1,$text2) {
		
		$file1 = explode("\n",(string)$text1);
		$file2 = explode("\n",(string)$text2);
		
		ob_start(); // workaround for hiding E_STRICT notices; not a good solution :-(
		
		require_once('Text/Diff3.php');
		require_once('Text/Diff/Renderer.php');
		require_once('Text/Diff/Renderer/inline.php');
		
		$diff = new Text_Diff($file1,$file2);
		$renderer = new Text_Diff_Renderer_inline();
		$diffString = $renderer->render($diff);
		
		ob_end_clean(); // workaround for hiding E_STRICT notices; not a good solution :-(
		
		return $diffString;
		
	}

	public static function irc_dump($data, $name = null) {
		static $dumpSocket = false;
		$dumped = print_r($data,true);
		if ($dumpSocket == false) {
			//$dumpSocket = fsockopen("udp://maintenance.blubolt.com", 22727, $errno, $errstr, 1);
		}
		if ($dumpSocket) {
			//fwrite ($dumpSocket, $name.'blucrit'.$dumped);
		}
	}


}
?>
