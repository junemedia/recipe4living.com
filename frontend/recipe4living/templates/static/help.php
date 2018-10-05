
	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">
		
				<div id="help" class="rounded"><div class="content">

					<h1>Help</h1>
			
					<?= Messages::getMessages(); ?>
	
					<div class="text-content">
												
						<ul class="bullets" id="top">
							<li>
								<h4>General</h4>
								<ul>
									<li><a href="#general-1">What is Recipe4Living?</a></li>
									<li><a href="#general-2">Why should I register with Recipe4Living?</a></li>
								</ul>
							</li>
							<li>
								<h4>Profile</h4>
								<ul>
									<li><a href="#profile-1">I've forgotten my password.  What do I do?</a></li>
									<li><a href="#profile-2">Can I change my username?</a></li>
									<li><a href="#profile-3">How do I delete my account with Recipe4Living?</a></li>
									<li><a href="#profile-4">How do I change my password?</a></li>
									<li><a href="#profile-5">How do I change my photo?</a></li>
								</ul>
							</li>
							<li>
								<h4>Reviews and Comments</h4>
								<ul>
									<li><a href="#comments-1">Can I review recipes &amp; articles and ask questions if I'm not a member of Recipe4Living?</a></li>
									<li><a href="#comments-2">How do I report an inappropriate comment?</a></li>
								</ul>
							</li>
							<li>
								<h4>Recipes</h4>
								<ul>
									<li><a href="#recipes-1">Who can submit a recipe to Recipe4Living?</a></li>
									<li><a href="#recipes-2">How do I submit a recipe?</a></li>
								</ul>
							</li>
						</ul>

						<div class="divider"></div>

						<h2>General</h2>
						
						<ul class="help-list">
							<li id="general-1">
								<h3>What is Recipe4Living?</h3>
								<p>Recipe4Living is dedicated to fostering an online community interested in cooking, thinking, and living. Through reader feedback and contribution, we strive to create a meaningful space for people to come together and communicate about important topics ranging from the best cheesecake recipe to grilling tips to current trends in dieting. Recipe4Living understands that the best ideas come from a diverse community working together.</p>
								<a href="#top" class="to-top">Back to top</a>
							</li>
							<li id="general-2">
								<h3>Why should I register with Recipe4Living?</h3>
								<p>Registering with Recipe4Living and becoming a member of our community allows you to share recipes and post comments on articles and recipes. Becoming a member only takes a few minutes and it's absolutely free. To become a member, please <a href="<?= SITEURL ?>/account">click here</a>.</p>
								<a href="#top" class="to-top">Back to top</a>
							</li>
						</ul>

						<div class="divider"></div>
						
						<h2>Profiles</h2>
						
						<ul class="help-list">
							<li id="profile-1">
								<h3>I&rsquo;ve forgotten my password.  What do I do?</h3>
								<p>Please go to <a href="<?= SITEURL ?>/account/login">the login page</a> and click on the link that says Forgot Password. Your new password will be emailed to the email account that you registered for Recipe4Living with. Please check your spam folder as some email programs have very strong filters.</p>
								<a href="#top" class="to-top">Back to top</a>
							</li>
							<li id="profile-2">
								<h3>Can I change my username?</h3>
								<p>Once you choose a username, it's yours forever. Please select something that you will remember well; the username is what you will use to sign in to your account at Recipe4Living.</p>
								<a href="#top" class="to-top">Back to top</a>
							</li>
							<li id="profile-3">
								<h3>How do I delete my account with Recipe4Living?</h3>
								<p>If you find that you can't participate in the Recipe4Living community as frequently as you'd like, we'd be happy to keep your account activated so you can check in whenever you have time; there is no need to delete your account. However, if you'd like to delete your account, please <a href="<?= SITEURL ?>/account/login">login</a> and click on the link next to Delete Account.</p>
								<a href="#top" class="to-top">Back to top</a>
							</li>
							<li id="profile-4">
								<h3>How do I change my password?</h3>
								<p>Once you are logged in to the site, you can change your password by going to your account and clicking the "Change password" link.</p>
								<a href="#top" class="to-top">Back to top</a>
							</li>
							<li id="profile-5">
								<h3>How do I change my photo?</h3>
								<p>Once you are logged in to the site, you can change your photo by going your account and uploading a new photo.</p>
								<a href="#top" class="to-top">Back to top</a>
							</li>
							
						</ul>

						<div class="divider"></div>
						
						<h2>Reviews and Comments</h2>

						<ul class="help-list">
							<li id="comments-1">
								<h3>Can I review recipes &amp; articles and ask questions if I'm not a member of Recipe4Living?</h3>
								<p>At this time, you have to join Recipe4Living to participate in our community. Joining Recipe4Living only takes a minute and it is absolutely free.</p>
								<a href="#top" class="to-top">Back to top</a>
							</li>
							<li id="comments-2">
								<h3>How do I report an inappropriate comment?</h3>
								<p>Click 'Report This' next to the comment you find inappropriate and it will be forwarded to our editorial team for review.</p>
								<a href="#top" class="to-top">Back to top</a>
							</li>
						</ul>

						<div class="divider"></div>
						
						<h2>Recipes</h2>

						<ul class="help-list">
							<li id="recipes-1">
								<h3>Who can submit a recipe to Recipe4Living?</h3>
								<p>At this time, you have to join Recipe4Living to participate in our community. Joining Recipe4Living only takes a minute and it is absolutely free.</p>
								<a href="#top" class="to-top">Back to top</a>
							</li>
							<li id="recipes-2">
								<h3>How do I submit a recipe?</h3>
								<p>Simply click the '<a href="<?= SITEURL ?>/share">Share a recipe</a>' link at the top of the page.</p>
								<a href="#top" class="to-top">Back to top</a>
							</li>
						</ul>


					</div>

				</div></div>

			</div>
	
			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
	
			<div id="panel-right" class="column">
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			
			<div class="clear"></div>
		</div>
	
	</div>
