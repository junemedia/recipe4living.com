<?php

/**
 *	Ingredients Row Object
 *
 *	@package Leon Package
 *	@subpackage SharedModels
 *  @author Leon Zhao
 *  @desc Since we will use the ingredients row everywhere and let's use object to make use of it in anyway we want
 */
 
 
 
class ingredientRow
{
    private $_solr;
    public $solr_escape;
    private $_rowStr;
    private $_rawData;
    private $_rawDataArray;
    private $_typeList;
    private $_rowType;
    private $_quantity;
    private $_meansurement;
    private $_meansurementRaw;
    private $_ingredient;
    private $_ingredientRaw;
    private $_quantityNumber = false;
    private $_measurementsDesc = false;
    private $_rowMeasurementType = false;
    private $_notes = false;
    private $_notesWords = false;
    private $_defaultValueArray = false;
    private $_rowMeasurementTypeArray = array(
                    "non-standard measurements with specified quantities", // 2 2/3 lb pkg. eggs -> 2 * (2/3 lb.) pkg. eggs  
                    "non-standard measurements without specified quantites", // 2 pkg. eggs -> 2 | pkg.| egg
                    "standard measurements with speicified quantities", // 2 1/2 tsp. salt -> 2 + 1/2 | tsp.| salt OR 2 tsp. salt
                    "standard measurements without specified quantities", // this does not exist
                    "range quantities" // 5-6 cups milk
                );
    
