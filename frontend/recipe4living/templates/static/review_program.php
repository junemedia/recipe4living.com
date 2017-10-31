
	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">
		
				<div id="review-program" class="rounded"><div class="content">

					<h1>Product Review Program</h1>
			
					<?= Messages::getMessages(); ?>
	
					<div class="text-content">
						
					<h2>Reviewers</h2>
					<p>Interested in receiving a free cooking-related product and reviewing it for the site? <a href="http://www.internetopinionsnow.com/dispatch2.asp?home=70-28096Y-L1">Please sign up here</a> and we will contact you when your product is ready to send. You can also get more information in our <a href="<?= SITEURL; ?>/product_tester">Product Tester FAQ</a>. Thanks for your help!</p>
					
					<div class="divider"></div>
					
					<h2>Advertisers</h2>
					<p>There is absolutely NO COST for your company to participate in this program. We're simply interested in working with your or your public relations agency to help you obtain real product reviews. We want to help you get valuable feedback on new product launches, updated products and/or existing features. After being reviewed by our enthusiastic users, your product will be featured in our newsletter, on the website and in our blog. If you are interested, please use our <a href="<?= SITEURL; ?>/contact">Contact Form here</a> and we will contact you directly.</p>
						
					</div>

				</div></div>

			</div>
	
			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
	
			<div id="panel-right" class="column">
				<div class="ad"><?php $this->_advert('openx_300x250atf'); ?></div>
	
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			
			<div class="clear"></div>
		</div>
	
	</div>
