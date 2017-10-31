<?php Template::startScript(); ?>
		var links = $$('.expand');
		links.addEvent('click', function(event) {
			var class = '.'+event.target.id;
			var id =  event.target.id;
			if($$(class).getStyle('display') == 'block'){
				$$(class).setStyle('display', 'none');
				url = 'url('+"<?= SITEASSETURL;?>"+'/images/site/bullet.png)';
				$(id).setStyle('background-image',url);
			}else{
				$$(class).setStyle('display', 'block');
				url = 'url('+"<?= SITEASSETURL;?>"+'/images/site/expand.png)';				
				$(id).setStyle('background-image',url);
			}
		});
<?php Template::endScript(); ?>
	<div>

		<div id="column-container">
			<div id="panel-center" class="column">
				
				<div>
					<h1>Browse our full recipe range...</h1>
					<div id="site_map">
						<?= $this->_getHtmlSitemap(); ?>
						<div class="clear"></div>
					</div>
				
				</div>
			</div>

			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>

			<div id="panel-right" class="column">
				<div class="ad"><?php $this->_advert('openx_300x250atf'); ?></div>

				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>

			<div class="clear"></div>
		</div>

	</div>
