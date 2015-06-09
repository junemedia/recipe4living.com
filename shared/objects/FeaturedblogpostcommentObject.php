<?

/**
 *	This pulls from the 'comments' table in the database.
 */
class FeaturedblogpostcommentObject extends CommentObject{
	
	public function __construct($blogid, $commentid){
		
		parent::__construct();
		
		$this->blogid = (int) $blogid;
		$this->commentid = (int) $commentid;
		$this->_wpcommentstable = $this->blogid==999 ? 'mainwp_comments' : 'wp_'.$this->blogid.'_comments';
		$this->_cacheObjectID = 'comment_featuredblogpost_' . $this->blogid . '_' . $this->commentid;
		
		/* Build object */
		$query = "SELECT *
			FROM `".$this->_wpcommentstable."`
			WHERE `comment_ID` = ".$this->commentid."
				AND `comment_approved` = 1";
		$this->_buildObject($query);
		
	}
	
	/**
	 *	Required by CommentObject.
	 *	Simply gets the blog.
	 */
	public function getThing(){
		return BluApplication::getModel('blogs')->getBlog('featured', $this->blogid);
	}
	
	/**
	 *	All publicly available variables must be set here before usage
	 */
	protected function _setVariables(){
		parent::_setVariables();
		return $this;
	}
	
	/**
	 *	@return String.
	 */
	protected function _getBody(){		
		return isset($this->_data->comment_content)?$this->_data->comment_content:null;
	}
	
	/**
	 *	@return String. Yes, string.
	 */
	protected function _getDate(){			
		return isset($this->_data->comment_date)?$this->_data->comment_date:null;		
	}
	
	/**
	 *	Required by CommentObject parent class.
	 */
	public function getAuthorData(){
		$author = new stdClass();
		$author->name = strip_tags($this->_data->comment_author);
		$author->url = strip_tags($this->_data->comment_author_url);
		return $author;
	}

}

?>