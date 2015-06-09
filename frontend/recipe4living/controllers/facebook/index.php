<?php

/**
 * Facebook PHP SDK (v.3.1.1)
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require 'src/facebook.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId'  => '139543866184920',
  'secret' => 'f36f813768fb551274aefa09154d8c35',
  'cookie' => true,
));


// Get User ID
$user = $facebook->getUser();

// We may or may not have this data based on whether the user is logged in.
//
// If we have a $user id here, it means we know the user is logged into
// Facebook, but we don't know if the access token is valid. An access
// token is invalid if the user logged out of Facebook.

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}

$server_name = $_SERVER['SERVER_NAME'];

print_r($user);

// Login or logout url will be needed depending on current user state.
if ($user) {
	$params = array( 'next' => "http://$server_name/account/login/");
	$logoutUrl = $facebook->getLogoutUrl($params);
} else {
	$params = array('scope' => 'email,user_birthday,user_location,user_photos,user_about_me');
	$loginUrl = $facebook->getLoginUrl($params);
}


//$user = 'spatel';
//$pass = 'spat8362';









?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<title>Login with Facebook</title>
</head>
<body>
<?php if ($user) {
	
echo "ID: ".$user_profile['id'];
echo "<br>";
echo "first_name: ".$user_profile['first_name'];
echo "<br>";
echo "last_name: ".$user_profile['last_name'];
echo "<br>";
echo "birthday: ".$user_profile['birthday'];
echo "<br>";
echo "location: ".$user_profile['location']['name'];
echo "<br>";
echo "gender: ".$user_profile['gender'];
echo "<br>";
echo "email: ".$user_profile['email'];
echo "<br>";
echo "username: ".$user_profile['username'];
echo "<br>";
echo "image: "."https://graph.facebook.com/".$user_profile['id']."/picture";
echo "<br>";
echo "<br>";
	
?>
	<a href="<?php echo $logoutUrl; ?>">Logout</a>
<?php } else { ?>
	<a href="<?php echo $loginUrl; ?>">Login with Facebook</a>
<?php } ?>
<pre><?php print_r($user_profile); ?></pre>
</body>
</html>
