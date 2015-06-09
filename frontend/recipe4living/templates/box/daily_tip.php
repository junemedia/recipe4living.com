<?php $content = reset($content); ?>

	<div id="tip_of_the_day" class="rounded">
		<div class="content">
			
			<?php if ($content['imageName']) { ?>
			<div class="im">
				<a href="<?= SITEURL.$content['link']; ?>">
					<img alt="<?= $content['title']; ?>" src="<?= ASSETURL; ?>/indeximages/75/75/3/<?= $content['imageName']; ?>" />
				</a>
			</div>
			<?php } ?>

			<div class="desc fl">
				<h2 class="fl"><?= $content['title']; ?></h2>
				<a href="<?= SITEURL; ?>/articles/encyclopedia_of_tips" class="text-content more fr">View all</a>
				<div class="clear"></div>
			
				<p class="text-content">
					<?= $content['text']; ?>
					<a href="<?= SITEURL.$content['link']; ?>">Read more</a>
				</p>
			</div>
			
			<div class="clear2"></div>
		</div>
	</div>