		
		<tr class="<?= $row; ?> <?= $priority.'_priority'; ?>">
		
			<?php /*
			<td><input type="checkbox" name="reportId" value="<?= $report['id']; ?>" /></td>
			*/ ?>
			
			<td><?= $post['id']; ?></td>
			
			<td><?= $post['author']['username']; ?></td>
			
			<td class="textfield">
				<span class="fr"><a class="small link" href="<?= SITEURL; ?>/groups/posts_details/<?= $post['id']; ?>/">(more)</a></span>
				<?= Text::trim($post['text'], 100); ?>
			</td>
			
			<td><?= $post['time']; ?></td>
			
			<td><?= $post['reports']; ?></td>
			
		</tr>