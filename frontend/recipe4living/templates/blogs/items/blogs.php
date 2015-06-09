
	<div id="list-heading" class="rounded">
		<div class="content">
			<h2><?= $listingTitle; ?> (<?= $pagination->get('total'); ?>)</h2>
			<div class="text-content" style="margin-top: 10px;">
				<a href="<?= SITEURL ?>/blogs/share">Submit a blog post</a>
			</div>
			
			<?php if ($showSearchExtra) { ?>
			<form id="listing-recipe-search-refine" action="<?= SITEURL.$listingBaseUrl; ?>" method="get" class="reloads fr"><div>
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
					<option value="reviews_desc"<?= $sort == 'reviews_desc' ? ' selected="selected"' : ''; ?>>Most Comments</option>
					<option value="rating"<?= $sort == 'rating' ? ' selected="selected"' : ''; ?>>Most Chef Hats</option>
				</select>
				<noscript>
					<input type="submit" value="Sort" />
				</noscript>
			</div>
			
			<div class="recipe-view fr">
				<strong class="fl">View:</strong>
				<ul>
					<li id="view-list"><a class="reloads<?= $layout == 'list' ? ' current' : ''; ?>" href="<?= SITEURL.$layoutBaseUrl ?>list">List</a></li>
					<li id="view-gallery"><a class="reloads<?= $layout == 'grid' ? ' current' : ''; ?>" href="<?= SITEURL.$layoutBaseUrl ?>grid">Grid</a></li>
				</ul>
			</div>
			
			<div class="clear"></div>
			
			<?php if ($showSearch) { ?><input type="hidden" name="searchterm" value="<?= $searchTerm ?>" /><?php } ?>
			<?php if ($showSearchExtra) { ?><input type="hidden" name="searchterm_extra" value="<?= $searchTermExtra; ?>" /><?php } ?>
			<input type="hidden" name="page" value="<?= $page ?>" />
			
		</div></form>
		
		<div class="clear"></div>
		
		<div class="thumb-list">
			<ul>
			<?php
				foreach ($items as $item) {
					
					// Do some controller stuff, eugh
					$recipeBoxLink = $itemsModel->getTaskLink($item['link'], 'save_to_recipe_box');
					$recipeBoxRemoveLink = $itemsModel->getTaskLink($item['link'], 'remove_from_recipe_box');
					$recipeBoxRemoveLink .= isset($listingBaseUrl) ? '?redirect='.base64_encode(SITEURL.$listingBaseUrl) : '';
					$recipeNoteLink = $itemsModel->getTaskLink($item['link'], 'save_recipe_note');
					
					
					// Display
					switch ($layout) {
						case 'list':
			?>			
			<li class="list">
				
				<div class="desc">
					<h5 class="fl"><a href="<?= SITEURL.$item['link']; ?>"><?= $item['title']; ?></a></h5>
					
					<div class="rating fl">
						<?php include(BLUPATH_TEMPLATES.'/articles/items/rating.php'); ?>
					</div>
					
					<div class="clear"></div>
					
					<div class="text-content">
						<p><?= Text::trim($item['teaser'], 200); ?></p>
						
						<div class="shared-by fl">
							Shared by 
							<?php if ($item['author']) { ?>
							<a href="<?= SITEURL; ?>/profile/<?= $item['author']['username']; ?>"><?= $item['author']['username']; ?></a>
							<?php } else { ?>
							<?= Template::text('global_anon_user'); ?>
							<?php } ?>
						</div>
						
						<div class="fr">
							<div class="views fl"><?= $item['views']; ?></div>
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
			<li class="grid">
				<div class="im fl">
					<a href="<?= SITEURL.$item['link']; ?>">
						<img alt="<?= $item['image']['title']; ?>" src="<?= ASSETURL; ?>/itemimages/140/140/1/<?= $item['image']['filename']; ?>" width="140" height="140" />
					</a>
				</div>
				
				<div class="desc">
					<h5><a href="<?= SITEURL.$item['link']; ?>"><?= Text::trim($item['title'], 25); ?></a></h5>
					
					<div class="text-content">
						<div class="shared-by">
							<?php if ($item['author']) { ?>
							<a href="<?= SITEURL; ?>/profile/<?= $item['author']['username']; ?>"><?= $item['author']['username']; ?></a>
							<?php } else { ?>
							<?= Template::text('global_anon_user'); ?>
							<?php } ?>
						</div>
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
