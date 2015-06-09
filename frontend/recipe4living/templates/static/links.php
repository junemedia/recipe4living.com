
	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">
		
				<div id="links" class="rounded"><div class="content">

					<h1>Advertise On Recipe4Living.com</h1>
			
					<?= Messages::getMessages(); ?>
					
					<div class="text-content">
						

<?php $this->_box('links'); ?>	
												
					</div>
						
				</div>
				</div>

			</div>
	
			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
	
			<div id="panel-right" class="column">
				<?php include(BLUPATH_TEMPLATES.'/site/newsletter.php') ?>
				
				<?php if (STAGING) { ?>
				<div class="ad">
					<!-- FM Test STAMP 300x250 expands to 300x600 Zone -->
					<script type='text/javascript' src='http://static.fmpub.net/zone/2461'></script>
					<!-- FM Test STAMP 300x250 expands to 300x600 Zone -->
				</div>
				<?php } ?>
				
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>
	
				<?php // $this->landing_featured_question(); ?>
				
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			
			<div class="clear"></div>
		</div>
	
	</div>
