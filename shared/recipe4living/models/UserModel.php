<?php

/**
 *  User model
 *
 *  @package BluApplication
 *  @subpackage SharedModels
 */
class ClientUserModel extends BluModel
{
  /**
   *  Current user
   *
   *  @access protected
   *  @var array
   */
  protected $_currentUser;

  /**
   *  Get current user
   *
   *  @access public
   *  @return array User
   */
  public function getCurrentUser()
  {
    // Get from memory
    if (empty($this->_currentUser)) {

      // Get from session
      if ($userId = Session::get('UserID')) {
        $this->_currentUser = $this->getUser($userId);
      } else {
        $this->_currentUser = false;
      }
    }

    // Fail
    return $this->_currentUser;
  }

  /**
   *  Get a user
   *
   *  @access public
   *  @param int User ID
   *  @param bool Rebuild
   *  @return array User
   */
  public function getUser($userId = null, $forceRebuild = false)
  {
    if ($userId == 0) {
      return false;
    }

    // Get base details
    $cacheKey = 'user_'.$userId;
    $user = $forceRebuild ? false : $this->_cache->get($cacheKey);
    if ($user === false) {

      // Get from database
      $query = 'SELECT u.*, ui.*
        FROM `users` AS `u`
        LEFT JOIN `userInfo` AS `ui` ON u.id = ui.userId
        WHERE u.id = '.(int) $userId;
      $this->_db->setQuery($query, 0, 1);
      if (!$user = $this->_db->loadAssoc()) {
        return false;
      }

      // Get full name
      if ($user['firstname']) {
        $user['fullname'] = $user['firstname'];
      }
      if ($user['lastname']) {
        $user['fullname'] = (empty($user['fullname']) ? '' : $user['fullname'].' ').$user['lastname'];
      }
      if (empty($user['fullname'])) {
        $user['fullname'] = $user['username'];  // Fallback
      }
      if ($user['displayname']) {
        $user['fullname'] = $user['displayname']; // Override
      }

      // Get user submissions (note: articles table contains recipes, questions etc. as well)
      $query = 'SELECT a.id, a.type
        FROM `articles` AS `a`
        WHERE a.author = '.(int) $user['id'].'
        AND a.live = 1';
      $this->_db->setQuery($query);
      $user['articles'] = array_merge(array(
        'recipe' => array(),
        'quicktip' => array(),
        'question' => array(),
        'article' => array(),
        'guide' => array(),
        'blog' => array()
      ), $this->_db->loadGroupedAssocList('type', 'id', 'id'));

      // Newsletter subscriptions? - WHERE clause is probably wrong, but we'll fix later.
      $query = 'SELECT COUNT(mls.*)
        FROM `mailListSubscriptions` AS `mls`
        LEFT JOIN `mailSubscribers` AS `ms` ON mls.subscriberId = ms.id
        LEFT JOIN `users` AS `u` ON ms.email = u.email
        WHERE u.id = '.(int) $user['id'];
      $user['subscribed'] = (bool) $this->_db->loadResult();

      // Oh god oh god, what to do....hmm, how about this...
      if (empty($user['joined'])) {
        $user['joined'] = date('Y-m-d H:i:s', 1234567890 - rand(1000, 50000000));
      }

      // Store in cache
      $this->_cache->set($cacheKey, $user);
    }

    // Get user saves
    $cacheKey = 'user_'.$user['id'].'_saves';
    $user['saves'] = $forceRebuild ? false : $this->_cache->get($cacheKey);
    if ($user['saves'] === false) {

      // Get from database
      $query = 'SELECT us.*
        FROM `userSaves` AS `us`
        WHERE us.userId = '.(int) $user['id'];
      $this->_db->setQuery($query);
      $user['saves'] = $this->_db->loadGroupedAssocList('objectType', 'objectId');

      // Store in cache
      $this->_cache->set($cacheKey, $user['saves']);
    }

    // Return
    return $user;
  }

  /**
   * Get a user based on their email address
   * @param string $email The email address of the user
   * @return bool success
   */
  public function getUserByEmail($email)
  {
    // Get id of user
    $query = 'SELECT u.id
      FROM `users` AS `u`
      WHERE u.email = "'.$this->_db->escape($email).'"';
    $this->_db->setQuery($query);
    $userId = $this->_db->loadResult();
    return $this->getUser($userId);
  }

