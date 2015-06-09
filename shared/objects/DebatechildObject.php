<?

/**
 *	Articles of type articleType = 'debatechild'
 */
class DebatechildObject extends ItemObject{

	public function __construct($id){
		
		parent::__construct((int) $id, 'debatechild');
		
	}
	
	public function getType($format){
		switch($format) {
			case 'single': return 'debate'; break;
			case 'plural': return 'debates'; break;
		}
	}
	
}

?>