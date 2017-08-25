<?php

/**
 *  Newsletters model
 *
 *  @package BluApplication
 *  @subpackage SharedModels
 */
class ClientNewslettersModel extends BluModel {

  /**
   * Campaign data
   *
   * @var array Campaign data
   */
  protected $_campaign = null;

  /**
   *  Get campaigns
   *
   *  @access public
   *  @param string newsletter
   *  @return array campaigns
   */
  public function getCampaigns($newsletter, $ascending=true) {

    $select = "SELECT *
               FROM `newsletterCampaign`
               WHERE `newsletter` = '$newsletter'";

    if ($ascending) {
      $and = "`campaign` >= CURDATE()";
      $order = "ASC";
    }
    else {
      $and = "`campaign` < CURDATE()";
      $order = "DESC";

    }

    $sql = "$select AND $and ORDER BY `campaign` $order";

    $this->_db->setQuery($sql);
    $campaigns = $this->_db->loadAssocList('id');
    return $campaigns;
  }

  /**
   *  Get campaign
   *
   *  @access public
   *  @param int id
   *  @return mixed array if exists, else false
   */
  public function getCampaign($id) {
    $sql = "SELECT *
            FROM `newsletterCampaign`
            WHERE `id` = $id
            LIMIT 1";
    $this->_db->setQuery($sql);
    $result = $this->_db->loadAssocList('id');

    // grab first result (should be only result)
    $this->_campaign = current($result);
    // don't try to get items if campaign doesn't exist
    if ($this->_campaign) {
      $this->_campaign['items'] = $this->_getNewsletterItems();
    }
    return $this->_campaign;
  }


  /**
   *  Get campaign items
   *
   *  @access public
   *  @param int id
   *  @return array items
   */
  protected function _getNewsletterItems() {
    $sql = "SELECT *
            FROM `newsletterItem`
            WHERE `newsletterCampaignId` = {$this->_campaign['id']}
            ORDER BY `order` ASC";
    $this->_db->setQuery($sql);
    $items = $this->_db->loadAssocList('order');

    return $items;
  }

  /**
   *  Create a campaign
   */
  public function createCampaign($campaignData) {
    // create the campaign
    $sql = "INSERT INTO `newsletterCampaign` (`id`, `newsletter`, `campaign`, `subject`)
            VALUES (null, '{$campaignData['newsletter']}', '{$campaignData['campaign']}', '')";
		$this->_db->setQuery($sql);
		if (!$this->_db->query()) {
			return false;
		}
	  $campaignId = $this->_db->getInsertID();

    // add campaign items
    foreach ($campaignData['items'] as $order => $targetUrl) {
      $targetUrl = mysql_real_escape_string($targetUrl);

      $sql = "INSERT INTO `newsletterItem` (`id`, `newsletterCampaignId`, `targetUrl`, `articleId`, `order`)
              VALUES (NULL, $campaignId, '$targetUrl', '', $order)";

      $this->_db->setQuery($sql);
      if (!$this->_db->query()) {
        return false;
      }
    }

    // set the subject line for the new campaign
    $this->_setSubject($campaignId, $campaignData['subject']);

    return $campaignId;
  }


  /**
   *  Update campaign
   */
  public function updateCampaign($campaignId, $campaignData) {
    $sql = "UPDATE `newsletterCampaign`
            SET `campaign` = '{$campaignData['campaign']}'
            WHERE `id` = $campaignId
            LIMIT 1";
    $this->_db->setQuery($sql);
    if (!$this->_db->query()) {
      return false;
    }


    foreach ($campaignData['items'] as $order => $targetUrl) {
      $targetUrl = mysql_real_escape_string($targetUrl);

      $sql = "UPDATE `newsletterItem`
              SET `targetUrl` = '$targetUrl'
              WHERE `newsletterCampaignId` = ".(int)$campaignId."
              AND   `order` = ".(int)$order."
              LIMIT 1";

      $this->_db->setQuery($sql);
      if (!$this->_db->query()) {
        return false;
      }
    }
    $this->_setSubject($campaignId, $campaignData['subject']);

    return true;
  }


  /**
   *  Delete a campaign
   */
  public function deleteCampaign($campaignId) {
    $sql = "DELETE FROM `newsletterItem`
            WHERE `newsletterCampaignId` = $campaignId";
    $this->_db->setQuery($sql);
    if (!$this->_db->query()) {
      return false;
    }

    $sql = "DELETE from `newsletterCampaign`
            WHERE `id` = $campaignId
            LIMIT 1";
    $this->_db->setQuery($sql);
    if (!$this->_db->query()) {
      return false;
    }


    return true;
  }


  /**
   *  Get article title from url
   */
  protected function _getTitleFromUrl($url) {

    $itemsModel = BluApplication::getModel('items');

    $subject = '';
    if (!empty($url)) {
      $slug = $this->_getSlug($url);
      $articleId = $itemsModel->getItemId($slug);
      $item = $itemsModel->getItem($articleId);
      $subject = $item['title'];
    }
    return $subject;
  }


  /**
   *  Get article title
   */
  protected function _getArticleTitle($campaignId) {
    $campaign = $this->getCampaign($campaignId);
    $title = $this->_getTitleFromUrl($campaign['items'][0]['targetUrl']);
    return $title;
  }


  /**
   *  Set campaign subject
   */
  protected function _setSubject($campaignId, $subject) {
    if ($subject === '') {
      $subject = $this->_getArticleTitle($campaignId);
    }
    $subject = mysql_real_escape_string($subject);

    $sql = "UPDATE `newsletterCampaign`
            SET `subject` = '$subject'
            WHERE `id` = $campaignId
            LIMIT 1";
    $this->_db->setQuery($sql);
    return $this->_db->query();
  }

  /**
   *  Extract the slug from a url
   *
   *  For now I'm assuming that we're getting something matching:
   *      http://www.recipe4living.com/slidearticles/details/this_is_the_slug/1
   *  anything else is basically going to be ignored
   *
   *  @access protected
   *  @param string url
   *  @return string slug
   *
   */
  protected function _getSlug($url) {
    $slug = '';

    $path = parse_url($url, PHP_URL_PATH);
    $path = explode('/', $path);
    // discard the first element which is empty since path has a leading slash
    array_shift($path);

    if ($path[0] === 'slidearticles') {
      if ($path[1] === 'details') {
        $slug = $path[2];
      }
    }

    return $slug;
  }
}
