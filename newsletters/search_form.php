<script>
function clearsearch() {
	if (document.getElementById("subject").value == "Enter search keywords...") {
		document.getElementById("subject").value="";
	}
}
</script>
<form name="form1" method="GET" action="<?php echo $root_dir; ?>/search.php" onsubmit="return check_fields();" target="_self">
	<input type="hidden" name="PHPSESSID" id="PHPSESSID" value="<?php echo session_id(); ?>">
	<table border="0" align="right" class="search-form-table">
		<tr>
			<td valign="middle">
				<input type="text" onclick="clearsearch()" onfocus="clearsearch()" id="subject" name="subject" maxlength="60" value="Enter search keywords..." style="padding: 5px 7px;border: 0;color: #4e4e4e;background: #FFF;border-top: 1px solid #CCC;border-left: 1px solid #CCC;border-right: 1px solid #eee;border-bottom: 1px solid #eee;font: 1em Arial, Helvetica, sans-serif;width:200px;">
			</td>
			<td valign="middle">
				<?php if ($list == '') { ?>
				<select name="list" style="background: #fff;width:100px;color: #515151;	border: 1px solid #ddd;	font-size: 1.0em;padding: 4px;border-top: 1px solid #CCC;border-left: 1px solid #CCC;border-right: 1px solid #eee;border-bottom: 1px solid #eee;font-family: Arial, Helvetica, sans-serif;">
					<option value="" <?php if ($list == '') { echo ' selected ';} ?>>All</option>
					<option value="Budget" <?php if ($list == 'Budget') { echo ' selected ';} ?>>Budget</option>
					<option value="RSVP" <?php if ($list == 'Casserole') { echo ' selected ';} ?>>Casserole Cookin'</option>
					<option value="Crockpot" <?php if ($list == 'Crockpot') { echo ' selected ';} ?>>Crockpot Creations</option>
					<option value="R4L" <?php if ($list == 'R4L') { echo ' selected ';} ?>>Daily Recipes</option>
					<option value="RSVP" <?php if ($list == 'RSVP') { echo ' selected ';} ?>>Party Tips & Recipes</option>
					<option value="QE" <?php if ($list == 'QE') { echo ' selected ';} ?>>Quick & Easy Recipes</option>
				</select>&nbsp;
				<?php } else { ?>
				<input type="hidden" name="list" id="list" value="<?php echo $list; ?>">
				<?php } ?>
			</td>
			<td valign="middle">
				<input type="image" src="<?php echo $root_dir; ?>/search_button.jpg" name="submit" border="0" alt="Submit Form" value="Search">
			</td>
		</tr>
	</table>
</form>