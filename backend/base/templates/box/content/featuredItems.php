
	<div<?= $contentId ? ' title="'.$title.'"' : ''; ?>>
		
		<?php if ($contentId) { ?>
		
		<?php if($box['slug']!='featured_blog_posts') { ?>
		<img class="image" src="<?= ASSETURL; ?>/itemimages/200/200/1/<?= $image; ?>" alt="<?= $title; ?>" title="<?= $title; ?>" />
		<?php } ?>
		<p class="title"><a href="<?= FRONTENDSITEURL.$link; ?>" target="_blank" title="<?= $title; ?>"><?= Text::trim($title, 20); ?></a></p>
		
		<?php } else { ?>
		
		<div class="search">
			<form action="<?= SITEURL; ?>/box/details/<?= $this->_boxId ?>" method="get"><div>
				<label for="search">
					<input type="text" name="search" value="<?= Template::get('search') ?>" />
					<input type="submit" value="Search" />
				</label>
			</div></form>
		</div>
		
		<?php } ?>

		<form action="<?= SITEURL; ?>/box/content/" method="post"><div>
		
			<? // Language code dropdown goes here ?>
			<input type="hidden" name="langCode" value="<?= $langCode; ?>" />
			
			<?php /* ?>
			<p>
				<label for="info[item]">
					Article/item
					<br />
					<select name="info[item]" class="flat">
						<?php foreach ($itemNames as $itemId => $name) { ?>
						<option value="<?= $itemId; ?>"<?= isset($info['item']) && $info['item'] == $itemId ? ' selected="selected"' : ''; ?>><?= $name; ?></option>
						<?php } ?>
					</select>
				</label>
			</p>
			<?php */ ?>
			
			<?php if ($contentId) { ?>
			
			<input type="hidden" name="info[item]" value="<?= $itemId; ?>" />
			
			<?php } else { ?>
			
			<div class="search">
				<?php $this->search(); ?>
			</div>
			
			<?php } ?>
			
			<?php if(!empty($contentId) || Template::get('search')) { ?>
			<p>
				<label for="order">
					Priority
					<br />
					<input type="text" name="order" value="<?= $sequence; ?>" class="flat" style="width: 80px; text-align: center;" />
				</label>
			</p>
			<?php } ?>

			<input type="hidden" name="canAdd" value="<?= (int) $canAdd; ?>" />
			<input type="hidden" name="canDelete" value="<?= (int) $canDelete; ?>" />
			<?php 
				// Edit existing
				if (!empty($contentId)) {
			?>
			<input type="hidden" name="contentId" value="<?= $contentId; ?>" />
			<input type="submit" name="save" value="Save" class="button" />
			<?php if ($canDelete) { ?>
			<input type="submit" name="delete" value="Delete" class="button" />
			<?php } ?>
			<?php 
				// Add new
				} elseif(Template::get('search')) {
				
			?>
			<input type="hidden" name="boxId" value="<?= $boxId; ?>" />
			<input type="submit" name="save" value="Add new" class="button" />
			<?php
				}
			?>
			
		</div></form>
		
	</div>