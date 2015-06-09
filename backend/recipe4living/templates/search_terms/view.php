
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
			<th class="textfield"><a href="?sort=<?= $sort == 'date_asc' ? 'date_desc' : 'date_asc'; ?>">Date </a></th>
			<th class="textfield"><a href="?sort=<?= $sort == 'term_asc' ? 'term_desc' : 'term_asc'; ?>">Search Term</a></th>
			<th class="textfield"><a href="?sort=<?= $sort == 'term_extra_asc' ? 'term_extra_desc' : 'term_extra_asc'; ?>">Refine Search Term</a></th>
			<th class="textfield"><a href="?sort=<?= $sort == 'results_asc' ? 'results_desc' : 'results_asc'; ?>">Results</a></th>
			<th class="textfield" style="padding: 5px;">Count</th>
		</tr>
		
		<?php 
			if (!empty($searchTerms)) {
				$alt = false; foreach ($searchTerms as $searchTerm) { $alt = !$alt;
		?>		
		<tr class="<?= $alt ? 'odd' : ''; ?>">
		
			<td><?= date('d/m/Y',$searchTerm['searched']) ?></td>
			<td class="textfield" style="padding: 5px;"><?= Text::trim($searchTerm['term'], 100); ?></td>
			<td class="textfield" style="padding: 5px;"><?= Text::trim($searchTerm['termExtra'], 100); ?></td>
			<td style="padding: 5px; text-align: center;"><?= $searchTerm['resultCount']; ?></td>
			<td style="padding: 5px; text-align: center;"><?= $searchTerm['count']; ?></td>
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

	<p>
		<a href="<?= SITEURL ?>/searchterms/download?sort=<?= $sort ?>">Download CSV File</a> (Please be patient when downloading this file)
	</p>
