
	<div class="stars fl">
		<?php for ($starCount = 0; $starCount < round($item['ratings']['average']) && $starCount < 5; $starCount++) { ?>
		<img alt="" src="<?= SITEASSETURL; ?>/images/site/icon-star.png" />
		<?php } ?>
		<?php for ($starCount = round($item['ratings']['average']); $starCount < 5; $starCount++) { ?>
		<img alt="" src="<?= SITEASSETURL; ?>/images/site/icon-star-off.png" />
		<?php } ?>
	</div>
	(<?php if (false) { ?><?= round($item['ratings']['average'], 2); ?> avg / <?php } ?><?= $item['ratings']['count']; ?>)
