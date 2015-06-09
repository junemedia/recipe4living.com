<?

/**
 *	Articles of type articleType = 'list'
 */
class ListObject extends ItemObject{

	public function __construct($id){
		
		parent::__construct((int) $id, 'list');
		
	}
	
	public function getType($format){
		switch($format) {
			case 'single': return 'checklist'; break;
			case 'plural': return 'checklists'; break;
		}
	}
	
}

?>