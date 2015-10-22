
<div class="item_details">
	<div style="margin: 0px auto; width: 700px;">
	
		<div class="header_wrapper">
			<h1><?= $type['singular']; ?>:</h1>
		</div>
		
		<div class="chunk">
			<div class="header_wrapper">
				<h3>Actions:</h3>
			</div>
			<div style="margin: 10px 0px;">
				<?php if ($item['deleted']){ ?>
				<span class="resolved action">Deleted</span>
				<?php } else { ?>
				<?php if ($item['live']){ ?>
				<a class="action" target="_top" href="<?= FRONTENDSITEURL.$item['link']; ?>">Frontend</a>
				<?php } ?>
				<a class="action" href="?task=delete">Delete</a>
				<?php } ?>
			</div>
		</div>
		
		<div class="chunk">
			<div class="header_wrapper">
				<h3>Info / Stats:</h3>
			</div>
			<table class="horizontal">
				<tr class="metadata">
					<th>ID</th>
					<th>Author</th>
					<th>Date posted</th>
					<th>Status</th>
					<th>Comments</th>
					<th>Views</th>
					<th>Category</th>
				</tr>
				<tr>
					<td><?= $item['id']; ?></td>
					<td><a href="<?= SITEURL; ?>/display_person/<?= $item['author']['username']; ?>" class="info-popup"><?= $item['author']['username']; ?></a></td>
					<td><?= Template::date($item['date']); ?></td>
					<td><?= Text::fromBoolean($item['live'], 'Live. <a class="small" href="?task=set_pending">Set pending.</a>', 'Pending. <a class="small" href="?task=set_live">Set live.</a>'); ?></td>
					<td><?= $item['comment_count']; ?></td>
					<td><?= $item['views']; ?></td>
					<td>
						<form id="form_category" action="<?= SITEURL; ?>/<?= strtolower($type['plural']); ?>/details/<?= $item['id']; ?>" method="post">
							<select name="categoryId">
								<?php foreach($categories as $categoryId => $category){ ?>
								<option value="<?= $categoryId; ?>"<?= $categoryId == $item['category']['id'] ? ' selected="true"' : ''; ?>><?= $category; ?></option>
								<?php } ?>
							</select>
							<input type="hidden" name="task" value="set_category" />
							
							<?php Template::startScript(); ?>
							
							var categoryForm = $('form_category');
							categoryForm.getElement('select').addEvent('change', function(){
								categoryForm.submit();
							});
							
							<?php Template::endScript(); ?>
						</form>
					</td>
					
				</tr>
			</table>
		</div>
		
		<div class="chunk">
			<div class="header_wrapper">
				<h3>Content:</h3>
			</div>
			<form action="<?= SITEURL; ?>/<?= strtolower($type['plural']); ?>/edit/<?= $item['id']; ?>" method="post">
				<table id="item_content" class="vertical">
				
					<tr>
						<td class="metadata">Title</td>
						<td><input class="textinput" type="text" name="title" value="<?= $item['title']; ?>" /></td>
					</tr>
					
					<tr>
						<td class="metadata">Subtitle</td>
						<td><input class="textinput" type="text" name="subtitle" value="<?= $item['subtitle']; ?>" /></td>
					</tr>
					
					<tr>
						<td class="metadata">Image</td>
						<td><img src="<?php Template::image($item, 90); ?>" alt="<?= $item['title']; ?>" /></td>
					</tr>
					
					<tr>
						<td class="metadata">Body</td>
						<td><textarea id="item_body" name="body"><?= $item['body']; ?></textarea></td>
						
						<?php Template::startScript(); ?>
						
						//new TinyMCE('asdf', {theme: 'standard'});
						
						<?php Template::endScript(); ?>
					</tr>
					
					<tr>
						<td style="text-align: center;" colspan="2"><input class="bigbutton" type="submit" value="Save content" /></td>
					</tr>
					
				</table>
			</form>
		</div>
		
		<div class="chunk">
			<div class="header_wrapper">
				<h3>Tags:</h3>
			</div>
			<?php if (Utility::iterable($item['tags'])){ ?>
			<ul>
				<?php foreach($item['tags'] as $id => $tag){ ?>
				<li><?= $tag; ?> <a class="small" href="?<?= http_build_query(array('task' => 'delete_tag', 'tag' => $tag)); ?>">(Remove)</a></li>
				<?php } ?>
			</ul>
			<?php } ?>
			<div class="clear"></div>
			<form action="<?= SITEURL; ?>/<?= strtolower($type['plural']); ?>/details/<?= $item['id']; ?>" method="post">
				<input type="text" class="textinput" style="width: 250px;" name="tag" value="" />
				<input type="hidden" name="task" value="add_tag" />
				<input type="submit" value="Add tag" />
			</form>
		</div>
		
	</div>
</div>