<?php

/**
 *	Ingredients Model
 *
 *	@package BluApplication
 *	@subpackage SharedModels
 */
class ClientIngredientsModel extends BluModel
{

	public function normalizeIngredients($ingredients) {

		$quantityIndicators = Array (
					't.' => 'tsp', 't' => 'tsp', 'tsp.' => 'tsp', 'tsp' => 'tsp', 'teaspoon' => 'tsp',
					'tbs.' => 'tbsp', 'Tbsp' => 'tbsp', 'tbs' => 'tbsp', 'tbsp.' => 'tbsp',
					'c.' => 'cup', 'cup' => 'cup', 'cups' => 'cup', 'c' => 'cup',
					'oz.' => 'oz', 'oz' => 'oz', 'ounce' => 'oz', 'ounces' => 'oz',
					'g.' => 'gram', 'g' => 'gram', 'grammes' => 'gram', 'grams' => 'gram',
					'lb' => 'lb', 'lb.' => 'lb', 'lbs' => 'lb', 'lbs.' => 'lb', 'pound' => 'lb', 'pounds' => 'lb',
					'packages' => 'package', 'package' => 'package', 'pkg' => 'package', 'pkg.' => 'package', 'pkgs' => 'package', 'pkgs.' => 'package', 'container' => 'package', 'bag' => 'package', 'pt.' => 'package',
					'dash' => 'dash', 'pinch' => 'dash',
					'qt' => 'qt', 'qt.' => 'qt', 'quart' => 'qt'	
					);

		$quantityConversions = Array (	'qt' => Array ('unit'=>'oz', 'number'=>40));

		$outputIngredients = Array();
						
		foreach ($ingredients as $ingredient) {
			$unit = false;
			$quantity = 0;
			$quantityIndicator = false;
			$quantityDone = false;
			$stopRegexp = 'sm\.|lg\.|med\.| \' |medium|blue(^ cheese)|your|piece|other|brown|bunch|to taste|large|wedge|coarse|melted|grated|doz\.|doz|dozen|warm|chopped|fine|round|finely|sliced|crumbled|diced|favorite|small|minced|crushed|spicy|pitted|refrigerated|real|firm|extra.virgin|virgin|bottle|thin|skinless|freshly|shredded|ripe|fully';

			// Several things to skip straight off
			if (trim($ingredient) == '') continue;
			$ingredient = strtolower($ingredient);
			if (preg_match('/salt (.*) pepper/', $ingredient)) continue;
			if (strpos($ingredient, 'any ') !== false) continue;
			if (preg_match('/:$/', $ingredient)) continue;
			//echo '<tr><td>'.$ingredient.'</td>';
			$ingredient = preg_replace('/ +/', ' ', $ingredient); // Remove duplicate spaces
			$ingredient = preg_replace('/^l\b/', '1 ', $ingredient); // People who use l and 1, and 0 and O interchangeably need to put down their typewriters and stop making my life difficult. 
			$ingredient = preg_replace('/([0-9]) \((([0-9]|[a-z]|\.|\ )*)\)/', '\\1 \\2', $ingredient);
			$ingredient = preg_replace('/\((.*)\)/U', '', $ingredient); // remove all content within brackets
			$ingredient = preg_replace('/&amp;/', '&', $ingredient); // & to and 
			$ingredient = preg_replace('/ & /', ' and ', $ingredient); // & to and 
			$ingredient = preg_replace('/([0-9]+)-?([a-z])/', '\\1 \\2', $ingredient); // deal with stupid stuff like 20-ounces of chikkern
			$ingredient = preg_replace('/('.$stopRegexp.'|fresh),/', '', $ingredient);
			$ingredient = preg_replace('/(,|;|for|to|--|~) (.*)/', '', $ingredient); // Remove everything after a comma, semicolon, for, double-dash, tilda, to

			// Are we going to multiply quantities?
			$multiplyMode = preg_match('/[0-9] [0-9]/', $ingredient);
			
			$ingredient = explode (' ', $ingredient);

			$conversionMultiplier = 1;
			// Start by yanking off the quantity
			foreach ($ingredient as $index=>&$ingredientPart) {
				if (array_key_exists($ingredientPart, $quantityIndicators)) {
					if ($quantityIndicator == false ) {
						$quantityIndicator = $index;
						if (isset($quantityConversions[$quantityIndicators[$ingredientPart]])) {
							$conversionMultiplier = $quantityConversions[$quantityIndicators[$ingredientPart]]['number'];
							$ingredientPart = $quantityConversions[$quantityIndicators[$ingredientPart]]['unit'];
						}
					} else {
						$ingredientPart = ''; // Remove the extra unit indicators
					}
				} else {
					if (preg_match('/\//', $ingredientPart) && preg_match('/[0-9]/', $ingredientPart)) {
						$sumParts = explode('/', $ingredientPart);
						$ingredientPart = $sumParts[0]/$sumParts[1]; 
					}
				}
			}
			unset ($ingredientPart);
			
			if ($quantityIndicator === false) {
				reset ($ingredient);
				foreach ($ingredient as $index=>$ingredientPart) {
					if (preg_match('/[0-9]+|\//',$ingredientPart)) {
						$quantityIndicator = $index;
					}
				}
				
				if ($multiplyMode == 0) {
					$quantity = array_sum(array_slice($ingredient, 0, $quantityIndicator+1));
				} else {
					$quantity = $ingredient[0];
					for ($a = 1; $a <= $quantityIndicator; $a++) {
						$quantity *= $ingredient[$a];
					}
				}
				$quantityDone = true;
				$unit = 'unit';
			}

			if ($quantityDone != true) {
				if ($multiplyMode == 0) {
					$quantity = array_sum(array_slice($ingredient, 0, $quantityIndicator));
				} else {
					$quantity = $ingredient[0];
					for ($a = 1; $a < $quantityIndicator; $a++) {
						$quantity *= $ingredient[$a];
					}
				}
			}

			if (array_search('doz', $ingredient) !== false || array_search('doz.', $ingredient) !== false ||  array_search('dozen', $ingredient) !== false) {
				$quantity *= 12;
			}

			$unit = $unit=='unit'?'unit':$quantityIndicators[$ingredient[$quantityIndicator]];

			$ingredientText = ''.implode (' ',array_slice($ingredient, $quantityIndicator +1, count($ingredient) - $quantityIndicator));

			$quantity *= $conversionMultiplier;

			if ($quantity == 0) {  // This went well, clearly
				$quantity = 1;
				$ingredientText = ''.implode(' ', $ingredient);
			}	
	
			// Tidyup regexes
			$ingredientText = preg_replace('/\'s\b/', ' ', $ingredientText); // deposess
			$ingredientText = preg_replace('/\' /', ' ', $ingredientText); // deposess
			$ingredientText = preg_replace('/([^s])s\b/', '\\1 ', $ingredientText); // depluralize 
			$ingredientText = preg_replace('/\b([a-z]*)berrie/', '\\1berry \\1berries', $ingredientText); // depluralize berries
			$ingredientText = preg_replace('/\bleave\b/', 'leaf', $ingredientText); // depluralize berries
			$ingredientText = preg_replace('/oe\b/', 'o ', $ingredientText); // depluralize oes 
			$ingredientText = preg_replace('/or to taste/', '', $ingredientText); // so many special cases
			$ingredientText = preg_replace('/(.*)\bor\b/', '', $ingredientText); // Remove everything before "or"
			$ingredientText = preg_replace('/(.*)\b:\b/', '', $ingredientText); // Remove everything before a colon
			$ingredientText = preg_replace('/(.*)\)/', '', $ingredientText); // straggling brackets 
			$ingredientText = preg_replace('/\((.*)/', '', $ingredientText); // straggling brackets 
			$ingredientText = preg_replace('/\bcan\b/', 'canned', $ingredientText); // can to canned 
			$ingredientText = preg_replace('/black olive/', 'green olive', $ingredientText); // can to canned 
			$ingredientText = preg_replace('/crescent/', 'croissant', $ingredientText); // bloody luddites
			$ingredientText = preg_replace('/\b(.*)-stuffed\b/', '', $ingredientText); // don't care what you stuff it with


			// Remove words which aren't going to be relevant to nutritional info
			$ingredientText = preg_replace('/\b('.$stopRegexp.')\b/', '', $ingredientText);		

			$typos = Array ('tomatoe' => 'tomato',
					'potatoe' => 'potato',
					'boullion' => 'bouillon');

			$typosKeys = array_keys($typos);

			$ingredientText = str_replace($typosKeys, $typos, $ingredientText);
			
			$ingredientText = preg_replace('/ +/', ' ', $ingredientText); // Remove duplicate spaces

			$ingredientText = trim($ingredientText, '. ;-,');

			$ingredientText = preg_replace('/"/', '', $ingredientText); // Pesky double-quotes

			if ($ingredientText == '') continue; // Either I've screwed up, or the data is total bunk

			if (preg_match('/\b(minute|hour)\b/', $ingredientText)) continue; // This is a timing incorrectly sat in the ingredients
		
			//echo "<td>$quantity</td><td>$unit</td><td>$ingredientText</td>";

$info = '';

			$foundIngredients = Array();

			$query = 'SELECT ingredientID FROM normalizationMap WHERE keyword = "'.$this->_db->escape($ingredientText).'" LIMIT 0,10';
			$this->_db->setQuery($query);
			$result = $this->_db->loadAssocList();
			foreach ($result as $row) {
				$foundIngredients[$row['ingredientID']] = 50;
			}

			if (count($foundIngredients) == 0) {
//			echo '<td>';
				$query = 'SELECT ingredientID FROM normalizationMap WHERE "'.$this->_db->escape($ingredientText).'" LIKE CONCAT("%", keyword, "%") LIMIT 0,10';
				$this->_db->setQuery($query);
				$result = $this->_db->loadAssocList();
				foreach ($result as $row) {
					!isset($foundIngredients[$row['ingredientID']]) ? ($foundIngredients[$row['ingredientID']] = 18) : $foundIngredients[$row['ingredientID']] += 18;
				}

				$query = 'SELECT * FROM usdaFoodDes WHERE Long_Desc LIKE "%'.$this->_db->escape($ingredientText).'%" LIMIT 0,10';
				$this->_db->setQuery($query);
				$result = $this->_db->loadAssocList();
				foreach ($result as $row) {
					!isset($foundIngredients[$row['NDB_No']]) ? ($foundIngredients[$row['NDB_No']] = 30) : $foundIngredients[$row['NDB_No']] += 30;
				}
//echo $query.'#####';
				// Let's add an exploded like to the mix
				$likes = explode(' ', $ingredientText);
				$likesText = implode('%" AND Long_Desc LIKE "%', $likes);
				$query = 'SELECT * FROM usdaFoodDes WHERE Long_Desc LIKE "%'.$likesText.'%" LIMIT 0,10';
				$this->_db->setQuery($query);
				$result = $this->_db->loadAssocList();
				foreach ($result as $row) {
					!isset($foundIngredients[$row['NDB_No']]) ? ($foundIngredients[$row['NDB_No']] = 25) : $foundIngredients[$row['NDB_No']] += 25;
				}
//echo $query.'#####';

				$query = 'SELECT NDB_No, Long_Desc, MATCH (Shrt_Desc, Long_Desc) AGAINST ("'.$this->_db->escape($ingredientText).'" ) AS score FROM usdaFoodDes WHERE MATCH (Shrt_Desc, Long_Desc) AGAINST ("'.$this->_db->escape($ingredientText).'" ) LIMIT 0, 30';
				$this->_db->setQuery($query);
				$result = $this->_db->loadAssocList();
//				$rank = 10;
				foreach ($result as $row) {
					!isset($foundIngredients[$row['NDB_No']]) ? ($foundIngredients[$row['NDB_No']] = (10 + $row['score'])) : $foundIngredients[$row['NDB_No']] += (10 + $row['score']);
//$info .= 'scored '.$row['Long_Desc'].' '.$row['score'].' for '.$ingredientText.'<br/>';
//					$rank--;
				}
//				echo '<hr>';
//echo $query.'#####';
//			echo '</td>';
			}

			arsort($foundIngredients);
//var_dump($foundIngredients);
			$foundIngredientsKeys = array_keys($foundIngredients);

			$bestIngredient = array_shift($foundIngredientsKeys);

		//	$this->_db->setQuery('SELECT Long_Desc FROM usdaFoodDes WHERE NDB_No = "'.$bestIngredient.'"');
			//echo '<td>'.$this->_db->loadResult().' '.$info.'</td><td>'.$bestIngredient.'</td>';

			if ($bestIngredient == 0) continue; // Total wash-out

			// get weightid from $unit and $bestIngredient

			$query = 'SELECT id FROM usdaWeight WHERE NDB_No = "'.$bestIngredient.'" AND Msre_Desc LIKE "%'.$this->_db->escape($unit).'%"';
//echo '<td>'.$query.'</td>';
			$this->_db->setQuery($query);
			$weightId = $this->_db->loadResult();

			if (!$weightId) {
				// Can't parse the unit.
				$query = 'SELECT id FROM usdaWeight WHERE NDB_No = "'.$bestIngredient.'" ORDER BY Gm_Wgt ASC LIMIT 1';
				$this->_db->setQuery($query);
				$weightId = $this->_db->loadResult();
			}
//echo '<td>########'.$weightId.'</td></tr>';
			$outputIngredients[$bestIngredient] = Array (
				'amount' => $quantity,
				'weightId' => $weightId,
				'details' => $this->_getIngredient($bestIngredient));
		}

		return $outputIngredients;
	}


