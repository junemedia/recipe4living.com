
	<div class="message message-info" style="font-weight: normal;">
	
		<div style="float: right;">
			<a href="#stats">Jump to statistics</a>
		</div>
	
		<p><big><strong><?= ucfirst($updateType) ?> items:</strong></big></p>
		<?php 
			if (empty($updated)) {
		?>
			[none]
		<?php
			} else {
				foreach($updated as $siteId => $items) {
		?>
		<strong><?= $siteId; ?></strong> (<?= count($items); ?> items)
		<ul style="margin-top: 0;"><li><?= implode('</li><li>', $items); ?></li></ul>
		<?php
				}
			}
		?>
	</div>