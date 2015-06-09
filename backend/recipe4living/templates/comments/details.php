
	<div class="centered horizontal">&nbsp;</div>
	
	<table id="items_data" class="centered horizontal" style="width: 1200px;">
		
		<?php if(isset($comment['article'])) { ?>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; width: 30%; text-align: right; font-weight: bold;">Article:</td>
			<td class="textfield" style="padding: 5px; width: 70%;"><a href="<?= $comment['article']['link'] ?>" target="_blank"><?= $comment['article']['title'] ?></a></td>
		</tr>
		<?php } ?>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Comment:</td>
			<td class="textfield" style="padding: 5px;"><?= $comment['body'] ?></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Submitted By:</td>
			<td class="textfield" style="padding: 5px;"><?= $comment['user']['username'] ?></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Date:</td>
			<td class="textfield" style="padding: 5px;"><?= $comment['date'] ?></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Live:</td>
			<td class="textfield" style="padding: 5px;"><?= $comment['live'] == 1 ? 'yes' : 'no' ?></td>
		</tr>
		
	</table>

	<div class="centered horizontal">&nbsp;</div>
	
	<?php /*if($comment['ratings']['count'] > 0) { ?>
	<table class="centered" style="width: 1200px;">
		
		<tr class="metadata">
			<th class="textfield" style="padding: 5px; text-align: right; font-weight: bold; width: 50%;">User</td>
			<th class="textfield" style="padding: 5px; width: 50%;">Rating</td>
		</tr>
		<?php $totalRating = 0; foreach($comment['ratings']['raw'] as $rating) { $totalRating += $rating['rating']; ?>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;"><?php echo $rating['username'] ?></td>
			<td class="textfield" style="padding: 5px;"><?php echo $rating['rating'] ?></td>
		</tr>
		<?php } ?>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; width: 50%; font-style: italic;">Total:</td>
			<td class="textfield" style="padding: 5px; width: 50%;"><?php echo $totalRating ?></td>
		</tr>
		
	</table>
	<?php }*/ ?>

	<div class="centered horizontal">&nbsp;</div>
	
	<?php if(!empty($comment['reports']['raw'])) { ?>
	
	<form name="updateStatusForm" method="post">
	
	<table class="centered" style="width: 1200px;">
		
		<tr class="metadata">
			<th class="textfield" style="padding: 5px; font-weight: bold; width: 20%;">User</td>
			<th class="textfield" style="padding: 5px; width: 20%;">Date</td>
			<!--
			<th class="textfield" style="padding: 5px; width: 40%;">Reason</td>
			-->
			<th class="textfield" style="padding: 5px; width: 20%;">Status</td>
		</tr>
		
		<?php foreach($comment['reports']['raw'] as $status => $reports) { ?>
			<?php foreach($reports as $report) { ?>
		
		<tr class="metadata">
			<td class="textfield" style="padding: 5px;"><?= $report['username'] ?></td>
			<td class="textfield" style="padding: 5px;"><?= $report['time'] ?></td>
			<!--
			<td class="textfield" style="padding: 5px;"><?= $report['reason'] ?></td>
			-->
			<td class="textfield" style="padding: 5px;">
					<select name="reportStatus[<?= $report['id'] ?>]">
					<?php
						foreach($reportStatuses as $reportStatusId=>$reportStatus) {
							if($status == 'viewed' || ($status == 'resolved' && $reportStatus != 'viewed')) {
								echo '<option value="' . $reportStatus . '"' . ( $reportStatus == $report['status'] ? ' selected="selected"' : '' ) . '>' . $reportStatus . '</option>';
							}
						}
					?>
					</select>
			</td>
		</tr>
		
			<?php } ?>
		<?php } ?>
		
		<tr class="metadata">
			<td class="textfield" style="padding: 5px;">&nbsp;</td>
			<td class="textfield" style="padding: 5px;">&nbsp;</td>
			<!--
			<td class="textfield" style="padding: 5px;">&nbsp;</td>
			-->
			<td class="textfield" style="padding: 5px;">
				<input type="submit" name="updateStatuses" value="Update Status" />
			</td>
		</tr>
	</table>
	
	<input type="hidden" name="task" value="updateReportStatuses" />
		
	</form>
	
	<?php } ?>

	<div class="centered horizontal" style="text-align: center;">
		<a href="<?= $backButtonUrl ?>">Go Back</a>
	</div>
	