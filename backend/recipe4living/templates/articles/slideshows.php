
	<div id="box_details">
		<h2>
			Article Slideshow: <?= $name; ?>
			<small><a href="#new">Add new</a></small>
		</h2>
		<?php Template::set('tinyMce', true);?>
		<ul class="slide_article">
			<?php foreach ($articleSlides as $contentId => $content) { ?>
			<li class="content"><?php $this->_content($articleId,$contentId); ?></li>
			<?php } ?>
			
			<li id="new" class="new content featuredItems">
				<?php $this->add_content($articleId); ?>
			</li>
		</ul>
	</div>