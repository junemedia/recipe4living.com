
	<?php if (!empty($items)) { ?>
	
	<ul class="results">
		<?php foreach ($items as $item) { ?>
		<li>
			<label title="<?= $item['title']; ?>">
				<?php if($item['type']!='blog') { ?>
				<img class="image" src="<?= ASSETURL; ?>/itemimages/120/120/1/<?= $item['image']['filename']; ?>" alt="<?= $item['title']; ?>" title="<?= $item['title']; ?>" />
				<?php } ?>
				
				<a class="title" href="<?= FRONTENDSITEURL.$item['link']; ?>" target="_blank" title="<?= $item['title']; ?>"><?= Text::trim($item['title'], 20); ?></a>
				<br />
				<span class="author">
					Submitted by:
					<?php if ($item['author']) { ?>
					<a href="<?= SITEURL; ?>/users/userdetails/<?= $item['author']['id']; ?>" title="<?= $item['author']['username']; ?>"><?= $item['author']['username']; ?></a>
					<?php } else { ?>
					N / A
					<?php } ?>
				</span>
				<br />
				(<?= $item['type'] ?>)
				<br />
				
				<input type="radio" name="info[item]" value="<?= $item['id']; ?>" />
			</label>
		</li>
		<?php } ?>
	</ul>
	<div class="clear"></div>
	<?= $pagination->get('buttons'); ?>

	<?php } ?>

