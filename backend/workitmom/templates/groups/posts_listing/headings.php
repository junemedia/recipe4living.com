<?php function show_heading(array $options, array $current){ ?>
			<th>
				<a href="?sort=<?= $options['key']; ?><?php 
				if ($current['sort'] == $options['key']) { echo '&amp;direction=' . ($current['direction'] == 'asc' ? 'desc' : 'asc'); }
				?>"><?= $options['text']; ?></a>
				<span style="font-size: 7pt;"><?php if ($current['sort'] == $options['key']) { echo '&nbsp;('.$current['direction'].')'; } ?></span>
			</th>
<?php } ?>


		<tr class="metadata">
			
			<?php /*
			<th><input class="checkall" type="checkbox" /></th>
			*/ ?>
			
			<?php show_heading(array('key' => 'id', 'text' => 'Post'), array('sort' => $sort, 'direction' => $direction)); ?>
			
			<?php show_heading(array('key' => 'poster', 'text' => 'Author'), array('sort' => $sort, 'direction' => $direction)); ?>
			
			<?php show_heading(array('key' => 'text', 'text' => 'Text preview'), array('sort' => $sort, 'direction' => $direction)); ?>
			
			<?php show_heading(array('key' => 'date', 'text' => 'Date'), array('sort' => $sort, 'direction' => $direction)); ?>
			
			<?php show_heading(array('key' => 'reports', 'text' => 'Reports'), array('sort' => $sort, 'direction' => $direction)); ?>
			
		</tr>