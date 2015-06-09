<?php

/**
 *	Polls model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendPollsModel extends ClientPollsModel
{
	
	/**
	 *	Get polls
	 *
	 *	@access public
	 *  @param int page
	 *  @param int limit
	 *  @param int total
	 *	@return array polls
	 */
	public function getPolls($page = 1, $limit = 10, &$total = NULL)
	{
		$query = 'SELECT id, name, live, UNIX_TIMESTAMP(added) AS added
					FROM polls
					ORDER BY added';
		$this->_db->setQuery($query, ($page-1)*$limit, $limit, true);
		$polls = $this->_db->loadAssocList('id');
		$total = $this->_db->getFoundRows();
		return $polls;
	}
	
	/**
	 *	Add poll
	 *
	 *	@access public
	 *  @param string name
	 *	@return bool result
	 */
	public function addPoll($name)
	{
		$query = 'INSERT INTO polls (name, live, added) VALUES ("'.$this->_db->escape($name).'", 0, NOW())';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Update poll
	 *
	 *	@access public
	 *  @param int pollId
	 *  @param string name
	 *	@return bool result
	 */
	public function updatePoll($pollId, $name)
	{
		$cacheKey = 'poll_'.$pollId;
		$this->_cache->delete($cacheKey);
		$query = 'UPDATE polls SET name="'.$this->_db->escape($name).'" WHERE id='.(int)$pollId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Delete poll
	 *
	 *	@access public
	 *  @param int pollId
	 *	@return bool result
	 */
	public function deletePoll($pollId)
	{
		$cacheKey = 'poll_'.$pollId;
		$this->_cache->delete($cacheKey);
		$query = 'DELETE FROM pollVotes WHERE pollId='.(int)$pollId;
		$this->_db->setQuery($query);
		$result1 = $this->_db->query();
		$query = 'DELETE FROM pollResults WHERE pollId='.(int)$pollId;
		$this->_db->setQuery($query);
		$result2 = $this->_db->query();
		$query = 'DELETE FROM polls WHERE id='.(int)$pollId;
		$this->_db->setQuery($query);
		$result3 = $this->_db->query();
		$query = 'DELETE FROM pollStatements WHERE pollId='.(int)$pollId;
		$this->_db->setQuery($query);
		$result4 = $this->_db->query();
		return ($result1 && $result2 && $result3 && $result4);
	}
	
	/**
	 *	Set poll status
	 *
	 *	@access public
	 *  @param int pollId
	 *  @param int status
	 *	@return bool result
	 */
	public function setPollStatus($pollId, $status)
	{
		$cacheKey = 'poll_'.$pollId;
		$this->_cache->delete($cacheKey);
		$query = 'UPDATE polls SET live='.(int)$status.' WHERE id='.(int)$pollId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Add poll statement
	 *
	 *	@access public
	 *  @param int poll ID
	 *  @param string text
	 *	@return bool result
	 */
	public function addStatement($pollId, $text)
	{
		$cacheKey = 'poll_'.$pollId;
		$this->_cache->delete($cacheKey);
		$cacheKey = 'poll_statements_'.$pollId;
		$this->_cache->delete($cacheKey);
		$query = 'SELECT COUNT(*) FROM pollStatements WHERE pollID='.(int)$pollId;
		$this->_db->setQuery($query);
		$statementCount = $this->_db->loadResult();
		$query = 'INSERT INTO pollStatements (pollId, text, sequence) VALUES ('.(int)$pollId.', "'.$this->_db->escape($text).'", '.($statementCount+1).')';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Update poll statement
	 *
	 *	@access public
	 *  @param int statementId
	 *  @param string text
	 *	@return bool result
	 */
	public function updateStatement($statementId, $text)
	{
		$statement = $this->getPollStatement($statementId);
		if(!$statement) {
			return false;
		}
		$pollId = $statement['pollId'];
		
		$cacheKey = 'poll_'.$pollId;
		$this->_cache->delete($cacheKey);
		$cacheKey = 'poll_statements_'.$pollId;
		$this->_cache->delete($cacheKey);
		
		$query = 'UPDATE pollStatements SET text="'.$this->_db->escape($text).'" WHERE id='.(int)$statementId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Delete poll statement
	 *
	 *	@access public
	 *  @param int statementId
	 *	@return bool result
	 */
	public function deleteStatement($statementId)
	{
		$statement = $this->getPollStatement($statementId);
		if(!$statement) {
			return false;
		}
		$pollId = $statement['pollId'];
		
		$cacheKey = 'poll_'.$pollId;
		$this->_cache->delete($cacheKey);
		$cacheKey = 'poll_statements_'.$pollId;
		$this->_cache->delete($cacheKey);
		
		$query = 'DELETE FROM pollVotes WHERE statementId='.(int)$statementId;
		$this->_db->setQuery($query);
		$result1 = $this->_db->query();
		$query = 'DELETE FROM pollResults WHERE statementId='.(int)$statementId;
		$this->_db->setQuery($query);
		$result2 = $this->_db->query();
		$query = 'DELETE FROM pollStatements WHERE id='.(int)$statementId;
		$this->_db->setQuery($query);
		$result3 = $this->_db->query();
		$query = 'UPDATE pollStatements SET sequence=sequence-1 WHERE pollId='.$pollId.' AND sequence>'.$statement['sequence'];
		$this->_db->setQuery($query);
		$result4 = $this->_db->query();
		
		return ($result1 && $result2 && $result3 && $result4);
	}
	
	/**
	 *	Update position of a statement
	 *
	 *	@access public
	 *  @param int statementId
	 *	@return bool result
	 */
	public function moveStatement($statementId, $move)
	{
		$statement = $this->getPollStatement($statementId);
		if(!$statement) {
			return false;
		}
		$pollId = $statement['pollId'];
		
		$cacheKey = 'poll_'.$pollId;
		$this->_cache->delete($cacheKey);
		$cacheKey = 'poll_statements_'.$pollId;
		$this->_cache->delete($cacheKey);
		
		$statement = $this->getPollStatement($statementId);
		if(!$statement) {
			return false;
		}
		$pollId = $statement['pollId'];
		
		if($move=='down') {
			$query = 'SELECT COUNT(*) FROM pollStatements WHERE pollID='.(int)$pollId;
			$this->_db->setQuery($query);
			$statementCount = $this->_db->loadResult();
			if($statement['sequence']>=$statementCount) {
				return false;
			}
			$sequence = $statement['sequence'] + 1;
			$query = 'UPDATE pollStatements SET sequence=sequence-1 WHERE pollID='.(int)$pollId.' AND sequence='.($statement['sequence']+1);
			$this->_db->setQuery($query);
			$result = $this->_db->query();
		}
		elseif($move=='up') {
			if($statement['sequence']<=1) {
				return false;
			}
			$sequence = $statement['sequence'] - 1;
			$query = 'UPDATE pollStatements SET sequence=sequence+1 WHERE pollID='.(int)$pollId.' AND sequence='.($statement['sequence']-1);
			$this->_db->setQuery($query);
			$result = $this->_db->query();
		}
		
		$query = 'UPDATE pollStatements SET sequence='.$sequence.' WHERE id='.(int)$statementId;
		$this->_db->setQuery($query);
		return ($result && $this->_db->query());
	}
	
}

?>
