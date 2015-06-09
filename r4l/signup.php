<?php

// Turn off all error reporting
error_reporting(0);

$error = '';
$success = '';
if ($_POST['submit'] == 'Submit') {
	$listid = '';
	$aJoinListId = $_POST['aJoinListId'];
	$email_addr = $_POST['email_addr'];
	
	if (count($aJoinListId) == 0) {
		$error = 'Please select at least one newsletter!';
	} else {
		foreach ($aJoinListId as $id) {
			$listid .= $id.',';
		}
	}
	
	if (!eregi("^[A-Za-z0-9\._-]+[@]{1,1}[A-Za-z0-9-]+[\.]{1}[A-Za-z0-9\.-]+[A-Za-z]$", $email_addr)) {
		$error = 'Please enter valid email address!';
	} else {
		list($prefix, $domain) = split("@",$email_addr);
		if (!getmxrr($domain, $mxhosts)) {
			$error = 'Please enter valid email address!';
		}
	}

	if ($error == '') {
		$listid .= '396,500,501,502,503';	// as bonus, add 396.  if not, then remove last , (comma)
		//echo var_dump($aJoinListId).'-'.$listid;

		$posting_url = "http://r4l.popularliving.com/r4l_signup.php?email=$email_addr&ipaddr=".trim($_SERVER['REMOTE_ADDR'])."&keycode=if3lkj6i8hjnax&sublists=$listid&subcampid=3098";
		$response = file_get_contents($posting_url);
		//echo $posting_url.'<br>'.$response;
		
		setcookie("EMAIL_ID", $email_addr, time()+642816000, "/", ".recipe4living.com");
		$plant_cookie = "<img src='http://jmtkg.com/plant.php?email=$email_addr' width='0' height='0'></img>";
		
		$pixel = "<!-- Google Tag Manager -->
				<noscript><iframe src=\"//www.googletagmanager.com/ns.html?id=GTM-PPMDBL\"
				height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
				<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
				new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
				j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
				'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
				})(window,document,'script','dataLayer','GTM-PPMDBL');
				dataLayer.push({'event': 'formsubscriberecipe4living'});</script>
				<!-- End Google Tag Manager -->";
		
		$success = 'Thank you for signing up!'.$plant_cookie.$pixel;
		$email_addr = '';
		$aJoinListId = array();
	}
	
} else {
	if (!isset($email_addr)) { $email_addr = ''; }
	if (!isset($aJoinListId)) { $aJoinListId = array(); }
}
?>
<html>
<head>
<title>Signup</title>
<style>
* {   
    margin: 0 !important;
    padding: 0px !important;
}
</style>
<script>
function check_fields() {
	var email = document.getElementById('email_addr').value;
	var pattern = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
	var chkFlag = pattern.test(email);
	if(!pattern.test(email)) {
		alert("If you'd like to sign up for our newsletters, please enter a valid e-mail address!");
		document.getElementById('email_addr').focus();
		return false;
	} else {
		if (!(document.getElementById('1').checked || document.getElementById('2').checked || document.getElementById('4').checked || document.getElementById('5').checked || document.getElementById('6').checked || document.getElementById('7').checked || document.getElementById('8').checked)) {
			alert("Please select at least one newsletter!");
			return false;
		} else {
			return true;
		}
	}
}
window.scroll(0,0); // horizontal and vertical scroll targets
</script>
</head>
<body>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="form1" name="form1" method="POST">
	<table width="295px" align="center" class="rounded" cellpadding="3" cellspacing="3" style="font: 12px Arial, Helvetica, sans-serif;font-weight:bold;overflow-y:hidden;overflow-x:hidden;">
	<tr>
		<td style="border: 0;margin:5;"><h2>Newsletter Signup</h2></td>
	</tr>
	<tr valign="top" id="email_field">
		<td style="border: 0;padding-left:10px;" valign="top">
