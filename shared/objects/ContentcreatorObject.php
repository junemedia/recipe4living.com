<?


class ContentCreatorObject extends BluObject{
	
	/**
	 *	@args (array) id: the content creator id
	 */	
	function __construct($id){
	
		//get the database and cache
		parent::__construct();
		
		$this->id = (int) $id;
		$this->_cacheObjectID = 'contentcreator_' . $this->id;
	
		/* Build object*/
		$query = "SELECT *
			FROM `contentCreators`
			WHERE `contentCreatorID` = " . $this->id;
		$this->_buildObject($query);
		
	}
	
	/**
	 *	Accessor.
	 */
	public function __get($var){
		switch($var){
			/* Aliases */
			case 'fullname':
				return $this->fullName;
				break;
				
			case 'byline':
				return $this->contentCreatorByLine;
				break;
				
			case 'image':
				return $this->contentCreatorImage;
				break;
				
			/* Data */
			case 'contentCreatorByLine':
			case 'contentCreatorImage':
			case 'fullName':
				return isset($this->_data->$var) ? $this->_data->$var : null;
				break;
			
			/* Not allowed */
			default:
				return null;
				break;
		}
	}
	
	/**
	 *	Set the user ID
	 */
	public function setUserID($id){
		$changes = array('contentCreatoruserID' => (int) $id);
		$criteria = array('contentCreatorID' => $this->id);
		return $this->_edit('contentCreators', $changes, array(), $criteria);
	}
	
}

?>
