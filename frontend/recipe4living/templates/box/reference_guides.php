
	<div id="reference_guides" class="rounded">
		<div class="content">
			<h2>Reference Guides</h2>
			<div class="text-content">
				
				<?php if (!empty($items)) { ?>
				<ul>
					<?php $i = 0; while (!empty($items) && $i < 5 && $item = array_shift($items)) { $i++; ?>
					<li><a href="<?= SITEURL.$item['link']; ?>"><?= $item['title']; ?></a></li>
					<?php } ?>
				</ul>
				<?php } ?>
				
				<?php if (!empty($items)) { ?>
				<ul>
					<?php foreach ($items as $item) { ?>
					<li><a href="<?= SITEURL.$item['link']; ?>"><?= $item['title']; ?></a></li>
					<?php } ?>
				</ul>
				<?php } ?>
				
				<? /*
				<ul>
					<li><a href="#">Encyclopedia of Tips</a></li>
					<li><a href="#">Conversion Calculator</a></li>
					<li><a href="#">Emergency Substitutions</a></li>
					<li><a href="#">Food Storage Guidelines</a></li>
					<li><a href="#">Selecting and Storing Meat, Fruits and Vegetables</a></li>
				</ul>
							
				<ul>
					<li><a href="#">How To Guides</a></li>
					<li><a href="#">Dictionary of Herbs &amp; Spices</a></li>
					<li><a href="#">Free Daily Newsletter</a></li>
					<li><a href="#">Most Popular Recipe Collections</a></li>
				</ul>
				*/ ?>
			
				<div class="clear"></div>
			</div>
		</div>
	</div>
	
	<?php //$this->_advert('WEBSITE_RIGHT_BANNER_1'); ?>
	