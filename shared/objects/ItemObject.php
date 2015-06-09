<?

/**
 *	This class basically holds the data of a single item.
 *
 *	DEFINITION: an 'item' is an object holding data from a single record in the 'article' table.
 *	But,  records are divided into many 'types', (given by the 'articleType' column).
 *
 *	Each type should be constructed using the appropriate child class constructor, i.e. not this class - hence the 'abstract' keyword.
 */
abstract class ItemObject extends BluObject implements CommentsInterface {

	/**
	 *	This holds the type of the item.
	 */
	protected $_type;

	/**
	 *	Get singular and/or plural type
	 */
	abstract public function getType($format);

	/**
	 *	Get the data
	 */
	public function __construct($id, $type){

		/* Database and cache. */
		parent::__construct();

		/* Definitions. */
		$this->id = (int)$id;
		$this->_type = $type;
		$this->_cacheObjectID = $this->_type.'_'.$this->id;

		/* Build object - yes, it checks the ID against the type too. */
		$query = "SELECT *
			FROM `article` AS `a`
			WHERE a.articleID = " . $this->id . "
				AND a.articleType = '" . $this->_type . "'";
		$this->_buildObject($query);

	}

	/**
	 *	Property overloading.
	 */
	public function __isset($var){
		$results = $this->$var;
		return !is_null($results);
	}

	/**
	 *	Property overloading.
	 */
	public function __get($var){
		switch($var){
			/* Text functions */
			case 'abridgedBody':
				return $this->strippedBody ? Text::trim($this->strippedBody, 110, false) : null;
				break;

			case 'strippedBody':
				return $this->body ? strip_tags($this->body) : null;
				break;

			case 'subtitle':
				return isset($this->_data->articleSubTitle) ? strip_tags($this->_data->articleSubTitle) : null;
				break;

			case 'title':
				return isset($this->_data->articleTitle) ? strip_tags($this->_data->articleTitle) : null;
				break;

			/* Date */
			case 'date':
				return isset($this->_data->articleTime) ? Utility::formatDate($this->_data->articleTime) : null;
				break;

			/* Standard data */
			case 'body':
			case 'image':
			case 'teaser':
				$field = 'article' . ucfirst($var);
				return isset($this->_data->$field) ? $this->_data->$field : null;
				break;

			/* Data with different names */
			case 'votes':
				$field = 'articleRatingTimes';
				return isset($this->_data->$field) ? $this->_data->$field : null;
				break;

			case 'xlink':
				$field = 'articleLink';
				return isset($this->_data->$field) ? $this->_data->$field : null;
				break;

			case 'views':
				$field = 'articleViews';
				return isset($this->_data->$field) ? $this->_data->$field : null;
				break;

			case 'live':
				$field = 'articleLive';
				return isset($this->_data->$field) ? $this->_data->$field : null;
				break;

			/* Other */
			case 'type':
				return $this->_type;
				break;

			default;
				return null;
				break;
		}
	}

	/**
	 *	Increase the number of views by one.
	 */
	public function increaseViews(){

		/* update database */
		$specialChanges = array('articleViews' => '`articleViews` + 1');
		$criteria = array('articleID' => $this->_data->articleID);
		$success = $this->_edit('article', array(), $specialChanges, $criteria, $this->_cacheObjectID);
		if ($success <= 0){ return false; }

		/* Update this object */
		$this->_data->articleViews++;

		/* Flush cached object */
		return $success;

	}

