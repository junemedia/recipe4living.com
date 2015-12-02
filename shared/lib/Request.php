<?php

/**
 * Request Environment Library
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class Request
{
	/**
	 *	The snapshot key.
	 */
	const SNAPSHOT_KEY = 'snapshot';

	/**
	 * Get a variable from the request
	 * Note: No cleaning is performed, so care should be taken if using this method directly
	 *
	 * @param string Key name
	 * @param mixed Default value
	 * @param string Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return mixed Value
	 */
	public static function getVar($key, $default = null, $hash = 'default')
	{
		// Supplied with input hash array?
		if (is_array($hash)) {
			$input = $hash;
		} else {
			// Get the input hash
			$input = &self::_switchHash($hash);
		}

		// Get the var, or the default
		if (isset($input[$key])) {
			$var = $input[$key];
			if(get_magic_quotes_gpc() && !is_array($var)) {
	            $var = stripslashes($var);
	        }
	    } else {
	    	$var = $default;
	    }

		// Return the var
		return $var;
	}

	/**
	 * Get a string from the request
	 *
	 * @param string Key name
	 * @param string Default value
	 * @param string Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 *	@param string whether *not* to strip HTML tags.
	 * @return string Value
	 */
	public static function getString($key, $default = null, $hash = 'default', $allowHTML = false)
	{
		$string = trim(self::getVar($key, $default, $hash));
		if (!$allowHTML) {
			$string = strip_tags($string);
		}
		return is_string($string) ? $string : null;
	}

	/**
	 * Get an array from the request
	 *
	 * @param string Key name
	 * @param string Default value
	 * @param string Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return mixed Value
	 */
	public static function getArray($key, $default = null, $hash = 'default')
	{
		$array = self::getVar($key, $default, $hash);
		return $array === $default ? $default : (array) $array;
	}

	/**
	 * Get an integer from the request
	 *
	 * @param string Key name
	 * @param int Default value
	 * @param string Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return mixed Value
	 */
	public static function getInt($key, $default = null, $hash = 'default')
	{
		$int = self::getVar($key, $default, $hash);
		return $int === $default ? $default : (int) $int;
	}

	/**
	 * Get a float from the request
	 *
	 * @param string Key name
	 * @param int Default value
	 * @param string Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return mixed Value
	 */
	public static function getFloat($key, $default = null, $hash = 'default')
	{
		$float = self::getVar($key, $default, $hash);
		return $float === $default ? $default : (float) $float;
	}

	/**
	 * Get a boolean from the request
	 *
	 * @param string Key name
	 * @param bool Default value
	 * @param string Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return mixed Value
	 */
	public static function getBool($key, $default = false, $hash = 'default')
	{
		$bool = self::getVar($key, $default, $hash);
		return $bool === $default ? $default : (bool) $bool;
	}

	/**
	 * Get a filtered string command from the request
	 * Only allows the characters [A-Za-z0-9.-_]
	 *
	 * @param string Key name
	 * @param bool Default value
	 * @param string Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return string Value
	 */
	public static function getCmd($key, $default = false, $hash = 'default')
	{
		$value = self::getVar($key, $default, $hash);
		return preg_replace('/[^A-Za-z0-9.-_]/', '', $value);
	}

	/**
	 * Set a value in the request
	 *
	 * @param string Key name
	 * @param mixed Value
	 * @param string Which hash to set the var in (POST, GET, FILES, COOKIE, METHOD)
	 */
	public static function setVar($key, $value, $hash = 'default')
	{
		// Get the input hash
		$input = &self::_switchHash($hash);

		// Set the value in the hash
		$input[$key] = $value;
	}

	/**
	 * Set cookie
	 *
	 * @param string Name
	 * @param string Value
	 * @param int Expiry time (secs)
	 *	@param string Domain in which cookie is valid
	 *	@return bool Success
	 */
	public static function setCookie($name, $value, $expiry = 0, $domain = '/')
	{
		$expiry = $expiry ? $expiry + time() : 0;
		return setcookie($name, $value, $expiry, $domain);
	}

	/**
	 * Get the visitors IP address
	 *
	 * @return string Visitors IP address in dot notation xxx.xxx.xxx.xxx
	 */
	public static function getVisitorIPAddress()
	{
		static $visitorIP = null;
		if (!$visitorIP) {

			// Get client IP
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$visitorIP = $_SERVER['HTTP_CLIENT_IP'];
			} else {
				$visitorIP = false;
			}

			// Get IPs of forwarder clients
			if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$forwardIPs = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);

				// Add client IP
				if ($visitorIP) {
					array_unshift($forwardIPs, $visitorIP);
					$visitorIP = false;
				}

				// Get last valid IP address in list
				foreach(array_reverse($forwardIPs) as $forwardIP) {
					if (!preg_match('/^(?:10|172\.(?:1[6-9]|2\d|3[01])|192\.168|127\.0)\./', $forwardIP)) {
						if (ip2long($forwardIP) != false) {
							$visitorIP = $forwardIP;
						}
					}
				}
			}

			// Fall back to server remote address
			if (!$visitorIP) {
				$visitorIP = $_SERVER['REMOTE_ADDR'];
			}
		}

		// DEBUG:
		// $visitorIP = '198.28.69.5';
		//$visitorIP = '74.53.58.199';

		return $visitorIP;
	}

	/**
	 * Get redirect for the given uri
	 *
	 * @param string Request URI
	 * @return string Redirect URL
	 */
	public static function getRedirect($uri)
	{
		$db = BluApplication::getDatabase();
		$cache = BluApplication::getCache();

		$redirects = $cache->get('redirects');
		$redirectsCache = $cache->get('redirectsCache');

		if ($redirectsCache === false) {
			$redirectsCache = array();
			$cache->set('redirectsCache', $redirectsCache);
		}

		// Load redirects from db
		if ($redirects === false) {
			$query = 'SELECT fromURL, toURL, type FROM redirects ORDER BY LENGTH(fromURL) DESC';
			$db->setQuery($query);
			$redirects = $db->loadAssocList('fromURL');
			$cache->set('redirects', $redirects);
		}
		$redirects = false;

		// No redirects - nothing to do
		if (empty($redirects)) {
			return false;
		}

		foreach ($redirects as $redirectKey => $redirect) {
			$redirectFrom[$redirectKey] = $redirectKey;
			if($redirect['type']=='article') {
				$uri = preg_replace('/\/\//', '', $uri);  // two trailing slashes fix
				$toURL = self::_translateOldUrl($uri, $redirectKey);
				$redirectTo[$redirectKey] = $toURL.'|||perm';
			}
			else {
				$redirectTo[$redirectKey] = $redirect['toURL'].'|||'.$redirect['type'];
			}
		}

		// Use the fast cache if it exists to avoid doing an expensive regex
		if (array_key_exists($uri, $redirectsCache)) {
			$url = $redirectsCache[$uri];
			$fromCache = true;
		} else {
			$url = preg_replace($redirectFrom, $redirectTo, $uri);
			$fromCache = false;
		}

		// We have a redirect
		if (strpos($url,'|||')) {
			list($destination, $type) = explode ('|||', $url);
			$responseCode = ($type == 'perm' ? 301 : 301);
			$constants = get_defined_constants(true);
			$userConstants = $constants['user'];
			$destination = str_replace(array_keys($userConstants), $userConstants, $destination);

			// Store in fast cache
			if ($fromCache == false) {
				$redirectsCache[$uri] = $url;
				$cache->set('redirectsCache', $redirectsCache);
			}

			return array('destinaton' => $destination, 'responseCode' => $responseCode);
		}

		return false;
	}

	/**
	 * Translates old URL (.aspx) to new URL
	 *
	 * @param string Request URI
	 * @param string Regulal Expression to match Request URI
	 * @return string redirect URL, false otherwise
	 */
	private static function _translateOldUrl($uri, $regExp) {
		$newUrl = false;
		if(preg_match($regExp, $uri)) {
			if($urlStr = parse_url($uri, PHP_URL_QUERY)) {
				parse_str($urlStr, $urlArgs);
			}
			if(isset($urlArgs) && isset($urlArgs['id'])) {
				$oldArticleId = preg_replace('/[^0-9]/','',$urlArgs['id']);
			}
			elseif($urlReplaced = preg_replace('/\-.+\.aspx$/s', '', preg_replace('/^Recipe\//s', '', $uri))) {
				$oldArticleId = $urlReplaced;
			}
			else {
				$oldArticleId = NULL;
			}
			if(isset($oldArticleId) && preg_match("/^[0-9]{5}$/",$oldArticleId)) {
				$db = BluApplication::getDatabase();
				$query = 'SELECT id FROM articles WHERE oldArticleId="'.Database::escape($oldArticleId).'"';
				$db->setQuery($query);
				if($articleId = $db->loadResult()) {
					$itemsModel = BluApplication::getModel('items');
					if($item = $itemsModel->getItem($articleId)) {
						$newUrl = $item['link'];
					}
				}
			}
		}
		return $newUrl;
	}


	/**
	 * See if the visitor is a search engine bot
	 *
	 * @return bool True if bot, false otherwise
	 */
	public static function isBot()
	{
		// If we have no user agent (e.g. from tstoreadmin curl) assume not bot
		if (!array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
			return false;
		}

		// Get user agent
		$userAgent = $_SERVER['HTTP_USER_AGENT'];

		// Check for a common bot name fragment in the UA string
		$bots = array(
			'bot',
			'spider',
			'google',
			'yahoo',
			'search',
			'crawl',
			'slurp',
			'msn',
			'teoma',
			'ask.com'
		);
		foreach ($bots as $bot) {
			if (strpos($userAgent, $bot)) {
				return true;
			}
		}

		return false;
	}

	/**
	 *	Takes a snapshot of the Request.
	 */
	public static function takeSnapshot($hash = 'default')
	{
		/* Get Request type. */
		$input = &self::_switchHash($hash);

		/* Store in Session */
		Session::set(self::SNAPSHOT_KEY.strtolower($hash), $input);

		/* Exit */
		return true;
	}

	/**
	 *	Fetch, and wipe, previous snapshot.
	 *	Don't return data, forcing to use this Request class's "get" methods.
	 */
	public static function fetchSnapshot($hash = 'default')
	{
		/* Get, and remove, from session. */
		$snapshot = self::getSnapshot($hash);
		self::deleteSnapshot($hash);
		if (!Utility::iterable($snapshot)){ return false; }

		/* Merge with current Request variables. */
		$current =& self::_switchHash($hash);
		$current = array_merge($current, (array) $snapshot);

		/* Exit */
		return true;
	}

	/**
	 *	Parse snapshot task.
	 */
	public static function parseSnapshotTask($hash = 'default'){

		/* Get snapshot */
		$snapshot = self::getSnapshot($hash);
		if (!Utility::iterable($snapshot)){ return false; }

		/* Get task */
		if (!isset($snapshot['task'])){ return false; }

		/* Return task */
		return $snapshot['task'];

	}

	/**
	 *	Get the snapshot from session.
	 */
	private static function getSnapshot($hash = 'default'){
		$snapshot = Session::get(self::SNAPSHOT_KEY.strtolower($hash));
		return Utility::iterable($snapshot) ? $snapshot : array();
	}

	/**
	 *	Remove, and return, snapshot from session.
	 */
	private static function deleteSnapshot($hash = 'default'){
		return Session::delete(self::SNAPSHOT_KEY.strtolower($hash), array());
	}






	#### SNAPSHOT IS A GOOD IDEA, BUT WAS IMPLEMENTED BADLY, TRY AGAIN:

	/**
	 *	Set $_REQUEST into $_SESSION
	 *
	 *	@static
	 *	@access public
	 */
	public static function storeRequest()
	{
		$sessionKey = self::SNAPSHOT_KEY;

		Session::set($sessionKey, $_REQUEST);
	}

	/**
	 *	Get $_REQUEST from $_SESSION
	 *
	 *	@static
	 *	@access public
	 */
	public static function restoreRequest()
	{
		$sessionKey = self::SNAPSHOT_KEY;

		if ($request = Session::get($sessionKey)) {
			$_REQUEST = $request;
			Session::delete($sessionKey);	// Clear up.
		}
	}

	####

	/**
	 *	Convenience method.
	 */
	private static function &_switchHash($hash = 'default')
	{
		// Get global hash name
		$hash = strtoupper($hash);
		if ($hash === 'METHOD') {
			$hash = strtoupper($_SERVER['REQUEST_METHOD']);
		}

		// Get the input hash
		switch ($hash) {
			case 'GET':
				$input = &$_GET;
				break;

			case 'POST':
				$input = &$_POST;
				break;

			case 'FILES':
				$input = &$_FILES;
				break;

			case 'COOKIE':
				$input = &$_COOKIE;
				break;

			case 'ENV':
				$input = &$_ENV;
				break;

			case 'SERVER':
				$input = &$_SERVER;
				break;

			default:
				$hash = 'REQUEST';
				$input = &$_REQUEST;
				break;
		}

		/* Return */
		return $input;
	}

	/**
	 *	Shortcut for getting a $_FILE.
	 *
	 *	@access public
	 *	@param string Variable name
	 *	@param mixed Fallback
	 *	@return array Upload details
	 */
	public static function getFile($key, $default = null)
	{
		// Get file
		if (!$file = self::getVar($key, false, 'FILES')) {
			return $default;		// No file uploaded
		}

		// Test integrity
		if (!Upload::isValid($file)) {
			return $default;		// Uploaded file corrupt.
		}

		// Return
		return $file;
	}

	/**
	 *	Shortcut for retrieving an array of $_FILES.
	 *
	 *	@static
	 *	@access public
	 *	@param string Variable name
	 *	@param mixed Fallback
	 *	@return array Files.
	 */
	public static function getFiles($key, $default = null)
	{
		// Variable doesn't exist
		if (empty($_FILES[$key])) {
			return $default;
		}

		// Reorder, lol PHP.
		$uploads = array();
		foreach ($_FILES[$key] as $attribute => $info) {
			self::_reorderFiles($uploads, $info, $attribute);
		}

		// Return
		return $uploads;
	}

	/**
	 *	Lol PHP
	 *
	 *	@static
	 *	@see http://us3.php.net/manual/en/features.file-upload.multiple.php
	 *	@access private
	 *	@param mixed Data
	 *	@param array Path + leaf data.
	 *	@return array Files
	 */
	private static function _reorderFiles(&$parent, $info, $attribute)
	{
		if (is_array($info)) {
			foreach ($info as $var => $val) {
				if (is_array($val)) {
					self::_reorderFiles($parent[$var], $val, $attribute);
				} else {
					$parent[$var][$attribute] = $val;
				}
			}
		} else {
			$parent[$attribute] = $info;
		}
		return true;
	}
}

?>