	/**
	 *	Set ingredients for a recipe
	 *
	 *	@access public
	 *	@param int Recipe ID
	 *	@param array Ingredients
	 *	@return bool Success
	 */
	public function setRecipeIngredients($recipeId, $ingredients)
	{
		// Delete existing
		$query = 'DELETE FROM `recipeIngredients`
			WHERE `articleId` = '.(int) $recipeId;
		$this->_db->setQuery($query);
		$this->_db->query();
		
		$query = 'DELETE FROM `articleMetaValues`
			WHERE `articleId` = '.(int) $recipeId.' AND groupId >=129 AND groupId <=177';
		$this->_db->setQuery($query);
		$this->_db->query();
		
		// Add new
		$success = true;
		krsort($ingredients);
		foreach ($ingredients as $ingredientId => $ingredientAmount) {
			$amount = $ingredientAmount['amount'];
			$weight = $ingredientAmount['weightId'];
			if (!$this->addRecipeIngredient($recipeId, $ingredientId, $amount, $weight)) {
				$success = false;
			}
		}

		// Clear cache entry
		$cacheKey = 'ingredients_recipe_'.$recipeId.'_nutrition';
		$this->_cache->delete($cacheKey);
		$cacheKey = 'ingredients_recipe_'.$recipeId;
		$this->_cache->delete($cacheKey);
		$itemsModel = BluApplication::getModel('items');
		$itemsModel->flushItem($recipeId);
		
		// Return
		return $success;
	}
	
