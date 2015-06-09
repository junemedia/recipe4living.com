	<div id="main-content" class="static">
		<table border="0" width="600px">
			<tr>
				<td width="600px" valign="top">
					<?php
						$iframe_url = "http://r4l.popularliving.com/subctr/unsub/r4l/entry.php?listid=".$_REQUEST['lid']."&jobid=".$_REQUEST['jid'];
						
						if (isset($_REQUEST['e'])) {
							setcookie("EMAIL_ID", $_REQUEST['e'], time()+642816000, "/", ".recipe4living.com");
							echo "<img src='http://jmtkg.com/plant.php?email=".$_REQUEST['e']."' width='0' height='0'></img>";
						}
						
					?>
					<iframe frameborder="0" scrolling="No" width="575" height="700" id="new_unsubscribe" src="<?php echo $iframe_url; ?>"></iframe>
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
	