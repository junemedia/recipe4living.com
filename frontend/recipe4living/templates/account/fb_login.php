
	<div id="main-content" class="login">

		<div id="column-container">

			<div id="panel-center" class="column">
			
				<div id="sign-in" class="rounded">
				<div class="content">
					
					<h2>Sign in to your account</h2>
					
					<br>
					 <?php if ($user) { ?>
				            <!--<a href="<?php echo $logoutUrl; ?>">Logout</a>-->
				        <?php } else { ?>
				            <a href="<?php echo $loginUrl; ?>"><img src="<?= SITEASSETURL; ?>/images/site/fblogin.jpg" border="0"></a>
				        <?php } ?>
					<br><br>
					
					

					
					<?= Messages::getMessages(); ?>

					<p class="text-content">Haven't got an account?  <a href="<?= SITEURL ?>/register">Sign up now</a>!</p>
				
					<div class="standardform"><div class="formholder">
						<form name="form_sign_in" id="form_sign_in" action="<?= SITEURL; ?>/account/login/" method="post" class="fullsubmit">
		
							<div class="fieldwrap">
								<label>Username or Email</label>
								<input name="form_identifier" class="textinput required" type="text" id="form_identifier" size="30" />
							</div>
							
							<?php Template::startScript(); ?>
							$('form_identifier').focus();
							<?php Template::endScript(); ?>
		
							<div class="fieldwrap">
								<label>Password</label>
								<input name="form_password" class="textinput required" type="password" id="form_password" size="30"  />
							</div>
							
							<div class="fieldwrap">
								<input type="hidden" name="redirect" value="<? if(isset($redirect)) echo  $redirect; ?>" />
								<button type="submit" class="button-md fl"><span>Sign in</span></button>
								<a id="forgot-password-link" class="fl" href="forgot-password-form">Forgot password</a>
							</div>
							
						</form>
						<div class="clear"></div>
		
						<? Template::startScript(); ?>
							new PanelSlider('forgot-password-link', 'forgot-password-form', {
								hideLink: false
							});
						<? Template::endScript(); ?>
		
						<form id="forgot-password-form" action="<?= SITEURL; ?>/account/password_reminder/" method="post">
		
							<div class="fieldwrap">
								<label>Enter your email address and we'll send you a new password.</label>
								<input name="form_identifier" class="textinput required" type="text" id="form_identifier" size="30" />
							</div>
							
							<div class="fieldwrap">
								<button type="submit" class="button-md fl"><span>Send Password</span></button>
							</div>
							
						</form>
		
					</div></div>
				
				</div></div>
				
				<div id="why-join" class="rounded">
				<div class="content">

					<h2>Why join Recipe4Living?</h2>
					
					<ul class="text-content bullets">
						<li>Meet other chefs</li>
						<li>Exchange tips and advice</li>
						<li>Publish articles &amp; share recipes</li>
						<li>Rate recipes and articles</li>
						<li>Find resources to make life easier</li>
					</ul>
					
					<a href="<?= SITEURL ?>/register/" class="button-md fl"><span>Sign up now</span></a>
					
					<div class="clear"></div>
					
				</div></div>
		
			</div>

			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
	
			<div id="panel-right" class="column">
				<?php include(BLUPATH_TEMPLATES.'/site/newsletter.php') ?>
				
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>

				<?php // $this->landing_featured_question(); ?>
				
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			
			<div class="clear"></div>
		
		</div>

	</div>
