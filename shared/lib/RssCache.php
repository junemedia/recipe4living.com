<?php

// Switch off the autoscript for RSS feeds temply.
//exit();

//echo "Rss Cache Start at: " . microtime(true);

define('BLUPATH_BASE', '/var/www/html/www.workitmom.com');
define('BLUPATH_CACHE','/var/www/html/www.workitmom.com/cache/workitmom');
define('STAGING','');
require_once(BLUPATH_BASE.'/config.php');
require_once(BLUPATH_BASE.'/shared/lib/Database.php');
require_once(BLUPATH_BASE.'/shared/lib/Cache.php');
require_once(BLUPATH_BASE.'/shared/lib/BluApplication.php');

/*function getCache($key,$expire = 3600){
	$cacheFile = BLUPATH_CACHE."/data/$key.cache";
	if((file_exists($cacheFile)) &&((time() - filectime($cacheFile)) > $expire)){
		// expired
		$item = false;
	}else{
		// We will use the cached one.
		$item = include $cacheFile;
	}
	return $item; 
}*/
    
function setCache($key,$content){
	$cacheFile = BLUPATH_CACHE."/data/$key.cache";
	$content = var_export($content, true);
	$content = '<?php return '.$content.'; ?>';

	// Create the cache object.
	file_put_contents($cacheFile, $content,LOCK_EX);
	return true;        
}
function getDatabase()
{
	$database = array();
	if (!is_object($database)) {
		
		/*
		 * Initialize the mutipul databases
		 */
		if ($databases = BluApplication::getSetting('databases', false)) {
			$database = $databases[array_rand($databases)];//echo array_rand($databases);
		} else {
			$database = Array ( 	'databaseHost' => BluApplication::getSetting('databaseHost'),
						'databaseUser' => BluApplication::getSetting('databaseUser'),
						'databasePass' => BluApplication::getSetting('databasePass'),
						'databaseName' => BluApplication::getSetting('databaseName'));
		}
	}
	return $database;
}
function getConnection()
{
	//$config = new Config();
	//$_settings = get_object_vars($config);
	$database = getDatabase();
	$db = mysql_connect($database['databaseHost'], $database['databaseUser'], $database['databasePass'], true) or die('Could not connect to mysql server.' );
	mysql_select_db($database['databaseName'], $db) or die('Could not select database.');
	return $db;
}

function loadAssocList($query,$db,$key='')
{
	$result = mysql_query($query, $db);
	if (!($result)) {
		return null;
	}
	$array = array();
	while ($row = mysql_fetch_assoc($result)) {
		if ($key) {
			$array[$row[$key]] = $row;
		} else {
			$array[] = $row;
		}
	}
	mysql_free_result($result);
	reset($array);

	return $array;
}
	
$t1 = microtime(true);
//$db = BluApplication::getDatabase();
$db = getConnection();

$offset = 0;
$limit = 25;
$cacheKey = "latest_rssfeed";
$query = 'SELECT `blogHosted` FROM `blogs` WHERE `blogHosted` IS NOT NULL';

/* Get raw data */
$blogrecords = loadAssocList($query,$db);

$query = ' CREATE TEMPORARY TABLE rsspost 
			(
			 id int(16) not null,
			 title varchar(255) not null,
			 author varchar(255),
			 url mediumtext,
			 description mediumtext,
			 livedate datetime not null,
			 rsstype varchar(50)
			)';
mysql_query($query, $db);

/*Gernerate query for blogs*/
if(!empty($blogrecords))
{
	foreach($blogrecords as $item)
	{
		$itemsquery = "(SELECT id, post_author, post_title, guid, post_content, post_date"
		." FROM wp_".$item['blogHosted']."_posts WHERE post_status = 'publish')";
		$blogsquery = "SELECT a.id,post_title AS title, b.user_nicename AS author, guid AS url,post_content AS description, post_date AS livedate, 'blogpost' AS rsstype 
				FROM ".$itemsquery." AS a LEFT JOIN wp_users AS b ON a.post_author = b.ID ORDER BY post_date desc LIMIT ".$offset.",".$limit;
		$query = "INSERT INTO rsspost (".$blogsquery.")";
		
		mysql_query($query, $db); 
	}
}

/*$blogsquery = implode(" UNION ALL ",$itemsquery);

$blogsquery = "SELECT a.id,post_title AS title, b.user_nicename AS author, guid AS url,post_content AS description, post_date AS livedate, 'blogpost' AS rsstype 
				FROM (".$blogsquery.") AS a LEFT JOIN wp_users AS b ON a.post_author = b.ID ORDER BY post_date desc LIMIT ".$offset.",".$limit;
				
$query = ' CREATE TEMPORARY TABLE rsspost 
			(
			 id int(16) not null,
			 title varchar(255) not null,
			 author varchar(255),
			 url mediumtext,
			 description mediumtext,
			 livedate datetime not null,
			 rsstype varchar(50)
			)';
mysql_query($query, $db);

$query = "INSERT INTO rsspost (".$blogsquery.")";
mysql_query($query, $db); */

$query = "INSERT INTO rsspost (SELECT articleID,articleTitle AS title, b.username AS author, articleLink AS url,articleBody AS description, articleTime AS livedate,articleType AS rsstypes
FROM article AS a LEFT JOIN users AS b ON a.articleAuthor  = b.UserID WHERE articleLive = 1 AND articleType = 'article' ORDER BY articleTime desc LIMIT ".$offset.",".$limit.")";
mysql_query($query, $db);

$query = 'SELECT * FROM rsspost ORDER BY livedate DESC LIMIT '.$offset.','.$limit;

$records = loadAssocList($query,$db);
$total = mysql_affected_rows();

$query = "DROP TABLE rsspost";
mysql_query($query, $db);
mysql_close($db);

setCache($cacheKey,$records);

$t2 = microtime(true);
//echo (($t2-$t1)*1000).'ms';

$str = "Rss Cache start at ".date("Y-m-d H:i:s",$t1).", end at ".date("Y-m-d H:i:s",$t2).".";
//echo "\nEND Rss Cache at : " . microtime(true);
mail(
		'leonz@junemedia.com,howew@junemedia.com',
		'WIM.RSS.Cache.Report - ' . date("Y-m-d H:m:s", time()),
		$str,
		'From: WIM.RSS.Cache <howew@junemedia.com>\r\n',
		"Return-Path:<howew@junemedia.com>\r\n"
	);
?>