	/**
	 *	Add a rating to the item.
	 */
	public function addRating($rating, PersonObject $rater){
	
		/* Parse rating */
		$rating = (int) $rating;
		
		/* Can only vote once, so check if already voted. */
		$query = 'SELECT *
			FROM `articleRatings` AS `ar`
			WHERE ar.articleId = '.$this->id.'
				AND ar.articleRatingUser = '.$rater->userid;
		$voted = Utility::iterable($this->_fetch($query, 'item_'.$this->id.'_rated_by_'.$rater->userid, 0, 1, false));
		if ($voted){
			return false;
		}
		
		/* Add rating */
		$rated = $this->_create('articleRatings', array(
			'articleRatingUser' => $rater->userid,
			'articleId' => $this->id,
			'articleRating' => $rating
		), array(
			'articleRatingTime' => 'NOW()'
		));
		if (!$rated){
			return false;
		}
		
		/* Update item author rating. */
		// Get content creator details
		$query = 'SELECT *
			FROM `article` AS `a`
				LEFT JOIN `contentCreators` AS `cc` ON cc.contentCreatorId = a.articleAuthor
			WHERE a.articleID = '.$this->id;
		$contentCreatorDetails = $this->_fetch($query, null, 0, 1, false);
		
		// Do calculations
		if (Utility::iterable($contentCreatorDetails)){
			
			// Duncan's code... (?)
			$newcontrate=round((($contentCreatorDetails['contentCreatorRating']*$contentCreatorDetails['contentCreatorRatingTimes'])+(20*$rating))/($contentCreatorDetails['contentCreatorRatingTimes']+1));
			$newartrate=round((($contentCreatorDetails['articleRating']*$contentCreatorDetails['articleRatingTimes'])+(20*$rating))/($contentCreatorDetails['articleRatingTimes']+1));
			
			// Update database
			$articleUpdated = $this->_edit('article', array(
				'articleRating' => $newartrate
			), array(
				'articleRatingTimes' => '`articleRatingTimes` + 1'
			), array(
				'articleId' => $this->id
			), $this->_cacheObjectId);
			
			// Update content creator
			$contentCreatorUpdated = $this->_edit('contentCreators', array(
				'contentCreatorRating' => $newcontrate
			), array(
				'contentCreatorRatingTimes' => '`contentCreatorRatingTimes` + 1'
			), array(
				'contentCreatorId' => $this->author->contentcreatorid
			));
			if ($contentCreatorUpdated){
				$this->author->flushCached('contentcreator');
			}
			
		}
		
		/* Finish */
		return true;
	}


	/**
	 * 	Required by CommentsInterface interface.
	 *
	 *	@return array of ItemcommentObject objects, ordered by most recent.
	 */
	public function getComments() {

		if (!isset($this->comments)){

			/* Get the comment IDs from DB. */
			$query = "SELECT `commentID` AS `id`
				FROM `comments` AS `c`
				WHERE c.commentTypeObjectId = " . $this->id . "
					AND c.commentType = 'article'
					AND c.commentDeleted != 1
				ORDER BY c.commentTime DESC";
			try {
				$records = $this->_fetch($query, null, null, null, false);
			} catch(NoDataException $exception){
				$records = array();
			}

			/* Wrap into CommentObject objects. */
			$commentsModel = BluApplication::getModel('comments');
			$this->comments = array();
			foreach($records as $record){
				if ($comment = $commentsModel->getComment((int) $record['id'], 'item')){ $this->comments[] = $comment; }
			}

		}

		/* spit out. */
		return $this->comments;

	}

	/**
	 *	Required by CommentsInterface interface.
	 */
	public function getCommentCount(){
		return count($this->getComments());
	}

	/**
	 *	Required by CommentsInterface.
	 */
	public function addComment(array $args){

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

		/* Return */
		return $commentID;
	}

