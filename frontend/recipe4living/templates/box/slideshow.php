	<div id="featured_recipe" >

		<div class="content">
			
			<div id="panels-holder">
				<div id="panels-inner" style="width: <?= 650 * (count($box['items']) + 1) + 50; // For good measure... ?>px;">
					<?php $i = 0; foreach ($box['items'] as $item) { $i++; ?>
					<div id="panel-<?= $i; ?>" class="panel">
						<a href="<?= SITEURL.$item['link']; ?>"><img alt="<?= isset($item['featuredImage']['filename']) ? $item['featured_alt'] : $item['title']; ?>" src="<?= ASSETURL; ?>/itemimages/650/300/3/<?= isset($item['featuredImage']['filename']) ? $item['featuredImage']['filename'] : $item['image']['filename']; ?>" /></a>
						
						<h2><a href="<?= SITEURL.$item['link']; ?>"><?= $item['title']; ?></a>
						
						<?php if ($item['title'] == 'Easy Chicken & Cheese Enchiladas' || $item['title'] == 'Beef Taco Skillet' || $item['title'] == 'French Onion Burgers') { ?>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<a href='http://www.campbellkitchen.com/Home.aspx' target="_blank"><img src="http://pics.recipe4living.com/campbells_logo.bmp" border="0"></a>
						<?php } ?>
						</h2>
						<p class="text-content"><?= Text::trim($item['teaser'], 150); ?></p>
						<a href="<?= SITEURL.$item['link']; ?>" class="text-content more fr">View full <?= ($item['type'] == 'article') ? 'article' : 'recipe'; ?></a>

						<div class="rating text-content fl">
							<?php if ($item['type'] != 'article') { include(BLUPATH_TEMPLATES.'/articles/items/rating.php'); } ?>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
			<div class="clear"></div>
			
			<ul id="panels-nav" class="others" style="zoom:1;">
				<?php $i = 0; foreach ($box['items'] as $item) { $i++; ?>
				<li class="panel-<?= $i; ?><?= $i == 1 ? ' first' : ($i == count($box['items']) ? ' last' : ''); ?>">
					<a href="#panel-<?= $i; ?>"><img alt="<?= isset($item['featuredImage']['filename']) ? $item['featured_alt'] : $item['title']; ?>" src="<?= ASSETURL; ?>/itemimages/86/67/3/<?= isset($item['featuredImage']['filename']) ? $item['featuredImage']['filename'] : $item['image']['filename']; ?>" width="81" height="50" /></a>
				</li>
				<?php } ?>
			</ul>
			<div class="clear2"></div>
		</div>
	</div>

	<?php Template::startScript(); ?>
	
	var panels = new Panels('panels-holder', 'panels-nav', {
		defaultPanel: 'panel-1',
		transition: 'expo:out',
		rotate: 5000,
		updateHash: false
	});
	
	<?php Template::endScript(); ?>
