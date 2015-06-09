
<?php
	//$pagination->set('locationHash','related_articles');
?>

<div id="related-articles"><div class="formholder">
	<form id="form_related_articles" action="<?= SITEURL.$listingBaseUrl; ?>" method="post">

	<input id="reloadtask" type="hidden" name="reloadtask" value="view_related_articles" />
	<input type="hidden" name="layout" value="list" />
	
	<?php if($pagination->get('total')>0) { ?>
	<div id="list-heading" class="rounded">
		<div class="content">
			<h2>Related Articles (<?= $pagination->get('total'); ?>)</h2>
		</div>
	</div>
	<?php } ?>

	<div id="recipes_listing">
	
		<?= Messages::getMessages(); ?>
		
		<?php if (!empty($items)) { ?>
		
		<div class="clear"></div>
		
		<div class="thumb-list">
			<ul>
			<?php
				$relatedArticles = Template::get('relatedArticles');
				foreach ($items as $item) {
					
					// Do some controller stuff, eugh
					$recipeBoxLink = $itemsModel->getTaskLink($item['link'], 'save_to_recipe_box');
					$recipeBoxRemoveLink = $itemsModel->getTaskLink($item['link'], 'remove_from_recipe_box');
					$recipeBoxRemoveLink .= isset($listingBaseUrl) ? '?redirect='.base64_encode(SITEURL.$listingBaseUrl) : '';
					$recipeNoteLink = $itemsModel->getTaskLink($item['link'], 'save_recipe_note');
			?>			
			<li class="list">
				<div class="im fl">
					<a href="<?= SITEURL.$item['link']; ?>">
						<img alt="<?= (!empty($item['default_alt']))?$item['default_alt']:'';?>" src="<?= ASSETURL; ?>/itemimages/75/75/1/<?= $item['image']['filename']; ?>" width="75" height="75" />
					</a>
				</div>
				<div class="desc">
					<h5 class="fl"><a href="<?= SITEURL.$item['link']; ?>" target="_blank"><?= $item['title']; ?></a></h5>
					
					<div class="clear"></div>
					
					<div class="text-content">

						<p><?= Text::trim($item['teaser'], 200); ?></p>
						<div class="shared-by fl">
							Shared by 
							<?php if ($item['author']) { ?>
							<a href="<?= SITEURL; ?>/profile/<?= $item['author']['username']; ?>"><?= $item['author']['username']; ?></a>
							<?php } else { ?>
							<?= Template::text('global_anon_user'); ?>
							<?php } ?>
						</div>
						<div class="fr">
							<div class="views fl"><?= $item['views']; ?></div>
							
							<div class="rating fl">
								<?php include(BLUPATH_TEMPLATES.'/articles/items/rating.php'); ?>
							</div>
							
							<?php 
								switch ($item['type']) {
									case 'recipe':
										// Bit of a bodge, should really duplicate whole file into another template: /recipes/items/recipes.php
										if ($item['inRecipeBox']) {
							?>
							<a class="recipe-box-remove fl" title="Remove from recipe box" href="<?= SITEURL.$recipeBoxRemoveLink; ?>"></a>
							<?php 
										} else { 
							?>
							<a class="recipe-box-add fl" title="Add to recipe box" href="<?= SITEURL.$recipeBoxLink; ?>"></a>
							<?php
										}
										break;
										
									default:
										break;
								}
							?>
						</div>

					</div>
					
					<div class="clear"></div>
					
					<?php if ($item['type'] == 'recipe' && !empty($item['recipe_note'])) {  ?>
					<div class="message message-info recipe-note"><?= htmlspecialchars($item['recipe_note']) ?></div>
					<?php } ?>
				</div>
				
				<div class="clear"></div>
				
				<div class="fieldwrap">
					<a href="<?= Template::get('removeRelatedArticleBaseUrl').'?relatedArticleId='.$item['id'] ?>" title="Remove Relationship" onclick="if(!confirm('Are you sure you want to remove this relationship?')) return false" style="font-weight:bold;font-size:12px;">Remove Relationship</a>
				</div>
				
				<div class="clear"></div>
				
			</li>
			<?php
				}
			?>
			</ul>
			
			<div class="clear"></div>
		</div>
		
		<div class="fieldwrap">
			<?= $pagination->get('buttons', array(
				'pre' => '<strong class="fl">Pages: </strong>'
			)); ?>
			<div class="clear"></div>
		</div>
		
		<?php } ?>
		
		
	</div>

	</form>

</div></div>
