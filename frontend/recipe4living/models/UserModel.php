<?php

/**
 *  User model
 *
 *  @package BluApplication
 *  @subpackage FrontendModels
 */
class ClientFrontendUserModel extends ClientUserModel
{
  /**
   *  Get a user from their user Id
   *
   *  @access public
   *  @param int $userId The ID of the user you want to get
   *  @param bool $forceRebuild Defaults to false
   *  @return array|bool The array of user details, or false if user not found
   */
  public function getUser($userId = null, $forceRebuild = false)
  {
    // Get base details
    if (!$user = parent::getUser($userId, $forceRebuild)) {
      return false;
    }

    // Add ranking
    $submissions = isset($user['articles']['recipe']) ? count($user['articles']['recipe']) : 0;

    if ($submissions > 100) {
      $user['ranking'] = array(
        'name' => 'Chef de Cuisine',
        'level' => 5
      );
    } else if ($submissions > 50) {
      $user['ranking'] = array(
        'name' => 'Sous Chef',
        'level' => 4
      );
    } else if ($submissions > 25) {
      $user['ranking'] = array(
        'name' => 'Gastronome',
        'level' => 3
      );
    } else if ($submissions > 10) {
      $user['ranking'] = array(
        'name' => 'Gourmand',
        'level' => 2
      );
    } else {
      $user['ranking'] = array(
        'name' => 'Fry Cook',
        'level' => 1
      );
    }

    // Return
    return $user;
  }

  /**
   *  Get a user's ID from their username
   *
   *  @access public
   *  @param string $username
   *  @return int|bool The ID of the user with this username, or false if not found
   */
  public function getUserId($username)
  {
    $usernameMapping = $this->_getUserUsernameMapping();
    return array_search($username, $usernameMapping);
  }

  /**
   *  Add an item to a users shopping list
   *
   *  @access public
   *  @param int $itemId The ID of the item to save
   *  @param int $userId The ID of the user to save the item for
   *  @param string $comment The comment to attach to this save
   *  @param bool $live Set live immediately?
   *  @return bool Success
   */
  public function addToShoppinglist($itemId, $userId, $comment = null, $live = true)
  {
    return $this->_saveItem($itemId, 'shopping_list', $userId, $comment, $live);
  }

  /**
   *  Add an item to recipe box
   *
   *  @access public
   *  @param int $itemId The ID of the item to save
   *  @param int $userId The ID of the user to save the item for
   *  @param string $comment The comment to attach to this save
   *  @param bool $live Set live immediately?
   *  @return bool Success
   */
  public function addToRecipeBox($itemId, $userId, $comment = null, $live = true)
  {
    return $this->_saveItem($itemId, 'recipebox', $userId, $comment, $live);
  }

  /**
   *  Remove an item from the shopping list
   *
   *  @access public
   *  @param int $itemId The ID of the item to remove
   *  @param int $userId The ID of the User to remove from
   *  @param bool $live Set live immediately?
   *  @return bool Success
   */
  public function removeFromShoppingList($itemId, $userId, $live = true)
  {
    return $this->_removeItem($itemId, 'shopping_list', $userId, $live);
  }

  /**
   *  Remove an item from the recipe box
   *
   *  @access public
   *  @param int $itemId Item ID
   *  @param int $userId User ID
   *  @param bool $live Set live immediately?
   *  @return bool Success
   */
  public function removeFromRecipeBox($itemId, $userId, $live = true)
  {
    return $this->_removeItem($itemId, 'recipebox', $userId, $live);
  }

  /**
   *  Save a note to a recipe.
   *
   *  @access public
   *  @param int $itemId Item ID
   *  @param int $userId User ID
   *  @param string $comment
   *  @param bool $live Set live immediately
   *  @return bool Success
   */
  public function saveRecipeNote($itemId, $userId, $comment = null, $live = true)
  {
    return $this->_saveItem($itemId, 'recipe_note', $userId, $comment, $live);
  }

  /**
   *  Save a cookbook
   *
   *  @access public
   *  @param int Cookbook ID
   *  @param int User ID
   *  @param string Comment
   *  @param bool Set live immediately
   *  @return bool Success
   */
  public function saveCookbook($cookbookId, $userId, $comment = null, $live = true)
  {
    // Update database
    if (!$this->_saveItem($cookbookId, 'cookbook', $userId, $comment, $live)) {
      return false;
    }

    // Update cache
    $this->_cache->delete('itemGroups_sortable_saves');

    // Return
    return true;
  }

