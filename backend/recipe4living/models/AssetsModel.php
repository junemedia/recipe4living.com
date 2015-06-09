<?php

/**
 *	Assets model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendAssetsModel extends BluModel
{
	private static $_sortField = 'filename';
	private static $_sortAsc = true;
	
	/**
	 *	Get Images
	 *
	 *	@access public
	 *	@return array Images
	 */
	public function getArticleImages($offset = 1, $limit = 10, &$total = NULL, $sort = null, $filterArray = null)
	{
		$where = array();
		if(isset($filterArray['filename'])) {
			$where[] = 'MATCH(`ai`.`filename`,`ai`.`title`,`ai`.`description`,`ai`.`minidescription`) AGAINST ("'. Database::escape($filterArray['filename']) .'")';
		}
		if(isset($filterArray['title'])) {
			$where[] = 'MATCH(`ai`.`filename`,`ai`.`title`,`ai`.`description`,`ai`.`minidescription`) AGAINST ("'. Database::escape($filterArray['title']) .'")';
		}
		if(isset($filterArray['article_title'])) {
			$where[] = 'MATCH(`a`.`title`,`a`.`body`,`a`.`teaser`,`a`.`keywords`,`a`.`slug`) AGAINST ("'. Database::escape($filterArray['article_title']) .'")';
		}
		if($where) {
			$where_string = 'WHERE ' . implode(' AND ', $where);
		}
		else {
			$where_string = '';
		}
		switch($sort) {
			case 'article_asc': $orderBy = 'a.`title` ASC'; break;
			case 'article_desc': $orderBy = 'a.`title` DESC'; break;
			case 'type_asc': $orderBy = 'ai.`type` ASC'; break;
			case 'type_desc': $orderBy = 'ai.`type` DESC'; break;
			case 'title_asc': $orderBy = 'ai.`title` ASC'; break;
			case 'title_desc': $orderBy = 'ai.`title` DESC'; break;
			default: $orderBy = 'ai.`articleId` ASC';
		}
		$query = 'SELECT a.`title` AS articleTitle, ai.`articleId`, ai.`type`, ai.`filename`, ai.`title`,ai.alt
					FROM `articleImages` AS ai
					LEFT JOIN `articles` AS a ON a.id=ai.articleId
					'. $where_string .'
					ORDER BY '.$orderBy.', ai.sequence ASC';
		$this->_db->setQuery($query, $offset, $limit, true);
		$articleImages = $this->_db->loadAssocList();
		$total = $this->_db->getFoundRows();
		return $articleImages;
	}

	public function getArticleImageDetails($filename, $articleId)
	{
		$query = 'SELECT ai.`articleId`, ai.`type`, ai.`filename`, ai.`title`, ai.`description`, ai.`minidescription`, ai.`sequence`,ai.alt
					FROM `articleImages` AS ai
					WHERE ai.filename="'.$this->_db->escape($filename).'" AND ai.articleId='.(int)$articleId;
		$this->_db->setQuery($query);
		$articleImage = $this->_db->loadAssoc();
		return $articleImage;
	}

	public function deleteArticleImage($filename, $articleId)
	{
		$articleImage = $this->getArticleImageDetails($filename, $articleId);
		if(!$articleImage) {
			return false;
		}
		// delete database entry
		$query = 'DELETE FROM `articleImages`
					WHERE filename="'.$this->_db->escape($filename).'" AND articleId='.(int)$articleId;
		$this->_db->setQuery($query);
		$this->_db->query();
		// process inline image
		if($articleImage['type']=='inline') {
			$itemsModel = BluApplication::getModel('items');
			if($article = $itemsModel->getItem($articleImage['articleId'])) {
				$body = $article['body'];
				$images = Utility::parseImageTags($body);
				//echo htmlentities($body);
				//var_dump($images);
				foreach($images as $image) {
					if(strpos($image['filePath'],$filename)!==false) {
						$body = str_replace($image['img'],'',$body);
					}
				}
				//echo htmlentities($body);
				//var_dump($body != $article['body']);
				if($body != $article['body']) {
					$query = 'UPDATE articles SET body="'.$this->_db->escape($body).'" WHERE id='.(int)$article['id'];
					//echo $query;
					$this->_db->setQuery($query);
					$this->_db->query();
				}
				if($articleImage['sequence']>0) {
					$query = 'UPDATE articleImages SET sequence=sequence-1 WHERE sequence>'.(int)$articleImage['sequence'];
					//echo $query;
					$this->_db->setQuery($query);
					$this->_db->query();
				}
				//die;
			}
		}
		// remove file from filesystem
		unlink(BLUPATH_ASSETS.'/itemimages/'.$filename);
		// clear cache
		$this->_cache->delete('item_'.$articleImage['articleId']);
		return true;
	}
	
	public function getFileDetails($files,$dir,$folder,$baseFolder,$searchFilename) {
		$dirFiles = array();
		for($i=0;$i<count($files);$i++) {
			if($files[$i]!='.' && ($folder!=$baseFolder || $files[$i]!='..')) {
				if(empty($searchFilename) || strpos($files[$i],$searchFilename)!==false) {
					$fileInfo = array();
					$fileInfo['filename'] = $files[$i];
					if(is_file($dir.$files[$i])) {
						$fileInfo['type'] = 'file';
						$stat = stat($dir . $files[$i]);
						$fileInfo['size'] = $stat['size'];
						$fileInfo['date'] = $stat['atime'];
						$fileInfo['displayName'] = $fileInfo['filename'];
					}
					elseif(is_dir($dir.$files[$i])) {
						$fileInfo['type'] = 'directory';
						$fileInfo['displayName'] = strtoupper($fileInfo['filename']);
					}
					if(isset($fileInfo['type'])) {
						$dirFiles[] = $fileInfo;
					}
				}
			}
		}
		return $dirFiles;
	}
	
	public function sortFiles($dirFiles,$sortField,$sortAsc) {
		// sort files
		self::$_sortField = $sortField=='filename' ? 'displayName' : $sortField;
		self::$_sortAsc = $sortAsc == 'true' ? true : false ;
		usort($dirFiles,array(__class__,'_sortFiles'));
		return $dirFiles;
	}
	
	private static function _sortFiles($a,$b) {
		$sortField = self::$_sortField;
		$sortAsc = self::$_sortAsc;
		if(!isset($a[$sortField]) && $a['type']=='directory' && !isset($b[$sortField]) && $b['type']=='directory') {
			// always order directories by name
			$sortField = 'filename';
			$sortAsc = 'asc';
		}
		if(!isset($a[$sortField]) && $a['type']=='directory') {
			return -1;
		}
		if(!isset($b[$sortField]) && $b['type']=='directory') {
			return 1;
		}
		if($a[$sortField] == $b[$sortField]) {
			return 0;
		}
		return ($a[$sortField] < $b[$sortField]) ? ($sortAsc ? -1 : 1) : ($sortAsc ? 1 : -1);
	}

}

?>
