<?php

/**
 *	Routing model
 *
 *	@package BluApplication
 *	@subpackage SharedModels
 */
class RoutesModel extends BluModel
{
	/**
	 *	Routes to use
	 *
	 *	@access protected
	 *	@var array
	 */
	protected $_routes = array();

	/**
	 *	Constructor
	 *
	 *	@access public
	 */
	public function __construct()
	{
		// Parent
		parent::__construct();

		// Load up routes
		$this->_loadRoutes();
	}

	/**
	 *	Load routes mapping into object variable.
	 *
	 *	@access protected
	 */
	protected function _loadRoutes()
	{
		$this->_routes['/^\/login\/?$/'] = '/account/login';
	}

	/**
	 *	Perform all internal redirects
	 *
	 *	@access public
	 *	@param string Original URI
	 *	@param bool Re-routed or not
	 *	@return string Mapped URI
	 */
	public function route($originalUri, &$routed = false)
	{
		// Prepare
		$mappedUri = $originalUri;

		// Do google webmaster
		$mappedUri = $this->_routeGoogleWebmaster($mappedUri);

		// Do internal routes
		if (!empty($this->_routes)) {
			$mappedUri = preg_replace(array_keys($this->_routes), $this->_routes, $mappedUri);
			$mappedUri = preg_replace('/(\/){2,}/', '/', $mappedUri);
		}

		// Return
		$routed = $originalUri == $mappedUri;
		return $mappedUri;
	}

	/**
	 *	Google webmaster tools: route check file to index controller.
	 *
	 *	@access protected
	 *	@param string Original URI
	 *	@param bool Re-routed or not
	 *	@return string Mapped URI
	 */
	protected function _routeGoogleWebmaster($originalUri, &$routed = false)
	{
		// Prepare
		$mappedUri = $originalUri;

		// Replace
		$googleWebMasterUrl = BluApplication::getSetting('googleWebMasterUrl', null);
		if (($googleWebMasterUrl != null) && ($mappedUri == '/'.$googleWebMasterUrl)) {
			$mappedUri = 'index';
			$routed = true;
		}
		
		// Return
		return $mappedUri;
	}
}

?>
