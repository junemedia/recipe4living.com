	
	<div id="featured_articles" class="rounded half fl">
		<div class="content">
			<h2 class="fl">Featured Articles</h2>
			<a href="<?= SITEURL; ?>/articles" class="text-content more fr">View all</a>
			<div class="clear2"></div>
			
			<?php if (empty($items)) { ?>
			No featured articles to display.
			<?php } else { ?>
			<ul class="thumb-list">
				<?php foreach ($items as $item) { ?>
				<li>
					<div class="im">
						<a href="<?= SITEURL.$item['link']; ?>">
							<img alt="<?= isset($item['featuredImage']['filename']) ? $item['featured_alt'] : $item['default_alt']; ?>" src="<?= ASSETURL; ?>/itemimages/75/75/3/<?= isset($item['featuredImage']['filename']) ? $item['featuredImage']['filename'] : $item['image']['filename']; ?>" />
						</a>
					</div>
					<div class="desc">
						<h5><a href="<?= SITEURL.$item['link']; ?>"><?= $item['title']; ?></a></h5>
						<div class="rating text-content fl">
							<?php //include(BLUPATH_TEMPLATES.'/articles/items/rating.php'); ?>
						</div>
					</div>
					<div class="clear"></div>
				</li>
				<?php } ?>
			</ul>
			<?php } ?>
		</div>
	</div>
