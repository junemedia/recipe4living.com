
	<div id="top_recipes" class="rounded half fr">

		<div class="content">
			
			<h2 class="fl">Top Recipes</h2>
			<a href="<?= SITEURL; ?>/recipes" class="text-content more fr">View all</a>
			<div class="clear2"></div>
			
			<?php if (empty($items)) { 
				$itemsModel = BluApplication::getModel('items');
				$recentlyAddedRecipes = $itemsModel->getRecentRecipes();
				shuffle($recentlyAddedRecipes);
				$items = array_slice(($recentlyAddedRecipes),0,3);	
			 } ?>
			<ul class="thumb-list">
				<?php foreach ($items as $item) { ?>
				<li>
				
					<div class="im">
						<a href="<?= SITEURL.$item['link']; ?>"><img alt="<?= isset($item['featuredImage']['filename']) ? $item['featured_alt'] : $item['title']; ?>" src="<?= ASSETURL; ?>/itemimages/75/75/3/<?= isset($item['featuredImage']['filename']) ? $item['featuredImage']['filename'] : $item['image']['filename']; ?>" /></a>
					</div>
					
					<div class="desc">
						<h5><a href="<?= SITEURL.$item['link']; ?>"><?= $item['title']; ?></a></h5>
						<div class="rating text-content fl">
							<?php include(BLUPATH_TEMPLATES.'/articles/items/rating.php'); ?>
						</div>
					</div>
					
					<div class="clear"></div>
					
				</li>
				<?php } ?>
			</ul>
		</div>

	</div>
	
