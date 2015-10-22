<?php

/**
 *	Articles
 */
class WorkitmomArticlesController extends WorkitmomItemsController {
	
	/**
	 *	Required by ItemsController.
	 */
	protected function _getType($key = null){
		$type = array(
			'key' => 'article',
			'singular' => 'Article',
			'plural' => 'Articles'
		);
		return Utility::multi_array_get($type, $key, $type);
	}
	
}

?>