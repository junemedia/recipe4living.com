<div id="main-content" class="recipes">
	<div id="column-container">
		<div id="panel-center" class="column">
			<div id="list-heading" class="rounded">
				<div class="content">
					<h2><?= $title; ?></h2>
				</div>
			</div>
			<div id="recipes_listing">
			
				<?= Messages::getMessages(); ?>
				
				<div class="thumb-list">
					<ul>
					<li class="list">
						<div class="im fl">
							<img alt="<?= isset($item['thumbnail']['title']) ? $item['thumbnail']['title'] : $item['image']['title']; ?>" src="<?= ASSETURL; ?>/itemimages/75/75/1/<?= isset($item['thumbnail']['filename']) ? $item['thumbnail']['filename'] : $item['image']['filename']; ?>" width="75" height="75" />
						</div>
						
						<div class="desc">
							<h5 class="fl">
								<a href="<?= SITEURL.$item['link'].'#'.$item['title']; ?>"><?= $item['title']; ?></a>
							</h5>
							
							<div class="clear"></div>
							
							<div class="text-content">
								<p><?= $item['body']; ?></p>
							</div>
							
							<div class="clear"></div>
						</div>
						
						<div class="clear"></div>
					</li>
					</ul>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		
		<?php Template::startScript(); ?>
		
			/* Article items */
			var articleItems = new ArticleItems($('panel-center'), null, {
				quickView: {
					use: false
				},
				scrollTo: true,
				updateTask: 'view_recipes'
			});
			
		<?php Template::endScript(); ?>
		
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
