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
  public function getDetails($id) {
    $sql = "SELECT *
            FROM `newsletterCampaign`
            WHERE `id` = $id";
    $this->_db->setQuery($sql);
    $details = $this->_db->loadAssocList('id');

    // return single array item
    return current($details);
  }



  /**
   *  Get campaign items
   *
   *  @access public
   *  @param int id
   *  @return array items
   */
  public function getItems($id) {
    $sql = "SELECT *
            FROM `newsletterItem`
            WHERE `newsletterCampaignId` = $id
            ORDER BY `order` ASC";
    $this->_db->setQuery($sql);
    $items = $this->_db->loadAssocList('order');

    return $items;
  }


  /**
   *  Update campaign items
   */
  public function updateCampaignItems($campaignId, $items) {
    foreach ($items as $order => $targetUrl) {
      $sql = "INSERT INTO `newsletterItem`
              SET `newsletterCampaignId` = ".(int)$campaignId.",
                  `targetUrl` = '$targetUrl',
                  `order` = ".(int)$order."
              ON DUPLICATE KEY UPDATE
                  `targetUrl` = '$targetUrl'";

      $this->_db->setQuery($sql);
      if (!$this->_db->query()) {
        return false;
      }
    }
    return true;
  }
}
