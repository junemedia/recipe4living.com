
	<?php Template::startScript(); ?>
	
		var slideshowItems = new ArticleItems($('slideshow_listing'), null, {
			quickView: {
				use: false
			},
			scrollTo: true,
			updateTask: 'slideshowitem'
		});
		
	<?php Template::endScript(); ?>
	
	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">
				
				<div class="search-nav text-content screenonly">
					<a href="<?= SITEURL ?>/slideshows" class="back fl">Back to all slideshows</a>
					<div class="clear"></div>
				</div>
				
				<div id="slideshow-title" class="rounded">
					<div class="content">
						<h2><?= $slideshow['title'] ?></h2>
						<div class="clear"></div>
					</div>
				</div>
						
				<div id="slideshow_listing" class="rounded">
					<?php $this->slideshowitem() ?>
				</div>
				
				<div class="clear"></div>
				
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
