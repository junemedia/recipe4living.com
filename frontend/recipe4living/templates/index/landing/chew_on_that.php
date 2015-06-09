<?php $post = reset($chewonthatBlog['posts']); ?>

	<div id="chew_on_that" class="rounded">
		<div class="content">
			<h2><a href="<?= $chewonthatBlog['link']; ?>">Chew On That Blog</a></h2>
			
			<h3><a href="<?= $post['link']; ?>"><?= $post['title']; ?></a></h3>
				
			<p class="text-content"><?= Text::trim($post['description'], 150); ?><br /><a href="<?= $post['link']; ?>">Read more</a></p>
		</div>
	</div>