
	<table id="items_data" class="centered horizontal" style="width: 1200px">
		
		<?php if($pagination->get('total') > 0) { ?>
		<tr class="metadata">
			<td colspan="8" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
				<div style="height: 10px; margin: 14px 0px;">
					Listing <?= $pagination->get('start'); ?> - <?= $pagination->get('end'); ?> of <?= $pagination->get('total'); ?>
				</div>
			</td>
		</tr>
		<?php } ?>
		
		<?php if(!empty($filterArray)) { ?>
		<tr class="metadata">
			<td colspan="8" style="border: 0px;">
				<strong>Search results for:</strong>
				<?php
					if(isset($filterArray['date_from'])) {
						echo '<strong style="color: blue;">Date From:</strong> '.$filterArray['date_from'];
					}
					if(isset($filterArray['date_to'])) {
						echo ' <strong style="color: blue;">Date To:</strong> '.$filterArray['date_to'];
					}
					if(isset($filterArray['content'])) {
						echo ' <strong style="color: blue;">Comment:</strong> '.$filterArray['content'];
					}
					if(isset($filterArray['recipe'])) {
						echo ' <strong style="color: blue;">Recipe:</strong> '.$filterArray['recipe'];
					}
					if(isset($filterArray['user'])) {
						echo ' <strong style="color: blue;">Submitted By:</strong> '.$filterArray['user'];
					}
					if(isset($filterArray['flagged'])) {
						echo ' <strong style="color: blue;">Flagged:</strong> '.($filterArray['flagged'] == 1 ? 'No' : 'Yes' );
					}
					if(isset($filterArray['live'])) {
						echo ' <strong style="color: blue;">Live:</strong> '.($filterArray['live'] == 1 ? 'No' : 'Yes' );
					}
				?>
			</td>
		</tr>
		<?php } ?>

		<tr class="metadata">
			<th class="textfield" style="padding: 5px;">Date</th>
			<th class="textfield" style="padding: 5px;">Comment</th>
			<!--
			<th class="textfield" style="text-align: center; padding: 5px;">Comment Rating</th>
			-->
			<th class="textfield" style="padding: 5px;">Recipe</th>
			<th class="textfield" style="text-align: center; padding: 5px;">Submitted By</th>
			<th class="textfield" style="text-align: center; padding: 5px;">IP</th>
			<th class="textfield" style="text-align: center; padding: 5px;">Flagged</th>
			<th class="textfield" style="text-align: center; padding: 5px;">Live</th>
			<th class="textfield" style="text-align: center; padding: 5px;">&nbsp;</th>
		</tr>
		
		<?php 
			if (!empty($comments)) {
				$alt = false; foreach ($comments as $comment) { $alt = !$alt;
		?>		
		<tr class="<?= $alt ? 'odd' : ''; ?>">
		
			<td><?= $comment['date'] ?></td>
			<td class="textfield" style="padding: 5px;"><?= (strip_tags($comment['body'])); ?></td>
			<!--
			<td style="text-align: center;"><?= $comment['rating'] ?></td>
			-->
			<td class="textfield" style="padding: 5px;"><?php if(isset($comment['article'])) echo '<a href="'. $comment['article']['link'] .'" target="_blank" style="display: inline;">'.Text::trim($comment['article']['title'], 100) .'</a>'; ?></td>
			<td style="text-align: center;"><?= $comment['user']['username']; ?></td>
			<?php 
			$spamIP=stream_get_contents(fopen('http://www.stopforumspam.com/api?ip='.$comment['ipaddr'],'rb'));
			if (strstr($spamIP,"<appears>no</appears>")) { ?>
			<td style="text-align: center;"><?= $comment['ipaddr']; ?></td>
			<?php } else { ?>
			<td style="text-align: center; background-color: red;"><?= $comment['ipaddr']; ?></td>
			
			<?php } ?>
			<td style="text-align: center;"><?= $comment['reportCount'] > 0 ? 'yes' : 'no' ?></td>
			<td style="text-align: center;"><a href="<?= SITEURL.$baseUrl.'/setstatus?commentId='.$comment['id'].'&amp;action='.($comment['live'] == 1 ? 'disable' : 'enable') ?>" title="Click to <?= $comment['live'] == 1 ? 'disable' : 'enable'; ?> this review"><img src="<?= COREASSETURL; ?>/images/famfamfam/<?= $comment['live'] == 1 ? 'tick.png' : 'cross.png'; ?>" /></a></td>
			<td style="text-align: center;"><a href="<?= SITEURL.$detailsPageBaseUrl.'/'.$comment['id'] ?>">View</a></td>
		</tr>
		<?php
			}
		?>
		
		<tr class="metadata">
			<td colspan="8" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
			</td>
		</tr>
		
		<?php
			}
			else {
		?>
		
		<tr class="metadata">
			<td colspan="8" style="border: 0px;">
				No matches found.
			</td>
		</tr>
		
		<?php
			}
		?>
		
	</table>

	<form name="search_form" method="get">
	<table id="items_data" class="centered horizontal" style="width: 1200px">
		<tr>
			<td colspan="2">Search</td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; width: 30%; text-align: right;">Date From:</td>
			<td style="width: 70%;"><input type="text" name="date_from" value="<?= $date_from ?>" style="width: 100px;" /> [mm/dd/yyyy]</td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; text-align: right;">Date To:</td>
			<td><input type="text" name="date_to" value="<?= $date_to ?>" style="width: 100px;" /> [mm/dd/yyyy]</td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; text-align: right;">Comment (part):</td>
			<td><input type="text" name="content" value="<?= $content ?>" style="width: 500px;" /></td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; text-align: right;">Recipe:</td>
			<td><input type="text" name="recipe" value="<?= $recipe ?>" style="width: 500px;" /></td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; text-align: right;">Submitted By:</td>
			<td><input type="text" name="user" value="<?= $user ?>" style="width: 200px;" /></td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; text-align: right;">Flagged:</td>
			<td>
				<label><input type="radio" name="flagged" value="1"<?= $flagged == 1 ? 'checked="checked"' : '' ?> /> No</label>
				<label><input type="radio" name="flagged" value="2"<?= $flagged == 2 ? 'checked="checked"' : '' ?> /> Yes</label>
				<label><input type="radio" name="flagged" value="3"<?= ($flagged == 3 || empty($flagged)) ? 'checked="checked"' : '' ?> /> No Preference</label>
			</td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; text-align: right;">Live:</td>
			<td>
				<label><input type="radio" name="live" value="1"<?= $live == 1 ? 'checked="checked"' : '' ?> /> No</label>
				<label><input type="radio" name="live" value="2"<?= $live == 2 ? 'checked="checked"' : '' ?> /> Yes</label>
				<label><input type="radio" name="live" value="3"<?= ($live == 3|| empty($live)) ? 'checked="checked"' : '' ?> /> No Preference</label>
			</td>
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
	
	
	
