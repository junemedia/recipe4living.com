<?php

/**
 *  Copycat Classics newsletter controller
 *
 *  @package BluApplication
 *  @subpackage BackendControllers
 */
class Recipe4livingCopycatController extends Recipe4livingNewslettersController {


  public function __construct($args) {
    parent::__construct($args);
    $this->_newsletter = array(
      'id' => 'copycat',
      'label' => 'Copycat Classics'
    );
    $this->_campaignBlank = array(
      'id'         => 0,
      'newsletter' => $this->_newsletter['id'],
      'campaign'   => '',
      'subject'    => '',
      'items' => array(
        array('targetUrl' => ''),
        array('targetUrl' => ''),
        array('targetUrl' => ''),
        array('targetUrl' => ''),
        array('targetUrl' => '')
      )
    );
  }

  /**
   *  Handle edit form submission
   *
   */
  protected function _handleEditSubmission() {

    // Get model
    $newslettersModel = BluApplication::getModel('newsletters');

    /*
     *  `process` comes from the submit button and is either 'update' or
     *  'save', and determines whether or not to return to the edit form
     */
    $process = strtolower(Request::getString('process'));

    $campaignData = array(
      'id'         => Request::getInt('newsletterCampaignId'),
      'newsletter' => Request::getString('newsletter'),
      'campaign'   => Request::getString('date'),
      'subject'    => Request::getString('subject'),
      'items' => array(
        Request::getString('feature'),
        Request::getString('mwl1'),
        Request::getString('mwl2'),
        Request::getString('mwl3'),
        Request::getString('mwl4')
      )
    );

    // creating a new campaign
    if ($campaignData['id'] === 0) {
      $newId = $newslettersModel->createCampaign($campaignData);
      if ($newId) {
        $this->_campaign = $newslettersModel->getCampaign((int)$newId);
      }
      $success = !!$this->_campaign;
    }

    // updating an existing campaign
    else {
      $success = $newslettersModel->updateCampaign($this->_campaign['id'], $campaignData);
    }

    if ($success) {
      switch($process) {
        case 'update':
          header('Location: '.SITEURL.'/newsletters/'.$this->_campaign['newsletter'].'/'.$this->_campaign['id']);
          break;
        case 'save':
        default:
          header('Location: '.SITEURL.'/newsletters/'.$this->_campaign['newsletter']);
          break;
      }
    }
    else {
      Messages::addMessage('There was a problem, please go back and try again.', 'error');
    }
  }
}

