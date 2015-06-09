<?

/**
 *	This holds the data for a comment on an item (i.e. article, slideshow etc).
 */	
class ItemcommentObject extends CommentObject{
	
	public function __construct($id){
		
		// Get the database and cache connection.
		parent::__construct();
		$this->id = (int)$id;
		$this->_cacheObjectID = 'comment_item_'.$this->id;
		
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
	 *	Delete a comment (only with enough privileges)
	 */
	public function delete(){
		$commentsModel = BluApplication::getModel('comments');
		$success = $commentsModel->deleteComment($this);
		if ($success) { $this->_cache->delete($this->_cacheObjectID); }
		return $success;
	}
	
	/**
	 *	File a report for this item.
	 */
	public function report(PersonObject $reporter){
		$commentsModel = BluApplication::getModel('comments');
		$success = $commentsModel->reportComment($this, $reporter);
		if ($success) { $this->_cache->delete($this->_cacheObjectID); }
		return $success;
	}
	
	/**
	 *	Required by CommentObject.
	 */
	public function getThing(){
		if (!isset($this->_data->commentTypeObjectId)){ return null; }
		$id = $this->_data->commentTypeObjectId;
		$itemsModel = BluApplication::getModel('items');
		return $itemsModel->getItem($id);
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
		return BluApplication::getModel('person')->getPerson(array('member' => (int)$this->_data->commentOwner));
	}
	
	private function _getReported()
	{
		$query = 'SELECT COUNT(*)
			FROM `reports` AS `r`
			WHERE r.objectType = "comment"
				AND r.objectId = '.(int) $this->id.'
				AND r.status != "resolved"';
		$this->_db->setQuery($query);
		return (bool) $this->_db->loadResult();
	}
	
	
	
}

?>
