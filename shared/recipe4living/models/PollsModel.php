<?php

/**
 *	Polls model
 *
 *	@package BluApplication
 *	@subpackage SharedModels
 */
class ClientPollsModel extends BluModel
{
	
	/**
	 *	Get poll
	 *
	 *	@access public
	 *  @param int pollId
	 *	@return array poll
	 */
	public function getPoll($pollId)
	{
		$cacheKey = 'poll_'.$pollId;
		$poll = $this->_cache->get($cacheKey);
		if($poll===false) {
			$query = 'SELECT name, live, UNIX_TIMESTAMP(added) AS added
						FROM polls
						WHERE id='.(int)$pollId;
			$this->_db->setQuery($query);
			$poll = $this->_db->loadAssoc();
			if($pollStatements = $this->getPollStatements($pollId)) {
				$poll['statements'] = $pollStatements;
				$poll['results'] = $this->getPollResults($pollId);
			}
			$this->_cache->set($cacheKey,$poll);
		}
		return $poll;
	}
	
	/**
	 *	Get poll statements
	 *
	 *	@access public
	 *  @param int pollId
	 *	@return array poll
	 */
	public function getPollStatements($pollId)
	{
		$cacheKey = 'poll_statements_'.$pollId;
		$pollStatements = $this->_cache->get($cacheKey);
		if($pollStatements===false) {
			$query = 'SELECT id, text, sequence
						FROM pollStatements
						WHERE pollId='.(int)$pollId.'
						ORDER BY sequence';
			$this->_db->setQuery($query);
			$pollStatements = $this->_db->loadAssocList('id');
			$this->_cache->set($cacheKey,$pollStatements);
		}
		return $pollStatements;
	}
	
	/**
	 *	Get poll statement
	 *
	 *	@access public
	 *  @param int statementId
	 *	@return array statement
	 */
	public function getPollStatement($statementId)
	{
		$query = 'SELECT pollId, text, sequence
					FROM pollStatements
					WHERE id='.(int)$statementId;
		$this->_db->setQuery($query);
		$pollStatement = $this->_db->loadAssoc();
		return $pollStatement;
	}
	
	/**
	 *	Get poll results
	 *
	 *	@access public
	 *  @param int pollId
	 *	@return array statement
	 */
	public function getPollResults($pollId)
	{
		$query = 'SELECT statementId, votes
					FROM pollResults
					WHERE pollId='.(int)$pollId;
		$this->_db->setQuery($query);
		$pollResults = $this->_db->loadAssocList('statementId');
		$totalVotes = 0;
		foreach($pollResults as $statementId=>$result) {
			$totalVotes += $result['votes'];
		}
		//$percentageTotal = 0;
		$pollStatementCount = count($pollResults);
		$i = 0;
		foreach($pollResults as $statementId=>$result) {
			$percentage = round($result['votes'] / $totalVotes * 100);
			//if(++$i==$pollStatementCount) {
			//	$percentage = 100 - $percentageTotal; // percentage adjustment for the last statement
			//}
			//$percentageTotal += $percentage;
			$pollResults[$statementId]['percentage'] = $percentage;
		}
		return $pollResults;
	}
}

?>
