	<div id="main-content" class="static">
		<table border="0" width="600px">
			<tr>
				<td width="600px" valign="top">
					<?php
						$iframe_url = BluApplication::getSetting('subctrURL') . "/subctr/unsub/r4l/remove.php?lid=".$_REQUEST['lid']."&jobid=".$_REQUEST['jid'];
						if (isset ($_REQUEST['email']) && $_REQUEST['email']) {
							$iframe_url .= "&email={$_REQUEST['email']}";
						}
						
						if (isset($_REQUEST['e'])) {
							setcookie("EMAIL_ID", $_REQUEST['e'], time()+642816000, "/", ".recipe4living.com");
							echo "<img src='http://jmtkg.com/plant.php?email=".$_REQUEST['e']."' width='0' height='0'></img>";
						}
						
					?>
					<iframe frameborder="0" scrolling="No" width="575" height="700" id="new_unsubscribe" src="<?php echo $iframe_url; ?>"></iframe>
				</td>
				<td width="300px" valign="top"><div id="panel-right" class="column">
				<div class="ad"><?php $this->_advert('openx_300x250atf'); ?></div>
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>
				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div></td>
			</tr>
		</table>
	</div>
	
