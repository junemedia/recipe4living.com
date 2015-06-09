<?php

/**
 *	Search term model
 *
 *	@package BluApplication
 *	@subpackage FrontendModels
 */
class ClientFrontendSearchtermsModel extends ClientSearchtermsModel
{
	
	/**
	 *	Save search term and number of results to the database
	 *
	 *	@access public
	 *	@param string Search term
	 *	@param int Number of results returned by search
	 *	@return bool
	 */
	public function saveSearchTerm($searchType, $searchTerm, $searchTermExtra, $resultCount)
	{
		$userModel = BluApplication::getModel('user');
		
		// admin's searches area not saved
		$user = $userModel->getCurrentUser();
		if($user['type'] == 'admin') {
			return false;
		}
		
		$searchTermArray = array('type' => $searchType, 'term' => strtolower($searchTerm), 'termExtra' => strtolower($searchTermExtra), 'resultCount' => $resultCount);
		$searchTerms = Session::get('searchTerms');
		if(!$searchTerms) {
			$searchTerms = array();
		}
		
		if(!in_array($searchTermArray, $searchTerms)) {
			$searchTerms[] = $searchTermArray;
			Session::set('searchTerms', $searchTerms);
			$query = 'INSERT INTO `searchTerms` SET 
						`type` = "'. Database::escape($searchType) .'",
						`term` = "'. Database::escape($searchTerm) .'",
						`termExtra` = '.(empty($searchTermExtra) ? 'NULL' : '"'. Database::escape($searchTermExtra) .'"').',
						`resultCount` = '. (int)$resultCount .',
						`searched` = NOW()';
			$this->_db->setQuery($query);
			return $this->_db->query();
		}
		
		return false;
	}
	
	/**
	 *	Get popular searches
	 *
	 *	@access public
	 *	@return array Popular search terms
	 */
	public function getPopularSearchTerms($limit = 12)
	{
		$cacheKey = 'popular_search_terms_'.$limit;
		$popularSearchTerms = $this->_cache->get($cacheKey);
		if($popularSearchTerms === false) {
			$expiry = mktime(5, 0, 0, date('m') , date('d') + 1, date('Y')) - time(); // cache will expire at 5 o'clock in the morning next day
			$query = 'SELECT `type`, `term`, COUNT(*) AS count 
					FROM `searchTerms` 
					WHERE `term` != ""
					GROUP BY `term`, `type` 
					ORDER BY count DESC
					LIMIT '. (int)$limit;
			$this->_db->setQuery($query);
			$popularSearchTerms = $this->_db->loadAssocList();
			$this->_cache->set($cacheKey, $popularSearchTerms, $expiry);
		}
		return $popularSearchTerms;
	}
	
}

?>
