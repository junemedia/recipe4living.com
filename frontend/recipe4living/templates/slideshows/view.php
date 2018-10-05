
	<?php Template::startScript(); ?>
	
		var slideshows = new ArticleItems($('slideshow_list'), null, {
			quickView: {
				use: false
			},
			scrollTo: true,
			updateTask: 'slideshowlist'
		});
		
	<?php Template::endScript(); ?>

	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">
				
				<div id="slideshow-title" class="rounded">
					<div class="content">
						<h2>Slideshows</h2>
						<div class="clear"></div>
					</div>
				</div>
				
				<div id="slideshow_list">
					<?php $this->slideshowlist() ?>
				</div>
				
			</div>
			
			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
	
			<div id="panel-right" class="column">
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			
			<div class="clear"></div>
		</div>
	
	</div>
