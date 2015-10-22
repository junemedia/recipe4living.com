
	
	<table id="reports_data" class="centered horizontal">
		
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
			if ($type){ echo '&amp;type='.$type; }
			?>">Report</a></th>
			
			<th><a href="?sort=type<?php 
			if ($sort == 'type') { echo '&amp;direction=' . ($direction == 'asc' ? 'desc' : 'asc'); } 
			?>">Type</a></th>
			
			<th>Text preview</th>
			
			<th><a href="?sort=date<?php 
			if ($sort == 'date') { echo '&amp;direction=' . ($direction == 'asc' ? 'desc' : 'asc'); }
			if ($type){ echo '&amp;type='.$type; }
			?>">Time Reported</a></th>
			
			<th><a href="?sort=status<?php 
			if ($sort == 'status') { echo '&amp;direction=' . ($direction == 'asc' ? 'desc' : 'asc'); }
			if ($type){ echo '&amp;type='.$type; }
			?>">Status</a></th>
			
		</tr>
		
		<?php if (Utility::iterable($reports)){
			foreach($reports as $report){
				$this->listing_individual($report);
			}
		} ?>
		
		<tr class="metadata">
			<td colspan="6" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
			</td>
		</tr>
		
		<?php Template::startScript(); ?>
		new Table('reports_data');
		<?php Template::endScript(); ?>
		
	</table>