<?php

class Recipe4livingFacebookController extends ClientFrontendController
{
    private $_facebook;

    public function __construct($args)
    {
        parent::__construct($args);
        require_once(dirname(__FILE__) . '/facebook/src/facebook.php');
        $this->_facebook = new Facebook(array(
          'appId'  => '139543866184920',
          'secret' => 'f36f813768fb551274aefa09154d8c35',
          'cookie' => true,
        ));
    }

  public function login()
  {
        // Get User ID
        $user = $this->_facebook->getUser();

        if ($user) {
          try {
            // Proceed knowing you have a logged in user who's authenticated.
            $user_profile = $this->_facebook->api('/me');
          } catch (FacebookApiException $e) {
            error_log($e);
            $user = null;
          }
        }

        $server_name = $_SERVER['SERVER_NAME'];

        // Login or logout url will be needed depending on current user state.
        if ($user) {
          $logoutUrl = $this->_facebook->getLogoutUrl(array( 'next' => "http://$server_name/facebook/login/"));
        } else {
            $params = array('scope' => 'email,user_birthday,user_location,user_photos,user_about_me');
            $loginUrl = $this->_facebook->getLoginUrl($params);
        }

        if(isset($user_profile)) $fb_email = addslashes($user_profile['email']);

        $userModel = BluApplication::getModel('user');
        if ($user) {
                $uarray = $userModel->getUserByEmail($fb_email);
        $uid = $uarray["id"];

        // if email doesn't exists in our system, then create an account before login in
        if ($uid == '' || $uid == null) {
          // create an account

          $visitorIP = Request::getVisitorIPAddress();

          // get information from facebook
          $firstName = addslashes($user_profile['first_name']);
          $lastName = addslashes($user_profile['last_name']);
          $username = addslashes($user_profile['id']);
          $password = '6ff02bf53d1abb02c5d1e25a11b33c13'; // fb100

          // Add base details
          $query = "INSERT INTO users (type,username,password,email,firstname,lastname,lastLoggedin,deleted,rating)
              VALUES (\"member\",\"$username\",\"$password\",\"$fb_email\",\"$firstName\",\"$lastName\",NOW(),\"0\",\"0\")";
          $result = mysql_query($query);
          $uid = mysql_insert_id();

          // Add extra info
          $query = "INSERT INTO userInfo (userId,image,private,joined,about,favouriteFoods,ipaddr)
              VALUES (\"$uid\",\"\",\"0\",NOW(),\"\",\"\",\"$visitorIP\")";
          $result = mysql_query($query);

          // Renew indices
          $cacheModel = BluApplication::getModel('cache');
          $cacheModel->deleteEntriesLike('users\_');
        }

                // Login
                if ($userModel->faceBookLogin($uid)) {
                    // Redirect to account, or wherever they last were
                    return $this->_redirect( '/account');
                }
        }

        // Load template
        $this->_doc->setTitle('Login');
        include(BLUPATH_TEMPLATES.'/account/fb_login.php');
  }


    public function logout()
    {
        $server_name = $_SERVER['SERVER_NAME'];
      $logoutUrl = $this->_facebook->getLogoutUrl(array( 'next' => "http://$server_name/"));

      $fbParts = parse_url($logoutUrl);
      parse_str($fbParts['query'],$fbParts);
      if (strlen($fbParts['access_token']) <= 1) {
        // facebook token is invalid so don't send user to facebook system, instead clear session and redirect to R4L homepage
        $logoutUrl = $fbParts['next'];
      }

      // Logout
    $userModel = BluApplication::getModel('user');
    $userModel->logout();

    /* Clear session variables */
    Session::clear(array('messages'));

    unset($this->_currentUser);
    Session::delete('UserID');

        // Redirect to facebook which redirects back to R4L
        header('Location: '.$logoutUrl);
    }
}
?>
