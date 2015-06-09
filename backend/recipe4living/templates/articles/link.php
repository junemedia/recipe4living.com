
	<form method="post">

	<table id="items_data" class="centered horizontal">
		
		<tr class="metadata">
			<td colspan="3" style="border: 0px;">
				<div style="height: 10px; margin: 14px 0px;">
					<h2>Related links: <?= $article['title'] ?></h2>
				</div>
			</td>
		</tr>
		
		<tr class="metadata">
			<td style="text-align: right; width: 20%;">
				Title:
			</td>
			<td style="text-align: left; width: 80%;">
				<input name="title" type="text" value="<?= isset($link) ? $link['title'] : '' ?>" style="width: 500px" />
			</td>
		</tr>
		<tr class="metadata">
			<td style="text-align: right">
				URL:
			</td>
			<td style="text-align: left">
				<input name="href" type="text" value="<?= isset($link) ? $link['href'] : '' ?>" style="width: 500px" />
			</td>
		</tr>
		<tr class="metadata">
			<td style="text-align: right; vertical-align: top;">
				Description:
			</td>
			<td style="text-align: left">
				<textarea name="description" style="width: 500px; height: 150px;"><?= isset($link) ? $link['description'] : '' ?></textarea>
			</td>
		</tr>
				
		<tr>
			<td colspan="2" style="text-align: center">
				<input name="submit" type="submit" value="Submit" />
			</td>
		</tr>
		
	</table>
	
	<?php if(isset($link)) { ?>
	<input name="linkId" type="hidden" value="<?= $link['id'] ?>" />
	<input name="task" type="hidden" value="update_link" />
	<?php } else { ?>
	<input name="task" type="hidden" value="add_link" />
	<?php } ?>

	</form>
