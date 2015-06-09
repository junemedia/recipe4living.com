
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
			
			<th>Giveaway</th>
			<th class="textfield">Date</th>
			<th class="textfield">Title</th>
			<th>&nbsp;</th>
			<th>Live</th>
			<th>Featured</th>	
			<th>Deleted</th>					
		</tr>
		
		<?php
			if (!empty($items)) {
				$alt = false; foreach ($items as $item) { $alt = !$alt;
		?>		

		<tr class="<?= $alt ? 'odd' : ''; ?>">
			<td><?= $item['id']; ?></td>
			<td><?= $item['createDate']; ?></td>
			<td class="textfield">
				<div style="width: 625px;">
				<?php
					$itemlink =$item['editLink']; 			
				?>
					<a href="<?php echo SITEURL.$itemlink; ?>"><?= Text::trim($item['title'], 100); ?></a>
				</div>
			</td>
			<td style="vertical-align: top; text-align: center;"><a href="<?= $item['link'];?>" target="_blank">View on frontend</a></td>
			<td><a href="<?= SITEURL.$item['liveToggler']; ?>"><img src="<?= COREASSETURL; ?>/images/famfamfam/<?= $item['status']==1 ? 'tick.png' : 'cross.png'; ?>" title="Click to <?= $item['status']==1 ? 'take offline' : 'set live'; ?>." /></a></td>
			<td><a href="<?= SITEURL.$item['featuredToggler']; ?>"><img src="<?= COREASSETURL; ?>/images/famfamfam/<?= $item['featured']==1 ? 'tick.png' : 'cross.png'; ?>" title="Click to <?= $item['featured']==1 ? 'set featured' : 'unset featured'; ?>." /></a></td>
			<td><a href="<?= SITEURL.$item['deleteToggler']; ?>" title="Click to delete" onclick="if(!confirm('Are you sure you want to delete this giveaway?')) return false">Delete</a></td>

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
	
