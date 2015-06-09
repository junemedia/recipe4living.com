
	<form action="<?= SITEURL; ?>/categories/publish_category_confirm/<?= $slug; ?>" method="post">
		<p>
			<input type="checkbox" name="publisharticles" />Do you want to set live all the recipies or articles in this category and sub-categories as well (check if you offlined them while Unpublishing)?<br/>
		</p>
		<button type="submit" name="submit" value="submit"><span>Publish</span></button>
	</form>