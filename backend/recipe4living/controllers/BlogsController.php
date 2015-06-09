<?php

/**
 *	Blog Posts Controller
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class Recipe4livingBlogsController extends Recipe4livingArticlesController
{
	/**
	 *	Default view
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_view = 'blog_listing';

	/**
	 *	Current item type
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_itemType = 'blog';
	
	/** 
	 *	Menu slug
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_menuSlug = 'blog_listing';
	
	/**
	 *	Send confirmation email to item author when setting item live
	 *
	 *	@access protected
	 *	@var bool
	 */
	protected $_sendSubmissionEmail = false;
	
	/**
	 *	Prepend to base URL
	 *
	 *	@access public
	 *	@param array Arguments
	 */
	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->_baseUrl = '/blogs/'.implode('/', $this->_args);
	}
	
	/**
	 *	Get title when filters are barren.
	 *
	 *	@access protected
	 *	@return string
	 */
	protected function _getEmptyFilterTitle()
	{
		return 'All blog posts';
	}
}

?>
