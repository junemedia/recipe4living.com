	<form action="/oversight/box/content/" method="post" enctype="multipart/form-data"><div>
	
		<? // Language code dropdown ?>
		<input type="hidden" name="langCode" value="<?= $langCode; ?>" />
		
		<p>
			<label for="title">
				Title
				<br />
				<input type="text" name="title" value="<?= $title; ?>" class="flat" style="width: 350px;" />
			</label>
		</p>
		<p>
			<label for="link">
				Link
				<br />
				<input type="text" name="link" value="<?= $link?>" class="flat" style="width: 350px;">
			</label>
		</p>
		<p>
			<label for="text">
				Text<br />
				<textarea name="text" rows="10"><?= $text; ?></textarea>
			</label>
		</p>
		
		<? // Image(s) ?>
		<div>
			<label for="image">
				<img class="image" src="<?= ASSETURL; ?>/indeximages/200/200/1/<?= $imageName; ?>" />
			
				Upload an image
				<input type="file" name="image" />
			</label>
		</div>
		<div style="clear:both;"></div>

		<? // Finish off ?>
		<span>
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
		</span>
	</div></form>