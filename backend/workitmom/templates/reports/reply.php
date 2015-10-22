
	<form class="reports_reply" action="<?= SITEURL; ?>/reports/reply_submit" method="post"><div>
		
		<div>
			<label>To:</label> <em><?= $recipient['username']; ?></em>
		</div>
		
		<div>
			<label>Subject:</label>
			<input type="text" name="subject" value="<?= $subject; ?>" />
		</div>
		
		<div>
			<label>Message:</label>
			<textarea name="message"><?= $message; ?></textarea>
		</div>
		
		<input type="hidden" name="report" value="<?= $report['id']; ?>" />
		<input type="hidden" name="redirect" value="<?= $redirect; ?>" />
		<button type="submit">Send</button>
		
	</div></form>