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
			'WEBSITE_TOP_NAV' => 1,
			'WEBSITE_LEFT_BANNER_1' => 2,
			'WEBSITE_INLINE_1' => 9,
			'AD_RIGHT1' => 11,	// Website 11 doesn't exist in OpenX...
			'WEBSITE_RIGHT_BANNER_1' => 12,
			'WEBSITE_BOTTOM_NAV' => 19
		);
		
	}
}

?>
