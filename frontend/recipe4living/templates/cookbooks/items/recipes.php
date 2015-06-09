
	<div id="recipes_listing">
	
		<?= Messages::getMessages(); ?>
		
		<?php if (!empty($items)) { ?>
		
		<form id="listing-recipe-search" action="<?= SITEURL.$listingBaseUrl; ?>" method="get" class="reloads"><div>
						
			<div id="list-sort" class="fl">
				<label for="sort" class="fl">Sort by:</label>
				<select id="sort-by" name="sort" class="reloads">
					<option value="relevance"<?= $sort == 'relevance' ? ' selected="selected"' : ''; ?>>Relevance</option>
					<option value="name_asc"<?= $sort == 'name_asc' ? ' selected="selected"' : ''; ?>>Name A to Z</option>
					<option value="date_desc"<?= $sort == 'date_desc' ? ' selected="selected"' : ''; ?>>Newest</option>
					<option value="reviews_desc"<?= $sort == 'reviews_desc' ? ' selected="selected"' : ''; ?>>Most Reviews</option>
					<option value="rating"<?= $sort == 'rating' ? ' selected="selected"' : ''; ?>>Most Chef Hats</option>
				</select>
				<noscript>
					<input type="submit" value="Sort" />
				</noscript>
			</div>
			<div class="fr">
				<ul id="cs-links" class="text-content fr screenonly">
					<li><a href="?format=print&controls=0" class="print-popup" target="_blank">Print Cookbook</a></li>
				</ul>
			</div>
			<div class="clear"></div>
			<?php if ($showSearch) { ?><input type="hidden" name="searchterm" value="<?= $searchTerm ?>" /><?php } ?>
			<?php if ($showSearchExtra) { ?><input type="hidden" name="searchterm_extra" value="<?= $searchTermExtra; ?>" /><?php } ?>
			<input type="hidden" name="page" value="<?= $page ?>" />
			
		</div></form>
		
		<div class="clear"></div>
		<div class="thumb-list" style="width:480px;">
			<div class="printonly">
				<div class="fl">
					<h2><?= $cookbook['title']; ?></h2>
				</div>
				<div id="recipe-directions" class="block">
					<h5>Description</h5>
					<div class="text-content">
						<?= Text::toHtml($cookbook['description']); ?>
					</div>
				</div>
				<hr>
			</div>
			<?= ($format=='print')?'<div>':'<ul>'; ?>
			<?php
				$count = 0;
				foreach ($items as $item) {
					
					// Do some controller stuff, eugh
					$recipeBoxLink = $itemsModel->getTaskLink($item['link'], 'save_to_recipe_box');
					$recipeBoxRemoveLink = $itemsModel->getTaskLink($item['link'], 'remove_from_recipe_box');
					$recipeBoxRemoveLink .= isset($listingBaseUrl) ? '?redirect='.base64_encode(SITEURL.$listingBaseUrl) : '';
					$recipeNoteLink = $itemsModel->getTaskLink($item['link'], 'save_recipe_note');
					
					
					// Display
			?>			
			<!-- should be handled by css havn't got time -->
			<?= ($format=='print')?'<div class="grid" style="width:460px; height:auto;">':'<li class="grid" style="width:460px; height:auto;">'; ?>
				<div class="fl boxing" style="width:460px;">
					<div class="fl">
						<h5><a href="<?= SITEURL.$item['link']; ?>"><?= $item['title']; ?></a></h5>
					</div>
					<div class="text-content fr  screenonly">
						<?php if ($cookbook['canEdit']) { ?>
							<a class="close fl" title="Remove from cookbook" href="<?= SITEURL.$item['removeLink']; ?>">Remove</a>
						<?php } ?>
					</div>
					<div class="clear"></div>					
					<div class="fl screenonly" style="width:140px;">
						<a href="<?= SITEURL.$item['link']; ?>">
							<img alt="<?= $item['default_alt']; ?>" src="<?= ASSETURL; ?>/itemimages/140/140/1/<?= $item['image']['filename']; ?>" width="140" height="140" />
						</a>
						<div class="desc">
							<div class="text-content">
								<div class="shared-by">
									<?php if ($item['author']) { ?>
									<a href="<?= SITEURL; ?>/profile/<?= $item['author']['username']; ?>"><?= $item['author']['username']; ?></a>
									<?php } else { ?>
									<?= Template::text('global_anon_user'); ?>
									<?php } ?>
								</div>
								<div class="views"><?= $item['views']; ?></div>
								<div class="rating">
									<?php include(BLUPATH_TEMPLATES.'/articles/items/rating.php'); ?>
								</div>
							</div>
						</div>
					</div>
					<div class="text-content fr" style="width:290px;">
						<p class="snippet"><?= $item['teaser']; ?></p>
					</div>
					<div class="clear"></div>
					<?php if (!empty($item['ingredients'])) { ?>
					<div id="recipe-ingredients" class="block">
						<h2>Ingredients</h2>
						<div class="text-content">
							<ul>
								<?php foreach ($item['ingredients'] as $ingredient) { ?>
								<li><?= $ingredient; ?></li>
								<?php } ?>
							</ul>
						</div>
					</div>
					<?php } ?>					
					<div id="recipe-directions" class="block">
							<h2>Directions</h2>
							<div class="text-content">
								<?= Text::toHtml($item['body']); ?>
							</div>
					</div>
				</div>
				<div class="clear"></div>	
			<?= ($format=='print')?'</div>':'</li>'; ?>
			<?php if($count!=$total-1){?><hr class="pagebreak"><?php }?>
			<?php
				$count ++;
				}
			?>
			<?= ($format=='print')?'</div>':'</ul>'; ?>			
			<div class="clear"></div>
			
		</div>
		<?= $pagination->get('buttons', array(
			'pre' => '<strong class="fl">Pages: </strong>'
		)); ?>

		<?php } ?>
		
	</div>
