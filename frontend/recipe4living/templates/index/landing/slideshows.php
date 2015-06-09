	
	<?php if($slideshows) { ?>
	
	<div id="slideshows" class="rounded">
		<div class="content">
			<h2>Slideshows</h2>

			<?php $i = 0; $slideshowCount = count($slideshows); foreach($slideshows as $slideshow) { ?>
			
			<div class="im">
				<a href="<?= SITEURL.'/slideshows/slideshow/'.$slideshow['id'] ?>">
					<img alt="" src="<?= ASSETURL.'/slideshowimages/75/75/0/'.$slideshow['filename'] ?>" />
				</a>
			</div>

			<div class="desc">
				<h3><a href="<?= SITEURL.'/slideshows/slideshow/'.$slideshow['id'] ?>"><?= $slideshow['title']; ?></a></h3>
			</div>
			<div class="desc" style="padding-top:5px;">
				<div class="text-content">
					<?= $slideshow['body'] ?>
				</div>
			</div>
			<div class="clear<?= ++$i<$slideshowCount ? ' item_separator' : '' ?>"></div>
			
			<?php } ?>
			
			<p class="text-content">
				<a href="<?= SITEURL ?>/slideshows">See all slideshows</a>
			</p>
			
		</div>
	</div>
	
	<?php } ?>
	
