
	<img class="image" src="<?= ASSETURL ?>/itemimages/45/45/1/<?= $item['image']['filename']; ?>" alt="<?= $item['title'] ?>" width="45" height="45" />
	<div class="desc">
		<p class="title"><?= $item['title'] ?></p>
		<p class="author"><small><?= $item['author'] ? $item['author']['fullname'] : Template::text('global_anon_user'); ?></small></p>
	</div>
	<div class="clear"></div>
