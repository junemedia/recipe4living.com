<?php

/**
 *	Member photoalbum - ported from old WIM.
 */
class PhotoalbumObject extends BluObject {

	/**
	 *	The owner of the photoalbum.
	 *
	 *	PersonObject
	 */
	public $author;
	
	/**
	 *	Constructor
	 */
	public function __construct($userid){
		
		/* BluObject constructor */
		parent::__construct();
		
		/* Cache key */
		$this->_cacheObjectID = 'photoalbum_' . $userid;
		
		/* Author */
		$personModel = $this->getModel('person');
		$this->author = $personModel->getPerson(array('member' => (int) $userid));
		
	}
	
	/**
	 *	Get the position of the photo in the photoalbum
	 */
	public function getPosition(MemberphotoObject $photo){
		$ids = array_keys($this->_getIDs());
		$position = array_search($photo->id,$ids);
		return (int) $position;
	}
	
	/**
	 *	Get the photo at a certain position.
	 */
	public function getPhoto($position){
		$ids = Utility::flatten($this->_getIDs());
		if (!array_key_exists($position, $ids)){ return null; }
		
		$id = $ids[$position];
		$photosModel = $this->getModel('photos');
		return $photosModel->getPhoto('member', $id);
	}
	
	/**
	 *	Get all photo IDs.
	 */
	protected function _getIDs($offset = null, $limit = null, &$total = null){
		
		/* For consistency, use PhotosModel::getUserPhotos */
		$photosModel = BluApplication::getModel('photos');
		return $photosModel->getUserPhotos($this->author->userid, $offset, $limit, $total, array());
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function generateMyPhotos($offset){
		if (!$offset) {$offset = 0;}
		$this->images = array();
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `images` WHERE `imageOwner`='".$this->userid."' ORDER BY `imageID` ASC LIMIT ".$offset.", ".$this->maxImages;
		$q = mysql_query($sql);
		$this->totalImages = array_pop(getSingleRowSQLResult("SELECT FOUND_ROWS()"));
		while($r = mysql_fetch_assoc($q)){
			$this->images[] = new photo($r['imageID'], $r['imageOwner'], $r['title'], $r['description'], $r['url'], $r['uploadDate']);
		}
		return $this;
	}
	
	function generateAllPhotos($offset, $sortby){
		if (!$offset) {$offset = 0;}
		if (!$sortby) {$sortby = 'latest';}
		
		switch ($sortby){
			case 'latest': 
				$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `images` WHERE `imageOwner` > 0 ORDER BY `uploadDate` DESC, `imageID` ASC LIMIT ".$offset.", ".$this->maxImages;
				break;
			case 'active': 
				$sql = "SELECT SQL_CALC_FOUND_ROWS *";
				$sql .= " FROM images LEFT JOIN (";
					//get images with comments
					$sql .= " SELECT imageID, count(imageID) AS numComments ";
					$sql .= " FROM `images` LEFT JOIN `comments` ON commentTypeObjectId = imageId";
					$sql .= " WHERE commentType = 'userphoto'";
					$sql .= " GROUP BY imageID";
				$sql .= " UNION";
					//add on images without comments, with a blank zero
					$sql .= " SELECT imageID, '0' as numComments FROM images";
				$sql .= ") `imageNumComments` ON images.imageID = imageNumComments.imageID";
				$sql .= " WHERE `imageOwner` > 0 ";
				$sql .= " GROUP BY images.imageID";
				$sql .= " ORDER BY numComments DESC, images.imageID ASC ";
				$sql .= " LIMIT ".$offset.", ".$this->maxImages;
				break;
		}
		//imageOwner = -1 means that the image is a slideshow image, not a user photo
		//imageOwner = -2 means that the image is for the blog highlight, again not a user photo
		$q = mysql_query($sql);
		$this->userid = null;
		$thsi->images = array();
		$this->totalImages = array_pop(getSingleRowSQLResult("SELECT FOUND_ROWS()"));
		while($r = mysql_fetch_assoc($q)){
			$this->images[] = new photo($r['imageID'], $r['imageOwner'], $r['title'], $r['description'], $r['url'], $r['uploadDate']);
		}
		return $this;
	}
	
	function dumpPhotos(){
		return $this->images;
	}
	
}

?>