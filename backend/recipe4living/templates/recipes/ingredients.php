
	<h2 style="font-style: italic;">&ldquo;<?= $item['title']; ?>&rdquo;</h2> <a href="<?= FRONTENDSITEURL.$item['link']; ?>">View recipe</a>
	<div class="clear"></div>

	<form id="form_recipe_ingredients" action="<?= SITEURL.$saveIngredientsLink; ?>" method="post" class="recipe-ingredients">
		
		<style type="text/css">
			.nav_column {
				float: left;
			}
			
			button.save {
				margin: 10px;
			}
			
			#panels-nav {
				margin: 2px;
				padding: 2px;
			}
			
			#panels-nav li {
				list-style: none;
			}
			
			#panels-nav li a {
				display: block;
				background-color: #F5F5F5;
				padding: 8px;
				text-decoration: none;
			}
			
			#panels-nav li a:hover {
				background-color: #EAEAEA;
			}
			
			#panels-nav li a.current {
				background-color: #BBBBBB;
				color: white;
				font-weight: bold;
			}
			
			#panels-nav li a span {
				font-style: italic;
			}
			
			#panels {
				float: left;
				overflow: hidden;
				margin-left: 15px;
				width: 800px;
				height: 0px;
			}
			
			.panel {
				padding: 10px 0px;
			}
			
			.panel.odd {
				background-color: #F8F8F8;
			}
			
			.metavalue {
				display: block;
			}
			
			.fl {
				float: left;
			}
			
			.clear {
				clear: both;
			}
		</style>
		
		<fieldset>
			<legend>Ingredients</legend>
			
			<small>Select the ingredients to assign to this recipe for searching.</small>
			<br /><br />
			
			<div class="nav_column">
				<ul id="panels-nav">
					<?php foreach ($ingredientMetaGroups as $group) { ?>
					<li><a href="#group_<?= $group['id']; ?>"><?= $group['internalName']; ?> <span class="count"></span></a></li>
					<?php } ?>
				</ul>
				
				<button name="submit" value="1">Save Ingredients</button>
			</div>
			
			<div id="panels">
				<div id="panels-inner">
					<?php 
						$odd = false;
						foreach ($ingredientMetaGroups as $groupId => $group) {
					?>
					<div id="group_<?= $groupId; ?>" class="panel<?= $odd ? ' odd' : ''; ?>">
					
						By first letter:
						<ul id="group_<?= $groupId; ?>_nav" class="alphabet">
							<?php foreach ($group['values'] as $firstLetter => $values) { ?>
							<li><a href="#group_<?= $group['id']; ?>_<?= $firstLetter; ?>"><?= $firstLetter; ?></a></li>
							<?php } ?>
						</ul>
						<div class="clear"></div>
						
						<div id="group_<?= $groupId; ?>_panels" class="group_panels">
							<div class="group_panels_inner">
							
								<?php foreach ($group['values'] as $firstLetter => $values) { ?>
								<div id="group_<?= $groupId; ?>_<?= $firstLetter; ?>" class="group_panel">
									<div class="fl">
										<?php 
											$i = 0;
											foreach ($values as $valueId => $value) {
										?>
										<label class="metavalue">
											<input class="flat" type="checkbox" name="values[<?= $groupId; ?>][<?= $valueId; ?>]" value="<?= $valueId; ?>"<?= isset($itemIngredients[$groupId]['values'][$valueId]) ? ' checked="checked"' : ''; ?> />
											<?= $value['internalName']; ?>
										</label>
										<?php
											}
										?>
									</div>
									<div class="clear"></div>
								</div>
								<?php } ?>
								
							</div>
						</div>
						
					</div>
					<?php
							$odd = !$odd;
							Template::startScript();
					?>
						
						new Panels('group_<?= $groupId; ?>_panels', 'group_<?= $groupId; ?>_nav', {
							panels: 'div.group_panel',
							transition: 'expo:in:out',
							resetHeight: false,
							link: 'ignore'
						});
					
					<?php
							Template::endScript();
						}
					?>
				</div>
			</div>
		</fieldset>
		
		<?php Template::startScript(); reset($ingredientMetaGroups); ?>
			
			var panels = new Panels('panels', 'panels-nav', {
				mode: 'vertical',
				transition: 'expo:in:out',
				duration: 1000,
				defaultPanel: 'group_<?= key($ingredientMetaGroups); ?>'
			});
			
			/* Do panel toggler count */
			metaGroupValues = new Hash();
			panels.links.each(function(link) {
				
				/* Get target id */
				var href = link.get('href');
				var id = href.substr(href.indexOf('#') + 1);
				var inputs = this.panels.get(id).getElements('input');
				
				/* Set up initial count */
				var count = 0;
				if (inputs.length) {
					count = inputs.filter(function(input) {
						return input.get('checked');
					}).length;
				} else {
					link.setStyle('text-decoration', 'line-through');
				}
				if (count) {
					metaGroupValues.set(id, count);
					link.getElement('span').set('text', '('+count+')');
				} else {
					metaGroupValues.set(id, 0);
				}
				
				/* Add event to targets */
				inputs.each(function(input) {
					input.addEvent('change', function(event) {
						
						/* Update count */
						var checked = input.get('checked');
						var count = metaGroupValues.get(id);
						if (checked) {
							count++;
						} else {
							count--;
						}
						metaGroupValues.set(id, count);
						
						link.getElement('span').set('text', count ? '('+count+')' : '');
					});
				});
				
			}, panels);
			
		<?php Template::endScript(); ?>
		
	</form>
	
	<div class="recipe-ingredients-panel">
	
	<? /* DON'T REALLY NEED THIS
		<div class="panel actions">
			<h2>Actions</h2>
			<ul>
				<li><a href="<?= FRONTENDSITEURL.$item['link']; ?>">View recipe</a></li>
				<li><a href="<?= FRONTENDSITEURL.$editLink; ?>">Edit recipe</a></li>
			</ul>
		</div>
	*/ ?>
	
		<div class="panel current">
			<h2>Searchable ingredients</h2>
			
			<?= Messages::getMessages('recipe_ingredients'); ?>
			
			<?php if ($itemIngredients) { ?>
			<ul>
				<?php foreach ($itemIngredients as $groupId => $group) { ?>
				<li><?= Text::trim($group['internalName']); ?></li>
				<ul>
					<?php foreach ($group['values'] as $ingredient) { ?>
					<li title="<?= $ingredient['name']; ?>"><?= Text::trim($ingredient['name'], 30); ?></li>
					<?php } ?>
				</ul>
				<?php } ?>
			</ul>
		<? /*	
			<div style="text-align: center; padding-top: 10px;">
				<a href="<?= SITEURL.$ingredientAmountsLink; ?>">Set ingredient amounts</a>	
			</div>
		*/ ?>
			<?php } else { ?>
			None
			<?php } ?>
		</div>
		
		<div class="panel proposed">
			<h2>Displayed ingredients</h2>
			<?php if ($proposedIngredients) { ?>
			<ul>
				<?php foreach ($proposedIngredients as $ingredient) { ?>
				<li title="<?= $ingredient; ?>">
					<a class="ingredient_search" href="#" rel="<?= $ingredient; ?>">search</a>
					<?= Text::trim($ingredient, 30); ?>
				</li>
				<?php } ?>
				
				<?php Template::startScript(); ?>
					
					$(document.body).getElements('.ingredient_search').addEvent('click', function(event) {
						event.stop();
						
						var searchterm = this.get('rel');
						$('search').getElement('input').set('value', searchterm);
						$('search').fireEvent('submit', null);
					});
					
				<?php Template::endScript(); ?>
			</ul>
			<?php } else { ?>
			None
			<?php } ?>
		</div>
		
		<div class="panel ingredients-search">
			<h2>Ingredients Database</h2>
			<form id="search" action="<?= SITEURL; ?>/recipes/quicksearch_ingredients" method="get">
				<?php $this->quicksearch_ingredients(); ?>
			</form>
			
			<?php Template::startScript(); ?>
			
				$('search').addEvent('submit', function(event) {
					if (event) {
						event.stop();
					}
					
					var form = this;
					var searchterm = form.getElement('input[type=text]').get('value');
					var requestObj = new Request.HTML({
						url: form.get('action'),
						update: form
					});
					
					// Tell the user it's loading
					var results = $('search_results');
					if (results) {
						results.set('html', 'Loading results...');
					}
					
					requestObj.post({
						searchterm: searchterm,
						format: 'raw'
					});
				});
			
			<?php Template::endScript(); ?>
		</div>
	</div>
