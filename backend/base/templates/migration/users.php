
	<?= Messages::getMessages(); ?>
	
	<div style="margin-top: 30px;">
		<form id="import_csv" action="" method="post" enctype="multipart/form-data">
			<label for="csv">Upload Unicode (UTF-8) .csv file</label>
			<input type="file" name="file" />
			<input type="submit" value="Upload" />
		</form>
	</div>