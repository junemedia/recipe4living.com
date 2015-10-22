
	
	<table id="posts_data" class="centered horizontal">
		
		<tr class="metadata">
			<td colspan="5" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
				<div style="height: 10px; margin: 14px 0px;">
					Listing <?= $pagination->get('start'); ?> - <?= $pagination->get('end'); ?> of <?= $pagination->get('total'); ?>
				</div>
			</td>
		</tr>
		
		<?php include(BLUPATH_TEMPLATES.'/groups/posts_listing/headings.php'); ?>
		
		<?php if (Utility::iterable($posts)){
			foreach($posts as $post){
				$this->posts_listing_individual($post);
			}
		} ?>
		
		<tr class="metadata">
			<td colspan="6" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
			</td>
		</tr>
		
		<?php Template::startScript(); ?>
		new Table('posts_data');
		<?php Template::endScript(); ?>
		
	</table>