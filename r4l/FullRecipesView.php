<?php
exit;

/*

CODE COMMENTED OUT BY SAMIR

mysql_pconnect ("192.168.51.53", "r4ldbuser", "acgnW3FsFSD2");
mysql_select_db ("recipe4living_staging");

$today = date("Y-m-d");
$title = addslashes($_GET['title']);

if ($title !='') {
	$check = "SELECT * FROM FullRecipesDisplay WHERE title=\"$title\" AND dateAdded=\"$today\"";
	$check_result = mysql_query($check);
	$count = mysql_num_rows($check_result);
	
	if ($count == 0) {
		$insert = "INSERT IGNORE INTO FullRecipesDisplay (title,count,dateAdded)
					VALUES (\"$title\",'1',\"$today\")";
		$insert_result = mysql_query($insert);
	} else {
		$update = "UPDATE FullRecipesDisplay
					SET count=count+1
					WHERE dateAdded=\"$today\" AND title=\"$title\"";
		$update_result = mysql_query($update);
	}
}*/
/*http://dev.recipe4living.com/r4l/FullRecipesView.php?title=test*/
?>