<?php

/**
 *	Comments admin.
 */
class WorkitmomCommentsController extends ClientBackendController {
	
	/**
	 *	List all comments.
	 */
	public function view(){
		
		/* Set breadcrumbs */
		$breadcrumbs = BluApplication::getBreadcrumbs();
		$breadcrumbs->add('Comments', '/comments/');
		
		/* Set page title */
		$this->_doc->setTitle('Comments');
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/comments/landing.php');
		
	}
	
	/**
	 *	Listing.
	 */
	public function listing(){
		
		/* Get parameters */
		$limit = BluApplication::getSetting('backendListingLength');
		$page = Request::getInt('page', 1);
		
		/* Get model */
		$commentsModel = $this->getModel('newcomments');
		
		/* Prepare sort */
		$options = array();
		
		/* Get type of report */
		$type = strtolower(Request::getCmd('type'));
		if (in_array($type, $commentsModel->getTypes())){
			$options['type'] = $type;
		}
		
		/* What to sort reports by */
		$sort = strtolower(Request::getCmd('sort'));
		if (!in_array($sort, array('date', 'type', 'text', 'id', 'reports'))){
			$sort = 'date';
		}
		$options['order'] = $sort;
		
		/* What direction to sort reports in */
		$direction = strtolower(Request::getCmd('direction'));
		if (!in_array($direction, array('asc', 'desc'))){
			$direction = 'asc';
		}
		$options['direction'] = $direction;
		
		/* Get data */
		$total = null;
		$comments = $commentsModel->getComments(($page - 1) * $limit, $limit, $total, $options);
		$commentsModel->addDetails($comments);
		
		/* Paginate */
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => '?sort='.$sort.'&amp;direction='.$direction.'&amp;type='.$type.'&amp;page='
		));
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/comments/listing.php');
		
	}
	
	/**
	 *	Listing: browse - individual rows.
	 */
	protected function listing_individual($comment){
		
		/* Get object */
		$commentsModel = $this->getModel('newcomments');
		$comment['object'] = $commentsModel->getCommentedObject($comment['id']);
		$type = Utility::coalesce(Utility::multi_array_get($comment, 'object', 'type', ''), $comment['objectType']);
		
		/* Styling */
		static $alt = false;
		$alt = !$alt;
		$row = $alt ? 'odd' : 'even';
		
		$priority = 'normal';
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/comments/listing/individual.php');
		
	}
	
	/**
	 *	Details page.
	 */
	public function details(){
		
		/* Get comment ID */
		$args = $this->_args;
		if (!Utility::iterable($args)){
			return $this->_redirect('/comments/');
		}
		$commentId = (int) array_shift($args);
		
		/* Get comment */
		$commentsModel = $this->getModel('newcomments');
		$comment = $commentsModel->getComment($commentId);
		if (!Utility::iterable($comment)){
			return $this->_redirect('/comments/', 'Comment not found.', 'error');
		}
		$comment['link'] = $commentsModel->getCommentLink($comment['id']);
		$comment['object'] = $commentsModel->getCommentedObject($comment['id']);
		
		$commentType = Utility::coalesce(Utility::multi_array_get($comment, 'object', 'type', ''), $comment['objectType']);
		
		/* Get reports */
		$reportsModel = $this->getModel('reports');
		$reports = $reportsModel->getReports(null, null, $reportCount, array(
			'type' => 'comment',
			'id' => $comment['id']
		));
		$reportsModel->addDetails($reports);
		
		/* Add breadcrumbs */
		$breadcrumbs = BluApplication::getBreadcrumbs();
		$breadcrumbs->add('Comments', '/comments/');
		$breadcrumbs->add('Comment #'.$comment['id'], '/comments/details/'.$comment['id'].'/');
		
		/* Set page title */
		$this->_doc->setTitle('Comment #'.$comment['id']);
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/comments/details.php');
		
	}
	
	/**
	 *	Delete a comment
	 */
	public function delete(){
		
		/* Get arguments */
		$commentId = Request::getInt('comment');
		if (!$commentId){
			return $this->_redirect('/comments/');
		}
		
		/* Get model */
		$commentsModel = $this->getModel('newcomments');
		
		/* Get comment */
		$comment = $commentsModel->getComment($commentId);
		if (!Utility::iterable($comment)){
			return $this->_redirect('/comments/', 'Comment not found', 'error');
		}
		
		/* Delete comment */
		$deleted = $commentsModel->delete($comment['id']);
		
		/* Return */
		$message = 'Comment #'.$comment['id'].($deleted ? '' : ' could not be').' deleted.';
		$messageType = $deleted ? 'info' : 'error';
		return $this->_redirect('/comments/', $message, $messageType);
		
	}
	
	/**
	 *	Redirect to admin the commented object.
	 */
	public function redirect(){
		
		/* Get comment ID */
		if (!$commentId = Request::getInt('comment')) {
			return false;
		}
		
		/* Get comment: useless, apart from ensuring it exists. */
		$commentsModel = $this->getModel('newcomments');
		if (!$comment = $commentsModel->getComment($commentId)) {
			return $this->_redirect('/comments', 'Comment not found.', 'error');
		}
		
		/* Redirect to the corresponding admin page. */
		switch($comment['objectType']){
			case 'article':
				$itemsModel = $this->getModel('newitems');
				if ($item = $itemsModel->getItem($comment['objectId'])) {
					return $this->_redirect($item['backend_link']);
				} else {
					Messages::addMessage('Could not find '.$comment['objectType'].' #'.$comment['objectId'], 'error');
				}
				break;
		}
		return $this->_redirect('/comments/');
		
	}
	
}

?>