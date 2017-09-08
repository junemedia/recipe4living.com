<?php 
$show = 'all';
if ($this->_doc->getFormat() == 'simple') {
	$show = 'simple';
}
?>
	<table id="items_data" class="centered horizontal" <?=$show=='all'?'style="width: 1200px"':''?>>
		
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
					if(isset($filterArray['filename'])) {
						echo ' <strong style="color: blue;">Filename:</strong> '.$filterArray['filename'];
					}
					if(isset($filterArray['title'])) {
						echo ' <strong style="color: blue;">Title:</strong> '.$filterArray['title'];
					}
					if(isset($filterArray['article_title'])) {
						echo ' <strong style="color: blue;">Article Title:</strong> '.$filterArray['article_title'];
					}
				?>
			</td>
		</tr>
		<?php } ?>
		
		<tr class="metadata">
			<th class="textfield" style="width: 10%; padding: 5px 10px;">&nbsp;</th>
		<?php if ($show == 'all') { ?>
			<th class="textfield" style="width: 20%; padding: 5px 10px;">Filename</th>
			<th class="textfield" style="width: 30%;"><a href="<?= $sortPageUrl ?>&amp;sort=<?= $sort == 'title_asc' ? 'title_desc' : 'title_asc'; ?>">Title</a></th>
			<th class="textfield" style="width: 10%;"><a href="<?= $sortPageUrl ?>&amp;sort=<?= $sort == 'type_asc' ? 'type_desc' : 'type_asc'; ?>">Type</a></th>
		<?php } ?>
			<th class="textfield" style="width: 30%;"><a href="<?= $sortPageUrl ?>&amp;sort=<?= $sort == 'article_asc' ? 'article_desc' : 'article_asc'; ?>">Article </a></th>
		</tr>
		
		<?php 
			if (!empty($articleImages)) {
				$alt = false; foreach ($articleImages as $articleImage) { $alt = !$alt;
		?>		
		<tr class="<?= $alt ? 'odd' : ''; ?>">
			<td class="textfield" style="padding: 5px; text-align: center;">
				<?php if ($show == 'all') { ?>
					<a href="<?= SITEURL.$this->_baseUrl.'/imagedetails?filename='.urlencode($articleImage['filename']).'&amp;articleId='.$articleImage['articleId'] ?>" style="width: 100px;">
				<?php } ?><img src="<?= ASSETURL . '/itemimages/100/100/3/'.$articleImage['filename'] ?>" />
				<?php if ($show == 'all') { ?></a><?php } ?></td>
		<?php if ($show == 'all') { ?>
			<td class="textfield" style="padding: 5px;"><?= $articleImage['filename'] ?></td>
			<td class="textfield" style="padding: 5px;"><?= Text::trim($articleImage['title'], 100); ?></td>
			<td class="textfield" style="padding: 5px;"><?= $articleImage['type']; ?></td>
		<?php } ?>
			<td class="textfield" style="padding: 5px;"><?= Text::trim($articleImage['articleTitle'], 100); ?></td>
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

	<form name="search_form" method="get" action="<?=SITEURL.$baseUrl?>">
	<?php if ($this->_doc->getFormat() != 'site') { ?>
		<input type="hidden" name="format" value="<?=$this->_doc->getFormat();?>" />
	<?php } ?>
	<table class="centered horizontal" <?=$show=='all'?'style="width: 1200px"':''?>>
		<tr>
			<td colspan="2">Search</td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; text-align: right;">Filename:</td>
			<td><input type="text" name="filename" value="<?= $filename ?>" style="width: 300px;" /></td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; text-align: right;">Tile:</td>
			<td><input type="text" name="title" value="<?= $title ?>" style="width: 300px;" /></td>
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
