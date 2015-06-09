
	<h1><?= $slideshow['title'] ?></h1>

	<?php if(!empty($slideshowItems)) { ?>
	
	<table id="items_data" class="centered horizontal" style="width: 1200px">
		
		<tr class="metadata">
			<th class="textfield" style="padding: 5px; width: 120px;">Image</th>
			<th class="textfield" style="padding: 5px;">Item</th>
			<th class="textfield" style="padding: 5px; text-align: center;">Sequence</th>
			<th class="textfield" style="padding: 5px;">&nbsp;</th>
			<th class="textfield" style="padding: 5px;">&nbsp;</th>
		</tr>
		
		<?php 
			$alt = false; 
			$slideshowItemCount = count($slideshowItems);
			foreach ($slideshowItems as $item) { $alt = !$alt;
		?>		
		<tr class="<?= $alt ? 'odd' : ''; ?>">
		
			<td class="textfield" style="padding: 5px; width: 120px; text-align: center;"><img src="<?= ASSETURL.'/slideshowimages/100/100/1/'.$item['filename'] ?>" alt="" /></td>
			<td class="textfield" style="padding: 5px;"><?= Text::trim($item['title'], 100); ?></td>
			<td style="padding: 5px; text-align: center;">
			<?php if($item['sequence']<$slideshowItemCount) { ?>
				<a href="<?= SITEURL . '/slideshows/move_slideshow_item?itemId='.$item['id'].'&amp;move=down' ?>">
					<img src="<?= COREASSETURL; ?>/images/famfamfam/arrow_down.png" title="Move down" />
				</a>
			<?php } ?>
			<?php if($item['sequence']>1) { ?>
				<a href="<?= SITEURL . '/slideshows/move_slideshow_item?itemId='.$item['id'].'&amp;move=up' ?>">
				<img src="<?= COREASSETURL; ?>/images/famfamfam/arrow_up.png" title="Move up" />
				</a>
			<?php } ?>
			</td>
			<td style="padding: 5px; text-align: center;"><a href="<?= SITEURL . '/slideshows/slideshowitem?itemId='.$item['id'] ?>">Edit</a></td>
			<td style="padding: 5px; text-align: center;"><a href="<?= SITEURL . '/slideshows/delete_slideshow_item?itemId='.$item['id'] ?>" onclick="if(!confirm('Are you sure you want to delete this item?')) return false">Delete Item</a></td>
		</tr>
		<?php
			}
		?>
		
	</table>
	
	<?php } ?>
	
	
	<p style="margin-top: 20px;">
		<a href="<?= SITEURL . '/slideshows/slideshowitem?slideshowId='.$slideshowId ?>">Add slideshow item</a>
	</p>
	<p style="margin-top: 20px;">
		<a href="<?= SITEURL ?>/slideshows/">Go back to all slideshows</a>
	</p>
