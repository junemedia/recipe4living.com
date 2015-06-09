	
	<form method="post" enctype="multipart/form-data">
	
	<table id="items_data" class="centered horizontal" style="width: 1200px">
		<tr class="metadata">
			<td colspan="2" style="border: 0px; text-align: center;">
				<h2><?= isset($slideshow) ? 'Edit slideshow' : 'Add new slideshow' ?></h2>
			</td>
		</tr>
		<tr class="metadata">
			<td style="border: 0px; text-align: right; width: 40%;">
				<label for="title">Title:</label>
			</td>
			<td style="border: 0px; text-align: left; width: 60%;">
				<input id="title" name="title" type="text" value="<?= Template::get('title') ? Template::get('title') : (isset($slideshow) ? $slideshow['title'] : '') ?>" size="50" maxlength="255" />
			</td>
		</tr>
		<tr class="metadata">
			<td style="border: 0px; text-align: right; width: 40%;">
				<label for="body">Body:</label>
			</td>
			<td style="border: 0px; text-align: left; width: 60%;">
				<textarea id="body" name="body"><?= isset($slideshow) ? $slideshow['body'] : (Template::get('body') ? Template::get('body') : '') ?></textarea>
			</td>
		</tr>		
		<tr class="metadata">
			<td style="border: 0px; text-align: right; width: 40%;">
				<label for="image">Image:</label>
			</td>
			<td style="border: 0px; text-align: left; width: 60%;">
				<?php if(!empty($slideshow['filename'])) { ?>
					<img src="<?= ASSETURL.'/slideshowimages/100/100/1/'.$slideshow['filename'] ?>" alt="" />
					<div>
						<a href="<?= SITEURL.'/slideshows/delete_image?slideshowId='.$slideshow['id']?>" onclick="if(!confirm('Are you sure you want to delete this image?')) return false">Delete</a>
					</div>
				<?php } else { ?>
					<input id="image" name="image" type="file" size="40" />
				<?php } ?>
			</td>
		</tr>
		<tr class="metadata">
			<td colspan="2" style="border: 0px; text-align: center;">
				<input type="submit" name="submit" value="Submit" />
				<input type="submit" name="cancel" value="Cancel" />
			</td>
		</tr>
	</table>
	
	<input name="task" type="hidden" value="submit_slideshow" />
	
	</form>
	