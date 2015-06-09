
	<form action="<?= SITEURL; ?>/categories/add_item/<?= $slug; ?>" method="post" class="reloads">
		
		<small style="font-style: italic;">Type to start searching for recipes:</small>
		<br />
		
		<input id="item" type="text" name="searchterm" value="<?= $searchTerm; ?>" style="width: 200px;" />

		<?php Template::startScript(); ?>
		/*
			// Add user autocompleter
			var autocompleter = new Autocompleter($('item'), {
				request: {
					url: SITEURL+'/recipes/quicksearch'
				},
				itemClass: 'quicksearch-item autocompleter-item',
				queryKey: 'searchterm',
				hideDelay: 0,
				typeDelay: 800
			});
			*/
		<?php Template::endScript(); ?>
		
		<button type="submit" name="submit" value="submit"><span>Search</span></button>
	</form>
	
	<?php if ($searchTerm) { ?>

	<?php if (!empty($items)) { ?>

		<form method="post" action="<?=SITEURL?>/categories/add_item_save_multi/<?=$slug?>">
		<div id="items_search_results">
			<ul>
			<?php foreach ($items as $item) { ?>
				<li class="file item">
					<ul class="fr item_tasks">
						<li><a href="<?= SITEURL; ?>/categories/add_item_save/<?= $slug; ?>/<?= $item['slug']; ?>"><img src="/backend/base/images/famfamfam/page_white_add.png" alt="Add <?= $item['type']; ?> to category" title="Add <?= $item['type']; ?> to category" /></a></li>
					</ul>
					<input type="checkbox" name="item[]" value="<?=$item['slug']?>" />		
					<?= $item['title']; ?>
				</li>
			<?php } ?>
			</ul>
		</div>
		
		<input type="submit" value="Add selected to this category"/>
		</form>
		<?php echo $pagination->get('buttons'); ?>

	<?php } else { ?>

		<div class="more">
			<?= Template::text('nav_quicksearch_items_not_found') ?>
		</div>

	<?php } ?>

	<?php } ?>
