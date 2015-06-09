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

$alias = substr(basename($_SERVER['REQUEST_URI']),0,strpos(basename($_SERVER['REQUEST_URI']),'.html'));

$query = "SELECT * FROM newsletters WHERE alias=\"$alias\" LIMIT 1";
$result = mysql_query($query);
echo mysql_error();
while ($row = mysql_fetch_object($result)) {
	$subject = $row->subject;
	$list = $row->list;
	$newsletterDate = $row->newsletterDate;
	$html = stripslashes($row->html);
	$keywords = $row->keywords;
	$desc = $row->desc;
	//echo "<h4><a href='$row->preview' target=_blank>Preview</a></h4>";
	
	$get_ad = "SELECT * FROM ads";
	$get_ad_result = mysql_query($get_ad);
	echo mysql_error();
	while ($ad_row = mysql_fetch_object($get_ad_result)) {
		$html = str_replace("[$ad_row->tag]",$ad_row->code,$html);
		
		$html = str_replace('<html>','',$html);
		$html = str_replace('</html>','',$html);
		$html = str_replace('</body>','',$html);
		$html = str_replace('<head>','',$html);
		$html = str_replace('</head>','',$html);
		
		$html = str_replace('<title>','<!-- ',$html);
		$html = str_replace('</title>',' -->',$html);
		$html = str_replace('<body bgcolor="#FFFFFF">','',$html);
		
		$html = str_replace("{opencount('<img src=\"{opct.url}\" width=\"1\" height=\"1\" border=\"0\">')}",'&nbsp;',$html);
	}
}

$querystring = '';
if (!strstr($_SESSION['previous_page'],'?')) {
	$querystring .= '?1=1';
}
if ($_GET['expand'] != '' && ctype_digit(trim($_GET['expand']))) {
	$querystring .= "&expand=".trim($_GET['expand']);
}




?>
<html>
<head>
<title><?php echo $subject; ?></title>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<meta name="description" content="<?php echo $desc; ?>" />
<link href="/newsletters/style.css" rel="stylesheet" type="text/css" media="screen" />


<?php if (isThisBotVisiting() == false) { $_SESSION['alias'] = $alias; ?>
<script type="text/javascript">
var sURL = ""+window.parent.location+"";
if (sURL.indexOf('view.php') > 0) {
	window.parent.location.href = 'http://<?php echo $_SERVER['SERVER_NAME']; ?>/index/newsletters';
}
</script>
<?php } ?>

<?php if ($_SESSION['alias_clear'] == 'yes') { unset($_SESSION['alias']); } ?>

<script>
function scrollToTop() {
	window.scroll(0,0); // horizontal and vertical scroll targets
}
</script>
<style>
table {
	font-size:12px;
}
</style>
<base target="_parent" />
</head>
<body>
<table align="center" width="100%" border="0">
<tr align="center">
	<td colspan="3">
		<table width="100%" align="center" class="orange-table">
			<tr>
				<td style="padding-left:10px;"><h2>Newsletter Archive: <?php echo $subject; ?><h2></td>
			</tr>
			<tr>
				<td align="right">
					<?php include_once("search_form.php"); ?>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
<tr>
	<td colspan="3"><a href="<?php echo $_SESSION['previous_page'].$querystring; ?>" target="_self">&lt; Go Back To Previous Page</a></td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
<tr>
	<td colspan="3" style="color:#707271;">Don't want to miss another issue?  Sign up for this newsletter 
	<a href="http://www.recipe4living.com/index/subctr" target="_blank">here</a>!
	</td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
</table>
<table width="100%" class="newsletter_full_view"><tr valign="top" align="center"><td>
<!-- START OF HTML -->
<?php echo $html; ?>
<!-- END OF HTML -->
</td></tr></table>
<table align="center" width="100%">
<tr>
	<td colspan="3">
	&nbsp;
	</td>
</tr>
<tr>
	<td colspan="3" style="color:#707271;">
	Did you find this newsletter helpful? Share it with a friend!<br><br>
	</td>
</tr>
<tr>
	<td colspan="3" style="color:#707271;">
	All newsletters provided by Recipe4Living.com are completely free and easy to receive! 
	Sign up for delicious recipes, healthy cooking tips and more today!
	</td>
</tr>
<tr>
	<td colspan="3">
	&nbsp;
	</td>
</tr>
<tr>
	<td align="left" colspan="2">
		<a href="<?php echo $main_page; ?>" target="_self">&lt; Back to Main Page</a>
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
