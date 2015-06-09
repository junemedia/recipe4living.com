
	<div class="slideshow-content">
		
		<h3><?= $slideshowItem['title'] ?></h3>
		
		<div class="im fl">
			<img alt="<?= htmlentities($slideshowItem['title']); ?>" src="<?= ASSETURL; ?>/slideshowimages/250/250/0/<?= $slideshowItem['filename']; ?>" width="250" height="250" />
		</div>
		
		<div class="desc">
			<div class="body">
				<?= $slideshowItem['body'] ?>
			</div>
			<div style="padding-top:20px;">
				<?php if($page > 1){?>
				<a href="<?= SITEURL ?>/slideshows/slideshow/<?= $slideshowId ?>?page=<?= $page-1 ?>" class="button-lg fl"><span>Previous Slide</span></a>
				<?php }if($page < $total){?>
				<a href="<?= SITEURL ?>/slideshows/slideshow/<?= $slideshowId ?>?page=<?= $page+1 ?>" class="button-lg fr"><span>Next Slide</span></a>
				<?php }?>
			</div>
				
		</div>
		
		<div class="clear"></div>
		<div id="slideshow-nav">
			<ol>
			<?php $i = 0; foreach($slideshowItems as $item) { $i++; ?>
				<li>
					<?php if($page!=$i) { ?><a class="reloads" href="<?= SITEURL ?>/slideshows/slideshow/<?= $slideshowId ?>?page=<?= $i ?>"><?php } else { ?><strong><?php } ?>
					<?= $item['title'] ?>
					<?php if($page!=$i) { ?></a><?php } else { ?></strong><?php } ?>
					</a>
				</li>
			<?php } ?>
			</ol>
		</div>
		<div class="clear"></div>
		
	</div>
	
	<?= $pagination->get('buttons', array(
		'pre' => '<strong class="fl">Slides: </strong>'
	)); ?>
