
	<div id="main-content" class="recipes">
		<div id="column-container" style="<?php echo ($iscategory)?'padding-left:0px;width:645px;':'';?>">
		
			<div id="panel-center" class="column">
				<?php if($iscategory){?>
				<div class="category_hubs">
					<h3 style="float:left"><a href="/">Home</a> <?php if(isset($breadcrumbs[1][1])){?>&gt;<?php }?>
						<a href="<?php echo isset($breadcrumbs[1][1])?$breadcrumbs[1][1]:'';?>">
							<?php echo isset($breadcrumbs[1][0])?$breadcrumbs[1][0]:'';?>
						</a>
					</h3>
					<!--<div id="accesibility" style="float:right;">
						<a class="font-decrease" href="#"><img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size-decrease.png" /></a>
						<img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size.png" />
						<a class="font-increase" href="#"><img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size-increase.png" /></a>
					</div>-->
				</div> 
				<?php }?>
				<?php $this->view_items(); ?>
			</div>
			
			<?php Template::startScript(); ?>
			
				/* Article items */
/*				var articleItems = new ArticleItems($('panel-center'), null, {
					quickView: {
						use: false
					},
					scrollTo: true
				}); */ // Don't execute this in order to cater for abysmal code.
				
			<?php Template::endScript(); ?>
			<?php if(!$iscategory){?>
			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
			<?php }?>
			
			<div id="panel-right" class="column">
				<?php include(BLUPATH_TEMPLATES.'/site/newsletter.php') ?>
				
				<?php //if (STAGING) { ?>
				<div class="ad">
					<!-- frontend/recipe4living/articles/article_listing.php -->
					<!-- FM Test STAMP 300x250 expands to 300x600 Zone -->
					<!--<script type='text/javascript' src='http://static.fmpub.net/zone/2461'></script>-->
					<!-- FM Test STAMP 300x250 expands to 300x600 Zone -->
				</div>
				<?php //} ?>
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>
				<?php if($iscategory){
					//$this->_box('our_best_recipes'); 
					$this->_box('right_column_featured_recipes', array('limit' => 1,'boxid'=>34));
				}?>
				

				<?php // $this->landing_featured_question(); ?>
				
				<?php if($iscategory){?>
				 <?php $this->_box('right_column_feature_collection', array('limit' => 3,'boxid'=>35)); ?>
				<?php } else {$this->_box('reference_guides', array('limit' => 10));} ?>
				<div class="clear"></div>
				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			
			<div class="clear"></div>
		</div>
	</div>
