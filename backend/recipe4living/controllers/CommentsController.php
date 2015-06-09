<?php

/**
 *	Comments controller
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class Recipe4livingCommentsController extends ClientBackendController
{

	/**
	 * Base url
	 *
	 * @var string Base url
	 */
	protected $_baseUrl = '/comments';
	
	/**
	 *	Menu slug
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_menuSlug = 'comment_listing';

	/**
	 * View list of comments
	 *
	 * @access public
	 */
	public function view()
	{
		// Get models
		$itemsModel = BluApplication::getModel('items');
		
		// Page
		$page = Request::getInt('page', 1);
		
		// Clear search
		if(Request::getBool('clear')) {
			return $this->_redirect($this->_baseUrl);
		}

		// Set search parameters
		$urlArgsArray = array();
		if(($date_from = Request::getString('date_from')) && ($date_from_timestamp = strtotime($date_from))) {
			$urlArgsArray['date_from'] = $date_from;
		}
		if(($date_to = Request::getString('date_to')) && ($date_to_timestamp = strtotime($date_to))) {
			if(empty($date_from) || ($date_from_timestamp<=$date_to_timestamp)) {
				$urlArgsArray['date_to'] = $date_to;
			}
		}
		if($content = Request::getString('content')) {
			$urlArgsArray['content'] = $content;
		}
		if($recipe = Request::getString('recipe')) {
			$urlArgsArray['recipe'] = $recipe;
		}
		if($user = Request::getString('user')) {
			$urlArgsArray['user'] = $user;
		}
		$flagged = Request::getInt('flagged');
		if($flagged==1 || $flagged==2) {
			$urlArgsArray['flagged'] = $flagged;
		}
		$live = Request::getInt('live');
		if($live==1 || $live==2) {
			$urlArgsArray['live'] = $live;
		}
		
		$baseUrl = $this->_baseUrl;
		$filterArray = $urlArgsArray;
		$urlArgsArray['page'] = '';
		$paginationUrl = SITEURL . $baseUrl . '?' . http_build_query($urlArgsArray);
		$urlArgsArray['page'] = $page;
		$pageUrl = $baseUrl . '?' . http_build_query($urlArgsArray);

		$limit = 20;
		$total = NULL;

		// Get comments
		$comments = $itemsModel->getComments($page, $limit, $total, $filterArray);
		
		Session::set('commentSearchArgs', $urlArgsArray);
		
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => $paginationUrl
		));

		// Base url
		$detailsPageBaseUrl = $this->_baseUrl . '/commentdetails';
		
		// Load template
		include(BLUPATH_TEMPLATES.'/comments/view.php');
	}
	
	/**
	 *	Enable / disable comment
	 *
	 *	@access public
	 */
	public function setStatus()
	{
		// Get models
		$itemsModel = BluApplication::getModel('items');
		
		$commentId = Request::getInt('commentId');
		$action = Request::getString('action');
		
		// Diable comment
		if($action == 'disable') {
			$itemsModel->setCommentStatus($commentId, 0);
		}
		// Enable comment
		elseif($action == 'enable') {
			$itemsModel->setCommentStatus($commentId, 1);
		}
		
		// Redirect
		$urlArgsArray = Session::get('commentSearchArgs');
		$redirectUrl = $this->_baseUrl . '?' . http_build_query($urlArgsArray);
		return $this->_redirect($redirectUrl);
	}
	
	/**
	 *	View comment details
	 *
	 *	@access public
	 */
	public function commentDetails()
	{
		// Get models
		$itemsModel = BluApplication::getModel('items');
		$reportsModel = BluApplication::getModel('reports');
		
		// Get comment ID
		if(isset($this->_args[0])) {
			$commentId = (int)$this->_args[0];
		}
		else {
			return $this->_redirect($this->_baseUrl);
		}
		
		$reportStatuses = array( 'viewed', 'resolved' );
		
		// Get models
		$comment = $itemsModel->getComment($commentId);
		
		// update report statuses to 'viewed'
		if(isset($comment['reports'])) {
			if(isset($comment['reports']['raw']['pending'])) {
				foreach($comment['reports']['raw']['pending'] as $reportId => $report) {
					$comment['reports']['raw']['viewed'][$reportId] = $report;
					unset($comment['reports']['raw']['pending'][$reportId]);
					$reportsModel->setStatus($reportId, 'viewed', $comment['live'] == 1 ? true : false);
				}
			}
		}

		$urlArgsArray = Session::get('commentSearchArgs');
		$backButtonUrl = SITEURL . $this->_baseUrl . '?' . http_build_query($urlArgsArray);
		
		// Load template
		include(BLUPATH_TEMPLATES.'/comments/details.php');
	}
	
	/**
	 *	Update report statuses
	 *
	 *	@access public
	 */
	public function updateReportStatuses()
	{
		// Get models
		$itemsModel = BluApplication::getModel('items');
		$reportsModel = BluApplication::getModel('reports');
		
		// Get comment ID
		if(isset($this->_args[0])) {
			$commentId = (int)$this->_args[0];
		}
		else {
			return $this->_redirect($this->_baseUrl);
		}
		
		// Get models
		$comment = $itemsModel->getComment($commentId);
		
		$reportStatuses = Request::getArray('reportStatus');

		foreach($reportStatuses as $reportId=>$reportStatus) {
			if($reportStatus == 'resolved') {
				$reportsModel->setStatus($reportId, 'resolved', $comment['live'] == 1 ? true : false);
			}
		}
		
		// Redirect
		$urlArgsArray = Session::get('commentSearchArgs');
		$redirectUrl = $this->_baseUrl . '?' . http_build_query($urlArgsArray);
		return $this->_redirect($redirectUrl);
		
	}

}

?>
