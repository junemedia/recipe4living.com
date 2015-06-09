<?php

/*
CREATE TABLE `Sweepstakes` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`email` VARCHAR( 100 ) NOT NULL ,
`first` VARCHAR( 100 ) NOT NULL ,
`last` VARCHAR( 100 ) NOT NULL ,
`address` VARCHAR( 100 ) NOT NULL ,
`city` VARCHAR( 100 ) NOT NULL ,
`state` VARCHAR( 2 ) NOT NULL ,
`zip` VARCHAR( 5 ) NOT NULL ,
`phone` VARCHAR( 10 ) NOT NULL ,
`date` DATETIME NOT NULL,
`ip` VARCHAR( 20 ) NOT NULL 
) ENGINE = MYISAM COMMENT = '$50 gift card from Chef''s Catalog sweepstakes';
*/

$error = '';

// TRIM ALL DATA AND ADD SLASHES JUST IN CASE TO PREVENT SQL INJECTION ATTACK.
// CLEAR OUR INVALID DATA
$submit = trim($_POST['submit']);
$email = trim($_POST['email']);
$first = trim($_POST['first']);
$last = trim($_POST['last']);
$address = trim($_POST['address']);
$city = trim($_POST['city']);
$state = strtoupper(trim($_POST['state']));
$zip = trim($_POST['zip']);
$phone1 = trim($_POST['phone1']);
$phone2 = trim($_POST['phone2']);
$phone3 = trim($_POST['phone3']);
$ip = addslashes(trim($_SERVER['REMOTE_ADDR']));
$today = trim(date('Y-m-d'));
$year_month = trim(date('Y-m'));

$valid_states = array('AL','AK','AZ','AR','CA','CO','CT','DE','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD',
		'MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT',
		'VT','VA','WA','WV','WI','WY');

