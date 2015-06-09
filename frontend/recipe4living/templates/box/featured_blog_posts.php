
	<?php
		if(!$items && ($blogPosts = Template::get('blogPosts'))) {
			$items = $blogPosts;
		}
	?>
	
	<div id="blog-posts" class="rounded">
		
		<div class="content">
			
			<h2>Blog Posts</h2>
			
			<?php foreach ($items as $item) { ?>
				
				<h3><a href="<?= $item['link']; ?>"><?= $item['title'] ?></a></h3>
				
				<p class="text-content"><?= Text::trim($item['title'], 150); ?><br /><a href="<?= $item['link']; ?>">Read more</a></p>
				
				<div class="clear" style="margin-bottom: 5px;"></div>
				
			<?php } ?>
			
			<p class="text-content">
				<a href="<?= SITEURL ?>/blogs">See all blog posts</a>
			</p>
			
		</div>
		
	</div>
	