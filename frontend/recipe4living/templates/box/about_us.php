
	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">

				<div id="about" class="rounded"><div class="content">

					<h1>About Recipe4Living</h1>

					<?= Messages::getMessages(); ?>

					<div class="text-content">

						<p>Recipe4Living is dedicated to fostering an online community interested in cooking, thinking, and living. Through reader feedback and contribution, we strive to create a meaningful space for people to come together and communicate about important topics ranging from the best cheesecake recipe to grilling tips to current trends in dieting. Recipe4Living understands that the best ideas come from a diverse community working together.</p>

						<p>Since Recipe4Living is community-driven, please feel free to contribute your own recipes and tips for thinking and living healthy with the submission link. We also appreciate any feedback, questions, or concerns.</p>

						<p>Thank you for visiting,</p>

						<p>Recipe4Living Editorial Team</p>

						<div class="divider"></div>

						<h2>Meet the Recipe4Living Team</h2>
						<?php
						foreach($box['content'] as $content) {
							echo '<div class="team-member">';
							echo $content['text'];
							echo '</div>';
						} ?>
					</div>

				</div></div>

			</div>

			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>

			<div id="panel-right" class="column">
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>

				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>

			<div class="clear"></div>
		</div>

	</div>
