
	<?php Template::startScript(); ?>
	
		toggle_div = function(a,diff_id) {
			var diff = document.getElementById(diff_id);
			if(diff.style.display=='none') {
				diff.style.display = 'block';
				a.innerHTML = a.innerHTML.replace(/^Show/,'Hide');
			}
			else {
				diff.style.display = 'none';
				a.innerHTML = a.innerHTML.replace(/^Hide/,'Show');
			}
			a.blur();
		}
		
	<?php Template::endScript(); ?>
	<?php 
		function slidetoarticle($link)
		{
			$result = str_replace("/edit/1", ".htm", $link);
			$result = str_replace("slidearticles/details","articles/edit",$result);
			return $result;
		}
	?>
	<?php if($viewType == 'deleted') { ?><h2>Deleted <?= ucfirst($this->_itemType) ?>s</h2><?php } ?>
	
	<table id="items_data" class="centered horizontal">
		
		<tr class="metadata">
			<td colspan="7" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
				<div style="height: 10px; margin: 14px 0px;">
					Listing <?= $pagination->get('start'); ?> - <?= $pagination->get('end'); ?> of <?= $pagination->get('total'); ?>
				</div>
			</td>
		</tr>
		
		<tr class="metadata">
			
			<?php /*
			<th><input class="checkall" type="checkbox" /></th>
			*/ ?>
			
			<th>Article</th>
			<th class="textfield"><a href="?<?= $viewType == 'deleted' ? 'view=deleted&amp;' : '' ?>sort=<?= $sort == 'date_asc' ? 'date_desc' : 'date_asc'; ?>">Date </a></th>
			<th class="textfield"><a href="?<?= $viewType == 'deleted' ? 'view=deleted&amp;' : '' ?>sort=<?= $sort == 'name_asc' ? 'name_desc' : 'name_asc'; ?>">Title</a></th>
			<th>&nbsp;</th>
			<?php if($this->_view=='article_listing' || $this->_view=='recipe_listing') { ?>
			<th>&nbsp;</th>
			<?php } ?>
			<?php if($this->_view=='article_listing'){?>
			<th>&nbsp;</th>
			<?php }?>
			<th>&nbsp;</th>
			<?php if ($this->_view != 'quicktip_listing') { ?>
			<th class="textfield"><a href="?<?= $viewType == 'deleted' ? 'view=deleted&amp;' : '' ?>sort=<?= $sort == 'views_asc' ? 'views_desc' : 'views_asc'; ?>">Views</a></th>
			<?php } ?>
			<?php if ($this->_view == 'recipe_listing') { ?>
			<th>Ingredients</th>
			<?php } ?>
			<th><?= $viewType=='deleted' ? 'Undelete' : 'Live' ?></th>
		
			<?php /*if ($this->_view != 'quicktip_listing' && $viewType != 'deleted') { ?>
			<th><a href="?<?= $viewType == 'deleted' ? 'view=deleted&amp;' : '' ?>sort=featured">Top Recipe</a></th>
			<?php }*/ ?>
			<th>Username</th>
			
		</tr>
		
		<?php
			if (!empty($items)) {
				$alt = false; foreach ($items as $item) { $alt = !$alt;
		?>		

		<tr class="<?= $alt ? 'odd' : ''; ?>">
		
			<?php /*
			<td><input type="checkbox" name="itemId" value="<?= $item['id']; ?>" /></td>
			*/ ?>
			
			<td><?= $item['id']; ?></td>
			<td><?= $item['date']; ?></td>
			<td class="textfield">
				<div style="width: 625px;">
				<?php $itemlink = "";
					if(isset($item['isslide']) && $item['isslide']==1){ 
						$itemlink = slidetoarticle($item['link']);
					}else {
						$itemlink =$item['link']; 
					} 				
				?>
					<a href="<?= ($this->_view=='quicktip_listing' ? SITEURL : FRONTENDSITEURL).$itemlink; ?>"><?= Text::trim($item['title'], 100).(isset($item['section']) ? ' [ '.$item['section'].' ]' : ''); ?></a>
				</div>
				<div id="body-<?= $item['id'] ?>" style="padding: 0px 10px; display: none;">
					<hr />
					<?= $item['body'] ?>
				</div>
			</td>
			<?php if($this->_view=='article_listing' || $this->_view=='recipe_listing') { ?>
			<td><a href="<?= SITEURL.'/'.$this->_itemType.'s/links/'.$item['id']; ?>">Related<br />Links</a></td>
			<?php } ?>
			<?php if($this->_view=='article_listing'){?>
			<td><a href="<?= SITEURL.'/'.$this->_itemType.'s/details/'.$item['id']; ?>">Article<br />Slideshows</a></td>
			<?php }?>
			<td style="vertical-align: top; text-align: center;"><a href="#" onclick="toggle_div(this,'body-<?= $item['id'] ?>'); return false;">Show<br />Content</a></td>
			<td style="vertical-align: top; text-align: center;"><a href="<?=str_replace("/edit", "", $item['link']);?>" target="_blank">View on frontend</a></td>
			<?php if ($this->_view != 'quicktip_listing') { ?>
			<td><?= $item['views']; ?></td>
			<?php } ?>
			<?php if ($this->_view == 'recipe_listing') { ?>
			<td><a href="<?= SITEURL.$item['ingredientsLink']; ?>">Edit</a></td>
			<?php } ?>
			<td>
				<?php if($viewType=='deleted') { ?>
					<a href="<?= SITEURL.$item['deleteToggler']  ?>" title="Click to undelete" onclick="if(!confirm('Are you sure you want to undelete this <?= $this->_itemType ?>?')) return false"><img src="<?= COREASSETURL; ?>/images/famfamfam/page_white_add.png" /></a>
				<?php } else { ?>
					<a href="<?= SITEURL.$item['liveToggler']; ?>"><img src="<?= COREASSETURL; ?>/images/famfamfam/<?= $item['live'] ? 'tick.png' : 'cross.png'; ?>" title="Click to <?= $item['live'] ? 'take offline' : 'set live'; ?>." /></a>
					<?php if(!$item['live'] && $this->_view != 'quicktip_listing') { ?><a href="<?= SITEURL.$item['deleteToggler']  ?>" title="Click to delete" onclick="if(!confirm('Are you sure you want to delete this <?= $this->_itemType ?>?')) return false"><img src="<?= COREASSETURL; ?>/images/famfamfam/page_white_delete.png" /></a><?php } ?>
				<?php } ?>
			</td>
			<td><?= $item['author']['username']; ?></td>
			<?php /*if ($this->_view != 'quicktip_listing') { ?>
			<td><a href="<?= SITEURL.$item['featuredToggler']; ?>"><img src="<?= COREASSETURL; ?>/images/famfamfam/<?= $item['featured'] ? 'tick.png' : 'cross.png'; ?>" title="Click to <?= $item['live'] ? 'unset from being featured' : 'set as featured'; ?>." /></a></td>
			<?php }*/ ?>
		</tr>
		<?php
				}
			}
		?>
		
		<tr class="metadata">
			<td colspan="7" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
			</td>
		</tr>
		
		<?php Template::startScript(); ?>
		//new Table('items_data');
		<?php Template::endScript(); ?>
		
	</table>

	<?php if($viewType != 'deleted') { ?>
	<form name="search_form" method="get" action="">
	<table class="centered horizontal" style="width: 1115px; margin-top: 20px;">
		<tr>
			<td colspan="2">Search</td>
		</tr>
		<tr>
			<td class="textfield" style="width: 50%; padding: 5px; text-align: right;">Title:</td>
			<td style="width: 50%;"><input type="text" name="searchterm" value="<?= $searchTerm ?>" style="width: 300px;" /></td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; text-align: right;">&nbsp;</td>
			<td>
				<input type="submit" value="Search" />
				<input type="submit" name="clear" value="Clear Search Criteria" />
			</td>
		</tr>
	</table>
	</form>
	<?php } ?>
	
