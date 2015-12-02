<?php

/**
 * Session Environment Library
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class Session
{
	/**
	 * Set up and start the session
	 */
	public static function start()
	{
		if (CACHE === true) { // Only if memcache exists do we do this
			// Use memcache as the save handler
			$memcacheSessionHost = BluApplication::getSetting('memcacheSessionHost');
			$memcacheSesssionPort = BluApplication::getSetting('memcacheSessionPort');
			$cacheClient = defined('CACHECLIENT') ? CACHECLIENT : self::DEFAULT_CACHE_CLIENT;
			switch ($cacheClient) {
				case 'memcached' :
					$sessionSavePath = $memcacheSessionHost.':'.$memcacheSesssionPort;
					ini_set('session.save_handler', $cacheClient);
					if(ULTRACACHE) {
						ini_set('session.serialize_handler', 'igbinary');
					}
					session_save_path($sessionSavePath);
					break;
				default :
					$sessionSavePath = 'tcp://'.$memcacheSessionHost.':'.$memcacheSesssionPort.'?persistent=1&weight=1&timeout=2&retry_interval=10';
					ini_set('session.save_handler', $cacheClient);
					session_save_path($sessionSavePath);
					break;
			}

		}

		// Don't limit caching
		session_cache_limiter('none');

		// Don't use the standard PHP session ID, just for an extra little bit of obfuscation
		session_name('blu-session-id');

		// Restrict the session to the base path
		session_set_cookie_params(864000 , BluApplication::getSetting('baseUrl').'/');//, '.'.BluApplication::getSetting('siteDomainName'));

		// Got encrypted session from fancy upload?
		if ($uploadKey = Request::getString('uploadkey')) {

			// Flash replaces + with SPACE
			$uploadKey = str_replace(' ', '+', $uploadKey);

			// Decrypt and start names session
			$crypto = Crypto::getInstance(md5(BluApplication::getSetting('cryptoSalt')));
			session_id($crypto->decrypt($uploadKey));
		}

		// Below if condition code added by Samir Patel on Oct 12th, 2011 to increase session time limit for office IPs
		// add white list IPs in array to increase session time limit
		if (in_array($_SERVER['REMOTE_ADDR'], array('66.54.186.254','208.103.93.46'))) {
			// Increase session cookie time in user's browser - 12 hours
			ini_set('session.cookie_lifetime', 12*60*60);

			// Increase the retention of the session information on the server - 12 hours
			ini_set('session.gc_maxlifetime', 12*60*60);

			// Increase the retention of the session information on the server - 12 hours
			ini_set('session.cache_expire', 12*60*60);
		}

		/** Admin user session longer **/
		if (isset($_SERVER['PHP_AUTH_USER'])) {
			// Increase session cookie time in user's browser - 15 hours
			ini_set('session.cookie_lifetime', 15*60*60);

			// Increase the retention of the session information on the server - 15 hours
			ini_set('session.gc_maxlifetime', 15*60*60);

			// Increase the retention of the session information on the server - 15 hours
			ini_set('session.cache_expire', 15*60*60);
		}
		/** Admin user session longer END **/

		// Start the session
		session_start();
	}

	/**
	 *	Get the session id
	 *
	 *	@return the session id.
	 */
	public static function getSessionId()
	{
		return session_id();
	}

	/**
	 *	Regenerate the session id, while keeping specific session variables intact.
	 *
	 *	@return the new session id.
	 */
	public static function regenerateId()
	{
		session_regenerate_id(true);
		return session_id();
	}

	/**
	 * Store a variable in the session
	 *
	 * @param string Key name
	 * @param mixed Value
	 */
	public static function set($key, $value)
	{
		$_SESSION[$key] = $value;

		// For some reason session variables don't save correctly in
		// rare circumstances unless we explicity reference them?!?
		// Go Figure.
		$lolPHP =& $_SESSION[$key];
	}

	/**
	 * Get a variable from the session
	 *
	 * @param string Key name
	 * @param mixed Default value
	 */
	public static function get($key, $default = null)
	{
		return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
	}

	/**
	 * Delete a session variable
	 *
	 * @param mixed Key name
	 *	@return mixed the deleted value.
	 */
	public static function delete($key, $default = null)
	{
		if ($value = self::get($key)){

			/* Unset the session variable */
			unset($_SESSION[$key]);

			/* Unload from persisting keys array too */
			if (isset($_SESSION['persist'][$key])){
				unset($_SESSION['persist'][$key]);
			}

			/* Return the deleted variable */
			return $value;

		}
		return $default;
	}

	/**
	 *	Clears out the session.
	 *
	 *	@static
	 *	@access public
	 *	@args array Keys to keep
	 */
	public static function clear(array $persist = array())
	{
		if (!empty($_SESSION)) {
			foreach ($_SESSION as $key => $value) {
				if (!in_array($key, $persist)) {
					unset($_SESSION[$key]);
				}
			}
		}
		return true;
	}

}
?>