	/**
	 *	Add an ingredient entry for a recipe
	 *
	 *	@access public
	 *	@param int Recipe ID
	 *	@param int NDB_No (USDA ingredient ID)
	 *	@param double Amount
	 *	@param int USDA Weight ID
	 *	@return bool Success
	 */
	public function addRecipeIngredient($recipeId, $ingredientId, $amount, $weightId)
	{
		// Update database
		$query = 'INSERT INTO `recipeIngredients`
			SET `articleId` = '.(int) $recipeId.',
				`NDB_No` = '.(int) $ingredientId.',
				`amount` = '.(double) $amount.',
				`weightId` = '.(int) $weightId.'
			ON DUPLICATE KEY UPDATE
				`amount` = '.(double) $amount.',
				`weightId` = '.(int) $weightId;
		$this->_db->setQuery($query);
		$this->_db->query();
		
		$query = 'SELECT groupId, id FROM metaValues LEFT JOIN usdaMeta ON metaValues.id = usdaMeta.metaValue WHERE usdaMeta.NDB_No = "'.$ingredientId.'"';
		$this->_db->setQuery($query);
		$ingredientMeta = $this->_db->loadAssoc();
		
		$query = 'REPLACE INTO `articleMetaValues`
				(articleId, groupId, valueId) VALUES
				("'.$recipeId.'", "'.$ingredientMeta['groupId'].'", "'.$ingredientMeta['id'].'")'; 
		$this->_db->setQuery($query);
		$this->_db->query();
		// Return 
		return true;
	}

