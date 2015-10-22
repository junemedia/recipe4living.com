<script>window.top.location.href = "http://www.recipe4living.com";</script>
<?php

// disable this while Samir is looking into memory leak issues...
exit;

// database connection login info
include_once("../config.newsletters_archive.php");


mysql_pconnect ($hostName, $username, $password);
mysql_select_db ($dbname);

// IF YOU MAKE CHANGES TO THIS FUNCTION, PLEASE MAKE SURE TO UPDATE COPY OF THIS FUNCTION IN BELOW SCRIPT
// /home/spatel/scripts/copy_nl_from_nibbles_to_nl_archive_system.php

function seoUrl($string) {
    //Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
    $string = strtolower($string);
    //Strip any unwanted characters
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    //Clean multiple dashes or whitespaces
    $string = preg_replace("/[\s-]+/", " ", $string);
    //Convert whitespaces and underscore to dash
    $string = preg_replace("/[\s_]/", "-", $string);
    //Convert two -- with only 1 that was already done at top
    $string = str_replace("--", "", $string);
    $string = str_replace('"', '', $string);
    $string = str_replace("'", '', $string);
    $string = str_replace("’", '', $string);
    return $string;
}

$thirty_days_ago = date('Y-m-d', strtotime('-30 days'));

$main_page = '/newsletters/index.php';

$domain = trim($_SERVER['SERVER_NAME']);

$root_url = "http://$domain/newsletters/";

$root_dir = '/newsletters';


include_once("functions.php");


?>
