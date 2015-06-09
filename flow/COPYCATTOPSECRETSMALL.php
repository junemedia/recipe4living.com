<?php

include_once("include.php");

$message = '';
$error = '';
$email_check_passed = false;
$signup_success = false;
$today = date('Y-m-d');
$new_email = true;
$error_type='';

if ($submit == 'Sign Me Up!') {
	if (!eregi("^[A-Za-z0-9\._-]+[@]{1,1}[A-Za-z0-9-]+[\.]{1}[A-Za-z0-9\.-]+[A-Za-z]$", $email)) {
		$error = "Invalid Email Address";
		$email_check_passed = false;
		$error_type='email_format_error';
	} else {
		// Check DNS records corresponding to a given domain
		// Get MX records corresponding to a given domain.
		list($prefix, $domain) = split("@",$email);
		if (!getmxrr($domain, $mxhosts)) {
			$error = "Invalid Email Address";
			$email_check_passed = false;
			$error_type='mx_error';
		} else {
			if ($error == '') {
				if (LookupImpressionWise($email) == false) {
					$error = "Invalid Email Address";
					$email_check_passed = false;
					$error_type='impressionwise_error';
				}
			}
			if ($error == '') {
				if (BullseyeBriteVerifyCheck($email) == false) {
					$error = 'Invalid Email Address';
					$email_check_passed = false;
					$error_type='briteverify_error';
				}
			}
		}
	}
	
	if ($error != '') {
		$message = $error;
		$signup_success = false;
		$saveResult = saveReportDetails($linkid,$subcampid,$error_type,$email,$message);
	} else {
		$signup_success = true;
		setcookie("EMAIL_ID", $email, time()+642816000, "/", ".recipe4living.com");

		$posting_url = "http://r4l.popularliving.com/flow.php?email=$email&ipaddr=".trim($_SERVER['REMOTE_ADDR'])."&keycode=kgjie95khls2968&sublists=$listid&subcampid=$subcampid&subsource=$linkid";
		$response = file_get_contents($posting_url);
		
		$message = "Thank you for signing up!"."<img src='http://jmtkg.com/plant.php?email=$email' width=0 height=0'></img>";
		
		//Only for a brand new email address that we have never seen do we fire the call to google analytics
		if (strstr($response, 'is_newemail:true')) {
			$result = mysql_query("SELECT * FROM report WHERE dateAdded = \"$today\" AND linkid = \"$linkid\"");
			if (mysql_num_rows($result) == 0) {
				$result = mysql_query("INSERT IGNORE INTO report (dateAdded,linkid,signup) VALUES (\"$today\",\"$linkid\",\"1\")");
			} else {
				$result = mysql_query("UPDATE report SET signup=signup+1 WHERE dateAdded = \"$today\" AND linkid = \"$linkid\"");
			}
			$saveResult = saveReportDetails($linkid,$subcampid,'signup',$email,$response);
		}else
		{
			$new_email = false;
			$saveResult = saveReportDetails($linkid,$subcampid,'signup_exist',$email,$response);
		}
		$email = '';
	}
} else {
	$result = mysql_query("SELECT * FROM report WHERE dateAdded = \"$today\" AND linkid = \"$linkid\"");
	if (mysql_num_rows($result) == 0) {
		$result = mysql_query("INSERT IGNORE INTO report (dateAdded,linkid,display) VALUES (\"$today\",\"$linkid\",\"1\")");
	} else {
		$result = mysql_query("UPDATE report SET display=display+1 WHERE dateAdded = \"$today\" AND linkid = \"$linkid\"");
	}
	$saveResult = saveReportDetails($linkid,$subcampid,'display');
}

