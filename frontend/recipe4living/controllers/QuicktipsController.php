<?php

/**
 *	Recipes Controller
 *
 *	@package BluApplication
 *	@subpackage FrontendControllers
 */
class Recipe4livingQuicktipsController extends Recipe4livingArticlesController
{
	/**
	 *	Default view
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_view = 'quicktip_listing';

	/**
	 *	Current item type
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_itemType = 'quicktip';
	
	/**
	 *	Prepend to base URL
	 *
	 *	@access public
	 *	@param array Arguments
	 */
	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->_baseUrl = '/quicktips/'.implode('/', $args);

/*		if ($this->_view == 'quicktip_listing') {
			Template::set(array(
				'rssUrl' => '/rss/'.implode('/', $args),
				'rssTitle' => $this->_getTitle('listingTitle')
			));
		}*/
		
		Template::set('searchType', 'quicktips');
	}
	
	/**
	 *	Get title when filters are barren.
	 *
	 *	@access protected
	 *	@return string
	 */
	protected function _getEmptyFilterTitle()
	{
		return 'All quicktips';
	}
}
