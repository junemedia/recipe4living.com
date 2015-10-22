
<div style="margin-left: 50px;">
	<div class="body_wrapper">
		
		<div class="header_wrapper">
			<h1>Post:</h1>
		</div>
		
		<div class="chunk">
			<div class="header_wrapper">
				<h3>Actions:</h3>
			</div>
			<div>
				<?php if ($post['deleted']){ ?>
				<span class="resolved action">Deleted</span>
				<?php } else { ?>
				<a class="action" href="<?= SITEURL; ?>/groups/posts_delete/?post=<?= $post['id']; ?>">Delete</a>
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
					<th>Author</th>
					<th>Group</th>
					<th>Discussion</th>
					<th>Date Posted</th>
					<th>Reports</th>
				</tr>
				<tr>
					<td><?= $post['id']; ?></td>
					<td><a class="info-popup" href="<?= SITEURL; ?>/display_person/<?= $post['author']['username']; ?>"><?= $post['author']['username']; ?></a></td>
					<td><?= $post['group']; ?></td>
					<td><?= $post['discussion']; ?></td>
					<td><?= Template::date($post['date']); ?></td>
					<td><?= $post['reports']; ?></td>
				</tr>
			</table>
		</div>
	
		<div class="chunk">
			<div class="header_wrapper">
				<a target="_top" class="action fr" href="<?= FRONTENDSITEINSECUREURL.$post['link']; ?>">Frontend</a>
				<h3>Content:</h3>
			</div>
			<textarea style="height: 100px;" readonly><?= $post['text']; ?></textarea>
			<div class="clear"></div>
		</div>
	
	</div>
</div>