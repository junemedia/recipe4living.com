
	<?php if(!empty($slideshows)) { ?>
	
	<table id="items_data" class="centered horizontal" style="width: 1200px">
		
		<tr class="metadata">
			<td colspan="8" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
				<div style="height: 10px; margin: 14px 0px;">
					Listing <?= $pagination->get('start'); ?> - <?= $pagination->get('end'); ?> of <?= $pagination->get('total'); ?>
				</div>
			</td>
		</tr>
		
		<tr class="metadata">
			<th class="textfield" style="padding: 5px;">Image</th>
			<th class="textfield" style="padding: 5px;">Title</th>
			<th class="textfield" style="padding: 5px;">Live</th>
			<th class="textfield" style="padding: 5px;">Featured</th>
			<th class="textfield" style="padding: 5px; text-align: center;">Sequence</th>
			<th class="textfield" style="padding: 5px;">&nbsp;</th>
			<th class="textfield" style="padding: 5px;">&nbsp;</th>
			<th class="textfield" style="padding: 5px;">&nbsp;</th>
		</tr>
		
		<?php 
			$alt = false; 
			foreach ($slideshows as $slideshow) { $alt = !$alt;
		?>		
		<tr class="<?= $alt ? 'odd' : ''; ?>">
		
			<td style="text-align: center;"><img src="<?= ASSETURL.'/slideshowimages/50/50/1/'.$slideshow['filename'] ?>" alt="" /></td>
			<td class="textfield" style="padding: 5px;"><?= Text::trim($slideshow['title'], 100); ?></td>
			<td style="padding: 5px; text-align: center;"><?php if($slideshow['live']) { ?><a href="<?= SITEURL . '/slideshows/unset_live?slideshowId='.$slideshow['id'] ?>" title="Set Offline"><img src="<?= COREASSETURL; ?>/images/famfamfam/tick.png" alt="" /></a><?php } else { ?><a href="<?= SITEURL . '/slideshows/set_live?slideshowId='.$slideshow['id'] ?>" title="Set Live"><img src="<?= COREASSETURL; ?>/images/famfamfam/cross.png" alt="" /></a><?php } ?></td>
			<td style="padding: 5px; text-align: center;"><?php if($slideshow['featured']) { ?><a href="<?= SITEURL . '/slideshows/unset_featured?slideshowId='.$slideshow['id'] ?>" title="Set Featured"><img src="<?= COREASSETURL; ?>/images/famfamfam/tick.png" alt="" /></a><?php } else { ?><a href="<?= SITEURL . '/slideshows/set_featured?slideshowId='.$slideshow['id'] ?>" title="Set Featured"><img src="<?= COREASSETURL; ?>/images/famfamfam/cross.png" alt="" /></a><?php } ?></td>
			<td style="padding: 5px; text-align: center;">
			<?php if($slideshow['sequence']<$slideshowCount) { ?>
				<a href="<?= SITEURL . '/slideshows/move_slideshow?slideshowId='.$slideshow['id'].'&amp;move=down' ?>">
					<img src="<?= COREASSETURL; ?>/images/famfamfam/arrow_down.png" title="Move down" />
				</a>
			<?php } ?>
			<?php if($slideshow['sequence']>1) { ?>
				<a href="<?= SITEURL . '/slideshows/move_slideshow?slideshowId='.$slideshow['id'].'&amp;move=up' ?>">
				<img src="<?= COREASSETURL; ?>/images/famfamfam/arrow_up.png" title="Move up" />
				</a>
			<?php } ?>
			</td>
			<td style="padding: 5px; text-align: center;"><a href="<?= SITEURL . '/slideshows/slideshow?slideshowId='.$slideshow['id'] ?>">Edit</a></td>
			<td style="padding: 5px; text-align: center;"><a href="<?= SITEURL . '/slideshows/slideshowitems?slideshowId='.$slideshow['id'] ?>">Items</a></td>
			<td style="padding: 5px; text-align: center;"><a href="<?= SITEURL . '/slideshows/delete_slideshow?slideshowId='.$slideshow['id'] ?>" onclick="if(!confirm('Are you sure you want to delete this slideshow?')) return false">Delete Slideshow</a></td>
		</tr>
		<?php
			}
		?>
		
		<tr class="metadata">
			<td colspan="8" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
			</td>
		</tr>
		
	</table>
	
	<?php } else { ?>
	
		No slideshows
	
	<?php } ?>
