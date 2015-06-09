<?php
/**
 * RedirectUrl Controller
 *
 * @package BluApplication
 * @subpackage FrontendControllers
 */
class RedirectUrl
{
	public static function errorUrl()
	{
	    $db = BluApplication::getDatabase();
		$ip = Request::getVisitorIPAddress();
		$date = date("Y-m-d h:m:s");
        $url= $_SERVER['REQUEST_URI'];
		$sql = "INSERT INTO redirectUrl VALUES ( '" . $url . "', '" . $date . "', '" . $ip . "')";
		$db->setQuery($sql);
		$db->query();
	}
}
?>