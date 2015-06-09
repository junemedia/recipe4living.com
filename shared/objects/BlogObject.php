<?

abstract class BlogObject extends BluObject{

	/**
	 *	This holds the blog type.
	 */
	protected $_type;

	/**
	 *	Get text.
	 */
	public function getType($format){
		switch($format) {
			case 'single': return 'blog'; break;
			case 'plural': return 'blogs'; break;
		}
	}

	/**
	 *	Get a single post from this blog.
	 *
	 *	@return BlogpostObject object.
	 */
	abstract public function getPost($id);

	/**
	 *	Get the latest however-many posts.
	 *
	 *	@args (int) offset
	 *	@args (int) limit: the number of posts we want to return. If limit is 1, return single BlogpostObject, otherwise return array of them.
	 *	@return (Array of) BlogpostObject object(s).
	 */
	abstract public function getLatestPosts($offset = null, $limit = null);

	/**
	 *	Get the most-commented-on posts.
	 *
	 *	@args [same as above]
	 */
	abstract public function getPopularPosts($offset, $limit);

	/**
	 *	Get the number of posts from this blog.
	 *
	 *	@return int.
	 */
	abstract public function getPostCount();


	/**
	 *	Set required variables
	 */
	protected function _setVariables(){
		$this->title = $this->_getTitle();
		$this->description = $this->_getDescription();
		$this->author = $this->_getAuthor();
		return $this;
	}

	abstract protected function _getTitle();
	abstract protected function _getDescription();
	abstract protected function _getAuthor();

	/**
	 *	Convenience functions
	 */
	public function getLatestPost(){ return $this->getLatestPosts(0, 1); }
	public function getPopularPost(){ return $this->getPopularPosts(0, 1); }

}


?>