	/**
	 *	Get related items.
	 */
	public function getRelated($offset = null, $limit = null){

		/* Set parameters */
		$offset = (int) $offset + 1;		// Bodgy, relies on SearchModel::findItems giving this same item as the first result.
		$limit = (int) $limit;

		/* Check cache */
		$cacheKey = 'item_' . $this->id . '_relateditems_' . $offset . '_' . $limit;
		$records = $this->_cache->get($cacheKey);
		if ($records === false){

			/* Build search criteria */
			$searchCriteria = $this->tags;
			array_unshift($searchCriteria, $this->title);

			/* Get models */
			$searchModel = BluApplication::getModel('search');

			/* Get records */
			$records = $searchModel->findItems($searchCriteria, $this->_type, $offset, $limit);

			/* Set cache */
			$this->_cache->set($cacheKey, $records);

		}

		/* Get ItemObjects - don't retrieve ItemObjects from two cache levels in. */
		$itemsModel = BluApplication::getModel('items');
		$relatedItems = array();
		if (Utility::iterable($records)){
			foreach($records as $record){
				
				// Parse link.
				$id = (int) preg_replace('/(.*)\/([0-9]+)/', '\\2', $record['thingLink']);
				
				// Check the item is not the same as the current one.
				if ($id == $this->id){
					continue;
				}
				
				// Get item.
				if ($item = $itemsModel->getItem($id)){
					$relatedItems[] = $item;
				}
				
			}
		}

		/* Return */
		return $relatedItems;

	}

	/**
	 *	Get raw date
	 */
	public function getRawDate(){
		return isset($this->_data->articleTime) ? $this->_data->articleTime : null;
	}

	/**
	 *	Get category
	 */
	public function getCategory(){

		/* Build query */
		$query = 'SELECT *
			FROM `articleCategories` AS `acs`
				LEFT JOIN `articleCategory` `ac` ON acs.articleCategoryID = ac.categoryId
			WHERE ac.articleId = ' . $this->id;
		$record = $this->_fetch($query, $this->_cacheObjectID . '_category', 0, 1, false);
		if (!Utility::is_loopable($record)){ return null; }
		return $record['articleCategoryName'];

	}






	###							PRIVATE CONVENIENCE FUNCTIONS							###

	/**
	 *	All variables that get used must be declared here.
	 */
	protected function _setVariables(){
		$this->author = $this->_getAuthor();
		$this->rating = $this->_getRating();
		$this->tags = $this->_getTags();
		return $this;
	}

	/**
	 *	Get the author of the item (Person object)
	 */
	private function _getAuthor(){
		$personModel = BluApplication::getModel('person');
		return $personModel->getPerson(array('contentcreator' => $this->_data->articleAuthor));
	}

	/**
	 *	Get the average user rating and the rating given by the user, if they've given one
	 */
	protected function _getRating(){
		
		/* Get article rating. */
		$rating = array();
		$rating['average'] = isset($this->_data->articleRating) ? $this->_data->articleRating : null;
		$rating['user'] = null;
		
		/* Append current user's latest personal rating. */
		if ($user = BluApplication::getUser()) {
			$query = 'SELECT ar.articleRating 
				FROM `articleRatings` AS `ar`
				WHERE ar.articleId = "'.$this->id.'" 
					AND ar.articleRatingUser = "'.$user->userid.'"
				ORDER BY articleRatingTime DESC';
			$record = $this->_fetch($query, 'itemrating_' . $this->id . '_' . $user->userid, 0, 1, false);
			if (Utility::iterable($record)){
				$rating['user'] = array_pop($record);
			}
		}
		
		/* Return */
		return $rating;
		
	}

	/**
	 *	Get a full list of tags associated with this item.
	 */
	private function _getTags(){

		/* Prepare */
		$tags_cacheObjectID = $this->_cacheObjectID . '_tags';
		$query = "SELECT t.tagName AS `tag`
			FROM `tags` AS `t`
				LEFT JOIN `articleTags` `at` ON at.tagId = t.tagId
			WHERE at.articleId = ".$this->id;

		/* Get */
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
	 *	Apply a category to an item.
	 */
	public function applyCategory($category) {

		/* Validate category. */
		$itemsModel = BluApplication::getModel('items');
		$category = $itemsModel->validateCategory($category, $this->_type);
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
	public function applyTags(array $tags)
	{
		$metaModel = BluApplication::getModel('meta');
		foreach($tags as $tag) {

			// Get tag id
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

}

?>
