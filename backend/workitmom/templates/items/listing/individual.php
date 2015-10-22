		
		<tr class="<?= $row; ?> <?= $priority.'_priority'; ?>">
		
			<?php /*
			<td><input type="checkbox" name="itemId" value="<?= $item['id']; ?>" /></td>
			*/ ?>
			
			<td><?= $item['id']; ?></td>
			
			<td><?= $item['date']; ?></td>
			
			<td class="textfield">
				<span class="fr"><a class="small link" href="<?= SITEURL.$item['backend_link']; ?>">(edit)</a></span>
				<?= Text::trim($item['title'], 100); ?>
			</td>
			
			<td><?= $item['category']['name'] ? $item['category']['name'] : '<small>(n/a)</small>' ?></td>
			
			<td><?= $item['comment_count']; ?></td>
			
		</tr>