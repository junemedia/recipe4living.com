	
	<h1><?= $poll['name'] ?></h1>
	
	<form method="post">
	
	<table id="items_data" class="centered horizontal" style="width: 1200px">
		<tr class="metadata">
			<td colspan="2" style="border: 0px; text-align: center;">
				<h2><?= isset($statement) ? 'Edit statement' : 'Add new statement' ?></h2>
			</td>
		</tr>
		<tr class="metadata">
			<td style="border: 0px; text-align: right; width: 40%;">
				<label for="statement">Statement:</label>
			</td>
			<td style="border: 0px; text-align: left; width: 60%;">
				<input id="statement" name="statement" type="text" value="<?= isset($statement) ? $statement['text'] : '' ?>" size="50" maxlength="255" />
			</td>
		</tr>
		<tr class="metadata">
			<td colspan="2" style="border: 0px; text-align: center;">
				<input type="submit" name="submit" value="Submit" />
			</td>
		</tr>
	</table>
	
	<input name="task" type="hidden" value="submit_statement" />
	
	</form>
	