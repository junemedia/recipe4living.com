
	<div id="nav-left" class="rounded">
		<h2 class="main"><?= Template::get('leftNavTitle', 'Browse Recipes'); ?></h2>
		
		<div class="content">
			<?php $i = 0; if ($count = count($links)) { ?>
			<div class="block<?= $i == $count ? ' last' : ''; ?>">
				<ul class="text-content">
					<?php foreach ($links as $link => $name) { $i++; ?>
					 
					<?php
					switch($name)
					{
					case "Appetizers":
					echo "<li><a href=/appetizers>Appetizer Recipes</a></li>";
					break;
					case "Beauty":
					/*echo "<li><a href=/beauty>Beauty Recipes</a></li>";*/
					break;
					case "Drink":
					echo "<li><a href=/beverages>Drink Recipes</a></li>";
					break;
					case "Brand":
					/*echo "<li><a href=/brand>Brand Recipes</a></li>";*/
					break;
					case "Breads":
					/*echo "<li><a href=/breads>Bread Recipes</a></li>";*/
					break;
					case 'Cheap':
					echo "<li><a href=/budget_cooking>Cheap Recipes</a></li>";
					break;
					case "Breakfast":
					echo "<li><a href=/breakfast>Breakfast Recipes</a></li>";
					break;
					
					case "Casseroles":
					echo "<li><a href=/casseroles>Casserole Recipes</a></li>";
					break;
					case "Comfort Foods":
					/*echo "<li><a href=/comfort_foods>Comfort Food Recipes</a></li>";*/
					break;
					case "Copycat":
					echo "<li><a href=/copycat>Copycat Recipes</a></li>";
					break;
					case "Crockpot":
					echo "<li><a href=/crockpot>Crockpot Recipes</a></li>";
					break;
					case "Desserts":
					echo "<li><a href=/desserts>Dessert Recipes</a></li>";
					break;
					case "Diabetic":
					echo "<li><a href=/diabetic>Diabetic Recipes</a></li>";
					break;
					case "Fruits":
					/*echo "<li><a href=/fruits>Fruit Recipes</a></li>";*/
					break;
					case "Healthy":
					/*echo "<li><a href=/healthy>Healthy Recipes</a></li>";*/
					break;
					case "Kid-Friendly":
					/*echo "<li><a href=/kid_friendly>Kid-Friendly Recipes</a></li>";*/
					break;
					case "Lunch":
					/*echo "<li><a href=/lunch>Lunch Recipes</a></li>";*/
					break;
					case "Pastas":
					/*echo "<li><a href=/pastas>Pasta Recipes</a></li>";*/
					break;
					case "Pizzas":
					/*echo "<li><a href=/pizzas>Pizza Recipes</a></li>";*/
					break;
					case "Salads":
					echo "<li><a href=/salads>Salad Recipes</a></li>";
					break;
					case "Seasons":
					/*echo "<li><a href=/seasons>Seasonal Recipes</a></li>";*/
					break;
					case "Sides":
					echo "<li><a href=/sides>Side Dish Recipes</a></li>";
					break;
					case "Snacks":
					/*echo "<li><a href=/snacks>Snack Recipes</a></li>";*/
					break;
					case "Vegetables":
					/*echo "<li><a href=/vegetables>Vegetable Recipes</a></li>";*/
					break;
					case "Campbell's Recipes":
					break;
					case "Jams, Jellies & Butters":
					break;
					case "Main Ingredients":
					break;
					case "Sandwiches":
					break;
					case "Sauces and Seasonings":
					break;
					case "Soups and Stews":
					break;
					case "Taste and Texture":
					break;
					case "Preparation":
					break;
					case "Wolfgang Puck":
					break;
					case "International":
					echo "<li><a href=/global_cuisines>International Recipes</a></li>";
					break;
					case "Holiday":
					echo "<li><a href=/special_occasion>Holiday Recipes</a></li>";
					break;
					case "Meatless":
					echo "<li><a href=/vegetarian_vegan>Meatless Recipes</a></li>";
					break;
					case "Edit my details":
					echo "<li><a title='Edit your profile, upload a photo, or delete your account here.' href=".SITEURL.$link.">".$name."</a></li>";
					break;
					case "Recipe box":
					echo "<li><a title='Store and sort your favorite recipes here.' href=".SITEURL.$link.">".$name."</a></li>";
					break;
					case "My Recipes":
					echo "<li><a title='These are the recipes you have posted to the site.' href=".SITEURL.$link.">".$name."</a></li>";
					break;
					case "Favorite Cookbooks":
					/*echo "<li><a title='Pick the cookbooks you like best and store them here.' href=".SITEURL.$link.">".$name."</a></li>";*/
					continue;
					case "My Cookbooks":
					/*echo "<li><a title='These are the cookbooks you have assembled.' href=".SITEURL.$link.">".$name."</a></li>";*/
					continue;
					case "Create a Cookbook":
					continue;
					case "My Blog Posts":
					echo "<li><a title='Tell us what you&#39;ve been cooking with your own blog post!' href=".SITEURL.$link.">".$name."</a></li>";
					break;
					case "My Messages":
					echo "<li><a title='Write to another user with questions or comments!' href=".SITEURL.$link.">".$name."</a></li>";
					break;
					default:
					echo "<li><a href=".SITEURL.$link.">".$name."</a></li>";
					}
										
					?>
					<?php } ?>

				</ul>
			</div>
			<?php } ?>
		</div>
	</div>

	<?php $this->_advert('WEBSITE_LEFT_BANNER_1'); ?>
