
		<form name="poll_<?= $pollId ?>" action="<?= SITEURL . '/index' ?>" method="post">
			<div class="statements">
				<?php foreach($poll['statements'] as $statementId=>$statement) { ?>
				<div class="statement" style="background-position: <?= 240 - round(isset($poll['results'][$statementId]) ? $poll['results'][$statementId]['percentage'] : 0) ?>px center;">
					<div class="statementholder">
					<div class="percentage"><?= round(isset($poll['results'][$statementId]) ? $poll['results'][$statementId]['percentage'] : 0).'%' ?></div>
					<label class="reloads" for="statement-<?= $statementId ?>" onclick="vote(this,<?= $statementId ?>)">
						<input id="statement-<?= $statementId ?>" class="fl" name="statement" type="radio" value="<?= $statementId ?>"<?= isset($selectedStatementId) && $selectedStatementId==$statementId ? ' checked="checked"' : '' ?> />
						<?= $statement['text'] ?>
					</label>
					</div>
				</div>
				<?php } ?>
				<noscript>
					<button class="button-md" type="submit" title="Vote"><span>Vote</span></button>
				</noscript>
			</div>
			<input name="pollId" type="hidden" value="<?= $pollId ?>" />
			<input name="task" type="hidden" value="vote" />
		</form>
