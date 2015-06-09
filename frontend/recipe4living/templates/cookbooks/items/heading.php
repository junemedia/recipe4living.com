
	<div id="list-heading" class="rounded">
		<div class="content">
			<h2><?= $listingTitle; ?></h2>
			
			<?php if ($description) { ?>
			<p class="description text-content"><?= $description; ?></p>
			<?php } ?>
			
			<?php if ($showSearchExtra) { ?>
			<form id="listing-recipe-search-refine" action="<?= SITEURL.$listingBaseUrl; ?>" method="get" class="reloads fr"><div>
				<label for="refine_search" class="fl">
					Refine results:
					<input id="refine_search" type="text" name="searchterm_extra" value="<?= $searchTermExtra; ?>" class="textinput" autocomplete="off" />
				</label>

				<?php if ($searchTerm) { ?><input type="hidden" name="searchterm" value="<?= $searchTerm; ?>" /><?php } ?>

				<noscript>
					<button class="fl">Filter</button>
				</noscript>
			</div></form>
			<div class="clear"></div>
			<?php } ?>
		</div>
	</div>