<?php

/**
 *	Search term model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendSearchtermsModel extends ClientSearchtermsModel
{
	
	/**
	 *	Get search terms
	 *
	 *	@access public
	 *	@return array Search terms
	 */
	public function getSearchTerms($page = 1, $limit = 10, &$total = NULL, $sort = '`searched` DESC')
	{
		switch($sort) {
			case 'date_asc': $orderBy = '`searched` ASC'; break;
			case 'date_desc': $orderBy = '`searched` DESC'; break;
			case 'term_asc': $orderBy = '`term` ASC'; break;
			case 'term_desc': $orderBy = '`term` DESC'; break;
			case 'term_extra_asc': $orderBy = '`termExtra` ASC'; break;
			case 'term_extra_desc': $orderBy = '`termExtra` DESC'; break;
			case 'results_asc': $orderBy = '`resultCount` ASC'; break;
			case 'results_desc': $orderBy = '`resultCount` DESC'; break;
			default: $orderBy = '`searched` DESC';
		}
		$query = 'SELECT `id`, `term`, `termExtra`, `resultCount`, UNIX_TIMESTAMP(`searched`) AS `searched`
					FROM `searchTerms`
					ORDER BY '.$orderBy;
		$this->_db->setQuery($query, ($page-1)*$limit, $limit, true);
		$searchTerms = $this->_db->loadAssocList('id');
		$total = $this->_db->getFoundRows();
		return $searchTerms;
	}

	/**
	 *	Get search term counts
	 *
	 *	@access public
	 *	@param array Search terms (passed by reference)
	 */
	public function getSearchTermCounts(&$searchTerms)
	{
		foreach($searchTerms as $searchTermId=>$searchTerm) {
			$query = 'SELECT COUNT(*)
						FROM `searchTerms`
						WHERE term="'.$this->_db->escape($searchTerm['term']).'" AND termExtra'.($searchTerm['termExtra']?'="'.$this->_db->escape($searchTerm['termExtra']).'"':' IS NULL');
			$this->_db->setQuery($query);
			$searchTermCount = $this->_db->loadResult();
			$searchTerms[$searchTermId]['count'] = $searchTermCount;
		}
	}
}

?>
