<?php

/**
 *	Recipe4living Adverts model
 *
 *	@package BluApplication
 *	@subpackage FrontendModels
 */
class ClientFrontendAdvertsModel extends FrontendAdvertsModel
{
	
	/**
	 *	Define mappings
	 *
	 *	@access public
	 */
	public function __construct()
	{
		
		// "Website" IDs
		$this->_siteMapping = array(
			'WEBSITE_RIGHT_BANNER_1' => 12,
		);
		
	}
}

?>
