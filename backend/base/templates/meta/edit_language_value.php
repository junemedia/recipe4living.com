
	<fieldset>
		<legend>Language-specific information</legend>
	
		<small style="float: right;"><a class="add_language" href="#">Add info for another language</a></small>
		
		<?php $languageCount = 0; foreach ($languages as $language) { extract($language); ?>
		<div class="language">
		
		<p><label>Language: 
			<select name="languages[<?= $languageCount; ?>][code]" class="flat">
				<?php foreach ($availableLanguages as $availableLangCode => $availableLangName) { ?>
				<option value="<?= $availableLangCode; ?>"<?= $langCode == $availableLangCode ? ' selected="selected"' : ''; ?>><?= $availableLangName; ?></option>
				<?php } ?>
			</select></label></p>

		<p>
			<label>
				Name:
				<span style="color: red;">*</span>
				<small>(shown on top-navigation drop downs, listings pages' filters, recipe/article details pages)</small>
				<br />
				<input type="text" name="languages[<?= $languageCount; ?>][name]" value="<?= $name; ?>" class="flat" />
			</label>
		</p>

		<p>
			<label>
				Slug:
				<small>(if in doubt, leave as is - will be automatically generated)</small>
				<br />
				<input type="text" name="languages[<?= $languageCount; ?>][slug]" value="<?= $slug; ?>" class="flat" />
			</label>
		</p>
		
		<p>
			<label>
				Description:
				<small>(shown on a listings page)</small>
				<br />
				<textarea name="languages[<?= $languageCount; ?>][description]" rows="8" cols="100"><?= $description; ?></textarea>
			</label>
		</p>
		
		<p>
			<label>
				Meta description:
				<small>(for SEO, not visible to users)</small>
				<br />
				<textarea name="languages[<?= $languageCount; ?>][pageDescription]" rows="2" cols="100"><?= $pageDescription; ?></textarea>
			</label>
		</p>
		
		<p>
			<label>
				Meta keywords:
				<small>(for SEO, not visible to users)</small>
				<br />
				<textarea name="languages[<?= $languageCount; ?>][keywords]" rows="2" cols="100"><?= $keywords; ?></textarea>
			</label>
		</p>
			
		<div style="margin-bottom: 10px;">
			<h3 style="display: inline;">Custom titles</h3>
			<small>(if empty, frontend will automatically use the Name.)</small>
		</div>
		
		<p>
			<label>
				Document title: 
				<small>(shown at top of the browser window)</small>
				<br />
				<input type="text" name="languages[<?= $languageCount; ?>][pageTitle]" value="<?= $pageTitle; ?>" class="flat" />
			</label>
		</p>
			
		<p>
			<label>
				Listing title: 
				<small>(shown on a listings page)</small>
				<br />
				<input type="text" name="languages[<?= $languageCount; ?>][listingTitle]" value="<?= $listingTitle; ?>" class="flat" />
			</label>
		</p>
		
		</div>
		<?php $languageCount++; } ?>
		
		<?php Template::startScript(); ?>
		
		// New languages
		languagesCount = <?= $languageCount; ?>;
		$(document.body).getElement('.add_language').addEvent('click', function(event) {
			event.stop();
			
			var lastLanguage = $(document.body).getElements('.language').getLast();
			var newLanguage = lastLanguage.clone();
			newLanguage.getElements('input, textarea, select').each(function(input) {
				input.set('name', input.get('name').replace(/\[[0-9]+\]/, '['+languagesCount+']'));
				input.set('value', '');
			});
			
			var pagebreak = new Element('hr').inject(lastLanguage, 'after');
			newLanguage.inject(pagebreak, 'after');
			new Fx.Scroll(window).toElement(newLanguage).chain(function() {
				newLanguage.highlight();
			});
			
			languagesCount++;
		});
		
		<?php Template::endScript(); ?>
	</fieldset>