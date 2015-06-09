	<div class="fl">
		<h2>Conversion Calculator</h2>
	</div>
	<div class="fl" style="padding-left:5px;padding-top: 5px;">
		<img class="help" src="<?= SITEASSETURL ?>/images/site/help.gif" width="12" height="12" alt="Help" title="Conversions between volume and weight are calculated using density = 0.42 g/ml" />
	</div>
	<div class="clear"></div>		
	<?= Messages::getMessages(); ?>
	
	<form action="<?= SITEURL; ?>/recipes/calculator" method="post">
		<label for="calculate_value">
			From:
			<br />
			<input type="text" name="calculate_value" value="<?= $value; ?>" class="textinput" style="width:20px;"/>
			<select name="calculate_from">
				<?php foreach ($ratios as $fromKey => $fromRatios) { ?>
				<option value="<?= $fromKey; ?>"<?= $from == $fromKey ? ' selected="selected"' : ''; ?>><?= $ratioNames[$fromKey]; ?></option>
				<?php } ?>
			</select>
		</label>
		
		<label for="calculate_to">
			To:
			<br />
			<input type="text" readonly="readonly" value="<?= $result; ?>" class="textinput disabled" style="width:30px;"/>
			<select name="calculate_to">
				<?php foreach ($ratios as $toKey => $toRatios) { ?>
				<option value="<?= $toKey; ?>"<?= $to == $toKey ? ' selected="selected"' : ''; ?>><?= $ratioNames[$toKey]; ?></option>
				<?php } ?>
			</select>
		</label>
		<button type="submit" name="submit" class="button-lg"><span>Convert</span></button> 
		<div class="clear"></div>
	</form>