<?php

/**
 *	Polls model
 *
 *	@package BluApplication
 *	@subpackage FrontendModels
 */
class ClientFrontendPollsModel extends ClientPollsModel
{
	
	/**
	 *	Get polls
	 *
	 *	@access public
	 *	@return array polls
	 */
	public function getPolls()
	{
		$query = 'SELECT id
					FROM polls
					WHERE live=1
					ORDER BY added';
		$this->_db->setQuery($query);
		$pollIds = $this->_db->loadAssocList();
		$polls = array();
		foreach($pollIds as $pollIdData) {
			$pollId = $pollIdData['id'];
			if($poll = $this->getPoll($pollId)) {
				$polls[$pollId] = $poll;
			}
		}
		return $polls;
	}
	
	/**
	 *	Add vote
	 *  (we allow one vote per one IP address; last vote overrides all previous votes from the same IP address)
	 *
	 *	@access public
	 *  @param int poll ID
	 *  @param int statement ID
	 *	@return bool result
	 */
	public function addVote($pollId, $statementId)
	{
/*
		$query = 'SELECT statementId FROM pollVotes WHERE pollId='.(int)$pollId.' AND IPAddress="'.$_SERVER['REMOTE_ADDR'].'"';
		$this->_db->setQuery($query);
		if($this->_db->loadResult()) {
			return false;
		}
*/
		$query = 'REPLACE INTO pollVotes (pollId, IPAddress, statementId, voted) VALUES ('.(int)$pollId.', "'.$_SERVER['REMOTE_ADDR'].'", '.(int)$statementId.', NOW())';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Update poll results
	 *
	 *	@access public
	 *  @param int pollId
	 *	@return array result
	 */
	public function updatePollResults($pollId)
	{
		$cacheKey = 'poll_'.$pollId;
		$this->_cache->delete($cacheKey);
		$query = 'REPLACE INTO pollResults SELECT ps.pollId, ps.id, COUNT(pv.statementId) as votes FROM 
					pollStatements AS ps 
					LEFT JOIN pollVotes AS pv ON ps.id=pv.statementId
					WHERE ps.pollId='.(int)$pollId.'
					GROUP BY ps.id';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
}

?>
