	<div id="main-content" class="login">

		<div id="column-container">

			<div id="panel-center" class="column">

				<?= Messages::getMessages(); ?>

				<div class="rounded" id="account-details">
					<div class="top"></div>
					<div class="content">
					<div id="account_icon" class="icon fl"></div>
						<h1>My Profile Details</h1>
						
						<?php
							switch($tab) {
								case 'delete': 
									$this->details_delete(); 
									break;
									
								case 'basic': 
								default:
									$this->details_basic(); 
									break;
							}
						?>
					</div>
					<div class="bot"></div>
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
