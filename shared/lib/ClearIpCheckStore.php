<?php
define('BLUPATH_BASE', '/var/www/html/www.workitmom.com');
define('STAGING','');
require_once(BLUPATH_BASE.'/config.php');
require_once(BLUPATH_BASE.'/shared/lib/Database.php');
require_once(BLUPATH_BASE.'/shared/lib/Cache.php');
require_once(BLUPATH_BASE.'/shared/lib/BluApplication.php');

$db = BluApplication::getDatabase();
$query = 'INSERT INTO ipcheckstorearchive SELECT * FROM ipcheckstore WHERE DATE(createDate) <= DATE_SUB(CURDATE(),INTERVAL 15 DAY)';
$db->setQuery($query);
$db->query();

$query = 'DELETE FROM ipcheckstore WHERE DATE(createDate) <= DATE_SUB(CURDATE(),INTERVAL 15 DAY)';
$db->setQuery($query);
$db->query();

$str = '';
$affectRows = mysql_affected_rows();
if($affectRows>=1)
{
	$str .= "WIM ipcheckstore table: affect rows ".$affectRows."\n";
}

$query = 'DELETE FROM ipcheckstorearchive WHERE DATE(createDate) <= DATE_SUB(CURDATE(),INTERVAL 6 MONTH)';
$db->setQuery($query);
$db->query();
$affectRows = mysql_affected_rows();
if($affectRows>=1)
{
	$str .= "WIM ipcheckstorearchive table: affect rows ".$affectRows."\n";
}

if($str != '')
{
	mail(
			'leonz@junemedia.com,howew@junemedia.com',
			'WIM.IP.Wipe.Report - ' . date("Y-m-d H:m:s", time()),
			$str,
			'From: WIM.Wipe <howew@junemedia.com>',
			"Return-Path:<howew@junemedia.com>\r\n"
		);
}
?>