function saveReportDetails($linkid,$subcampid,$actionType,$email=false,$severResponse='')
{
	$ipaddress = $_SERVER['REMOTE_ADDR'];
	$today = date('Y-m-d H:m:s');
	
	$result = mysql_query('INSERT INTO report_details (linkid,subcampid,actionType,email,dateAdded,serverResponse,ipaddress) VALUES ("'.$linkid.'",'.$subcampid.',"'.$actionType.'", "'.$email.'", "'.$today.'","'.$severResponse.'","'.$ipaddress.'")');
	return $result;
}
?>
<html>
<head>
<title></title>
<script language="JavaScript">
function closethis() {
	parent.R4LDhtml.fancybox.close();
}
function check_fields() {
	document.form1.email.style.backgroundColor="";
	var str = '';
	var response = '';
	
	var email = document.form1.email.value;
	var pattern = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
	var chkFlag = pattern.test(email);
	if(!pattern.test(email)) {
		str += "Please enter valid email address.";
		document.form1.email.style.backgroundColor="yellow";
	}
	
	if (str == '') {
		return true;
	} else {
		alert (str);
		return false;
	}
}
</script>
<style type="text/css">
#divBG {
background-image:url('http://pics.recipe4living.com/squeeze/Copycat-Top-Secret-Small.png');
background-repeat: no-repeat;
border:0px;
font-size:11px;
font-family: verdana;
height: 200px;
width: 300px;
position: relative;
}
#emailRow {
position: absolute;
top: 150px;
left: 20px;
font-family: arial,helvetica;
font-size:16px;
color:#004AB2;
font-weight:bold;
}
#emailConf {
position: absolute;
top: 130px;
left: 70px;
font-family: arial,helvetica;
font-size:14px;
color:red;
}
</style>
</head>
<body>
<form name="form1" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return check_fields();">
<input type="hidden" name="usercontrol" value="<?php echo $usercontrol; ?>">
<input type="hidden" name="submit" value="Sign Me Up!">
<input type="hidden" name="listid" value="<?php echo $listid; ?>">
<input type="hidden" name="subcampid" value="<?php echo $subcampid; ?>">
<input type="hidden" name="linkid" value="<?php echo $linkid; ?>">
<input type="hidden" name="source" value="<?php echo $source; ?>">
<div id="divBG">
	<div id="emailConf"><?php echo $message; ?></div>
	<div id="emailRow">
		<input style="background-color: #fdee8f;" type="text" id="email" name="email" value="<?php echo $email; ?>" size="40" maxlength="100" onfocus="if(this.value=='Your Email')this.value=''" onblur="if(this.value=='')this.value='Your Email'"><br><br>
		<INPUT style="vertical-align:bottom;position:absolute;top:25px;left:80px;" TYPE="image" SRC="http://pics.recipe4living.com/squeeze/subscribe1.png" BORDER="0" ALT="Submit Form" />
	</div>
</div>
</form>
<?php
if ($signup_success == true && strtolower($source) == 'google'&& $new_email==true) {
	echo '<script type="text/javascript">
		/* <![CDATA[ */
		var google_conversion_id = 991736654;var google_conversion_language = "en";var google_conversion_format = "2";var google_conversion_color = "ffffff";var google_conversion_label = "Yr3jCJrrywcQzuby2AM";var google_conversion_value = 0;
		/* ]]> */
		</script><script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
		<noscript><div style="display:inline;"><img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/991736654/?value=0&amp;label=Yr3jCJrrywcQzuby2AM&amp;guid=ON&amp;script=0"/></div></noscript>';
}
if ($signup_success == true && $usercontrol == 'N'&& $new_email==true) {
	echo "<!-- Google Tag Manager -->
<noscript><iframe src='//www.googletagmanager.com/ns.html?id=GTM-PPMDBL' 
height='0' width='0' style='display:none;visibility:hidden'></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PPMDBL');
dataLayer.push({'event': 'squeezepagesubscriberecipe4living'});</script>
<!-- End Google Tag Manager -->";
	echo "<script>window.setTimeout('closethis();', 2000);</script>";
}
if($new_email==false)
{
	echo "<script>window.setTimeout('closethis();', 2000);</script>";
}
?>

<script>
if (document.getElementById('email').value == '') { document.getElementById('email').value='Your Email'; }
</script>
</body>
</html>
