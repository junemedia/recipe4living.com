
	<div class="thumb-list">
	<?php if($slideshows) { ?>
	<ul>
		<?php $row = 0; foreach($slideshows as $slideshow) { $row++; ?>
		<li class="grid<?= $row%3!=0 ? ' margin-right' : '' ?>">
			
			<div class="im fl">
				<a href="<?= SITEURL.'/slideshows/slideshow/'.$slideshow['id']; ?>">
					<img alt="<?= htmlentities($slideshow['title']); ?>" src="<?= ASSETURL; ?>/slideshowimages/140/140/0/<?= $slideshow['filename']; ?>" width="140" height="140" />
				</a>
			</div>
			
			<div class="desc">
				<h3><a href="<?= SITEURL.'/slideshows/slideshow/'.$slideshow['id']; ?>"><?= Text::trim($slideshow['title'], 75); ?></a></h3>
			</div>
			
			<div class="desc" style="padding-top:5px;">
				<div class="text-content">
					<?= $slideshow['body'] ?>
				</div>
			</div>
			
			<div class="clear"></div>
			
		</li>
		<?php if($row==3) $row = 0; ?>
		<?php } ?>
	</ul>
	<?php } ?>
	</div>
				
	<?= $pagination->get('buttons', array(
		'pre' => '<strong class="fl">Pages: </strong>'
	)); ?>
