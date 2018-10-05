
	<div id="main-content" class="recipes">
		<div id="column-container">
		
			<div id="panel-center" class="column">
				<?php $this->_listUsers();?>
			</div>
			<?php Template::startScript(); ?>
			
				/* Article items */
				var articleItems = new ArticleItems($('panel-center'), null, {
					quickView: {
						use: false
					},
					scrollTo: true,
					updateTask: '<?= _listUsers; ?>'
				});
				
			<?php Template::endScript(); ?>
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
