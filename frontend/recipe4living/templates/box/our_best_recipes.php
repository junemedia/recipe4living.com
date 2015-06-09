 <div class="our_best">
	<h2>Our Best Recipe Collections</h2>
	<div class="content">
		<a href="<?= SITEURL.$item['link']; ?>"><img src="<?= ASSETURL; ?>/itemimages/280/125/3/<?= $item['image']['filename']; ?>" width="280" height="125"/></a>
		<div class="recipe_desc">
		<?= Text::trim($item['teaser'], 220); ?>
		</div>
		<a href="<?= SITEURL.$item['link']; ?>" class="link_here">Link Here</a>
	</div>
</div>

	