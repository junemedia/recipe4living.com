
	<form action="/oversight/hacks/serialize" method="post">
		
		<label for="q">Set </label>
		<input type="text" name="q" value="<?= $input; ?>" style="width: 300px;" />
		
		<label for="cast">to a</label>
		<input type="text" name="cast" value="string" style="width: 100px;" />
		<br />
		
		<input type="submit" value="Serialize!" />
		
	</form>