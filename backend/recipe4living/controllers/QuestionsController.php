<?php

/**
 *	Questions Controller
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class Recipe4livingQuestionsController extends Recipe4livingArticlesController
{
	/**
	 *	Default view
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_view = 'question_listing';

	/**
	 *	Current item type
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_itemType = 'question';
	
	/**
	 *	Get title when filters are barren.
	 *
	 *	@access protected
	 *	@return string
	 */
	protected function _getEmptyFilterTitle()
	{
		return 'All questions';
	}
}