  /**
   *  Remove a cookbook save
   *
   *  @access public
   *  @param int Cookbook ID
   *  @param int User ID
   *  @param bool Set live immediately
   *  @return bool Success
   */
  public function removeCookbook($cookbookId, $userId, $live = true)
  {
    // Update database
    if (!$this->_removeItem($cookbookId, 'cookbook', $userId, $live)) {
      return false;
    }

    // Update cache
    $this->_cache->delete('itemGroups_sortable_saves');

    // Return
    return true;
  }

  /**
   *  Save an item against a user
   *
   *  @access protected
   *  @param int $itemId Item ID
   *  @param string $saveType The type of item being saved
   *  @param int $userId User ID
   *  @param string $comment
   *  @param bool $live Set live immediately?
   *  @return bool Success
   */
  protected function _saveItem($itemId, $saveType, $userId, $comment = null, $live = true)
  {
    // Add to database
    $query = 'INSERT INTO `userSaves`
      SET `userId` = '.(int) $userId.',
        `objectType` = "'.$this->_db->escape($saveType).'",
        `objectId` = '.(int) $itemId.',
        `comment` = "'.$this->_db->escape($comment).'"
      ON DUPLICATE KEY UPDATE
        `comment` = "'.$this->_db->escape($comment).'"';
    $this->_db->setQuery($query);
    if (!$this->_db->query()) {
      return false;
    }

    // Flush cache
    if ($live) {
      $this->flushUserSaves($userId);

      // Flush item from memory
      $itemsModel = BluApplication::getModel('items');
      $itemsModel->getItem($itemId, null, true);
    }

    // Return
    return true;
  }

  /**
   *  Un-save an item against a user
   *
   *  @access protected
   *  @param int $itemId Item ID
   *  @param string $saveType Save type
   *  @param int $userId User ID
   *  @param bool $live Set live immediately?
   *  @return bool Success
   */
  protected function _removeItem($itemId, $saveType, $userId, $live = true)
  {
    // Add to database
    $query = 'DELETE FROM `userSaves`
      WHERE `userId` = '.(int) $userId.'
        AND `objectType` = "'.$this->_db->escape($saveType).'"
        AND `objectId` = '.(int) $itemId;
    $this->_db->setQuery($query);
    if (!$this->_db->query()) {
      return false;
    }

    // Flush cache
    if ($live) {
      $this->flushUserSaves($userId);

      // Flush item from memory
      $itemsModel = BluApplication::getModel('items');
      $itemsModel->getItem($itemId, null, true);
    }

    // Return
    return true;
  }

  /**
   *  Attempt to login a user
   *
   *  If successful, the userId is stored in a session variable which is used by getCurrentUser()
   *
   *  @access public
   *  @param string $userId User ID
   *  @param string $password Password
   *  @return bool Success
   */
  public function login($userId, $password)
  {
    // Get user
    if (!$userId) {
      return false;
    }
    $user = $this->getUser($userId);

    // Test against password
    if ($user['password'] != self::_hashPassword($user['username'], $password)) {
      return false;
    }

    // Banned/deleted user?
    if ($user['deleted']) {
      return false;
    }

    // Login
    $this->_login($user['id']);

    // Return
    return true;
  }

  /**
   *  Log out a user
   *
   *  @access public
   *  @return bool Success
   */
  public function logout()
  {
    unset($this->_currentUser);
    Session::delete('UserID');
    $nonExistant = Session::get('UserID');
    return empty($nonExistant);
  }

  /**
   *  Get user ID from an identifier (username or email)
   *
   *  @access public
   *  @param string Identifier
   *  @return int User ID
   */
  public function getUserIdFromIdentifier($identifier)
  {
    // Try against username
    $usernameMapping = $this->_getUserUsernameMapping();
    if (!$userId = array_search($identifier, $usernameMapping)) {

      // Try against email
      $query = 'SELECT u.id
        FROM `users` AS `u`
        WHERE u.email = "'.$this->_db->escape($identifier).'"';
      $this->_db->setQuery($query);
      $userId = $this->_db->loadResult();
    }

    // Return
    return $userId;
  }

