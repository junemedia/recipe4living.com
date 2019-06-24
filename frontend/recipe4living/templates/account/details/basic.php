<div class="standardform" id="account-details-form">
	<div class="formholder">
	<form method="post" action="/account/details" enctype="multipart/form-data">
	
	<div>

		<dl>
			<?php /* <dt><label for="form_display_name">Public Display Name <span class="red-ast">*</span></label></dt>
			<dd>
				<input name="form_display_name" class="textinput required" type="text" id="form_display_name" value="<?= $displayname ?>" />
			</dd>*/ ?>
			
			<dt><label for="form_first_name">First Name <span class="red-ast">*</span></label></dt>
			<dd>
				<input name="form_first_name" class="textinput required" type="text" id="form_first_name" value="<?= $firstname ?>" />
			</dd>
			
			<dt><label for="form_last_name">Last Name <span class="red-ast">*</span></label></dt>
			<dd>
				<input name="form_last_name" class="textinput required" type="text" id="form_last_name" value="<?= $lastname ?>" />
			</dd>

			<dt><label for="form_email">Email Address <span class="red-ast">*</span></label></dt>
			<dd>
				<input name="form_email" class="textinput required validate-email" type="text" id="form_email" value="<?= $email ?>" />
				<small class="fieldhint"><a href="#change-password-panel" id="change-password">Change password?</a></small>
			</dd>
		</dl>
		<div class="clear"></div>

		<dl id="change-password-panel">
			<dt><label for="form_password">New Password <span class="red-ast">*</span></label></dt>
			<dd>
				<input name="form_password" class="textinput minLength maxLength" validatorProps="{minLength: 6, maxLength: 30}" type="password" id="password" value="" size="30" maxlength="30" />
			</dd>

			<dt><label for="form_password_confirm">Confirm Password <span class="red-ast">*</span></label></dt>
			<dd>
				<input name="form_password_confirm" class="textinput validate-passwordconfirm" type="password" id="form_password_confirm" size="30" maxlength="30" />
			</dd>
		</dl>
		<div class="clear"></div>

		<?php Template::startScript(); ?>

			/* Show/hide change password fields */
			var passwordPanel = new PanelSlider('change-password', 'change-password-panel');

		<?php Template::endScript(); ?>

		<dl>
			<dt><label>Location</label></dt>
			<dd>
				<input name="form_location" class="textinput" type="text" id="form_location" size="30" maxlength="100" value="<?= $location ?>" />
			</dd>

			<dt><label for="form_about">A little bit about you</label></dt>
			<dd>
				<textarea name="form_about" class="textinput" id="form_about" rows="5" cols="30"><?= $about; ?></textarea>
			</dd>
			
			<dt><label for="form_favourite_foods">Your favorite foods</label></dt>
			<dd>
				<textarea name="form_favourite_foods" class="textinput" id="form_favourite_foods" rows="5" cols="30"><?= $favouriteFoods; ?></textarea>
			</dd>
			
			<dt><label for="form_dob">Your birthday</label></dt>
			<dd>
				<select name="form_dob_month" id="form_dob_year">
					<option value="">Month</option>
					<?php for ($i = 1; $i <= 12; $i++) { ?>
					<option value="<?= $i ?>" <?= ($dobMonth == $i) ? 'selected="selected"' : '' ?>><?= $monthNames[$i] ?></option>
					<?php } ?>
				</select>

				<select name="form_dob_day" id="form_dob_day">
					<option value="">Day</option>
					<?php for ($i = 1; $i <= 31; $i++) { ?>
					<option value="<?= $i ?>" <?= ($dobDay == $i) ? 'selected="selected"' : '' ?>><?= $i ?></option>
					<?php } ?>
				</select>
				
				<select name="form_dob_year" id="form_dob_year">
					<option value="">Year</option>
					<?php for ($i = 1920; $i <= date('Y'); $i++) { ?>
					<option value="<?= $i ?>" <?= ($dobYear == $i) ? 'selected="selected"' : '' ?>><?= $i ?></option>
					<?php } ?>
				</select>
			</dd>
			
		</dl>
		<div class="clear"></div>

		<div class="divider"></div>

		<h3>Your Profile Photo</h3>
		<div class="fieldwrap photo">
			<img src="<?= ASSETURL ?>/userimages/150/150/1/<?= $user['image'] ?>" class="fl" style="margin-right: 20px;" />
			<ul class="fl">
				<li>
					<label for="form_imgfile">Replace with a photo from your computer</label>
					<input type="file" name="photoupload" id="photoupload" class="file text-content" size="50" />
				</li>
				<li>
					<label>OR, choose an avatar below:</label>
					<div class="imageradios">
						<label><img src="<?= ASSETURL ?>/userimages/60/60/1/avatar1.png" />
							<input type="radio" name="avatar" value="1"<?php if ($user['image'] == 'avatar1.png') { echo 'checked="checked"'; } ?> /></label>
						<label><img src="<?= ASSETURL ?>/userimages/60/60/1/avatar2.png" />
							<input type="radio" name="avatar" value="2"<?php if ($user['image'] == 'avatar2.png') { echo 'checked="checked"'; } ?> /></label>
						<label><img src="<?= ASSETURL ?>/userimages/60/60/1/avatar3.png" />
							<input type="radio" name="avatar" value="3"<?php if ($user['image'] == 'avatar3.png') { echo 'checked="checked"'; } ?> /></label>
					</div>
					<div class="clear"></div>
				</li>
			</ul>
			<div class="clear"></div>
		</div>

		<div class="divider"></div>

		<!--<div class="notify">
			The information below WILL NOT be made public in your profile and is for our information only. We will NEVER share your private information with any third party. To read our Privacy Policy, <a href="<?= SITEURL; ?>/privacy/" target="_blank">click here</a>.
		</div>-->

		<div class="clear"></div>

		<dl>
			<dt style="padding-top:0;"><label for="form_age">Delete Account</label></dt>
			<dd style="padding-top:0;" id="delete-account">
				<a href="/account/details?tab=delete"><span>Click here to delete your account</span></a>
				<br />
				(You'll be able to confirm this before your account is fully deleted)
			</dd>
		</dl>
		
		<div class="divider"></div>
		
		<label for="form_private" class="check" style="margin-bottom: 15px;"><input name="form_private" id="form_private" value="1" type="checkbox" <?= ($private) ? 'checked="checked"' : ''; ?>/> Make my profile private</label>
		
		<div class="divider"></div>

		<button type="submit" class="button-md fl"><span>Update Details</span></button>

		<input type="hidden" name="task" value="details_basic_save" />
		<input type="hidden" id="queueid" name="queueid" value="<?= $queueId ?>" />
	
		<div class="clear"></div>
		
	</div>
	</form>
	</div>
</div>
