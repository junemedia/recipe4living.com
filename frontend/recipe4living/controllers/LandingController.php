<?php

/**
 *	Generic landings Controller
 *
 *	@package BluApplication
 *	@subpackage FrontendControllers
 */
class Recipe4livingLandingController extends ClientFrontendController
{
	/**
	 *	Dash of fun.
	 *
	 *	@access public
	 */
	public function dash_of_fun()
	{
		// Display variables
		$title = 'A Dash of Fun';
		$description = 'Who says food and cooking have to be all work and no play? This is your section to have a little fun. This is the lighter side of cooking - the side that makes you really enjoy your time in the kitchen. We\'ve got everything on the fun spectrum from toasts and quotes to share with your guests to those oddball food-related holidays.';
		$image = array(
			'filename' => 'hints_and_tips.jpg',
			'title' => 'Hints and Tips'
		);
		$boxSlug = 'dash_of_fun';
		
		// Left nav
		$leftLinks = $this->_getLandingLinks('a_dash_of_fun');
		
		// Set document meta
		$this->_doc->setTitle($title);
		
		// Load template
		include (BLUPATH_TEMPLATES.'/landing/landing.php');
	}
	
	/**
	 *	Thinking healthy.
	 *
	 *	@access public
	 */
	public function thinking_healthy()
	{
		// Display variables
		$title = 'Thinking Healthy';
		$description = 'This is where you can find some great articles dealing with the many aspects of health and fitness, and how they relate to eating and cooking. By making some small changes to what and how you eat, you might see some major changes to your body shape. By making some small changes to your thought process, you might find yourself breathing easier, reducing your dependence on medicines, and sleeping better.';
		$image = array(
			'filename' => 'hints_and_tips.jpg',
			'title' => 'Hints and Tips'
		);
		$boxSlug = 'thinking_healthy';
		
		// Left nav
		$leftLinks = $this->_getLandingLinks('thinking_healthy');
		
		// Set document meta
		$this->_doc->setTitle($title);
		
		// Load template
		include (BLUPATH_TEMPLATES.'/landing/landing.php');
	}
	
	
	/**
	 *	Hints and Tips
	 *
	 *	@access public
	 */
	public function hints_and_tips()
	{
		// Display variables
		$title = 'Hints & Tips';
		$description = 'Cooking can be a challenge. From deciphering the lingo to selecting the ripest fruits and vegetables, not knowing what you are doing can make you want to give up and go out for your meals. Luckily, lots of professional chefs and other everyday people like you have been sharing their tips with us, and we are sharing them with you.';
		$image = array(
			'filename' => 'hints_and_tips.jpg',
			'title' => 'Hints and Tips'
		);
		$boxSlug = 'hints_and_tips';
		
		// Left nav
		$leftLinks = $this->_getLandingLinks('hints_tips');
		
		// Set document meta
		$this->_doc->setTitle($title);
		
		// Load template
		include (BLUPATH_TEMPLATES.'/landing/landing.php');
	}
	
	/**
	 *	Convenience
	 *
	 *	@access private
	 *	@param string Meta group slug
	 *	@return array Links
	 */
	public function _getLandingLinks($slug)
	{
		return parent::_getLandingLinks($slug);
	}
}