<?php

/**
 *	Standard comment that comes from the `comments` table.
 */
abstract class StandardcommentObject extends CommentObject {

	/**
	 *	The comment author.
	 *
	 *	PersonObject
	 */
	protected $_author;
	
	/**
	 *	Standard constructor.
	 */
	public function __construct($id){
		
		// Get the database and cache connection.
		parent::__construct();
		
		/* Set data */
		$this->id = (int)$id;
		if (!$this->_cacheObjectID){
			$this->_cacheObjectID = 'comment_'.$this->id;
		}
		
		/* Build object */
		$query = "SELECT *
			FROM `comments` AS `c`
			WHERE c.commentID = ".$this->id;
		$this->_buildObject($query);
	}
	
	/**
	 *	Property overloading
	 */
	public function __isset($var){
		return !is_null($this->$var);
	}
	
	/**
	 *	Property overloading.
	 */
	public function __get($var){
		switch($var){
			/* Aliases */
			case 'body':
				return $this->commentBody;
				break;
			
			case 'reported':
				return (bool) $this->commentReports;
				break;
				
			case 'thingID':
				return $this->commentTypeObjectId;
				break;
				
			case 'date':
				return $this->commentTime;
				break;
				
			/* Standard data */
			case 'commentBody':
			case 'commentReports':
			case 'commentTypeObjectId':
				return isset($this->_data->$var) ? $this->_data->$var : null;
				break;
			
			/* Date */
			case 'commentTime':
				return isset($this->_data->$var) ? Utility::formatDate($this->_data->$var) : null;
				break;
				
			/* To set */
			case 'author':
				return isset($this->{'_' . $var}) ? $this->{'_' . $var} : $this->{'_get' . ucfirst($var)}();
				break;
				
			/* Unset */
			default:
				return null;
				break;
		}
	}
	
	/**
	 *	Get and return the author.
	 */
	final protected function _getAuthor(){
		
		/* Get author */
		$personModel = $this->getModel('person');
		$author = $personModel->getPerson(array('member' => $this->_data->commentOwner));
		
		/* Set */
		$this->_author = $author;
		
		/* Return */
		return $this->_author;
		
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
	 *	Get the thing commented on.
	 */
	//abstract public function getThing();
	// Uncomment when extending BluObject instead of CommentOBject
	
	/**
	 *	REMOVE THESE when extending BluObject instead of Commnetobject.
	 */
	protected function _setVariables(){}
	protected function _getBody(){}
	protected function _getDate(){}
	public function getAuthorData(){}
	
}

?>