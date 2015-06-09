<?

/**
 *	Articles of type articleType = 'lifesaver'
 */
class LifesaverObject extends ItemObject{

	public function __construct($id){
		
		parent::__construct((int) $id, 'lifesaver');
		
	}
	
	public function getType($format){
		switch($format) {
			case 'single': return 'lifesaver'; break;
			case 'plural': return 'lifesavers'; break;
		}
	}

	/**
	 *	Overrides ItemObject.
	 *
	 *	The difference is it doesn't check if the user has previously voted on the lifesaver.
	 */
	public function addRating($rating, PersonObject $rater){
	
		/* Parse rating */
		$rating = (int) $rating;
		
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
	
}

?>