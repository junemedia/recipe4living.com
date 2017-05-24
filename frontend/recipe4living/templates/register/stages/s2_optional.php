<div class="wrapper">
  <div class="content">
    <form action="<?= SITEURL ?>/register" method="post" enctype="multipart/form-data" class="fullsubmit"><div>

      <p class="text-content" style="margin-bottom: 10px;">
      The following information is not required but will help you have a richer experience at <?= BluApplication::getSetting('storeName'); ?>. If you'd like to fill it out later, just click the <strong>'complete sign up'</strong> at the bottom of this page.
      </p>

      <div class="divider"></div>

      <h3>A bit about you</h3>
      <dl>

        <dt><label for="form_location">Location</label></dt>
        <dd>
          <input name="form_location" class="textinput"  type="text" id="form_location" size="30" value="<?= $location ?>" />
          <small class="fieldhint">(Example: Chicago, IL)</small>
        <dt><label for="form_favourite_foods">Favorite Foods</label></dt>
        <dd>
          <textarea name="form_favourite_foods" class="textinput simpletext" id="form_favourite_foods" title="Examples: pizza, pasta, sushi" rows="2" cols="30"><?= $favouriteFoods ?></textarea>
        </dd>
        <dt><label for="form_about">A little bit about you</label></dt>
        <dd>
          <textarea name="form_about" class="textinput" id="form_about" rows="5" cols="30"><? Template::out($about); ?></textarea>
        </dd>
        <dt><label for="form_dob">Your birthday</label></dt>
        <dd>
          <select name="form_dob_month" id="form_dob_year" class="dates">
            <option value="">Month</option>
            <?php for ($i = 1; $i <= 12; $i++) { ?>
            <option value="<?= $i ?>" <?= ($dobMonth == $i) ? 'selected="selected"' : '' ?>><?= $monthNames[$i] ?></option>
            <?php } ?>
          </select>

          <select name="form_dob_day" id="form_dob_day" class="dates">
            <option value="">Day</option>
            <?php for ($i = 1; $i <= 31; $i++) { ?>
            <option value="<?= $i ?>" <?= ($dobDay == $i) ? 'selected="selected"' : '' ?>><?= $i ?></option>
            <?php } ?>
          </select>

          <select name="form_dob_year" id="form_dob_year" class="dates">
            <option value="">Year</option>
            <?php for ($i = 1920; $i <= date('Y'); $i++) { ?>
            <option value="<?= $i ?>" <?= ($dobYear == $i) ? 'selected="selected"' : '' ?>><?= $i ?></option>
            <?php } ?>
          </select>
        </dd>

        <dt></dt>
        <dd>
          <label for="form_private" class="check" style="margin-bottom: 15px;"><input name="form_private" id="form_private" value="1" type="checkbox" <?= ($private) ? 'checked="checked"' : ''; ?>/>Make my profile private</label>
        </dd>
      </dl>
      <div class="clear"></div>

      <div class="divider"></div>

      <h3>Would you like to add a photo to your profile?</h3>
      <div class="fieldwrap photo">
        <ul>
          <li>
            <label for="form_imgfile">Upload a photo from your computer</label>
            <input type="file" name="photoupload" id="photoupload" class="file text-content" size="50" />
          </li>
          <li>
            <label>OR, choose an avatar below:</label>
            <div class="imageradios">
              <label><img src="<?= ASSETURL ?>/userimages/60/60/1/avatar1.png" />
                <input type="radio" name="avatar" value="1" checked="checked" /></label>
              <label><img src="<?= ASSETURL ?>/userimages/60/60/1/avatar2.png" />
                <input type="radio" name="avatar" value="2" /></label>
              <label><img src="<?= ASSETURL ?>/userimages/60/60/1/avatar3.png" />
                <input type="radio" name="avatar" value="3" /></label>
            </div>
            <div class="clear"></div>
          </li>
        </ul>
      </div>

      <div class="divider"></div>

      <div align="center"><button type="submit" class="button-md fl"><span>Complete sign up and go to my account</span></button></div>

      <input type="hidden" name="task" value="s2_optional_save" />
      <input type="hidden" id="queueid" name="queueid" value="<?= $queueId ?>" />
    </div></form>
  </div>
</div>