    public function __construct($ingredientRowRawData)
    {
        $this->_rowStr = $ingredientRowRawData;
        $t = $this->_rawData = strtolower(trim($ingredientRowRawData));
        $t1 = $this->_replaceMeasurementsRawArrayMatch();
        $this->_rawData = str_replace(array(","), ' ', $this->_rawData);

        $this->_rawData = str_replace($this->getNotes(), '', $this->_rawData); 
        $this->_rawData = str_replace($this->getNotesWords(), '', $this->_rawData);       
        $this->_rawData = str_replace('(', '', $this->_rawData);
        $this->_rawData = str_replace(')', '', $this->_rawData);
        
        if($this->_measurementsDesc)$this->_rawData = str_replace($this->_measurementsDesc, '', $this->_rawData);
                
        $this->_rowMeasurementType = $this->getMeasurementRowType();       
        $this->_measurementsDesc = $this->_getMeasurementsDesc();

        if($this->_rowMeasurementType == 0){
            // 2 8 oz pkg. beef
            // remove the measurement description first
            $mdes = $this->getMeasurementsDesc();
            $this->_rawData = str_replace($this->getMeasurementsDesc(), "", $this->_rawData);
        }       
        
        //echo $t . " => $t1 => $t2 => " . $this->_rawData. "<br>";
        //$this->_rawData = str_replace(array('stick','sticks'), 'sticks (8 tsp.)', $this->_rawData);
        $this->_rawDataArray = explode(" ", $this->_rawData);
        // Get reference to global database object
        //$this->_db = BluApplication::getDatabase();

        // Get reference to global cache object
        //$this->_cache = BluApplication::getCache();
        
        require_once(BLUPATH_BASE . '/leon/solr/solr.config.ingredient.php');
        global $solr_ingredient;
        $this->_solr = $solr_ingredient;
        $this->solr_escape = array(':', ',',  '+', '-', '&&', '||', '!', '(', ')', '<p>','</p>', '<br />',
                            '{', '}', '[', ']', '^', '"', '~', '*', '?', '\'', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');

       
    } 
    /*
     * Define the row types, casue there are all kinds of possiblilities
     * Access private
     */
    private function _getRowType()
    {
        $returnType = false;
        if(strpos($this->_rawData, ":") !== false) {
            $returnType = 0;                               // section header: Pizza Sauce
        }else if($this->getQuantity() == false && $this->getMeasurement() == false) {
            $returnType = 1;    // no measurements: 2 eggs
        }else if($this->getQuantity() !== false && $this->getMeasurement() == false) {
            $returnType = 2;   // missing measurements tomatoes
        }else if($this->getQuantity() !== false && $this->getMeasurement() !== false) {
            $returnType = 3;  // normal data
        }
        return $returnType;     
    }
    
    /*
     * fetch the quantity in the given ingredient row
     * Access private
     */
    private function _getQuantity()
    {
        return $this->_getQuantityRaw();
    }

    private function _getQuantityRaw()
    {        
        // 2 lb. beef
        // 2 1/2 lb. beef
        // tomatoes
        // 2 8-oz lb. beef (8-oz.) has already been removed from the $this->_rawData;
        $count = preg_match("/([.]|[\/]|[0-9])+(\-|\ )+([.]|[\/]|[0-9]){0,5}/", $this->_rawData, $match);
        if($count > 0)
        {
            return $match[0];
        }else{
            return false;
        }

    }
    
    private function _getQuantityMappingList()
    {
        $array = array(
                    "a"=>'1', '1/2'=>"0.5", 'half'=>'0.5'
                    );
        return $array; 
    }
    
    /*
     *find the meansurement in the given raw ingredient Row
     * Access private
     */
    private function _getMeasurementRaw()
    {
        $return = false;
        $measurements = $this->getMeasurementList();
        foreach ($this->_rawDataArray as $k=>$v)
        {
            $v = trim($v);
            if(array_key_exists($v, $measurements)){
                $return = $measurements[$v];
            }
        }
        return $return;
    }
    
    private function _replaceMeasurementsRawArrayMatch()
    {
        $return = false;
        $measurements = $this->getMeasurementList();
        $temp = explode(" ", $this->_rawData);
        foreach ($temp as $k=>$v)
        {
            if(array_key_exists($v, $measurements) !== false){
                $this->_rawData = str_replace(' ' .$v . ' ', ' ' . $measurements[$v] . ' ', $this->_rawData);
                //echo "str_replace($v, $measurements[$v], $this->_rawData);";
            }
        }
        return $this->_rawData;
    }
    
    /*
     *find the meansurement in the given raw ingredient Row
     * Access private
     */
    private function _getMeasurement()
    {
        $measurements = $this->getMeasurementList();
        $raw = $this->getMeasurementRaw();
        if($raw){
            return $measurements[$raw];
        }else{
            return false;
        }
    }
    
    /*
     * Find the ingredient in the given raw ingredient Row
     * Access private
     *
     */
    private function _getIngredientRaw()
    {
        $ingredient = $this->_rawData;
        if($this->getQuantity() !== false)$ingredient = preg_replace('/' . str_replace('/', '\/', ($this->getQuantity())) . '/', "", $ingredient, 1); 
        if($this->getMeasurementRaw())$ingredient = preg_replace('/' . $this->getMeasurementRaw() . '/', "", $ingredient, 1);
        return trim($ingredient);
    }



    private function _solrSearchIngredientsRaw($limit = 1)
    {
        $results = false;
        if($this->getRowType() === 0){
            // This is Section Header such as Pizza Sauce:
        }else if(!trim($this->getIngredientRaw())){
            // This is an empty content ingredient    
        }else{ 
            $foundDefaultValue = $this->_solrSearchDefaultValue($this->getIngredientRaw());
            $foundAllIngredients = $this->_solrSearchAll($this->getIngredientRaw(), $limit);
            $results = false;
            if($foundDefaultValue){
                $results = $foundDefaultValue;
                //echo 'Found Default';
            }else if($foundAllIngredients){
                //echo 'Found Solr';
                $results = $foundAllIngredients;
            }else{
            } 
        }
        return $results;
    }
    
    private function _solrSearchDefaultValue($query)
    {
        $results_defaultValue = false;
        // search via pre-search first:
        $query = str_replace($this->solr_escape, ' ', $query);
        $sql = "SELECT * FROM `ingredients_default_value` WHERE `default_value` LIKE '" . $query . "'"; 
        //echo $sql . '<br>';
        $r = mysql_query($sql);
        if(mysql_num_rows($r) > 0 )
        {
            while($row = mysql_fetch_assoc($r)){
                $search_NDB_No = "NDB_No:(\"" . $row['NDB_No'] . "\")";
                $results_defaultValue = $this->_solr->search($search_NDB_No, 0, 1);
                //echo $row['NDB_No'];
                // only loop once
                break;
            }                 
        }else{
            $NDB_No = $this->getMultiValue($query);
            //var_dump($NDB_No);
            if($NDB_No ){
                $search_NDB_No = "NDB_No:(\"$NDB_No\")";
                $results_defaultValue = $this->_solr->search($search_NDB_No, 0, 1);                
            }
        } 
        return $results_defaultValue;
        
    }
    
    public function getMultiValue($query){
        $key = false;
        if($this->_defaultValueArray === false){
            $sql = "SELECT * FROM `ingredients_default_value` WHERE `default_value` LIKE '%,%'";
            //echo $sql . '<br>';
            $r = mysql_query($sql);
            $result = array();

            while($row = mysql_fetch_assoc($r)){
                $rawRow = explode(',', strtolower($row['default_value']));
                foreach($rawRow as $k=>$v){
                    $result[$row['NDB_No']][$k] = trim($v);
                }
            }
            $this->_defaultValueArray = $result;
        }
        //print_r($this->_defaultValueArray);
        $return = false;
        foreach($this->_defaultValueArray as $key=>$id_array){
           //if($key == 6150 && $query = 'barbecue sauce'){
               $a = array_search(trim($query),$id_array);
               //var_dump($a); 
               //echo "array_search($query," . print_r($id_array,true) . ")<br>";
               //echo 'ss';
               //exit;
           //}
            
            if(array_search($query,$id_array) !== false){
                
                $return = $key;
                break;
            }
        }
        $key = $return;
        // loop does not found in the end
        return $key; 
    }

    private function _solrSearchAll($query, $limit = 5)
    {
        $query = str_replace($this->solr_escape, ' ', $query);
        if(trim($query)){
            $fields = array("Feed_Back", "Long_Desc");
            $searchRaw = "";
            //foreach($fields as $field)
            //{
            //    $searchRaw .= $field . ":(+" . trim($query) . "*) AND ";
            //}
            $query = explode(' ', $query);
            $searchTerm = '';
            foreach($query as $unit){
                if(trim($unit) !== '')
                {
                    $searchTerm .= trim($unit) . "~ ";
                }
            }
            
            
            $searchRaw = "Feed_Back:($searchTerm) 
                          Or 
                          Long_Desc:($searchTerm)";
            //echo $searchRaw;
            //get ride of the last 'or '
            //$searchRaw = substr($searchRaw, 0, -3);        

            //echo $searchRaw . "<br />";
            $results = $this->_solr->search($searchRaw, 0, $limit);
            if(count($results->response->docs) > 0){
                return $results;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }    
    
    public function getQuantity()
    {
        if(!$this->_quantity){
            $this->_quantity = $this->_getQuantity();
        }
        return $this->_quantity;
    }

    public function getIngredientRaw()
    {
        if(!$this->_ingredientRaw){
            $this->_ingredientRaw = $this->_getIngredientRaw();
        }
        return $this->_ingredientRaw;
    }
    
    public function getIngredient()
    {
        $foundIngredient = $this->_solrSearchIngredientsRaw();
        $results = false;
        if($foundIngredient){
            foreach($foundIngredient->response->docs as $key => $doc)
            {
                $results["NDB_No"] = $this->valueEscape($doc->getField('NDB_No'));
                $results["Feed_Back"]      = $this->valueEscape($doc->getField('Feed_Back'));
                $results["Long_Desc"]           = $this->valueEscape($doc->getField('Long_Desc'));
                break;
            }
        }
        return $results;        
    }
    
    public function getIngredientList($limit = 5)
    {
        // For default search fields function only
        $results = false;
        if($this->getRowType() == 0){
            // This is Section Header such as Pizza Sauce:
        }else{
            $foundDefaultValue = $this->_solrSearchDefaultValue($this->getIngredientRaw());
            $foundAllIngredients = $this->_solrSearchAll($this->getIngredientRaw(), $limit);
            $results = false;
            $i = 0;
            if($foundDefaultValue){
                if($foundDefaultValue){
                    
                    foreach($foundDefaultValue->response->docs as $key => $doc)
                    {
                        $results[$i]["NDB_No"] = $this->valueEscape($doc->getField('NDB_No'));
                        $results[$i]["Feed_Back"]      = $this->valueEscape($doc->getField('Feed_Back'));
                        $results[$i]["Long_Desc"]           = $this->valueEscape($doc->getField('Long_Desc'));
                        $i++;
                    }
                }
            }
            if($foundAllIngredients){
                foreach($foundAllIngredients->response->docs as $key => $doc)
                {
                    $results[$i]["NDB_No"] = $this->valueEscape($doc->getField('NDB_No'));
                    $results[$i]["Feed_Back"]      = $this->valueEscape($doc->getField('Feed_Back'));
                    $results[$i]["Long_Desc"]           = $this->valueEscape($doc->getField('Long_Desc'));
                    $i++;
                }
            } 
            return $results;
        }      
    }
    
    public function getMeasurementRaw()
    {
        if(!$this->_meansurementRaw){
            $this->_meansurementRaw = $this->_getMeasurementRaw();
        }
        return $this->_meansurementRaw;
    }
    
    public function getMeasurement()
    {
        if(!$this->_meansurement){
            $this->_meansurement = $this->_getMeasurement();
        }
        return $this->_meansurement;
    }
    
    public function getMeasurementList()
    {
        $measurements = Array (
                't.' => 'tsp.', 't' => 'tsp.', 'tsp.' => 'tsp.', 'tsp' => 'tsp.', 'teaspoon' => 'tsp.', 'teaspoons'=> 'tsp.', 'tsp.'=>'tsp.', 'Tspn.'=> 'tsp.',
                'tbs.' => 'Tbs.', 'tbsp' => 'Tbs.', 'tbs' => 'Tbs.', 'tbsp.' => 'Tbs.', 'tablespoon'=>'Tbs.', 'Tbs.'=>'Tbs.','Tbls'=>'Tbs', 
                'cup' => 'C.', 'cup of'=>'C.','c.' => 'C.', 'cup' => 'C.', 'cups' => 'C.', 'c' => 'C.', 'C.'=>'C.',
                'oz.' => 'oz.', 'oz' => 'oz.', 'ounce' => 'oz.', 'ounces' => 'oz.', 'oz.'=>'oz.',
                'fl. oz.' => 'fl. oz.', 'fl oz' => 'fl. oz.', 'fluid ounce' => 'fl. oz.', 'fluid ounces' => 'fl. oz.','fl. oz.'=>'fl. oz.',
                'quarts' => 'qts.',
                'g.' => 'g.', 'g' => 'g.', 'grammes' => 'g.', 'grams' => 'g.', 'g.' => 'g.', 
                'lb' => 'lb.', 'lb.' => 'lb.', 'lbs' => 'lbs.', 'lbs.' => 'lbs.', 'pound' => 'lb.', 'pounds' => 'lbs.', 'lbs.' => 'lbs.',
                'packages' => 'pkgs.', 'package' => 'pkg.', 'pkg' => 'pkg.', 'pkg.' => 'pkg.', 'pkgs' => 'pkgs.', 'pkgs.' => 'pkgs.', 'container' => 'pkg.', 'bag' => 'pkg.', 'package' => 'pkg.',
                'pt.' => 'pt.','pint' => 'pt.','pt.' => 'pt.',
                'liter' => 'ltr.','liters' => 'ltr.', 'ltr' => 'ltr.', 'ltrs' => 'ltr.', 'ltr' => 'ltr.',
                'milliliter' => 'ml.','milliliters' => 'ml.', 'ml' => 'ml.', 'mls' => 'ml.', 'ml.' => 'ml.',
                'dash' => 'dash', 'pinch' => 'pinch', 'oz.' => 'oz.',
                'qt' => 'qt.', 'qt.' => 'qt.', 'quart' => 'qt.',  'qt.' => 'qt.',
                'cans'=>'cans', 'can'=> 'can',
                'med.'=>'med.',
                'big'=>'lg.', 'large'=>'lg.', 'lg.'=>'lg.',
                'small'=>'sm.','sm.'=>'sm.',
                'stick'=>'stick', 'sticks'=>'sticks',
                'gallon'=>'gal.', 'gallons'=>'gal.', 'gal.'=>'gal.', 'gals.'=>'gal.', 'gal'=>'gal.',  'gal.'=>'gal.', 
                'bottle'=>'btl.', 'bottles'=>'btl.', 'btl'=>'btl.', 'btls'=>'btl.', 'btl.'=>'btl.', 'btls.'=>'btl.', 'btl.'=>'btl.',
                'handful'=>'handful',
                'box'=>'box',
                'boxes' =>'boxes',
                'slice'=>'slice',
                'slices' => 'slices',
                'jar'=>'jar',
                'bag'=>'bag',
                'bags' => 'bags',
                'clove' => 'clove',
                'dozen' => 'dozen',
                'block'=>'block',
                'wedges'=>'wedges',
                'ribs'=>'ribs'
                    );
                    
        return $measurements;
    }

    public function getNonstandardMeasurementsList()
    {
        $nonStandardMeasurementsList = array(
                    "bottle",
                    "can",
                    "cans", 
                    "pkg.",
                    "pkgs.",
                    "package",
                    "packages",
                    "handful",
                    "handfuls", 
                    "bunch",
                    "bunches",
                    "box",
                    "slice",
                    "slices",
                    "jar",
                    "jars", 
                    "bag",
                    "bags",
                    "container",
                    "fillet",
                    "fillets",
                    "boxes",
                    "btl.",
                    "btls.",
                    "bottles",
                    "containers",
                    "ctr",
                    "block",
                    "ctrs"
        );
        return $nonStandardMeasurementsList;
    }    
    
    private function _getMeasurementsDesc()
    {
        if($this->_measurementsDesc === false){
            $this->_measurementsDesc = "";
            if($this->_rowMeasurementType == 0){
                // 2 8 oz pkg. beef
                $count = preg_match("/([.]|[\/]|[0-9])+(\-|\ )+(oz.|lb.|ounces)/", $this->_rawData, $matchDesc);
                if($count){
                    $this->_measurementsDesc = $matchDesc[0];
                }else{
                    $this->_measurementsDesc = "";
                }
                //echo $description . "<br>";
            }
            
        }
        
        return $this->_measurementsDesc;
    }


        
    public function getRowType()
    {
        if(!$this->_rowType){
            $this->_rowType = $this->_getRowType();
        }
        return $this->_rowType;
    }   
    
    public function getTypeList()
    {
        if(!$this->_typeList)
        {
            // We will do it later perhaps. Save it here for now.
            $this->_typeList = array(
                "sectionHeader",            // Pizza preparation:
                "noMeasurements",           // 2 eggs
                "missingMeasurements",      // 1 lib. beef -> entered 1 beef
                "normal",                   // The common case, such as 1 lib. beef raw, light baked
                "others"                    // We don't know what to do. Category this whatever it is.
            );
        }
        return $this->_typeList;           
    }


    Public function valueEscape($value)
    {
        if(!is_array($value)){
            return htmlspecialchars($value, ENT_NOQUOTES, 'utf-8') ;
        }else{
            return $value['value'];
            //return implode(",", $value);
        }
    }
    
    public function getIngredientContentByKey($key)
    {
        $r = $this->getIngredient();
        if($r && trim($key)!== "" && array_key_exists($key, $r)){
            return $r[$key];
        }else{
            return false;
        }
    }
    
    public function getMeasurementsDesc()
    {
        return $this->_measurementsDesc;
    }

    public function getMeasurementRowType()
    {
        if($this->_rowMeasurementType === false){
            $this->_rowMeasurementType = $this->_getMeasurementRowType();
        }
        return $this->_rowMeasurementType;
    }
    
    private function _getMeasurementRowType()
    {
        $rawDataArray = explode(' ', $this->_rawData);
        $nonStandardMeasurements = $this->getNonstandardMeasurementsList();
        $standardMeasurements = $this->getMeasurementList();
        $nonStandard = false;
        $standard = false;
        foreach($rawDataArray as $v)
        {
            if(array_search($v, $nonStandardMeasurements) !== false){
                $nonStandard = $v;
            }
            
            if(array_search($v, $standardMeasurements) !== false){
                $standard = $v;
            }
        } 
        //echo "Non-Standard:$nonStandard, Standard:$standard<br>"; 
        
        if($nonStandard){
            // The non-standard measurement found and specified with measurments
            // 1 8 oz pkg. beef
            $this->_rowMeasurementType = 0;
        }else if($nonStandard && $standard == false){
            // The non-standard measurements found but did not specified the measurements
            // 1 pkg. beef
            $this->_rowMeasurementType = 1;
        }else{
            // The standard measurements found
            // 1 lb beef
            // 1 1/2 lb. beef
            $this->_rowMeasurementType = 2;
        }
        
        return $this->_rowMeasurementType;
    }

    public function getRowStr()
    {
        return $this->_rowStr;
    }
    public function getQuantityNumber()
    {
        if($this->_quantityNumber === false){
            $quantityNumber = false;
            $quantity = $this->getQuantity();
            if(strpos($quantity, '-') !== false){
                // range numbers such as 5-6
                $number = preg_match("/([.]|[\/]|[0-9])+/", $quantity, $match);
                if($number > 0){
                    $quantityNumber = $match[0];
                }else{
                    $quantityNumber = 0;
                }
            }else{
                // non-range numbers
                $numbers = explode(' ', $quantity);
                foreach($numbers as $no){
                    if(strpos($no, '/')){
                        $fraction = explode('/', $no);
                        $no = $fraction[0]/$fraction[1];
                    }
                    $quantityNumber = $quantityNumber + round($no,2);
                }
            }
            $this->_quantityNumber = $quantityNumber;
        }
        // force to 1 for zero result
        //if($this->_quantityNumber == 0) $this->_quantityNumber = 1;
        return $this->_quantityNumber;
    }

    public function getNotes()
    {
        if($this->_notes === false){
            $this->_notes = "";
            $count = preg_match_all("/\(([^()]+|(?R))*\)/", $this->_rawData, $matchDesc);
            if($count > 0){
                // may be (8 oz.) or maybe (well prepared)
                $str = $matchDesc[1][0];
                $match_measurement_description = preg_match('/([.]|[\/]|[0-9])+(\-|\ )+(oz.|lb.|oz|lb|ounces|in.|inches|inch)/', $str, $match);
                //echo $str;
                if(count($match) > 0){
                    $this->_measurementsDesc = $matchDesc[1][0];
                    // If we found 8 oz for the first params, just remove it
                    $matchDesc[1][0] = "";
                    $matchDesc[0][0] = "";
                    
                }
                //echo '<pre>';
                //print_r($matchDesc);
                //echo '</pre>';
                foreach($matchDesc[0] as $items){
                    if(trim($items) != false)
                    $this->_notes []= $items;
                }
            }
        }
        return $this->_notes;
    }
    
    public function getNotesStr()
    {
        if($this->_notes && count($this->_notes) > 0){
            return implode(' ', $this->_notes);
        }else{
            return false;
        }
    }
    
    public function getNotesWordsArray()
    {
        $file = BLUPATH_BASE . '/leon/notes.words.txt';
        $words = strtolower(file_get_contents($file));
        $words = str_replace(array("\n","\r"), '', $words);
        $notesWords = explode(',', $words);
            //$notesWords = array();
            
        //print_r($notesWords);
        return $notesWords;
    }
    
    public function getNotesWords()
    {
        if($this->_notesWords === false){
            $this->_notesWords = "";
            $notesWords = $this->getNotesWordsArray();
            $rowArray = explode(' ', $this->_rawData);
            $return = false;
            
            $this->_rawData .= ' ';  // We need to add the white space at the end of the search string because we want to automatically match ' ' + phrase + ' '
           
            foreach($notesWords as $phrase){
                if(strpos($this->_rawData, ' ' . $phrase . ' ') !== false){
                    $return[] = trim($phrase);
                    $this->_rawData = str_replace($phrase, '', $this->_rawData);    
                }
            }
            
             /*
            foreach($rowArray as $str){
                if(array_search(trim($str), $notesWords) !== false){
                    $return[] = trim($str);
                }
            }
            */
            $this->_notesWords = $return;
        }
        return $this->_notesWords;
    }
    
    public function getNotesWordsStr()
    {
        if($this->_notesWords && count($this->_notesWords) > 0){
            return implode(' ', $this->_notesWords);
        }else{
            return false;
        }
    }

    public function getAllNotes()
    {
        return $this->getNotesStr() . ' ' . $this->getNotesWordsStr();
    }
    
    /**
    * @desc To handle all the special cases
    * @author Leon Zhao
    * @example cloves garlic and 1 lb. cloves
    */
    private function _specialCase()
    {
        $this->_handleCloves();
    }
    
    private function _handleCloves()
    {
        $specialCaseArray = array("cloves" => "cloves garlic");
    }
    
    private function _handleStalk()
    {
    }
    	
}

?>
