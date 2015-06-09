<?php

/**
 *	Assets controller
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class Recipe4livingIngredientsController extends ClientBackendController
{
    /**
     *    Item view type
     *
     *    @access protected
     *    @var string
     */
    protected $_view = 'view';

    /**
     *    Requested base url
     *
     *    @access protected
     *    @var string
     */
    protected $_baseUrl = '/ingredients';
    
    /**
     *    Show all articles
     *
     *    @access protected
     *    @var bool
     */
    protected $_showAll = false;

    /**
     *    Show search form?
     *
     *    @access protected
     *    @var bool
     */
    protected $_showSearch = false;

    /**
     *    Current item id
     *
     *    @access protected
     *    @var string
     */
    protected $_itemId = null;
    
    /**
     *    Current item type
     *
     *    @access protected
     *    @var string
     */
    protected $_itemType = 'article';
    
    /**
     *    Current page number
     *
     *    @access protected
     *    @var int
     */
    protected $_page = 1;
    
    /**
     *    Menu slug
     *
     *    @access protected
     *    @var string
     */
    protected $_menuSlug = '/ingredients';
    
    /**
     *    Send confirmation email to item author when setting item live
     *
     *    @access protected
     *    @var bool
     */	
	//protected $_menuSlug = 'images';

	//private $folder;
	
	/**
	 * Base url
	 *
	 * @var string Base url
	 */
	//protected $_baseUrl = '/ingredients';

    public function __construct($args){
        parent::__construct($args);
        $userId = Session::get('UserID');
        if(!$userId){
            $this->_redirect('/../login');
        }
    }
    
	public function view()
	{
        include(BLUPATH_TEMPLATES.'/ingredients/list.php');
		
	}
    
    public function list_right()
    {
        
        // force to list only the unchecked items
        $status = "0";
        Session::set('filter_status', $status);
        
                
        $page = Request::getInt('page', 1);
        $viewall = ISBOT ? true : Request::getBool('viewall', false);
        $limit = 50;
        $offset = ($page - 1) * $limit;
        $format = $this->_doc->getFormat();
        
        
        
        // Get models
        $ingredientsrowModel = BluApplication::getModel('ingredientsrow');
        
        // Get the slug mappings
        $mappings = $ingredientsrowModel->getSlugMappings();
        //print_r($mappings);
        
        
        //$layoutBaseUrl = $baseUrl.$qsSep.'sort='.$sort.$searchBaseUrl.'&amp;page='.$page.'&amp;layout=';
         
            //$baseUrl . $qsSep . ($viewType == 'deleted' ? 'view=deleted&amp;' : '').'layout='.$layout.'&amp;sort='.$sort.$searchBaseUrl.'&amp;page=';
        $viewType = Request::getString('view');
        if ($viewType == 'latest') {
            $sort = 'date_desc';
        }               
        // Get pagination values
        $total = $ingredientsrowModel->getTotal();
        if ($viewType == 'latest') {
            $total = min($total, BluApplication::getSetting('numLatestItemsListing', $limit * 3));
        }
        if ($viewall) {
            $start = 1;
            $end = $total;
        } else {
            $start = $offset;
            $end = min($offset + $limit, $total);
        }

        // Get base URLs for listing updates
        $baseUrl = SITEURL . $this->_baseUrl;

        if ($viewType == 'latest') {
            $baseUrl .= '?view=latest';
            $qsSep = '&amp;';
        } else {
            $qsSep = '?';
        }       
        // Do pagination
        $paginationBaseUrl = $baseUrl . $qsSep . 'page=';
        $pagination = Pagination::simple(array(
            'limit' => $limit,
            'total' => $total,
            'current' => $page,
            'url' => $paginationBaseUrl
        ));
        
        
        $ingredients = $ingredientsrowModel->getList($start, $limit);

        include(BLUPATH_TEMPLATES.'/ingredients/items/ingredient.php');        
    }
    /**
     * Get sort order
     */
    protected function _getSort()
    {
        static $sort;
        
        if (!isset($sort)) {
            if ($this->_showSearch) {
                $sort = 'relevance';
            } else {
                 $sort = 'id';
                 $sort = Session::get('adminsort', $sort);
            }
            $sort = Request::getString('sort', $sort);
        }
        
        return $sort;
    }
    
    public function save()
    {
        $r = $_POST;
        $ingredientsrowModel = BluApplication::getModel('ingredientsrow'); 
        
        if(isset($_POST['search']) && $_POST['search'] == 'Go!')
        {
            $ingredients = $ingredientsrowModel->search($_POST['field'], $_POST['searchTerm']);   
            include(BLUPATH_TEMPLATES.'/ingredients/items/ingredient.php');
        }else{        
            $ingredientsrowModel->update($r);
            $this->_redirect('/ingredients?page=' . $_GET['page']);
        }
    }
    
    public function filter_status()
    {
        //$status = $_GET['status'];
        
        // force to list only the unchecked items
        $status = 0;
        Session::set('filter_status', $status);
        $this->_redirect('/ingredients');
    }
    
    public function default_value_list()
    {
        $page = Request::getInt('page', 1);
        $viewall = ISBOT ? true : Request::getBool('viewall', false);
        $limit = 50;
        $offset = ($page - 1) * $limit;
        $format = $this->_doc->getFormat();
        
        
        
        // Get models
        $ingredientsrowModel = BluApplication::getModel('ingredientsrow');
        
        //$layoutBaseUrl = $baseUrl.$qsSep.'sort='.$sort.$searchBaseUrl.'&amp;page='.$page.'&amp;layout=';
         
            //$baseUrl . $qsSep . ($viewType == 'deleted' ? 'view=deleted&amp;' : '').'layout='.$layout.'&amp;sort='.$sort.$searchBaseUrl.'&amp;page=';
        $viewType = Request::getString('view');
        if ($viewType == 'latest') {
            $sort = 'date_desc';
        }               
        // Get pagination values
        $total = $ingredientsrowModel->getDefaultValueTotal();
        if ($viewType == 'latest') {
            $total = min($total, BluApplication::getSetting('numLatestItemsListing', $limit * 3));
        }
        if ($viewall) {
            $start = 1;
            $end = $total;
        } else {
            $start = $offset;
            $end = min($offset + $limit, $total);
        }

        // Get base URLs for listing updates
        $baseUrl = SITEURL . $this->_baseUrl . '/default_value_list';

        $searchTerm = Request::getString('q');
        
        if ($searchTerm != false) {
            $baseUrl .= '?q=' . $searchTerm;
            $qsSep = '&amp;';
        } else {
            $qsSep = '?';
        }       
        // Do pagination
        $paginationBaseUrl = $baseUrl . $qsSep . 'page=';
        $pagination = Pagination::simple(array(
            'limit' => $limit,
            'total' => $total,
            'current' => $page,
            'url' => $paginationBaseUrl
        ));
        
        $noteswords = file_get_contents(BLUPATH_BASE . "/leon/notes.words.txt");
        
        $ingredients = $ingredientsrowModel->getDefaultValueList($start, $limit);

        include(BLUPATH_TEMPLATES.'/ingredients/items/default_value.php');
    }
    
    public function default_value_save()
    {
        $r = $_POST;
        $ingredientsrowModel = BluApplication::getModel('ingredientsrow');
         
        if(isset($r['add_new_ingredient']) && $r['add_new_ingredient'] == 'Save'){
            // Add a new ingredient
            
            $ingredientsrowModel->add_new_ingredient($r);
            $this->_redirect('/ingredients/default_value_list?page=' . $_GET['page']);
        }else{
   
            
            $ingredientsrowModel->default_value_update($r);
            
            $this->_redirect('/ingredients/default_value_list?page=' . $_GET['page']);
        }
    }
    
    public function getSearch()
    {
        $searchString = $_POST['searchTerm'];
        $ingredientsrowModel = BluApplication::getModel('ingredientsrow');
        
        $row = new ingredientRow($searchString);
        
        $title = '';
        $title .= "[Original input] => " . $row->getRowStr() . "\r\n";
        $title .= "[Quantity] => " . $row->getQuantity() . "\r\n";
        $title .= "[Quantity Number] => " . $row->getQuantityNumber() . "\r\n";
        $title .= "[Measurement] => " . $row->getMeasurement() . "\r\n"; 
        $title .= "[Measurement description] => " . $row->getMeasurementsDesc() . "\r\n";
        $title .= "[Ingredient search raw] => " . $row->getIngredientRaw() . "\r\n";
        $title .= "[common words] => " . $row->getNotesWordsStr() . "\r\n";
        $title .= "[notes words] => " . $row->getNotesStr() . "\r\n";
        $title .= "[All notes words] => " . $row->getAllNotes() . "\r\n";
        $title .= "[Ingredient feedBack] => " . print_r($row->getIngredient(), true);
        
        print_r($title);
        
    }
    
    public function updateNotes()
    {
        $notes = $_POST["notes"];
        $file = BLUPATH_BASE . '/leon/notes.words.txt';
        $fp = fopen($file, 'w');
        fwrite($fp, $notes);
        fclose($fp);
    }
}

?>
