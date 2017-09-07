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

  protected $_apiUrl = 'http://api.recipe4living.com';

  protected $_newsletter;
  protected $_campaign;

  protected $_campaignBlank;

  protected $_view;


  public function __construct($args) {
    parent::__construct($args);
  }

  /**
   *  Default view
   *
   *  @access public
   */

  public function view() {
    $target = isset($this->_args[0]) ? $this->_args[0]
                                     : NULL;

    // check for a campaign id
    if (ctype_digit($target)) {
      $this->_editCampaign($target);
    }

    // new campaign
    else if ($target === 'new') {
      $this->_createCampaign();
    }

    // delete campaign
    else if ($target === 'delete') {
      if (ctype_digit($this->_args[1])) {
        $this->_deleteCampaign($this->_args[1]);
      }
    }

    // view archive
    else if ($target === 'archive') {
      $this->_listCampaigns(false);
    }

    // if no args, then show list of upcoming campaigns
    else if ($target === NULL) {
      $this->_listCampaigns();
    }
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
      include(BLUPATH_TEMPLATES.'/newsletters/editor.php');
    }

    // handle form submission
    else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $this->_handleEditSubmission();
    }
  }

  /**
   *  Delete a campaign
   */
  protected function _deleteCampaign($campaignId) {
    $newslettersModel = BluApplication::getModel('newsletters');
    $this->_campaign = $newslettersModel->getCampaign((int)$campaignId);

    $success = $newslettersModel->deleteCampaign($campaignId);

    if ($success) {
      header('Location: '.SITEURL.'/newsletters/'.$this->_campaign['newsletter']);
    }
    else {
      Messages::addMessage('There was a problem, unable to delete campaign.', 'error');
    }
  }

  /**
   *  List newsletter campaigns
   *
   *  @access protected
   *
   */
  protected function _listCampaigns($viewUpcoming = true) {
    if ($viewUpcoming) {
      $this->_view = 'upcoming';
      $sortAscending = true;
    }
    else {
      $this->_view = 'archive';
      $sortAscending = false;
    }

    // Get model
    $newslettersModel = BluApplication::getModel('newsletters');

    $campaigns = $newslettersModel->getCampaigns($this->_newsletter['id'], $sortAscending);

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
    $this->_campaign = $this->_campaignBlank;

    include(BLUPATH_TEMPLATES.'/newsletters/editor.php');
  }
}