  /**
   *  Delete a user
   *
   *  @param int $userId
   *  @param string $reason The reason for the deletion
   *  @return bool Success
   */
  public function deleteUser($userId, $reason = '')
  {
    $query = 'UPDATE  users
          SET   deleted = 1,
              deleteReason = "'.$this->_db->escape($reason).'"
          WHERE   id = '.(int)$userId;
    $this->_db->setQuery($query);
    return $this->_db->loadSuccess();
  }

  /**
   *  Check if username is taken.
   *
   *  @access public
   *  @param string $username Username
   *  @return bool Result of test
   */
  public function isUsernameInUse($username)
  {
    $query = 'SELECT u.id
      FROM `users` AS `u`
      WHERE u.username = "'.$this->_db->escape($username).'"';
    $this->_db->setQuery($query);
    return (bool) $this->_db->loadResult();
  }

  /**
   *  Check if email address is taken.
   *
   *  @access public
   *  @param string $email Email
   *  @return bool Result of test
   */
  public function isEmailInUse($email)
  {
    $query = 'SELECT u.id
      FROM `users` AS `u`
      WHERE u.email = "'.$this->_db->escape($email).'"';
    $this->_db->setQuery($query);
    return (bool) $this->_db->loadResult();
  }

  /**
   *  Add a user, along with their basic details
   *
   *  @access public
   *  @param string $username Username
   *  @param string $password The raw password (unhashed)
   *  @param string $email Email
   *  @param string $firstName First name
   *  @param string $lastName Last name
   *  @param bool Skip cache flushing
   *  @return int|bool The ID of the newly created user, or false on failure
   */
  public function addUser($username, $password, $email, $firstName, $lastName, $skipCache = false)
  {
    $visitorIP = Request::getVisitorIPAddress();

    // For god's sake, we've checked this earlier in the logic, just how do they manage to get around it??!
    // For the umpteenth time, check unique indices
    $query = 'SELECT u.*
      FROM `users` AS `u`
      WHERE u.username = "'.$this->_db->escape($username).'"
        OR u.email = "'.$this->_db->escape($email).'"';
    $this->_db->setQuery($query);
    if ($this->_db->loadAssoc()) {
      return false;
    }

    // Hash password
    $password = self::_hashPassword($username, $password);

    // Add base details
    $query = 'INSERT INTO `users`
      SET `type` = "member",
        `username` = "'.$this->_db->escape($username).'",
        `password` = "'.$this->_db->escape($password).'",
        `email` = "'.$this->_db->escape($email).'",
        `firstname` = "'.$this->_db->escape($firstName).'",
        `lastname` = "'.$this->_db->escape($lastName).'",
        `lastLoggedin` = NOW(),
        `deleted` = 0,
        `rating` = 0';
    $this->_db->setQuery($query);
    if (!$this->_db->query()) {
      return false;
    }
    $userId = $this->_db->getInsertID();

    // Add extra info
    $query = 'INSERT INTO `userInfo`
      SET `userId` = '.(int) $userId.',
        `image` = "",
        `private` = 0,
        `joined` = NOW(),
        `about` = "",
        `favouriteFoods` = "",
        `ipaddr` = "'.$this->_db->escape($visitorIP).'"';
    $this->_db->setQuery($query);
    if (!$this->_db->query()) {
      return false;
    }

    // Renew indices
    if (!$skipCache) {
      $cacheModel = BluApplication::getModel('cache');
      $cacheModel->deleteEntriesLike('users\_');
    }

    // Return
    return $userId;
  }

  /**
   *  Set user's profile as either private or public
   *
   *  @access public
   *  @param int $userId User ID
   *  @param bool $private Whether to make the profile private or not
   *  @param bool $live Set live immediately?
   *  @return bool Success
   */
  public function setPrivate($userId, $private = true, $live = true)
  {
    // Update database
    $query = 'UPDATE `userInfo`
      SET `private` = ' . (int) $private . '
      WHERE `userId` = '.(int) $userId;
    $this->_db->setQuery($query);
    if (!$this->_db->query()) {
      return false;
    }

    // Flush cache
    if ($live) {
      $this->flushUser($userId);
    }

    // Return
    return true;
  }

  /**
   *  Set user's profile as public (convenience function)
   *
   *  @access public
   *  @param int $userId User ID
   *  @param bool $live Set live immediately?
   *  @return bool Success
   */
  public function unsetPrivate($userId, $live = true)
  {
    return $this->setPrivate($userId, false, $live);
  }

