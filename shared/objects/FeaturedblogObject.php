<?

class FeaturedblogObject extends BlogObject{

	/**
	 *	This is the relevant record from 'wp_blogs' table.
	 */
	protected $_wpdata;

	/**
	 *	This is the table name for posts for this blog. Used for convenience.
	 */
	protected $_wppoststable;

	/**
	 *	@args (int) id: the blog id used in 'blogs' table.
	 */
	function __construct($id){

		$this->_type = 'featuredBlog';
		$this->id = (int) $id;
		$this->_cacheObjectID = $this->_type.'_'.$this->id;
		
		parent::__construct();

		/* Build object */
		$query = 'SELECT *
			FROM `blogs`
			WHERE `blogID`='.$this->id;
		$this->_buildObject($query);

	}

	/**
	 *	@return a FeaturedblogpostObject object.
	 */
	public function getPost($id){

		//wordpress blog id
		$wpid = $this->_data->blogHosted == 999 ? $this->_data->blogHosted : $this->_wpdata->blog_id;

		//get an instance of the post of this blog, with this id
		try {
			return BluApplication::getObject('featuredblogpost', $wpid, $id);
		} catch (NoDataException $exception){
			return null;
		}

	}

	/**
	 *	Gets latest posts. Required by abstract BlogObject parent class.
	 *
	 *	@return (mixed) FeaturedblogpostObject(s).
	 */
	public function getLatestPosts($offset = null, $limit = null){
		$order = '`post_date` DESC';

		//get post ids here
		$query = "SELECT `ID`
			FROM `".$this->_wppoststable."`
			WHERE post_status = 'publish'
			ORDER BY ".$order;

		//spit out
		return $this->_queryAndWrap($query, $offset, $limit);
	}

	/**
	 *	Gets most commented posts. Required by abstract BlogObject parent class.
	 *
	 *	@return (mixed) FeaturedblogpostObject(s).
	 */
	public function getPopularPosts($offset, $limit){
		$order = '`comment_count` DESC';

		//get post ids here
		$query = "SELECT `ID`
			FROM `".$this->_wppoststable."`
			ORDER BY ".$order;

		//spit out
		return $this->_queryAndWrap($query, (int)$offset, (int)$limit);
	}

	/**
	 *	The number of posts from this blog.
	 */
	public function getPostCount(){

		$query = "SELECT COUNT(*)
			FROM `".$this->_wppoststable."`";

		$this->_db->setQuery($query);
		return (int)$this->_db->loadResult();

	}





	###							PRIVATE CONVENIENCE FUNCTIONS BELOW 							###

	/**
	 *	All variables that get used must be declared here.
	 */
	protected function _setVariables(){
		parent::_setVariables();
		$this->_wpdata = $this->_getWordPressData();
		$this->_wppoststable = $this->_data->blogHosted==999 ? 'mainwp_posts' : (isset($this->_wpdata->blog_id) ? 'wp_'.$this->_wpdata->blog_id.'_posts' : null);		//used solely because Nataly has a blog on a separate install of Wordpress.
		$this->url = $this->_getPath();
		$this->blogImage = $this->_data->blogImage;
		return $this;
	}

	/**
	 *	Gets the blog title. Required by abstract BlogObject parent class.
	 */
	protected function _getTitle(){
		return isset($this->_data->blogTitle)?$this->_data->blogTitle:null;
	}

	/**
	 *	Gets the owner of the blog.
	 *
	 *	@return the PersonObject object.
	 */
	protected function _getAuthor(){

		//get an instance of the owner of this blog, using their member id
		$personModel = BluApplication::getModel('person');
		return $personModel->getPerson(array('member'=>$this->_data->blogOwner));

	}

	/**
	 *	Get a description for the blog.	NEEDS TO BE DONE.
	 */
	protected function _getDescription(){

	}

	/**
	 *	Gets the stuff from the relevant Wordpress table.
	 *	N.B. we should use $this->_wpdata->blog_id in general (rather than $this->_data->blogHosted), but at this stage we obviously don't have it yet....
	 */
	private function _getWordPressData(){
		$this->_db->setQuery('SELECT * FROM `wp_blogs` WHERE `blog_id`='.(int)$this->_data->blogHosted, 0, 1);
		$record = $this->_db->loadAssoc();

		return Utility::toObject($record);
	}

	/**
	 *	Get the URL path for the blog.
	 */
	private function _getPath(){
		return isset($this->_wpdata->path) ? $this->_wpdata->path : null;
	}

	/**
	 *	Same as wrapItems [see below], but returns single BlogpostObject, instead of an array of BlogpostObjects.
	 */
	private function _wrapPost(array $recordset){
		$item = null;
		if (Utility::is_loopable($recordset)){
			$record = $recordset[0];
			$item = $this->getPost($record['ID']);
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
