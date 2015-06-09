
	<table id="items_data" class="centered horizontal" style="width:1000px">
		
		<tr class="metadata">
			<td colspan="3" style="border: 0px;">
				<div style="height: 10px; margin: 14px 0px;">
					<h2>Related links</h2>
					<h3><?= $article['title'] ?></h3>
				</div>
			</td>
		</tr>
		
		<?php if($links) { ?>
		
		<tr class="metadata">
			<th style="width: 80%">
				Title
			</th>
			<th style="width: 10%">&nbsp;</td>
			<th style="width: 10%">&nbsp;</td>
		</tr>
		
		<?php foreach($links as $link) { ?>
		<tr class="metadata">
			<td>
				<?= $link['title'] ?>
			</td>
			<td style="text-align:center">
				<a href="<?= SITEURL ?>/<?= $urlPart ?>/link/<?= $article['id'] ?>?linkId=<?= $link['id'] ?>">edit</a>
			</td>
			<td style="text-align:center">
				<a href="<?= SITEURL ?>/<?= $urlPart ?>/delete_link/<?= $article['id'] ?>?linkId=<?= $link['id'] ?>" onclick="if(!confirm('Are you sure you want to delete this link?')) return false">delete</a>
			</td>
		</tr>
		<?php } ?>
		
		<?php } ?>
		
		<tr>
			<td colspan="3" style="text-align: center">
				<a href="<?= SITEURL ?>/<?= $urlPart ?>/link/<?= $article['id'] ?>">Add new</a>
			</td>
		</tr>
		
	</table>

