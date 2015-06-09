
	<div class="popup-content">

		<div id="newsletter-form" class="standardform">
			<div class="formholder">
				<form action="<?= SITEURL ?>/" method="post"><div>

					<?= Messages::getMessages('maillist_signup') ?>

					<p class="text-content">If you would like to sign up to our newsletter, simply complete the form below.</p>

					<dl class="form">
						<dt><label for="newslettername">Name: <span class="red-ast">*</span></label></dt>
						<dd><input type="text" name="name" id="newssendername" value="<?= htmlspecialchars($name) ?>" title="<?= Template::text('global_enter') ?> <?= Template::text('global_name') ?>" class="textinput required" /></dd>

						<dt><label for="newslettername">Email: <span class="red-ast">*</span></label></dt>
						<dd><input type="text" name="email" id="newssenderemail" value="<?= htmlspecialchars($email) ?>" title="<?= Template::text('global_enter') ?> <?= Template::text('global_email') ?>" class="textinput required validate-email" /></dd>

						<?php
							if (count($mailLists) > 1) {
								foreach ($mailLists as $listId => $list) {
						?>
						<dt></dt>
						<dd>
							<label><input type="checkbox" name="maillists[<?= $listId ?>]" checked="checked" value="1" />
								<?= $list['name'] ?></label>
							<span class="clear"></span>
						</dd>
						<?php
								}
							}
						?>

						<dt></dt>
						<dd><button type="submit" title="Sign Up" class="button-md fl"><span>Sign up</span></button></dd>
					</dl>

					<div class="clear"></div>

					<input type="hidden" name="task" value="maillist_signup" />
					<?php
						if (count($mailLists) == 1) {
							$list = reset($mailLists);
					?>
					<input type="hidden" name="maillists[<?= $list['id'] ?>]" value="1" />
					<?php
						}
					?>
				</div></form>
			</div>
		</div>
		<?php Template::startScript(); ?>

			var newsletterForm = new StandardForm($('newsletter-form'));

		<?php Template::endScript(); ?>

	</div>

