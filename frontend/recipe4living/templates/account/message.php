
	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">
		
				<div class="rounded"><div class="content">

					<h1>My Messages</h1>
	
					<div class="text-content">

						<?= Messages::getMessages(); ?>

						<?php include(BLUPATH_TEMPLATES.'/account/messages/nav.php'); ?>

						<div class="items-right">
							<div class="item_list small_list" id="message-detail">
								<ul id="message-current">
									<li class="odd">
										<div class="header">
											<h3 style="color:inherit"><?= $message['subject'] ?></h3>
											<small class="date"><?= Template::time($message['sent'], true) ?></small>
											<div class="clear"></div>
											<p><em>
												<?php if ($message['type'] == 'sent') { ?>
												To: <a href="<?= SITEURL.'/profile/'.$message['recipient']['username'] ?>"><?= $message['recipient']['fullname'] ?></a>
												<?php } else { ?>
												From: <a href="<?= SITEURL.'/profile/'.$message['sender']['username'] ?>"><?= $message['sender']['fullname'] ?></a>
												<?php } ?>
											</em></p>
										</div>
										<div class="body">
											<?= $message['body'] ?>
										</div>
										<div class="actions" style="margin-top: 15px;">
											<a href="<?= SITEURL ?>/account/write_message?reply=<?= $message['messageID'] ?>" class="button_dark">Reply to this message</a> | 
											<a href="<?= SITEURL ?>/account/messages?folder=<?= $folder ?>&amp;task=delete_message&amp;id=<?= $message['messageID'] ?>" onclick="if(!confirm('Are you sure you want to delete this message?')) return false">Delete this message</a><?php /*&nbsp;|&nbsp; <a href="#">Report this</a>*/ ?>
										</div>
										<div class="clear"></div>
									</li>
								</ul>

								<?php if (!empty($messageHistory)) { ?>
								<h2 class="recent">Recent message history with <?= ($message['type'] == 'sent') ? $message['recipient']['fullname'] : $message['sender']['fullname'] ?></h2>
								<ul>
									<?php
										$alt = true;
										foreach ($messageHistory as $message) {
									?>
									<li<?php if ($alt) { ?> class="odd"<?php } ?>>
										<div class="header">
											<h3 style="color:inherit;"><?= $message['subject'] ?></h3>
											<small class="date"><?= Template::time($message['sent'], true) ?></small>
											<div class="clear"></div>
											<p><em>From: <a href="<?= SITEURL.'/profile/'.$message['sender']['username'] ?>"><?= $message['sender']['fullname'] ?></a></em></p>
										</div>
										<div class="body">
											<?= $message['body'] ?>
										</div>
										<div class="clear" style="margin-bottom: 15px;"></div>
									</li>
									<?php
											$alt = !$alt;
										}
									?>
								</ul>
								<?php } ?>

							</div>

						</div>

						
					</div>

				</div></div>

			</div>

			<?= Messages::getMessages(); ?>
	
			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
	
			<div id="panel-right" class="column">
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>

				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			
			<div class="clear"></div>
		
		</div>

	</div>
