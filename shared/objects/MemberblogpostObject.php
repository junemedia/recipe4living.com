<?

/**
 *	This is a member blog post. It is essentially an ItemObject, but instead extends BlogpostObject for the purpose of BlogpostObject functionality.
 */
class MemberblogpostObject extends BlogpostObject{

	/**
	 *	Mimics the ItemObject constructor.
	 */
	function __construct($postid){

		parent::__construct();

		$this->_type = 'memberBlogpost';
		$this->id = (int) $postid;
		$this->_cacheObjectID = 'memberblogpost_'.$this->id;

		/* Build object */
		$query = "SELECT *
			FROM `article` AS `a`
			WHERE a.articleID = ".$this->id;
		$this->_buildObject($query);

	}

	public function getType($format){
		switch($format) {
			case 'single': return 'blog post'; break;
			case 'plural': return 'blog posts'; break;
		}
	}

	/**
	 *	Required by CommentsInterface.
	 */
	public function getComments(){

		if (!isset($this->comments)){

			/* Get */
			$query = "SELECT c.commentID AS `id`
				FROM `comments` AS `c`
				WHERE c.commentTypeObjectId = ".$this->_data->articleID."
					AND c.commentType = 'article'
					AND c.commentDeleted != 1
				ORDER BY c.commentTime DESC";
			try {
				$records = $this->_fetch($query, null, null, null, false);
			} catch (NoDataException $exception){
				$records = array();
			}

			/* Format */
			$commentsModel = BluApplication::getModel('comments');
			$this->comments = array();
			foreach($records as $record){
				if ($comment = $commentsModel->getComment($record['id'], 'memberblogpost')){ $this->comments[] = $comment; }
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
	public function addComment(array $args)
	{
		/* Append (overwrite) item ID, and comment type. */
		$args['commentTypeObjectId'] = $this->id;
		$args['commentType'] = 'article';

		/* Delegate to Comments model */
		$commentsModel = BluApplication::getModel('comments');
		$commentID = $commentsModel->addComment($args);

		/* Increment `article` table record */
		$reserved = array('articleComments' => '`articleComments` + 1');
		$criteria = array('articleID' => $this->id);
		$incrementSuccess = $this->_edit('article', array(), $reserved, $criteria, $this->_cacheObjectID);
		if ($incrementSuccess){ $this->flushCached(); }

		// Add alert
		$alertsModel = BluApplication::getModel('alerts');
		$alertId = $alertsModel->createAlert('notereply', array(
			'itemId' => $this->id,
			'itemTitle' => $this->title
		), $args['commentOwner']);
		$alertsModel->applyAlert($alertId, $this->author->userid);

		/* Return */
		return $commentID;
	}

	/**
	 *	Add a rating to the item. DUNCAN'S CODE.
	 */
	public function addRating($rating, PersonObject $rater)
	{
		$rating = (int) $rating;
		$israted=mysql_query("SELECT articleRatingId
				FROM articleRatings
				WHERE articleId='" . $this->id . "'
				AND articleRatingUser=" . $rater->userid);
		if (@mysql_num_rows($israted)==0) {
			mysql_query("INSERT INTO articleRatings (articleRatingUser,articleId,articleRating,articleRatingTime)
							VALUES	('" . $rater->userid . "', '" . $this->id . "', '" . $rating . "', NOW())");

			$articlerateq=mysql_query("SELECT contentCreatorID, articleRating, articleRatingTimes,
							contentCreatorRating, contentCreatorRatingTimes
							FROM article
							LEFT JOIN contentCreators on contentCreators.contentCreatorId=article.articleAuthor
							WHERE articleID=" . $this->id);
			if (@mysql_num_rows($articlerateq)==0) die();
			$artratea=mysql_fetch_assoc($articlerateq);
			$newcontrate=round((($artratea['contentCreatorRating']*$artratea['contentCreatorRatingTimes'])+(20*$rating))/($artratea['contentCreatorRatingTimes']+1));
			$newartrate=round((($artratea['articleRating']*$artratea['articleRatingTimes'])+(20*$rating))/($artratea['articleRatingTimes']+1));
			mysql_query("UPDATE article SET articleRating='" . $newartrate . "', articleRatingTimes=articleRatingTimes+1
							WHERE articleId='".$this->id."'") or die(mysql_error());
			mysql_query("UPDATE contentCreators SET contentCreatorRating='" . $newcontrate . "', contentCreatorRatingTimes=contentCreatorRatingTimes+1
							WHERE contentCreatorId='".$artratea['contentCreatorID']."'") or die(mysql_error());
			return true;
		} else return false;

	}

	/**
	 *	Required by BlogostObject.
	 *	Get the blog that this blog post belongs to. (Remember, the blog goes by 'articleAuthor' = 'contentcreatorid')
	 */
	public function getBlog(){
		return BluApplication::getModel('blogs')->getBlog('member', $this->author->contentcreatorid);
	}

	/**
	 *	Required by BlogpostObject.
	 *	Same as ItemObject method.
	 */
	public function increaseViews(){

		/* update database */
		$query = "UPDATE `article`
			SET `articleViews` = `articleViews` + 1
			WHERE `articleID` = '".$this->_data->articleID . "'";
		$this->_db->setQuery($query);
		if ($this->_db->loadSuccess() <= 0){ return false; }

		/* Update this object */
		$this->_data->articleViews++;
		$this->views = $this->_getViews();

		/* Flush cached object */
		return $this->_cache->delete($this->_cacheObjectID);

	}





	###							CLONED FROM ITEMOBJECT BECAUSE MEMBERBLOGPOSTOBJECT IS REALLY AN ITEM							###


	/**
	 *	Apply a category to an item.
	 */
	public function applyCategory($category) {

		/* Validate category. */
		$itemsModel = BluApplication::getModel('items');
		$category = $itemsModel->validateCategory($category);
		if (!$category) { return null; }

		/* Apply category. */
		$args = array(
			'articleId' => $this->id,
			'categoryId' => $category->articleCategoryID
		);
		$id = $this->_create('articleCategory', $args);

		/* Return */
		return (bool) $id;

	}

	/**
	 *	Add tags to the item.
	 *
	 *	@args (array) tags.
	 */
	public function applyTags($tags) {

		/* Get model */
		$metaModel = BluApplication::getModel('meta');
		foreach($tags as $tag) {

			// Add tag
			$tagId = $metaModel->addTag($tag);

			/* Add to this item. */
			$args = array(
				'articleId' => $this->id,
				'tagId' => $tagId
			);
			$this->_create('articleTags', $args);

		}

		/* Exit */
		return $this;

	}






	###							PRIVATE CONVENIENCE FUNCTIONS BELOW							###

	/**
	 *	Publicly available variables need to be defined here.
	 */
	protected function _setVariables(){
		parent::_setVariables();
		$this->views = $this->_getViews();
		$this->tags = $this->_getTags();
		$this->rating = $this->_getRating();
		return $this;
	}

	/**
	 *	Get title.
	 */
	protected function _getTitle(){
		return isset($this->_data->articleTitle)?$this->_data->articleTitle:null;
	}

	/**
	 *	Get author
	 *
	 *	@return (PersonObject) the author.
	 */
	protected function _getAuthor(){
		$personModel = $this->getModel('person');
		$criteria = array('contentcreator' => $this->_data->articleAuthor);
		$author = isset($this->_data->articleAuthor) ? $personModel->getPerson($criteria) : null;
		return $author;
	}

	/**
	 *	Get body content
	 */
	protected function _getBody(){
		return isset($this->_data->articleBody) ? $this->_data->articleBody : null;
	}

	/**
	 *	Number of views
	 */
	private function _getViews(){
		return isset($this->_data->articleViews) ? (int) $this->_data->articleViews : null;
	}

	/**
	 *	Get the post date.
	 */
	protected function _getDate(){
		return isset($this->_data->articleTime) ? Utility::formatDate($this->_data->articleTime) : null;
	}

	/**
	 *	Get tags for this post.
	 */
	private function _getTags(){

		/* Prepare */
		$tags_cacheObjectID = $this->_cacheObjectID . '_tags';
		$query = "SELECT t.tagName AS `tag`
			FROM `tags` AS `t`
				LEFT JOIN `articleTags` `at` ON at.tagId = t.tagId
			WHERE at.articleId = " . $this->id;

		/* Get tags */
		$records = $this->_fetch($query, $tags_cacheObjectID, null, null, false);

		/* Format */
		$tags = array();
		foreach($records as $record){
			$tags[] = $record['tag'];
		}

		/* Return */
		return $tags;

	}

	/**
	 *	Get the average user rating and the rating given by the user, if they've given one
	 */
	private function _getRating(){
		if($user = BluApplication::getUser()) {
			$query = 'SELECT articleRating FROM articleRatings WHERE articleId = "'.$this->id.'" AND articleRatingUser = "'.$user->userid.'"';
			$record = $this->_fetch($query, null, 0, 1, false);
			if(Utility::is_loopable($record)) $userrating = array_pop($record);
		}
		return array('average' => isset($this->_data->articleRating) ? $this->_data->articleRating : null,
						'user' => isset($userrating) ? $userrating : null);
	}


}

?>
