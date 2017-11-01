
	<div id="nav-left" class="rounded">
		<h2 class="main"><a style="color: white;" href="<?= SITEURL.$titleLink; ?>"><?= $title; ?></a></h2>
		
		<div class="content">
			<?php $i = 0; if ($count = count($subHierarchy)) { ?>
		
			<?php if (Template::get('displayParent')) { ?>
			
			<a class="parent" href="<?= SITEURL.$parentFilter['link']; ?>">&laquo; <?= $parentFilter['name']; ?></a>
			
			<?php } ?>
			
			<div class="block<?= $i == $count ? ' last' : ''; ?>">
				<ul class="text-content">
					<?php foreach ($subHierarchy as $element) { $i++; ?>
					<li>
						<?php if ($currentFilter && ($element['slug'] == $currentFilter['slug'])) { ?>
						
						<?php 
						
						 
						switch($element['name'])
					{					
					case "Wolfgang Puck":
					echo "Wolfgang Puck";
					break;
					case "Meatless":
					echo "Meatless Recipes";
					break;
					case "Soups and Stews":
					echo "Soups and Stews";
					break;
					case "Sauces and Seasonings":
					echo "Sauces and Seasonings";
					break;
					case "Sandwiches":
					echo "Sandwiches";
					break;
					case "Quick & Easy":
					echo "Quick & Easy";
					break;
					case "Jams, Jellies & Butters":
					echo "Jams, Jellies & Butters";
					break;
					case "Campbell's Recipes":
					echo "Campbell's Recipes";
					break;
					case "Cheap":
					echo "Cheap Recipes";
					break;
					case "Bath":
					echo "Bath Recipes";
					break;
					case "Hair":
					echo "Hair Recipes";
					break;
					case "Lotions":
					echo "Lotions Recipes";
					break;
					case "Makeup":
					echo "Makeup Recipes";
					break;					
					case "Cheesy":
					echo "Cheesy Recipes";
					break;
					case "Creamy":
					echo "Creamy Recipes";
					break;
					case "Crispy":
					echo "Crispy Recipes";
					break;
					case "Crunchy":
					echo "Crunchy Recipes";
					break;
					case "Light & Fluffy":
					echo "Light & Fluffy Recipes";
					break;
					case "Rich":
					echo "Rich Recipes";
					break;
					case "Salty":
					echo "Salty Recipes";
					break;
					case "Smooth":
					echo "Smooth Recipes";
					break;
					case "Spicy":
					echo "Spicy Recipes";
					break;
					case "Sweet":
					echo "Sweet Recipes";
					break;
					case "Tart":
					echo "Tart Recipes";
					break;
					case "Potatoes":
					echo "Potatoe Recipes";
					break;
					case "Stuffings":
					echo "Stuffing Recipes";
					break;
					case "Fall":
					echo "Fall Recipes";
					break;
					case "Spring":
					echo "Spring Recipes";
					break;
					case "Summer":
					echo "Summer Recipes";
					break;
					case "Winter":
					echo "Winter Recipes";
					break;
					case "Bake":
					echo "Bake Recipes";
					break;
					case "Fry":
					echo "Fry Recipes";
					break;
					case "Grill":
					echo "Grill Recipes";
					break;
					case "Microwave":
					echo "Microwave Recipes";
					break;
					case "No Cook":
					echo "No Cook Recipes";
					break;
					case "Pressure Cooker":
					echo "Pressure Cooker Recipes";
					break;
					case "Quick":
					echo "Quick Recipes";
					break;
					case "Stir Fry":
					echo "Stir Fry Recipes";
					break;
					case "Beans and Legumes":
					echo "Beans and Legume Recipes";
					break;
					case "Cheese and Dairy":
					echo "Cheese and Dairy Recipes";
					break;
					case "Chocolate":
					echo "Chocolate Recipes";
					break;
					case "Duck":
					echo "Duck Recipes";
					break;
					case "Eggs":
					echo "Eggs Recipes";
					break;
					case "Fish":
					echo "Fish Recipes";
					break;
					case "Fruit":
					echo "Fruit Recipes";
					break;
					case "Grains":
					echo "Grains Recipes";
					break;
					case "Shellfish":
					echo "Shellfish Recipes";
					break;
					case "Soy/Tofu":
					echo "Soy/Tofu Recipes";
					break;
					case "Wild Game":
					echo "Wild Game Recipes";
					break;
					case "Beef":
					echo "Beef Recipes";
					break;
					case "Chicken":
					echo "Chicken Recipes";
					break;
					case "Fish and Seafood":
					echo "Fish and Seafood Recipes";
					break;
					case "Lamb":
					echo "Lamb Recipes";
					break;
					case "Other":
					echo "Other Recipes";
					break;
					case "Pork":
					echo "Pork Recipes";
					break;
					case "Turkey":
					echo "Turkey Recipes";
					break;
					case "Dairy-free":
					echo "Dairy-free Recipes";
					break;
					case "Egg-free":
					echo "Egg-free Recipes";
					break;
					case "High Fiber":
					echo "High Fiber Recipes";
					break;
					case "Low Calorie":
					echo "Low Calorie Recipes";
					break;
					case "Low Carb":
					echo "Low Carb Recipes";
					break;
					case "Low Fat":
					echo "Low Fat Recipes";
					break;
					case "Low Sodium":
					echo "Low Sodium Recipes";
					break;
					case "Low Sugar/Sugar Free":
					echo "Low Sugar/Sugar Free Recipes";
					break;
					case "Vegan":
					echo "Vegan Recipes";
					break;
					case "Vegetarian":
					echo "Vegetarian Recipes";
					break;
					case "Wheat/Gluten-free":
					echo "Wheat/Gluten-free Recipes";
					break;
					case "African":
					echo "African Recipes";
					break;
					case "Cajun/Creole":
					echo "Cajun/Creole Recipes";
					break;
					case "Central/South American":
					echo "Central/South American Recipes";
					break;
					case "Chinese":
					echo "Chinese Recipes";
					break;
					case "Eastern European":
					echo "Eastern European Recipes";
					break;
					case "English":
					echo "English Recipes";
					break;
					case "French":
					echo "French Recipes";
					break;
					case "German":
					echo "German Recipes";
					break;
					case "Indian":
					echo "Indian Recipes";
					break;
					case "Irish":
					echo "Irish Recipes";
					break;
					case "Italian":
					echo "Italian Recipes";
					break;
					case "Japanese":
					echo "Japanese Recipes";
					break;
					case "Korean":
					echo "Korean Recipes";
					break;
					case "Mediterranean":
					echo "Mediterranean Recipes";
					break;
					case "Mexican":
					echo "Mexican Recipes";
					break;
					case "Middle Eastern":
					echo "Middle Eastern Recipes";
					break;
					case "Scandinavian":
					echo "Scandinavian Recipes";
					break;
					case "Thai":
					echo "Thai Recipes";
					break;
					case "Vietnamese":
					echo "Vietnamese Recipes";
					break;					
					case "Appetizers":
					echo "Appetizer Recipes";
					break;
					case "Beauty":
					echo "Beauty Recipes";
					break;
					case "Drink":
					echo "Drink Recipes";
					break;
					case "Breads":
					echo "Bread Recipes";
					break;
					case "Brand":
					echo "Brand Recipes";
					break;
					case "Breakfast":
					echo "Breakfast Recipes";
					break;
					case "Cakes":
					echo "Cake Recipes";
					break;
					case "Casseroles":
					echo "Casserole Recipes";
					break;
					case "Copycat":
					echo "Copycat Recipes";
					break;
					case "Cocktails":
					echo "Cocktail Recipes";
					break;
					case "Comfort Foods":
					echo "Comfort Food Recipes";
					break;
					case "Cookies":
					echo "Cookie Recipes";
					break;
					case "Crockpot":
					echo "Crockpot Recipes";
					break;
					case "Desserts":
					echo "Dessert Recipes";
					break;
					case "Diabetic":
					echo "Diabetic Recipes";
					break;
					case "Dips":
					echo "Dip Recipes";
					break;
					case "Fruits":
					echo "Fruit Recipes";
					break;
					case "Healthy":
					echo "Healthy Recipes";
					break;
					case "Kid-Friendly":
					echo "Kid-Friendly Recipes";
					break;
					case "Lunch":
					echo "Lunch Recipes";
					break;
					case "Muffins":
					echo "Muffin Recipes";
					break;
					case "Pastas":
					echo "Pasta Recipes";
					break;
					case "Pies":
					echo "Pie Recipes";
					break;
					case "Pizzas":
					echo "Pizza Recipes";
					break;
					case "Punch":
					echo "Punch Recipes";
					break;
					case "Rolls and Biscuits":
					echo "Rolls and Biscuit Recipes";
					break;
					case "Salads":
					echo "Salad Recipes";
					break;
					case "Scones":
					echo "Scone Recipes";
					break;
					case "Seasons":
					echo "Seasonal Recipes";
					break;
					case "Sides":
					echo "Side Dish Recipes";
					break;
					case "Snacks":
					echo "Snack Recipes";
					break;
					case "Smoothies":
					echo "Smoothie Recipes";
					break;
					case "Spreads":
					echo "Spread Recipes";
					break;
					case "Vegetables":
					echo "Vegetable Recipes";
					break;
					default:
					$element['name'];
					}
						
						?>
						<?php if (!empty($element['values'])) { ?>
						<ul class="expanded">
							<?php 
								foreach ($element['values'] as $childElement) {
									
									// Hidden
									if (!$childElement['display']) {
										continue;
									}
									
									// Internal
									if ($childElement['internal']) {
										continue;
									}
									
									// Display
									
								?>
							<li><a href="<?= SITEURL.$childElement['link']; ?>"><?= $childElement['name']; ?></a></li>
							<?php
								}
							?>
						</ul>
						<?php } ?>
						
						<?php } else { ?>
						<?php
						switch($element['name'])
					{
					case "Bath":
					echo "<li><a href=/recipes/beauty/bath>Bath Recipes</a></li>";
					break;
					case "Hair":
					echo "<li><a href=/recipes/beauty/hair>Hair Recipes</a></li>";
					break;
					case "Lotions":
					echo "<li><a href=/recipes/beauty/lotions>Lotions Recipes</a></li>";
					break;
					case "Makeup":
					echo "<li><a href=/recipes/beauty/makeup>Makeup Recipes</a></li>";
					break;
					case "Cheesy":
					echo "<li><a href=/recipes/taste_and_texture/cheesy>Cheesy Recipes</a></li>";
					break;
					case "Creamy":
					echo "<li><a href=/recipes/taste_and_texture/creamy>Creamy Recipes</a></li>";
					break;
					case "Crispy":
					echo "<li><a href=/recipes/taste_and_texture/crispy>Crispy Recipes</a></li>";
					break;
					case "Crunchy":
					echo "<li><a href=/recipes/taste_and_texture/crunchy>Crunchy Recipes</a></li>";
					break;					
					case "Light & Fluffy":
					echo "<li><a href=/recipes/taste_and_texture/light_fluffy>Light & Fluffy Recipes</a></li>";
					break;
					case "Rich":
					echo "<li><a href=/recipes/taste_and_texture/rich>Rich Recipes</a></li>";
					break;
					case "Salty":
					echo "<li><a href=/recipes/taste_and_texture/salty>Salty Recipes</a></li>";
					break;
					case "Smooth":
					echo "<li><a href=/recipes/taste_and_texture/smooth>Smooth Recipes</a></li>";
					break;
					case "Spicy":
					echo "<li><a href=/recipes/taste_and_texture/spicy>Spicy Recipes</a></li>";
					break;
					case "Sweet":
					echo "<li><a href=/recipes/taste_and_texture/sweet_2>Sweet Recipes</a></li>";
					break;
					case "Tart":
					echo "<li><a href=/recipes/taste_and_texture/tart>Tart Recipes</a></li>";
					break;
					case "Christmas":
					echo "<li><a href=/recipes/special_occasion/christmas>Christmas Recipes</a></li>";
					break;
					case "Easter":
					echo "<li><a href=/recipes/special_occasion/easter>Easter Recipes</a></li>";
					break;
					case "Fourth of July":
					echo "<li><a href=/recipes/special_occasion/fourth_of_july>Fourth of July Recipes</a></li>";
					break;
					case "Halloween":
					echo "<li><a href=/recipes/special_occasion/halloween>Halloween Recipes</a></li>";
					break;
					case "Jewish Holidays":
					echo "<li><a href=/recipes/special_occasion/jewish_holidays>Jewish Holiday Recipes</a></li>";
					break;
					case "Kwanzaa":
					echo "<li><a href=/recipes/special_occasion/kwanzaa>Kwanzaa Recipes</a></li>";
					break;
					case "New Years":
					echo "<li><a href=/recipes/special_occasion/new_years>New Years Recipes</a></li>";
					break;
					case "St. Patrick's Day":
					echo "<li><a href=/recipes/special_occasion/st_patrick_s_day>St. Patrick's Day Recipes</a></li>";
					break;
					case "Super Bowl":
					echo "<li><a href=/recipes/special_occasion/super_bowl>Super Bowl Recipes</a></li>";
					break;
					case "Thanksgiving":
					echo "<li><a href=/recipes/special_occasion/thanksgiving>Thanksgiving Recipes</a></li>";
					break;
					case "Valentine's Day":
					echo "<li><a href=/recipes/special_occasion/valentine_s_day>Valentine's Day Recipes</a></li>";
					break;
					case "Potatoes":
					echo "<li><a href=/recipes/side/potatoes_2>Potato Recipes</a></li>";
					break;
					case "Stuffings":
					echo "<li><a href=/recipes/sides/stuffings>Stuffing Recipes</a></li>";
					break;					
					case "Fall":
					echo "<li><a href=/recipes/seasons/fall>Fall Recipes</a></li>";
					break;
					case "Spring":
					echo "<li><a href=/recipes/seasons/spring>Spring Recipes</a></li>";
					break;
					case "Summer":
					echo "<li><a href=/recipes/seasons/summer>Summer Recipes</a></li>";
					break;
					case "Winter":
					echo "<li><a href=/recipes/seasons/winter>Winter Recipes</a></li>";
					break;
					case "Bake":
					echo "<li><a href=/recipes/preparation/bake>Bake Recipes</a></li>";
					break;
					case "Fry":
					echo "<li><a href=/recipes/preparation/fry>Fry Recipes</a></li>";
					break;
					case "Grill":
					echo "<li><a href=/recipes/preparation/grill>Grill Recipes</a></li>";
					break;
					case "Microwave":
					echo "<li><a href=/recipes/preparation/microwave>Microwave Recipes</a></li>";
					break;
					case "No Cook":
					echo "<li><a href=/recipes/preparation/no_cook>No Cook Recipes</a></li>";
					break;
					case "Pressure Cooker":
					echo "<li><a href=/recipes/preparation/pressure_cooker>Pressure Cooker Recipes</a></li>";
					break;
					case "Quick":
					echo "<li><a href=/recipes/preparation/quick>Quick Recipes</a></li>";
					break;
					case "Stir Fry":
					echo "<li><a href=/recipes/preparation/stir_fry_2>Stir Fry Recipes</a></li>";
					break;
					case "Beans and Legumes":
					echo "<li><a href=/recipes/main_ingredients/beans_and_legumes>Beans and Legume Recipes</a></li>";
					break;
					case "Cheese and Dairy":
					echo "<li><a href=/recipes/main_ingredients/cheese_and_dairy>Cheese and Dairy Recipes</a></li>";
					break;
					case "Chocolate":
					echo "<li><a href=/recipes/main_ingredients/chocolate_2>Chocolate Recipes</a></li>";
					break;
					case "Duck":
					echo "<li><a href=/recipes/main_ingredients/duck>Duck Recipes</a></li>";
					break;
					case "Eggs":
					echo "<li><a href=/recipes/main_ingredients/eggs_2>Eggs Recipes</a></li>";
					break;
					case "Fish":
					echo "<li><a href=/recipes/main_ingredients/fish_3>Fish Recipes</a></li>";
					break;
					case "Fruit":
					echo "<li><a href=/recipes/main_ingredients/fruit_3>Fruit Recipes</a></li>";
					break;
					case "Grains":
					echo "<li><a href=/recipes/main_ingredients/grains>Grains Recipes</a></li>";
					break;
					case "Shellfish":
					echo "<li><a href=/recipes/main_ingredients/shellfish>Shellfish Recipes</a></li>";
					break;
					case "Soy/Tofu":
					echo "<li><a href=/recipes/main_ingredients/soy_tofu>Soy/Tofu Recipes</a></li>";
					break;
					case "Wild Game":
					echo "<li><a href=/recipes/main_ingredients/wild_game>Wild Game Recipes</a></li>";
					break;
					case "Beef":
					echo "<li><a href=/recipes/main_courses/beef>Beef Recipes</a></li>";
					break;
					case "Chicken":
					echo "<li><a href=/recipes/main_courses/chicken_4>Chicken Recipes</a></li>";
					break;
					case "Fish and Seafood":
					echo "<li><a href=/recipes/main_courses/fish_and_seafood>Fish and Seafood Recipes</a></li>";
					break;
					case "Lamb":
					echo "<li><a href=/recipes/main_courses/lamb_2>Lamb Recipes</a></li>";
					break;
					case "Other":
					echo "<li><a href=/recipes/main_courses/other>Other Recipes</a></li>";
					break;
					case "Pork":
					echo "<li><a href=/recipes/main_courses/pork_2>Pork Recipes</a></li>";
					break;
					case "Turkey":
					echo "<li><a href=/recipes/main_courses/turkey_2>Turkey Recipes</a></li>";
					break;
					case "African":
					echo "<li><a href=/recipes/global_cuisines/african>African Recipes</a></li>";
					break;
					case "Cajun/Creole":
					echo "<li><a href=/recipes/global_cuisines/cajun_creole>Cajun/Creole Recipes</a></li>";
					break;
					case "Central/South American":
					echo "<li><a href=/recipes/global_cuisines/central_south_american>Central/South American Recipes</a></li>";
					break;
					case "Chinese":
					echo "<li><a href=/recipes/global_cuisines/chinese>Chinese Recipes</a></li>";
					break;
					case "Eastern European":
					echo "<li><a href=/recipes/global_cuisines/eastern_european>Eastern European Recipes</a></li>";
					break;
					case "English":
					echo "<li><a href=/recipes/global_cuisines/english>English Recipes</a></li>";
					break;
					case "French":
					echo "<li><a href=/recipes/global_cuisines/french>French Recipes</a></li>";
					break;
					case "German":
					echo "<li><a href=/recipes/global_cuisines/german>German Recipes</a></li>";
					break;
					case "Indian":
					echo "<li><a href=/recipes/global_cuisines/indian>Indian Recipes</a></li>";
					break;
					case "Irish":
					echo "<li><a href=/recipes/global_cuisines/irish>Irish Recipes</a></li>";
					break;
					case "Italian":
					echo "<li><a href=/recipes/global_cuisines/italian>Italian Recipes</a></li>";
					break;
					case "Japanese":
					echo "<li><a href=/recipes/global_cuisines/japanese>Japanese Recipes</a></li>";
					break;
					case "Korean":
					echo "<li><a href=/recipes/global_cuisines/korean>Korean Recipes</a></li>";
					break;
					case "Mediterranean":
					echo "<li><a href=/recipes/global_cuisines/mediterranean>Mediterranean Recipes</a></li>";
					break;
					case "Mexican":
					echo "<li><a href=/recipes/global_cuisines/mexican>Mexican Recipes</a></li>";
					break;
					case "Middle Eastern":
					echo "<li><a href=/recipes/global_cuisines/middle_eastern>Middle Eastern Recipes</a></li>";
					break;
					case "Scandinavian":
					echo "<li><a href=/recipes/global_cuisines/scandinavian>Scandinavian Recipes</a></li>";
					break;
					case "Thai":
					echo "<li><a href=/recipes/global_cuisines/thai>Thai Recipes</a></li>";
					break;
					case "Vietnamese":
					echo "<li><a href=/recipes/global_cuisines/vietnamese>Vietnamese Recipes</a></li>";
					break;
					case "Dairy-free":
					echo "<li><a href=/recipes/healthy/dairy_free>Dairy-free Recipes</a></li>";
					break;
					case "Egg-free":
					echo "<li><a href=/recipes/healthy/egg_free>Egg-free Recipes</a></li>";
					break;
					case "High Fiber":
					echo "<li><a href=/recipes/healthy/high_fiber>High Fiber Recipes</a></li>";
					break;
					case "Low Calorie":
					echo "<li><a href=/recipes/healthy/low_calorie>Low Calorie Recipes</a></li>";
					break;
					case "Low Carb":
					echo "<li><a href=/recipes/healthy/low_carb>Low Carb Recipes</a></li>";
					break;
					case "Low Fat":
					echo "<li><a href=/recipes/healthy/low_fat>Low Fat Recipes</a></li>";
					break;
					case "Low Sodium":
					echo "<li><a href=/recipes/healthy/low_sodium>Low Sodium Recipes</a></li>";
					break;
					case "Low Sugar/Sugar Free":
					echo "<li><a href=/recipes/healthy/low_sugar_sugar_free>Low Sugar/Sugar Free Recipes</a></li>";
					break;
					case "Vegan":
					echo "<li><a href=/recipes/healthy/vegan>Vegan Recipes</a></li>";
					break;
					case "Vegetarian":
					echo "<li><a href=/recipes/healthy/vegetarian>Vegetarian Recipes</a></li>";
					break;
					case "Wheat/Gluten-free":
					echo "<li><a href=/recipes/healthy/wheat_gluten_free>Wheat/Gluten-free Recipes</a></li>";
					break;
					case "Appetizers":
					echo "<li><a href=/recipes/appetizers>Appetizer Recipes</a></li>";
					break;
					case "Beauty":
					echo "<li><a href=/recipes/beauty>Beauty Recipes</a></li>";
					break;
					case "Drink":
					echo "<li><a href=/recipes/beverages>Drink Recipes</a></li>";
					break;
					case "Brand":
					echo "<li><a href=/recipes/brand>Brand Recipes</a></li>";
					break;
					case "Breads":
					echo "<li><a href=/recipes/breads>Bread Recipes</a></li>";
					break;
					case "Breakfast":
					echo "<li><a href=/recipes/breakfast>Breakfast Recipes</a></li>";
					break;
					case "Cakes":
					echo "<li><a href=/recipes/desserts/cakes>Cake Recipes</a></li>";
					break;
					case "Casseroles":
					echo "<li><a href=/recipes/casseroles>Casserole Recipes</a></li>";
					break;
					case "Comfort Foods":
					echo "<li><a href=/recipes/comfort_foods>Comfort Food Recipes</a></li>";
					break;
					case "Copycat":
					echo "<li><a href=/recipes/copycat>Copycat Recipes</a></li>";
					break;
					case "Cocktails":
					echo "<li><a href=/recipes/beverages/cocktails>Cocktail Recipes</a></li>";
					break;
					case "Cookies":
					echo "<li><a href=/recipes/desserts/cookies_2>Cookie Recipes</a></li>";
					break;
					case "Crockpot":
					echo "<li><a href=/recipes/crockpot>Crockpot Recipes</a></li>";
					break;
					case "Desserts":
					echo "<li><a href=/recipes/desserts>Dessert Recipes</a></li>";
					break;
					case "Diabetic":
					echo "<li><a href=/recipes/diabetic>Diabetic Recipes</a></li>";
					break;
					case "Dips":
					echo "<li><a href=/recipes/appetizers/dips>Dip Recipes</a></li>";
					break;
					case "Fruits":
					echo "<li><a href=/recipes/fruits>Fruit Recipes</a></li>";
					break;
					case "Healthy":
					echo "<li><a href=/recipes/healthy>Healthy Recipes</a></li>";
					break;
					case "Kid-Friendly":
					echo "<li><a href=/recipes/kid_friendly>Kid-Friendly Recipes</a></li>";
					break;
					case "Lunch":
					echo "<li><a href=/recipes/lunch>Lunch Recipes</a></li>";
					break;
					case "Muffins":
					echo "<li><a href=/recipes/breads/muffins_2>Muffin Recipes</a></li>";
					break;
					case "Pastas":
					echo "<li><a href=/recipes/pastas>Pasta Recipes</a></li>";
					break;
					case "Pies":
					echo "<li><a href=/recipes/desserts/pies>Pie Recipes</a></li>";
					break;
					case "Pizzas":
					echo "<li><a href=/recipes/pizzas>Pizza Recipes</a></li>";
					break;
					case "Punch":
					echo "<li><a href=/recipes/beverages/punch>Punch Recipes</a></li>";
					break;
					case "Rolls and Biscuits":
					echo "<li><a href=/recipes/breads/rolls_and_biscuits>Rolls and Biscuit Recipes</a></li>";
					break;
					case "Salads":
					echo "<li><a href=/recipes/salads>Salad Recipes</a></li>";
					break;
					case "Scones":
					echo "<li><a href=/recipes/breads/scones_2>Scone Recipes</a></li>";
					break;
					case "Seasons":
					echo "<li><a href=/recipes/seasons>Seasonal Recipes</a></li>";
					break;
					case "Sides":
					echo "<li><a href=/recipes/sides>Side Dish Recipes</a></li>";
					break;
					case "Smoothies":
					echo "<li><a href=/recipes/beverages/smoothies>Smoothie Recipes</a></li>";
					break;
					case "Snacks":
					echo "<li><a href=/recipes/snacks>Snack Recipes</a></li>";
					break;
					case "Spreads":
					echo "<li><a href=/recipes/appetizers/spreads>Spread Recipes</a></li>";
					break;
					case "Vegetables":
					echo "<li><a href=/recipes/vegetables>Vegetable Recipes</a></li>";
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
					echo "<li><a href=".SITEURL.$element['link'].">".$element['name']."</a></li>";
					
						}
						
						?>
						
					
						
					<?php } ?>
					</li>
					<?php } ?>
				</ul>
			</div>
			<?php } ?>
		</div>
	</div>

  <?php if (!isset($_GET['cid'])) { $this->_advert('medianet_120x300'); } ?>