	public function getTidyRecipeIngredients($recipeId) {
		$cacheKey = 'ingredients_recipe_'.$recipeId;
		$return = $this->_cache->get($cacheKey);
		if ($return === false) {
			// grab the ingredients from the db
			$ingredients = $this->_getRecipeIngredients($recipeId);
			foreach ($ingredients as &$ingredient) {
				$ingredient['details'] = $this->_getIngredient($ingredient['NDB_No']);
			}
			$return = $ingredients;
			$this->_cache->set($cacheKey, $return);
		}

		return $return;
	}
	
	/**
	 *	Get ingredients for a recipe
	 *
	 *	@access protected
	 *	@param int Recipe ID
	 *	@return array Ingredients
	 */
	protected function _getRecipeIngredients($recipeId)
	{
		// Get ingredients mapped to the recipe
		$query = 'SELECT um.NDB_No, 0 AS `amount`
			FROM `usdaMeta` AS `um`
				LEFT JOIN `articleMetaValues` AS `amv` ON um.metaValue = amv.valueId
			WHERE amv.articleId = '.(int) $recipeId;
		$this->_db->setQuery($query);
		$ingredients = $this->_db->loadResultAssocArray('NDB_No', 'amount');
		
		// Get (relevant) available ingredient amounts
		$query = 'SELECT ri.*
			FROM `recipeIngredients` AS `ri`
			WHERE ri.articleId = '.(int) $recipeId;
		$this->_db->setQuery($query);
		$amounts = $this->_db->loadAssocList('NDB_No');
		$amounts = array_intersect_key($amounts, $ingredients);
		
		// Merge
		$ingredients = $amounts + $ingredients;
		
		// Return
		return $ingredients;
	}
	
