<?

/**
 *	This holds the data for a comment. The item that the comment is for could be anything, an article, a user's profile, or even a Wordpress blog post.
 */	
abstract class CommentObject extends BluObject{
	
	/**
	 *	Publicly available data.
	 */
	protected function _setVariables(){
		$this->body = $this->_getBody();
		$this->date = $this->_getDate();
		return $this;
	}
	
	abstract protected function _getBody();
	abstract protected function _getDate();
	
	/**
	 *	Return object containing a link (absolute) to the users profile (if profile exists), plus the users display name.
	 *	N.B. This is the bare minimum that a comment should return about its author. However, it can always return more information, if it has the information cf. MemberblogpostcommentObject.
	 */
	abstract public function getAuthorData();
	
	/**
	 *	Gets the thing that this comment was made on.
	 */
	abstract public function getThing();
	
}

?>