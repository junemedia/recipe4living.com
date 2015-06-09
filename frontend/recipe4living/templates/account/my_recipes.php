	<div id="main-content" class="login">

		<div id="column-container">

			<div id="panel-center" class="column">

				<?= Messages::getMessages(); ?>

				<div id="article-list">
					<?php $this->my_recipes_items(); ?>
				</div>
					
				<?php Template::startScript(); ?>
				
					/* Article items */
					var articleItems = new ArticleItems($('article-list'), null, {
						updateTask: 'my_recipes_items',
						quickView: {
							use: false
						}
					});
					
				<?php Template::endScript(); ?>
			</div>

			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
	
			<div id="panel-right" class="column">
				<?php include(BLUPATH_TEMPLATES.'/site/newsletter.php') ?>
				
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>

				<?php // $this->landing_featured_question(); ?>
				
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			
			<div class="clear"></div>
		
		</div>

	</div>