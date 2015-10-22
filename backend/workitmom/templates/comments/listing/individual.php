		
		<tr class="<?= $row; ?> <?= $priority.'_priority'; ?>">
			
			<td><?= $comment['id']; ?></td>
			
			<td><?= $type; ?></td>
			
			<td class="textfield">
				<span class="fr"><a class="small link" href="<?= SITEURL; ?>/comments/details/<?= $comment['id']; ?>/">(view)</a></span>
				<?= Text::trim($comment['text'], 100); ?>
			</td>
			
			<td><?= $comment['time']; ?></td>
			
			<td><?= $comment['reports']; ?></td>
			
		</tr>