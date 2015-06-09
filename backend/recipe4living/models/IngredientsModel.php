<?php

/**
 *	Ingredients Model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendIngredientsModel extends ClientIngredientsModel
{
	/**
	 *	Get recipe ingredients
	 *
	 *	@access public
	 *	@param int Recipe ID
	 *	@return array Ingredients
	 */
	public function getRecipeIngredients($recipeId)
	{
		// Get mapping
		$ingredients = $this->_getRecipeIngredients($recipeId);
		
		// Extend with basic details
		foreach ($ingredients as $ingredientId => &$ingredient) {
			$fullIngredient = $this->_getIngredient($ingredientId);
			
			if (!$ingredient) {
				$ingredient = array(
					'amount' => 0,
					'weightId' => key($fullIngredient['weights'])	// Just pick a default
				);
			}
			
			$ingredient = array_merge($fullIngredient, $ingredient);
		}
		unset($ingredient);
		
		// Return
		return $ingredients;
	}

	/**
	 * Get keyword-ingredient map for exporting
	 *
	 * @access public
	 * @return array Normalization map
	 */
	public function exportNormalizationMap()
	{
		$query = 'SELECT keyword, ingredientID
			FROM normalizationMap';
		$this->_db->setQuery($query);
		$normalizationMap = $this->_db->loadAssocList();
		return $normalizationMap;
	}

	/**
	 * *Append* to normalization map
	 *
	 * @access public
	 * @param array Normalization map
	 * @return bool Success
	 */
	public function importNormalizationMap($map)
	{
		if (!empty($map)) {
			foreach ($map as $mapping) {
				list($keyword, $ingredientId) = $mapping;

				$query = 'INSERT IGNORE INTO normalizationMap
					SET keyword = "'.$this->_db->escape($keyword).'",
						ingredientID = '.(int) $ingredientId;
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}
		return true;
	}

	/**
	 * Export bog-standard USDA data
	 *
	 * @access public
	 * @return array USDA data (usdaFoodDes table)
	 */
	public function exportUsdaFoodDes()
	{
		$query = 'SELECT NDB_No, Long_Desc, Shrt_Desc
			FROM usdaFoodDes';
		$this->_db->setQuery($query);
		$table = $this->_db->loadAssocList();
		return $table;
	}
}

