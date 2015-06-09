<?

class MemberphotoObject extends PhotoObject implements CommentsInterface {

	/**
	 *	Build the Member photo object.
	 */
	public function __construct($id){

		/* DB and Cache */
		parent::__construct();

		$this->id = (int) $id;
		$this->_cacheObjectID = 'memberphoto_' . $this->id;

		/* Build object */
		$query = "SELECT *
			FROM `images` AS `i`
			WHERE i.imageID = " . $this->id . "
				AND i.imageOwner > 0";
		$this->_buildObject($query);

	}

	/**
	 *	Required by CommentsInterface.
	 */
	public function getComments(){

		/* Query */
		$query = 'SELECT * FROM `comments` WHERE `commentType` = "userphoto" AND `commentTypeObjectId` = '. $this->id;
		$comments = $this->_fetch($query, $this->_cacheObjectID . '_comments', null, null, false);

		/* Parse */
		$commentsModel = $this->getModel('comments');
		if(Utility::is_loopable($comments)){
			foreach($comments as &$comment){
				$comment = $commentsModel->getComment($comment['commentID'], 'userphoto');
			}
		}
		return $comments;

	}

	/**
	 *	Required by CommentsInterface
	 */
	public function getCommentCount(){
		$comments = $this->getComments();
		return count($comments);
	}

	/**
	 *	Required by CommentsInterface
	 */
	public function addComment(array $args){

		/* Append (overwrite) item ID, and comment type. */
		$args['commentTypeObjectId'] = $this->id;
		$args['commentType'] = 'userphoto';

		/* Delegate to Comments model */
		$commentsModel = $this->getModel('comments');
		$commentID = $commentsModel->addComment($args);

		// Add alert
		$alertsModel = BluApplication::getModel('alerts');
		$alertId = $alertsModel->createAlert('photocomment', array(
			'photoId' => $this->id
		), $args['commentOwner']);
		$alertsModel->applyAlert($alertId, $this->author->userid);

		/* Return */
		return $commentID;
	}


	###							PRIVATE CONVENIENCE FUNCTIONS							###

	/**
	 *	Overrides PhotoObject method.
	 */
	protected function _setVariables(){
		parent::_setVariables();
		$this->description = $this->_getDescription();
		$this->date = $this->_getDate();
		$this->author = $this->_getAuthor();
		return $this;
	}

	/**
	 *	Data
	 */
	protected function _getTitle(){
		return isset($this->_data->title) ? ($this->_data->title ? $this->_data->title : '[Untitled]') : null;
	}
	protected function _getImage(){
		return isset($this->_data->url) ? $this->_data->url : null;
	}
	private function _getDescription(){
		return isset($this->_data->description) ? $this->_data->description : null;
	}
	private function _getDate(){
		return isset($this->_data->uploadDate) ? Utility::formatDate($this->_data->uploadDate) : null;
	}
	private function _getAuthor(){
		if (!isset($this->_data->imageOwner) || !(int) $this->_data->imageOwner){ return null; }
		$personModel = BluApplication::getModel('person');
		return $personModel->getPerson(array('member' => (int) $this->_data->imageOwner));
	}

}

?>