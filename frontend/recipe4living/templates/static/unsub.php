	<div id="main-content" class="static">
		<table border="0">
			<tr>
				<td width="600px" valign="top">
					<script type="text/javascript">
						function SubCtrScrollToTop() {
						    self.scroll(0,0);
						}
					</script>
					<iframe onload="SubCtrScrollToTop();" src="http://stage.r4l.popularliving.com/subctr/index.php?email=<?php $_GET['e']; ?>&source=<?php $_GET['s']; ?>&subsource=<?php $_GET['ss']; ?>" frameborder="0" width="100%" height="1500"></iframe>
				</td>
				<td width="300px" valign="top"><div id="panel-right" class="column">
				<?php include(BLUPATH_TEMPLATES.'/site/newsletter.php') ?>
				<?php if (STAGING) { ?>
				<div class="ad">
					<!-- FM Test STAMP 300x250 expands to 300x600 Zone -->
					<!--<script type='text/javascript' src='http://static.fmpub.net/zone/2461'></script>-->
					<!-- FM Test STAMP 300x250 expands to 300x600 Zone -->
				</div>
				<?php } ?>
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>
				<?php // $this->landing_featured_question(); ?>
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>
				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div></td>
			</tr>
		</table>
	</div>
	
