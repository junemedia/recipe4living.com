<?php

/**
 * Slideshow Controller
 *
 * @package BluApplication
 * @subpackage FrontendControllers
 */
class Recipe4livingSlidearticlesController extends Recipe4livingArticlesController
{

    /**
     *    Construct.
     */
    public function __construct($args)
    {
        /* Set variables */
        $this->_itemtype = 'slidearticle';
        $this->itemtype_singular = 'Slideshow';
        $this->itemtype_plural = 'Slideshows';

        /* Add breadcrumb */
        BluApplication::getBreadcrumbs()->add('De-Stress', '/destress/');
        
        /* ItemsController constructor */
        parent::__construct($args);
    }
    
    public function details(){
        
        $this->_baseUrl = '/slidearticles/details/'.implode('/', $this->_args);
        
        // Get models
        $itemsModel = BluApplication::getModel('items');
        $metaModel = BluApplication::getModel('meta');
        $userModel = BluApplication::getModel('user');
        Template::set('user', $userModel->getCurrentUser());

        // Extract slug
        $arg = $this->_args;       
        $slug = $arg[0];
		
		echo '<div id="tracking" style="display:none;">';
		print_r($arg);
		echo '</div>';

		
		
        $this->_itemId = $itemsModel->getItemId($slug);

        if (!$item = $itemsModel->getItem($this->_itemId)) {
            return $this->_errorRedirect();
        }

        
        // Get the article slide page
        $slidePage = $arg[1];
		if(empty($slidePage))
		{
			$slidePage = 1;
		}
        
        // Get breadcrumbs
        $breadcrumbs = $this->_getBreadcrumbs();
        $this->_doc->setTitle('Slidearticles');
        
		//if($slidePage == 1)
		//{
			// Increase view count for parent slide article
			$itemsModel->incrementViews($this->_itemId);
		//}
		
        // Get all the details information of the slide pages.
        $slideAll = $itemsModel->getSlideArticleByArticleId($this->_itemId);
        
        // Get the current slide details information
        $pages = $itemsModel->getSlideArticleByOrder($this->_itemId, $slidePage);
        
        if(!$pages){
            return $this->_redirect('/slidearticles/details/' . $arg[0] . '/1');
        }
        // Get total numbers of the slide
        $totalSlideNum = count($slideAll);
        
        if (!$item = $itemsModel->getItem($this->_itemId)) {
            return $this->_errorRedirect();
        }
		
		if (!isset($pages['id']) || empty($pages)) {
            return $this->_errorRedirect();
        }
        
        $readerloved = $itemsModel->getReaderLovedItems($pages['id'],array_keys($pages['related']),false, 6);        
        
        //echo "<pre>";
           // print_r($item);
            //print_r($pages);
            //print_r($slidePage);
        //echo "</pre>";

       /**
        *  Ok. Let's make it display this way:
        * 1-5 – when you get to 5 and click next, it loads up:
            6-10 – when you get to 10 and click next, it loads up: 
            11-15 – when you get to… 
            If the display is showing: 
            11-15 and you are on number 11 and click previous – it should load:
            6-10 – If you are on number 6 and click previous – it should load:
            1-5 – if you are on 1 and click previous – it should just keep you on #1. 
        */
        
        //print_r($slideAll);
        
        
        $currentPageOrder = $itemsModel->getSlideOrderBySlidePageArticleId($item['id'], $pages['id']);
		
		// Increase view count for slide article
        $itemsModel->incrementViews($pages['id']);
        
        // In the current page position 
        $sequence = $currentPageOrder%5;
        if ($sequence == 0) {
            // OMG, we are on the 5th item
            $sequence = 5;
        }
                                
        // the first item sequence
        $start = $currentPageOrder - $sequence;
                         
        // In case we are on the first page
        if($start < 0) $start = 0;                                           
        //if($end < 5) $end = 5;
        
        
        $slideAll = array_slice($slideAll, $start, 5);
        

        $pre =  ($currentPageOrder - 1) < 1? 1: ($currentPageOrder - 1);
        $next = ($currentPageOrder + 1) > $totalSlideNum ? 1: ($currentPageOrder + 1);
        
        $pageLinkPre = '/slidearticles/details/'. $arg[0] . '/' . $pre;
        $pageLinkNext = '/slidearticles/details/' . $arg[0] . '/' . $next;
        
        include(BLUPATH_TEMPLATES.'/slidearticles/details.php');
    }  
    
	/**
	 * Display slideshow
	 */
	public function view()
	{
		$this->_doc->setTitle('Slideshows');
		include(BLUPATH_TEMPLATES.'/slideshows/view.php');
	}
    
    public function slideshowlist(){
        //$this->_redirect('articles');
    }
	
}

?>
