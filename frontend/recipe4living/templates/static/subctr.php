	<div id="main-content" class="static">
		<table border="0">
			<tr>
				<td width="600px" valign="top">
					<script type="text/javascript">
						function SubCtrScrollToTop() {
						    self.scroll(0,0);
						}
					</script>
					<iframe onload="SubCtrScrollToTop();" src="<?php echo BluApplication::getSetting('subctrURL'); ?>/subctr/index.php?email=<?php $_GET['e']; ?>&source=<?php $_GET['s']; ?>&subsource=<?php $_GET['ss']; ?>" frameborder="0" width="100%" height="1500"></iframe>
				</td>
				<td width="300px" valign="top"><div id="panel-right" class="column">
				<?php include(BLUPATH_TEMPLATES.'/site/newsletter.php') ?>
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>
				<?php // $this->landing_featured_question(); ?>
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>
				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div></td>
			</tr>
		</table>
	</div>
	
