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


$list = '';
if (trim($_GET['list']) != '') {
	if (ctype_alnum(trim($_GET['list']))) {
		$list = trim($_GET['list']);
	}
}

if ($_GET['year'] != '') {
	if (ctype_digit(trim($_GET['year']))) {
		$_SESSION['year'] = trim($_GET['year']);
	} else {
		$_SESSION['year'] = '';
	}
} else {
	$_SESSION['year'] = '';
}

$list_filter = '';
if ($list != '') { $list_filter = " AND list=\"{$list}\" "; }

$year_filter = '';
if ($_SESSION['year'] != '') {
	$year_filter = " AND newsletterDate LIKE '{$_SESSION['year']}%' ";
}


$year_month_array = array();
$year_array = array();
$get_date = "SELECT DISTINCT substring(newsletterDate,1,7) as nDate 
		FROM newsletters WHERE live='Y' $list_filter 
		$year_filter AND newsletterDate BETWEEN '2000-01-01' AND '$thirty_days_ago' 
		ORDER BY substring(newsletterDate,1,7) DESC";
$get_result = mysql_query($get_date);
echo mysql_error();
while ($date_row = mysql_fetch_object($get_result)) {
	array_push($year_month_array, $date_row->nDate);
	array_push($year_array, substr($date_row->nDate,0,4));
}

$year_array = array_unique($year_array);
$year_month_array = array_unique($year_month_array);


//echo "<pre>";
//var_dump($year_month_array);
//echo "</pre>";

$expand_year = $year_array[0];
$epand_month = '';


$listing = '';
foreach ($year_array as $year) {
	if (date('Y') == $year) {
		$year_display_type = 'block';
		//$bullet_img = 'minus';
	} else {
		$year_display_type = 'none';
		//$bullet_img = 'plus';
	}
	
	// if it's only year in array, expand it
	if (count($year_array) == 1) {
		$year_display_type = 'block';
	}
	
	$listing .= "<ul>
		<li><a style=\"color:#0F52B0;cursor: pointer;\" onclick=\"collapsiblearchive_toggle('ara_ca_mo{$year}','ara_ca_mosign{$year}')\"><span id=\"ara_ca_mosign{$year}\"><img src='plus.png'>&nbsp;</span><b>{$year}</b></a>
		<br><ul id=\"ara_ca_mo{$year}\" style=\"display: $year_display_type;\">";

	for ($month = 12; $month >= 1; $month--) {
		$month_name = date("F", mktime(0, 0, 0, $month, 1));
		if (date('Y') == $year) {
			if ($month > date('m')) {
				continue;
			}
		}
		if (strlen($month) == 1) { $month = '0'.$month; }
		if (date('m') == $month && date('Y') == $year) {
			$month_display_type = 'none';
		}
			
		$query = "SELECT * FROM newsletters 
			WHERE live='Y' AND newsletterDate BETWEEN '2000-01-01' AND '$thirty_days_ago'
			$list_filter 
			AND newsletterDate LIKE '$year-$month%' 
			ORDER BY newsletterDate DESC";
		$result = mysql_query($query);
		echo mysql_error();
		//echo mysql_num_rows($result)."<br>";
		if (mysql_num_rows($result) > 0) {
			if ($epand_month == '') {
				$epand_month = $month;
			}
			
			$listing .= "<li><a style=\"color:#0F52B0;cursor: pointer;\" onclick=\"collapsiblearchive_toggle('ara_ca_po{$year}{$month}','ara_ca_posign{$year}{$month}')\"><span id=\"ara_ca_posign{$year}{$month}\"><img src='plus.png'>&nbsp;</span><b>{$month_name}</b></a></li>
				<li style=\"margin-top: 1em;color:#0F52B0;\">
					<ul id=\"ara_ca_po{$year}{$month}\" style=\"list-style:none;display: $month_display_type;color:#0F52B0;padding-bottom:5px;padding-top:0px;\">";
			
			while ($row = mysql_fetch_object($result)) {
				$month_day = substr($row->newsletterDate,8,2);
				$month_name_again = date("M", mktime(0, 0, 0, substr(substr($row->newsletterDate,5,5),0,2), 1));

				if ($_SESSION['year'] != '') { $list_name = "(".getFullListName($row->list).")"; } else { $list_name = ''; }
				
				$listing .= "<li style=\"margin-top: 1em;color:#0F52B0;\"><b>$month_name_again $month_day:</b> <a href=\"view.php/{$row->alias}.html?expand={$year}{$month}\" style=\"text-decoration: none;\">$row->subject {$list_name}</a></li>";
			}
	
			$listing .=	"</ul>
				</li>";
		}
	}
	
	$listing .=	"</ul>
	</li>
</ul>";
}

?>
<html>
<head>
<title><?php echo $list; ?></title>
<link href="/newsletters/style.css" rel="stylesheet" type="text/css" media="screen" />
<style>
ul, li {
	list-style: none;
	font-family: arial;
	font-size: 12px;
}
</style>
<script type="text/javascript">
collapsiblearchive_toggle = function(listelement, listsign) {
	/*alert(listelement);
	alert(listsign);*/
	var listobject = document.getElementById(listelement);
	var sign = document.getElementById(listsign);
	if(listobject.style.display == 'block') {
		listobject.style.display = 'none';
		collapsiblearchive_togglesign(sign, true);
	} else {
		listobject.style.display = 'block';
		collapsiblearchive_togglesign(sign, false);
	}
}
collapsiblearchive_togglesign = function(element,visibility) {
	(visibility == false ? element.innerHTML = '<img src="minus.png" alt="" />&nbsp;' : element.innerHTML = '<img src="plus.png" alt="" />&nbsp;');
	parent.NLArchiveOnloadCall();
}



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
<table align="left" width="100%">
<tr>
	<td colspan="3">
		<table width="100%" align="center" class="orange-table">
			<tr>
				<td style="padding-left:10px;"><h2>Newsletter Archive<?php
				if ($list !='') {
					echo ": ".getFullListName($list);
				}
				if ($_SESSION['year'] !='') {
					echo ": ".$_SESSION['year']." Issues";
				}
				?><h2></td>
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
	<td colspan="3"><a href='<?php echo $main_page; ?>' style="text-decoration: none;">&lt; Back to Main Page</a></td>
</tr>
</table>

<br>
			

<table align="center" width="100%" cellpadding="0" cellspacing="0" style="background-color:white;">
<tr>
	<td colspan="2" height="25" class="blue-heading-bar">
	Choose a month to see a list of all <?php echo $_SESSION['year'];echo getFullListName($list); ?> issues: 
	</td>
</tr>
<tr>
	<td colspan="2" style="background-color:white;padding-top:10px;">
		<?php echo $listing; ?>
		<?php if ($listing == '') { echo 'Sorry, this newsletter is not available yet, please check back few days later.  Thanks'; } ?>
	</td>
</tr>
<tr>
	<td></td>
	<td></td>
</tr>
</table>

<?php

if (ctype_digit(trim($_GET['expand'])) && strlen(trim($_GET['expand'])) == 6) {
	$expand_year = substr(trim($_GET['expand']),0,4);
	$epand_month = substr(trim($_GET['expand']),4,2);
}

?>


<script type="text/javascript">
collapsiblearchive_toggle('ara_ca_po<?php echo $expand_year; ?><?php echo $epand_month; ?>','ara_ca_posign<?php echo $expand_year; ?><?php echo $epand_month; ?>');
</script>

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
<?php $_SESSION['previous_page'] = htmlentities($_SERVER['REQUEST_URI']); ?>
