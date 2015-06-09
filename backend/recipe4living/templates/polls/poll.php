	
	<form method="post">
	
	<table id="items_data" class="centered horizontal" style="width: 1200px">
		<tr class="metadata">
			<td colspan="2" style="border: 0px; text-align: center;">
				<h2><?= isset($poll) ? 'Edit poll' : 'Add new poll' ?></h2>
			</td>
		</tr>
		<tr class="metadata">
			<td style="border: 0px; text-align: right; width: 40%;">
				<label for="name">Name:</label>
			</td>
			<td style="border: 0px; text-align: left; width: 60%;">
				<input id="name" name="name" type="text" value="<?= isset($poll) ? $poll['name'] : '' ?>" size="50" maxlength="255" />
			</td>
		</tr>
		<tr class="metadata">
			<td colspan="2" style="border: 0px; text-align: center;">
				<input type="submit" name="submit" value="Submit" />
			</td>
		</tr>
	</table>
	
	<input name="task" type="hidden" value="submit_poll" />
	
	</form>
	