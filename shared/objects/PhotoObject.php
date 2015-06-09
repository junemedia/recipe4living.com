<?

/**
 *	This is a photo. 'Nuff said.
 */
abstract class PhotoObject extends BluObject{
	
	/**
	 *	Set required variables.
	 */ 
	protected function _setVariables(){
		$this->title = $this->_getTitle();
		$this->image = $this->_getImage();
		return $this;
	}
	
	abstract protected function _getTitle();
	abstract protected function _getImage();
	
}



?>