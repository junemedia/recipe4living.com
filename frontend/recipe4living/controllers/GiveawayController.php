<?php

/**
 * Giveaway Controller
 *
 * @package BluApplication
 * @subpackage FrontendControllers
 */
class Recipe4livingGiveawayController extends ClientFrontendController
{
	/**
	 *	Constructor
	 *
	 *	@access public
	 *	@param array Arguments
	 */
	public function __construct($args)
	{
		parent::__construct($args);

		// Store base URL
		$this->_baseUrl = '/'.implode('/', $this->_args);
		
		$arg = end($this->_args);
		$urlSuffix = BluApplication::getSetting('urlSuffix', 'htm');
		if (strtolower(substr($arg,0, -(strlen($urlSuffix) + 1))) == 'thankyou')
		{
			$this->_doc->setTitle('Contest - Thank you for entering. Good luck!');
			$this->thankyou();
		}

	}

	/**
	 *	View a user's profile
	 *
	 *	@access public
	 */
	public function view()
	{ 
		
	}


/**
	 *	Thank you page
	 *
	 *	@access public
	 */
	protected function thankyou()
	{
		// Display
		// Load template
		Template::set('giveaway', true);
		include(BLUPATH_TEMPLATES.'/static/giveaway_thanks.php');
	}

	/**
	 *	Left navigation
	 *
	 *	@access public
	 *	@param array Links
	 */
	public function leftnav(array $links = array())
	{
		return parent::leftnav(array_merge($this->_getRecipeCategoryLinks(), $links));
	}

	

}

?>
