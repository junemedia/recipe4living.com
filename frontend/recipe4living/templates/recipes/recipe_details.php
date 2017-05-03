<div id="main-content" class="recipe">
	<div id="column-container" style="padding-left:0px;width:645px;">

		<!-- start of class hrecipe -->
		<div itemscope itemtype="http://schema.org/Recipe">
			<div id="panel-center" class="column">
				<?= Messages::getMessages(); ?>

				<? if (isset($format) && $format == 'print') { ?>
				<a href="#" onclick="window.print();return false;">Click here to print now</a>
				<? } ?>

				<?php if ($this->_doc->getFormat() != 'print') {
					$this->category_hubs();
				}?>

				<?php include(BLUPATH_TEMPLATES.'/recipes/details/title.php'); ?>

				<?php
				// Get the author, if none then display the recipe4living as the author
				if (isset($item['author']['username']) && $item['author']['username'] != "") {
					$authorDisplay = $item['author']['username'];
				}
				else {
					$authorDisplay = "Recipe4living.com";
				} ?>

				<!-- Using ogp.me Tags -->
				<meta property="og:title" content="<?= $item['title']; ?>" />
				<meta property="og:article:author" content="<?= $authorDisplay; ?>" />
				<meta itemprop="ratingValue" content="<?= round($item['ratings']['average']); ?>" />

				<?php
				$currUrl = urlencode('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
				$currImage = urlencode('http://'.$_SERVER['SERVER_NAME'].ASSETURL.'/itemimages/200/200/3/' .$item['image']['filename']);
				?>

				<div id="post">
				<?php
				$temp_img = $item['image']['filename'];
				if (strstr($item['image']['filename'],'avatar')) {
					$item['image']['filename']='';
				}
				?>

				<?php
				if ($item['image']['filename'] != '') {
					if ($this->_doc->getFormat() != 'print') { ?>

					<div class="im screenonly">
						<img itemprop="image" width="350" height="350" src="<?= ASSETURL; ?>/itemimages/400/400/3/<?= $item['image']['filename']; ?>" class="photo" alt="<?= (!empty($item['default_alt']))?$item['default_alt']:$item['title'];?>" style="float:left;margin:0 20px 20px 0;"/>
						<div class="gallery-link"> </div>
					</div>
					<?php }
					} else { ?>
					<meta itemprop="image" content="<?= ASSETURL; ?>/itemimages/200/200/3/<?php echo $temp_img; ?>">
					<?php } ?>

					<div class="teaser fl">
						<div id="add-buttons" class="screenonly">
							<div class="clear"></div>
						</div>

						<?php $this->_addRating(); ?>

						<span class="summary">
							<?php if ($item['image']['filename'] != '') { ?>
							<p class="snippet" itemprop="description">
							<?php } else { ?>
							<p class="snippet" style="width:460px;" itemprop="description">
							<?php } ?>
							<?= $item['teaser']; ?></p>
						</span>

						<?php $format = $this->_doc->getFormat(); ?>

						<?php if ($item['author']) {
							switch ($item['author']['username']) {
								case 'Recipe4Living':
									echo '<p class="text-content">Shared by <span itemprop="author">Recipe4Living</span></p>';
									break;
								case 'Campbells';
									echo '';
									break;
								default: ?>
						<p class="text-content">
							Shared by
							<a href="<?= SITEURL; ?>/profile/<?= $item['author']['username']; ?>">
								<span itemprop="author"><?= $item['author']['username']; ?></span>
							</a>
							<?php if ($item['author']['location']) { ?>
							<br /><?= $item['author']['location']; ?>
							<?php } ?>
						</p>
							<?php } ?>
						<?php } else { ?>
						<p class="text-content">Shared by <span itemprop="author">Recipe4Living</span></p>
						<?php } ?>
					</div>

					<?php if ($this->_doc->getFormat() != 'print') {?>
					<div class="clear"></div>
					<div class="share_recipe screenonly">
						<div class="share_title"><h2>Share Recipe</h2></div>
						<div class="share_icon">
							<ul>
								<li><a href="http://www.facebook.com/sharer.php?u=<?php echo $currUrl;?>" target="_blank"><img src="<?= SITEASSETURL; ?>/images/site/R4l-facebook-box.png"/></a></li>
								<li><a href="https://twitter.com/share?original_referer=<?php echo $currUrl;?>" target="_blank"><img src="<?= SITEASSETURL; ?>/images/site/R4l-twitter-box.png"/></a></li>
								<li><a href="http://pinterest.com/pin/create/button/?url=<?php echo $currUrl;?>&media=<?=$currImage;?>" target="_blank"><img src="<?= SITEASSETURL; ?>/images/site/R4l-pinterest-box.png"/></a></li>
								<li><a href="https://plus.google.com/share?url=<?php echo $currUrl;?>" target="_blank"><img src="<?= SITEASSETURL; ?>/images/site/R4l-googleplus-box.png"/></a></li>
								<li class="share_print"><a href="?format=print" class="print-popup"><img src="<?= SITEASSETURL; ?>/images/site/R4l-print-box.png"/></a></li>
								<li>
								<?php if ($item['inRecipeBox']) { ?>
								<a href="<?= SITEURL.$recipeBoxRemoveLink; ?>" class="recipe-box-remove">Remove from recipe box</a>
								<?php } else { ?>
								<a href="<?= SITEURL.$recipeBoxLink; ?>" id="add-recipe-box"><img src="<?= SITEASSETURL; ?>/images/site/R4l-add-box.png"/></a>
								<?php } ?>
								</li>
								<li class="last"><a href="<?= SITEURL.$item['link'];?>?shownote=1" id="add-recipe-note"><img src="<?= SITEASSETURL; ?>/images/site/R4l-note-box.png"/></a></li>
								<li class="first"><span  class='st_email_button' displayText='Email'></span></li>
							</ul>
							<script type="text/javascript">var switchTo5x=false;</script>
							<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
							<script type="text/javascript">stLight.options({publisher:'0541fe9f-2a3f-4c01-ac74-8f02c84e7fde'});</script>
						</div>
					</div>
					<?php }?>

					<div class="clear"></div>
					<?php $shownote = (isset($_GET['shownote'])) ? $_GET['shownote'] : 0; ?>
					<div id="recipe-note" class="screenonly" <? if (strlen(trim($recipeNote)) == 0 && $shownote !=1) { ?>style="display:none"<? } ?>>
						<form action="<?= SITEURL.$recipeNoteLink; ?>" id="form_recipe_notes" method="post">
							<div>
								<label for="recipe_note">My Recipe Notes:</label>
								<textarea name="comments" id="recipe_note" cols="" rows=""><?= htmlspecialchars($recipeNote); ?></textarea>
								<button type="submit" class="button-md fl"><span>Save recipe note</span></button>
								<div class="clear"></div>
							</div>
						</form>
					</div>

					<?php Template::startScript(); ?>
					var recipeNoteBox = $('recipe-note');
					<?php if (empty($recipeNote)) { ?>
					recipeNoteBox.setStyle('display', 'none');
					<?php } ?>
					$('add-recipe-note').addEvent('click', function(event) {
					event.stop();
					recipeNoteBox.setStyle('display', 'block').highlight();
					});
					<?php Template::endScript(); ?>

					<div class="entry">
						<?php
						include_once(BLUPATH_TEMPLATES.'/site/ads/AOL_VIDEOS.php');
						$this->view_page(); ?>
					</div>

					<?php include BLUPATH_TEMPLATES.'/site/ads/lockerdome.html'; ?>

					<?php include BLUPATH_TEMPLATES.'/site/ads/medianet_604x250.php'; ?>

					<?php Template::startScript(); ?>
					var fontResizer = new FontResizer($('post').getElement('.entry'), {initial: 0.75});
					$('post').getElement('.font-decrease').addEvent('click', function (event) {
					event.stop();
					fontResizer.decrease();
					});
					$('post').getElement('.font-increase').addEvent('click', function (event) {
					event.stop();
					fontResizer.increase();
					});
					<?php Template::endScript(); ?>

					<?php include(BLUPATH_TEMPLATES.'/recipes/details/author.php'); ?>

					<?php $this->_addReview(); ?>

					<?php $this->reviews(); ?>

					<div class="clear"></div>
				</div>

			</div>
		</div>
		<!-- end of class hrecipe -->

		<div id="panel-right" class="column screenonly">
			<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>

			<?php $this->showNtent = true; ?>

			<?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?>
		</div>
		<div class="clear"></div>

	</div>
</div>

<div class="screenonly">
	<?php $this->_advert('swoop'); ?>
</div>
