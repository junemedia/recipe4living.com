
	<strong>Selectors</strong>
	
	<?php
		if (!empty($selectors)) {
			foreach ($selectors as $selector) {
	?>
	
	<div>
		<a href="/oversight/meta/editSelector?selector=<?= $selector['id']; ?>"><?= $selector['internalName']; ?></a>
		<?php 
			if (!empty($selector['values'])) {
				$names = array();
				foreach ($selector['values'] as $groupId => $values) {
					// if pick group ...
					if ($groupNames = Arrays::column($values, 'internalName')) {
						$names[] = implode(', ', $groupNames);
					}
				}
				echo '('.implode(', ', $names).')';
			}
		?>
	</div>
	
	<?php
			}
		}
	?>