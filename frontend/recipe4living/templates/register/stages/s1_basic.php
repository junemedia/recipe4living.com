<!--INFOLINKS_OFF-->
<form action="<?= SITEURL ?>/register" method="post" class="fullsubmit"><div>

	<dl>
		<dt><label for="form_username">Username <span class="red-ast">*</span></label></dt>
		<dd>
			<input name="form_username" class="textinput minLength validate-alphanum" validatorProps="{minLength: 6}" type="text" id="form_username" size="30" value="<?= $username ?>" />
			<small class="fieldhint">This name is unique to you and can't be changed later, so please pick something you will remember.</small>
			<div class="clear"></div>
		</dd>

		<dt><label for="form_first_name">First Name <span class="red-ast">*</span></label></dt>
		<dd>
			<input name="form_first_name" class="textinput required validate-alphanum" type="text" id="form_first_name" size="30" value="<?= $firstname ?>" />
		</dd>

		<dt><label for="form_last_name">Last Name <span class="red-ast">*</span></label></dt>
		<dd>
			<input name="form_last_name" class="textinput required validate-alphanum" type="text" id="form_last_name" size="30" value="<?= $lastname ?>" />
		</dd>

		<dt><label for="form_email">Email Address <span class="red-ast">*</span></label></dt>
		<dd>
			<input name="form_email" class="textinput required validate-email" type="text" id="form_email" value="<?= $email ?>" />
		</dd>

		<dt><label for="form_password">Password <span class="red-ast">*</span></label></dt>
		<dd>
			<input name="form_password" class="textinput minLength maxLength" validatorProps="{minLength: 6, maxLength: 30}" type="password" id="password" value="" size="30" maxlength="30" />
		</dd>

		<dt><label for="form_password_confirm">Confirm Password <span class="red-ast">*</span></label></dt>
		<dd>
			<input name="form_password_confirm" class="textinput validate-passwordconfirm" type="password" id="form_password_confirm" />
		</dd>

		<dt><label for="form_referral">Where did you hear about Recipe4Living&reg;?</label></dt>
		<dd>
			<input name="form_referral" class="textinput" type="text" id="form_referral" size="30" maxlength="100" value="<?= $referral ?>" />
			<small class="fieldhint">If you were referred by a friend, please type in their email or name in the box above.</small>
		</dd>

	</dl>

	<div class="clear"></div>

	<div class="captcha">
		<div class="img">
			<img src="<?= SITEURL; ?>/captcha?format=asset&uniq=<?= uniqid(); ?>" class="captcha-img" />
			<div class="captcha-reload"><small><a href="#">Get a new image</a></small></div>
		</div>
		<div class="body">
			<label>Please enter the code to the left:</label>
			<input name="form_captcha" class="textinput validate-captcha" type="text" id="form_captcha" size="30" maxlength="100" />
			<small><small>This helps us prevent automatic scripts from spamming you!</small></small>
		</div>

		<div class="clear"></div>
	</div>

	<p>
		<label class="check"><input name="form_newsletter" class="check" type="checkbox" id="form_newsletter" value="y"<?php if ($newsletter) { echo ' checked="checked"'; } ?> />
		<?= Template::text('registration_newsletter'); ?></label>
	</p>
	<div class="clear"></div>

	<p>
		<label class="check"><input name="form_terms" class="check validate-terms-required" type="checkbox" id="form_terms" value="y" />
		Yes, I agree to the Recipe4Living&reg; <a href="<?= SITEURL ?>/terms" class="info-popup">terms and conditions</a></label>
	</p>
	<div class="clear" style="height:10px;"></div>

	<button type="submit" class="button-md fl"><span>Continue</span></button>
	<div class="clear"></div>

	<input type="hidden" name="task" value="s1_basic_save" />
</div></form>
<!--INFOLINKS_ON-->
