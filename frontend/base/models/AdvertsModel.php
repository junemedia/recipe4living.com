<?php

/**
 *	Adverts model
 *
 *	@package BluApplication
 *	@subpackage FrontendModels
 */
class FrontendAdvertsModel extends BluModel
{
	/**
	 *	Some sort of mapping, which I can't remember what it does.
	 *
	 *	@access protected
	 *	@var array
	 */
	protected $_siteMapping = array();
	
	/**
	 *	Some sort of mapping, which I can't remember what it does.
	 *
	 *	@access protected
	 *	@var array
	 */
	protected $_zoneMapping = array();
	
	/**
	 *	Constructor
	 *
	 *	@access public
	 */
	public function __construct()
	{
		parent::__construct();
		
		if (ADS) {
			
			// Let's try loading the OpenX library once at the start
			include_once(BLUPATH_BASE.'/openads/www/delivery/alocal.php');
		}
	}
	
	/**
	 *	Get an advert
	 *
	 *	Only works for OpenX at the mo.
	 *
	 *	@access public
	 *	@param int Advert ID
	 *	@return array
	 */
	public function getAdvert($id)
	{
		// Say no.
		if (!ADS) {
			return false;
		}
		
		// Get context
		$userModel = BluApplication::getModel('user');
		$source = $userModel->getCurrentUser() ? 'loggedin' : 'loggedout';
		
		// Get ad
		$rawAdvert = $this->_getOpenXAdvert($id, $source);
		$advert = array(
			'content' => $rawAdvert['html']
		);
		
		// Return
		return $advert;
	}
	
	/**
	 *	Get OpenX advert
	 *
	 *	@todo Tie in Campaign ID/Banner ID etc.
	 *
	 *	@access protected
	 *	@param int Zone ID
	 *	@param string Source
	 *	@return array Stuff
	 */
	private function _getOpenXAdvert($zoneId, $source)
	{
		// Defaults
		$what = '';
		$campaignId = 0;
		$bannerId = 0;
		$target = '';
		$withText = 0;
		$context = array();
		$charset = '';
		
		// Get advert
		$raw = view_local($what, $zoneId, $campaignId, $bannerId, $target, $source, $withText, $context, $charset);
		
		// Return
		return $raw;
	}
	
	/**
	 *	Get an advert of a specific type, from a location
	 *
	 *	@access public
	 *	@param string Website slug
	 *	@param string Zone slug
	 *	@return array Advert
	 */
	public function getAdvertByType($website, $zone)
	{
		// Run
		if (!ADS) {
			return false;
		}
		
		// Get Zone ID
		if (!$zoneId = $this->_getZoneId($website, $zone)) {
			return false;
		}
		
		// Get advert
		if (!$advert = $this->getAdvert($zoneId)) {
			return false;
		}
		
		// Add some meta stuff
		$advert['meta'] = '<!-- '.$website.':'.$zoneId.'-->';
		
		// Return
		return $advert;
	}
	
	/**
	 *	Get Zone ID from Type/location combination
	 *
	 *	@access public
	 *	@param string Website slug
	 *	@param string Zone slug
	 *	@return int
	 */
	protected function _getZoneId($website, $zone)
	{
		$zoneId = false;
		
		// Parse type (I don't actually know what this means anymore).
		$website = $this->_siteMapping[$website];
		
		// By type
		if (isset($this->_zoneMapping[$website])) {
			$zoneId = reset($this->_zoneMapping[$website]);
		}
		
		// By type and location
		if (isset($this->_zoneMapping[$website][$zone])) {
			$zoneId = $this->_zoneMapping[$website][$zone];
		}
		
		return $zoneId;
	}
}

?>
