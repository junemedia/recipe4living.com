<?php

/**
 *	This represents a link on an Essentials guide page.
 *
 *	It holds a URL, together with a friendly name for it.
 */
class LandingpagelinkObject extends LinkObject{
	
	/**
	 *	@args (array) id: the link id
	 */	
	function __construct($id){
		
		//get the database and cache
		parent::__construct();		
		$this->id = (int) $id;
		$this->_cacheObjectID = 'landingpagelinkobject_'.$this->id;
		
		/* Build object */
		$query = "SELECT * 
			FROM `links` AS `l`
			WHERE l.linkID = " . $this->id;
		$this->_buildObject($query);
		
	}
	
	/**
	 *	Publicly available variables must be defined here.
	 */
	protected function _setVariables(){
		parent::_setVariables();
		return $this;
	}
	
	protected function _getTitle(){
		return isset($this->_data->linkTitle)?$this->_data->linkTitle:null;
	}
	
	protected function _getURL(){
		return isset($this->_data->linkUrl)?$this->_data->linkUrl:null;
	}
	
}

?>