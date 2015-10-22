<?php

/**
 *	Slideshows
 */
class WorkitmomSlideshowsController extends WorkitmomItemsController {
	
	/**
	 *	Required by ItemsController.
	 */
	protected function _getType($key = null) {
		$type = array(
			'key' => 'slideshow',
			'singular' => 'Slideshow',
			'plural' => 'Slideshows'
		);
		return Utility::multi_array_get($type, $key, $type);
	}
	
	/**
	 *	Get a slide.
	 *
	 *	N.B. Until we fully migrate to oversight, keep the method public (for hindsight).
	 */
	public function slide(){
		
	}
	
}

?>