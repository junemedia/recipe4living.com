
<div class="reports_details">
	<div class="body_wrapper">
	
		<div class="header_wrapper">
			<h1>Report:</h1>
		</div>
		
		<div class="chunk">
			<div class="header_wrapper">
				<h3>Actions:</h3>
			</div>
			<div>
				<?php if ($report['resolved']){ ?>
				<span class="resolved action">Resolved</span>
				<?php } else { ?>
				<a class="action" href="<?= SITEURL; ?>/reports/resolve/?report=<?= $report['id']; ?>">Resolve</a>
				<?php } ?>
				<a class="action info-popup" href="<?= SITEURL; ?>/reports/reply/?report=<?= $report['id']; ?>">Reply</a>
			</div>
		</div>
		
		<div class="chunk">
			<div class="header_wrapper">
				<h3>Info / Stats:</h3>
			</div>
			<table class="horizontal" style="margin: 0px auto;">
				<tr class="metadata">
					<th>ID</th>
					<th>Reporter</th>
					<th>Date</th>
					<th>Status</th>
				</tr>
				<tr>
					<td><?= $report['id']; ?></td>
					<td><a class="info-popup" href="<?= SITEURL; ?>/display_person/<?= $report['author']['username']; ?>"><?= $report['author']['username']; ?></a></td>
					<td><?= Template::date($report['date']); ?></td>
					<td><?= ucfirst($report['status']); ?></td>
				</tr>
			</table>
		</div>
	
		<div class="chunk">
			<div class="header_wrapper">
				<a target="_top" class="action fr" href="<?= FRONTENDSITEINSECUREURL.$report['link']; ?>">Frontend</a>
				<a class="action fr" href="<?= SITEURL; ?>/reports/redirect/?report=<?= $report['id']; ?>">Admin</a>
				<span style="margin-right: 5px;" class="fr">Content type: <span class="italic"><?= $report['objectType']; ?></span></span>
				<h3>Content:</h3>
			</div>
			<textarea id="reported_text" readonly style="height: 200px;"><?= $report['text']; ?></textarea>
			<div class="clear"></div>
		</div>

		<div class="chunk">
			<div class="header_wrapper">
				<h3>Related reports:</h3>
			</div>
			<?php if (empty($relatedReports)){ ?>
			No reports.
			<?php } else { ?>
			<table class="horizontal" style="margin: 0px auto;">
				<tr class="metadata">
					<th>ID</td>
					<th>Reporter</th>
					<th>Actions</th>
				</tr>
				<?php foreach($relatedReports as $report){ ?>
				<tr>
					<td><?= $report['id']; ?></td>
					<td><?= $report['author']['name']; ?> <small>(username: <?= $report['author']['username']; ?>)</small></td>
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