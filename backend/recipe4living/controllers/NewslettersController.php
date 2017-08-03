<?php

/**
 *  Newsletters controller
 *
 *  @package BluApplication
 *  @subpackage BackendControllers
 */
class Recipe4livingNewslettersController extends ClientBackendController {

  protected $_menuSlug = 'newsletters';

  /**
   * Base url
   *
   * @var string Base url
   */
  protected $_baseUrl = '/newsletters';

  protected $_newsletter;
  protected $_campaign;

  /**
   *  Default view -- display list of upcoming campaigns for a
   *  given newsletter
   *
   *  @access public
   */
  public function view() {
    $this->_newsletter = $this->_args[0];

    if ($this->_newsletter) {
      // Get model
      $newslettersModel = BluApplication::getModel('newsletters');

      $campaigns = $newslettersModel->getCampaigns($this->_newsletter);

      // Load template
      include(BLUPATH_TEMPLATES.'/newsletters/listCampaigns.php');
    }
    else {
      echo "Please select a newsletter from the dropdown.";
    }
  }


  /**
   *  Edit/update items for a campaign
   *
   *  @access public
   *
   */
  public function campaign() {
    $newsletterCampaignId = end($this->_args);

    // Get model
    $newslettersModel = BluApplication::getModel('newsletters');

    $this->_campaign = $newslettersModel->getDetails($newsletterCampaignId);

    // show/change newsletter items
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
      $items = $newslettersModel->getItems($newsletterCampaignId);
      include(BLUPATH_TEMPLATES.'/newsletters/'.$this->_campaign['newsletter'].'.php');
    }

    // handle form submission from the above
    else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $items = array();
      $items[0] = Request::getString('feature');
      $items[1] = Request::getString('mwl1');
      $items[2] = Request::getString('mwl2');
      $items[3] = Request::getString('mwl3');
      $items[4] = Request::getString('mwl4');

      $success = $newslettersModel->updateCampaignItems($newsletterCampaignId, $items);

      if ($success) {
        header('Location: '.SITEURL.'/newsletters/'.$this->_campaign['newsletter']);
      }
      else {
        Messages::addMessage('There was a problem, please go back and try again.', 'error');
      }
    }
  }



}

?>
