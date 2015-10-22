
<div id="items_listing">
	<?php $this->listing(); ?>
	
	<?php Template::startScript(); ?>
	new BrowseArea('items_listing', {updateTask: 'listing', evalScripts: true});
	<?php Template::endScript(); ?>
</div>