  /**
   *  Increment a user's rating
   *
   *  @access public
   *  @param int $userId User ID
   *  @param bool $live Set live immediately?
   *  @return bool Success
   */
  public function incrementRating($userId, $live = true)
  {
    // Update database
    $query = 'UPDATE `users`
      SET `rating` = `rating` + 1
      WHERE `id` = '.(int) $userId;
    $this->_db->setQuery($query);
    if (!$this->_db->query()) {
      return false;
    }

    // Flush cache
    if ($live) {
      $this->flushUserRatings($userId);
    }

    // Return
    return true;
  }

  /**
   *  Filter users by deleted flag
   *
   *  @access public
   *  @param array $users A array of Users indexed by their User ID
   *  @param bool $invert Return deleted users?
   *  @return array Filtered users
   */
  public function filterLiveUsers($users, $invert = false)
  {
    // Get all live users
    static $liveUsers;
    if (empty($liveUsers)) {
      $cacheKey = 'users_live';
      $liveUsers = $this->_cache->get($cacheKey);
      if ($liveUsers === false) {
        $query = 'SELECT u.id
          FROM `users` AS `u`
          WHERE u.deleted = 0';
        $this->_db->setQuery($query);
        $liveUsers = $this->_db->loadResultAssocArray('id', 'id');
        $this->_cache->set($cacheKey, $liveUsers);
      }
    }

    // Intersect (or diff) keys
    $users = $invert ? array_diff_key($users, $liveUsers) : array_intersect_key($users, $liveUsers);

    // Return
    return $users;
  }

  /**
   * Get all articles, recipes, questions, quicktips or guides written by a user
   *
   * @param int|array $user The Id, or details array, of the user to find items for
   * @param string $type The type of items to fetch (Must be either article, recipe, question, quicktip or guide)
   * @param int $offset The offset from which to return from (ie nth item and onwards)
   * @param int $limit The maximum number of items to be returned
   * @return array An array of the items requested
   */
  public function getArticlesForUser($user, $type, $offset = 0, $limit = null)
  {
    // load the user if an Id is provided instead of the user array
    if(!is_array($user)) {
      $user = $this->getUser($user);
    }

    $allowedTypes = array('recipe', 'article', 'question', 'quicktip', 'guide', 'blog');

    if(isset($type) && in_array($type, $allowedTypes) && isset($user['articles'][$type])) {
      $articleIds = array_slice($user['articles'][$type], $offset, $limit);
    } else {
      // type not set/valid
      return false;
    }

    $itemsModel = BluApplication::getModel('items');
    $articles = array();
    foreach($articleIds as $id) {
      $articles[$id] = $itemsModel->getItem($id);
    }
    return $articles;
  }

  /**
   *  Flush user details from memory
   *
   *  @access public
   *  @param int $userId User ID
   *  @return bool Success
   */
  public function flushUser($userId)
  {
    $this->_currentUser = false;
    return parent::flushUser($userId);
  }

  /**
   *  Flush user saves (recipe box etc) from cached memory. Must be done for changes to be visible
   *
   *  @access public
   *  @param int $userId The ID of the user to refresh saves for
   *  @return bool Success
   */
  public function flushUserSaves($userId)
  {
    $this->_currentUser = false;
    return parent::flushUserSaves($userId);
  }

  /**
   *  Get number of users that logged in during the last 7 days
   *
   *  @access public
   *  @return array Quicktips
   */
  public function getLatestUserCount() {
    $cacheKey = 'latestUserCount';
    $newRecipeCount = $this->_cache->get($cacheKey);
    if ($newRecipeCount === false) {
      $query = 'SELECT COUNT(*)
            FROM users AS u
            WHERE DATEDIFF(NOW(),lastLoggedin)<=7 AND u.type="member"';
      $this->_db->setQuery($query);
      $newRecipeCount = $this->_db->loadResult();
      $expiry = mktime(6, 0, 0, date('m') , date('d') + 1, date('Y')) - time(); // cache will expire at 6 o'clock in the morning next day
      $this->_cache->set($cacheKey,$newRecipeCount,$expiry);
    }
    return $newRecipeCount;
  }
  /**
   *  Get users information by id
   *
   *  @access public
   */
  public function getArticleAddUser($userId)
  {
    $sql = "SELECT * FROM users as u where u.id=" . $userId . " AND u.type='admin' LIMIT 0,1";
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result))
    {
      if($row['type'] == 'admin')
      {
        return $row;
      }else{
        return false;
      }
    }
  }
}

?>
