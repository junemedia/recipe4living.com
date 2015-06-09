<?php

/**
 *	This represents a slide.
 *
 *	It is actually an image, stored in the 'images' table, but must have an 'imageOwner' value of -1.
 */
class SlideObject extends PhotoObject{
	
	/**
	 *	@args id: the slide id
	 */	
	public function __construct($id){
		
		//get the database and cache
		parent::__construct();
		
		$this->id = (int) $id;
		$this->_cacheObjectID = 'slide_'.$this->id;
		
		/* Build object */
		$query = "SELECT * 
			FROM `images` AS `i`
			WHERE i.imageID = ".$this->id."
				AND i.imageOwner = -1";
		$this->_buildObject($query);
		
	}
	
	
	###							PRIVATE CONVENIENCE FUNCTIONS							###
	
	/**
	 *	Publicly available variables must be defined here.
	 */
	protected function _setVariables(){
		parent::_setVariables();
		$this->description = $this->_getDescription();
		return $this;
	}
	
	/**
	 *	Return the title.
	 */
	protected function _getTitle(){
		return isset($this->_data->title)?$this->_data->title:null;
	}
	
	/**
	 *	Return the filename.
	 *	Use 'articleImageUrl' for the filename (don't include filepath).
	 */
	protected function _getImage(){
		return isset($this->_data->articleImageUrl)?$this->_data->articleImageUrl:null;		
	}
	
	/**
	 *	Return the description
	 */
	private function _getDescription(){
		return isset($this->_data->description)?$this->_data->description:null;
	}
	
}

?>