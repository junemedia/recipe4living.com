	<div id="list-heading" class="rounded">
		<div class="content">
			<h1 style="font-size: 1.25em; color: #FFFFFF; margin-bottom: 0; line-height: 1.25em;<?php echo $iscategory?'float:left;':'';?>">Recipes - <?= $listingTitle; ?> (<?php echo number_format($pagination->get('total')); ?>)</h1>

			<?php if ($showSearchExtra) { ?>
			<form id="listing-recipe-search-refine" style="<?php echo $iscategory?'margin-top: -10px;':'';?>" action="<?php echo SITEURL . htmlspecialchars($listingBaseUrl); ?>" method="get" class="reloads fr"><div>
				<label for="refine_search" class="fl">
					Refine results:
					<input id="refine_search" type="text" name="searchterm_extra" value="<?= $searchTermExtra; ?>" class="textinput" autocomplete="off" />
				</label>

				<?php if ($searchTerm) { ?><input type="hidden" name="searchterm" value="<?= $searchTerm; ?>" /><?php } ?>

				<noscript>
					<button class="fl">Filter</button>
				</noscript>
			</div></form>
			<div class="clear"></div>
			<?php } ?>
		</div>
	</div>
	<?php if($iscategory && $listingTitle!='All Articles' && $listingTitle !='All Recipes'){?>
	<div id="seo_desc">
		<h1><?php echo $listingTitle;?></h1>
		<div class="seo_content">
		<?php echo $description?>
		</div>
	</div>
	<?php }?>
	<div id="recipes_listing">

		<?= Messages::getMessages(); ?>

		<?php if (!empty($items)) { ?>

		<form id="listing-recipe-search" action="<?php echo SITEURL . htmlspecialchars($listingBaseUrl); ?>" method="get" class="reloads"><div>

			<div id="list-sort" class="fl">
				<label for="sort" class="fl">Sort by:</label>
				<select id="sort-by" name="sort" class="reloads">
					<option value="relevance"<?= $sort == 'relevance' ? ' selected="selected"' : ''; ?>>Relevance</option>
					<option value="name_asc"<?= $sort == 'name_asc' ? ' selected="selected"' : ''; ?>>Name A to Z</option>
					<option value="date_desc"<?= $sort == 'date_desc' ? ' selected="selected"' : ''; ?>>Newest</option>
					<option value="reviews_desc"<?= $sort == 'reviews_desc' ? ' selected="selected"' : ''; ?>>Most Reviews</option>
					<option value="rating"<?= $sort == 'rating' ? ' selected="selected"' : ''; ?>>Most Chef Hats</option>
				</select>
					<input type="submit" value="Go" />
			</div>

			<div class="recipe-view fr">
				<strong class="fl">View:</strong>
				<ul>
					<li id="view-list"><a class="reloads<?= $layout == 'list' ? ' current' : ''; ?>" href="<?php echo SITEURL . htmlspecialchars($layoutBaseUrl); ?>list">List</a></li>
					<li id="view-gallery"><a class="reloads<?= $layout == 'grid' ? ' current' : ''; ?>" href="<?php echo SITEURL . htmlspecialchars($layoutBaseUrl); ?>grid">Grid</a></li>
				</ul>
			</div>

			<div class="clear"></div>

			<?php if ($showSearch) { ?><input type="hidden" name="searchterm" value="<?= $searchTerm ?>" /><?php } ?>
			<?php if ($showSearchExtra) { ?><input type="hidden" name="searchterm_extra" value="<?= $searchTermExtra; ?>" /><?php } ?>
			<input type="hidden" name="page" value="<?= $page ?>" />

		</div></form>

		<div class="clear"></div>

		<div class="thumb-list">
			<ul style="<?php echo $iscategory?'width:640px':'';?>">
			<?php
        $i = 0;
				foreach ($items as $item) {
          $i++;
					// Do some controller stuff, eugh
					$recipeBoxLink = $itemsModel->getTaskLink($item['link'], 'save_to_recipe_box');
					$recipeBoxRemoveLink = $itemsModel->getTaskLink($item['link'], 'remove_from_recipe_box');
					$recipeBoxRemoveLink .= isset($listingBaseUrl) ? '?redirect='.base64_encode(SITEURL.$listingBaseUrl) : '';
					$recipeNoteLink = $itemsModel->getTaskLink($item['link'], 'save_recipe_note');

					if ($item['author']['username'] == '') { $item['author']['username'] = 'Recipe4Living'; }

					// Display
					switch ($layout) {
						case 'list':
			?>
			<li class="list">
				<?php if (strstr($item['image']['filename'],'avatar')) { $item['image']['filename']='';} if (isset($item['thumbnail']['filename']) || (!isset($item['thumbnail']['filename']) && $item['image']['filename'] !='')) { ?>
				<div class="im fl">
					<a href="<?= SITEURL.$item['link']; ?>">
						<img alt="<?= isset($item['thumbnail']['filename']) ? $item['thumbnail_alt'] : isset($item['default_alt']); ?>" src="<?= ASSETURL; ?>/itemimages/75/75/1/<?= isset($item['thumbnail']['filename']) ? $item['thumbnail']['filename'] : $item['image']['filename']; ?>" width="75" height="75" />
					</a>
				</div>
				<?php } ?>

				<div class="desc" style="<?php echo $iscategory?'width:495px;':'';?>">
					<h2 class="fl"><a href="<?= SITEURL.$item['link']; ?>"><?= $item['title']; ?></a></h2>

					<div class="rating fl">
						<?php if (!strstr($item['link'], '/articles/')) { include(BLUPATH_TEMPLATES.'/articles/items/rating.php'); } ?>
					</div>

					<div class="clear"></div>

					<?php if (isset($item['thumbnail']['filename']) || (!isset($item['thumbnail']['filename']) && $item['image']['filename'] !='')) { ?>
						<div class="text-content">
					<?php } else { ?>
						<div class="text-content" style="width:470px;">
					<?php } ?>
						<p><?= Text::trim($item['teaser'], 200); ?></p>
						<div class="fr">
							<div class="views fl"><?= $item['views']; ?></div>
							<?php
								switch ($item['type']) {
									case 'recipe':
										// Bit of a bodge, should really duplicate whole file into another template: /recipes/items/recipes.php
										if ($item['inRecipeBox']) {
							?>
							<a class="recipe-box-remove fl" title="Remove from recipe box" href="<?= SITEURL.$recipeBoxRemoveLink; ?>"></a>
							<?php
										} else {
							?>
							<a class="recipe-box-add fl" title="Add to recipe box" href="<?= SITEURL.$recipeBoxLink; ?>"></a>
							<?php
										}
										break;

									default:
										break;
								}
							?>
						</div>

					</div>

					<div class="clear"></div>

					<?php if ($item['type'] == 'recipe' && !empty($item['recipe_note'])) {  ?>
					<div class="message message-info recipe-note"><?= htmlspecialchars($item['recipe_note']) ?></div>
					<?php } ?>
				</div>

				<div class="clear"></div>

			</li>
			<?php
						break;
						case 'grid':
			?>
			<li class="grid" style="<?php echo $iscategory?'border:none;padding:2px;width:207px;height:295px !important;margin: 0 2px 2px 0;':'';?>">
			<?php
			$imgwidth = 140;
			$imgsize = 140;
			$imgtype = 1;
			if($iscategory){
				$imgwidth = 207;
				$imgsize = 210;
				$imgtype = 3;
			}?>
				<div class="im fl">
					<a href="<?= SITEURL.$item['link']; ?>">
						<img alt="<?= (!empty($item['default_alt']))?$item['default_alt']:'';?>" src="<?= ASSETURL; ?>/itemimages/<?= $imgsize;?>/<?= $imgsize;?>/<?= $imgtype;?>/<?= $item['image']['filename']; ?>" width="<?= $imgwidth;?>" height="<?= $imgwidth;?>" />
					</a>
				</div>

				<div class="desc">
					<h2><a href="<?= SITEURL.$item['link']; ?>"><?= Text::trim($item['title'], 25); ?></a></h2>

					<div class="text-content">
						<div class="views fl"><?= $item['views']; ?></div>

						<div class="rating fl">
							<?php include(BLUPATH_TEMPLATES.'/articles/items/rating.php'); ?>
						</div>

						<div class="clear"></div>
					</div>

				</div>

				<div class="clear"></div>

			</li>
			<?php
						break;
					}
          // place Lockerdome unit after 6th item (2nd row of grid view)
          if ($i === 6) {
            include BLUPATH_TEMPLATES.'/site/ads/lockerdome.html';
          }
				}
			?>
			</ul>
			<div class="clear"></div>
		</div>

		<?= $pagination->get('buttons', array(
			'pre' => '<strong class="fl">Pages: </strong>'
		)); ?>

		<?php } ?>

	</div>
