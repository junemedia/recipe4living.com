
<div id="comments_listing">
	<?php $this->listing(); ?>
	
	<?php Template::startScript(); ?>
	new BrowseArea('comments_listing', {updateTask: 'listing', evalScripts: true});
	<?php Template::endScript(); ?>
</div>