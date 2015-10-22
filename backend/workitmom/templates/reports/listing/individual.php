		
		<tr class="<?= $row; ?> <?= $priority.'_priority'; ?>">
		
			<?php /*
			<td><input type="checkbox" name="reportId" value="<?= $report['id']; ?>" /></td>
			*/ ?>
			
			<td><?= $report['id']; ?></td>
			
			<td>
				<?/* <a href="?type=<?= $report['objectType']; ?>">*/?>
				<?= $report['objectType']; ?>
				<?/*</a>*/?>
			</td>
			
			<td class="textfield">
				<span class="fr"><a class="small link" href="<?= $link; ?>">(view)</a></span>
				<a class="normal" href="<?= $link; ?>"><?= Text::trim($report['text'], 100); ?></a>
			</td>
			
			<td><?= $report['time']; ?></td>
			
			<td><?= $report['status']; ?></td>
			
		</tr>