<?php

/**
 *	Article history model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendArticlehistoryModel extends ClientArticlehistoryModel
{
	
	/**
	 *	Get article history list
	 *
	 *	@access public
	 *	@return array Article history list
	 */
	public function getArticleHistoryList($offset = 1, $limit = 10, &$total = NULL, $sort = 'ah.date DESC', $filterArray = null)
	{
		$where = array();
		if(isset($filterArray['articleId'])) {
			$where[] = 'ah.articleId='.$filterArray['articleId'];
		}
		if(isset($filterArray['article_title'])) {
			$where[] = 'MATCH(a.title,a.body,a.teaser,a.keywords,a.slug) AGAINST ("'. Database::escape($filterArray['article_title']) .'")';
		}
		if($where) {
			$where_string = 'WHERE ' . implode(' AND ', $where);
		}
		else {
			$where_string = '';
		}
		switch($sort) {
			case 'article_title_asc': $orderBy = 'a.title ASC, ah.date DESC, id DESC'; break;
			case 'article_title_desc': $orderBy = 'a.title DESC, ah.date DESC, id DESC'; break;
			case 'date_asc': $orderBy = 'ah.date ASC, id ASC'; break;
			default: $orderBy = 'ah.date DESC, id DESC';
		}
		$query = 'SELECT ah.id, a.title AS articleTitle, a.type AS articleType, ah.articleId, u.username, ah.userId, ah.type, ah.oldValue, ah.newValue, ah.date
					FROM articleHistory AS ah
					LEFT JOIN articles AS a ON a.id=ah.articleId
					LEFT JOIN users AS u ON u.id=ah.userId
					'. $where_string .'
					ORDER BY '.$orderBy;
		$this->_db->setQuery($query, $offset, $limit, true);
		$articleHistoryList = $this->_db->loadAssocList();
		$total = $this->_db->getFoundRows();
		return $articleHistoryList;
	}

	/**
	 *	Get article revision
	 *
	 *	@access public
	 *  @param int revisionId
	 *	@return array Article revision
	 */
	public function getRevision($revisionId)
	{
		$query = 'SELECT userId, articleId, type, oldValue, newValue, date FROM articleHistory WHERE id='.(int)$revisionId;
		$this->_db->setQuery($query);
		return $this->_db->loadAssoc();
	}

	/**
	 *	Get the oldest revision for given field
	 *
	 *	@access public
	 *  @param string Field type
	 *	@return array Oldest revision
	 */
	public function getOldestRevision($articleId,$type)
	{
		$query = 'SELECT id FROM articleHistory WHERE articleId='.(int)$articleId.' AND type="'.$this->_db->escape($type).'" ORDER BY date ASC, id ASC';
		$this->_db->setQuery($query, 0, 1);
		if($revisionId = $this->_db->loadResult()) {
			return $this->getRevision($revisionId);
		}
		else {
			return null;
		}
	}

}

?>