
<div class="rating screenonly">	
		<h4>Rating:</h4>
		
		<div class="stars fl">
			<?php for ($starCount = 0; $starCount < round($item['ratings']['average']) && $starCount <5 ; $starCount++) { ?>
			<a class="hat" href="<?= SITEURL.$submitLink; ?>?rating=<?= $starCount + 1; ?>">
				<img alt="" src="<?= SITEASSETURL; ?>/images/site/icon-star.png" />
			</a>
			<?php } ?>
			<?php for ($starCount = round($item['ratings']['average']); $starCount < 5; $starCount++) { ?>
			<a class="hat" href="<?= SITEURL.$submitLink; ?>?rating=<?= $starCount + 1; ?>">
				<img alt="" src="<?= SITEASSETURL; ?>/images/site/icon-star-off.png" />
			</a>
			<?php } ?>
		</div>
		(<?php if (false) { ?><?= round($item['ratings']['average']); ?> / <?php } ?><?= $item['ratings']['count']; ?> vote<?= $item['ratings']['count'] == 1 ? '' : 's'; ?>)
		<font style="color:white;font-size:1px;"><span class="average"><?php echo round($item['ratings']['average']); ?></span>
		<span class="count"><?php echo $item['ratings']['count']; ?></span></font>
	</div>
	
	<?php Template::startScript(); ?>
		
		var ratingContainer = $(document.body).getElement('.stars');
		var ratings = new Ratings(ratingContainer, {
			current: <?= round($item['ratings']['average']); ?>,
			selector: '.hat',
			onSrc: '/site/icon-star.png',
			offSrc: '/site/icon-star-off.png'
		});
		
	<?php Template::endScript(); ?>
