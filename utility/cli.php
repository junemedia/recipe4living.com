<?php
/**
 * BluApplication command line site entry point
 *
 * Handles class autoloading and application creation, dispatch and render
 *
 * @package BluApplication
 */
 

// Make it obvious everywhere that we are in the command line environment
define('CLI',true);

// Work out what site this is related to.
$siteId = $_SERVER['argv'][1];


// Since we may be running locally, and therefore need the subdomain for things like caching,
// set the subdomain as well as the domain.
$subdomain = 'www';
if (strpos($siteId,'.')) {
	$explodedSiteId = explode('.',$siteId);
	$subdomain = $explodedSiteId[0];
	$siteId = $explodedSiteId[1];
}


// Get information about the controller and task to run
$controller = $_SERVER['argv'][2];
$task = $_SERVER['argv'][3];

// Get arguments passed to the script
$args = array_slice($_SERVER['argv'],4);

// Set up some $_SERVER variables to trick BluApplication into letting us run.
$_SERVER = array_merge($_SERVER,Array(
	'REMOTE_ADDR' => '127.0.0.1',			// Request is local, so we are automatically admin
	'REQUEST_URI' => '/oversight/',		// Force our way into the backend
	'HTTP_HOST' => $subdomain.'.'.$siteId.'.local'	// Needed by multisites to decide the siteId.  Changed later.
));


/**
 * bluCommerce base paths
 */
define('BLUPATH_BASE', realpath(dirname(__FILE__).'/../'));

// Load config
require_once(BLUPATH_BASE.'/config.php');

// Full error reporting for debug
if (DEBUG) { error_reporting(E_ALL | E_STRICT); }

// Record script execution start time
if (DEBUG) { $startTime = microtime(true); }

/**
 * Class autoloader for libraries
 */
function __autoload($className)
{
	if (!file_exists(BLUPATH_BASE.'/shared/lib/'.$className.'.php')) {
		trigger_error('Could not find '.$className, E_USER_ERROR);
	}
	require_once(BLUPATH_BASE.'/shared/lib/'.$className.'.php');
}

// Create application
$bluCommerce = BluApplication::getInstance($controller);

// Dispatch to requested option
$bluCommerce->dispatchRaw($controller,$task,$args);

require_once(BLUPATH_BASE.'/utility/cli2.php');
