
	<h1><?= $title; ?> for <?= $languages[$currentLangCode]; ?></h1>
	
	<small>N.B. Do not modify the text between the square brackets: [ ]</small>
	
	<table>
		<tr>
			<th>Key</th>
			<th>Text</th>
		</tr>
		<?php foreach ($languageStrings as $languageString) { ?>
		<tr>
			<td><code><?= $languageString['place']; ?></code></td>
			<td>
				<form action="" method="post">
					<?php if (strlen($languageString['text']) > 40) { ?>
					<textarea name="text"><?= htmlentities($languageString['text']); ?></textarea>
					<?php } else { ?>
					<input type="text" name="text" value="<?= htmlentities($languageString['text']); ?>" />
					<?php } ?>
					<input type="hidden" name="languageStringId" value="<?= $languageString['id']; ?>" />
					<input type="hidden" name="task" value="saveLanguageString" />
					<button>Save</button>
				</form>
			</td>
		</tr>
		<?php } ?>
	</table>
	
	<style type="text/css">
		input[type!=button] {
			width: 200px;
		}
		textarea {
			width: 200px;
			height: 100px;
			border-style: solid;
			border-width: 1px;
			padding: 3px;
			font-family: Segoe UI, Verdana;
			font-size: 9pt;
		}
	</style>