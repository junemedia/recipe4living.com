
	<?php if ($searchTerm) { ?>

	<?php if (!empty($items)) { ?>

		<div class="items">
			<ul>
			<?php foreach ($items as $item) { ?>
				<li>
					<?php include(BLUPATH_BASE_TEMPLATES.'/items/quick_search_item.php'); ?>
				</li>
			<?php } ?>
			</ul>
			<div class="clear"></div>
		</div>

	<?php } else { ?>

		<div class="more">
			<?= Template::text('nav_quicksearch_items_not_found') ?>
		</div>

	<?php } ?>

	<?php } ?>
