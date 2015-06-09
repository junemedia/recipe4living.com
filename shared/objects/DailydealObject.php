<?

/**
 *	Articles of type articleType = 'dailydeal'
 */
class DailydealObject extends ItemObject{

	public function __construct($id)
	{
		parent::__construct((int) $id, 'dailydeal');

		// There ain't no author for daily deals
		$this->author = null;
	}

	public function getType($format)
	{
		switch($format) {
			case 'single': return 'daily deal'; break;
			case 'plural': return 'daily deals'; break;
		}
	}

}

?>