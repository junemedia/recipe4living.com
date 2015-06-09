<?

/**
 *	Articles of type articleType = 'interview'
 */
class InterviewObject extends ItemObject{

	public function __construct($id){
		
		parent::__construct((int) $id, 'interview');
		
	}
	
	public function getType($format){
		switch($format) {
			case 'single': return 'interview'; break;
			case 'plural': return 'interviews'; break;
		}
	}
	
}

?>