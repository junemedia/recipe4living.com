<?php
exit;

/*

CODE COMMENTED OUT BY SAMIR

mysql_pconnect ("192.168.51.53", "r4ldbuser", "acgnW3FsFSD2");
mysql_select_db ("recipe4living_staging");

$today = date("Y-m-d");
$title = addslashes($_GET['title']);
$url = addslashes($_GET['url']);

if ($url !='' && $title !='') {
	$check = "SELECT * FROM RecipesDisplay WHERE title=\"$title\" AND dateAdded=\"$today\" AND url=\"$url\"";
	$check_result = mysql_query($check);
	$count = mysql_num_rows($check_result);
	
	if ($count == 0) {
		$insert = "INSERT IGNORE INTO RecipesDisplay (title,url,count,dateAdded)
					VALUES (\"$title\",\"$url\",'1',\"$today\")";
		$insert_result = mysql_query($insert);
	} else {
		$update = "UPDATE RecipesDisplay
					SET count=count+1
					WHERE dateAdded=\"$today\" AND url=\"$url\" AND title=\"$title\"";
		$update_result = mysql_query($update);
	}
}*/
/*http://dev.recipe4living.com/r4l/RecipesDisplay.php?title=test&url=/recipes/test.htm*/
?>