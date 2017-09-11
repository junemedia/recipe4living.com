
	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">
		
				<div id="rss" class="rounded"><div class="content">

					<h1>Recipe4Living RSS Feeds</h1>
			
					<?= Messages::getMessages(); ?>
	
					<div class="text-content">
						
						<p style="margin:0;">Recipe4Living has a unique RSS feed for each recipe category as well as a <a href="<?= SITEURL; ?>/rss/_all">general RSS feed</a>, which will notify you each time a recipe is added in any category</p>
						
						<ul class="rss-listing">
							
							<li class="parent">
								<h2>Meal / Course</h2>
								<ul class="bullets">
									<li><a href="<?= SITEURL; ?>/rss/breakfast">Breakfast</a></li>
									<li><a href="<?= SITEURL; ?>/rss/lunch">Lunch</a></li>
									<li><a href="<?= SITEURL; ?>/rss/appetizers">Appetizers</a></li>
									<li><a href="<?= SITEURL; ?>/rss/sides">Side Dishes</a></li>
									<li><a href="<?= SITEURL; ?>/rss/main_courses">Main Courses</a></li>
									<li><a href="<?= SITEURL; ?>/rss/desserts">Desserts</a></li>
								</ul>
							</li>
							
							<li class="parent">
								<h2>Special Diet</h2>
								<ul class="bullets">
									<li><a href="<?= SITEURL; ?>/rss/budget_cooking">Budget Cooking</a></li>
									<li><a href="<?= SITEURL; ?>/rss/global_cuisines">Global Cuisines</a></li>
									<li><a href="<?= SITEURL; ?>/rss/healthy">Healthy</a></li>
									<li><a href="<?= SITEURL; ?>/rss/kid_friendly">Kid Friendly</a></li>
									<li><a href="<?= SITEURL; ?>/rss/taste_and_texture">Taste &amp; Texture</a></li>
									<li><a href="<?= SITEURL; ?>/rss/vegetarian_vegan">Vegetarian / Vegan</a></li>
								</ul>
							</li>
							
							<li class="parent">
								<h2><a href="<?= SITEURL; ?>/rss/preparation">Preparation</a></h2>
								<ul class="bullets">
									<li><a href="<?= SITEURL; ?>/rss/quick_easy">Quick &amp; Easy</a></li>
									<li><a href="<?= SITEURL; ?>/rss/crockpot">Crockpot</a></li>
								</ul>
							</li>
							
							<li class="parent">
								<h2>Events</h2>
								<ul class="bullets">
									<li><a href="<?= SITEURL; ?>/rss/seasons">Seasonal</a></li>
									<li><a href="<?= SITEURL; ?>/rss/special_occasion">Special Occasions</a></li>						
								</ul>
							</li>
							
							<li class="parent">
								<h2>Food Type</h2>
								<ul class="bullets">
									<li><a href="<?= SITEURL; ?>/rss/breads">Breads</a></li>
									<li><a href="<?= SITEURL; ?>/rss/casseroles">Casseroles</a></li>
									<li><a href="<?= SITEURL; ?>/rss/fruits">Fruits</a></li>
									<li><a href="<?= SITEURL; ?>/rss/jams_jellies_butters">Jams, Jellies &amp; Butter</a></li>
									<li><a href="<?= SITEURL; ?>/rss/pastas">Pastas</a></li>
									<li><a href="<?= SITEURL; ?>/rss/pizzas">Pizzas</a></li>
									<li><a href="<?= SITEURL; ?>/rss/sandwiches">Sandwiches</a></li>
									<li><a href="<?= SITEURL; ?>/rss/sauces_and_seasonings">Sauces &amp; Seasonings</a></li>
									<li><a href="<?= SITEURL; ?>/rss/soups_and_stews">Soups &amp; Stews</a></li>
									<li><a href="<?= SITEURL; ?>/rss/vegetables">Vegetables</a></li>
								</ul>
							</li>
							
							<li class="parent">
								<h2>Other</h2>
								<ul class="bullets">
									<li><a href="<?= SITEURL; ?>/rss/beauty">Beauty</a></li>
									<li><a href="<?= SITEURL; ?>/rss/wolfgang_puck">Celebrity Chef: Wolfgang Puck</a></li>
								</ul>
							</li>
						</ul>
						
					</div>

				</div></div>

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
