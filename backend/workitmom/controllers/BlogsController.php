<?php

/**
 *	Blogs.
 */
class WorkitmomBlogsController extends ClientBackendController {
	
	/**
	 *	Blog order to set as 'not shown'.
	 */
	const BLOG_ORDER_NOT_SHOWN = 12;
	
	/**
	 *	Default view.
	 */
	public function view(){}

	/**
	 *	Set blog order for featured blogs.
	 */
	public function featured_blog_order(){
		
		/* Get all blogs. */
		$bloggersModel = $this->getModel('bloggers');
		$blogs = $bloggersModel->getBlogs(0, 0, $total, array(
			'order' => 'name',
			'direction' => 'asc'
		));
		
		/* Available categories to choose from */
		$availableCategories = array(
			'Balancing Act',
			'Career & Money',
			'Pregnancy & Parenting',
			'Your Business',
			'Just For You'
		);
		
		/* The "12" was carried over from old site. */
		$notShown = self::BLOG_ORDER_NOT_SHOWN;
		
		/* Set breadcrumbs */
		$breadcrumbs = BluApplication::getBreadcrumbs();
		//$breadcrumbs->add('Blogs', '/blogs/');
		$breadcrumbs->add('Blog Order', '/blogs/featured_blog_order/');
		
		/* Set page title */
		$this->_doc->setTitle('Blog Order');
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/blogs/featured_blog_order.php');
		
	}
	
	/**
	 *	Set blog order: submit.
	 */
	public function featured_blog_order_submit(){
		
		/* Get request. */
		$blogOrders = Request::getArray('order');
		$blogCategories = Request::getArray('category');
		
		/* Update... */
		$bloggersModel = $this->getModel('bloggers');
		if (Utility::iterable($blogOrders)){
			foreach($blogOrders as $blogID => &$order){
				$order = (bool) $bloggersModel->updateBlogOrder($blogID, $order);
			}
			unset($order);
		}
		if (Utility::iterable($blogCategories)){
			foreach($blogCategories as $blogID => &$category){
				$category = (bool) $bloggersModel->updateBlogCategory($blogID, $category);
			}
			unset($category);
		}
		
		/* Redirect */
		return $this->_redirect('/blogs/featured_blog_order/');
		
	}
	
}

?>