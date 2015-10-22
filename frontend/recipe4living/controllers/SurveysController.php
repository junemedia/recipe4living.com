<?php

/**
 * Slideshow Controller
 *
 * @package BluApplication
 * @subpackage FrontendControllers
 */
class Recipe4livingSurveysController extends Recipe4livingArticlesController
{

    /**
     *    Construct.
     */
    public function __construct($args)
    {
        /* Set variables */
        $this->_itemtype = 'survey';
        $this->itemtype_singular = 'survey';
        $this->itemtype_plural = 'survey';
   
        /* ItemsController constructor */
        parent::__construct($args);
    }
    
    public function details(){
        //echo "<pre>";
        
        $this->_baseUrl = '/surveys/details/'.implode('/', $this->_args);
        
        
        // Get models
        $itemsModel = BluApplication::getModel('items');
        $metaModel = BluApplication::getModel('meta');
        $userModel = BluApplication::getModel('user');
        Template::set('user', $userModel->getCurrentUser());

        // Extract slug
        $arg = $this->_args;       
        $slug = $arg[0];
        $this->_itemId = $itemsModel->getItemId($slug);


        if (!$item = $itemsModel->getItem($this->_itemId)) {
            return $this->_errorRedirect();
        }
        
        //print_r($item);exit;
        
        // Get the article slide page
        $surveyPage = $arg[1];
        // Get breadcrumbs
        $breadcrumbs = $this->_getBreadcrumbs();
        $this->_doc->setTitle('Survey Page');

        $itemsId = $itemsModel->getSurveyItemsById($this->_itemId);
        
        $itemsCount = count($itemsId);
        
        $surveySubmitPage = 1 + $itemsCount + 1;
        $thankyouPage = $surveySubmitPage + 1;
        
        // Switch the page
        
        //echo $surveyPage;
        
        switch($surveyPage){
            case 1:
                //This is the main page;
                include(BLUPATH_TEMPLATES.'/surveys/main.php');
                break;
            case $surveySubmitPage:
                // This is a survey submit page     
                foreach($itemsId as $id){
                    $recipes[$id] = $itemsModel->getItem($id);
                }  
                include(BLUPATH_TEMPLATES.'/surveys/surveysubmit.php');
                break;
            case $thankyouPage;
                //This is the thank you page
                
                if(isset($_POST['recipeSurvey']) && $vote_id = trim($_POST['recipeSurvey'])){
                    $result = $itemsModel->saveSurveyVote($vote_id, $this->_itemId);    
                }
                

                
                $voteResult = $itemsModel->getSurveyResult($this->_itemId);
                $total = array_sum($voteResult);
                
                foreach($voteResult as $voteId=>$r){
                    $v = $itemsModel->getItem($voteId);
                    $recipeTitles[$voteId] = $v['title'];
                    $recipeImages[$voteId] = $v['image']['filename'];
                }
                 
                include(BLUPATH_TEMPLATES.'/surveys/thankyou.php'); 
                break;
            default:
                // items page.
                $index = $surveyPage - 2;
                $surveyItemId = $itemsId[$index];
                $recipe = $itemsModel->getItem($surveyItemId);
                include(BLUPATH_TEMPLATES.'/surveys/item.php');
                break;
        }
        
        
        
        
        
    }  
    
	/**
	 * Display slideshow
	 */
	public function view()
	{
		//$this->_doc->setTitle('Slideshows');
		//include(BLUPATH_TEMPLATES.'/slideshows/view.php');
	}
	
}

?>
