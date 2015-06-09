
	<style type="text/css">
		#panels-nav {
			float: left;
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
		<legend>Meta values</legend>
		
		<small>Select which values to group together.</small>
		<br /><br />
		
		<ul id="panels-nav">
			<?php
				foreach ($allGroups as $group) {
					switch ($group['type']) {
						case 'numberpick':
						case 'numberrange':
							continue 2;
							
						case 'pick':
			?>
			<li><a href="#group_<?= $group['id']; ?>"><?= $group['internalName']; ?> <span class="count"></span></a></li>
			<?php
							break;
					}
				}
			?>
		</ul>
		
		<div id="panels">
			<div id="panels-inner">
				<?php 
					$odd = false;
					foreach ($allGroups as $groupId => $group) {
						switch ($group['type']) {
							case 'numberpick':
							case 'numberrange':
								// Could consider saving ranges for these types, then intersect with available values on build?
								continue 2;
								
							case 'pick':
				?>
				<div class="panel<?= $odd ? ' odd' : ''; ?>" id="group_<?= $groupId; ?>">
					<div class="fl">
						<?php
							if (empty($group['values'])) {
						?>
						There are no values available for selection from this group.
						<?php
							} else {
								$i = 0;
								foreach ($group['values'] as $valueId => $value) {
						?>
						<label class="metavalue">
							<input class="flat" type="checkbox" name="values[<?= $groupId; ?>][<?= $valueId; ?>]" value="<?= $valueId; ?>"<?= isset($selectorValues[$groupId][$valueId]) ? ' checked="checked"' : ''; ?> />
							<?= $value['internalName']; ?>
						</label>
						<?php
									// Page break every 25 entries.
									if (!((++$i) % 25)) {
						?>
					</div>
					<div class="fl">
						<?php
									}
								}
							}
						?>
					</div>
					<div class="clear"></div>
				</div>
				<?php
								break;
						}
						$odd = !$odd;
					}
				?>
			</div>
		</div>
	</fieldset>
	
	<?php Template::startScript(); ?>
		
		var panels = new Panels('panels', 'panels-nav', {
			mode: 'vertical',
			transition: 'expo:in:out',
			duration: 1000
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
			inputs.addEvent('change', function(event) {
				
				/* Update count */
				var checked = $(event.target).get('checked');
				var count = metaGroupValues.get(id);
				if (checked) {
					count++;
				} else {
					count--;
				}
				metaGroupValues.set(id, count);
				
				link.getElement('span').set('text', count ? '('+count+')' : '');
			});
			
		}, panels);
		
	<?php Template::endScript(); ?>
	