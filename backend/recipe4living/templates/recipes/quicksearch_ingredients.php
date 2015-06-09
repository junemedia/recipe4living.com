
	<label for="searchterm">
		Search:
		<input type="text" name="searchterm" value="<?= $searchTerm; ?>" />
		<button>-&gt;</button>
	</label>
	
	
	<div id="search_results">
	<?php if ($searchTerm) { ?>
	
		<?php if (empty($ingredients)) { ?>
		
		There are no matching ingredients.
		<br />
		<small style="font-style: italic;">Try fewer, less-descriptive keywords?</small>
		
		<?php } else { ?>
		
		<ul>
			<?php foreach ($ingredients as $metaValue) { ?>
			<li meta="{<?= $metaValue['groupId']; ?>: <?= $metaValue['id']; ?>}" title="<?= $metaValue['name']; ?>">
				<?= Text::trim($metaValue['name'], 30); ?>
				<br />
				<small style="font-size: 7pt;">(under <span style="font-style: italic;"><?= $metaGroups[$metaValue['groupId']]['internalName']; ?></span>)</small>
			</li>
			<?php } ?>
		</ul>
		
		<?php if ($total > $limit) { ?>
		There were more results not listed here.
		<br />
		<small style="font-style: italic;">Try adding more keywords to refine.</small>
		<?php } ?>
		
		<?php } ?>
		
	<?php } ?>
	</div>
	
	<?php Template::startScript(); ?>
	
	var metaValues = $('search_results').getElements('li').filter(function(li) {
		return li.get('meta');
	}).each(function(li) {
		eval('var metaValue = '+li.get('meta'));
		metaValue = $H(metaValue);
		metaGroup = metaValue.getKeys();
		metaValue = metaValue.getValues();
		li.addEvent('click', function(event) {
			event.stop();
			
			var input = $(document.body).getElement('input[value='+metaValue+']');
			if (input && !input.get('checked')) {
				input.set('checked', true);
				input.fireEvent('change');
				
				// How crude.
				alert('Added: '+li.get('title'));
			}
		}).setStyle('cursor', 'pointer');
	});
	
	<?php Template::endScript(); ?>