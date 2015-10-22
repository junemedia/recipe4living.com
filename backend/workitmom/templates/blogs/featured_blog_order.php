
	<form action="<?= SITEURL; ?>/blogs/featured_blog_order_submit/" method="post">
		<table id="blog_order" class="centered horizontal">
			
			<tr class="metadata">
				<th>ID</th>
				<th>Title</th>
				<th>Order</th>
				<th>Category</th>
			</tr>
			
			<?php if (Utility::iterable($blogs)){
				foreach($blogs as $blog){ ?>
			<tr>
				<td><?= $blog['blogID']; ?></td>
				<td><?= $blog['title']; ?></td>
				<td>
					<select name="order[<?= $blog['blogID']; ?>]"> 
						<?php for ($i = 0; $i < $notShown; $i++){ ?>
						<option value="<?= $i; ?>"<?= $blog['blogOrder'] == $i ? ' selected="selected"' : ''; ?>><?= $i; ?></option>
						<?php } ?>
						<option value="<?= $notShown; ?>"<?= $blog['blogOrder'] == $notShown ? ' selected="selected"' : ''; ?>>Not shown.</option>
					</select>
				</td>
				<td>
					<select name="category[<?= $blog['blogID']; ?>]">
						<?php foreach($availableCategories as $category){ ?>
						<option value="<?= $category; ?>"<?= $blog['blogCategory'] == $category ? ' selected="selected"' : ''; ?>><?= $category; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
				<?php }
			} ?>
			
			<tr class="metadata">
				<td colspan="4" style="text-align: center;">
					<input class="bigbutton" type="submit" name="submit" value="Update" />
				</td>
			</tr>
			
			<?php Template::startScript(); ?>
			new Table('blog_order');
			<?php Template::endScript(); ?>
			
		</table>
	</form>