
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
			</p>
		
			<p>
				<label>
					Priority:
					<input type="text" style="width: 50px;" name="order" value="<?= $order; ?>"  class="flat" />
					<small>(The smaller the number, the higher priority. Default is zero.)</small>
				</label>
			</p>
		</fieldset>
		
		<?php include(BLUPATH_BASE_TEMPLATES.'/meta/edit_language_selector.php'); ?>
		
		<?php
			$baseUrl = '/oversight/meta/deleteSelectorImage?selector='.$selectorId;
			include(BLUPATH_BASE_TEMPLATES.'/meta/edit_images.php');
		?>
		
		<?php include(BLUPATH_BASE_TEMPLATES.'/meta/edit_selector_values.php'); ?>
		
		<?php include(BLUPATH_BASE_TEMPLATES.'/meta/edit_selector_groups.php'); ?>
		
		<?php if ($selectorId) { ?><input type="hidden" name="selectorId" value="<?= $selectorId; ?>" /><?php } ?>
		<input type="hidden" name="task" value="saveSelector" />
		
		<input type="submit" name="submit" value="Save Changes" class="button" /> &nbsp; <a href="/oversight/meta/selectors">Cancel</a>
			
		<small style="display: block; margin-top: 10px;">Please note that, unless mentioned above, any changes will immediately take effect on the frontend.</small>

	</div></form>