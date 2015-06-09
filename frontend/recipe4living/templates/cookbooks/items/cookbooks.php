
	<div id="recipes_listing">
	
		<?= Messages::getMessages(); ?>
		
		<?php if (!empty($itemGroups)) { ?>
		
		<form id="listing-recipe-search" action="<?= SITEURL.$listingBaseUrl; ?>" method="get" class="reloads"><div>
			
			<div id="list-sort" class="fl">
				<label for="sort" class="fl">Sort by:</label>
				<select id="sort-by" name="sort" class="reloads">
					<option value="relevance"<?= $sort == 'relevance' ? ' selected="selected"' : ''; ?>>Relevance</option>
					<option value="name_asc"<?= $sort == 'name_asc' ? ' selected="selected"' : ''; ?>>Name A to Z</option>
					<option value="date_desc"<?= $sort == 'date_desc' ? ' selected="selected"' : ''; ?>>Newest</option>
					<option value="saved"<?= $sort == 'saved' ? ' selected="selected"' : ''; ?>>Popular</option>
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
				foreach ($itemGroups as $itemGroup) {
					
					// Display variables
					$isAuthor = $itemGroup['author'] && $user && ($itemGroup['author']['id'] == $user['id']);
					$showTasks = $isAuthor && ($this->_view != 'cookbook_add_recipe');
					$canFavourite = ($this->_view == 'cookbook_listing');
					$isFavourite = $user && isset($user['saves']['cookbook'][$itemGroup['id']]);
					
					// Display
					switch ($layout) {
						case 'list':
			?>			
			<li class="list">
				<div class="im fl">
					<a href="<?= SITEURL.$itemGroup['link']; ?>">
						<img alt="<?= $itemGroup['image']['title']; ?>" src="<?= ASSETURL; ?>/itemimages/75/75/1/<?= $itemGroup['image']['filename']; ?>" width="75" height="75" />
					</a>
				</div>
				
				<div class="desc">
					<h5 class="fl"><a href="<?= SITEURL.$itemGroup['link']; ?>"><?= $itemGroup['title']; ?></a></h5>
					<div class="clear"></div>
					
					<div class="text-content">
						<p><?= Text::trim($itemGroup['description'], 200); ?></p>
						<div class="shared-by fl">
							
							<?php if ($showTasks) { ?>
							
								<a href="<?= SITEURL; ?>/cookbooks/edit/<?= $itemGroup['slug']; ?>.htm">Edit</a>
								&nbsp;|&nbsp;
								<a href="<?= SITEURL; ?>/cookbooks/delete/<?= $itemGroup['slug']; ?>.htm">Delete</a>
								
							<?php } else { ?>
							
								Shared by 
								<?php if ($itemGroup['author']) { ?>
								<a href="<?= SITEURL; ?>/profile/<?= $itemGroup['author']['username']; ?>"><?= $itemGroup['author']['username']; ?></a>
								<?php } else { ?>
								<?= Template::text('global_anon_user'); ?>
								<?php } ?>
								
							<?php } ?>
							
						</div>
						<div class="fr">
							<?php if ($canFavourite) { ?>
							<a class="recipe-box-<?= $isFavourite ? 'remove' : 'add'; ?> fl" title="<?= $isFavourite ? 'Remove from' : 'Save to'; ?> my cookbook collection" href="<?= SITEURL.$itemGroup['favouriteLink']; ?>"></a>
							<?php } ?>
						</div>
					</div>
					
					<div class="clear"></div>
					
					<?php if ($itemGroup['comment']) {  ?>
					<div class="message message-info"><?= htmlspecialchars($itemGroup['comment']) ?></div>
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
					<a href="<?= SITEURL.$itemGroup['link']; ?>">
						<img alt="<?= $itemGroup['image']['title']; ?>" src="<?= ASSETURL; ?>/itemimages/140/140/1/<?= $itemGroup['image']['filename']; ?>" width="140" height="140" />
					</a>
				</div>
				
				<div class="desc">
					<h5><a href="<?= SITEURL.$itemGroup['link']; ?>"><?= Text::trim($itemGroup['title'], 25); ?></a></h5>
					
					<div class="text-content">
						
						<div class="shared-by">
							<?php if ($showTasks) { ?>
							
								<a href="<?= SITEURL; ?>/cookbooks/edit/<?= $itemGroup['slug']; ?>.htm">Edit</a>
								&nbsp;|&nbsp;
								<a href="<?= SITEURL; ?>/cookbooks/delete/<?= $itemGroup['slug']; ?>.htm">Delete</a>
								
							<?php } else { ?>
								
								<?php if ($itemGroup['author']) { ?>
								<a href="<?= SITEURL; ?>/profile/<?= $itemGroup['author']['username']; ?>"><?= $itemGroup['author']['username']; ?></a>
								<?php } else { ?>
								<?= Template::text('global_anon_user'); ?>
								<?php } ?>
								
							<?php } ?>
						</div>
						
						<?php if ($canFavourite) { ?>
						<a class="recipe-box-add fl" title="Save to my cookbook collection" href="<?= SITEURL.$itemGroup['favouriteLink']; ?>"></a>
						<?php } ?>
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
		
		<?php
			if ($pagination) {
				echo $pagination->get('buttons', array(
					'pre' => '<strong class="fl">Pages: </strong>'
				));
			}
		 ?>

		<?php } ?>
		
	</div>
