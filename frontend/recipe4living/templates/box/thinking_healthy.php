
	<div id="landing_featured">
		<ul>
			<?php foreach ($items as $item) { ?>
			<li>
				<div class="im">
					<a href="<?= SITEURL.$item['link']; ?>"><img src="<?= ASSETURL ?>/itemimages/120/80/1/<?= isset($item['featuredImage']['filename']) ? $item['featuredImage']['filename'] : $item['image']['filename']; ?>" alt="<?= isset($item['featuredImage']['filename']) ? $item['featured_alt'] : $item['default_alt']; ?>" /></a>
				</div>
				<h3><a href="<?= SITEURL.$item['link']; ?>"><?= $item['title']; ?></a></h3>
				<p class="text-content"><?= Text::trim($item['teaser'], 100); ?></p>
			</li>
			<?php } ?>
		</ul>
	</div>