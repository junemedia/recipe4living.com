
	<form action="<?= SITEURL; ?>/categories/unpublish_category_confirm/<?= $slug; ?>" method="post">
		<p>
			<input type="checkbox" name="unpublisharticles" value="1" />Do you want to offline all the recipies or articles in this category and sub-categories as well (they cannot be searched any more)?<br/>
		</p>
		<button type="submit" name="submit" value="submit"><span>Unpublish</span></button>
	</form>