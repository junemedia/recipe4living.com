<?

/**
 *	This holds the data for a comment on a user (Member).
 */	
class UsercommentObject extends CommentObject{
	
	public function __construct($id){
		
		// Get the database and cache connection.
		parent::__construct();
		
		$this->id = (int) $id;
		$this->_cacheObjectID = 'comment_user_' . $this->id;
		
		/* Build object */
		$query = "SELECT *
			FROM `comments` AS `c`
			WHERE c.commentID = ".$this->id;
		$this->_buildObject($query);
		
	}
	
	/**
	 *	Required by abstract parent CommentObject
	 */	
	public function getAuthorData(){
		
		// Get person object from user base.
		$person = $this->author;
		
		// Format and return
		$author = new stdClass();
		$author->url = isset($person->profileURL) ? $person->profileURL : null;
		$author->name = isset($person->name) ? $person->name : null;
		return $author;
		
	}
	
	/**
	 *	Required by CommentObject. 
	 *	Get the PersonObject (not MemberObject). Not the same as $this->author, obviously.
	 */
	public function getThing(){
		return isset($this->_data->commentTypeObjectId) ? BluApplication::getModel('person')->getPerson(array('member' => (int) $this->_data->commentTypeObjectId)) : null;
	}
	
	
	
	
	###							PRIVATE CONVENIENCE FUNCTIONS BELOW							###
	
	protected function _setVariables(){
		parent::_setVariables();
		$this->author = $this->_getAuthor();
		$this->reported = $this->_getReported();
		return $this;
	}
	
	protected function _getBody(){
		return isset($this->_data->commentBody) ? strip_tags($this->_data->commentBody) : null;
	}
	
	protected function _getDate(){
		return isset($this->_data->commentTime) ? Utility::formatDate($this->_data->commentTime) : null;
	}
	
	private function _getAuthor(){
		return isset($this->_data->commentOwner) ? BluApplication::getModel('person')->getPerson(array('member' => (int) $this->_data->commentOwner)) : null;
	}
	
	private function _getReported(){
		return isset($this->_data->commentReports) ? (bool) $this->_data->commentReports : null;
	}
	
	
	
}

?>