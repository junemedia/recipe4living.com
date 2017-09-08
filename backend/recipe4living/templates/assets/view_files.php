
	<h1><?php echo $folder ?></h1>

	<table id="items_data" class="centered horizontal" style="width: 1200px; margin: 20px 0px 20px 0px !important;">
		
		<?php if(!empty($urlArgsArray['filename'])) { ?>
		<tr class="metadata">
			<td colspan="8" style="border: 0px;">
				<strong>Search results for:</strong>
				<?php
					if(isset($urlArgsArray['filename'])) {
						echo ' <strong style="color: blue;">Filename:</strong> '.$urlArgsArray['filename'];
					}
				?>
			</td>
		</tr>
		<?php } ?>
		
		<tr class="metadata">
			<th class="textfield" style="width: 30%;"><a href="<?= $pageUrl ?>sort_field=filename&amp;sort_asc=<?= $sortField == 'filename' ? ( $sortAsc == 'true' ? 'false' : 'true') : 'true'; ?>">Filename</a></th>
			<th class="textfield"><a href="<?= $pageUrl ?>sort_field=size&amp;sort_asc=<?= $sortField == 'size' ? ( $sortAsc == 'true' ? 'false' : 'true') : 'true'; ?>">Size</a></th>
			<th class="textfield"><a href="<?= $pageUrl ?>sort_field=date&amp;sort_asc=<?= $sortField == 'date' ? ( $sortAsc == 'true' ? 'false' : 'true') : 'true'; ?>">Date</a></th>
			<th class="textfield">&nbsp;</th>
		</tr>
		
		<?php 
			if (!empty($dirFiles)) {
				$alt = false; foreach ($dirFiles as $dirFile) { $alt = !$alt;
		?>		
		<tr class="<?= $alt ? 'odd' : ''; ?>">
			<td class="textfield" style="padding: 5px;">
				<a href="<?= $dirFile['type'] == 'file' ? ASSETURL . $folder . $dirFile['filename'] : '?path='.urlencode($folder.$dirFile['filename']) ?>"<?= $dirFile['type'] == 'file' ? ' target="_blank"' : '' ?>><?= $dirFile['type'] == 'file' ? $dirFile['filename'] : strtoupper($dirFile['filename']) ?></a>
				<?= $dirFile['type'] == 'file' ? 'http://'. $_SERVER['HTTP_HOST'] . ASSETURL . $folder . $dirFile['filename'] : '' ?>
			</td>
			<td class="textfield" style="padding: 5px;"><?= $dirFile['type'] == 'file' ? number_format($dirFile['size']/1000,3).' KB' : '- directory -' ?></td>
			<td class="textfield" style="padding: 5px;"><?= $dirFile['type'] == 'file' ? date('Y-m-d H:i:s',$dirFile['date']) : '- directory -' ?></td>
			<td class="textfield" style="padding: 5px; text-align: center;"><?php if($dirFile['type'] == 'file') { ?><a href="<?= SITEURL.'/assets/deletefile?path='.urlencode($folder).'&amp;filename='.urlencode($dirFile['filename']) ?>" onclick="if(!confirm('Are you sure you want to delete this file?')) return false" style="display:inline">Delete</a><?php } else echo '-' ?></td>
		</tr>
		<?php
				}
			}
		?>
		
	</table>

	<form name="search_form" method="post" action="<?php echo $pageUrl ?>" enctype="multipart/form-data">
	<table class="centered horizontal" style="width: 1200px;">
		<tr>
			<td colspan="2">Upload New File</td>
		</tr>
		<tr>
			<td class="textfield" style="width: 50%; padding: 5px; text-align: right;">File:</td>
			<td style="width: 50%;"><input type="file" name="file" value="" size="50" style="width: 350px;" /></td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; text-align: right;">&nbsp;</td>
			<td>
				<input type="submit" value="Upload" />
				<input type="hidden" name="task" value="uploadFile" />
			</td>
		</tr>
	</table>
	</form>
	
	
	<form name="search_form" method="get" action="<?php echo $pageUrl ?>">
	<table class="horizontal" style="width: 1200px; margin-top: 20px;">
		<tr>
			<td colspan="2">Search</td>
		</tr>
		<tr>
			<td class="textfield" style="width: 50%; padding: 5px; text-align: right;">Filename:</td>
			<td style="width: 50%;"><input type="text" name="filename" value="<?= $filename ?>" style="width: 300px;" /></td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; text-align: right;">&nbsp;</td>
			<td>
				<input type="submit" value="Search" />
				<input type="submit" name="clear" value="Clear Search Criteria" />
			</td>
		</tr>
	</table>
	<input type="hidden" name="path" value="<?= $folder ?>" />
	</form>
