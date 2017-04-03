<?php

/**
 * BoxOut Model
 *
 * @package BluApplication
 * @subpackage BluModels
 */
class BoxoutModel extends BluModel
{
  /**
   *  Get internal-slug mapping
   *
   *  @access protected
   *  @return array Box slug to ID
   */
  protected function _getBoxSlugMapping()
  {
    static $mapping;
    if (!$mapping) {
      $cacheKey = 'boxSlugMapping';
      $mapping = $this->_cache->get($cacheKey);
      if ($mapping === false) {
        $query = 'SELECT bo.slug, bo.id
                  FROM `boxOut` AS `bo`';
        $this->_db->setQuery($query);
        $mapping = $this->_db->loadResultAssocArray('slug', 'id');
        $this->_cache->set($cacheKey, $mapping);
      }
    }
    return $mapping;
  }

  /**
   *  Get a box through a slug
   *
   *  @access public
   *  @param string Slug
   *  @param array Parameters
   *  @return array Box
   */
  public function getBoxBySlug($slug, array $args = array())
  {
    // Get box ID
    $boxSlugMapping = $this->_getBoxSlugMapping();
    if (!isset($boxSlugMapping[$slug])) {
      return false;
    }

    // Return
    return $this->getBox($boxSlugMapping[$slug], $args);
  }

  /**
   *  Get a box from a content ID
   *
   *  @access public
   *  @param int Box content ID
   *  @return array Box
   */
  public function getBoxFromContent($contentId)
  {
    // Query
    $query = 'SELECT boc.boxId
              FROM `boxOutContent` AS `boc`
              WHERE boc.id = '.(int) $contentId;
    $this->_db->setQuery($query);
    if (!$boxId = $this->_db->loadResult()) {
      return false;
    }

    // Return box
    return $this->getBox($boxId);
  }

  /**
   *  Get a box
   *
   *  @access public
   *  @param int Box ID
   *  @param array Parameters
   *  @param string Site ID
   *  @param string Language code
   *  @return array
   */
  public function getBox($boxId, array $args = array(), $siteId = null, $langCode = null)
  {
    // Get site ID
    if (!$siteId) {
      $siteId = BluApplication::getSetting('siteId');
    }

    // Get language
    if (!$langCode) {
      $language = BluApplication::getLanguage();
      $langCode = $language->getLanguageCode();
    }

    // Get from cache
    $cacheKey = 'box_'.$boxId.'_'.$siteId.'_'.$langCode.'_'.serialize($args);
    $box = $this->_cache->get($cacheKey);

    // Reload for Chicago office only
    if (($_SERVER["REMOTE_ADDR"] == "66.54.186.254")) { $box = false; }

    if ($box === false) {

      // Get base details from database
      $query = 'SELECT bo.*
                FROM `boxOut` AS `bo`
                LEFT JOIN `boxOutSiteMapping` AS `bosm` ON bo.id = bosm.id
                WHERE bo.id = '.(int) $boxId.'
                AND bosm.siteId = "'.$this->_db->escape($siteId).'"';
      $this->_db->setQuery($query);
      $box = $this->_db->loadAssoc();

      // Get box content
      $query = 'SELECT boc.*
                FROM `boxOutContent` AS `boc`
                WHERE boc.boxId = '.(int) $boxId.'
                AND boc.lang = "'.$this->_db->escape($langCode).'"
                ORDER BY boc.sequence,
                         boc.date DESC,
                         boc.id ASC';
      $this->_db->setQuery($query);
      $box['content'] = $this->_db->loadAssocList('id');
      foreach ($box['content'] as &$content) {
        $content['info'] = unserialize($content['info']);
      }
      unset($content);

      // Set template to load, default expiry time (transformable)
      $box['template'] = $box['slug'].'.php';
      $box['expiry'] = 0;

      // Set backend settings
      $box['canAdd'] = true;
      $box['canDelete'] = true;

      // Do pre-cache transformations
      $transformationMethod = '_preTransform'.ucfirst($box['type']);
      if (method_exists($this, $transformationMethod)) {
        $this->$transformationMethod($box, $args);
      }

      // Pull out expiry time, we don't need to store it too
      $expiry = $box['expiry'];
      unset($box['expiry']);

      // Store in cache
      $this->_cache->set($cacheKey, $box, $expiry);
      if($box['slug'] == 'daily_chef'){
        $this->deleteBoxContent($box['content'][0]['id']);
      }

    }

    // Do post-cache transformations
    $transformationMethod = '_postTransform'.ucfirst($box['type']);
    if (method_exists($this, $transformationMethod)) {
      $this->$transformationMethod($box, $args);
    }

    // Return
    return $box;
  }

  /**
   *  Add box details
   *
   *  @access public
   *  @param array Boxes
   */
  public function addDetails(&$boxes)
  {
    if (!empty($boxes)) {
      $boxes = array_flip($boxes);
      foreach ($boxes as $boxId => &$box) {
        $box = $this->getBox($boxId);
      }
      unset($box);
    }
  }

  /**
   *  Featured items - set limit
   *
   *  @access protected
   *  @param array Box
   *  @param array Parameters
   */
  protected function _preTransformFeaturedItems(&$box, array $arguments)
  {
    // Diff out unwanted items
    if (isset($arguments['omit'])) {
      foreach ((array) $arguments['omit'] as $omitId) {
        foreach ($box['content'] as $key => $content) {
          if ($content['info']['item'] == $omitId) {
            unset($box['content'][$key]);
          }
        }
      }
    }

    // Set limit
    if (isset($arguments['limit'])) {
      $box['content'] = array_slice($box['content'], 0, $arguments['limit']);
    }
  }

