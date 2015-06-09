
	<?php if($total>0) { ?>
	
	<form action="" name="" id=""><div>

			<!--<div id="message-actions" class="fr">
				<label for="select-messages">select</label>
				<select name="actions" id="select-messages">
					<option value="">none</option>
					<option value="">read</option>
					<option value="">unread</option>
					<option value="">all</option>
				</select>
				<a href="#">Mark as Read</a>
				<a href="#">Mark as Unread</a>
				<a href="#">Delete</a>
			</div>-->

			<div class="fr"><?= $offset + 1 ?> - <?= min($offset + $limit, $total) ?> of <strong><?= $total ?></strong> message<?= Text::pluralise($total) ?></div>

			<div class="clear"></div>
			
		<table id="inbox" class="messaging-system">
			<thead>
				<tr>
					<th>Subject</th>
					<th><?= $folder == 'inbox' ? 'From' : 'To' ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
					if (!empty($messages)) {
						foreach($messages as $message) {
							$link = SITEURL.'/account/message/'.$message['messageID'];
				?>
				<tr<?= $message['read'] ? '' : ' class="new-message"'; ?>>
					<td class="message-subject"><a href="<?= $link ?>"><?= $message['subject'] ?></a></td>
					<td>
						<?php if ($folder == 'inbox') { ?>
						<a href="<?= SITEURL.'/profile/'.$message['sender']['username'] ?>"><?= $message['sender']['fullname'] ?></a>
						<?php } else { ?>
						<a href="<?= SITEURL.'/profile/'.$message['recipient']['username'] ?>"><?= $message['recipient']['fullname'] ?></a>
						<?php } ?>
						<small><?= Template::time($message['sent'], true) ?></small>
					</td>
					<td class="message-actions"><a href="/account/messages?folder=<?= $folder ?>&amp;task=delete_message&amp;id=<?= $message['messageID'] ?>" title="Delete" onclick="if(!confirm('Are you sure you want to delete this message?')) return false"><img src="<?= SITEASSETURL ?>/images/site/icon-delete.png" alt="Delete" align="absmiddle" />Delete</a></td>
				</tr>
				<?php
						}
					}
				?>
			</tbody>
		</table>

		<?= $pagination->get('buttons'); ?>

	</div></form>
	
	<?php } else { ?>
	
	<div>
		No messages.
	</div>
	
	<?php } ?>
	