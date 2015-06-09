
	<div id="box_details">
		<h2>
			<?= $name; ?>
			<?php if ($canAdd) { ?>
			<small><a href="#new">Add new</a></small>
			<?php } ?>
		</h2>
		
		<ul>
			<?php foreach ($boxContents as $contentId => $content) { ?>
			<li class="content <?= $box['type']; ?>"><?php $this->_content($contentId); ?></li>
			<?php } ?>
			
			<?php if ($canAdd) { ?>
			<li id="new" class="new content <?= $box['type']; ?>">
				<?php $this->add_content(); ?>
			</li>
			<?php } ?>
		</ul>
		
		<?php /* Hack */if ($box['type'] == 'dailyTip') { ?>
		<div style="display: block; float: left; width: 600px; height: 550px; overflow: auto; margin-left: 20px; padding: 10px;"><?= $encyclopedia['body']; ?></div>
		<?php } ?>
	</div>