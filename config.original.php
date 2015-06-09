<?php


if($_SERVER["SERVER_ADDR"] == "192.168.51.76")
{
	define('DEBUG_SERVER', true);
}else{
	define('DEBUG_SERVER', false);
}

/**
 *      Debug
 */
define('DEBUG', false);
define('NOVIEWCACHE', false);

/**
 *      Display error info
 */
define('DEBUG_INFO', false);
define('STAGING', false);

/**
 * Need a password to access the front end?
 */
define('NEEDPASSWORD', false);

/**
 *      Cache
 */
define('CACHE', true);
define('CACHECLIENT', 'memcached');
define('ULTRACACHE', false);
define('CACHEBUILD', false);

/**
 *      Adverts
 */
define('ADS', true);

/**
 *      STUPIDLY LONG SQL QUERY LIST?
 */
define('QUERY_LIST', false);

/**
 *      Purify
 */
define('PURIFY', false);




/**
 * BluApplication Configuration Settings
 *
 * @package BluApplication
 */
class Config {
	
	/* Database settings */
	
	/* Server settings */
	var $baseUrl = '';

	/* Data cache */
	var $memcacheSessionHost = '192.168.51.66';
	var $memcacheSessionPort = '12590';
	
	/* Site settings */
	var $siteId = 'recipe4living';
	var $defaultFrontendController = 'recipes';
	var $defaultBackendController = 'recipes';
	
	/* Product/news commenting permissions */
	const COMMENT_ANON				= 1;  // Allow comments from anonymous users
	const COMMENT_REGISTERED		= 2;  // Allow comments from registered users
	const COMMENT_CAPTCHA_ANON		= 4;  // Require a captcha for anonymous users
	const COMMENT_CAPTCHA_REGISTERED = 8;  // Require a captcha for registered users

	const COMMENT_ALL				= 3;  // COMMENT_ANON | COMMENT_REGISTERED
	const COMMENT_CAPTCHA_ALL		= 12; // COMMENT_CAPTCHA_ANON | COMMENT_CAPTCHA_REGISTERED

	function __construct() {
		$this->databases = Array(
						Array (	'databaseHost' => '192.168.51.65',
							'databaseUser' => 'r4ldbuser',
							'databasePass' => 'acgnW3FsFSD2',
							'databaseName' => 'recipe4living_staging')
							,
						Array (	'databaseHost' => '192.168.51.66',
							'databaseUser' => 'r4ldbuser',
							'databasePass' => 'acgnW3FsFSD2',
							'databaseName' => 'recipe4living_staging')
					);

		$this->caches = Array (
					'data' => Array (
//							Array (
//								'192.168.51.56', '12589'
//							),
//							Array (
//								'192.168.51.55', '12589'
//							),
//							Array (
//								'192.168.51.54', '12589'
//							),
Array('192.168.51.66', '12591'),
Array('192.168.51.65', '12591')
					),
					'session' => Array (
//							Array (
//								'memcacheHost' => '192.168.51.56',
//								'memcachePort' => '12590'
//							),
//							Array (
//								'memcacheHost' => '192.168.51.55',
//								'memcachePort' => '12590'
//							),
//							Array (
//								'memcacheHost' => '192.168.51.54',
//								'memcachePort' => '12590'
//							),
//Array('192.168.51.66', '12590'),
//Array('192.168.51.65', '12590')
					),
					'bigslab' => Array(
//Array('192.168.51.66', '12589'),
//Array('192.168.51.65', '12589')
					),

				);
	}
	
}

?>
