
	<form action="<?= SITEURL; ?>/categories/remove_category_confirm/<?= $slug; ?>" method="post">
		<p>Are you sure you want to remove this category?</p>
		<p>
			Removes this category and any subcategories; 
			<br />
			recipes and articles will be left in the parent category.
		</p>
		<button type="submit" name="submit" value="submit"><span>Remove</span></button>
	</form>