	
	<div class="centered horizontal">&nbsp;</div>


		<table id="items_data" class="centered horizontal" style="width: 1200px;">
			
			<?php if(isset($articleImage['item'])) { ?>
			<tr class="metadata">
				<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Article:</td>
				<td class="textfield" style="padding: 5px;"><a href="<?= $articleImage['item']['link'] ?>" target="_blank"><?= $articleImage['item']['title'] ?></a></td>
			</tr>
			<?php } ?>
			<tr class="metadata">
				<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold; width: 50%; vertical-align: top;">Image:</td>
				<td class="textfield" style="padding: 5px; width: 50%;"><img src="<?= ASSETURL . '/itemimages/200/200/1/' . $articleImage['filename'] ?>" alt="" /></td>
			</tr>
			<tr class="metadata">
				<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold; width: 50%;">Filename:</td>
				<td class="textfield" style="padding: 5px; width: 50%;"><?= $articleImage['filename'] ?></td>
			</tr>
			<tr class="metadata">
				<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Title:</td>
				<td class="textfield" style="padding: 5px;"><?= $articleImage['title'] ?></td>
			</tr>
			<tr class="metadata">
				<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Type:</td>
				<td class="textfield" style="padding: 5px;"><?= $articleImage['type'] ?></td>
			</tr>
			<tr class="metadata">
				<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Description:</td>
				<td class="textfield" style="padding: 5px;"><?= $articleImage['description'] ?></td>
			</tr>
			<tr class="metadata">
				<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Minidescription:</td>
				<td class="textfield" style="padding: 5px;"><?= $articleImage['minidescription'] ?></td>
			</tr>
			<tr class="metadata">
				<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">&nbsp;</td>
				<td class="textfield" style="padding: 5px;">
					<form action="<?= SITEURL. '/assets/deleteimage?filename='.urlencode($articleImage['filename']).'&amp;articleId='.$articleImage['articleId'] ?>" method="post">
						<input type="submit" name="delete" value="Delete" onclick="if(!confirm('Are you sure you want to delete this image?')) return false" />
					</form>
				</td>
			</tr>
			
		</table>

	<div class="centered horizontal">&nbsp;</div>

	<div class="centered horizontal" style="text-align: center;">
		<a href="<?= $backButtonUrl ?>">Go Back</a>
	</div>

	<div class="centered horizontal">&nbsp;</div>
