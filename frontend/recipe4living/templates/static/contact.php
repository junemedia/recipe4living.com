
	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">
		
				<div id="contact" class="rounded"><div class="content">
					<h1>Contact Recipe4Living</h1>
			
					<?= Messages::getMessages('contact'); ?>
	
					<p class="text-content">Confused about how to prepare lobster? Chocolate cake didn't come out as planned? Having trouble with the site? Fill out our contact form and we will try to answer your questions to the best of our abilities.</p>
	
					<div id="contact-form" class="standardform"><div class="formholder">
						<form id="form_contact" name="form_contact" action="<?= SITEURL ?>/contact" method="post">
	
							<div class="fieldwrap inline">
								<label for="subject">Subject <span class="red-ast">*</span></label>
								<select name="subject" id="subject">
									<option>Please select from the options below...</option>
									<option value="feedback"<?= $subject == 'feedback' ? ' selected="selected"' : ''; ?>>Ideas to improve Recipe4Living&reg;!</option>
									<option value="support"<?= $subject == 'support' ? ' selected="selected"' : ''; ?>>Website Support &amp; Help</option>
									<option value="press"<?= $subject == 'press' ? ' selected="selected"' : ''; ?>>Press</option>
									<option value="bizdev"<?= $subject == 'bizdev' ? ' selected="selected"' : ''; ?>>Business Development</option>
								</select>
								<div class="clear"></div>
							</div>
	
							<div class="fieldwrap inline">
								<label for="email">Your Email <span class="red-ast">*</span></label>
								<input type="text" class="textinput required validate-email" maxlength="100" name="email" value="<?= $email; ?>" />
								<div class="clear"></div>
							</div>
	
							<div class="fieldwrap inline">
								<label for="name">Your Name <span class="red-ast">*</span></label>
								<input type="text" class="textinput required" maxlength="30" name="name" value="<?= $name; ?>" />
								<div class="clear"></div>
							</div>
	
							<div class="fieldwrap inline">
								<label for="comment">Message <span class="red-ast">*</span></label>
								<textarea class="textinput required" name="comment" cols="10" rows="12"><?= $comment; ?></textarea>
								<div class="clear"></div>
							</div>
	
							<div class="fieldwrap captcha">
								<div class="img">
									<img src="<?= SITEINSECUREURL ?>/captcha?format=asset&amp;uniq=<?= uniqid(); ?>" class="captcha-img" />
									<div class="captcha-reload"><small><a href="#">Get a new image</a></small></div>
								</div>
								<div class="body">
									<label for="captcha">Please enter the code to the left: <span class="red-ast">*</span></label>
									<input name="captcha" id="captcha" class="textinput validate-captcha" type="text" size="30" maxlength="100" />
									<small><a href="<?= SITEURL ?>/captcha/help" class="info-popup">What's this?</a></small>
								</div>
								<div class="clear"></div>
							</div>
	
							<button class="button-md fl" type="submit"><span>Send your message</span></button>
							
							<div class="clear"></div>
	
							<input type="hidden" name="submit" value="submit" />
							<input type="hidden" name="task" value="contact_send" />
						</form>
					</div></div>
				</div></div>

			</div>
	
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
