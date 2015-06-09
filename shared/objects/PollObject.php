<?

/**
 *	Represents a poll, its possible answers, and its current results.
 */
class PollObject extends BluObject{

	public function __construct($id){

		// Get database and cache resources.
		parent::__construct();

		$this->id = (int) $id;
		$this->_cacheObjectID = 'poll_'.$this->id;

		/* Build Object */
		$query = "SELECT *
			FROM `polls` AS `p`
			WHERE p.pollId = " . $this->id;
		$this->_buildObject($query);

	}

	/**
	 *	Make a vote.
	 *
	 *	@args (int) answer: the ID of the answer.
	 */
	public function vote($answer){

		// Update database
		$query = "UPDATE `pollAnswers` AS `pa`
			SET pa.answerVotes = pa.answerVotes + 1
			WHERE pa.pollAnswerId = ".(int)$answer;
		$this->_db->setQuery($query);
		$success = $this->_db->loadSuccess() > 0;

		// If success, flush cache
		return $success ? $this->_cache->delete($this->_cacheObjectID) : false;

	}




	###							PRIVATE CONVENIENCE FUNCTIONS							###

	/**
	 *	Publicly available variables must be defined here.
	 */
	protected function _setVariables(){
		$this->pollForum = $this->_data->pollForum;
		$this->question = $this->_getQuestion();
		$this->answers = $this->_getAnswers();
		$this->date = $this->_getDate();
		return $this;
	}

	/**
	 *	Get the question text.
	 */
	private function _getQuestion(){
		return isset($this->_data->pollQuestion)?$this->_data->pollQuestion:null;
	}

	/**
	 *	Get the answers, as objects, together with the number of votes for that answer.
	 */
	private function _getAnswers(){

		// Get the answers - these will largely stay unchanged
		$answers_cacheObjectID = $this->_cacheObjectID . '_answers';
		$answers = $this->_cache->get($answers_cacheObjectID);
		if ($answers === false){

			$query = "SELECT pa.pollAnswerId AS `id`, pa.answerText AS `text`, pa.answerVotes AS `votes`
				FROM `pollAnswers` AS `pa`
				WHERE pa.pollId = ".$this->id;
			$this->_db->setQuery($query);
			$answers = $this->_db->loadAssocList();

		    // Store in cache
			$this->_cache->set($answers_cacheObjectID, $answers);

		}

		// Format into objects
		$return = array();
		if (Utility::is_loopable($answers)){
			foreach($answers as $answer){
				$return[] = Utility::toObject($answer);
			}
		}

		// Exit
		return $return;

	}

	/**
	 *	Get the date that this poll was first posted
	 */
	private function _getDate(){
		return isset($this->_data->pollDate)?Utility::formatDate($this->_data->pollDate):null;
	}

}

?>