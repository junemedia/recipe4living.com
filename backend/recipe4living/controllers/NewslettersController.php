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
    echo "Please select a newsletter from the dropdown.";
  }

  public function daily() {
    $this->_newsletter = 'daily';
    $target = $this->_args[0];

    // check for a campaign id
    if (ctype_digit($target)) {
      $this->_editCampaign($target);
    }

    // new campaign
    else if ($target === 'new') {
      $this->_createCampaign();
    }

    // if no args, then show list of upcoming campaigns
    else if ($target === NULL) {
      $this->_listCampaigns();
    }
  }

  /**
   *  List newsletter campaigns
   *
   *  @access protected
   *
   */
  protected function _listCampaigns() {
    // Get model
    $newslettersModel = BluApplication::getModel('newsletters');

    $campaigns = $newslettersModel->getCampaigns($this->_newsletter);

    // Load template
    include(BLUPATH_TEMPLATES.'/newsletters/listCampaigns.php');

  }


  /**
   *  Create a new campaign
   *
   *  @access protected
   *
   */
  protected function _createCampaign() {
    $this->_campaign = array(
      'id' => 0,
      'newsletter' => $this->_newsletter,
      'campaign' => '',
      'subject' => ''
    );

    include(BLUPATH_TEMPLATES.'/newsletters/'.$this->_campaign['newsletter'].'.php');
  }


  /**
   *  Edit/update items for a campaign
   *
   *  @access protected
   *
   */
  protected function _editCampaign($newsletterCampaignId) {
    // Get model
    $newslettersModel = BluApplication::getModel('newsletters');
    $this->_campaign = $newslettersModel->getCampaign((int)$newsletterCampaignId);

    // view/edit newsletter items
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
      $items = $newslettersModel->getItems($newsletterCampaignId);
      include(BLUPATH_TEMPLATES.'/newsletters/'.$this->_campaign['newsletter'].'.php');
    }

    // handle form submission
    else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $campaignData = array(
        'id' => Request::getInt('newsletterCampaignId'),
        'newsletter' => Request::getString('newsletter'),
        'campaign' => Request::getString('date'),
        'subject' => Request::getString('subject'),
        'items' => array(
          Request::getString('feature'),
          Request::getString('mwl1'),
          Request::getString('mwl2'),
          Request::getString('mwl3'),
          Request::getString('mwl4')
        )
      );

      if ($campaignData['id'] === 0) {
        $newId = $newslettersModel->createCampaign($campaignData);
        if ($newId) {
          $this->_campaign = $newslettersModel->getCampaign((int)$newId);
        }
        $success = !!$this->_campaign;
      }

      else {
        $success = $newslettersModel->updateCampaign($newsletterCampaignId, $campaignData);
      }

      if ($success) {
        header('Location: '.SITEURL.'/newsletters/'.$this->_campaign['newsletter']);
      }
      else {
        Messages::addMessage('There was a problem, please go back and try again.', 'error');
      }
    }
  }
}
