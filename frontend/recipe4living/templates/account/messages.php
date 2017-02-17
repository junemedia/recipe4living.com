
	<div id="main-content" class="static">
		<div id="column-container">
			<div id="panel-center" class="column">
				<div id="article-list" style="overflow: hidden;">
					<div id="list-heading" class="rounded">
						<div class="content">
							<h2>My Messages</h2>
						</div>
					</div>
					<div class="rounded">
						<div class="text-content">
							
							<div style="padding: 10px 0px 0px 15px;" class="fl">
								<?php include(BLUPATH_TEMPLATES.'/account/messages/nav.php'); ?>
							</div>
						</div>
					</div>
					<div id="recipes_listing" class="rounded">
						<div class="text-content">
							<div class="clear"></div>
							<?= Messages::getMessages(); ?>
							<div class="messaging-wrap">
								<?php $this->messages_listing(); ?>
							</div>
							<div class="clear"></div>
						</div>
					</div>						
				</div>
			</div>

			<?= Messages::getMessages(); ?>
	
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
