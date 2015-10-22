
<div id="deleted_users_listing">
	<?php $this->deleted_listing(); ?>
	
	<?php Template::startScript(); ?>
	new BrowseArea('deleted_users_listing', {updateTask: 'deleted_listing', evalScripts: true});
	<?php Template::endScript(); ?>
</div>