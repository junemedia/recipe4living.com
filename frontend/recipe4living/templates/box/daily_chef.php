<?php $user = reset($box['users']); ?>

	<div id="cook_of_the_day" class="rounded">
		<div class="content">
			<h2>Cook of the Day</h2>

			<div class="im">
				<a href="<?= SITEURL; ?>/profile/<?= $user['username']; ?>">
					<img alt="<?= $user['username']; ?>" src="<?= ASSETURL; ?>/userimages/75/75/1/<?= $user['image']; ?>" />
				</a>
			</div>

			<div class="desc">
				<h3><a href="<?= SITEURL; ?>/profile/<?= $user['username']; ?>"><?= $user['username']; ?></a></h3>
				<p class="text-content">
					<?= Text::trim($user['about'], 100); ?>
					<br />
					<a href="<?= SITEURL; ?>/profile/<?= $user['username']; ?>">Read more</a>
				</p>
			</div>
			<div class="clear"></div>
		</div>
	</div>