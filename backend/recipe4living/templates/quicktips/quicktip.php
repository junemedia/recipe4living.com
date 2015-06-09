
	<form action="" method="post">
		
		<?= Messages::getMessages(); ?>
		
		<label for="section">
			Section:
			<input id="section" type="text" name="section" value="<?= isset($quicktip['section']) ? $quicktip['section'] : $quicktip['title'] ?>" />
		</label>
		
		<label for="title">
			Title:
			<input id="title" type="text" name="title" value="<?= $quicktip['title'] ?>" />
		</label>

		<label for="body">
			Body:
			<textarea id="body" name="body"><?= $quicktip['body'] ?></textarea>
		</label>
		
		<button type="submit" name="submit" value="submit">
			<span>Save</span>
		</button>
		
		<input type="hidden" name="task" value="save" />
		
	</form>
	
	<style type="text/css">
		
		form {
			position: relative;
			width: 500px;
		}
		
		input, textarea {
			display: block;
			background-color: #F8F8F8;
			margin: 1px;
			border: 1px #BBBBBB solid;
		}
		
		input {
			width: 450px;
			font-size: 14pt;
			padding: 15px;
		}
		
		textarea {
			width: 460px;
			font-size: 11pt;
			padding: 10px;
			line-height: 25px;
			
			height: 360px;
		}
		
		input:hover, textarea:hover {
			border: 2px #999999 solid;
			margin: 0px;
		}
		
		label {
			display: block;
			margin: 10px;
		}
		
		button {
			display: block;
			margin: 0 auto;
			padding: 20px;
		}
		
	</style>