
	<h2><?= $internalName ?></h2>
	
	<form action="/oversight/meta" method="post" enctype="multipart/form-data"><div>
	
		<fieldset>
			<legend>Basic information</legend>
			
			<p>
				<label>
					Internal name:
					<span style="color: red;">*</span>
					<small>(only visible in the admin)</small>
					<br />
					<input type="text" name="internal" value="<?= $internalName; ?>" class="flat" />
				</label>
			</p>

			<p>
				<label><input type="checkbox" name="hidden"<?= $hidden ? ' checked="checked"' : ''; ?> />
					Hidden on frontend</label>
				<br />
				<label><input type="checkbox" name="default"<?= $default ? ' checked="checked"' : ''; ?> />
					Default <small>(automatically enabled on first visit to the site)</small></label>
				<br />
				<label><input type="checkbox" name="featured"<?= $featured ? ' checked="checked"' : ''; ?> />
					Featured</label>
			</p>
		
			<p>
				<label>
					Priority:
					<input type="text" style="width: 50px;" name="order" value="<?= $order; ?>"  class="flat" />
					<small>(The smaller the number, the higher priority. Default is zero.)</small>
				</label>
			</p>
		</fieldset>
		
		<?php include(BLUPATH_BASE_TEMPLATES.'/meta/edit_language_value.php'); ?>
		
		<?php 
			$baseUrl = '/oversight/meta/deleteValueImage?value='.$valueId;
			include(BLUPATH_BASE_TEMPLATES.'/meta/edit_images.php');
		?>
			
		<?php if ($valueId) { ?><input type="hidden" name="valueId" value="<?= $valueId; ?>" /><?php } ?>
		<input type="hidden" name="group" value="<?= $groupId; ?>" />
		<input type="hidden" name="task" value="saveValue" />
		
		<input type="submit" name="submit" value="Save Changes" class="button" /> &nbsp; <a href="/oversight/meta">Cancel</a>
			
		<small style="display: block; margin-top: 10px;">Please note that any changes will immediately take effect on the frontend.</small>

	</div></form>