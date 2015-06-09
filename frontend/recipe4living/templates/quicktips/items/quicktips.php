	<div id="list-heading" class="rounded">
		<div class="content">
			<h2><?= $listingTitle; ?> (<?= $pagination->get('total'); ?>)</h2>
		</div>
	</div>

	<div id="recipes_listing">
		<?= Messages::getMessages(); ?>
		<?php if (!empty($items)) { ?>
		
		<div class="thumb-list">
			<ul>
			<?php foreach ($items as $item) { ?>
			<li class="list">
				<div class="im fl">
					<img alt="<?= isset($item['thumbnail']['title']) ? $item['thumbnail']['title'] : $item['image']['title']; ?>" src="<?= ASSETURL; ?>/itemimages/75/75/1/<?= isset($item['thumbnail']['filename']) ? $item['thumbnail']['filename'] : $item['image']['filename']; ?>" width="75" height="75" />
				</div>
				
				<div class="desc">
					<h5 class="fl">
						<a href="<?= SITEURL.$item['link'].'#'.$item['title']; ?>"><?= $item['title']; ?></a>
					</h5>
					<div class="clear"></div>
					
					<div class="text-content">
						<p><?= $item['body']; ?></p>
					</div>
					
					<div class="clear"></div>
				</div>
				
				<div class="clear"></div>
			</li>
			<?php } ?>
			</ul>
			<div class="clear"></div>
		</div>
		
		<?= $pagination->get('buttons', array(
			'pre' => '<strong class="fl">Pages: </strong>'
		)); ?>

		<?php } ?>
		
	</div>
