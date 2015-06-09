
	<img class="image" src="<?= ASSETURL ?>/userimages/45/45/1/<?= $user['image'] ?>" alt="<?= $user['fullname'] ?>" width="45" height="45" />
	<div class="desc">
		<p class="title"><?= $user['fullname'] ?></p>
		<p class="recipes"><small><?= empty($user['articles']['recipe']) ? 0 : count($user['articles']['recipe']); ?> recipes</small></p>
	</div>
	<div class="clear"></div>