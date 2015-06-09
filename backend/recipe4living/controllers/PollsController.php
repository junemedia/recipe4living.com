<?php

/**
 *	Polls controller
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class Recipe4livingPollsController extends ClientBackendController
{

	protected $_menuSlug = 'polls';

	/**
	 * Base url
	 *
	 * @var string Base url
	 */
	protected $_baseUrl = '/polls';

	public function view()
	{
		// Get models
		$pollsModel = BluApplication::getModel('polls');

		$page = Request::getInt('page', 1);

		$limit = 20;
		$total = NULL;

		$polls = $pollsModel->getPolls($page, $limit, $total);
		
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => '?page='
		));
		
		// Load template
		include(BLUPATH_TEMPLATES.'/polls/view.php');
	}
	
	public function poll() {
		// Get models
		$pollsModel = BluApplication::getModel('polls');
		
		$pollId = Request::getInt('pollId');
		
		if($pollId) {
			$poll = $pollsModel->getPoll($pollId);
		}
		
		// Load template
		include(BLUPATH_TEMPLATES.'/polls/poll.php');
	}
	
	public function submit_poll() {
		// Get models
		$pollsModel = BluApplication::getModel('polls');
		
		$pollId = Request::getInt('pollId');
		$name = Request::getString('name');
		
		if(empty($name)) {
			Messages::addMessage('Name cannot be empty', 'error');
			$this->poll();
			return false;
		}
		
		if($pollId) {
			if($pollsModel->updatePoll($pollId, $name)) {
				Messages::addMessage('Poll updated successfully', 'info');
			}
		}
		else {
			if($pollsModel->addPoll($name)) {
				Messages::addMessage('Poll added successfully', 'info');
			}
		}
		
		return $this->_redirect('/polls');
	}
	
	public function delete_poll() {
		// Get models
		$pollsModel = BluApplication::getModel('polls');
		
		$pollId = Request::getInt('pollId');
		
		if($pollsModel->deletePoll($pollId)) {
			Messages::addMessage('Poll deleted successfully', 'info');
		}
		
		return $this->_redirect('/polls');
	}
	
	public function set_live() {
		// Get models
		$pollsModel = BluApplication::getModel('polls');
		
		$pollId = Request::getInt('pollId');
		
		if($pollsModel->setPollStatus($pollId, 1)) {
			Messages::addMessage('Poll set live successfully', 'info');
		}
		
		return $this->_redirect('/polls');
	}
	
	public function unset_live() {
		// Get models
		$pollsModel = BluApplication::getModel('polls');
		
		$pollId = Request::getInt('pollId');
		
		if($pollsModel->setPollStatus($pollId, 0)) {
			Messages::addMessage('Poll set offline successfully', 'info');
		}
		
		return $this->_redirect('/polls');
	}

	public function statements()
	{
		// Get models
		$pollsModel = BluApplication::getModel('polls');
		
		$pollId = Request::getInt('pollId');
		
		$poll = $pollsModel->getPoll($pollId);
		$pollStatements = $pollsModel->getPollStatements($pollId);
		
		// Load template
		include(BLUPATH_TEMPLATES.'/polls/statements.php');
	}
	
	public function statement() {
		// Get models
		$pollsModel = BluApplication::getModel('polls');
		
		if($statementId = Request::getInt('statementId')) {
			$statement = $pollsModel->getPollStatement($statementId);
			if(!$statement) {
				return $this->_redirect('/polls');
			}
			$pollId = $statement['pollId'];
		}
		else {
			$pollId = Request::getInt('pollId');
		}
		$poll = $pollsModel->getPoll($pollId);
		if(!$poll) {
			return $this->_redirect('/polls');
		}
		
		// Load template
		include(BLUPATH_TEMPLATES.'/polls/statement.php');
	}
	
	public function submit_statement() {
		// Get models
		$pollsModel = BluApplication::getModel('polls');
		
		$statementId = Request::getInt('statementId');
		$text = Request::getString('statement');
		
		if(empty($text)) {
			Messages::addMessage('Statement cannot be empty', 'error');
			$this->statement();
			return false;
		}
		
		if($statementId) {
			$statement = $pollsModel->getPollStatement($statementId);
			if(!$statement) {
				return $this->_redirect('/polls');
			}
			$pollId = $statement['pollId'];
			if($pollsModel->updateStatement($statementId, $text)) {
				Messages::addMessage('Poll statement updated successfully', 'info');
			}
		}
		else {
			$pollId = Request::getInt('pollId');
			if($pollsModel->addStatement($pollId, $text)) {
				Messages::addMessage('Poll statement added successfully', 'info');
			}
		}
		
		return $this->_redirect('/polls/statements?pollId='.$pollId);
	}
	
	public function delete_statement() {
		// Get models
		$pollsModel = BluApplication::getModel('polls');
		
		$statementId = Request::getInt('statementId');
		$statement = $pollsModel->getPollStatement($statementId);
		if(!$statement) {
			return $this->_redirect('/polls');
		}
		
		if($pollsModel->deleteStatement($statementId)) {
			Messages::addMessage('Poll statement deleted successfully', 'info');
		}
		
		return $this->_redirect('/polls/statements?pollId='.$statement['pollId']);
	}
	
	public function move_statement() {
		// Get models
		$pollsModel = BluApplication::getModel('polls');
		
		$statementId = Request::getString('statementId');
		$move = Request::getString('move');
		$statement = $pollsModel->getPollStatement($statementId);
		if(!$statement) {
			return $this->_redirect('/polls');
		}
		
		if(!$pollsModel->moveStatement($statementId, $move)) {
			Messages::addMessage('Poll statement could not be upadated.', 'error');
		}
		
		return $this->_redirect('/polls/statements?pollId='.$statement['pollId']);
	}

}

?>
