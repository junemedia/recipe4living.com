
	<div id="customers_left">
	
	<h4 style="margin: 15px">Edit boxes</h4>

	<ul>
		<?php foreach ($boxes as $boxId => $box) { ?>
		<li<?php if($box['slug']==Template::get('box_slug')) { ?> class="on"<?php } ?>>
			<a href="/oversight/box/details/<?= $boxId; ?>"><?= $box['internalName']; ?></a>
		</li>
		<?php } ?>
	</ul>

	</div>
	