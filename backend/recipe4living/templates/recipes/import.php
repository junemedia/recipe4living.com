
	<?= Messages::getMessages(); ?>

	<p>Please upload your normalization map datafile below:</p>

	<form action="/oversight/recipes/importNormalizationMap" method="post" enctype="multipart/form-data">
		<label for="datafile">
			<input type="file" name="datafile" value="" />
		</label>

		<button type="submit"><span>Submit</span></button>
	</form>

