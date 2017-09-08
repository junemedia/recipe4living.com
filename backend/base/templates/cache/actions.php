
	<fieldset>
		<legend>Actions</legend>
		
		<p><strong>Note:</strong> Actions will be applied to all sites</p>
	
		<p><a href="/oversight/cache/listing/">View listing</a></p>
		<p><a href="/oversight/cache/purgeQueue">Purge queued entries &amp; rebuild core data</a></p>
	
		<form method="POST" action="/oversight/cache"">
			<label for="keys">Delete by comparison:</label> (one key fragment per line)<br />
			<textarea name="keys" id="keys" cols="50" rows="4"></textarea><br />
			<input type="submit" value="Delete" />
	
			<input type="hidden" name="task" value="deleteEntriesLike" />
		</form>

	</fieldset>
