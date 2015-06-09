<?php


function getFullListName($list) {
	switch ($list) {
		case "R4L":
			return "Daily Recipes";
			break;
		case "QE":
			return "Quick & Easy Recipes";
			break;
		case "Budget":
			return "Budget Cooking";
			break;
		case "RSVP":
			return "Party Tips & Recipes";
			break;
		case "Crockpot":
			return "Crockpot Creations";
			break;
		case "Casserole":
			return "Casserole Cookin'";
			break;
		case "Copycat":
			return "Copycat Classics";
			break;
	}
}

function isThisBotVisiting() {
	$bot_array = array('http://www.google.com/bot.html','http://search.msn.com/msnbot.htm','http://www.bing.com/bingbot.htm');
	foreach ($bot_array as $bot_url) {
		if (strstr($_SERVER['HTTP_USER_AGENT'],$bot_url)) {
			return true;
		}
	}
	return false;
}

?>
