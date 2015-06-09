<?php $user = reset($box['users']); ?>

	<form action="<?= SITEURL; ?>/box/content/" method="post"><div>
	
		<? // Language code dropdown goes here ?>
		<input type="hidden" name="langCode" value="<?= $langCode; ?>" />
		
		<p>
			<img class="image" src="<?= ASSETURL; ?>/userimages/200/200/1/<?= $user['image']; ?>" alt="<?= $user['username']; ?>" title="<?= $user['username']; ?>" />
			<label for="info[user]">
				Username
				<br />
				<input id="username" name="info[user]" class="flat" type="text" value="<?= $username; ?>" />
				
				<?php Template::startScript(); ?>
				
					// Add user autocompleter
					var usernameInput = $(document.body).getElement('#username');
					var autocompleter = new Autocompleter(usernameInput, {
						request: {
							url: SITEURL+'/users/quicksearch'
						},
						itemClass: 'quicksearch-user autocompleter-item',
						queryKey: 'searchterm',
						hideDelay: 0,
						typeDelay: 800
					});
					
				<?php Template::endScript(); ?>
			</label>
		</p>
		
		<p>
			<label for="order">
				Priority
				<br />
				<input type="text" name="order" value="<?= $sequence; ?>" class="flat" />
			</label>
		</p>
		
		<input type="hidden" name="canAdd" value="<?= (int) $canAdd; ?>" />
		<input type="hidden" name="canDelete" value="<?= (int) $canDelete; ?>" />
		<?php 
			// Edit existing
			if (!empty($contentId)) {
		?>
		<input type="hidden" name="contentId" value="<?= $contentId; ?>" />
		<input type="submit" name="save" value="Save" class="button" />
		<?php if ($canDelete) { ?>
		<input type="submit" name="delete" value="Delete" class="button" />
		<?php } ?>
		<?php 
			// Add new
			} else {
		?>
		<input type="hidden" name="boxId" value="<?= $boxId; ?>" />
		<input type="submit" name="save" value="Add new" class="button" />
		<?php
			}
		?>
		
	</div></form>
