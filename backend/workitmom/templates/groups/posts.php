
<div id="posts_listing">
	<?php $this->posts_listing(); ?>
	
	<?php Template::startScript(); ?>
	new BrowseArea('posts_listing', {updateTask: 'posts_listing', evalScripts: true});
	<?php Template::endScript(); ?>
</div>