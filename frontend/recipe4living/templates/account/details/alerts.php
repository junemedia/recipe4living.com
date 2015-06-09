
	<form method="post" action="/account/details"><div>

		<p class="text-content">You can choose how you'd like to be notified about new alerts and messages at recipes4living below.</p>

		<table>
			<thead>
				<tr>
					<th class="text-content">Alert type</th>
					<th class="text-content">Send me an email</th>
					<th class="text-content">Show an alert in my account</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($alertPrefs as $type => $alertPref) { ?>
				<tr>
					<th class="text-content"><?= $alertPref['title'] ?></th>
					<td>
						<select name="alertPrefs[<?= $type ?>][emailAt]">
							<option<?php if ($alertPref['emailAt'] == 'daily') { echo ' selected="selected"'; } ?> value="daily">Daily</option>
							<option<?php if ($alertPref['emailAt'] == 'immediate') { echo ' selected="selected"'; } ?> value="immediate">Immediately</option>
							<option<?php if ($alertPref['emailAt'] == 'never') { echo ' selected="selected"'; } ?> value="never">Never</option>
						</select>
					</td>
					<td>
						<label class="check"><input type="checkbox" name="alertPrefs[<?= $type ?>][showAlert]"<?php if ($alertPref['showAlert']) { echo ' checked="checked"'; } ?> value="1" /></label>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>

		<button type="submit"><span>Update Details</span></button>

		<input type="hidden" name="task" value="details_alerts_save" />
	</div></form>
