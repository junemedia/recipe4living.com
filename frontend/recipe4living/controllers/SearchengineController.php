<?php

/**
 *	Search engine controller
 *
 *	@package BluApplication
 *	@subpackage FrontendControllers
 */
class Recipe4livingSearchengineController extends SearchengineController
{
	/**
	 *	Google thingy
	 *
	 *	@access public
	 */
	public function google()
	{
		$this->_doc->setFormat('raw');
		include(BLUPATH_TEMPLATES.'/searchengine/google.html');
	}
}

?>
