	<form action="/oversight/box/content/" method="post" enctype="multipart/form-data"><div>

		<? // Language code dropdown ?>
		<input type="hidden" name="langCode" value="<?= $langCode; ?>" />

		<p>
			<label for="text">
				<textarea name="text" rows="10"><?= $text; ?></textarea>
			</label>
		</p>

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
			<input type="submit" name="save" value="Save" class="button" />
			<?php
				}
			?>
		</span>
	</div></form>