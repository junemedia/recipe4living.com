
	<div<?= $contentId ? ' title="'.$title.'"' : ''; ?>>
		
		<?php if ($contentId) { ?>
		<img class="image" src="<?= ASSETURL; ?>/itemimages/200/200/1/<?= $imageName; ?>" alt="<?= $title; ?>" title="<?= $title; ?>" />
		<p class="title"><a href="<?= FRONTENDSITEURL.$link; ?>" target="_blank" title="<?= $title; ?>"><?= Text::trim($title, 20); ?></a></p>
		
		<?php } else { ?>
		
		<div class="search">
			<form action="<?= SITEURL; ?>/articles/details/<?= $articleId; ?>" method="get"><div>
				<label for="search">
					<input type="text" name="search" value="<?= Template::get('search') ?>" />
					<input type="submit" value="Search" />
				</label>
			</div></form>
		</div>
		
		<?php } ?>

		<form action="<?= SITEURL; ?>/articles/content/<?= $articleId;?>" method="post"><div>
		
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
			
			<input type="hidden" name="articleId" value="<?= $articleId; ?>" />
			
			<?php } else { ?>
			
			<div class="search">
				<?php $this->search(); ?>
			</div>
			
			<?php } ?>
			
			<?php if(!empty($contentId) || Template::get('search')) {?>
			<p>
				<label for="order">
					Priority
					<br />
					<input type="text" name="order" value="<?= $sequence; ?>" class="flat" style="width: 80px; text-align: center;" />
				</label>
			</p>
			<?php }?>
			<?php if(!empty($contentId)){?>
			<p>
			<label for="order" style="width:100%">
					Description
					<br />
					<textarea name="desc" class="flat" rows="20" cols="10" id="slide_desc<?= $contentId;?>" style="width:460px;"><?= $desc;?></textarea>
				</label>
				
			</p>
			<?php } ?>

			<?php 
				// Edit existing
				if (!empty($contentId)) {
			?>
			<input type="hidden" name="contentId" value="<?= $contentId; ?>" />
			<input type="submit" name="save" value="Save" class="button" />
			<input type="submit" name="delete" value="Delete" class="button" />

			<?php 
				// Add new
				} elseif(Template::get('search')) {
				
			?>
			<input type="hidden" name="articleId" value="<?= $articleId; ?>" />
			<input type="submit" name="save" value="Add new" class="button" />
			<?php
				}
			?>
			
		</div></form>
		
	</div>