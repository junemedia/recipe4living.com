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

//	Place code to connect to your DB here.
include_once("config.php");
	
if ($_GET['subject'] != '') { $_SESSION['subject'] = trim($_GET['subject']); }

if (ctype_alnum(trim($_GET['list']))) {
	if ($_GET['list'] != '') { $_SESSION['list'] = trim($_GET['list']); }
}

$list = '';
if (trim($_GET['list']) != '') {
	if (ctype_alnum(trim($_GET['list']))) {
		$list = trim($_GET['list']);
	}
}


$subject_filter = '';

$mysqlsafe_subject = mysql_real_escape_string($_SESSION['subject']);

if ($_SESSION['subject'] != '') { $subject_filter = " AND subject LIKE \"%{$mysqlsafe_subject}%\" "; }

$list_filter = '';
if ($list != '') { $list_filter = " AND list=\"{$list}\" "; }

// How many adjacent pages should be shown on each side?
$adjacents = 3;
	

//   First get total number of rows in data table. 
//   If you have a WHERE clause in your query, make sure you mirror it here.
$query = "SELECT count(*) as num FROM newsletters WHERE MATCH(subject, keywords) AGAINST('{$mysqlsafe_subject}') $list_filter ";
$total_pages = mysql_fetch_object(mysql_query($query));
$total_pages = $total_pages->num;
// Setup vars for query.
$targetpage = trim($_SERVER['PHP_SELF']); 	//your file name  (the name of this file)
$limit = 25; 								//how many items to show per page
	
if (ctype_digit(trim($_GET['page']))) {
	$page = trim($_GET['page']);
} else {
	$page = 0;
}
	
if($page) {
	$start = ($page - 1) * $limit; 			//first item to display on this page
} else {
	$start = 0;								//if no page var is given, set start to 0
}

// Get data.
//$sql = "SELECT * FROM newsletters WHERE 1=1 $list_filter $subject_filter LIMIT $start, $limit";
$sql = "SELECT *, MATCH(subject, keywords) AGAINST('{$mysqlsafe_subject}') AS score 
		FROM newsletters 
		WHERE MATCH(subject, keywords) AGAINST('{$mysqlsafe_subject}') 
		$list_filter 
		ORDER BY score DESC 
		LIMIT $start, $limit";
$result = mysql_query($sql);
echo mysql_error();
while($row = mysql_fetch_object($result)) {
	$listing .= "<li style=\"margin-top: 1em;color:#0F52B0;\"><a href='view.php/$row->alias.html' style=\"text-decoration: none;\">$row->subject</a></li>";
}
		
// Setup page vars for display.
if ($page == 0) $page = 1;					//if no page var is given, default to 1.
$prev = $page - 1;							//previous page is page - 1
$next = $page + 1;							//next page is page + 1
$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage - 1;						//last page minus 1
	
 
//	Now we apply our rules and draw the pagination object. 
//	We're actually saving the code to a variable in case we want to draw it more than once.
$pagination = "";
if($lastpage > 1) {	
	$pagination .= "<div class=\"pagination\"><b>Pages: </b>";
	//previous button
	if ($page > 1) {
		$pagination.= "<a href=\"$targetpage?page=$prev\">Previous</a>";
	} else {
		$pagination.= "<span class=\"disabled\">Previous</span>";	
	}
	
	//pages	
	if ($lastpage < 7 + ($adjacents * 2)) {
		//not enough pages to bother breaking it up
		for ($counter = 1; $counter <= $lastpage; $counter++)
		{
			if ($counter == $page)
				$pagination.= "<span class=\"current\">$counter</span>";
			else
				$pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";					
		}
	} elseif($lastpage > 5 + ($adjacents * 2)) {
		//enough pages to hide some 
		//close to beginning; only hide later pages
		if($page < 1 + ($adjacents * 2)) {
			for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
				if ($counter == $page) {
					$pagination.= "<span class=\"current\">$counter</span>";
				} else {
					$pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";					
				}
			}
			$pagination.= "...";
			$pagination.= "<a href=\"$targetpage?page=$lpm1\">$lpm1</a>";
			$pagination.= "<a href=\"$targetpage?page=$lastpage\">$lastpage</a>";		
		} elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
			//in middle; hide some front and some back
			$pagination.= "<a href=\"$targetpage?page=1\">1</a>";
			$pagination.= "<a href=\"$targetpage?page=2\">2</a>";
			$pagination.= "...";
			for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
				if ($counter == $page) {
					$pagination.= "<span class=\"current\">$counter</span>";
				} else {
					$pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";					
				}
			}
			$pagination.= "...";
			$pagination.= "<a href=\"$targetpage?page=$lpm1\">$lpm1</a>";
			$pagination.= "<a href=\"$targetpage?page=$lastpage\">$lastpage</a>";		
		} else {
			//close to end; only hide early pages
			$pagination.= "<a href=\"$targetpage?page=1\">1</a>";
			$pagination.= "<a href=\"$targetpage?page=2\">2</a>";
			$pagination.= "...";
			for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
				if ($counter == $page) {
					$pagination.= "<span class=\"current\">$counter</span>";
				} else {
					$pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";					
				}
			}
		}
	}

	//next button
	if ($page < $counter - 1) {
		$pagination.= "<a href=\"$targetpage?page=$next\">Next</a>";
	} else {
		$pagination.= "<span class=\"disabled\">Next</span>";
	}
	$pagination.= "</div>\n";
}
?>
<html>
<head>
<title>Recipe4Living Newsletter Archive - Search View</title>
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
<table width="100%" align="center" border="0">
	<tr align="center">
		<td>
			<table width="100%" align="center" class="orange-table">
					<tr>
						<td><h2>Newsletter Archive<h2></td>
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
		<td><a href="<?php echo $main_page; ?>">&lt; Go Back To All Newsletters</a></td>
	</tr>
	<tr>
		<td>
			<table align="center" width="100%" cellpadding="0" cellspacing="0" style="background-color:white;">
				<tr>
					<td height="25" class="blue-heading-bar">
						Results For: <?php echo stripslashes($_SESSION['subject']); ?>
					</td>
				</tr>
				<tr>
					<td>
						<ul>
							<?php echo $listing; ?>
						</ul>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center">
			<?php echo $pagination; ?>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
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
<?php $_SESSION['previous_page'] = htmlentities($_SERVER['REQUEST_URI']); ?>
