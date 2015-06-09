	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">

				<div id="share-recipe" class="rounded" style="margin-bottom:10px;"><div class="content">

					<h1><?= $pageHeading; ?></h1>

					<?= Messages::getMessages(); ?>

					<div id="form-share-recipe" class="standardform"><div class="formholder">
						<form id="form_article_submit" name="form_article_submit" action="#" method="post" enctype="multipart/form-data">

						<div class="fieldwrap title">
							<label for="title">Title <span class="red-ast">*</span></label>
							<input type="text" class="textinput required" maxlength="255" name="title" id="form_title" value="<?= htmlspecialchars($title); ?>" />
						</div>

						<div class="fieldwrap blurb">
							<label for="teaser">Blurb</label>
							<textarea id="form_summary" class="textinput" name="teaser" cols="10" rows="5"><?= $teaser; ?></textarea>
							<small>A short description for your article</small>
						</div>

						<?php if (Template::get('adminPrivileges')) { ?>
						<div class="fieldwrap video">
							<label for="video_js">Video JavaScript</label>
							<textarea id="form_video_js" class="textinput" name="video_js" cols="10" rows="5"><?= $video_js; ?></textarea>
							<small>A video script for your article</small>
						</div>
						<?php } ?>
						
						<div class="fieldwrap body">
							<label for="body">Body <span class="red-ast">*</span></label>
							<textarea id="form_body" class="textinput required" name="body" cols="10" rows="20"><?= $body; ?></textarea>
						</div>

						<?php /*Template::startScript('load'); Template::set('tinyMce', true); ?>

							new TinyMCEElement('form_body', {plugins: ['spellchecker'], document_base_url: '<?= 'http://'.$_SERVER['SERVER_NAME'].FRONTENDSITEURL ?>'});

						<?php Template::endScript(); */?>

						<div class="clear"></div>

						<?php if (Template::get('adminPrivileges')) { ?>
						<!--<iframe src="<?= SITEURL . '/assets/upload' ?>" width="460" height="200" frameborder="0"></iframe>-->
<!-- 						<iframe src="<?//= SITEURL . '/oversight/assets/?format=simple' ?>" width="460" height="200" frameborder="0"></iframe> -->
						<?php } ?>

						<div class="clear"></div>

						<div id="categories_accordion" class="fieldwrap categories">
							<label for="form_category">Categories<?= $requireCategories ? ' <span class="red-ast">*</span>' : ''; ?></label> 
							<small>Please choose as many as apply!</small>
							<?php $showCategories = array('Articles','Giveaways','Product Reviews','Recipe Collections');?>
							<?php foreach ($categories as $parent) { ?>
							<?php if($parent['name']=="Hints & Tips"){
								$parent['name'] = "Categories";
							?>
							<div class="block">
								<a href="javascript:void(0);" title="Click Here To See Details"><h4><?=$parent['name']?></h4></a>
								<ul>
									<?php foreach ($parent['values'] as $category) {
										if(in_array(trim($category['name']),$showCategories))
										{
									?>
										<li><label class="check"><input type="checkbox" name="categories[main_ingredients][<?= $category['slug']; ?>]" value="<?= $category['slug']; ?>"<?= isset($selectedCategories[$parent['slug']][$category['slug']]) || isset($selectedCategories['main_ingredients'][$category['slug']]) ? ' checked="checked"' : ''; ?> /> <?= $category['name']; ?></label></li>
									<?php 
										}
									} ?>
								</ul>
								<div class="clear"></div>
							</div>
							<?php }
								} ?>
							<!--<div class="block">
								<h4>A Dash of Fun</h4>
								<ul>
									<?php foreach ($categories['a_dash_of_fun'] as $category) { ?>
									<li><label class="check"><input type="checkbox" name="categories[a_dash_of_fun][<?= $category['slug']; ?>]" value="<?= $category['slug']; ?>"<?= isset($selectedCategories['a_dash_of_fun'][$category['slug']]) ? ' checked="checked"' : ''; ?> /> <?= $category['name']; ?></label></li>
									<?php } ?>
								</ul>
								<div class="clear"></div>
							</div>

							<div class="block">
								<h4>Hints and Tips</h4>
								<ul>
									<?php foreach ($categories['hints_tips'] as $category) { ?>
									<li><label class="check"><input type="checkbox" name="categories[hints_tips][<?= $category['slug']; ?>]" value="<?= $category['slug']; ?>"<?= isset($selectedCategories['hints_tips'][$category['slug']]) ? ' checked="checked"' : ''; ?> /> <?= $category['name']; ?></label></li>
									<?php } ?>
								</ul>
								<div class="clear"></div>
							</div>

							<div class="block">
								<h4>Thinking Healthy</h4>
								<ul>
									<?php foreach ($categories['thinking_healthy'] as $category) { ?>
									<li><label class="check"><input type="checkbox" name="categories[thinking_healthy][<?= $category['slug']; ?>]" value="<?= $category['slug']; ?>"<?= isset($selectedCategories['thinking_healthy'][$category['slug']]) ? ' checked="checked"' : ''; ?> /> <?= $category['name']; ?></label></li>
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
							<img src="<?= ASSETURL ?>/itemimages/150/150/3/<?= isset($item['image']) ? $item['image']['filename'] : '' ?>" alt="<?= $default_alt; ?>" />
							<?php } ?>
							<label for="form_imgfile">Upload a photo from your computer</label>
							<input type="file" name="default" id="default" class="file text-content" size="30" />
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
								<label for="form_go_live_date">Go Live Date [mm/dd/yyyy]</label>
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
							<button name="preview" class="button-lg fl" type="submit" value="preview"><span>Preview your article</span></button>
							<button name="submit" class="button-lg fl" type="submit" value="submit" style="margin-left: 10px"><span>Save your article</span></button>
						</div>

						<div class="clear"></div>

						<input type="hidden" name="task" value="<?= $submitTask; ?>" />
						</form>

					</div></div>

					<?php
						// Allow admin to add relationships, if editing an existing article
						if (Template::get('adminPrivileges') && $this->_itemId && isset($editRelatedLink)) {
					?>
					<div class="clear"></div>
					<div class="fieldwrap" style="margin-top: 30px;">
						<a href="<?= SITEURL.$editRelatedLink; ?>">Edit related articles</a>
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
				<?php include(BLUPATH_TEMPLATES.'/site/newsletter.php') ?>

				<?php //if (STAGING) { ?>
				<div class="ad">
					<!-- frontend/recipe4living/articles/article_write.php -->
					<!-- FM Test STAMP 300x250 expands to 300x600 Zone -->
					<!--<script type='text/javascript' src='http://static.fmpub.net/zone/2461'></script>-->
					<!-- FM Test STAMP 300x250 expands to 300x600 Zone -->
				</div>
				<?php //} ?>

				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>

				<?php // $this->landing_featured_question(); ?>

				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>

			<div class="clear"></div>
		</div>

	</div>
