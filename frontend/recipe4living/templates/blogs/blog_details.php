
	<div id="main-content" class="recipe">

		<div id="column-container">
			
			<div id="panel-center" class="column">
				<?= Messages::getMessages(); ?>

				<?php if ($referer) { ?>
				<div class="search-nav text-content screenonly">
					
					<a class="back fl" href="<?= SITEURL.$referer; ?>">Back to previous page</a>

					<? /* Could put previous/next links here?
					<span class="fr">
						<a class="screenonly" href="#">Previous</a>
						&nbsp;|&nbsp;
						<a class="screenonly" href="#">Next</a>
					</span>
					*/ ?>
					
					<div class="clear"></div>
				</div>
				<?php } ?>

				<?php include(BLUPATH_TEMPLATES.'/blogs/details/title.php'); ?>
	
				<div id="post">
 
					<div id="post-details">
						<p class="text-content fl screenonly">
							Added: <?php Template::date($item['date']); ?>
						</p>
						
						<ul id="cs-links" class="text-content fr screenonly">
							<li class="first"><script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher=d78b8763-d922-459b-871d-1177ab5d4c23&amp;type=website&amp;buttonText=Email%20or%20Share%20This&amp;style=rotate"></script></li>
							<li><a href="?format=print" class="print-popup">Print</a></li>
							<li id="accesibility">
								<a class="font-decrease" href="#"><img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size-decrease.png" /></a>
								<img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size.png" />
								<a class="font-increase" href="#"><img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size-increase.png" /></a>
							</li>
						</ul>
						
						<div class="clear"></div>
					</div>
		
					<div class="teaser fl">
						<?php $this->_addRating(); ?>
						<?php if ($item['author']) { ?>
						<p class="text-content">
							Shared by <a href="<?= SITEURL; ?>/profile/<?= $item['author']['username']; ?>"><?= $item['author']['username']; ?></a><?php if ($item['author']['location']) { ?>, <br /><?= $item['author']['location']; ?><?php } ?>
							<br /><a href="<?= SITEURL; ?>/blogs/posts/<?= $item['author']['username']; ?>">See all blog posts from this user</a>
						</p>
						<?php } ?>
					</div>
					
					<div class="clear"></div>
			
					<div class="entry">
						<?php $this->view_page(); ?>
						<?php $this->_advert('WEBSITE_INLINE_1'); ?>
					</div>
					
					<?php Template::startScript(); ?>
					
					var fontResizer = new FontResizer($('post').getElement('.entry'), {initial: 0.75});
					$('post').getElement('.font-decrease').addEvent('click', function (event) {
						event.stop();
						fontResizer.decrease();
					});
					$('post').getElement('.font-increase').addEvent('click', function (event) {
						event.stop();
						fontResizer.increase();
					});
					
					<?php Template::endScript(); ?>
			
					<?php include(BLUPATH_TEMPLATES.'/blogs/details/author.php'); ?>
					
					<?php $this->_addReview(); ?>
					
					<?php $this->reviews(); ?>
					
					<div class="clear"></div>
				</div>
			</div>

			<div id="panel-left" class="column screenonly">
				<?php $this->leftnav(); ?>
			</div>
	
			<div id="panel-right" class="column screenonly">
				<?php include(BLUPATH_TEMPLATES.'/site/newsletter.php') ?>
				
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>

				<?php $this->_box('reference_guides', array('limit' => 10)); ?>
				<?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?>
			</div>
			
			<div class="clear"></div>
		</div>

	</div>
