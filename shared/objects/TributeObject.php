<?

/**
 *	Articles of type articleType = 'tribute'
 */
class TributeObject extends ItemObject{

	public function __construct($id){
		
		parent::__construct((int) $id, 'tribute');
		
	}
	
}

?>