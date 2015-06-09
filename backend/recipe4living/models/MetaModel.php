<?php

/**
 *	Recipe4living Meta Model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendMetaModel extends BackendMetaModel
{
	/**
	 *	Quicksearch ingredients
	 *
	 *	@access public
	 *	@param string Searchterm
	 *	@param int Offset
	 *	@param int Limit
	 *	@param int Total ingredients
	 *	@param bool Add meta value details
	 *	@return array Meta values
	 */
	public function quicksearchIngredients($searchterm, $offset = null, $limit = null, &$total = null, $addDetails = true)
	{
		// Get searchable ingredients
		$cacheKey = 'metaGroups_USDA_quicksearch';
		$ingredients = $this->_cache->get($cacheKey);
		if ($ingredients === false) {
			$ingredients = array();
			
			// Pull out ingredients names
			$ingredientMetaGroups = $this->getIngredientMetaGroups();
			foreach ($ingredientMetaGroups as $group) {
				foreach ($group['values'] as $firstLetter => $values) {
					foreach ($values as $value) {
						$ingredients[$value['id']]['name'] = $value['name'];
					}
				}
			}
			
			$this->_cache->set($cacheKey, $ingredients);
		}
		
		// Quicksearch
		$moreThanThreeCharactersFilter = create_function('$str', 'return strlen($str) > 3;');
		$metaValues = Utility::quickSearch($searchterm, $ingredients, array($moreThanThreeCharactersFilter));
		$total = count($metaValues);
		
		// Slice
		if ($offset || $limit) {
			$metaValues = array_slice($metaValues, $offset, $limit, true);
		}
		
		// Re-append details
		if ($addDetails) {
			foreach ($metaValues as $valueId => &$value) {
				$value = $this->getValue($valueId);
			}
			unset($value);
		}
		
		// Return
		return $metaValues;
	}
	
	/**
	 *	Full-text search ingredients
	 *
	 *	@access public
	 *	@param string Searchterm
	 *	@param int Offset
	 *	@param int Limit
	 *	@param int Total ingredients
	 *	@param bool Add details
	 *	@return array Ingredients
	 */
	public function fulltextsearchIngredients($searchterm, $offset = null, $limit = null, &$total = null, $addDetails = true)
	{
		// Query
		$query = 'SELECT'.(is_null($total) ? '' : ' SQL_CALC_FOUND_ROWS').' um.metaValue, fd.NDB_No
			FROM `usdaFoodDes` AS `fd`
				LEFT JOIN `usdaMeta` AS `um` ON fd.NDB_No = um.NDB_No
			WHERE MATCH (fd.Long_Desc, fd.Shrt_Desc) AGAINST ("'.$this->_db->escape($searchterm).'")
			ORDER BY MATCH (fd.Long_Desc, fd.Shrt_Desc) AGAINST ("'.$this->_db->escape($searchterm).'") DESC';
		$this->_db->setQuery($query, $offset, $limit);
		$ingredients = $this->_db->loadResultAssocArray('metaValue', 'NDB_No');
		
		// Get total
		if ($total) {
			$total = $this->_db->getFoundRows();
		}
		
		// Add meta details
		if ($addDetails) {
			foreach ($ingredients as $valueId => &$value) {
				$value = $this->getValue($valueId);
			}
			unset($value);
		}
		
		// Return
		return $ingredients;
	}
	
	/**
	 *	Get USDA meta groups
	 *
	 *	@access public
	 *	@return array Meta groups
	 */
	public function getIngredientMetaGroups()
	{
		$cacheKey = 'metaGroups_USDA';
		$groups = $this->_cache->get($cacheKey);
		if ($groups === false) {
			$query = 'SELECT lmg.id
				FROM `languageMetaGroups` AS `lmg`
				WHERE lmg.slug LIKE "usda_%"
				ORDER BY lmg.name ASC';
			$this->_db->setQuery($query);
			$groups = $this->_db->loadResultAssocArray('id', 'id');
			
			// Regroup values by first letter
			foreach ($groups as $groupId => &$group) {
				$group = $this->getGroup($groupId);
				
				$alphabeticalValues = array();
				foreach ($group['values'] as $value) {
					if ($firstLetter = substr(trim($value['name']), 0, 1)) {
						$alphabeticalValues[$firstLetter][$value['id']] = $value;
					}
				}
				
				$group['values'] = $alphabeticalValues;
			}
			unset($group);
			
			$this->_cache->set($cacheKey, $groups);
		}
		return $groups;
	}

	/**
	 *	Get Category meta groups
	 *
	 *	@access public
	 *	@desc we render here for it is backend only and it is more eficiency.
	 */	
	public function getNewCategory()
	{
		$sql = "SELECT mg.id as mgid,mg.internalName as mgname,mg.type,mv.id as mvid,mv.groupId,mv.internalName as mvname 
				FROM metaGroups as mg 
				LEFT JOIN metaValues as mv on mg.id=mv.groupId 
				WHERE mg.id in ('2','4','50','52','54') 
				LIMIT 0,1000";
		$r = mysql_query($sql);
		echo "<table style='clear:both'>";
		while($row = mysql_fetch_array($r))
		{
			//print_r($row);
			echo "<tr>";
			echo "<td>" . $row["mgname"] . "</td><td><a href='?mgid=" .$row['mgid'] ."&mvid=" .$row['mvid'] ."'>" . $row['mvname'] . "</a></td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	
	public function getNewCategoryArticles($mgid,$mvid,$type)
	{
		$live = array('0'=>'New Added','1'=>'Live','2'=>'Offline');
	
		if($mgid == 2)
		{
			if($mvid == 2)
			{
				return false;
			}else if($mvid == 128208)
			{
				$sql = "SELECT a.id as articleId,amv.*,a.*,mv.* FROM articleMetaValues as amv 
					LEFT JOIN articles as a on amv.articleId=a.id 
					LEFT JOIN metaValues as mv on amv.valueId=mv.id
					WHERE amv.groupId=50 
					AND a.type='" . $type . "' 
					ORDER BY a.date DESC";
			}else if($mvid == 128302)
			{
				$sql = "SELECT a.id as articleId,amv.*,a.*,mv.* FROM articleMetaValues as amv 
					LEFT JOIN articles as a on amv.articleId=a.id 
					LEFT JOIN metaValues as mv on amv.valueId=mv.id
					WHERE amv.groupId=52 
					AND a.type='" . $type . "' 
					ORDER BY a.date DESC";
			}else if($mvid == 128338)
			{
				$sql = "SELECT a.id as articleId,amv.*,a.*,mv.* FROM articleMetaValues as amv 
					LEFT JOIN articles as a on amv.articleId=a.id 
					LEFT JOIN metaValues as mv on amv.valueId=mv.id
					WHERE amv.groupId=54 
					AND a.type='" . $type . "' 
					ORDER BY a.date DESC";
			}
			else{
			}
			
		}else{
			$sql = "SELECT a.id as articleId,amv.*,a.*,mv.* FROM articleMetaValues as amv 
					LEFT JOIN articles as a on amv.articleId=a.id 
					LEFT JOIN metaValues as mv on amv.valueId=mv.id
					WHERE amv.valueId=$mvid 
					AND a.type='" . $type . "' 
					ORDER BY a.date DESC";
		}
		
		$r = mysql_query($sql);
		echo "Total " . mysql_num_rows($r);
		echo "<table style='clear:both'>";
		while($row = mysql_fetch_array($r))
		{
			$onlineEcho = "online";
			if($row["live"] == 1){
				$onlineEcho = "offline";
			}
			//print_r($row);
			echo "<tr>";
			echo "<td>" . $row['articleId'] . "</td>";
			echo "<td><a href='http://" . $_SERVER['HTTP_HOST'] . '/' . $type . 's/edit/' . $row['slug'] . ".htm'>" . substr($row["title"],0,50) . "</a></td>";
			echo "<td>" . $row["type"] . "</td>";
			echo "<td>" . $row["internalName"] . "</td>";
			echo "<td><div title='Click to put it " . $onlineEcho . "'><a href='new_push_live?id=" . $row['articleId'] . "&action=" . $row["live"] . "'>" . $live[$row["live"]] . "</a></div></td>";
			echo "<td><div title='Delete the item from this category!'><a href='new_delete_from_category?id=" . $row['articleId'] . "&cid=" . $row['valueId'] . "' >Del</a></div></td>";
			echo "</tr>";
		}
		echo "</table>";
		//echo "<div style='color:white;'>" . $sql . "</div>";
	}
	
	public function pushOnline($id)
	{
		$sql = "UPDATE articles set live=1 where id=" . $id;
		return $r = mysql_query($sql);
	}
	public function pushOffline($id)
	{
		$sql = "UPDATE articles set live=2 where id=" . $id;
		return $r = mysql_query($sql);
	}
	
	public function removeFromCategory($id,$cid)
	{
		if(array_search($cid, array('2','4','50','52','54')) === false)
		{
			$sql = "DELETE FROM articleMetaValues where articleId=$id AND valueId=$cid LIMIT 1";
			//echo $sql;
			return $r = mysql_query($sql);
		}else{
			echo 'This is not a sub category !';
			return false;
		}
	}
}

?>
