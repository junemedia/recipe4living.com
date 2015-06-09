<?

/**
 *	Articles of type articleType = 'article'
 */
class ArticleObject extends ItemObject {

	public function __construct($id){
		parent::__construct((int) $id, 'article');
	}

	public function getType($format){
		switch($format) {
			case 'single': return 'article'; break;
			case 'plural': return 'articles'; break;
		}
	}

}

?>