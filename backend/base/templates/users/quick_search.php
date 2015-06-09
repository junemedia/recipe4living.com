
	<?php if ($searchTerm) { ?>

	<?php if (!empty($users)) { ?>

		<div class="items">
			<ul>
			<?php foreach ($users as $user) { ?>
				<li>
					<?php include(BLUPATH_BASE_TEMPLATES.'/users/quick_search_user.php'); ?>
				</li>
			<?php } ?>
			</ul>
			<div class="clear"></div>
		</div>

	<?php } else { ?>

		<div class="more">
			<?= Template::text('nav_quicksearch_users_not_found') ?>
		</div>

	<?php } ?>

	<?php } ?>