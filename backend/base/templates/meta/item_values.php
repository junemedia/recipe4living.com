
	<table>
		<thead>
			<tr>
				<th>Filter</th>
				<th>Existing Values</th>
				<th>Add Value</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				foreach ($allGroups as $metaGroupId => $metaGroup) { 
					
					// No possibilities to select.
					if ($metaGroup['type'] == 'pick' && empty($metaGroup['values'])) {
						continue;
					}
			?>
			<tr>
				<td><?= $metaGroup['internalName']; ?></td>
				<td style="padding-bottom: 0;">
					<?php
						// If exist meta values for this item/meta group, display them
						if (!empty($itemMetaGroups[$metaGroupId]['values'])) {
							foreach ($itemMetaGroups[$metaGroupId]['values'] as $metaValue) {
								if ($metaGroup['type'] == 'pick') {
									$name = $metaValue['internalName'];
									$metaValueId = $metaValue['id'];
								} else {
									$name = $metaValue;
									$metaValueId = $metaValue;
								}
						?>
						<form action="/oversight/meta/deleteItemValue" method="post" style="margin-bottom: 3px;"><div>
							<div style="float: left; padding: 2px 0;"><?= $name; ?></div>
							<input type="hidden" name="value" value="<?= $metaValueId ?>" />
							<input type="hidden" name="group" value="<?= $metaGroupId ?>" />
							<input type="hidden" name="slug" value="<?= $itemSlug ?>" />
							<input type="submit" value="Delete" class="button" style="float: right;" />
							<div class="clear"></div>
						</div></form>
					<?php
							}
						}
					?>
				</td>
				<td>
					<form action="/oversight/meta/addItemValue" method="post"><div>
					<?php
						switch ($metaGroup['type']) {
							case 'pick':
								// Display drop down.
					?>
						<select name="value" class="flat" style="width: 202px;">
							<?php foreach ($metaGroup['values'] as $metaValue) { ?>
							<option value="<?= $metaValue['id']; ?>"><?= $metaValue['internalName']; ?></option>
							<?php } ?>
						</select>
					<?php
								break;
								
							case 'numberpick':
							case 'numberrange':
							default:
								// Display input field.
					?>
						<input type="text" name="value" value="" class="flat" style="width: 200px;" />
					<?php
								break;
						}
					?>
						<input type="hidden" name="group" value="<?= $metaGroupId ?>" />
						<input type="hidden" name="slug" value="<?= $itemSlug; ?>" />
						<input type="submit" value="Add" class="button" />
					</div></form>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>