
		<div class="user">
			<div class="fl img"><img src="<?php Template::image($person, 60); ?>" alt="<?= $person['name']; ?>" /></div>
			<div class="text">
				<h3><?= $person['name']; ?></h3>
				<div class="details">(username: <?= $person['username']; ?>, id: <?= $person['userid']; ?>)</div>
				<div>
					<a target="_top" href="<?= FRONTENDSITEINSECUREURL.$person['link']; ?>">Frontend</a>
					<? /*<a target="_top" href="<?= SITEURL; ?>/users/details/<?= $person['username']; ?>">Admin</a> */ ?>
				</div>
			</div>
			<div class="clear"></div>
		</div>