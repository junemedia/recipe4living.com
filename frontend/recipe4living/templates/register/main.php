	<div id="main-content" class="recipes">

		<div id="column-container">

			<div id="panel-center" class="column">

				<div id="register-progress" class="register-step<?= $currentStageNum; ?>"></div>

				<div id="register-stages" class="wizard">
				<?php
					$stageNum = 0;
					foreach ($registerStages as $registerStage) {
					// Need to increment stage number regardless of if we complete the loop else it shows nothing
					$i = ++$stageNum;

						if ($stageNum < $currentStageNum) {
							$status = 'complete';
							continue;	// Just don't show it.
						} elseif ($stageNum == $currentStageNum) {
							$status = 'current';
						} else {
							$status = 'incomplete';
							continue;	// Just don't show it.
						}
				?>
					<div class="stage <?= $status ?>" id="<?= $registerStage['id'] ?>">
						<div class="title"><h2><?= $registerStage['title'] ?></h2></div>
						<div class="stagecontent standardform">
						<div class="formholder">
							<?php
								if ($stageNum == $currentStageNum) {
									echo Messages::getMessages();
									$this->view_stage();
								}
							?>
						</div>
						</div>
						<div class="clear"></div>
					</div>
				<?php

					}
				?>
				</div>

				<div class="clear"></div>
			</div>

			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>

			<div id="panel-right" class="column">
				<?php include(BLUPATH_TEMPLATES.'/site/newsletter.php') ?>

				<?php //if (STAGING) { ?>
				<div class="ad">
					<!-- REMOVE AD - /frontend/recipe4living/templates/register/main.php -->
					<!-- FM Test STAMP 300x250 expands to 300x600 Zone -->
					<!--<script type='text/javascript' src='http://static.fmpub.net/zone/2461'></script>-->
					<!-- FM Test STAMP 300x250 expands to 300x600 Zone -->
				</div>
				<?php //} ?>

				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>

				<?php // $this->landing_featured_question(); ?>

				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>

			<div class="clear"></div>
		</div>

	</div>
