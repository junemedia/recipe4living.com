<?

/**
 *	This pulls from the 'comments' table in the database.
 */
class MemberblogpostcommentObject extends CommentObject{
	
	public function __construct($id){
		
		parent::__construct();
		$this->id = (int)$id;
		$this->_cacheObjectID = 'comment_memberblogpost_'.$this->id;
		
		/* Build object */
		$query = "SELECT *
			FROM `comments` AS `c`
			WHERE c.commentID = ".$this->id;
		$this->_buildObject($query);
		
	}
	
	/**
	 *	File a report for the blog post.
	 */
	public function report(PersonObject $reporter){
		$commentsModel = BluApplication::getModel('comments');
		return $commentsModel->reportComment($this, $reporter);
	}
	
	/**
	 *	Required by CommentObject.
	 *	Get the member blog post.
	 */
	public function getThing(){
		return isset($this->_data->commentTypeObjectId) ? BluApplication::getModel('blogs')->getMemberBlogPost((int) $this->_data->commentTypeObjectId) : null;
	}
	
	
	
	
	###						PRIVATE CONVENIENCE FUNCTIONS							###
	
	/**
	 *	All publicly available variables must be set here before usage
	 */
	protected function _setVariables(){
		parent::_setVariables();
		$this->author = $this->_getAuthor();
		$this->reported = $this->_getReported();
		return $this;
	}
	
	/**
	 *	@return String. Yes, string.
	 */
	protected function _getDate(){
		return isset($this->_data->commentTime) ? Utility::formatDate($this->_data->commentTime) : null;		
	}
	
	/**
	 *	@return String.
	 */
	protected function _getBody(){
		return isset($this->_data->commentBody) ? strip_tags($this->_data->commentBody) : null;
	}
	
	/**
	 *	@return PersonObject object.
	 */
	private function _getAuthor(){	
		return BluApplication::getModel('person')->getPerson(array('member' => $this->_data->commentOwner));
	}	
	
	/**
	 *	Required by CommentObject parent class.
	 */
	public function getAuthorData(){
		$author = new stdClass();
		$author->name = isset($this->author->name) ? $this->author->name : null;
		$author->url = isset($this->author->profileURL) ? SITEURL.$this->author->profileURL : null;
		return $author;
	}
	
	/**
	 *	@return boolean.
	 */
	private function _getReported(){
		return isset($this->_data->commentReports) ? (bool) $this->_data->commentReports : null;
	}

}

?>
