<?

/**
 *	Articles of type articleType = 'question'
 */
class QuestionObject extends ItemObject {

	public function __construct($id){
		parent::__construct((int) $id, 'question');
	}

	public function getType($format){
		switch($format) {
			case 'single': return 'question'; break;
			case 'plural': return 'questions'; break;
		}
	}

}

?>