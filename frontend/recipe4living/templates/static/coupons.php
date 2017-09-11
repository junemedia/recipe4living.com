	<style>
	#content-wrapper .site-wrapper{
	width:auto;
	}
	</style>
	<div id="main-content" class="static">
		<?php if ($_SERVER['REMOTE_ADDR'] == '62.219.118.71') { ?>
			Josh's URL: <iframe style="height: auto; min-height: 2500px;" frameborder="0" src="http://print.coupons.com/ccr/default.aspx?go=14824hm5810&cid=<?php echo trim($_REQUEST["cid"]); ?>&plid=<?php echo trim($_REQUEST["plid"]); ?>&crid=<?php echo trim($_REQUEST["crid"]); ?>" width="100%"></iframe>
			<?php } else { ?>
			<iframe style="height: auto; min-height: 2500px;" frameborder="0" src="http://print.coupons.com/ccr/default.aspx?go=14824hm5810" width="100%"></iframe>
			<?php } ?>
		<!--<table border="0">
			<tr>
				<td width="600px" valign="top">
					
				</td>
				<td width="300px" valign="top"><div id="panel-right" class="column">
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>
				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div></td>
			</tr>
		</table>-->
	</div>
