
<div style="margin-left: 50px;">
	<div class="body_wrapper">
	
		<div class="header_wrapper">
			<h1>Comment:</h1>
		</div>
		
		<div class="chunk">
			<div class="header_wrapper">
				<h3>Actions:</h3>
			</div>
			<div>
				<?php if ($comment['deleted']){ ?>
				<span class="resolved action">Deleted</span>
				<?php } else { ?>
				<a class="action" href="<?= SITEURL; ?>/comments/delete/?comment=<?= $comment['id']; ?>">Delete</a>
				<?php } ?>
			</div>
		</div>
	
		<div class="chunk">	
			<div class="header_wrapper">
				<h3>Info / Stats:</h3>
			</div>
			<table class="horizontal" style="margin: 0px auto;">
				<tr class="metadata">
					<th>ID</th>
					<th>Commentor</th>
					<th>Date posted</th>
					<th>Reports</th>
				</tr>
				<tr>
					<td><?= $comment['id']; ?></td>
					<td><a class="info-popup" href="<?= SITEURL; ?>/display_person/<?= $comment['author']['username']; ?>"><?= $comment['author']['username']; ?></a></td>
					<td><?= Template::date($comment['time']); ?></td>
					<td><?= $comment['reports']; ?></td>
				</tr>
			</table>
		</div>
	
		<div class="chunk">
			<div class="header_wrapper">
				<a target="_top" class="action fr" href="<?= FRONTENDSITEINSECUREURL.$comment['link']; ?>">Frontend</a>
				<a class="action fr" href="<?= SITEURL; ?>/comments/redirect/?comment=<?= $comment['id']; ?>">Admin</a>
				<span style="margin-right: 5px;" class="fr">Content type: <span class="italic"><?= $commentType; ?></span></span>
				<h3>Content:</h3>
			</div>
			<textarea style="height: 100px;" readonly><?= $comment['text']; ?></textarea>
			<div class="clear"></div>
		</div>
	
		<div class="chunk">
			<div class="header_wrapper">
				<h3>Reports:</h3>
			</div>
			<?php if (empty($reports)){ ?>
			No reports.
			<?php } else { ?>
			<table class="horizontal" style="margin: 0px auto;">
				<tr class="metadata">
					<th>ID</th>
					<th>Reporter</th>
					<th>Actions</th>
				</tr>
				<?php foreach($reports as $report){ ?>
				<tr>
					<td><?= $report['id']; ?></td>
					<td><a class="info-popup" href="<?= SITEURL; ?>/display_person/<?= $report['author']['username']; ?>"><?= $report['author']['username']; ?></a></td>
					<td>
						<a href="<?= SITEURL; ?>/reports/details/<?= $report['id']; ?>/">Admin</a> 
						&nbsp;
						<?php if ($report['resolved']){ ?>
						<span class="resolved">Resolved</span>
						<?php } else { ?>
						<a href="<?= SITEURL; ?>/reports/resolve/?report=<?= $report['id']; ?>">Resolve</a>
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
			</table>
			<?php } ?>
		</div>
	
	</div>
</div>