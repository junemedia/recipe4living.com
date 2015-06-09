
	<div class="stars fl">
		<?php for ($starCount = 0; $starCount < round($ritem['ratings']['average']) && $starCount < 5; $starCount++) { ?>
		<img alt="" src="<?= SITEASSETURL; ?>/images/site/icon-star.png" />
		<?php } ?>
		<?php for ($starCount = round($ritem['ratings']['average']); $starCount < 5; $starCount++) { ?>
		<img alt="" src="<?= SITEASSETURL; ?>/images/site/icon-star-off.png" />
		<?php } ?>
	</div>
	(<?php if (false) { ?><?= round($ritem['ratings']['average'], 2); ?> avg / <?php } ?><?= $ritem['ratings']['count']; ?>)
