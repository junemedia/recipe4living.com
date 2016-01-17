
	<div id="main-content" class="recipe">

		<div id="column-container" style="padding-left:0px;width:645px;">
			<!-- start of class hrecipe -->
			<div itemscope itemtype="http://schema.org/Recipe">
			<div id="panel-center" class="column">
				<?= Messages::getMessages(); ?>

				<?php if ($referer&&0) { ?>
				<div class="search-nav text-content screenonly">

					<a class="back fl" href="<?= SITEURL.$referer; ?>">Back to previous search results</a>

					<? /* Could put previous/next links here?
					<span class="fr">
						<a class="screenonly" href="#">Previous</a>
						&nbsp;|&nbsp;
						<a class="screenonly" href="#">Next</a>
					</span>
					*/ ?>

					<div class="clear"></div>
				</div>
				<?php } ?>
				<? if (isset($format) && $format == 'print') { ?>
				<a href="#" onclick="window.print();return false;">Click here to print now</a>
				<? } ?>
                
                <?php if ($this->_doc->getFormat() != 'print') { 
					$this->category_hubs(); 
				}?>
                
				<?php include(BLUPATH_TEMPLATES.'/recipes/details/title.php'); ?>
                
                <?php
                    // Get the author, if none then display the recipe4living as the author
                    if(isset($item['author']['username']) && $item['author']['username'] != ""){
                        $authorDisplay = $item['author']['username'];
                    }else{
                        $authorDisplay = "Recipe4living.com";
                    }
                ?>
                
                <!-- Using ogp.me Tags -->
                <meta property="og:title" content="<?= $item['title']; ?>" />
                <meta property="og:article:author" content="<?= $authorDisplay; ?>" />
                <meta itemprop="ratingValue" content="<?= round($item['ratings']['average']); ?>" />
                
				<?php 

				$currUrl = urlencode('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
				$currImage = urlencode('http://'.$_SERVER['SERVER_NAME'].ASSETURL.'/itemimages/200/200/3/' .$item['image']['filename']);
				?>
				
				<div id="post">

					<!--<div id="post-details">
						<p class="text-content fl screenonly">
							<meta itemprop="datePublished" content="<?php echo substr($item['date'],0,10); ?>">
						</p>			
				
						<ul id="cs-links" class="text-content fr screenonly">
						
							<li class="first">
							<span style="position: absolute; top: 10px; left: 50px;"><a href="http://pinterest.com/pin/create/button/?url=<?=$currUrl;?>&media=<?=$currImage;?>" class="pin-it-button" count-layout="none"><img border="0" src="http://assets.pinterest.com/images/PinExt.png" title="Pin It" /></a></span>
							<span class='st_facebook_button' displayText='Facebook'></span>
							<span  class='st_twitter_button' displayText='Tweet'></span>
							<span  class='st_plusone_button' ></span>
							<span  class='st_email_button' displayText='Email'></span>
							</li>							
							<script type="text/javascript">var switchTo5x=false;</script>
							<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
							<script type="text/javascript">stLight.options({publisher:'0541fe9f-2a3f-4c01-ac74-8f02c84e7fde'});</script>
							
							<li><a href="?format=print" class="print-popup">Print</a></li>
							<li id="accesibility">
								<a class="font-decrease" href="#"><img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size-decrease.png" /></a>
								<img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size.png" />
								<a class="font-increase" href="#"><img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size-increase.png" /></a>
							</li>
						</ul>
						<div class="clear"></div>
					</div>
					<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>
					-->
					<?php $temp_img = $item['image']['filename']; if (strstr($item['image']['filename'],'avatar')) { $item['image']['filename']='';} ?>
					<?php if ($item['image']['filename'] != '') { ?>
					<?php if ($this->_doc->getFormat() != 'print') {?>
					<div class="im screenonly">
						<img itemprop="image" width="350" height="350" src="<?= ASSETURL; ?>/itemimages/400/400/3/<?= $item['image']['filename']; ?>" class="photo" alt="<?= (!empty($item['default_alt']))?$item['default_alt']:$item['title'];?>" style="float:left;margin:0 20px 20px 0;"/>
						<div class="gallery-link">
							<?php /*if($item['image']['filename']) { ?>
								<a href="<?= SITEURL . $imageGalleryLink ?>">Submit more pictures of this recipe</a>
							<?php } else { ?>
								<a href="<?= SITEURL . $imageGalleryLink ?>">Be the first to submit a picture<br />of this recipe</a>
							<?php } */?>
						</div>
					</div>
					<?php } 
					} else { ?>
					<meta itemprop="image" content="<?= ASSETURL; ?>/itemimages/200/200/3/<?php echo $temp_img; ?>">
					<?php } ?>



					<div class="teaser fl">					
						<div id="add-buttons" class="screenonly">
						<!--<ul>
							<li>
								<?php if ($item['inRecipeBox']) { ?>
								<a href="<?= SITEURL.$recipeBoxRemoveLink; ?>" class="recipe-box-remove">Remove from recipe box</a>
								<?php } else { ?>
								<a href="<?= SITEURL.$recipeBoxLink; ?>" id="add-recipe-box"><img src="<?= SITEASSETURL; ?>/images/site/R4l-add-box.png"/>Add to recipe box</a>
								<?php } ?>
							</li>
							<li></li>
							<li class="last"><a href="<?= SITEURL.$item['link'];?>?shownote=1" id="add-recipe-note"><img src="<?= SITEASSETURL; ?>/images/site/R4l-note-box.png"/>Add recipe note</a></li>							
						</ul>-->
						<div class="clear"></div>
					</div>
					
						<?php
						
							$this->_addRating();
							

						?>
						<span class="summary">
							<?php if ($item['image']['filename'] != '') { ?>
								<p class="snippet" itemprop="description">
							<?php } else { ?>
								<p class="snippet" style="width:460px;" itemprop="description">
							<?php } ?>
							<?= $item['teaser']; ?></p>
						</span>
							<?php
								$format = $this->_doc->getFormat();

								/*if (in_array($item['id'],array('54261','54271','54183','54255','54263','54157','54171','54193','54165','54167','54267','54187','54259','54163','54191','54173','54269','54159','54189','54177','54265','54257','54175','54185','54181','48451','48410','42925','38128','48373','48400','48666','48422','48395','48389','48434','48452','48409','48417','48394','48402','48416','48431','48382','48388','48432','48415','48375','48391','48420','48414','48392','48439','48387','38131','48380','49706','48378','48425','48404','48398','48385','48436','48423','48418','48413','48397','48406','48407','48412','38129','38180','48384','48376','48426','48429','48419'))) {
									echo "<script type='text/javascript' src='http://thirdparty.fmpub.net/placement/542773?fleur_de_sel=[timestamp]'></script>";
								}*/
							?>

							
						<?php if ($item['author']) {
							switch ($item['author']['username']) {
								case 'Recipe4Living':
									echo '<p class="text-content">Shared by <span itemprop="author">Recipe4Living</span></p>';
									break;
								case 'Campbells';
									echo '';
									break;
								default: ?>
									<p class="text-content">Shared by <a href="<?= SITEURL; ?>/profile/<?= $item['author']['username']; ?>"><span itemprop="author"><?= $item['author']['username']; ?></span></a><?php if ($item['author']['location']) { ?>, <br /><?= $item['author']['location']; ?><?php } ?></p>
									<?php
							}
						?>
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
					<!--<div id="add-buttons" class="screenonly">
						<ul>
							<li>
								<?php if ($item['inRecipeBox']) { ?>
								<a href="<?= SITEURL.$recipeBoxRemoveLink; ?>" class="recipe-box-remove">Remove from recipe box</a>
								<?php } else { ?>
								<a href="<?= SITEURL.$recipeBoxLink; ?>" id="add-recipe-box">Add to recipe box</a>
								<?php } ?>
							</li>

							<li>

							</li>
							<li class="last"><a href="<?= SITEURL.$item['link'];?>?shownote=1" id="add-recipe-note">Add recipe note</a></li>
							<li class="give-a-recipe"><a href="<?= SITEURL ?>/account/write_message?recipe=<?= $item['slug'];?>&type=<?= $item['type'];?>" id="give-a-recipe">Give a recipe</a></li>
						</ul>
						<div class="clear"></div>
					</div>-->
					<?php $shownote = (isset($_GET['shownote']))?$_GET['shownote']:0;
					?>
					<div id="recipe-note" class="screenonly" <? if (strlen(trim($recipeNote)) == 0 && $shownote !=1) { ?>style="display:none"<? } ?>>
						<form action="<?= SITEURL.$recipeNoteLink; ?>" id="form_recipe_notes" method="post"><div>
							<label for="recipe_note">My Recipe Notes:</label>
							<textarea name="comments" id="recipe_note" cols="" rows=""><?= htmlspecialchars($recipeNote); ?></textarea>
							<button type="submit" class="button-md fl"><span>Save recipe note</span></button>
							<div class="clear"></div>
						</div></form>
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
						<?php include_once(BLUPATH_TEMPLATES.'/site/ads/AOL_VIDEOS.php'); ?>
					
						<?php $this->view_page(); ?>
						
						
						
						<?php //$this->_advert('WEBSITE_INLINE_1'); ?>
					</div>

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

				<?php //if (STAGING) { ?>
				<div class="conversationalist ad">
					<script type='text/javascript' src='http://static.fmpub.net/zone/2560'></script>
				</div>
				<?php //} ?>
			</div>
			</div><!-- end of class hrecipe -->

			<!--<div id="panel-left" class="column screenonly">
				<?php $this->leftnav(); ?>
			</div>-->

			<div id="panel-right" class="column screenonly">
				<?php include(BLUPATH_TEMPLATES.'/site/newsletter.php') ?>
				<?php //$this->_box('our_best_recipes'); ?>
				
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>
<?php $this->_box('right_column_featured_recipes', array('limit' => 1,'boxid'=>34)); ?>
				<?php // $this->landing_featured_question(); ?>

				<?php
					if (DEBUG || STAGING) {
//						$this->nutrition(true);
					}
					//print_r($this);exit;
				?>
				<?php  //$this->related_categories(); ?>
				<?php $this->_box('right_column_feature_collection', array('limit' => 3,'boxid'=>35)); ?>
				<?php if (DEBUG || STAGING) { ?>
				<div id="calculator" class="standardform rounded">
					<div class="formholder content">
						<?php //$this->calculator(); ?>
					</div>
				</div>
				<?php } ?>
			<div class="clear"></div>
				<?php //$this->_box('reference_guides', array('limit' => 10)); ?>
				<?php $this->showNtent = true;?>
				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
				<!--<div class="ad"><?php include(BLUPATH_TEMPLATES.'/site/right_ntent_tags.php') ?></div>-->
			</div>

			<div class="clear"></div>
		</div>

	</div>
	<div class="screenonly">
	<?php $this->_advert('swoop'); ?>
</div>
