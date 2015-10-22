<?php

/**
 *	Giveaways admin.
 */
class WorkitmomGiveawaysController extends ClientBackendController {
	
	/**
	 *	Default page.
	 */
	public function view(){
		
		/* Set breadcrumbs */
		$breadcrumbs = BluApplication::getBreadcrumbs();
		$breadcrumbs->add('Giveaways', '/giveaways/');
		
		/* Set page title */
		$this->_doc->setTitle('Giveaways');
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/giveaways/landing.php');
		
	}
	
	/**
	 *	Listing.
	 */
	public function comments_listing(){
		
		/* Get parameters */
		$limit = BluApplication::getSetting('backendListingLength');
		$page = Request::getInt('page', 1);
		
		/* Get model */
		$commentsModel = $this->getModel('newcomments');
		
		/* Prepare sort */
		$options = array();
		
		/* What to sort reports by */
		$sort = strtolower(Request::getCmd('sort'));
		if (!in_array($sort, array('date', 'text', 'reports'))){
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
		$options['object'] = 1200;	// Giveaway only.
		$comments = $commentsModel->getComments(($page - 1) * $limit, $limit, $total, $options);
		$commentsModel->addDetails($comments);
		
		/* Paginate */
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => '?sort='.$sort.'&amp;direction='.$direction.'&amp;page='
		));
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/giveaways/comments_listing.php');
		
	}
	
	/**
	 *	Listing: browse - individual rows.
	 */
	protected function comments_listing_individual($comment){
		
		/* Styling */
		static $alt = false;
		$alt = !$alt;
		$row = $alt ? 'odd' : 'even';
		
		$priority = 'normal';
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/giveaways/comments_listing_individual.php');
		
	}
	
	/**
	 *	Delete comments
	 */
	public function delete_comments(){
		
		/* Get giveaways model */
		$giveawaysModel = $this->getModel('giveaways');
		
		/* Delete */
		$success = $giveawaysModel->delete_comments();
		
		/* Display success */
		if ($success){
			echo 'Success';
		} else {
			echo 'Failed';
		}
		echo ': '.__FUNCTION__.'<br />';
		
		/* Return */
		return $this->view();
		
	}
	
}

?>