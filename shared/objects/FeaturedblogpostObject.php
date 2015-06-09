<?


class FeaturedBlogpostObject extends BlogpostObject {
	
	/**
	 *	Composite key.
	 */
	public $blogid;
	public $postid;

	/**
	 *	Gets a blog post.
	 *
	 *	@args blogid: the blog id used in wordpress.
	 *	@args postid: take a guess.
	 */
	function __construct($blogid, $postid){
		
		parent::__construct();
		
		$this->_type = 'featuredBlogpost';
		$this->blogid = (int) $blogid;
		$this->postid = (int) $postid;
		$this->_cacheObjectID = 'featuredblogpost_'.$this->blogid.'_'.$this->postid;
		$this->_wppoststable = $this->blogid==999 ? 'mainwp_posts' : 'wp_'.$this->blogid.'_posts';
		$this->_wpcommentstable = $this->blogid==999 ? 'mainwp_comments' : 'wp_'.$this->blogid.'_comments';
		
		/* Build object */
		$query = "SELECT * 
			FROM `".$this->_wppoststable."` 
			WHERE `ID`= ".$this->postid;
		$this->_buildObject($query);
		
	}

	/**
	 *	Required by CommentsInterface.
	 */
	public function getComments(){
		
		if (!isset($this->comments)){
			
			/* Get */
			$query = "SELECT `comment_ID` AS `id`
				FROM `" . $this->_wpcommentstable . "`
				WHERE `comment_post_ID` = " . $this->postid . "
					AND `comment_approved` = 1";
			try {
				$records = $this->_fetch($query, null, null, null, false);
			} catch (NoDataException $exception){
				$records = array();
			}
			
			/* Format */
			$this->comments = array();
			foreach($records as $record){
				try { $this->comments[] = BluApplication::getObject('featuredblogpostcomment', $this->blogid, $record['id']); } 
				catch (NoDataException $exception){}
			}
			
		}
		
		/* Return */
		return $this->comments;
		
	}
	
	/**
	 *	Required by CommentsInterface.
	 */
	public function getCommentCount(){		
		return count($this->getComments());		
	}	
	
	/**
	 *	Required by CommentsInterface
	 */
	public function addComment(array $args){
		// Don't do anything here. Featured blogs are handled by Wordpress.
		return null;
	}
	
	/**
	 *	Required by BlogpostObject.
	 */
	public function getBlog(){
		return BluApplication::getObject('featuredblog', $this->blogid);
	}
	
	/**
	 *	Required by BlogpostObject.
	 */
	public function increaseViews(){
		// Increase views in Wordpress? No no no no no...
		return null;
	}
	
	
	
	###							PRIVATE CONVENIENCE FUNCTIONS BELOW							###
	
	
	/**
	 *	Publicly available variables need to be defined here.
	 */
	protected function _setVariables(){
		parent::_setVariables();
		$this->url = $this->_getUrl();
		return $this;
	}
	
	/**
	 *	Get title of post.
	 */
	protected function _getTitle(){
		return isset($this->_data->post_title)?$this->_data->post_title:null;
	}
	
	/**
	 *	Get author of post.
	 *
	 *	@return (PersonObject) the author.
	 */
	protected function _getAuthor(){
		//BIG BODGE COMING UP!!!
		//Because of the structure of the current database, we cannot obtain the user id (nor content creator id) of the author of the POST, but we can for the author of the BLOG. 
		//Assuming the BLOG owner will do most of the posting  (happens 99% of the time), the following will be correct:
		
		$personModel = BluApplication::getModel('person');
		
		//if Nataly, return her immediately:
		if ($this->blogid == 999){
			return $personModel->getPerson(array('contentcreator' => 65));
		}
		
		// get blog owner of blog.
		$query = "SELECT `blogOwner`
			FROM `blogs`
			WHERE `blogHosted` = ".$this->blogid;
		$this->_db->setQuery($query, 0, 1);
		
		$blogownerid = (int)$this->_db->loadResult();
		return $blogownerid > 0 ? $personModel->getPerson(array('member' => $blogownerid)) : null;
		
	}
	
	/**
	 *	Get body content.
	 */
	protected function _getBody(){
		return isset($this->_data->post_content) ? $this->_data->post_content : null;
	}
	
	/**
	 *	Get date of post.
	 */
	protected function _getDate(){
		return isset($this->_data->post_date) ? Utility::formatDate($this->_data->post_date) : null;
	}
	
	/**
	 *	Get the absolute URL to the post.
	 */
	private function _getUrl(){
		return isset($this->_data->guid) ? $this->_data->guid : null;
	}
	
}

?>
