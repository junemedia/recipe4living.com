<?php

/*

This script is included from article_details.php and recipe_details.php

We want to include video below "Share Recipe" / "Share Article" section (not all articles/recipes, only selected ones)

Below Template::set is being used in frontend/recipe4living/templates/site/ad/WEBSITE_INLINE_1.php, if we have video in article/recipe, then we want to hide video above disqus code

*/


Template::set('aol_video_within_article','N');
if(!empty($item['video_js'])) {
	echo'<div id="video_container" class="screenonly">'.$item['video_js'].'</div><div class="screenonly"></div>';
	Template::set('aol_video_within_article','Y');
}

?>