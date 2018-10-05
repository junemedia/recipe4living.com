

	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">
		
				<div id="press" class="rounded"><div class="content">

					<h1>Press &amp; Media</h1>
		
					<?= Messages::getMessages(); ?>
	
					<div class="text-content">
					
					<?php foreach ($list as $linkCategory=>$links) { ?>
					
						<h2><?= $linkCategory ?></h2>
						<ul class="bullets">
						
						<?php foreach($links as $linkId=>$link) { ?>
						
							<li>
								<h3><a href="<?= $link['link'] ?>" target="_blank"><?= $link['title'] ?></a></h3>
								<?php if(!empty($link['subtitle'])) { ?>
									<?php if(!empty($link['info']['subtitleLink'])) { ?><a href="<?= $link['info']['subtitleLink'] ?>" target="_blank"><?php } ?>
									<?= $link['subtitle'] ?><?php if(!empty($link['info']['subtitleLink'])) { ?></a><?php } ?><?= !empty($link['info']['date']) ? ', '.$link['info']['date'] : '' //date('F j, Y',strtotime($link['date'])) ?>
								<?php } ?>
							</li>
						
						<?php } ?>
						
						</ul>
						<div class="divider"></div>
					
					<?php } ?>
					
						<p>For all media inquiries, please <a href="<?= SITEURL; ?>/contact">contact us here.</a></p>

					</div>
				
				</div></div>
				
			</div>
	
			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
	
			<div id="panel-right" class="column">
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			
			<div class="clear"></div>
		</div>
	
	</div>
