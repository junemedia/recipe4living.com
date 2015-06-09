
	<?php Messages::getMessages(); ?>


	
	<div id="main-content" class="index">

		<div id="column-container">
        

			
            
            <div id="lc" style="overflow: hidden; width: 650px; margin-left: -160px; float: left;">
                <!-- SlideShow -->  
                <div>
                <?php $this->_box('slideshow', array('limit' => 7)); ?>
                    <div class="homepage conversationalist ad">
                    <!-- REPLACE CODE WITH LIVE VERSION PER MAXINE'S REQUEST - by Samir Patel - /frontend/recipe4living/template/index/landing.php -->
                    <!-- FM Test STAMP ("Home") 500x250 Zone -->
                    <script type='text/javascript' src='http://static.fmpub.net/zone/2560'></script>
                    <!-- FM Test STAMP ("Home") 500x250 Zone -->
                    </div>   
             </div>
                <!-- Center -->
                <div id="panel-center" class="column">

                    <?php //$this->_box('featured_articles', array('limit' => 20)); //getting max 20 and then shuffle them to show 3?>

                    <?php //include(BLUPATH_TEMPLATES.'/index/landing/top_recipes.php') ?>
                    <?php //include(BLUPATH_TEMPLATES.'/index/landing/recently_added_recipes.php') ?>
                    

                    
                    <?php $this->_box('middle_featured_recipes', array('limit' => 4,'boxid'=>33)); ?>
                    <div class="clear"></div>

                    <div id="top_recipes" class="toprecipes rounded half fr">
                        <div id="pubexchange_below_content___narrow"></div>
                    </div>                    
                    
                    <?php //$this->_box('daily_tip'); ?>
                    
                    <?php //$this->_box('reference_guides', array('limit' => 10)); ?>

                    <?php //include(BLUPATH_TEMPLATES.'/index/landing/popular_searches.php') /*Remove 'Popular Searches' Section*/?> 

                    <?php //$this->_box('top_recipes', array('limit' => 20)); //getting max 20 and then shuffle them to show 3  /*Remove 'Top Recipes' Section */?>

                </div>       
                <!-- Left -->  
                <div id="panel-left" class="column">
                    <?php $this->leftnav(); ?>
                </div>            
            </div>

	        <!-- Right -->  
			<div id="panel-right" class="column">
				<?php //include(BLUPATH_TEMPLATES.'/index/landing/giveawayslider.php');?>
				<?php //include(BLUPATH_TEMPLATES.'/site/newsletter.php') ?>      

                <?php /*if (STAGING) { ?>
                <div class="ad">
                    <!-- THIS AD USED TO APPEAR ON THE RIGHT SIDE OF THE PAGE, BUT IT IS BEING REMOVED PER MAXINE'S REQUEST - by Samir Patel - /frontend/recipe4living/template/index/landing.php -->
                    <!-- FM Test STAMP 300x250 expands to 300x600 Zone -->
                    <!--<script type='text/javascript' src='http://static.fmpub.net/zone/2461'></script>-->
                    <!-- FM Test STAMP 300x250 expands to 300x600 Zone -->
                </div>
                <?php }*/ ?>
				<!--
                <div class="our_best">
                    <h2>Our Best Recipe</h2>
                    <div class="content">
                    Our Best will be here.
                    </div>
                </div>-->
				<?php //$this->_box('our_best_recipes'); ?>				
                <div class="ad"><?php $this->_advert('AD_RIGHT1', 'index'); ?></div> 
				<?php $this->_box('right_column_featured_recipes', array('limit' => 1,'boxid'=>34)); ?>
				<?php // $this->landing_featured_question(); ?>

				<?php //$this->_box('daily_chef', array('limit' => 1)); /*Remove Cook of the Day*/?>
				
				<?php // $this->_box('featured_blog_posts', array('limit' => 3)); ?>
				
				<?php //include(BLUPATH_TEMPLATES.'/index/landing/chew_on_that.php') /*Remove Chew on that Blog*/?>
				
				<?php //include(BLUPATH_TEMPLATES.'/index/landing/latest_activity.php') /*Remove Chefs in the Kitchen*/?>

				<?php $this->_box('right_column_feature_collection', array('limit' => 3,'boxid'=>35)); ?>
				
				<?php include(BLUPATH_TEMPLATES.'/polls/polls.php') ?>
				
				<?php //include(BLUPATH_TEMPLATES.'/index/landing/slideshows.php') ?>
				<div class="clear"></div>
				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
		
		</div>

		<div class="clear"></div>
		
		
	</div>

	<?php 
		if (Template::get('showPopup')) {
			
			// I feel so dirty doing this. Eugh. 
			Template::startScript();
	?>
		
		/*window.open('http://www.adviceismeaningless.com/dispatch2.asp?home=70-26175X-L1', '_blank', 'width=790,height=480');*/
		
	<?php 
			Template::endScript();
		}
	?>
