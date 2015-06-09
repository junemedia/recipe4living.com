
	<div id="main-content" class="cookbook">
		<div id="column-container">
		
			<div id="panel-center" class="column">
				<div class="rounded">
					<div class="top"></div>
					<div class="content delete">
						Are you sure you want to delete this cookbook?
						
						<div class="tasks">
							<a href="<?= SITEURL.$taskUrl; ?>">Yes</a>
							<a href="<?= SITEURL.$cookbook['link']; ?>">No, take me back</a>
						</div>
					</div>
					<div class="bot"></div>
				</div>
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
