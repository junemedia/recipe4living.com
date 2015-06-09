<?

/**
 *	Articles of type articleType = 'quicktip'
 */
class QuicktipObject extends ItemObject{

	public function __construct($id){
		
		parent::__construct((int) $id, 'quicktip');
		
	}
	
	public function getType($format){
		switch($format) {
			case 'single': return 'quick tip'; break;
			case 'plural': return 'quick tips'; break;
		}
	}
	
}

?>