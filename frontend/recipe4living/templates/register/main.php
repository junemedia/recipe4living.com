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
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>

			<div class="clear"></div>
		</div>

	</div>
