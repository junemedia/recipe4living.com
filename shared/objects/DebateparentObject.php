<?

/**
 *	Articles of type articleType = 'debateparent'
 */
class DebateparentObject extends ItemObject{

	public function __construct($id){
		
		parent::__construct((int) $id, 'debateparent');
		
	}
	
	public function getType($format){
		switch($format) {
			case 'single': return 'debate'; break;
			case 'plural': return 'debates'; break;
		}
	}
	
	/**
	 *	Extend ItemObject method definition.
	 */
	protected function _setVariables(){
		parent::_setVariables();
		$this->debatechildren = $this->_getDebateChildren();
		return $this;
	}
	
	/**
	 *	Returns the debate children.
	 *
	 *	@return array.
	 */
	private function _getDebateChildren(){
		
		return array(
			BluApplication::getObject('debatechild', $this->_data->articleChild1),
			BluApplication::getObject('debatechild', $this->_data->articleChild2)
		);		
		
	}
	
}

?>