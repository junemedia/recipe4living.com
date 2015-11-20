<?php

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
define('CACHE', false);
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

  // login credentials for staging site
  var $stageUser = '';
  var $stagePass = '';

  /* Database settings */

  /* Server settings */
  var $baseUrl = '';

  /* Data cache */
  var $memcacheSessionHost = '';
  var $memcacheSessionPort = '';

  /* Site settings */
  var $siteId = 'recipe4living';
  var $defaultFrontendController = 'recipes';
  var $defaultBackendController = 'recipes';

  /* Product/news commenting permissions */
  const COMMENT_ANON = 1;  // Allow comments from anonymous users
  const COMMENT_REGISTERED = 2;  // Allow comments from registered users
  const COMMENT_CAPTCHA_ANON = 4;  // Require a captcha for anonymous users
  const COMMENT_CAPTCHA_REGISTERED = 8;  // Require a captcha for registered users

  const COMMENT_ALL = 3;  // COMMENT_ANON | COMMENT_REGISTERED
  const COMMENT_CAPTCHA_ALL = 12; // COMMENT_CAPTCHA_ANON | COMMENT_CAPTCHA_REGISTERED

  function __construct() {
    $this->databases = Array(
			Array (
				'databaseHost' => '',
				'databaseUser' => '',
				'databasePass' => '',
				'databaseName' => ''
			)
    );

    $this->caches = Array (
      'data' => Array (
        Array('127.0.0.1', '11211')
      ),
      'session' => Array (
        Array('127.0.0.1', '11211')
      ),
      'bigslab' => Array(
        Array('127.0.0.1', '11211')
      ),
    );
  }
}
