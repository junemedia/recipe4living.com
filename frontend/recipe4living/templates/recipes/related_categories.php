<div id="related_categories" class="standardform rounded">
	<div class="content">
		<h2>Related Categories</h2>
		<div class="text-content">
			<ul>
				<?php foreach ($categories as $cat) { ?>
				<li><?=$cat['parent']?> &gt; <a href="<?=SITEURL.'/recipes/'.$cat['link'];?>"><?=$cat['name']?></a></li>
				<?php } ?>
				<?php if($item['author']['username']){?>
					<li>Author &gt; <a href="<?=SITEURL.'/profile/'.$item['author']['username'];?>"><?= $item['author']['username'];?></a></li>
				<?php }?>				
				
			</ul>
		</div>
	</div>
</div>