  /**
   *  Get user ID from username
   *
   *  @access public
   *  @param string Username
   *  @return int User ID
   */
  public function getUserId($username)
  {
    $usernameMapping = $this->_getUserUsernameMapping();
    $usernameMapping = array_flip($usernameMapping);  // This is painful...
    return isset($usernameMapping[$username]) ? $usernameMapping[$username] : false;
  }

  /**
   *  Get user ID to username mapping
   *
   *  @access protected
   *  @param bool Rebuild
   *  @return array User ID => Username mapping
   */
  protected function _getUserUsernameMapping($forceRebuild = false)
  {
    // Let's just steal someone else's hard work
    return $this->_getSortIndex('username', Utility::SORT_ASC);
  }

  /**
   *  Hash a password.
   *
   *  @static
   *  @param string Username.
   *  @param string Password to hash.
   *  @return string Hash.
   */
  protected static function _hashPassword($username, $password)
  {
    $username = strtolower($username);
    //echo  "md5(strtolower(trim($password)).BluApplication::getSetting('passwordSalt').$username)"; exit;
    return md5(strtolower(trim($password)).BluApplication::getSetting('passwordSalt').$username);
  }

  /**
   *  Meh
   */
  public function hashPassword($username, $password)
  {
    return self::_hashPassword($username, $password);
  }

  /**
   *  Process login procedures
   *
   *  @access protected
   *  @param int $userId User ID
   *  @return bool Success
   */
  protected function _login($userId)
  {
    Session::set('UserID', $userId);
    unset($this->_currentUser);

    // Set last login time
    $query = 'UPDATE users SET lastLoggedin=NOW() WHERE id='.(int)$userId;
    $this->_db->setQuery($query);
    $this->_db->query();

    return true;
  }

  /**
   *
   *
   */
  public function setProfileImageFromUpload($userId, $uploadId, $file)
  {
    // Determine path to asset file
    $origFileName = basename($file['name']);
    $assetFileName = md5(microtime().mt_rand(0, 250000)).'_'.$origFileName;
    $assetPath = BLUPATH_ASSETS.'/userimages/'.$assetFileName;

    // Move uploaded file into place
    if (!Upload::move($uploadId, $assetPath)) {
      return false;
    }

    // Update user details
    return $this->setProfileImage($userId, $assetFileName);
  }

  /**
   * Set the users photo from an uploaded file
   *
   * @param int User ID
   * @param string File name
   */
  public function setProfileImage($userId, $fileName)
  {
    // Get current photo details
    $query = 'SELECT image FROM userInfo
      WHERE userID = '.(int)$userId;
    $this->_db->setQuery($query);
    $oldFileName = $this->_db->loadResult();

    // Delete old photo if we are setting a new one and it isn't an avatar
    $avatars = array('avatar1.png', 'avatar2.png', 'avatar3.png');
    //var_dump($oldFileName);
    if (!in_array($oldFileName, $avatars) && !empty($oldFileName) && ($oldFileName != $fileName) && file_exists(BLUPATH_ASSETS.'/userimages/'.$oldFileName)) {
      unlink(BLUPATH_ASSETS.'/userimages/'.$oldFileName);
    }

    // Add details of new photo to database
    $query = 'UPDATE userInfo
      SET image = "'.Database::escape($fileName).'"
      WHERE userID = '.(int)$userId;
    $this->_db->setQuery($query);
    return $this->_db->query();
  }

