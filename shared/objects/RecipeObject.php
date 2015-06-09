<?

/**
 *	Articles of type articleType = 'dailydeal' - NEEDS TO BE CHANGED TO articleType = 'recipe'.
 */
class RecipeObject extends ItemObject{

	public function __construct($id)
	{
		parent::__construct((int) $id, 'recipe');

		// There ain't no author for daily deals
		$this->author = null;
	}

	public function getType($format)
	{
		switch($format) {
			case 'single': return 'quick recipe'; break;
			case 'plural': return 'quick recipes'; break;
		}
	}

}

?>