
	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">
		
				<div id="share-recipe" class="rounded" style="margin-bottom:10px;"><div class="content">
					<?php include(BLUPATH_TEMPLATES.'/recipes/details/title.php'); ?>
					<h1><?= $pageHeading; ?></h1>
					
					<?php
						if (!$user['username']) {
					?>
							<div class="message message-info">
							Wait! Before you submit your recipe, please make sure you have logged in. 
							You must be <a href="<?= SITEURL ?>/account/login/">logged in</a> to submit a recipe. 
							If you do not have an account, <a href="<?= SITEURL ?>/register/">register here</a>.
							</div>
					<?php } ?>
			
					<?= Messages::getMessages(); ?>
					<div id="form-share-recipe" class="standardform"><div class="formholder">
						<form id="form_recipe_submit" name="form_article_submit" action="#" method="post" enctype="multipart/form-data">
						
						<div class="fieldwrap title">
							<label for="title">Title <span class="red-ast">*</span></label>
							<input type="text" class="textinput required" maxlength="255" name="title" id="form_title" value="<?= $title; ?>" />							
						</div>

						<div class="fieldwrap summary">
							<label for="teaser">Summary</label>
							<textarea id="form_summary" class="textinput required" name="teaser" cols="10" rows="5"><?= $teaser; ?></textarea>
							<small>A short description of your recipe (can include overall taste, appearance, history, etc.)</small>
						</div>
						
						<?php if (Template::get('adminPrivileges')) { ?>
						<div class="fieldwrap video">
							<label for="video_js">Video JavaScript</label>
							<textarea id="form_video_js" class="textinput" name="video_js" cols="10" rows="5"><?= $video_js; ?></textarea>
							<small>A video script for your recipe</small>
						</div>
						<?php } ?>

						<div class="fieldwrap ingredients">
							<label for="ingredients">Ingredients <span class="red-ast">*</span></label>
							<p class="text-content">Place each ingredient on a new line.</p>
							<textarea id="form_ingredients" class="textinput required" name="ingredients" cols="10" rows="10"><?= $ingredients; ?></textarea>
							<small>Please use the following abbreviations: tsp., Tbs., lb., oz., pkg., C., qt.</small>
						</div>

						<div class="fieldwrap directions">
							<label for="body">Directions <span class="red-ast">*</span></label>
							<textarea id="form_directions" class="textinput required" name="body" cols="10" rows="20"><?= $body; ?></textarea>
						</div>

						
						<?php 
							/*if (Template::get('tinyMce')) {
								Template::startScript('load');
						?>
							
							new TinyMCEElement('form_directions', {plugins: ['spellchecker'], document_base_url: '<?= 'http://'.$_SERVER['SERVER_NAME'].FRONTENDSITEURL ?>'});
							
						<?php 
								Template::endScript();
							}*/ ?>
							
						<?php	if (Template::get('adminPrivileges')) {
						?>
							<!--<iframe src="<?= SITEURL . '/assets/upload' ?>" width="460" height="200" frameborder="0"></iframe>-->
						<?php
							}
						?>
						
						<div class="fieldwrap serving">
							<label>Serving Size / Yield <span class="red-ast">*</span></label>
							<div class="option">
								<label class="radio"><input name="outcome" type="radio" value="serving"<?= $outcome == 'serving' ? ' checked="checked"' : ''; ?> /> Serving</label>			
								<div class="clear"></div>
								<input type="text" name="serving" id="form_servings" class="textinput" maxlength="15" value="<?= $servesPeople; ?>" />
								<small>e.g. 2</small>
							</div>
							<div class="option">
								<label class="radio"><input name="outcome" type="radio" value="yield"<?= $outcome == 'yield' ? ' checked="checked"' : ''; ?> /> Yield</label>	
								<div class="clear"></div>
								<div class="fl">
									<input type="text" name="yield_quantity" id="form_yield_number" class="textinput" maxlength="15" value="<?= $yieldQuantity; ?>" />
									<small>e.g. 2</small>
								</div>
								<div class="fl">
									<input type="text" name="yield_measure" id="form_yield" class="textinput yield" maxlength="15" value="<?= $yieldMeasure; ?>" />
									<small>e.g. pounds or cakes</small>
								</div>
							</div>
							<div class="clear"></div>
						</div>
						
						<div class="fieldwrap prep">
							<label for="form_prep">Preparation Time</label>
							<input type="text" class="textinput" maxlength="100" name="preparation_time_quantity" id="form_prep" value="<?= $preparationTimeQuantity; ?>" />
							<select name="preparation_time_measure" id="form_prep_time">
								<option value="minutes"<?= $preparationTimeMeasure == 'minutes' ? ' selected="selected"' : ''; ?>>minutes</option>
								<option value="hours"<?= $preparationTimeMeasure == 'hours' ? ' selected="selected"' : ''; ?>>hours</option>
								<option value="days"<?= $preparationTimeMeasure == 'days' ? ' selected="selected"' : ''; ?>>days</option>
							</select>
						</div>

						<div class="fieldwrap cook">
							<label for="form_cook">Cooking Time</label>
							<input type="text" class="textinput" maxlength="100" name="cooking_time_quantity" id="form_cook" value="<?= $cookingTimeQuantity; ?>" />
							<select name="cooking_time_measure" id="form_prep_time">
								<option value="minutes"<?= $cookingTimeMeasure == 'minutes' ? ' selected="selected"' : ''; ?>>minutes</option>
								<option value="hours"<?= $cookingTimeMeasure == 'hours' ? ' selected="selected"' : ''; ?>>hours</option>
								<option value="days"<?= $cookingTimeMeasure == 'days' ? ' selected="selected"' : ''; ?>>days</option>
							</select>
						</div>
						
						<div class="clear"></div>
						
						<div id="categories_accordion" class="fieldwrap categories">
							<label for="form_category">Categories<?= $requireCategories ? ' <span class="red-ast">*</span>' : ''; ?></label>
							<small>Please choose as many as apply!</small>
							<?php $showCategories = array('Appetizers','Drink','Cheap','Breakfast','Casseroles','Copycat','Crockpot','Desserts','Diabetic','Salads','Sides','International','Holiday','Meatless','Main Courses','Quick & Easy');?>
							<?php foreach ($categories as $parent) { ?>
							<?php if($parent['name']=="Categories"){?>
							<div class="block">
								<a href="javascript:void(0);" title="Click Here To See Details"><h4><?=$parent['name']?></h4></a>
								<ul>								
									<?php foreach ($parent['values'] as $category) { 									
										if(in_array($category['name'],$showCategories))
										{
									?>
										<li><label class="check"><input type="checkbox" name="categories[main_ingredients][<?= $category['slug']; ?>]" value="<?= $category['slug']; ?>"<?= isset($selectedCategories[$parent['slug']][$category['slug']]) || isset($selectedCategories['main_ingredients'][$category['slug']]) ? ' checked="checked"' : ''; ?> /> <?= $category['name']; ?></label></li>
									<?php }
										}?>
								</ul>
								<div class="clear"></div>
							</div>
							<?php } 
							}?>
							<!--
							<div class="block">
								<h4>Main Ingredients</h4>
								<ul>
									<?php foreach ($categories['main_ingredients'] as $category) { ?>
									<li><label class="check"><input type="checkbox" name="categories[main_ingredients][<?= $category['slug']; ?>]" value="<?= $category['slug']; ?>"<?= isset($selectedCategories['main_ingredients'][$category['slug']]) ? ' checked="checked"' : ''; ?> /> <?= $category['name']; ?></label></li>
									<?php } ?>
								</ul>
								<div class="clear"></div>
							</div>
							
							<div class="block">
								<h4>Recipe Type</h4>
								<ul>
									<?php foreach ($categories['recipes'] as $category) { ?>
									<li><label class="check"><input type="checkbox" name="categories[recipes][<?= $category['slug']; ?>]" value="<?= $category['slug']; ?>"<?= isset($selectedCategories['recipes'][$category['slug']]) ? ' checked="checked"' : ''; ?> /> <?= $category['name']; ?></label></li>
									<?php } ?>
								</ul>
								<div class="clear"></div>
							</div>
							
							<div class="block">
								<h4>Special Diet</h4>
								<ul>
									<?php foreach ($categories['healthy'] as $category) { ?>
									<li><label class="check"><input type="checkbox" name="categories[healthy][<?= $category['slug']; ?>]" value="<?= $category['slug']; ?>"<?= isset($selectedCategories['healthy'][$category['slug']]) ? ' checked="checked"' : ''; ?> /> <?= $category['name']; ?></label></li>
									<?php } ?>
								</ul>
								<div class="clear"></div>
							</div>
							
							<div class="block">
								<h4>Preparation</h4>
								<ul>
									<?php foreach ($categories['preparation'] as $category) { ?>
									<li><label class="check"><input type="checkbox" name="categories[preparation][<?= $category['slug']; ?>]" value="<?= $category['slug']; ?>"<?= isset($selectedCategories['preparation'][$category['slug']]) ? ' checked="checked"' : ''; ?> /> <?= $category['name']; ?></label></li>
									<?php } ?>
								</ul>
								<div class="clear"></div>
							</div>

							<div class="block">
								<h4>Global Cuisines</h4>
								<ul>
									<?php foreach ($categories['global_cuisines'] as $category) { ?>
									<li><label class="check"><input type="checkbox" name="categories[global_cuisines][<?= $category['slug']; ?>]" value="<?= $category['slug']; ?>"<?= isset($selectedCategories['global_cuisines'][$category['slug']]) ? ' checked="checked"' : ''; ?> /> <?= $category['name']; ?></label></li>
									<?php } ?>
								</ul>
								<div class="clear"></div>
							</div>

							<div class="block">
								<h4>Special Occasion</h4>
								<ul>
									<?php foreach ($categories['special_occasion'] as $category) { ?>
									<li><label class="check"><input type="checkbox" name="categories[special_occasion][<?= $category['slug']; ?>]" value="<?= $category['slug']; ?>"<?= isset($selectedCategories['special_occasion'][$category['slug']]) ? ' checked="checked"' : ''; ?> /> <?= $category['name']; ?></label></li>
									<?php } ?>
								</ul>
								<div class="clear"></div>
							</div>

							<div class="block">
								<h4>Seasonal</h4>
								<ul>
									<?php foreach ($categories['seasons'] as $category) { ?>
									<li><label class="check"><input type="checkbox" name="categories[seasons][<?= $category['slug']; ?>]" value="<?= $category['slug']; ?>"<?= isset($selectedCategories['seasons'][$category['slug']]) ? ' checked="checked"' : ''; ?> /> <?= $category['name']; ?></label></li>
									<?php } ?>
								</ul>
								<div class="clear"></div>
							</div>

							<div class="block">
								<h4>Tastes &amp; Textures</h4>
								<ul>
									<?php foreach ($categories['taste_and_texture'] as $category) { ?>
									<li><label class="check"><input type="checkbox" name="categories[taste_and_texture][<?= $category['slug']; ?>]" value="<?= $category['slug']; ?>"<?= isset($selectedCategories['taste_and_texture'][$category['slug']]) ? ' checked="checked"' : ''; ?> /> <?= $category['name']; ?></label></li>
									<?php } ?>
								</ul>
								<div class="clear"></div>
							</div>

							<div class="block">
								<h4>Beauty</h4>
								<ul>
									<?php foreach ($categories['beauty'] as $category) { ?>
									<li><label class="check"><input type="checkbox" name="categories[beauty][<?= $category['slug']; ?>]" value="<?= $category['slug']; ?>"<?= isset($selectedCategories['beauty'][$category['slug']]) ? ' checked="checked"' : ''; ?> /> <?= $category['name']; ?></label></li>
									<?php } ?>
								</ul>
								<div class="clear"></div>
							</div>-->
						</div>
						
						<?php Template::startScript(); ?>
						
						var accordionLinks = $('categories_accordion').getElements('h4');
						new Accordion($('categories_accordion'), {
							alwaysHide: true,
							onActive: function(toggler, element) { toggler.removeClass('closed').addClass('open'); },
							onBackground: function(toggler, element) { toggler.removeClass('open').addClass('closed'); }
						}, accordionLinks, '#categories_accordion ul');
						accordionLinks.setStyle('cursor', 'pointer');
						
						<?php Template::endScript(); ?>

						<div class="fieldwrap image">
							<label for="form_image">Attach Image</label>
							<?php if(!empty($image)) { ?>
							<img src="<?= ASSETURL . '/tempimages/150/150/3/' . $image ?>" alt="<?= $default_alt; ?>" />
							<?php } else { ?>
							<img src="<?= ASSETURL ?>/itemimages/150/150/3/<?= isset($item['image']['type']) && $item['image']['type']=='default' ? $item['image']['filename'] : '' ?>" alt="<?= $default_alt; ?>" />
							<?php } ?>
							<label for="form_imgfile">Upload a photo from your computer</label>
							<input type="file" name="default" id="default" class="file text-content" size="30" />
							<?php if((!empty($image) || (isset($item['image']['type']) && $item['image']['type']=='default')) && Template::get('adminPrivileges')) { ?>
								<a href="<?= $deleteImageLink.'?type=default&amp;session='.(!empty($image)?1:0) ?>" onclick="if(!confirm('Are you sure you want to delete this image?')) return false">Delete Image</a>
							<?php } ?>
							<?php if (Template::get('adminPrivileges')) { ?>
								<div class="fieldwrap keywords">
									<label for="form_default_alt">Alt Tag</label>
									<input style="width:230px;" type="text" class="textinput" maxlength="125" name="default_alt" id="default_alt" value="<?= $default_alt; ?>" />
								</div>
							<?php }?>
							<div class="clear"></div>
						</div>

						<?php /*if (Template::get('adminPrivileges')) { ?>
						
						<div class="fieldwrap image">
							<label for="form_image">Attach Thumbnail Image</label>
							<?php if(!empty($thumbnail)) { ?>
							<img src="<?= ASSETURL . '/tempimages/150/150/1/' . $thumbnail ?>" alt="<?= $thumbnail_alt; ?>" />
							<?php } elseif(isset($item['thumbnail'])) { ?>
							<img src="<?= ASSETURL ?>/itemimages/150/150/1/<?= $item['thumbnail']['filename'] ?>" alt="<?= $thumbnail_alt; ?>" />
							<?php } ?>
							<label for="form_imgfile">Upload a photo from your computer</label>
							<input type="file" name="thumbnail" id="thumbnail" class="file text-content" size="30" />
							<?php if(!empty($thumbnail) || (isset($item['thumbnail'])) && Template::get('adminPrivileges')) { ?>
								<a href="<?= $deleteImageLink.'?type=thumbnail&amp;session='.(!empty($thumbnail)?1:0) ?>" onclick="if(!confirm('Are you sure you want to delete this thumbnail?')) return false">Delete Thumbnail</a>
							<?php } ?>
							<?php if (Template::get('adminPrivileges')) { ?>
								<div class="fieldwrap keywords">
									<label for="form_thumbnail_alt">Thumbnail Alt Tag</label> 
									<input style="width:230px;" type="text" class="textinput" maxlength="125" name="thumbnail_alt" id="thumbnail_alt" value="<?= $thumbnail_alt; ?>" />
								</div>
							<?php }?>							
							<div class="clear"></div>
						</div>

						<div class="fieldwrap image">
							<label for="form_image">Attach Featured Listing Image</label>
							<?php if(!empty($featuredImage)) { ?>
							<img src="<?= ASSETURL . '/tempimages/150/150/3/' . $featuredImage ?>" alt="<?= $featured_alt; ?>" />
							<?php } elseif(isset($item['featuredImage'])) { ?>
							<img src="<?= ASSETURL ?>/itemimages/150/150/3/<?= $item['featuredImage']['filename'] ?>" alt="<?= $featured_alt; ?>" />
							<?php } ?>
							<label for="form_imgfile">Upload a photo from your computer</label>
							<input type="file" name="featured" id="featured" class="file text-content" size="30" />
							<?php if(!empty($featuredImage) || isset($item['featuredImage']) && Template::get('adminPrivileges')) { ?>
								<a href="<?= $deleteImageLink.'?type=featured&amp;session='.(!empty($featuredImage)?1:0) ?>" onclick="if(!confirm('Are you sure you want to delete this featured listing image?')) return false">Delete Featured Listing Image</a>
							<?php } ?>
						<?php if (Template::get('adminPrivileges')) { ?>
							<div class="fieldwrap keywords">
								<label for="form_featured_alt">Featured Alt Tag</label> 
								<input style="width:230px;" type="text" class="textinput" maxlength="125" name="featured_alt" id="featured_alt" value="<?= $featured_alt; ?>" />
							</div>
						<?php }?>							
							<div class="clear"></div>
						</div>
						
						<?php }*/ ?>

						<div class="fieldwrap terms">
							<label class="check">
								<input type="checkbox" name="terms" value="y"<?= $terms ? ' checked="checked"' : ''; ?> /> I have read and agree to the <a href="<?= SITEURL ?>/terms" target="_blank">Terms of Use</a>.
							</label>
							<div class="clear"></div>
						</div>

						<?php if (Template::get('adminPrivileges')) { ?>
						
							<div class="fieldwrap keywords">
								<label for="form_keywords">Go Live Date [mm/dd/yyyy]</label>
								<input type="text" class="textinput" maxlength="10" name="go_live_date" id="go_live_date" value="<?= Template::get('goLiveDate'); ?>" />
							</div>
							
							<div class="fieldwrap keywords">
								<label for="form_keywords">Meta Keywords</label>
								<textarea id="form_keywords" class="textinput" name="keywords" cols="10" rows="3"><?= Template::get('keywords'); ?></textarea>
							</div>

							<div class="fieldwrap keywords">
								<label for="form_description">Meta Description</label>
								<textarea id="form_description" class="textinput" name="description" cols="10" rows="5"><?= Template::get('description'); ?></textarea>
							</div>
						
						<?php } ?>
						
						<div class="fieldwrap">
						<button name="<?= (Template::get('adminPrivileges'))?'normalize':'preview';?>" class="button-lg fl" type="submit" value="preview"><span>Continue</span></button>						
<!--					<button name="preview" class="button-lg fl" type="submit" value="preview"><span>Preview your recipe</span></button>
 						<button name="submit" class="button-lg fl" type="submit" value="submit" style="margin-left: 10px"><span>Save your recipe</span></button> -->
						</div>
						
						<div class="clear"></div>
						
						<input type="hidden" name="task" value="<?= $submitTask; ?>" />
						</form>
						
					</div></div>
					
					<?php 
						// Allow admin to add relationships, if editing an existing recipe
						if (Template::get('adminPrivileges') && $this->_itemId && isset($editRelatedLink)) {
					?>
					<div class="clear"></div>
					<div class="fieldwrap" style="margin-top: 30px;">
						<a href="<?= SITEURL.$editRelatedLink; ?>">Edit related recipes</a>
					</div>
					<?php
						}
					?>

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