  /**
   *  Featured items - append item details.
   *
   *  @access protected
   *  @param array Box
   *  @param array Parameters
   */
  protected function _postTransformFeaturedItems(&$box, array $arguments)
  {
    // Prepare
    $box['items'] = array();

    // Get model
    $itemsModel = BluApplication::getModel('items');

    // Expand out
    if (!empty($box['content'])) {

      // Build items.
      foreach ($box['content'] as $content) {

        // Pull out item
        $itemId = $content['info']['item'];
        $box['items'][$itemId] = $itemsModel->getItem($itemId);

        // Overrides
        if (!empty($content['title'])) {
          $box['items'][$itemId]['title'] = $content['title'];
        }
        if (!empty($content['link'])) {
          $box['items'][$itemId]['link'] = $content['link'];
        }
        if (!empty($content['imageName'])) {
          $box['items'][$itemId]['image']['filename'] = $content['imageName'];
        }
      }
    }
    elseif (SITEEND=='frontend' && ($randomItems = $itemsModel->getRandomItems($box['slug'],$arguments['limit']))) {
      foreach($randomItems as $randomItem) {
        $itemId = $randomItem['id'];
        $item = $itemsModel->getItem($itemId);
        $box['items'][$itemId] = $item;
        $box['content'][$itemId]['title'] = $item['title'];
        $box['content'][$itemId]['link'] = $item['link'];
        if(isset($item['image']['filename'])) {
          $box['content'][$itemId]['imageName'] = $item['image']['filename'];
        }
      }
    }
  }

  /**
   *  Featured users - set limit
   *
   *  @access protected
   *  @param array Box
   *  @param array Parameters
   */
  protected function _preTransformFeaturedUsers(&$box, array $arguments)
  {
    // Set limit
    if (isset($arguments['limit'])) {
      $box['content'] = array_slice($box['content'], 0, $arguments['limit']);
    }
  }

  /**
   *  Featured users - append user details.
   *
   *  @access protected
   *  @param array Box
   *  @param array Parameters
   */
  protected function _postTransformFeaturedUsers(&$box, array $arguments)
  {
    if (!empty($box['content'])) {

      // Pull out user IDs
      $box['users'] = array();
      foreach ($box['content'] as $content) {
        $userId = $content['info']['user'];
        $box['users'][$userId] = $userId;
      }

      // Append details
      $userModel = BluApplication::getModel('user');
      foreach ($box['users'] as $userId => &$user) {
        $user = $userModel->getUser($userId);
      }
      unset($user);
    }
  }

  /**
   *  Don't do 'nethin'
   *
   *  @access protected
   *  @param array Box
   *  @param array Parameters
   */
  protected function _preTransformFreetext(&$box, array $arguments)
  {
    // Yawn.
  }

  /**
   *  Don't do 'nethin'
   *
   *  @access protected
   *  @param array Box
   *  @param array Parameters
   */
  protected function _postTransformFreetext(&$box, array $arguments)
  {
    // Yawn.
  }

  /**
   *  Set backend settings
   *
   *  @access protected
   *  @param array Box
   *  @param array Parameters
   */
  protected function _preTransformDailyTip(&$box, array $arguments)
  {
    $box['canAdd'] = false;
    $box['canDelete'] = false;
  }

  /**
   *  Select random tip of the day in case none is set
   *
   *  @access protected
   *  @param array Box
   *  @param array Parameters
   */
  protected function _postTransformDailyTip(&$box, array $arguments)
  {
    if(SITEEND=='frontend') {
      $key = key($box['content']);
      if(empty($box['content'][$key]['title']) && empty($box['content'][$key]['link']) && empty($box['content'][$key]['text'])) {
        $itemsModel = BluApplication::getModel('items');
        $quicktip = $itemsModel->getRandomQuicktip();
        $box['content'][$key]['title'] = (isset($quicktip['section']) ? $quicktip['section'].' - '.$quicktip['title'] : $quicktip['title']);
        $box['content'][$key]['text'] = Text::trim($quicktip['body'],200,false,false,false);
        $fragment = (isset($quicktip['section']) && $quicktip['section']==$quicktip['title'] ? $quicktip['section'] : $quicktip['title']);
        $box['content'][$key]['link'] = SITEURL . '/articles/encyclopedia_of_tips#'.$fragment.';scroll-'.$fragment;
      }
    }
  }

  /**
   *  Group links in a list by link category
   *
   *  @access protected
   *  @param array Box
   *  @param array Parameters
   */
  protected function _postTransformLinkList(&$box, array $arguments)
  {
    $box['list'] = array();
    foreach($box['content'] as $linkId=>$link) {
      $box['list'][$link['info']['linkCategory']][$linkId] = $link;
    }
  }

  /**
   *  Delete a box content entry
   *
   *  @access public
   *  @param int Content ID
   *  @return bool Success
   */
  public function deleteBoxContent($contentId)
  {
    $query = 'DELETE FROM `boxOutContent`
              WHERE `id` = '.(int) $contentId;
    $this->_db->setQuery($query);
    return $this->_db->query();
  }

  /**
   *  Get all content of a box
   *
   *  @access public
   *  @param int Box ID
   *  @return array
   */
  public function getBoxContents($boxId)
  {
    $query = 'SELECT boc.*
              FROM `boxOutContent` AS `boc`
              WHERE boc.boxId = '.(int) $boxId.'
              ORDER BY boc.sequence,
                       boc.date DESC,
                       boc.id ASC';
    $this->_db->setQuery($query);
    $boxContents = $this->_db->loadAssocList('id');
    if (!empty($boxContents)) {
      foreach ($boxContents as &$content) {
        $content['info'] = unserialize($content['info']);
      }
      unset($content);
    }
    return $boxContents;
  }
}
?>
