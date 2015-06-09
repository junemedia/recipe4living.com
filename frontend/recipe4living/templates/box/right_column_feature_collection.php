<?php /*  
<div class="recipe_collections rounded">
	<h2 class="main">Latest Recipe Collections</h2>
	<div class="content">
	<?php if(!empty($items)){?>
	<ul>
	<?php foreach($items as $item) {?>
	<li>
		<div class="title_desc">
			<h2><a href="<?= SITEURL.$item['link']; ?>" title="<?= $item['title']; ?>"><?= Text::trim($item['title'], 30); ?></a></h2>
			<p><?= Text::trim($item['teaser'], 65); ?></p>
		</div>	
		<img alt="<?= (!empty($item['default_alt']))?$item['default_alt']:$item['title']; ?>" src="<?= ASSETURL; ?>/itemimages/75/75/3/<?= isset($item['featuredImage']['filename']) ? $item['featuredImage']['filename'] : $item['image']['filename']; ?>"/>
	</li>
	<?php }?>
	</ul>
	<?php }?>
	</div>
</div>
*/ ?>	