	protected function _getArticleMeta($recipeId)
	{
		// Get ingredients mapped to the recipe
		$query = 'SELECT amv.groupId,amv.rawValue FROM `articleMetaValues` AS `amv`
			WHERE amv.articleId = '.(int) $recipeId;
		$this->_db->setQuery($query);
		$articleMeta = $this->_db->loadAssocList('groupId');
		
		// Get meta groups details
		$metaModel = BluApplication::getModel('meta');
		$yieldQuantityGroupId = $metaModel->getGroupIdBySlug('yield_quantities');
		$yieldMeasureGroupId = $metaModel->getGroupIdBySlug('yield_measures');

		if(empty($articleMeta[$yieldQuantityGroupId]['rawValue']) || empty($articleMeta[$yieldMeasureGroupId]['rawValue'])){
			$articleMeta['servingQuantity'] = 1;
			$articleMeta['servingMeasure'] = 'Serving';
		} else {
			$articleMeta['servingQuantity'] = $articleMeta[$yieldQuantityGroupId]['rawValue'];
			$articleMeta['servingMeasure'] = $articleMeta[$yieldMeasureGroupId]['rawValue'];
		}
		
		return $articleMeta;
	} 
	
	/**
	 *	Get ingredient details
	 *
	 *	@access protected
	 *	@param int Ingredient ID
	 *	@return array Details
	 */
	protected function _getIngredient($ingredientId)
	{
		// Get base details
		$query = 'SELECT fd.*
			FROM `usdaFoodDes` AS `fd`
			WHERE fd.NDB_No = '.(int) $ingredientId;
		$this->_db->setQuery($query);
		if (!$ingredient = $this->_db->loadAssoc()) {
			return false;
		}
		
		// Get ingredient nutrition amount (per 100g)
		$query = 'SELECT dat.Nutr_No, dat.Nutr_Val
			FROM `usdaNutData` AS `dat`
			WHERE dat.NDB_No = '.(int) $ingredient['NDB_No'];
		$this->_db->setQuery($query);
		$ingredient['nutrition'] = $this->_db->loadResultAssocArray('Nutr_No', 'Nutr_Val');
		
		// Get ingredient measures
		$query = 'SELECT w.*
			FROM `usdaWeight` AS `w`
			WHERE w.NDB_No = '.(int) $ingredient['NDB_No'].'
			ORDER BY w.Seq ASC';
		$this->_db->setQuery($query);
		$ingredient['weights'] = $this->_db->loadAssocList('id');
		
		// Return
		return $ingredient;
	}
	
