
	<div id="main-content" class="recipes">
		<div id="column-container">
		
			<div id="panel-center" class="column">
				<div id="advanced_search" class="rounded"><div class="content">
					
					<h1>Advanced Search</h1>
					
					<p class="text-content"><?= Template::text('advanced_search_desc'); ?></p>
					
					<form action="<?= SITEURL; ?>/advanced_search" method="post"><div>
						
						<div id="advanced_search_filters" class="fieldwrap accordion">
							<?php foreach ($filters as $filter) { ?>
							<div class="block">
								<h4 class="filter_title text-content"><?= $filter['name']; ?></h4>
								<div class="filter_values">
									<ul>
										<?php foreach ($filter['values'] as $filterValue) { ?>
										<li><label class="check text-content"><input type="checkbox" name="filters[]" value="<?= $filterValue['slug']; ?>" /><?= $filterValue['name']; ?></label></li>
										<?php } ?>
										<div class="clear"></div>
									</ul>
								</div>
								<div class="clear"></div>
							</div>
							<?php } ?>
						</div>
						
						<?php Template::startScript(); ?>
						
						var accordionLinks = $('advanced_search_filters').getElements('.filter_title');
						var accordionSections = $('advanced_search_filters').getElements('.filter_values');
						new BluAccordion(accordionLinks, accordionSections, {
							alwaysHide: true,
							allowMultipleOpen: true,
							display: false
						});
						accordionLinks.setStyle('cursor', 'pointer');
						
						<?php Template::endScript(); ?>
						
						<? /* search type goes here */ ?>
						<input type="hidden" name="searchtype" value="recipe" />
						
						<div id="submit">
							<label for="searchterm">Keywords: <small>e.g. ingredients, username, recipe title</small></label>
							<input type="text" class="textinput fl" name="searchterm" value="" />
							
							<button type="submit" name="submit" value="submit" class="button-lg fl"><span>Search</span></button>
							
							<div class="clear"></div>
						</div>
						
					</div></form>
					
					<?php Template::startScript(); ?>
						
						new FancyForm($('advanced_search_filters').getParent('form'));
						
					<?php Template::endScript(); ?>
					
				</div></div>
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