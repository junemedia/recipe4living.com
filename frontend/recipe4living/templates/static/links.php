
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
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>
	
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			
			<div class="clear"></div>
		</div>
	
	</div>
