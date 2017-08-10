<?php

/**
 *  Newsletters model
 *
 *  @package BluApplication
 *  @subpackage SharedModels
 */
class ClientNewslettersModel extends BluModel {

  /**
   *  Get campaigns
   *
   *  @access public
   *  @param string newsletter
   *  @return array campaigns
   */
  public function getCampaigns($newsletter) {
    $sql = "SELECT *
            FROM `newsletterCampaign`
            WHERE `newsletter` = '$newsletter'
            AND `campaign` >= CURDATE()
            ORDER BY `campaign` ASC";
    $this->_db->setQuery($sql);
    $campaigns = $this->_db->loadAssocList('id');
    return $campaigns;
  }

  /**
   *  Get campaign details
   *
   *  @access public
   *  @param int id
   *  @return array details
   */
  public function getCampaign($id) {
    $sql = "SELECT *
            FROM `newsletterCampaign`
            WHERE `id` = $id
            LIMIT 1";
    $this->_db->setQuery($sql);
    $result = $this->_db->loadAssocList('id');

    // grab first result (should be only result)
    $campaign = current($result);
    $campaign['items'] = $this->_getItems($id);
    return $campaign;
  }


  /**
   *  Get campaign items
   *
   *  @access public
   *  @param int id
   *  @return array items
   */
  protected function _getItems($id) {
    $sql = "SELECT *
            FROM `newsletterItem`
            WHERE `newsletterCampaignId` = $id
            ORDER BY `order` ASC";
    $this->_db->setQuery($sql);
    $items = $this->_db->loadAssocList('order');

    return $items;
  }

  /**
   *  Create a campaign
   */
  public function createCampaign($campaignData) {
    $sql = "INSERT INTO `newsletterCampaign` (`id`, `newsletter`, `campaign`, `subject`)
            VALUES (null, '{$campaignData['newsletter']}', '{$campaignData['campaign']}', '')";
		$this->_db->setQuery($sql);
		if (!$this->_db->query()) {
			return false;
		}
	  $campaignId = $this->_db->getInsertID();

    foreach ($campaignData['items'] as $order => $targetUrl) {
      $sql = "INSERT INTO `newsletterItem` (`id`, `newsletterCampaignId`, `targetUrl`, `articleId`, `order`)
              VALUES (NULL, $campaignId, '$targetUrl', '', $order)";

      $this->_db->setQuery($sql);
      if (!$this->_db->query()) {
        return false;
      }
    }
    return $campaignId;
  }


  /**
   *  Update campaign
   */
  public function updateCampaign($campaignId, $campaignData) {
    foreach ($campaignData['items'] as $order => $targetUrl) {
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

    return true;
  }
}
