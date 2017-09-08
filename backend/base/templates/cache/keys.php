
	<p>Total of <strong><?= $count; ?></strong> cache entries.</p>
	<table class="cache" width="100%">
		<tr>
			<th width="65%">Human readable key</th>
			<th width="10%">Time set</th>
			<th width="10%">Size in bytes</th>
			<th width="10%">Site</th>
			<th width="5%">Delete</th>
		</tr>
		<?php 
			foreach ($cacheItems as $cacheItem) { 
				$name = strlen($cacheItem['humanKey']) > 150 ? substr($cacheItem['humanKey'], 0, 150).'...' : $cacheItem['humanKey']
		?>
		<tr>
			<td><a href="/oversight/cache/?key=<?= urlencode($cacheItem['humanKey']); ?>"><?= $name; ?></a></td>
			<td><?= $cacheItem['timeSet'] ?></td>
			<td><?= Template::fileSize($cacheItem['sizeBytes']) ?></td>
			<td><?= $cacheItem['siteId']?></td>
			<td><a href="/oversight/cache/deleteEntry?key=<?= urlencode($cacheItem['humanKey']);; ?>">Delete</a></td>
		</tr>
		<?php
			}
		?>
	</table>
