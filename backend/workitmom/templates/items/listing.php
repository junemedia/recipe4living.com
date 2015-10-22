
	
	<table id="items_data" class="centered horizontal">
		
		<tr class="metadata">
			<td colspan="5" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
				<div style="height: 10px; margin: 14px 0px;">
					Listing <?= $pagination->get('start'); ?> - <?= $pagination->get('end'); ?> of <?= $pagination->get('total'); ?>
				</div>
			</td>
		</tr>
		
		<tr class="metadata">
			
			<?php /*
			<th><input class="checkall" type="checkbox" /></th>
			*/ ?>
			
			<th><a href="?type=<?= $type; ?>&sort=id<?php 
			if ($sort == 'id') { echo '&amp;direction='.($direction == 'asc' ? 'desc' : 'asc'); }
			?>"><?= $this->_getType('singular'); ?></a></th>
			
			<th><a href="?type=<?= $type; ?>&sort=date<?php 
			if ($sort == 'date') { echo '&amp;direction='.($direction == 'asc' ? 'desc' : 'asc'); }
			?>">Date </a></th>
			
			<th><a href="?type=<?= $type; ?>&sort=title<?php
			if ($sort == 'title') { echo '&amp;direction='.($direction == 'asc' ? 'desc' : 'asc'); } 
			?>">Title</a></th>			
			
			<th><a href="?type=<?= $type; ?>&sort=category<?php
			if ($sort == 'category') { echo '&amp;direction='.($direction == 'asc' ? 'desc' : 'asc'); } 
			?>">Category</a></th>
			
			<th><a href="?type=<?= $type; ?>&sort=comments<?php
			if ($sort == 'comments') { echo '&amp;direction='.($direction == 'asc' ? 'desc' : 'asc'); } 
			?>">Comments</a></th>
			
		</tr>
		
		<?php if (Utility::iterable($items)){
			foreach($items as $item){
				$this->listing_individual($item);
			}
		} ?>
		
		<tr class="metadata">
			<td colspan="6" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
			</td>
		</tr>
		
		<?php Template::startScript(); ?>
		new Table('items_data');
		<?php Template::endScript(); ?>
		
	</table>