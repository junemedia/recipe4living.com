<?

/**
 *	This is a blog post. Could be a member blog. Could be a WordPress blog. But the important thing is you know what you can do with them, by using the abstract function names defined below.
 */
abstract class BlogpostObject extends BluObject implements CommentsInterface {
	
	/**
	 *	This holds the blog type.
	 */
	protected $_type;
	
	/**
	 *	Set required variables.
	 */
	protected function _setVariables(){
		$this->title = $this->_getTitle();
		$this->author = $this->_getAuthor();
		$this->body = $this->_getBody();
		$this->date = $this->_getDate();
		$this->abridgedBody = $this->_getAbridgedBody();
		return $this;
	}
	
	abstract protected function _getTitle();
	abstract protected function _getAuthor();
	abstract protected function _getBody();
	abstract protected function _getDate();
	
	/**
	 *	@return (String) Get blurb.
	 */
	private function _getAbridgedBody(){
		return isset($this->body) && strlen($this->body) > 0 ? Text::trim($this->body, 110) : null;
	}
	
	/**
	 *	Get the blog that this blog post belongs to.
	 */
	abstract public function getBlog();
	
	/**
	 *	Increase the number of views.
	 */
	abstract public function increaseViews();
	
}


?>