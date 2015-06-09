
	<fieldset>
		<legend>Meta groups</legend>
		
		<small>Select the filter groups under which to display this selector.</small>
		<br />
		<small><strong>Note</strong>: these require you to <em>'Push changes live'</em> to take effect on the frontend.</small>
		<br /><br />
		
		<div>
			<?php 
				foreach ($allGroups as $groupId => $group) { 
					switch ($group['type']) {
						case 'pick':
			?>
			<label>
				<input name="groups[]" type="checkbox" value="<?= $groupId; ?>"<?= isset($selectorGroups[$groupId]) ? ' checked="checked"' : ''; ?> />
				<?= $group['internalName']; ?>
			</label>
			<br />
			<?php 
							break;
							
						case 'numberpick':
						case 'numberrange':
							// Not available...yet
							break;
							
						default:
							// Not implemented
							break;
					}
				}
			?>
		</div>
	</fieldset>