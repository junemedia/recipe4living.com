
	<table id="deleted_users_data" class="centered horizontal">
		
		<tr class="metadata">
			<td colspan="5" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
				<div style="height: 10px; margin: 14px 0px;">
					Listing <?= $pagination->get('start'); ?>-<?= $pagination->get('end'); ?> of <?= $pagination->get('total'); ?>
				</div>
			</td>
		</tr>
		
		<tr class="metadata">
			
			<?php /*
			<th><input class="checkall" type="checkbox" /></th>
			*/ ?>
			
			<th><a href="?sort=id<?php 
			if ($sort == 'id') { echo '&amp;direction=' . ($direction == 'asc' ? 'desc' : 'asc'); }
			?>">User</a></th>
			
			<th class="medium"><a href="?sort=name<?php
			if ($sort == 'name') { echo '&amp;direction=' . ($direction == 'asc' ? 'desc' : 'asc'); }
			?>">Name</a></th>
			
			<th><a href="?sort=date<?php 
			if ($sort == 'date') { echo '&amp;direction=' . ($direction == 'asc' ? 'desc' : 'asc'); }
			?>">Date deleted</a></th>
			
			<th><a href="?sort=active<?php 
			if ($sort == 'active') { echo '&amp;direction=' . ($direction == 'asc' ? 'desc' : 'asc'); }
			?>">Activity</a></th>
			
			<th></th>
			
		</tr>
		
		<?php if (!empty($people)){
			foreach($people as $person){
				static $alt = false;
				$alt != $alt;
				?>
		
		<tr class="<?= $alt ? 'odd' : ''; ?>">
		
			<?php /*
			<td><input type="checkbox" name="reportId" value="<?= $report['id']; ?>" /></td>
			*/ ?>
			
			<td><?= $person['userid']; ?></td>
			
			<td><?= $person['name']; ?></td>
			
			<td><?= date('d/m/y H:i:s', $person['terminatedtime']); ?></td>
			
			<td><?= $person['activity']; ?></td>
			
			<td><a href="<?= SITEURL; ?>/users/reinstate_deleted/<?= $person['userid']; ?>/">Reinstate</a></td>
			
		</tr>
				<?php
			}
		} ?>
		
		<tr class="metadata">
			<td colspan="6" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
			</td>
		</tr>
		
		<?php Template::startScript(); ?>
		new Table('deleted_users_data');
		<?php Template::endScript(); ?>
		
	</table>