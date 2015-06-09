
	<div id="left_column">
		<h3>Categories</h3>
		<ul class="categories">
			<?php $this->tree(); ?>
			
			<?php Template::startScript(); ?>
			
			var browse = $(document.body).getElement('#left_column ul.categories');
			//var categories = new Expandable(browse);
			<?php if (!empty($slug)) { // Delay for 1 second, for heights to register. ?>
			//categories.expand.delay(1000, categories, '<?= $slug; ?>');
			<?php } ?>
			
			<?php Template::endScript(); ?>
		</ul>
		
	</div>
	
	<div id="panel">
		<?php $this->panel(); ?>
	</div>