
	<form action="<?= SITEURL; ?>/categories/edit_category_save/<?= $slug; ?>" method="post">
		
		<label for="name">
			<span>Name:</span>
			<input type="text" name="name" value="<?= $name; ?>" style="width: 200px;" />
		</label>
		<label for="description">
			<span>Description: <small>(displayed in the category page)</small></span>
			<input type="text" name="description" value="<?= $description; ?>" style="width: 200px;" />
		</label>
		<label for="keywords">
			<span>Keywords: <small>(used in the &lt;meta&gt; header tag)</small></span>
			<input type="text" name="keywords" value="<?= $keywords; ?>" style="width: 200px;" />
		</label>
		
		<label for="pageDescription">
			<span>Page Description: <small>(used in the &lt;meta&gt; header tag)</small></span>
			<input type="text" name="pageDescription" value="<?= $pageDescription; ?>" style="width: 200px;" />
		</label>
		
		<button type="submit" name="submit" value="submit"><span>Save category</span></button>
	</form>