	/**
	 *	Get calculated recipe nutrition
	 *
	 *	@access public
	 *	@param int Recipe ID
	 *	@return array Nutrition data
	 */
	public function getRecipeNutrition($recipeId,$servingSize = null)
	{
		$cacheKey = 'ingredients_recipe_'.$recipeId.'_'.$servingSize.'_nutrition';
		$return = $this->_cache->get($cacheKey);
		
		if ($return === false) {
			$recipeMeta = $this->_getArticleMeta($recipeId);
			if(!$recipeMeta){
				return false; 
			}
			// Get ingredients
			$ingredients = $this->_getRecipeIngredients($recipeId);
			
			$recipeNutrition = array();
			$recipeWeight = 0;	// Recipe weight (grams)
			$recipeCalories = array(
				'carbohydrate' => 0,
				'fat' => 0,
				'protein' => 0
			);
			foreach ($ingredients as $ingredientId => $ingredient) {
				
				// Zero amount? Ignore.
				if (!$ingredient['amount'] || !$ingredient['weightId']) {
					continue;
				}

				// Get/append weight details
				$ingredientMeasure = $this->_getWeight($ingredient['weightId']);
				$ingredientWeight = $ingredient['amount'] * $ingredientMeasure['Gm_Wgt'];	// Ingredient weight (in grams)
				$ingredientWeight = ($ingredientWeight/$recipeMeta['servingQuantity']) * $servingSize; // calculating for serving size 
				$recipeWeight += $ingredientWeight;
				
				// Total up nutrition
				$fullIngredient = $this->_getIngredient($ingredientId);
				foreach ($fullIngredient['nutrition'] as $nutrientId => $nutrientValue) {
					
					// Nutrition itself has no value
					if (!$nutrientValue) {
						continue;
					}
					
					// Add nutrition details
					if (!isset($recipeNutrition[$nutrientId])) {
						$recipeNutrition[$nutrientId] = 0;
					}
					$ingredientNutritionValue = $ingredientWeight * $nutrientValue / 100;	// Amount of nutrition from this ingredient
					$recipeNutrition[$nutrientId] += $ingredientNutritionValue;

					// Add calories
					switch ($nutrientId) {
						case 205:
							// Carbohydrate
							$recipeCalories['carbohydrate'] += $ingredientNutritionValue * $fullIngredient['CHO_Factor'];
							break;

						case 204:
							// Fat
							$recipeCalories['fat'] += $ingredientNutritionValue * $fullIngredient['Fat_Factor'];
							break;

						case 203:
							// Protein
							$recipeCalories['protein'] += $ingredientNutritionValue * $fullIngredient['Pro_Factor'];
							break;
					}
				}
			}
			
			$return = array(
				'weight' => $recipeWeight,
				'servingQuantity' => $recipeMeta['servingQuantity'],
				'servingMeasure' => $recipeMeta['servingMeasure'],
				'nutrition' => $recipeNutrition,
				'calories' => $recipeCalories
			);
			
			$this->_cache->set($cacheKey, $return);
		}
		return $return;
	}
	
	/**
	 *	Get weight
	 *
	 *	@access protected
	 *	@param int Weight ID
	 *	@return array Details
	 */
	protected function _getWeight($weightId)
	{
		$query = 'SELECT w.*
			FROM `usdaWeight` AS `w`
			WHERE w.id = '.(int) $weightId;
		$this->_db->setQuery($query);
		return $this->_db->loadAssoc();
	}

	/**
	 *	Get nutrient definition
	 *
	 *	@access protected
	 *	@param int Nutrient ID
	 *	@param array Details
	 */
	protected function _getNutrient($nutrientId)
	{
		$query = 'SELECT nd.*
			FROM `usdaNutrDef` AS `nd`
			WHERE nd.Nutr_No = '.(int) $nutrientId;
		$this->_db->setQuery($query);
		return $this->_db->loadAssoc();
	}

	/**
	 *	Get all nutrient definitions
	 *
	 *	@access public
	 *	@return array Details
	 */
	public function getNutrients()
	{
		$cacheKey = 'ingredients_nutrients';
		$nutrients = $this->_cache->get($cacheKey);
		if ($nutrients === false) {
			$query = 'SELECT nd.Nutr_No, nd.Units, nd.NutrDesc, nd.Decimal,nd.DV
				FROM `usdaNutrDef` AS `nd`
				ORDER BY nd.NutrDesc ASC';
			$this->_db->setQuery($query);
			$nutrients = $this->_db->loadAssocList('Nutr_No');
			
			$this->_cache->set($cacheKey, $nutrients);
		}
		return $nutrients;
	}
}

?>
