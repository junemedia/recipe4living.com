	
	<h2>Meta Values</h2>
	
	<form style="display: none; float: right; margin-top: -40px; text-align: right;" action="/oversight/meta/" method="post">
		<input style="border: 3px double rgb(153, 170, 153); padding: 2px; background-color: rgb(51, 136, 51); color: white; font-weight: bold;" type="submit" value="Yum, rebuild meta!" />
		<input type="hidden" name="task" value="purgeQueue" />
	</form>
	
	<?php
		foreach ($allGroups as $groupId => $group) {
			if ($group['type'] != 'pick') {
				continue;
			}
			
			// Internal use only
			if ($group['internal']) {
				continue;
			}
	?>
		<fieldset style="float: left; width: 300px; margin-right: 10px;">
			<legend><?= $group['internalName']; ?></legend>
			
			<div style="height: 200px; overflow-y: auto; margin-bottom: 10px;">
				<table>
					<thead>
						<tr>
							<th>Filter name</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($group['values'] as $valueId => $metaValue) { ?>
						<tr>
							<td><?= $metaValue['internalName']; ?></td>
							<td>
								<form action="/oversight/meta/editValue" method="get" style="float: left; margin-right: 5px;"><div>
									<input type="hidden" name="value" value="<?= $valueId ?>" />
									<input type="submit" name="edit" value="Edit" class="button" />
								</div></form>
								<form action="/oversight/meta/deleteValue" method="post" style="float: left;"><div>
									<input type="hidden" name="value" value="<?= $valueId ?>" />
									<input type="submit" name="edit" value="Delete" class="button" />
								</div></form>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			
			<a href="/oversight/meta/addValue?group=<?= $group['id']; ?>">Add a new value</a>
		</fieldset>
	<?php
		}
	?>