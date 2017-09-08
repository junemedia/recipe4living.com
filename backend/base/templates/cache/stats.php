
	<div id="stats">
		<fieldset style="float: left; width: 300px; margin-right: 10px;">
			<legend>Statistics</legend>
			<?php foreach ($cacheStats as $server=>$stats) { ?>
			<table><tr><td colspan=2><?=$server;?></td></tr>
				<?php foreach ($stats as $statName=>$statVal) { ?>
					<tr><td><?=$statName;?></td><td><?=is_array($statVal)?var_dump($statVal):strpos($statName,'bytes')!==false?Template::fileSize($statVal):$statVal;?></td></tr>
				<?php } ?>
			</table>
			<?php } ?>
		</fieldset>
	
		<fieldset style="float: left; width: 300px; margin-right: 10px;">
			<legend>Session Statistics</legend>
			<?php foreach ($sessionStats as $server=>$stats) { ?>
			<table><tr><td colspan=2><?=$server;?></td></tr>
				<?php foreach ($stats as $statName=>$statVal) { ?>
					<tr><td><?=$statName;?></td><td><?=is_array($statVal)?var_dump($statVal):strpos($statName,'bytes')!==false?Template::fileSize($statVal):$statVal;?></td></tr>
				<?php } ?>
			</table>
			<?php } ?>
		</fieldset>
	</div>
