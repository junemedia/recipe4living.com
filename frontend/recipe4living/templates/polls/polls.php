
	<?php if(!empty($polls)) { ?>
		
	<?php Template::startScript(); ?>
		vote = function(label,statementId) {
			$('statement-'+statementId).checked = true;
		}
	<?php Template::endScript(); ?>
		
	<div id="polls" class="rounded">
		<div class="content">
		<?php $i = 0; foreach($polls as $pollId=>$poll) { ?>
			<h2><?= $poll['name'] ?></h2>
			<div class="standardform screenonly"><div class="formholder">
				<?php include(BLUPATH_TEMPLATES.'/polls/items/poll.php') ?>
			</div></div>
			<div class="clear<?= ++$i<count($polls) ? ' item_separator' : '' ?>"></div>
		<?php } ?>
		</div>
	</div>

	<?php } ?>
