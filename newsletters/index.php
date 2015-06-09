<?php

include_once('session_handlers.php');
if (!(isset($_POST['PHPSESSID'])) && !(isset($_GET['PHPSESSID']))) {
	session_start();
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
} else {
	if ($_POST['PHPSESSID']) {
		$PHPSESSID = $_POST['PHPSESSID'];
	} else {
		$PHPSESSID = $_GET['PHPSESSID'];
	}
	
	if (session_id() == '') {
		session_start();
	}
}

include_once("config.php");

if ($_SESSION['alias'] !='') {
	$_SESSION['alias_clear'] = 'yes';
	$url = 'http://'.$_SERVER['SERVER_NAME'].'/newsletters/view.php/'.$_SESSION['alias'].'.html';
	header("location:$url");
	exit;
}

$years_array = array();
$query = "SELECT DISTINCT substring(newsletterDate,1,4) as nDate FROM newsletters WHERE live='Y' AND newsletterDate BETWEEN '2000-01-01' AND '$thirty_days_ago'";
$get_result = mysql_query($query);
echo mysql_error();
while ($date_row = mysql_fetch_object($get_result)) {
	array_push($years_array, $date_row->nDate);
}


?>
<html>
<head>
<title>Recipe4Living Newsletter Archive</title>
<link href="/newsletters/style.css" rel="stylesheet" type="text/css" media="screen" />
<script language="JavaScript">
function check_fields() {
	document.form1.subject.style.backgroundColor="";
	var str = '';
	var response = '';
	
	if (document.form1.subject.value == '') {
		str += "* Please enter your title/subject address.";
		document.form1.subject.style.backgroundColor="yellow";
	}
	
	if (str == '') {
		return true;
	} else {
		alert (str);
		return false;
	}
}
</script>
</head>
<body>

<table width="100%" align="left" border="0">
	<tr align="center" valign="top">
		<td>
			<table width="100%" border="0" align="center" class="orange-table">
				<tr>
					<td style="padding-left:10px;"><h2><b>Newsletter Archive</b><h2></td>
				</tr>
				<tr>
					<td align="right">
						<?php include_once("search_form.php"); ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td style="color:#707271;padding-left:8px;">If you missed a newsletter, don't worry! Search by title, date, or newsletter and catch up on old issues!
			If you'd like to sign up for a newsletter, <a href="http://www.recipe4living.com/index/subctr" target="_blank">click here</a>.</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><h1 style="color:#0F52B0;font-size: 1.25em; line-height: 1.25em; margin-bottom: 0pt;padding-left:8px;">Browse Archived Newsletter:</h1></td>
	</tr>
	<tr valign="top">
		<td>
			<table align="center" width="100%" style="font-size:12px;" border="0">
				<tr valign="top">
					<td width="35%" align="left">
						<table class="blue-heading-section" width="95%" cellpadding="0" cellspacing="0">
							<tr>
								<td height="25px" class="blue-heading-bar">Year Archive</td>
							</tr>
							<tr>
								<td style="color:#707271;padding-left:15px;padding-top:10px;">Choose a year to see a list of all issues:</td>
							</tr>
							<tr>
								<td style="color:#707271;padding-top:10px;" valign="top">
									<ul style="color:#0F52B0;padding-left:25px;">
									<?php arsort($years_array);
										foreach ($years_array as $list_year) { ?>
											<li style="padding-bottom:5px;"><a href="list.php?year=<?php echo $list_year; ?>" style="text-decoration: none;"><?php echo $list_year; ?></a></li>
										<?php }
									?>
									</ul>
								</td>
							</tr>
						</table>
					</td>
					<td width="55%" align="right">
						<table class="blue-heading-section" width="95%" cellpadding="0" cellspacing="0">
							<tr>
								<td height="25px" class="blue-heading-bar">Newsletter</td>
							</tr>
							<tr>
								<td style="color:#707271;padding-left:15px;padding-top:10px;">Choose a specific newsletter:</td>
							</tr>
							<tr>
								<td style="color:#707271;padding-top:10px;">
									<ul style="color:#0F52B0;padding-left:25px;">
										<li style="padding-bottom:5px;"><a href="list.php?list=Budget" style="text-decoration: none;">Budget Cooking</a></li>
										<li style="padding-bottom:5px;"><a href="list.php?list=Casserole" style="text-decoration: none;">Casserole Cookin'</a></li>
										<li style="padding-bottom:5px;"><a href="list.php?list=Copycat" style="text-decoration: none;">Copycat Classics</a></li>
										<li style="padding-bottom:5px;"><a href="list.php?list=Crockpot" style="text-decoration: none;">Crockpot Creations</a></li>
										<li style="padding-bottom:5px;"><a href="list.php?list=R4L" style="text-decoration: none;">Daily Recipes</a></li>
										<li style="padding-bottom:5px;"><a href="list.php?list=RSVP" style="text-decoration: none;">Party Tips & Recipes</a></li>
										<li style="padding-bottom:5px;"><a href="list.php?list=QE" style="text-decoration: none;">Quick & Easy Recipes</a></li>
									</ul>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-1200417-1']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
</body>
</html>
