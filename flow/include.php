<?php

$host = "192.168.51.65";
$dbase = "flow";
$user = "r4ldbuser";
$pass = "acgnW3FsFSD2";

mysql_pconnect ($host, $user, $pass);
mysql_select_db ($dbase);

while (list($key,$val) = each($_POST)) { $$key = addslashes(trim($val)); }
while (list($key,$val) = each($_GET)) { $$key = addslashes(trim($val)); }


function LookupImpressionWise($email_addr) {
	$pieces = explode("&", strtolower(file_get_contents("http://post.impressionwise.com/fastfeed.aspx?code=560020&pwd=SilCar&email=$email_addr")));
	foreach ($pieces as $pair) {
		$data = explode("=", $pair);
		$$data[0] = $data[1];
	}
	if($npd=='041')
	{
		$ipaddress = $_SERVER['REMOTE_ADDR'];
		$today = date('Y-m-d H:m:s');		
		$result = mysql_query('INSERT INTO report_details (linkid,subcampid,actionType,email,dateAdded,serverResponse,ipaddress) VALUES ("'.$_POST['linkid'].'",'.$_POST['subcampid'].',"impressionwise_041", "'.$email_addr.'", "'.$today.'","'.$npd.'","'.$ipaddress.'")');
		return true;
	}
	
	if (in_array($result, array("invalid", "seed", "trap", "mole"))) { return false; }
	return true;
}

/*function BullseyeBriteVerifyCheck ($email) {
	$request = "http://www3.tendollars.com/BriteVerifyForSubscriptionCenter.aspx?email=$email&source=subcenter";
	$handle = fopen($request, "r");
	$server_response = stream_get_contents($handle);
	fclose($handle);
	if (strstr($server_response,'valid') || strstr($server_response,'unknown')) { $return_value = true; } else { $return_value = false; }
	if (strstr($server_response,'not valid') || strstr($server_response,'invalid')) { $return_value = false; }
    	$server_response = addslashes($server_response);
    	$user_ip = trim($_SERVER['REMOTE_ADDR']);
    	$insert_bv_log = "INSERT INTO BullseyeBriteVerifyCheck (email,dateTimeAdded,ip,request, response)
                VALUES (\"$email\", NOW(), \"$user_ip\", \"$request\", \"$server_response\")";
	$insert_bv_log_result = mysql_query($insert_bv_log);
	return $return_value;
}*/

function BullseyeBriteVerifyCheck ($email) {
	$emailInfo = array();
	if(!empty($email))
	{
		$result = mysql_query("SELECT * FROM email_validation WHERE date(dateAdded) >= date_sub(curdate(),interval 1 day) and email = \"$email\"");
		$emailInfo = mysql_fetch_array($result,MYSQL_ASSOC);
		if (empty($emailInfo)) {
			$url = "https://bpi.briteverify.com/emails.json?address=$email&apikey=ad6d5755-ff3e-4a0b-8d63-c61bcffd57b1";
			$content = file_get_contents($url);
			$emailInfo = json_decode($content, true);
			
			$ipaddress = $_SERVER['REMOTE_ADDR'];
			
			if(!empty($emailInfo))
			{
				//Cache the new email address
				$sql = 'INSERT IGNORE INTO email_validation (email,status,error_code,error,dateAdded,ipaddress) VALUES ("'.$emailInfo["address"].'","'.$emailInfo["status"].'","'.$emailInfo["error_code"].'", "'.$emailInfo["error"].'", NOW(),"'.$ipaddress.'")';
				$result = mysql_query($sql);
			}
		} 
	}
	
	if(!empty($emailInfo) && ($emailInfo["status"]=="valid" || $emailInfo["status"]=="unknown" || $emailInfo["status"]=="accept all"|| $emailInfo["status"]=="accept_all"))
	{
		return true;
	}
	else
	{
		return false;
	}
}

?>
