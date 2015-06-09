
	<div id="list-heading" class="rounded screenonly">
		<div class="content">
			<h2><?= $listingTitle; ?></h2>
			
			<p class="description text-content"><?= htmlentities($description); ?></p>
			
			<?php if ($showSearchExtra) { ?>
			<form id="listing-recipe-search-refine" action="<?= SITEURL.$listingBaseUrl; ?>" method="get" class="reloads fr"><div>
				<label for="refine_search" class="fl">
					Filter recipes:
					<input id="refine_search" type="text" name="searchterm_extra" value="<?= $searchTermExtra; ?>" class="textinput" autocomplete="off" />
				</label>

				<?php if ($searchTerm) { ?><input type="hidden" name="searchterm" value="<?= $searchTerm; ?>" /><?php } ?>

				<noscript>
					<button class="fl">Filter</button>
				</noscript>
			</div></form>
			<?php } ?>
			
			<p class="share text-content">
				<script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher=d78b8763-d922-459b-871d-1177ab5d4c23&amp;type=website&amp;buttonText=Email%20or%20Share%20This&amp;style=rotate"></script>
			</p>
			
			<div class="clear"></div>
		</div>
	</div>