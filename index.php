<?php
/**
 * BluApplication site entry point
 *
 * Handles class autoloading and application creation, dispatch and render
 *
 * @package BluApplication
 */
if (!isset($_SERVER['HTTP_USER_AGENT'])) $_SERVER['HTTP_USER_AGENT'] = 'none';
if(	($_SERVER['REMOTE_ADDR'] == '216.180.167.121')|| 
	($_SERVER['REMOTE_ADDR'] == '216.48.124.25')||
	($_SERVER['REMOTE_ADDR'] == '192.168.51.31')||
	($_SERVER['REMOTE_ADDR'] == '127.0.0.1')
  )
{
	//echo 'office';
}else{
	//echo $_SERVER['REMOTE_ADDR'];
	//require(dirname(__FILE__) . '/error_diplay.php');
	//exit;
} 
global $timeStartLog;
global $leon;
$leon = "leon:";
$timeStartLog = microtime(true);

/**
 * BluApplication base path
 */
define('BLUPATH_BASE', dirname(__FILE__));
global $dinfo;
$dinfo = '1';
// Load config
require_once(BLUPATH_BASE.'/config.php');

error_reporting(E_ALL);
// Full error reporting for debug
if (DEBUG) { error_reporting(E_ALL | E_STRICT); }

// Record script execution start time
if (DEBUG) { $startTime = microtime(true); }

/**
 * Class autoloader for libraries
 */
function __autoload($className)
{
	// Miscellaneous
	if (file_exists(BLUPATH_BASE.'/shared/lib/'.$className.'.php')) {
		require_once(BLUPATH_BASE.'/shared/lib/'.$className.'.php');
		return;
	}
	if (file_exists(BLUPATH_BASE.'/shared/objects/'.$className.'.php')) {
		require_once(BLUPATH_BASE.'/shared/objects/'.$className.'.php');
		return;
	}
	if (file_exists(BLUPATH_BASE.'/shared/interfaces/'.$className.'.php')) {
		require_once(BLUPATH_BASE.'/shared/interfaces/'.$className.'.php');
		return;
	}
	
	// Allows more complex inheritance while staying sane.
	$fail = false;
	$siteId = BluApplication::getSetting('siteId');
	
	$path = BLUPATH_BASE.'/'.SITEEND;
	if (strpos($className, ucfirst($siteId)) === 0) {
		$path .= '/'.$siteId;
		$className = substr($className, strlen($siteId));
	} else {
		$path .= '/base';
	}
	
	if (strpos($className, 'Controller') !== false) {
		$path .= '/controllers';
	} else {
		$fail = true;
	}
	
	if (!$fail && file_exists($path.'/'.$className.'.php')) {
		require_once($path.'/'.$className.'.php');
		return;
	}
	
	// Fail
	//trigger_error('Could not find '.$className, E_USER_ERROR);
}

// Include HTML purifier
if (PURIFY) {
	require_once 'HTMLPurifier.auto.php';
}

// Create application
$bluApplication = BluApplication::getInstance();

// Dispatch to requested option
$bluApplication->dispatch();

// Render option
$bluApplication->render();

//echo $leon;

//if($_SERVER['REMOTE_ADDR'] === '216.180.167.121'){
//echo "<!--\r\n";
//print_r($dinfo);
//echo "\r\n" . $_SERVER['SERVER_ADDR'];
//echo "\r\n-->";
//}
//$memlist = fopen ('/var/www/html/cache/memory.csv', 'a+');
//fwrite ($memlist, $_SERVER['REQUEST_URI'].','.memory_get_usage().','.memory_get_usage(true).','.memory_get_peak_usage().','.memory_get_peak_usage(true)."\n");
//fclose ($memlist);
if($_SERVER["SERVER_ADDR"] == "192.168.51.76")
{
//	echo $_SERVER["SERVER_ADDR"];
}
?>
