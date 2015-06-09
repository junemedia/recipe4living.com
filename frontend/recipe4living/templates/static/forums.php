
	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">
		
				<div id="forums" class="rounded"><div class="content">

					<h1>Forums</h1>
			
					<?= Messages::getMessages(); ?>
	
					<div class="message message-info">
						Sorry, this page is temporarily under construction.  Please check back soon!
					</div>
					
				</div></div>

			</div>
	
			<div id="panel-left" class="column">
				<?php include(BLUPATH_TEMPLATES.'/nav/left.php') ?>
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
