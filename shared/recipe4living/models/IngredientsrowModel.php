<?php

/**
 *	Ingredients Model
 *
 *	@package BluApplication
 *	@subpackage SharedModels
 */
 
 
 
class ClientIngredientsrowModel extends BluModel
{
    private $solr_ingredients;
    public $types;
    public $solr_escape;
    public $outputIngredients;
    private $_table = 'ingredient_row';
    
    public function __construct()
    {
        // Get reference to global database object
        $this->_db = BluApplication::getDatabase();

        // Get reference to global cache object
        $this->_cache = BluApplication::getCache();
        
        require_once(BLUPATH_BASE . '/leon/solr/solr.config.ingredient.php');
        require_once(dirname(__FILE__) . '/IngredientRow.php');
        
        global $solr_ingredient;
        $this->solr_ingredients = $solr_ingredient;
        $this->solr_escape = array(':', ',',  '+', '-', '&&', '||', '!', '(', ')', '{', '}', '[', ']', '^', '"', '~', '*', '?', '\'');

        $this->types = array(
                "sectionHeader",            // Pizza preparation:
                "noMeasurements",           // 2 eggs
                "missingMeasurements",      // 1 lib. beef -> entered 1 beef
                "normal",                   // The common case, such as 1 lib. beef raw, light baked
                "others"                    // We don't know what to do. Put it here
                );      
    }
    
    public function getList($start,$limit){
        $return = array();
        $sql = "SELECT ir.*, ir.id as irid,av.*,u.id,u.username FROM $this->_table as ir left join articleViews as av on av.articleId = ir.recipeId left join users as u on u.id = ir.user_id " . $this->_getFilterStatus() . " order by av.views desc limit $start,$limit";
        //echo $sql;
        $r = mysql_query($sql);
        while($row = mysql_fetch_assoc($r)){
            $return[] = $row;
        }
        return $return;
    }
    
    public function getTotal(){
        $total = $sql = "SELECT count(*) as total FROM $this->_table" . $this->_getFilterStatus();
        $r = mysql_query($total);
        $re = mysql_fetch_assoc($r);
        return $re['total'];
    }
    
    public function update($array)
    {
        $ids = $array['id'];
        $user_id = Session::get('UserID');
        //print_r($array);
        foreach($ids as $id=>$id_value){
            
            if($this->checkStatus($id_value, $array['status'][$id])){
                // Do the update
                $sql = "UPDATE $this->_table SET
                `ingredientRawRow` = '" . $array['ingredientRawRow'][$id] . "',
                `quantity` = '" . $array['quantity'][$id] . "',
                `quantity_true` = '" . $array['quantity_true'][$id] . "',
                `measurement` = '" . $array['measurement'][$id] . "',
                `measurementDescription` = '" . $array['measurementDescription'][$id] . "',
                `IngredientRaw` = '" . $array['IngredientRaw'][$id] . "',
                `notes` = '" . $array['notes'][$id] . "',
                `IngredientFeedBack` = '" . $array['IngredientFeedBack'][$id] . "',
                `IngredientLongDesc` = '" . $array['IngredientLongDesc'][$id] . "',
                `status` = '" . $array['status'][$id] . "',
                `user_id` = '" . $user_id . "',
                `time` = '" . date('Y-m-d:H:m:s', time()) . "'  
                  WHERE `id` = $id_value LIMIT 1";
                 mysql_query($sql);
                 //echo $sql . "<br>";                
            }else{
                // Ignore this one
            }
            

        }
        //exit;
        return true;  
    }
    
    
    public function checkStatus($id, $status_target){
        $sql = "SELECT id,status,user_id from $this->_table where `id` = $id LIMIT 1";
        $result = mysql_query($sql);
        while($row = mysql_fetch_array($result)){
            $user_id = $row["user_id"];
            $status = $row['status'];
            
            if($status_target == 1){
                // We will do the update
                if($user_id == 0 && $status == 0){
                    // Nobody touch this ingredient yet . Will do the update
                    return true;
                }else if($user_id != 0 && $status == 0){
                    // This is belong to someone. But since we change the status to 1, we should do the update.
                    return true;
                }else if($user_id != 0 && $status != 0){
                    // This is checked and belong to somebody. Let's ingore this one
                    return false;
                }
            }else{
                // We should ignore this one.
                
                // We will mark it as someone who is the first one to touch this row
                    if($user_id == 0 && $status == 0){
                        // Nobody touch this ingredient yet . But we wonna save the user info who is the first to touch this row. Will do the update
                        return true;
                    }else if($user_id != 0 && $status == 0){
                        // This is belong to someone. But since we change the status to 0, we should ignore this one
                        return false;
                    }else if($user_id != 0 && $status != 0){
                        // This is checked and belong to somebody. Let's ingore this one
                        return false;
                    }
            }
        }
    }
    
    
    public function commitToAll()
    {
    }
    
    
    private function _getFilterStatus()
    {
        $s = Session::get('filter_status');
        if($s == 'all') return ' where 1 ';
        if($s == '0') return ' where status=0 ';
        if($s == '1') return ' where status=1' ;
    }

