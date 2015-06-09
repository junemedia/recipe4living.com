
	<fieldset>
		<legend>Images</legend>
		<?php
			foreach ($images as $type => $image) {
		?>
		<fieldset>
			<legend><?= ucfirst($type) ?> image</legend>
				
			<?php if ($image) { ?>
			<p><img src="<?= ASSETURL; ?>/meta/150//1/<?= $image; ?>" style="border: 1px solid #ddd;" /></p>
			<?php } ?>
			
			<label><?= $image ? 'Update' : 'Add' ?>: <input type="file" id="image_<?= $type ?>" name="images[<?= $type ?>]" /></label>
			<?php if ($image) { ?>
			<a style="float: left; padding-top: 5px;" href="<?= $baseUrl; ?>&type=<?= $type ?>">Delete current image</a>
			<?php } ?>
				
			<div class="clear"></span>
		</fieldset>
		<?php
			}
		?>
	</fieldset>
	