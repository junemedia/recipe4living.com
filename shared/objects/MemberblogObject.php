<?

class MemberBlogObject extends BlogObject{

	/**
	 *	@args (int) id: the content creator id of the user who owns the blog.
	 *	Doesn't do much, no direct fetching from the DB, because THERE IS NOTHING TO FETCH.
	 *	To keep consistent, with other BluObject constructors, lets grab the number of posts.
	 */
	function __construct($id){

		$this->_type = 'memberBlog';
		$this->_author = (int) $id;
		$this->_cacheObjectID = $this->_type.'_'.$this->_author;		// Required by abstract parent class, but never gets used because there is nothing to cache.

		parent::__construct();

		/* Build object */
		$query = "SELECT COUNT(*)
			FROM `article` AS `a`
			WHERE a.articleType = 'note'" . $this->_generateSQLVisibility()->where . "
				AND a.articleAuthor = " . $this->_author;
		$this->_buildObject($query);

	}

	/**
	 *	Get a specific post. Mimics the ItemObject constructor - after all, it is basically an ItemObject, but with extra BlogObject functions.
	 *
	 *	@args (int) id: the post id (corresponds to 'articleID' in 'article' table)
	 *	@return MemberblogpostObject object.
	 */
	public function getPost($id){

		//get an instance of the post of this blog, with this id
		try {
			return BluApplication::getObject('memberblogpost', (int)$id);
		} catch(NoDataException $exception){
			return null;
		}
	}

	/**
	 *	Gets latest posts. Required by abstract BlogObject parent class.
	 *
	 *	@return (mixed) MemberblogpostObject(s).
	 */
	public function getLatestPosts($offset = null, $limit = null){

		$visibilitySQL = $this->_generateSQLVisibility();

		//get post ids here
		$query = "SELECT a.articleID
			FROM `article` AS `a`
			WHERE a.articleType = 'note'".$visibilitySQL->where."
				AND a.articleAuthor = ".$this->_author."
			ORDER BY a.articleTime DESC";

		//spit out
		return $this->_queryAndWrap($query, $offset, $limit);
	}

	/**
	 *	Gets most commented posts. Required by abstract BlogObject parent class.
	 *
	 *	@return Array of MemberblogpostObject objects.
	 */
	public function getPopularPosts($offset, $limit){

		$visibilitySQL = $this->_generateSQLVisibility();

		/* Get post ids */
		$query_withcomments = "SELECT a.articleID, COUNT(a.articleID) AS `count`
			FROM `article` AS `a`
				LEFT JOIN `comments` `c` ON a.articleID = c.commentTypeObjectId
			WHERE a.articleType = 'note'".$visibilitySQL->where."
				AND a.articleAuthor = ".$this->_author."
				AND c.commentType = 'article'
				AND c.commentDeleted != 1
			GROUP BY a.articleID";

		$query_nocomments = "SELECT a.articleID, '0' AS `count`
			FROM `article` AS `a`
			WHERE a.articleType = 'note'".$visibilitySQL->where."
				AND a.articleAuthor = ".$this->_author;

		$query_all = "SELECT *
			FROM ((".$query_withcomments.") UNION (".$query_nocomments.")) `u`
			ORDER BY u.count DESC";

		/* spit out */
		return $this->_queryAndWrap($query_all, (int)$offset, (int)$limit);

	}

	/**
	 *	The number of posts from this blog.
	 */
	public function getPostCount(){

		$visibilitySQL = $this->_generateSQLVisibility();

		$query = "SELECT COUNT(*)
			FROM `article` AS `a`
			WHERE a.articleType = 'note'".$visibilitySQL->where."
				AND a.articleAuthor = ".$this->_author;

		$this->_db->setQuery($query);
		return (int)$this->_db->loadResult();

	}



	/**
	 *	Complete clone of ItemsModel->createItem
	 */
	public function addPost($args){

		/* Special arguments */
		$special = array(
			'articleTime' => 'NOW()'
		);

		/* Create item */
		$postID = $this->_create('article', $args, $special);

		/* Get item */
		$post = $this->getPost($postID);

		/* Exit */
		return $post;

	}







	###							PRIVATE CONVENIENCE FUNCTIONS BELOW							###

	/**
	 *	Set variables here.
	 */
	protected function _setVariables(){
		parent::_setVariables();
		return $this;
	}

	/**
	 *	Get the blog name.
	 */
	protected function _getTitle(){
		$author = $this->_getAuthor();
		return isset($author->name) ? $author->name . '\'s Blog' : null;
	}

	/**
	 *	Get the blog owner.
	 *
	 *	@return PersonObject object.
	 */
	protected function _getAuthor(){
		$criteria = array('contentcreator' => $this->_author);
		$personModel = $this->getModel('person');
		return $personModel->getPerson($criteria);
	}

	/**
	 *	Get the description
	 *
	 *	@return string.
	 */
	protected function _getDescription(){
		return isset($this->author->name) ? $this->author->name . ' has ' . $this->getPostCount() . ' post' . Text::pluralise($this->getPostCount()) : '';
	}

	/**
	 *	Whether to show deleted and live items. Determined by whether we are frontend or backend. (Backend sees everything).
	 */
	private function _generateSQLVisibility(){
		$sql = new stdClass();
		$sql->where = SITEEND == 'backend'?"":"
				AND a.articleDeleted != 1
				AND a.articleLive = 1";
		return $sql;
	}

	/**
	 *	Same as wrapItems [see below], but returns single BlogpostObject, instead of an array of BlogpostObjects.
	 */
	private function _wrapPost(array $recordset){
		$item = null;
		if (Utility::is_loopable($recordset)){
			$record = $recordset[0];
			$item = $this->getPost($record['articleID']);
		}
		return $item;
	}

	/**
	 *	If we have an arbitrary recordset (2d array) wrap them into BlogpostObjects.
	 *
	 *	@args (Array) recordset: an array containing records, which are themselves arrays each containing the post ID.
	 *	@return (Array) an array of mixed objects, depending on what type of item each one is.
	 */
	private function _wrapPosts(array $recordset){
		$wrappeditems = array();
		if (Utility::is_loopable($recordset)){
			foreach($recordset as $record){
				$wrappeditems[] = $this->_wrapPost(array($record));
			}
		}
		return $wrappeditems;
	}

	/**
	 *	Convenience function.
	 *
	 *	@args (string) type: what type of BlogObject object(s) you want to return.
	 */
	private function _queryAndWrap($query, $offset, $limit){

		$this->_db->setQuery($query, $offset, $limit);
		$records = $this->_db->loadAssocList();

		//get the wrapped data
		$posts = null;
		if (Utility::is_loopable($records)){
			if ($limit == 1){
				//if we want a single item, don't return the useless array around it.
				$posts = $this->_wrapPost($records);
			} else {
				// if we want more than a single item, return an array.
				$posts = $this->_wrapPosts($records);
			}
		}

		//spit out
		return $posts;

	}

}

?>