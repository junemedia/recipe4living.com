
	<div class="centered horizontal">&nbsp;</div>
	<form method="POST" action="<?=SITEURL.$baseUrl.'/edit/'.$key?>">
	<table id="items_data" class="centered horizontal" style="width: 1200px;">
		
		<?php
		$first = true;
		foreach ($current as $val) {
			
			?>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold; width: 50%; vertical-align: top;"><?=$first?$key.':':''?></td>
			<td class="textfield" style="padding: 5px; width: 50%;"><input type="text" name="value[]" size="50" value="<?=$val?>" /></td>
		</tr>
		<? $first = false; } ?>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold; width: 50%; vertical-align: top;"><?=$first?$key.':':''?></td>
			<td class="textfield" style="padding: 5px; width: 50%;"><input type="text" name="value[]" size="50" value="" /></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold; width: 50%; vertical-align: top;"></td>
			<td class="textfield" style="padding: 5px; width: 50%;"><input type="submit" value="Update" /></td>
		</tr>
	</table>
	</form>
