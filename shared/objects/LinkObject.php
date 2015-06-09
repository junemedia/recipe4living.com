<?

/**
 *	This is a link. 'Nuff said.
 */
abstract class LinkObject extends BluObject{
	
	/**
	 *	Set required variables.
	 */ 
	protected function _setVariables(){
		$this->title = $this->_getTitle();
		$this->url = $this->_getURL();
		return $this;
	}
	abstract protected function _getTitle();
	abstract protected function _getURL();		/* This is an ABSOLUTE url. */
	
}



?>