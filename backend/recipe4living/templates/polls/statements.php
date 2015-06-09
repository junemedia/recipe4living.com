
	<h1><?= $poll['name'] ?></h1>

	<?php if(!empty($pollStatements)) { ?>
	
	<table id="items_data" class="centered horizontal" style="width: 1200px">
		
		<tr class="metadata">
			<th class="textfield" style="padding: 5px;">Statement</th>
			<th class="textfield" style="padding: 5px; text-align: center;">Votes</th>
			<th class="textfield" style="padding: 5px; text-align: center;">Percentage</th>
			<th class="textfield" style="padding: 5px; text-align: center;">Sequence</th>
			<th class="textfield" style="padding: 5px;">&nbsp;</th>
			<th class="textfield" style="padding: 5px;">&nbsp;</th>
		</tr>
		
		<?php 
			$alt = false; 
			$pollStatementCount = count($pollStatements);
			foreach ($pollStatements as $statement) { $alt = !$alt;
		?>		
		<tr class="<?= $alt ? 'odd' : ''; ?>">
		
			<td class="textfield" style="padding: 5px;"><?= Text::trim($statement['text'], 100); ?></td>
			<td style="padding: 5px; text-align: center; text-align: center;"><?= (isset($poll['results'][$statement['id']]) ? $poll['results'][$statement['id']]['votes'] : 0) ?></td>
			<td style="padding: 5px; text-align: center; text-align: center;"><?= (isset($poll['results'][$statement['id']]) ? $poll['results'][$statement['id']]['percentage'] : 0).'%' ?></td>
			<td style="padding: 5px; text-align: center;">
			<?php if($statement['sequence']<$pollStatementCount) { ?>
				<a href="<?= SITEURL . '/polls/move_statement?statementId='.$statement['id'].'&amp;move=down' ?>">
					<img src="<?= COREASSETURL; ?>/images/famfamfam/arrow_down.png" title="Move down" />
				</a>
			<?php } ?>
			<?php if($statement['sequence']>1) { ?>
				<a href="<?= SITEURL . '/polls/move_statement?statementId='.$statement['id'].'&amp;move=up' ?>">
				<img src="<?= COREASSETURL; ?>/images/famfamfam/arrow_up.png" title="Move up" />
				</a>
			<?php } ?>
			</td>
			<td style="padding: 5px; text-align: center;"><a href="<?= SITEURL . '/polls/statement?statementId='.$statement['id'] ?>">Edit</a></td>
			<td style="padding: 5px; text-align: center;"><a href="<?= SITEURL . '/polls/delete_statement?statementId='.$statement['id'] ?>" onclick="if(!confirm('Are you sure you want to delete this poll statement?')) return false">Delete Statement</a></td>
		</tr>
		<?php
			}
		?>
		
	</table>
	
	<?php } ?>
	
	
	<p style="margin-top: 20px;">
		<a href="<?= SITEURL . '/polls/statement?pollId='.$pollId ?>">Add poll statement</a>
	</p>
