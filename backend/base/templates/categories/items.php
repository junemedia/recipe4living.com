
	<h3>
		Category listing
		<?php if ($total) { ?>
		<small>(<?= $total; ?> in total)</small>
		<?php } ?>
	</h3>
	
	<?php if (empty($items)) { ?>
	
	<p>No recipes/articles to display</p>
	
	<?php } else { ?>
	
	<form action="<?= SITEURL; ?>/categories/<?= $slug; ?>" method="post" class="reloads">
		<input type="text" name="searchterm" value="<?= $searchTerm; ?>" />
		<input type="hidden" name="task" value="items" />
		<button type="submit"><span>Filter</span></button>
	</form>
	
	<ul>
		<?php foreach ($items as $item) { ?>
		<li class="file item<?= $item['type'] == 'recipe' ? ' recipe' : ''; ?>">
			<ul class="fr item_tasks">
				<li><a href="<?= FRONTENDSITEURL.$item['link']; ?>"><img src="/backend/base/images/famfamfam/page_white_magnify.png" alt="View <?= $item['type']; ?>" title="View <?= $item['type']; ?>" /></a></li>
				<li><a href="<?= FRONTENDSITEURL.$item['editLink']; ?>"><img src="/backend/base/images/famfamfam/page_white_edit.png" alt="Edit <?= $item['type']; ?>" title="Edit <?= $item['type']; ?>" /></a></li>
				<li><a href="<?= FRONTENDSITEURL.$item['editRelatedLink']; ?>"><img src="/backend/base/images/famfamfam/page_white_link.png" alt="Edit related <?= $item['type']; ?>s" title="Edit related <?= $item['type']; ?>s" /></a></li>
	<? /*			<li><a href="<?= SITEURL.$item['setFeaturedLink']; ?>"><img src="/backend/base/images/famfamfam/page_white_star.png" alt="Set featured" title="Set featured" /></a></li>	*/ ?>
				<li><a href="<?= SITEURL; ?>/categories/remove_item/<?= $slug; ?>/<?= $item['slug']; ?>"><img src="/backend/base/images/famfamfam/page_white_delete.png" alt="Remove <?= $item['type']; ?> from category" title="Remove <?= $item['type']; ?> from category" /></a></li>
			</ul>
			
			<?= $item['title']; ?>
		</li>
		<?php } ?>
	</ul>
	
	<?php 
		if ($total > $limit) {
			echo $pagination->get('buttons');
		}
		
		Template::startScript();
	?>
	
		var reloads = $('items').getElements('.reloads');
		$('items').set('tween', { link: 'cancel' });
		reloads.each(function(reload) {
			
			// Define event
			var eventName;
			switch (reload.get('tag')) {
				case 'a':
					eventName = 'click';
					break;
					
				case 'form':
					eventName = 'submit';
					break;
			}
			
			// Add event
			reload.addEvent(eventName, function(event) {
				event.stop();
				$('items').fade(0.2);
				
				// Define URL
				var requestUrl;
				switch (reload.get('tag')) {
					case 'a':
						requestUrl = reload.get('href');
						break;
						
					case 'form':
						requestUrl = reload.get('action')+'?'+reload.toQueryString();
						break;
				}
				
				// Send request
				var request = new Request.JSON({
					url: requestUrl+'&task=items&format=json',
					link: 'ignore',
					onSuccess: function(response) {
						var content = {};
						content.html = response.content.stripScripts(function(script){
							content.javascript = script;
						});
						
						$('items').set('html', content.html);
						$exec(content.javascript);
						
						new Fx.Scroll(window, {
							duration: 'long',
							wheelStops: false
						}).toElement('items');
						
						$('items').fade('show');
					}
				});
				request.send();
			});
		});
	
	<?php Template::endScript(); ?>
	
	<?php } ?>
	