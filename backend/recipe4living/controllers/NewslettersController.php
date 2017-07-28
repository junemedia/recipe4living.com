<?php

/**
 *  Newsletters controller
 *
 *  @package BluApplication
 *  @subpackage BackendControllers
 */
class Recipe4livingNewslettersController extends ClientBackendController
{

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
	 *	Default view
	 *
	 *	@access public
	 */
	public function view()
	{
		// Load template
		include(BLUPATH_TEMPLATES.'/newsletters/view.php');
	}

  /**
   *  Daily Recipes newsletter
   *
   *  @access public
   */
  public function daily() {
    $this->_newsletter = 'daily';
    // Get model
    $newslettersModel = BluApplication::getModel('newsletters');

    $campaigns = $newslettersModel->getCampaigns($this->_title);

    // Load template
    include(BLUPATH_TEMPLATES.'/newsletters/daily.php');
  }



}

?>