    public function getDefaultValueList($start,$limit){
        $return = array();
        $where = ' where 1 ';
        $searchTerm = Request::getString('q');
        if($searchTerm != false){
            $where = " where feed_back like '%" . $searchTerm . "%'";
        }
        
        //$sql = "SELECT * FROM ingredients_default_value WHERE MATCH (default_value,feed_back,long_desc) AGAINST ('" . $searchTerm . "' IN NATURAL LANGUAGE MODE) order by NDB_No limit $start,$limit";
        $sql = "SELECT * FROM ingredients_default_value $where order by NDB_No limit $start,$limit";
        $r = mysql_query($sql);
        while($row = mysql_fetch_assoc($r)){
            $return[] = $row;
        }
        return $return;
    }
    
    public function getDefaultValueTotal(){
        $searchTerm = Request::getString('q');
        $where = ' where 1 ';
        if($searchTerm != false){
            $where = " where feed_back like '%" . $searchTerm . "%'";
        }
        $total = "SELECT count(*) as total FROM ingredients_default_value" . $where;
        //$total = "SELECT * FROM ingredients_default_value WHERE MATCH (default_value,feed_back,long_desc) AGAINST ('" . $searchTerm . "' IN NATURAL LANGUAGE MODE)";
        //echo $total;exit;
        $r = mysql_query($total);
        $re = mysql_fetch_assoc($r);
        return $re['total'];
    }
    
    public function default_value_update($array)
    {
        $ids = $array['id'];
        foreach($ids as $id=>$id_value){
            $sql = "UPDATE ingredients_default_value SET
            `default_value` = '" . $array['default_value'][$id] . "',
            `feed_back` = '" . $array['feed_back'][$id] . "',
            `long_desc` = '" . $array['long_desc'][$id] . "'  
              WHERE `id` = $id_value LIMIT 1";
             $r = mysql_query($sql);
             //echo $sql . "<br>";

             // update solr as well
                
                $doc = new Apache_Solr_Document();
                $doc->addField('id', $id_value);
                $doc->addField('NDB_No', htmlspecialchars($array['NDB_No'][$id]));
                $doc->addField('Default_Value', htmlspecialchars($array['default_value'][$id]));
                $doc->addField('Feed_Back', htmlspecialchars($array['feed_back'][$id]));
                $doc->addField('Long_Desc', htmlspecialchars($array['long_desc'][$id]));

                //echo $rawPost;
                $this->solr_ingredients->addDocument($doc);
                $this->solr_ingredients->commit();
        }
        return $r;
    }
    
    public function add_new_ingredient($array)
    {
        $default_value = $array['new_default_value'];
        $feed_back = $array['new_feed_back'];
        $long_desc = $array['new_long_desc'];
        $NDB_No = time();
        $sql = "INSERT INTO `ingredients_default_value` (`id`,`NDB_No`,`default_value`,`feed_back`,`long_desc`)
                VALUES (
                NULL , '$NDB_No', '$default_value', '$feed_back', '$long_desc'
                )";
        $r = mysql_query($sql);
        $id = mysql_insert_id();
        
             // update solr as well
                
            $doc = new Apache_Solr_Document();
            $doc->addField('id', $id);
            $doc->addField('NDB_No', htmlspecialchars($NDB_No));
            $doc->addField('Default_Value', htmlspecialchars($default_value));
            $doc->addField('Feed_Back', htmlspecialchars($feed_back));
            $doc->addField('Long_Desc', htmlspecialchars($long_desc));

            //echo $rawPost;
            $this->solr_ingredients->addDocument($doc);
            $this->solr_ingredients->commit();
    }
    
    public function getSlugMappings()
    {
        $query = 'SELECT a.id, a.slug
            FROM `articles` AS `a`';
        $this->_db->setQuery($query);
        $mapping = $this->_db->loadResultAssocArray('id', 'slug');
        return $mapping;
    }
    
    public function search($field, $searchTerm)
    {
        $return = array();
        $sql = "select ir.id as irid,ir.* from ingredient_row as ir where `$field` like \"%" . addslashes($searchTerm) . "%\" limit 0,200";
        echo $sql;
        $r = mysql_query($sql);
        while($row = mysql_fetch_assoc($r)){
            $return[] = $row;
        }
        return $return;        
    }	
}

?>
