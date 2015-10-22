<?php

/**
 *	Administrating users and accounts.
 */
class WorkitmomUsersController extends ClientBackendController {
	
	/**
	 *	Users page.
	 */
	public function view(){
		
		/* Set breadcrumbs */
		$breadcrumbs = BluApplication::getBreadcrumbs();
		$breadcrumbs->add('User accounts', '/users/');
		
		/* Set page title */
		$this->_doc->setTitle('User accounts.');
		
	}
	
	/**
	 *	Details page.
	 */
	public function details(){
		
		/* Get user */
		if (empty($this->_args)){
			return $this->_redirect('/users');
		}
		$username = $this->_args[0];
		$personModel = $this->getModel('newperson');
		$person = $personModel->getPerson(array('username' => $username));
		if (empty($person)){
			return $this->_redirect('/users');
		}
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/users/details.php');
		
	}

	/**
	 *	Deleted users listing.
	 */
	public function deleted(){
		
		/* Set breadcrumbs */
		$breadcrumbs = BluApplication::getBreadcrumbs();
		$breadcrumbs->add('User accounts', '/users/');
		$breadcrumbs->add('Deleted accounts', '/users/deleted/');
		
		/* Set page title */
		$this->_doc->setTitle('Deleted accounts');
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/users/deleted.php');
		
	}
	
	/**
	 *	Listing: browse all deleted accounts.
	 */
	public function deleted_listing(){
		
		/* Get parameters */
		$limit = BluApplication::getSetting('backendListingLength');
		$page = Request::getInt('page', 1);
		
		/* Get model */
		$personModel = $this->getModel('newperson');
		
		/* Prepare sort - always show deleted */
		$options = array('deleted' => true);
		
		/* What to sort reports by */
		$sort = strtolower(Request::getCmd('sort'));
		if (!in_array($sort, array('date', 'name', 'id', 'active'))){
			$sort = 'name';
		}
		switch($sort){
			case 'date':
				$options['order'] = 'date_terminated';
				break;
				
			case 'name':
				$options['order'] = 'name';
				break;
				
			case 'id':
				$options['order'] = 'id';
				break;
				
			case 'active':
				$options['order'] = 'active';
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
		$people = $personModel->getPeople(($page - 1) * $limit, $limit, $total, $options);
		
		/* Paginate */
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => '?sort='.$sort.'&amp;direction='.$direction.'&amp;page='
		));
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/users/deleted_listing.php');
		
	}
	
	/**
	 *	Re-open a deleted account.
	 */
	public function reinstate_deleted(){
		
		/* Arguments */
		$args = $this->_args;
		if (!Utility::iterable($args)){
			return $this->_errorRedirect();
		}
		$userId = array_shift($args);
		if (!$userId){
			return $this->_errorRedirect();
		}
		
		/* Reopen account */
		$userModel = $this->getModel('user');
		$reopened = $userModel->reinstate($userId);
		
		$personModel = $this->getModel('newperson');
		$person = $personModel->getPerson(array('member' => $userId));
		
		/* Redirect */
		if ($reopened){
			
			/* Generate new password */
			$password = $userModel->regenerate_password($person['userid']);
			
			/* Messages */
			Messages::addMessage('Reopened account for "'.$person['name'].'" (User ID: '.$person['userid'].', username: '.$person['username'].').');
			//Messages::addMessage('Assigned new password: '.$password);
			Messages::addMessage('Password has been reset, please ask user to request a new password through <span class="underlined">http://www.workitmom.com/account/password_reminder/?form_identifier='.$person['email'].'</span> .');
			
		} else {
			
			/* Message */
			Messages::addMessage('Could not reopen account for "'.$person['name'].'" (User ID: '.$person['userid'].', username: '.$person['username'].')', 'error');
			
		}
		return $this->_redirect('/users/deleted/');
		
	}

}

?>