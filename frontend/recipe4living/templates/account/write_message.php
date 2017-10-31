
	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">
		
				<div class="rounded"><div class="content">

					<h1>My Messages</h1>
	
					<div class="text-content">

						<?= Messages::getMessages(); ?>
<?php /* ?>
						<div id="browse_bar" class="block">
							<ul>
								<li><a href="/account/messages?folder=inbox">Inbox</a></li>
								<li><a href="/account/messages?folder=sent">Sent Messages</a></li>
								<li class="on"><a href="/account/write_message">Write a Message</a></li>
							</ul>
						</div>
<?php */ ?>
	
						<?php $folder = 'compose'; include(BLUPATH_TEMPLATES.'/account/messages/nav.php'); ?>

						<div class="items-right">
							<div class="standardform" id="write-message">
								<?php if($recipe && $recipeType){?>
									<h2>Give a Recipe</h2>
								<?php }else{?>
									<h2>Write a Message</h2>								
								<?php }?>	
								<div class="formholder">
									<form method="post" action="<?= SITEURL ?>/account/messages" class="nofancy message-write"><div>

										<dl>
											<dt style="text-align: right;"><label>To:</label></dt>
											<dd>
												<?php if ($replyMessage) { ?>
												<strong class="text-content"><?= $replyMessage['sender']['fullname'] ?></strong>
												<input type="hidden" name="recipients[]" value="<?= $replyMessage['sender']['id'] ?>" />
												<?php } elseif (isset($recipientUser)) { ?>
												<strong class="text-content"><?= $recipientUser['fullname'] ?></strong>
												<input type="hidden" name="recipients[]" value="<?= $recipientUser['id'] ?>" />
												<?php } else{?>
												<input type="text" name="recipientsbyname" class="textinput" value="" style="width:200px"/>
												<?php }?>
											</dd>
											<dd class="text-content" style="font-style:italic">
											Please enter a Recipe4Living <span style="font-weight:bold">username</span> to send your recipe! Note: Do not enter an e-mail address.
											</dd>
											<dt style="text-align: right;"><label for="subject">Subject:</label></dt>
											
											<dd>
												<?php if($recipe && $recipeType){
													echo '<span class="text-content">'.$subject.'</span>'; ?>	
													<input type="hidden" name="subject" id="subject" class="textinput" value="<?= $subject ?>" style="width:200px" />	
												<?php }else{?>
													<input type="text" name="subject" id="subject" class="textinput" value="<?= $subject ?>" style="width:200px" />
												<?php }?>
											</dd>

											<dt style="text-align: right;"><label for="message">Message:</label></dt>
											<dd>
												<?php if($recipe && $recipeType){
													echo '<span class="text-content">'.$message.'</span>';	?>
													<div style="display:none;"><textarea name="message" id="message" class="textinput" cols="12" rows="12" style="width:200px"><?= $message ?></textarea></div>																											
												<?php }else{?>
													<textarea name="message" id="message" class="textinput" cols="12" rows="12" style="width:200px"><?= $message ?></textarea>
												<?php }?>
											</dd>

											<dt></dt>
											<dd>
												<button type="submit" class="button-lg"><span>Send Message</span></button>
											</dd>
										</dl>
										<div class="clear"></div>

										<input type="hidden" name="task" value="write_message_send" />
									</div></form>
								</div>
							</div>

							<div class="item_list small_list" id="message-detail">
								<?php if (!empty($messageHistory)) { ?>
								<h2>Recent message history with <?= $replyMessage['sender']['fullname'] ?></h2>
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
										<div class="clear"></div>
									</li>
									<?php
											$alt = !$alt;
										}
									?>
								</ul>
								<?php } ?>
							</div>

						</div>

						<div class="clear"></div>

						
					</div>

				</div></div>

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
