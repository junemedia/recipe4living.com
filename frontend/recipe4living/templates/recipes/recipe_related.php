
	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">
		
				<div id="share-recipe" class="rounded" style="margin-bottom:10px;"><div class="content">

					<h1><?= $pageHeading; ?></h1>
					<?= Messages::getMessages(); ?>

						<?php Template::startScript(); ?>

							var relatedArticleItems = new ArticleItems($('related-articles'), null, {
								quickView: {
									use: false
								},
								scrollTo: true,
								updateTask: 'view_related_articles',
								useFancyForm: true
							});
							
						<?php Template::endScript(); ?>
						
						<?php $this->view_related_articles(); ?>

						<?php if(!Template::get('searchTerm')) { ?>
						
						<div id="related-articles-search"><div class="formholder">
						
							<div style="margin-top: 20px; margin-bottom:10px;"><div class="formholder">
								<form id="related_articles_search_box" name="related_articles_search_box" action="<?= SITEURL.$listingBaseUrl ?>" method="get" class="reloads"><div>
									<input class="textinput simpletext" type="text" title="Enter search keywords..." autocomplete="off" name="searchterm" value="<?= Template::get('searchTerm') ?>" style="width: 365px;" />
									<button name="submit" class="button-lg" type="submit" value="search"><span>Search</span></button>
								</div></form>
							</div></div>
							
							<div class="clear"></div>
							
						</div></div>
						
						<?php } else { ?>
						
									
							<?php $this->view_related_articles_search_results(); ?>
						
						<?php } ?>
							
						<?php Template::startScript(); ?>
						
							var articleItems = new ArticleItems($('related-articles-search'), null, {
								quickView: {
									use: false
								},
								scrollTo: true,
								updateTask: 'view_related_articles_search_results',
								useFancyForm: true
							});
						
						<?php Template::endScript(); ?>
						
				</div></div>

			</div>
	
			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
	
			<div id="panel-right" class="column">
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			
			<div class="clear"></div>
		</div>
	
	</div>
