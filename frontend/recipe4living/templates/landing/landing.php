
	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">
		
				<div id="about" class="rounded"><div class="content">

					<h1><?= htmlentities($title); ?></h1>
			
					<?= Messages::getMessages(); ?>
	
					<?php include (BLUPATH_TEMPLATES.'/landing/details/header.php'); ?>
					
					<?php $this->_box($boxSlug, array('limit' => 6)); ?>

				</div></div>

			</div>
	
			<div id="panel-left" class="column">
				<?php $this->leftnav($leftLinks); ?>
			</div>
	
			<div id="panel-right" class="column">
				<?php include(BLUPATH_TEMPLATES.'/site/newsletter.php') ?>
				
				<?php //if (STAGING) { ?>
				<div class="ad">
					<!-- REMOVED AD PER MAXINE'S REQUEST by Samir Patel - /frontend/recipe4living/templates/landing/landing.php -->
					<!-- FM Test STAMP 300x250 expands to 300x600 Zone -->
					<!--<script type='text/javascript' src='http://static.fmpub.net/zone/2461'></script>-->
					<!-- FM Test STAMP 300x250 expands to 300x600 Zone -->
				</div>
				<?php //} ?>
				
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>
	
				<?php // $this->landing_featured_question(); ?>
				
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			
			<div class="clear"></div>
		</div>
	
	</div>