  /**
   *  Edit a user
   *
   *  @access public
   *  @param int|array $userId Either the user Id, or an array of properties (inc. userId) to be updated
   *  @param string $password Password
   *  @param string $firstName First name
   *  @param string $lastName Last name
   *  @param string $location Location name
   *  @param string $imageName Image name
   *  @param string $about About
   *  @param string $favouriteFoods Favourite foods
   *  @param string $dob Date of Birth
   *  @return bool Success
   */
  public function editUser($userId, $password = null, $firstName = null, $lastName = null, $location = null, $imageName = null, $about = null, $favouriteFoods = null, $dob = null)
  {
    // allow a assoc array to be passed instead of all the variables above
    if(is_array($userId)) {
      // check for userId
      if(!isset($userId['userId'])) {
        return false;
      }
      // bring variables out into scope of function
      extract($userId);

      // define list of allowed properties for the users table
      $userVars = array('');
    }

    // Prepare
    $success = true;

    // Set userInfo data
    $extraParams = array();
    if (!is_null($imageName)) {
      $extraParams['image'] = $image;
    }
    if (!is_null($dob)) {
      $extraParams['dob'] = $dob;
    }
    if (!is_null($about)) {
      $extraParams['about'] = $about;
    }
    if (!is_null($favouriteFoods)) {
      $extraParams['favouriteFoods'] = $favouriteFoods;
    }

    if (!empty($extraParams)) {
      foreach ($extraParams as $field => &$param) {
        $param = '`'.$field.'` = "'.$this->_db->escape($param).'"';
      }
      unset($param);

      $query = 'UPDATE `userInfo`
        SET '.implode(', ', $extraParams).'
        WHERE `userId` = '.(int) $userId;

      $this->_db->setQuery($query);
      if (!$this->_db->query()) {
        $success = false;
      }
    }
    unset($query);
    unset($imageName);
    unset($about);
    unset($favouriteFoods);
    unset($extraParams);

    // Update base details
    $baseParams = array_filter(array(
      'password'    => $password,
      'firstName'   => $firstName,
      'lastName'    => $lastName,
      'location'      => $location
    ));

    if (!empty($baseParams)) {
      // only update the password if it needs to be!
      if(!is_null($baseParams['password'])) {
        // Load original user details
        $user = $this->getUser($userId);
        //echo self::_hashPassword($user['username'], $password) . " = self::_hashPassword($user[username], $password)";   exit;
        $baseParams['password'] = self::_hashPassword($user['username'], $password);
      }
      foreach ($baseParams as $field => &$param) {
        $param = '`'.$field.'` = "'.$this->_db->escape($param).'"';
      }
      unset($param);
      $query = 'UPDATE `users`
        SET '.implode(', ', $baseParams).'
        WHERE `id` = '.(int) $userId;

      $this->_db->setQuery($query);
      if (!$this->_db->query()) {
        $success = false;
      }
    }

    // Flush cache
    if ($success) {
      $this->flushUser($userId);
      if(!empty($password) && !empty($type) && $type=='admin') {
        // delete all cached admin logins (including cached passwords)
        $this->_cache->delete('adminLogins');
      }
    }

    // Return
    return $success;
  }

  /**
   *  Set email address for a user
   *
   *  @access public
   *  @param int User ID
   *  @param string Email address
   *  @return bool Success
   */
  public function setEmailAddress($userId, $email)
  {
    $query = 'UPDATE `users`
      SET `email` = "'.$this->_db->escape($email).'"
      WHERE `id` = '.(int) $userId;
    $this->_db->setQuery($query);
    return $this->_db->query();
  }

  /**
   *  Rebuild indices DANGER DANGER DANGER
   *
   *  @access public
   *  @return bool Success
   */
  public function rebuildCoreData()
  {
    $cacheModel = BluApplication::getModel('cache');
    return $cacheModel->deleteEntriesLike('user');
  }

  /**
   *  Refresh a user's cache
   *
   *  @access public
   *  @param int User ID
   *  @return bool
   */
  public function flushUser($userId)
  {
    return $this->_cache->delete('user_'.$userId);
  }

  /**
   *  Refresh a user's ratings cache
   *
   *  @access public
   *  @param int User ID
   *  @return bool
   */
  public function flushUserRatings($userId)
  {
    return $this->flushUser($userId); // User ratings are built into user.
  }

  /**
   *  Refresh a user's saves' cache
   *
   *  @access public
   *  @param int User ID
   *  @return bool
   */
  public function flushUserSaves($userId)
  {
    return $this->_cache->delete('user_'.$userId.'_saves');
  }

  /**
   *  Refresh log of a user's article submissions
   *
   *  @access public
   *  @param int User ID
   *  @return bool
   */
  public function flushUserSubmissions($userId)
  {
    return $this->flushUser($userId); // User submissions are built into user.
  }

  /**
   *  Get user ID to name mapping
   *
   *  N.B. DON'T CONFUSE WITH "USERNAME".
   *
   *  @access public
   *  @param bool Rebuild
   *  @return array
   */
  public function getUserNameMapping($forceRebuild = false)
  {
    static $mapping;
    if (!$mapping) {
      $cacheKey = 'users_nameMapping';
      $mapping = $forceRebuild ? false : $this->_cache->get($cacheKey);
      if ($mapping === false) {

        // Get user IDs
        $query = 'SELECT u.id
          FROM `users` AS `u`';
        $this->_db->setQuery($query);
        $mapping = $this->_db->loadResultAssocArray('id', 'id');

        // Get users' full names.
        foreach ($mapping as $userId => &$user) {
          $user = $this->getUser($userId);
          $user = $user['fullname'];
        }
        unset($user);

        $this->_cache->set($cacheKey, $mapping);
      }
    }
    return $mapping;
  }

