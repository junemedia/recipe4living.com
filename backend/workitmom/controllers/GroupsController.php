<?php

/**
 *	Groups admin.
 */
class WorkitmomGroupsController extends ClientBackendController {
	
	/**
	 *	Default overview page.
	 */
	public function view(){
		
		/* Set breadcrumbs */
		$breadcrumbs = BluApplication::getBreadcrumbs();
		$breadcrumbs->add('Groups', '/groups/');
		
		/* Set page title */
		$this->_doc->setTitle('Groups');
		
		/* Load template */
		//include(BLUPATH_TEMPLATES.'/groups/landing.php');
		
	}
	
	/**
	 *	Group posts overview page.
	 */
	public function posts(){
		
		/* Set breadcrumbs */
		$breadcrumbs = BluApplication::getBreadcrumbs();
		$breadcrumbs->add('Groups', '/groups/');
		$breadcrumbs->add('Posts', '/groups/posts/');
		
		/* Set page title */
		$this->_doc->setTitle('Group posts');
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/groups/posts.php');		
		
	}
	
	/**
	 *	Group posts listing.
	 */
	public function posts_listing(){
	
		/* Get parameters */
		$limit = BluApplication::getSetting('backendListingLength');
		$page = Request::getInt('page', 1);
		
		/* Get model */
		$groupsModel = $this->getModel('groups');
		
		/* Prepare sort */
		$options = array();
		
		/* What to sort reports by */
		$sort = strtolower(Request::getCmd('sort'));
		if (!in_array($sort, array('id', 'poster', 'text', 'date', 'reports'))){
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
		$posts = $groupsModel->getAllPosts(($page - 1) * $limit, $limit, $total, $options);
		
		/* Paginate */
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => '?sort='.$sort.'&amp;direction='.$direction.'&amp;page='
		));
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/groups/posts_listing.php');
		
	}
	
	/**
	 *	Group posts listing - individual post.
	 */
	public function posts_listing_individual($post){
		
		/* Styling */
		static $alt = false;
		$alt = !$alt;
		$row = $alt ? 'odd' : 'even';
		
		$priority = 'normal';
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/groups/posts_listing/individual.php');
		
	}
	
	/**
	 *	Post details page.
	 */
	public function posts_details(){
		
		/* Get post */
		$args = $this->_args;
		if (!Utility::iterable($args)){
			return $this->_redirect('/groups/posts/');
		}
		$postId = (int) array_shift($args);
		
		/* Get model */
		$groupsModel = $this->getModel('groups');
		
		/* Get post */
		$post = $groupsModel->getPost($postId);
		if (empty($post)){
			return $this->_redirect('/groups/posts/', 'Post #'.$postId.' not found.', 'error');
		}
		
		/* Add breadcrumbs */
		$breadcrumbs = BluApplication::getBreadcrumbs();
		$breadcrumbs->add('Groups', '/groups/');
		$breadcrumbs->add('Posts', '/groups/posts/');
		$breadcrumbs->add('Post #'.$post['id'], '/groups/posts_details/'.$post['id'].'/');
		
		/* Set page title */
		$this->_doc->setTitle('Post #'.$post['id'].' (Group #'.$post['group'].')');
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/groups/posts_details.php');
		
	}
	
	/**
	 *	Delete a post.
	 */
	public function posts_delete(){
		
		/* Get post ID */
		$postId = Request::getInt('post');
		if (!$postId){
			return $this->_redirect('/groups/posts/');
		}
		
		/* Get model */
		$groupsModel = $this->getModel('groups');
		
		/* Delete post */
		$deleted = $groupsModel->deletePost($postId);
		
		/* Return */
		if ($deleted){
			return $this->_redirect('/groups/posts/', 'Post #'.$postId.' deleted.');
		} else {
			return $this->_redirect('/groups/posts/', 'Post #'.$postId.' could not be deleted.', 'error');
		}
		
	}
	
}

?>