if ($submit == 'Click Here To Complete Your Entry!') {
	
	if ($email == '' || $first == '' || $last == '' || $address == '' || $city == '' || $state == '' || $zip == '' || $phone1 == '' || $phone2 == '' || $phone3 == '') {
		$error = "* Please fill in all the required fields!";
	} else {
		if (!eregi("^[A-Za-z0-9\._-]+[@]{1,1}[A-Za-z0-9-]+[\.]{1}[A-Za-z0-9\.-]+[A-Za-z]$", $email)) {
			$error = "* Please enter valid email address.";
		} else if (!ctype_alpha($first)) {
			$error = "* Please enter a valid first name (Alphabets only).";
		} else if (!ctype_alpha($last)) {
			$error = "* Please enter a valid last name (Alphabets only).";
		} else if (!ereg("^[a-zA-Z0-9 \'\x2e\#\:\\\/\,\’\&\@()\°_-]{1,}$", $address)) {
			$error = "* Please enter a valid address.";
		} else if (!ereg("^[a-zA-Z0-9 \'\x2e\-\’\`\&]{1,}$", $city)) {
			$error = "* Please enter a valid city.";
		} else if (!(in_array($state, $valid_states))) {
		    $error = "* Please enter a valid state.";
		} else if (!ereg("^[0-9-]{5,}$", strtoupper($zip))) {
			$error = "* Please enter a valid five digit zip code.";
		} else if (!(ctype_digit($phone1) && strlen($phone1) == 3 && $phone1 >= 200)) {
			$error = "* Please enter a valid phone area code.";
		} else if (!(ctype_digit($phone2) && strlen($phone2) == 3 && $phone2 >= 200)) {
			$error = "* Please enter a valid phone prefix.";
		} else if (!(ctype_digit($phone3) && strlen($phone3) == 4)) {
			$error = "* Please enter a valid phone suffix.";
		}
	}
	
	if ($error == '') {
		mysql_pconnect ("192.168.51.51", "r4ldbuser", "acgnW3FsFSD2");
		mysql_select_db ("recipe4living_staging");
		
		$check_query = "SELECT * FROM Sweepstakes WHERE email =\"$email\" AND date LIKE '$year_month-%' LIMIT 1";
		$check_result = mysql_query($check_query);
		
		if (mysql_num_rows($check_result) > 0) {
			$message = 'Oops! This email address has already been entered into this month\'s giveaway. Please try again next month!';
		} else {
			$insert_query = "INSERT INTO Sweepstakes (email,first,last,address,city,state,zip,phone,date,ip)
						VALUES (\"$email\",\"$first\",\"$last\",\"$address\",\"$city\",
						\"$state\",\"$zip\",\"$phone1-$phone2-$phone3\",\"$today\",\"$ip\")";
			$insert_result = mysql_query($insert_query);
			$message = 'Success! You have entered the Chef\'s Catalog Gift Card Giveaway.';
		}
		
		echo "<center>$message<br><br>
				You will be redirected to Recipe4Living.com in the next 5 seconds.</center>
				<meta http-equiv='refresh' content='5;url=http://www.recipe4living.com'>";
		exit;
	} else {
		$error = "<tr><td colspan=;2;>$error</td></tr><tr><td colspan='2'>&nbsp;</td></tr>";
	}
}


?>

<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Recipe4Living.com Sweepstakes</title>
</head>
<body>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<table width="450" align="center" border="0" cellpadding="0" cellspacing="0" height="148">
<tbody>


<tr><td colspan="2" align="center">
<img src="http://pics.recipe4living.com/CHEFSLOGO.jpg">
</td></tr>


<tr><td colspan="2">&nbsp;</td></tr>

<tr><td colspan="2"><font color="#990000">
Please complete the following information to enter for a chance to win a 
$100 gift card from Chef's Catalog. New winners are chosen monthly.</font>
</td></tr>

<tr><td colspan="2">&nbsp;</td></tr>

<?php echo $error; ?>

<tr><td bgcolor="#ffe8c8" height="25">
	Email:
	</td>
	<td align="left" bgcolor="#ffe8c8" height="25">
	<input name="email" size="25" type="text" maxlength="100" value="<?php echo $email; ?>">
</td></tr>



<tr><td bgcolor="#f0fff0" height="25">First Name:</td>
      <td align="left" bgcolor="#f0fff0" height="25">
      <input name="first" size="25" type="text" maxlength="100" value="<?php echo $first; ?>">
     </td>
</tr>
    
    
    <tr>
      <td bgcolor="#ffe8c8" height="25">Last Name:</td>
      <td align="left" bgcolor="#ffe8c8" height="25"><input name="last" size="25" type="text" maxlength="100" value="<?php echo $last; ?>">
      </td>
    </tr>
    
    <tr>
      <td bgcolor="#f0fff0" height="25">Address:</td>
      <td align="left" bgcolor="#f0fff0" height="25"><input name="address" size="25" type="text" maxlength="100" value="<?php echo $address; ?>">
      </td>
    </tr>
    
    <tr>
      <td bgcolor="#ffe8c8" height="25">City:</td>
      <td align="left" bgcolor="#ffe8c8" height="25"><input name="city" size="25" type="text" maxlength="100" value="<?php echo $city; ?>">
      </td>
    </tr>
    
    <tr>
      <td bgcolor="#f0fff0" height="23">State:</td>
		      <td align="left" bgcolor="#f0fff0" height="25">
		      <input name="state" size="2" type="text" maxlength="2" value="<?php echo $state; ?>">
		      <font size="1"> example: IL</font>
		        </td>
		    </tr>
		    
    <tr>
      <td bgcolor="#ffe8c8" height="25">Zip Code:<br>
      
       </td>
    
      <td valign="top" align="left" bgcolor="#ffe8c8" height="25"><input name="zip" size="5" type="text" maxlength="5" value="<?php echo $zip; ?>">
	</td>
    </tr>
    <tr>
      <td bgcolor="#f0fff0" height="25">
Phone Number:</td>
      <td align="left" bgcolor="#f0fff0" height="25">
      <input name="phone1" size="3" type="text" maxlength="3" value="<?php echo $phone1; ?>">-
      <input name="phone2" size="3" type="text" maxlength="3" value="<?php echo $phone2; ?>">-
      <input name="phone3" size="4" type="text" maxlength="4" value="<?php echo $phone3; ?>">
</td></tr>

<tr><td height="25" colspan="2">&nbsp;</td></tr>


<tr><td height="25" colspan="2" align="center">
<input value="Click Here To Complete Your Entry!" name="submit" type="submit">
</td></tr>

<tr><td height="25" colspan="2">&nbsp;</td></tr>

<tr><td height="25" colspan="2"><font size="2">
Note: You must be a U.S. resident to enter online.  
See rules for alternate method of entry.</font>
</td></tr>


<tr><td height="25" colspan="2" align="center"><font size="2">
<a href="http://pics.recipe4living.com/r4l_sweepstakes_terms.htm" target="_blank">Official Rules</a>
 - <a href="http://www.recipe4living.com/privacy" target="_blank">Privacy Policy</a>
</font>
</td></tr>


</tbody></table>
</form>
</body></html>

