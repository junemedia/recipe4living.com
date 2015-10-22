
<div id="reports_listing">
	<?php $this->listing(); ?>
	
	<?php Template::startScript(); ?>
	new BrowseArea('reports_listing', {updateTask: 'listing', evalScripts: true});
	<?php Template::endScript(); ?>
</div>