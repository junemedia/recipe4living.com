<?php

/**
 *	Dealing with reports.
 */
class WorkitmomReportsController extends ClientBackendController {
	
	/**
	 *	Overview page.
	 */
	public function view(){
		
		/* Set breadcrumbs */
		$breadcrumbs = BluApplication::getBreadcrumbs();
		$breadcrumbs->add('Reports', '/reports/');
		
		/* Set page title */
		$this->_doc->setTitle('Reports');
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/reports/landing.php');
		
	}
	
	/**
	 *	Listing: browse all reports.
	 */
	public function listing(){
		
		/* Get parameters */
		$limit = BluApplication::getSetting('backendListingLength');
		$page = Request::getInt('page', 1);
		
		/* Get model */
		$reportsModel = $this->getModel('reports');
		
		/* Prepare sort */
		$options = array();
		
		/* Get type of report */
		$type = strtolower(Request::getCmd('type'));
		if (!in_array($type, array('article', 'comment', 'grouppost', 'userphoto'))){
			$type = null;
		}
		switch($type){
			case 'article':
			case 'comment':
			case 'grouppost':
			case 'userphoto':
				$options['type'] = $type;
				break;
			
			default:
				break;
		}
		
		/* What to sort reports by */
		$sort = strtolower(Request::getCmd('sort'));
		if (!in_array($sort, array('date', 'type', 'id', 'status'))){
			$sort = 'status';
		}
		switch($sort){
			case 'date':
				$options['order'] = 'time';
				break;
				
			case 'type':
				$options['order'] = 'type';
				break;
				
			case 'id':
				$options['order'] = 'id';
				break;
				
			case 'status':
				$options['order'] = 'status';
				break;
		}
		
		/* What direction to sort reports in */
		$direction = strtolower(Request::getCmd('direction'));
		if (!in_array($direction, array('asc', 'desc'))){
			$direction = 'asc';
		}
		switch($direction){
			case 'asc':
				$options['direction'] = 'asc';
				break;
				
			case 'desc':
				$options['direction'] = 'desc';
				break;
		}
		
		/* Get data */
		$total = null;
		$reports = $reportsModel->getReports(($page - 1) * $limit, $limit, $total, $options);
		$reportsModel->addDetails($reports);
		
		/* Paginate */
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => '?sort='.$sort.'&amp;direction='.$direction.'&amp;type='.$type.'&amp;page='
		));
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/reports/listing.php');
		
	}
	
	/**
	 *	Listing: browse - individual rows.
	 */
	protected function listing_individual($report){
		
		/* Styling */
		static $alt = false;
		$alt = !$alt;
		$row = $alt ? 'odd' : 'even';
		
		$priority = 'normal';
		switch($report['status']){
			case 'pending':
				$priority = 'high';
				break;
				
			case 'viewed':
				$priority = 'normal';
				break;
				
			case 'resolved':
				$priority = 'low';
				break;
		}
		
		// Get link
		$link = SITEURL.'/reports/details/'.$report['id'];
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/reports/listing/individual.php');
		
	}
	
	/**
	 *	Report details page.
	 */
	public function details(){
		
		/* Get arguments */
		$args = $this->_args;
		if (empty($args)){
			return $this->_redirect('/reports/');
		}
		$reportId = (int) array_shift($args);
		
		/* Get model */
		$reportsModel = $this->getModel('reports');
		
		/* Get report */
		$report = $reportsModel->getReport($reportId);
		if (!Utility::iterable($report)){
			return $this->_redirect('/reports/', 'Report not found.', 'error');
		}
		ksort($report);
		
		/* "View" the report. */
		$reportsModel->viewReport($report);
		
		/* Get reported comment */
		$object = $reportsModel->getReportedObject($report['id']);
		
		/* Get related reports */
		$totalRelated = null;
		$relatedReports = $reportsModel->getRelated($report['id'], null, null, $totalRelated, array(
			'order' => 'id', 
			'show_original' => false
		));
		$reportsModel->addDetails($relatedReports);
		
		/* Set breadcrumbs */
		$breadcrumbs = BluApplication::getBreadcrumbs();
		$breadcrumbs->add('Reports', '/reports/');
		$breadcrumbs->add('Report #'.$report['id'], '/reports/details/'.$report['id'].'/');
		
		/* Set page title */
		$this->_doc->setTitle('Report #'.$report['id']);
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/reports/details.php');
		
	}
	
	/**
	 *	Resolve a report.
	 */
	public function resolve(){
		
		/* Get report ID */
		$reportId = Request::getInt('report');
		if (!$reportId){
			return false;
		}
		
		/* Get model */
		$reportsModel = $this->getModel('reports');
		
		/* Resolve single report */
		$success = $reportsModel->resolveReport($reportId, false);
		
		/* Output message */
		if ($success){
			Messages::addMessage('Report #'.$reportId.' set to "<code>resolved</code>"');
		} else {
			Messages::addMessage('Report #'.$reportId.' was not changed.', 'error');
		}
		
		/* Action */
		switch ($this->_doc->getFormat()){
			case 'json':
				echo json_encode($success);
				break;
				
			case 'raw':
				echo $success;
				break;
				
			default:
				return $this->_redirect('/reports/details/'.$reportId);
				break;
		}
		
		/* Return */
		return $success;
		
	}
	
	/**
	 *	Resolve all reports related to the same object.
	 */
	public function resolve_all(){
		
		/* Get report ID */
		$reportId = Request::getInt('report');
		if (!$reportId){
			return false;
		}
	
		/* Get model */
		$reportsModel = $this->getModel('reports');
		
		/* Resolve all reports similar to this report. */
		$success = $reportsModel->resolveReport($reportId, true);
		
		/* Output message */
		if ($success){
			Messages::addMessage('All related reports were set to "<code>resolved</code>"');
		} else {
			Messages::addMessage('Not all reports were changed.', 'error');
		}
		
		/* Action */
		switch ($this->_doc->getFormat()){
			case 'json':
				echo json_encode($success);
				break;
				
			case 'raw':
				echo $success;
				break;
				
			default:
				return $this->_redirect('/reports/details/'.$reportId);
				break;
		}
		
		/* Return */
		return $success;
		
	}
	
	/**
	 *	Redirect to administrate the reported object.
	 */
	public function redirect(){
		
		/* Get report ID */
		$reportId = Request::getInt('report');
		if (!$reportId){
			return false;
		}
		
		/* Get report */
		$reportsModel = $this->getModel('reports');
		$report = $reportsModel->getReport($reportId);
		if (!Utility::iterable($report)){
			return $this->_redirect('/reports/', 'Report not found.', 'error');
		}
		
		/* Redirect to the corresponding admin page. */
		switch($report['objectType']){
			case 'article':
				$itemsModel = $this->getModel('newitems');
				$item = $itemsModel->getItem($report['objectId']);
				return $this->_redirect($item['backend_link']);
				break;
				
			case 'comment':
				return $this->_redirect('/comments/details/'.$report['objectId']);
				break;
				
			case 'grouppost':
				return $this->_redirect('/groups/posts_details/'.$report['objectId']);
				break;
				
			default:
				return $this->_redirect('/reports/');
				break;
		}
		
	}
	
	/**
	 *	Send a message to a user's site inbox.
	 */
	public function reply() 
	{
		// Get reporter
		if (!$report = Request::getInt('report')) {
			return false;
		}
		$reportsModel = $this->getModel('reports');
		$report = $reportsModel->getReport($report);
		
		// Prepare message
		$personModel = $this->getModel('newperson');
		$recipient = $personModel->getPerson(array('member' => $report['reporter']));
		$subject = 'Re: Report of abuse';
		$_nl = "\r\n";
		$message = $recipient['name'].','.$_nl.$_nl.'Thank you for your report, we will endeavor to resolve any problems as soon as possible.'.$_nl.$_nl.'Regards,'.$_nl.'Workitmom Team'; // [sic] American
		
		// Prepare redirect
		$redirect = '/reports/details/'.$report['id'];
		
		// Set page meta
		$this->_doc->setTitle('Send message to reporter.');
		
		// Display form
		include(BLUPATH_TEMPLATES.'/reports/reply.php');
	}
	
	/**
	 * Send a message
	 */
	public function reply_submit()
	{
		// Get data from request
		$report = Request::getInt('report');
		$subject = Request::getString('subject');
		$message = Request::getString('message');

		// Validate
		$validation = array();
		$validation['required_subject'] = !empty($subject);
		$validation['required_message'] = !empty($message);
		if (in_array(false, $validation)) {
			Messages::addMessage('You need to enter a subject and a message', 'error');
			return $this->_showMessages('reply', 'reply');
		}
		
		// Get report
		$reportsModel = $this->getModel('reports');
		$report = $reportsModel->getReport($report);
		
		// Get users
		$personModel = $this->getModel('newperson');
		$recipient = $report['author'];
		$admin = $personModel->getPerson(array(
			'username' => BluApplication::getSetting('adminUsername')
		));

		// Send message
		$messagesModel = BluApplication::getModel('messages');
		$sent = $messagesModel->sendMessage($admin['userid'], $recipient['userid'], $subject, $message);
		
		// Change report status
		if ($sent) {
			$reportsModel->replyReport($report['id']);
		}
		
		// Redirect
		$redirect = Request::getString('redirect', $report['backend_link']);
		if ($sent) {
			$this->_redirect($redirect, 'Your message has been sent.');
		} else {
			$this->_redirect($redirect, 'Your message wasn\'t sent. Please try again.', 'error');
		}
	}
	
}

?>