<input value="<?php echo $email_addr; ?>" name="email_addr" style="background: #F2F2F2;border: 1px solid #6C6C6C;height:25px;" class="textinput required" type="text" id="email_addr" size="25" onfocus="showTable();if(this.value==' Enter your email address...')this.value=''" onblur="if(this.value=='')this.value=' Enter your email address...'" />
<input type="submit" name="submit" id="submit" value="Submit" onclick="return check_fields();" style="background:#494949;color:white;border: 1px solid #6C6C6C;width:75px;height:25px;">
		</td>
	</tr>
	<?php if ($success != '') { ?>
		<tr><td style="color:red;">&nbsp;&nbsp;<?php echo $success; ?></td></tr>
	<?php } ?>
	</table>
	<table width="290px" id="signup_details" align="center" cellpadding="8" cellspacing="8" style="font: 12px Arial, Helvetica, sans-serif;font-weight:bold;color:#27329F;overflow-y:hidden;overflow-x:hidden;">
	<?php if ($error != '') { ?>
		<tr><td style="line-height:0;color:red;"><?php echo $error; ?></td></tr>
	<?php } ?>
	<tr><td style="line-height:0">&nbsp;</td></tr>
	<tr>
		<td><input value="393" id="1" name="aJoinListId[]" type="checkbox" <?php if (is_array($aJoinListId) && in_array("393", $aJoinListId)) { echo ' checked '; } ?>> Daily Recipes (Daily)</td>
	</tr>
	<tr>
		<td><input value="395" id="2" name="aJoinListId[]" type="checkbox" <?php if (is_array($aJoinListId) && in_array("395", $aJoinListId)) { echo ' checked '; } ?>> Budget Cooking (3 issues/week)</td>
	</tr>
	<tr>
		<td><input value="394" id="4" name="aJoinListId[]" type="checkbox" <?php if (is_array($aJoinListId) && in_array("394", $aJoinListId)) { echo ' checked '; } ?>> Quick & Easy Recipes (3 issues/week)</td>
	</tr>
	<tr>
		<td><input value="511" id="5" name="aJoinListId[]" type="checkbox" <?php if (is_array($aJoinListId) && in_array("511", $aJoinListId)) { echo ' checked '; } ?>> Crockpot Creations (2 issues/week)</td>
	</tr>
	<tr>
		<td><input value="539" id="6" name="aJoinListId[]" type="checkbox" <?php if (is_array($aJoinListId) && in_array("539", $aJoinListId)) { echo ' checked '; } ?>> Casserole Cookin' (2 issues/week)</td>
	</tr>
	<tr>
		<td><input value="554" id="7" name="aJoinListId[]" type="checkbox" <?php if (is_array($aJoinListId) && in_array("554", $aJoinListId)) { echo ' checked '; } ?>> Copycat Classics (2 issues/week)</td>
	</tr>
	<tr>
		<td><input value="574" id="8" name="aJoinListId[]" type="checkbox" <?php if (is_array($aJoinListId) && in_array("574", $aJoinListId)) { echo ' checked '; } ?>> Diabetic-Friendly Dishes (2 issues/week)</td>
	</tr>
	<tr>
		<td align="left"><br>I understand that by signing up for these newsletters, I will also be receiving special offers from third party partners.  I also agree to Recipe4Living's <a href="/terms" target="_parent">Terms of Use</a>, and <a href="/privacy" target="_parent">Privacy Policy</a>.</td>
	</tr>
	</table>
</form>

<script>
if (document.getElementById('email_addr').value == '') { document.getElementById('email_addr').value=' Enter your email address...'; }
function showTable() { document.getElementById('signup_details').style.display = "block";document.getElementById('signup_details').style.visibility = "visible";parent.NewsletterSignupOnloadCall(); }
function hideTable() { document.getElementById('signup_details').style.display = "none";document.getElementById('signup_details').style.visibility = "hidden";parent.NewsletterSignupOnloadCall(); }
function hideField() { document.getElementById('email_field').style.display = "none";document.getElementById('email_field').style.visibility = "hidden";parent.NewsletterSignupOnloadCall(); }
<?php if ($error == '' || $success != '') { ?>hideTable();<?php } ?>
<?php if ($error != '') { ?>showTable();<?php } ?>
<?php if ($success != '') { ?>hideField();setTimeout("parent.hideItem('newsletter_signup');",2000);<?php } ?>
</script>
</body>
</html>