  /**
   *  Get users
   *
   *  @access public
   *  @param int Offset
   *  @param int Limit
   *  @param string Sort
   *  @param string Search term
   *  @return array User IDs
   */
  public function getUsers($offset = null, $limit = null, $sort = null, $search = null)
  {
    // Fewer cache entries
    if ($search) {
      $search = strtolower($search);
    }

    // Get users from cache
    $cacheKey = 'users_'.serialize(array(
      'search' => $search
    ));
    $users = $this->_cache->get($cacheKey);
    if ($users === false) {

      // Do search
      if (strlen($search)) {
        $users = $this->_quickSearch($search);

        // Get all users
      } else {
        $query = 'SELECT u.id
          FROM `users` AS `u`';
        $this->_db->setQuery($query);
        $users = $this->_db->loadResultAssocArray('id', 'id');
      }

      // Store in cache
      $this->_cache->set($cacheKey, $users, 0, null, array('compress' => true));
    }

    // Sort
    if ($sort) {
      $users = $this->sortUsers($users, $sort);
    }

    // Get total and slice as required
    if ($limit !== null) {
      $users = array_slice($users, $offset, $limit, true);
    }

    // Return
    return $users;
  }

  /**
   *  Get users Like
   *
   *  @access public
   *  @param int Offset
   *  @param int Limit
   *  @param string Sort
   *  @param string Search term
   *  @return array User IDs
   */
  public function getUsersLike($offset = null, $limit = null, $sort = null, $search = null,&$total)
  {
    // Fewer cache entries
    if ($search) {
      $search = strtolower($search);
    }
    $query = "SELECT u.id, u.username, u.displayname, u.firstname, u.lastname, ui.about
      FROM `users` AS `u`
      LEFT JOIN `userInfo` AS `ui` ON u.id = ui.userId
      WHERE u.username Like '%".$search."%'";

    /**
     * @desc We don't have to get all. They are eaten up all the memory!!!!!
     * @author Leon Zhao
     */
    if($limit !== null) $queryLimit = $query . " LIMIT $offset, $limit";
    //echo $query;

    $this->_db->setQuery($queryLimit);
    $users = $this->_db->loadAssocList('id');

    $total = mysql_query($query);
    $total = mysql_num_rows($total);
    // Get total and slice as required
    if ($limit !== null) {
      $users = array_slice($users, $offset, $limit, true);
    }
    // Return
    return $users;
  }
  /**
   *  Quicksearch
   *
   *  @access private
   *  @param string Search term
   *  @param string Language code
   *  @return array User IDs
   */
  private function _quickSearch($search)
  {
    // Get greppable index
    $cacheKey = 'users_quicksearch';
    $quickSearchIndices = $this->_cache->get($cacheKey);
    if ($quickSearchIndices === false) {

      // Get from database
      $query = 'SELECT u.id, u.username, u.displayname, u.firstname, u.lastname, ui.about
        FROM `users` AS `u`
        LEFT JOIN `userInfo` AS `ui` ON u.id = ui.userId';
      $this->_db->setQuery($query);
      $quickSearchIndices = $this->_db->loadAssocList('id');

      foreach ($quickSearchIndices as &$user) {
        unset($user['id']);
        unset($user['userId']);

        if ($user['about']) {
          $user['about'] = Text::filterCommonWords($user['about']);
          $user['about'] = implode(' ', array_unique($user['about']));
          $user['about'] = Text::trim($user['about'], 50);
        }
      }
      unset($user);

      // Set in cache
      $this->_cache->set($cacheKey, $quickSearchIndices, 0, null, array('compress' => true));
    }

    // Do quicksesarch
    $users = Utility::quickSearch($search, $quickSearchIndices);

    // Return
    return $users;
  }

  /**
   *  Get full user details and add to user IDs
   *
   *  @access public
   *  @param array
   */
  public function addDetails(&$users)
  {
    if (!empty($users)) {
      $users = array_flip($users);
      foreach ($users as $userId => &$user) {
        $user = $this->getUser($userId);
      }
      unset($user);
    }
  }

