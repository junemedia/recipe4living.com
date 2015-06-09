
<?php
	$relatedArticles = Template::get('relatedArticles');
	//$pagination->set('locationHash','related_articles_search');
?>

<div id="related-articles-search"><div class="formholder">

	<div style="margin-top: 20px; margin-bottom:10px;"><div class="formholder">
		<form id="related_articles_search_box" name="related_articles_search_box" action="<?= SITEURL.$listingBaseUrl ?>" method="get" class="reloads"><div>
			<input class="textinput simpletext" type="text" title="Enter search keywords..." autocomplete="off" name="searchterm" value="<?= Template::get('searchTerm') ?>" style="width: 365px;" />
			<button name="submit" class="button-lg" type="submit" value="search"><span>Search</span></button>
		</div></form>
	</div></div>

	<div class="clear"></div>
	
<?php if(Template::get('searchTerm')) { ?>

	<div id="list-heading" class="rounded">
		<div class="content">
			<h2><?= $listingTitle; ?> (<?= $pagination->get('total'); ?>)</h2>
			
			<?php if ($showSearchExtra) { ?>
			<form id="listing-recipe-search-refine" action="<?= SITEURL.$listingBaseUrl; ?>" method="get" class="reloads fr"><div>
				<label for="refine_search" class="fl">
					Refine results:
					<input id="refine_search" type="text" name="searchterm_extra" value="<?= Template::get('searchTermExtra'); ?>" class="textinput" autocomplete="off" />
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
		
<?php /* ?>
		<form id="listing-article-search" action="<?= SITEURL.$listingBaseUrl; ?>" method="get"><div>
						
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
			
			<div class="clear"></div>
			
			<?php if ($showSearch) { ?><input type="hidden" name="searchterm" value="<?= $searchTerm ?>" /><?php } ?>
			<?php if ($showSearchExtra) { ?><input type="hidden" name="searchterm_extra" value="<?= Template::get('searchTermExtra'); ?>" /><?php } ?>
			<input type="hidden" name="page" value="<?= $page ?>" />
			
		</div></form>
<?php */ ?>
		
		<div class="clear"></div>
		
		<form id="related_articles_search" name="form_related_articles_search_search" action="<?= SITEURL.$listingBaseUrl; ?>" method="post">
		
		<div class="thumb-list">
			<ul>
			<?php
				$relatedArticles = Template::get('relatedArticles');
				foreach ($items as $item) {
					
					// Do some controller stuff, eugh
					$recipeBoxLink = $itemsModel->getTaskLink($item['link'], 'save_to_recipe_box');
					$recipeBoxRemoveLink = $itemsModel->getTaskLink($item['link'], 'remove_from_recipe_box');
					$recipeBoxRemoveLink .= isset($listingBaseUrl) ? '?redirect='.base64_encode(SITEURL.$listingBaseUrl) : '';
					$recipeNoteLink = $itemsModel->getTaskLink($item['link'], 'save_recipe_note');
			?>			
			<li class="list">
				<div class="im fl">
					<a href="<?= SITEURL.$item['link']; ?>">
						<img alt="<?= (!empty($item['default_alt']))?$item['default_alt']:'';?>" src="<?= ASSETURL; ?>/itemimages/75/75/1/<?= $item['image']['filename']; ?>" width="75" height="75" />
					</a>
				</div>
				<div class="desc">
					<h5 class="fl"><a href="<?= SITEURL.$item['link']; ?>" target="_blank"><?= $item['title']; ?></a></h5>
					
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
							
							<div class="rating fl">
								<?php include(BLUPATH_TEMPLATES.'/articles/items/rating.php'); ?>
							</div>
							
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
				
				<div class="fieldwrap">
					<label class="check"><input type="checkbox" name="related_articles[<?= $item['id'] ?>]" value="on"<?= isset($relatedArticles[$item['id']]) ? ' checked="checked" disabled="disabled"' : '' ?> />Select this <?= $item['type']; ?></label>
				</div>
				
				<div class="clear"></div>
				
			</li>
			<?php
				}
			?>
			</ul>
			
			<div class="clear"></div>
		</div>
		
		<div class="fieldwrap">
			<?= $pagination->get('buttons', array(
				'pre' => '<strong class="fl">Pages: </strong>'
			)); ?>
			<div class="clear"></div>
		</div>

		<div class="fieldwrap">
			<button name="submit" class="button-lg" type="submit" value="save_selected"><span>Save Selected</span></button>
		</div>
		
		<input type="hidden" name="task" value="add_related_articles" />

		</form>
		
		<?php } ?>
		
	</div>

<?php } ?>

</div></div>
