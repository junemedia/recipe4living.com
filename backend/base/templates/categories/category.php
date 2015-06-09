
	<li id="<?= $slug; ?>" class="<?= Template::get('hasChildren') ? ' expandable' : ''; ?><?= Template::get('current') ? ' current' : '';?>">
		
		<a <?= ($category['display'] == 1)?'':'style="color: red;"';?> href="<?= SITEURL; ?>/categories/<?= $slug; ?>"><?= $name; ?></a>
		
		<?php if (Template::get('hasChildren')) { ?>
		<ul>
			<?php 
				foreach ($values as $subCategory) {
					$this->_category($subCategory);
				}
			?>
		</ul>
		<?php } ?>
		
	</li>