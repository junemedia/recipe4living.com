
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

	<h1><?= $article['title'] ?></h1>
	
	<div>
		<a href="javascript: void(0)" onclick="toggle_div(this,'article_details'); return false;">Show Current <?php if($article['type']=='recipe') echo 'Recipe'; elseif($article['type']=='article') echo 'Article'; ?> Details</a>
	</div>
	
	<div id="article_details" style="display: none; margin-top: 10px;">
		<table class="centered horizontal" style="width: 1200px">
			<tr class="metadata">
				<td style="vertical-align: top; width: 10%; font-weight: bold; background-color: #DDDDDD;"><?php if($article['type']=='recipe') echo 'Summary'; elseif($article['type']=='article') echo 'Blurb'; ?></td>
				<td style="padding: 5px; width: 90%;"><?= $article['teaser'] ?></td>
			</tr>
			<tr class="metadata">
				<td style="vertical-align: top; font-weight: bold; background-color: #DDDDDD;"><?php if($article['type']=='recipe') echo 'Directions'; elseif($article['type']=='article') echo 'Body'; ?></td>
				<td style="padding: 5px"><?= $article['body'] ?></td>
			</tr>
			<?php if(isset($article['ingredients'])) { ?>
			<tr class="metadata">
				<td style="vertical-align: top; font-weight: bold; background-color: #DDDDDD;">Ingredients</td>
				<td style="padding: 5px"><?= implode('<br />',$article['ingredients']) ?></td>
			</tr>
			<?php } ?>
		</table>
	</div>

	<div>&nbsp;</div>

	<div>
		<a href="<?= SITEURL . '/articlehistory/reverttooriginal?articleId='.$article['id'] ?>" onclick="if(!confirm('Are you sure you want to revert to the first original version?')) return false">Revert to the first original version</a>
	</div>

	<noscript><span style="color: #FF0000">Note: JavaScript is required for this page</span></noscript>
	
	<table id="items_data" class="centered horizontal" style="width: 1200px">
		
		<tr class="metadata">
			<td colspan="6" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
				<div style="height: 10px; margin: 14px 0px;">
					Listing <?= $pagination->get('start'); ?> - <?= $pagination->get('end'); ?> of <?= $pagination->get('total'); ?>
				</div>
			</td>
		</tr>
		
		<tr class="metadata">
			<th class="textfield" style="width: 10%;"><a href="<?= $sortPageUrl ?>&amp;sort=<?= $sort == 'date_asc' ? 'date_desc' : 'date_asc'; ?>">Date</a></th>
			<th class="textfield" style="width: 10%; padding: 5px;">User</th>
			<th class="textfield" style="width: 10%; padding: 5px;">Field</th>
			<th class="textfield" style="width: 65%; padding: 5px;">Difference</th>
			<th class="textfield" style="width: 5%; padding: 5px;">&nbsp;</th>
		</tr>
		
		<?php 
			if (!empty($articleHistoryList)) {
				$alt = false; foreach ($articleHistoryList as $revision) { $alt = !$alt;
				switch($revision['type']) {
					case 'title': $fieldType = 'Title';	break;
					case 'body': 
						if($article['type']=='recipe') {
							$fieldType = 'Directions';
						}
						elseif($article['type']=='article' || $article['type']=='quicktip' || $article['type']=='blog') {
							$fieldType = 'Body';
						}
						break;
					case 'teaser': 
						if($article['type']=='recipe') {
							$fieldType = 'Summary';
						}
						elseif($article['type']=='article') {
							$fieldType = 'Blurb';
						}
						break;
					case 'ingredients': $fieldType = 'Ingredients'; break;
				}
		?>		
		<tr class="<?= $alt ? 'odd' : ''; ?>">
			<td class="textfield" style="padding: 20px 5px 5px 5px; vertical-align: top;"><?= $revision['date']; ?></td>
			<td class="textfield" style="padding: 20px 5px 5px 5px; vertical-align: top;"><?= $revision['username']; ?></td>
			<td class="textfield" style="padding: 20px 5px 5px 5px; vertical-align: top;"><?= $fieldType; ?></td>
			<td class="textfield" style="padding: 5px; vertical-align: top;">
				<?php if(isset($revision['noHtmlDiff'])) { ?>
					<pre class="diff" wrap="normal"><?= str_replace("\n","\n\n",$revision['noHtmlDiff']) ?></pre>
					<hr />
					<div>
						<a href="javascript: void(0)" onclick="toggle_div(this,'<?= 'diff-'.$revision['id'] ?>'); return false;">Show Full Difference</a>
					</div>
					<pre id="<?= 'diff-'.$revision['id'] ?>" class="diff" wrap="normal" style="display: none"><?= str_replace("\n","\n\n",$revision['diff']); ?></pre>
				<?php } else { ?>
					<pre class="diff" wrap="normal"><?= $revision['diff'] ?></pre>
				<?php } ?>
			</td>
			<td class="textfield" style="padding: 5px; vertical-align: top;"><a href="<?= SITEURL . '/articlehistory/revert?revisionId='.$revision['id'] ?>" onclick="if(!confirm('Are you sure you want to revert to this version?')) return false" style="display:inline">Revert</a></td>
		</tr>
		<?php
				}
			}
		?>
		
		<tr class="metadata">
			<td colspan="6" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
			</td>
		</tr>
		
	</table>
	