  /**
   *  Sort a list of users
   *
   *  @access public
   *  @param array User IDs
   *  @param string Sort
   *  @return array User IDs
   */
  public function sortUsers($users, $sort = 'username_asc')
  {
    // Empty
    if (empty($users)) {
      return $users;
    }

    // Generate sort index
    switch ($sort) {
    case 'username_asc':
      $index = $this->_getSortIndex('username', Utility::SORT_ASC);
      break;

    case 'username_desc':
      $index = $this->_getSortIndex('username', Utility::SORT_DESC);
      break;

    case 'latest':
      $index = $this->_getSortIndex('date', Utility::SORT_DESC);
      break;

    case 'active':
      $index = $this->_getSortIndex('recipes', Utility::SORT_DESC);
      break;

    case 'relevance':
    default:
      $index = null;
      break;
    }

    // Pair up with those we're interested in
    if ($index) {
      $users = array_intersect_key($index, array_flip($users));

      // Use IDs only
      $users = array_keys($users);
      $users = array_combine($users, $users);
    }

    // Return
    return $users;
  }

  /**
   *  Load sorting index
   *
   *  @access protected
   *  @param string Criteria
   *  @param int Direction
   *  @return array User ID => Relative statistic (only used for sorting)
   */
  protected function _getSortIndex($criteria, $direction)
  {
    // Get index, sorted in ascending order
    switch ($criteria) {
    case 'username':
      $cacheKey = 'users_sortable_username';
      $index = $this->_cache->get($cacheKey);
      if ($index === false) {
        $query = 'SELECT u.id, u.username
          FROM `users` AS `u`
          ORDER BY u.username ASC';
        $this->_db->setQuery($query);
        $index = $this->_db->loadResultAssocArray('id', 'username');
        // Set in cache, deplete daily
        //$expiry = strtotime('4AM +1 day EST') - time();
        //$this->_cache->set($cacheKey, $index, $expiry, null, array('compress' => true));
        $this->_cache->set($cacheKey, $index, 3600, null, array('compress' => true));
      }
      $sorted = Utility::SORT_ASC;
      break;

    case 'date':
      $cacheKey = 'users_sortable_date';
      $index = $this->_cache->get($cacheKey);
      if ($index === false) {
        $query = 'SELECT ui.userId, UNIX_TIMESTAMP(ui.joined) AS `date`
          FROM `userInfo` AS `ui`
          ORDER BY `date` DESC';
        $this->_db->setQuery($query);
        $index = $this->_db->loadResultAssocArray('userId', 'date');

        // We need to save as much space as possible... SHAVE SHAVE SHAVE
        $query = 'SELECT UNIX_TIMESTAMP(ui.joined) AS `oldest`, AVG(UNIX_TIMESTAMP(ui.joined)) AS `average`
          FROM `userInfo` AS `ui`
          WHERE ui.joined != 0
          ORDER BY ui.joined ASC';
        $this->_db->setQuery($query, 0, 1);
        $specialDates = $this->_db->loadAssoc();
        $specialDates['average'] = round($specialDates['average']); // Doesn't need to be accurate at all, just a benchmark (rounded so that we store with fewer decimal places)

        foreach ($index as &$date) {
          $date = max($specialDates['oldest'], $date) - $specialDates['average'];           // *Relatively normalised* unix timestamps are smaller than datetimes
        }
        unset($date);

        $this->_cache->set($cacheKey, $index, 3600, null, array('compress' => true));
      }
      $sorted = Utility::SORT_DESC;
      break;

    case 'recipes':
      $cacheKey = 'users_sortable_recipes';
      $index = $this->_cache->get($cacheKey);
      if ($index === false) {
        $query = 'SELECT u.id, COUNT(a.id) AS `submissions`
          FROM `users` AS `u`
          LEFT JOIN `articles` AS `a` ON u.id = a.authorId
          AND a.type = "recipe"
          ORDER BY `submissions` DESC';
        $this->_db->setQuery($query);
        $index = $this->_db->loadResultAssocArray('id', 'submissions');

        // Ints are smaller than strings
        foreach ($index as &$recipes) {
          $recipes = (int) $recipes;
        }
        unset($recipes);

        $this->_cache->set($cacheKey, $index, 3600, null, array('compress' => true));
      }
      $sorted = Utility::SORT_DESC;
      break;
    }

    // Flip, if necessary
    if ($direction != $sorted) {
      $index = array_reverse($index, true);
    }

    // Return
    return $index;
  }

  /*
   * for facebook login only
   */
  public function faceBookLogin($userId)
  {
    Session::set('UserID', $userId);

    // Set last login time
    $query = 'UPDATE users SET lastLoggedin=NOW()+48400 WHERE id='.(int)$userId;
    $this->_db->setQuery($query);
    $this->_db->query();

    return true;
  }
}

?>
