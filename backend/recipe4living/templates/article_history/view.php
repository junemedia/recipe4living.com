
	<table id="items_data" class="centered horizontal" style="width: 1200px">
		
		<tr class="metadata">
			<td colspan="6" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
				<div style="height: 10px; margin: 14px 0px;">
					Listing <?= $pagination->get('start'); ?> - <?= $pagination->get('end'); ?> of <?= $pagination->get('total'); ?>
				</div>
			</td>
		</tr>
		
		<?php if(!empty($filterArray)) { ?>
		<tr class="metadata">
			<td colspan="8" style="border: 0px;">
				<strong>Search results for:</strong>
				<?php
					if(isset($filterArray['article_title'])) {
						echo ' <strong style="color: blue;">Article Title:</strong> '.$filterArray['article_title'];
					}
				?>
			</td>
		</tr>
		<?php } ?>
		
		<tr class="metadata">
			<th class="textfield" style="width: 20%;"><a href="<?= $sortPageUrl ?>&amp;sort=<?= $sort == 'date_asc' ? 'date_desc' : 'date_asc'; ?>">Date</a></th>
			<th class="textfield" style="width: 10%; padding: 5px;">Field</th>
			<th class="textfield" style="width: 70%;"><a href="<?= $sortPageUrl ?>&amp;sort=<?= $sort == 'article_title_asc' ? 'article_title_desc' : 'article_title_asc'; ?>">Article</a></th>
		</tr>
		
		<?php 
			if (!empty($articleHistoryList)) {
				$alt = false; foreach ($articleHistoryList as $listItem) { $alt = !$alt;
				switch($listItem['type']) {
					case 'title': $fieldType = 'Title';	break;
					case 'body': 
						if($listItem['articleType']=='recipe') {
							$fieldType = 'Directions';
						}
						elseif($listItem['articleType']) {
							$fieldType = 'Body';
						}
						break;
					case 'teaser': 
						if($listItem['articleType']=='recipe') {
							$fieldType = 'Summary';
						}
						elseif($listItem['articleType']=='article') {
							$fieldType = 'Blurb';
						}
						break;
					case 'ingredients': $fieldType = 'Ingredients'; break;
				}
		?>		
		<tr class="<?= $alt ? 'odd' : ''; ?>">
			<td class="textfield" style="padding: 5px;"><?= $listItem['date']; ?></td>
			<td class="textfield" style="padding: 5px;"><?= $fieldType; ?></td>
			<td class="textfield" style="padding: 5px;"><a href="<?= SITEURL . '/articlehistory/history?articleId='.$listItem['articleId'] ?>"><?= Text::trim($listItem['articleTitle'], 100); ?></a></td>
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

	<form name="search_form" method="get">
	<table class="centered horizontal" style="width: 1200px">
		<tr>
			<td colspan="2">Search</td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; text-align: right; width: 30%;">Article:</td>
			<td style="width: 70%;"><input type="text" name="article_title" value="<?= $articleTitle ?>" style="width: 300px;" /></td>
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
