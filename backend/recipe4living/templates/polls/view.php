
	<?php if(!empty($polls)) { ?>
	
	<table id="items_data" class="centered horizontal" style="width: 1200px">
		
		<tr class="metadata">
			<td colspan="6" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
				<div style="height: 10px; margin: 14px 0px;">
					Listing <?= $pagination->get('start'); ?> - <?= $pagination->get('end'); ?> of <?= $pagination->get('total'); ?>
				</div>
			</td>
		</tr>
		
		<tr class="metadata">
			<th class="textfield" style="padding: 5px;">Date</th>
			<th class="textfield" style="padding: 5px;">Name</th>
			<th class="textfield" style="padding: 5px;">Live</th>
			<th class="textfield" style="padding: 5px;">&nbsp;</th>
			<th class="textfield" style="padding: 5px;">&nbsp;</th>
			<th class="textfield" style="padding: 5px;">&nbsp;</th>
		</tr>
		
		<?php 
			$alt = false; 
			foreach ($polls as $poll) { $alt = !$alt;
		?>		
		<tr class="<?= $alt ? 'odd' : ''; ?>">
		
			<td><?= date('d/m/Y',$poll['added']) ?></td>
			<td class="textfield" style="padding: 5px;"><?= Text::trim($poll['name'], 100); ?></td>
			<td style="padding: 5px; text-align: center;"><?php if($poll['live']) { ?><a href="<?= SITEURL . '/polls/unset_live?pollId='.$poll['id'] ?>" title="Set offline"><img src="<?= COREASSETURL; ?>/images/famfamfam/tick.png" alt="" /></a><?php } else { ?><a href="<?= SITEURL . '/polls/set_live?pollId='.$poll['id'] ?>" title="Set Live"><img src="<?= COREASSETURL; ?>/images/famfamfam/cross.png" alt="" /></a><?php } ?></td>
			<td style="padding: 5px; text-align: center;"><a href="<?= SITEURL . '/polls/poll?pollId='.$poll['id'] ?>">Edit</a></td>
			<td style="padding: 5px; text-align: center;"><a href="<?= SITEURL . '/polls/statements?pollId='.$poll['id'] ?>">Statements</a></td>
			<td style="padding: 5px; text-align: center;"><a href="<?= SITEURL . '/polls/delete_poll?pollId='.$poll['id'] ?>" onclick="if(!confirm('Are you sure you want to delete this poll?')) return false">Delete Poll</a></td>
		</tr>
		<?php
			}
		?>
		
		<tr class="metadata">
			<td colspan="6" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
			</td>
		</tr>
		
	</table>
	
	<?php } ?>
