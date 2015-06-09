<?php

/**
 *	Recipes Controller
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class Recipe4livingRecipesController extends Recipe4livingArticlesController
{
	/**
	 *	Default view
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_view = 'recipe_listing';

	/**
	 *	Current item type
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_itemType = 'recipe';
	
	/** 
	 *	Menu slug
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_menuSlug = 'recipe_listing';
	
	/**
	 *	Send confirmation email to item author when setting item live
	 *
	 *	@access protected
	 *	@var bool
	 */
	protected $_sendSubmissionEmail = true;
	
	/**
	 *	Prepend to base URL
	 *
	 *	@access public
	 *	@param array Arguments
	 */
	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->_baseUrl = '/recipes/'.implode('/', $this->_args);
	}
	
	/**
	 *	Get title when filters are barren.
	 *
	 *	@access protected
	 *	@return string
	 */
	protected function _getEmptyFilterTitle()
	{
		return 'All recipes';
	}

	/**
	 *	Assign ingredients to recipes
	 *
	 *	@access public
	 */
	public function ingredients()
	{
		// Get recipe
		$itemsModel = BluApplication::getModel('items');
		$item = $itemsModel->getItem($this->_itemId);

		// Deal with submission
		if (Request::getBool('submit')) {
			
			// Set ingredients
			$ingredients = Request::getArray('values');
			if ($success = $itemsModel->setIngredients($this->_itemId, $ingredients)) {
				Messages::addMessage('Ingredients updated.', 'info', 'recipe_ingredients');
			} else {
				Messages::addMessage('Ingredients were not updated, please try again.', 'error', 'recipe_ingredients');
			}
			
			// Refresh item
			if ($success) {
				$item = $itemsModel->getItem($this->_itemId, null, true);
			}
			
			// Return JSON
			switch ($this->_doc->getFormat()) {
				case 'json':
					echo json_encode($success);
					return;
					
				default:
					break;
			}
		}
		
		// Get all ingredients, recipe ingredients (searchable - meta) and recipe ingredients (displayable - article)
		$metaModel = BluApplication::getModel('meta');
		$ingredientMetaGroups = $metaModel->getIngredientMetaGroups();
		if ($item['meta']) {
			$itemIngredients = $metaModel->getItemMetaGroups($item['id']);
			$itemIngredients = array_intersect_key($itemIngredients, $ingredientMetaGroups);
		} else {
			$itemIngredients = array();
		}
		$proposedIngredients = $item['ingredients'];
		
		// Add some links
		$saveIngredientsLink = $itemsModel->getTaskLink($item['link'], 'ingredients');
		$ingredientAmountsLink = $itemsModel->getTaskLink($item['link'], 'ingredient_amounts');
		
		// Display
		include(BLUPATH_TEMPLATES.'/recipes/ingredients.php');
	}
	
	/**
	 *	Search ingredients
	 *
	 *	@access public
	 */
	public function quicksearch_ingredients()
	{
		$metaModel = BluApplication::getModel('meta');
		
		$searchTerm = Request::getString('searchterm');
		$page = Request::getInt('page', 1);
		$limit = BluApplication::getSetting('quickSearchLimit', 10);
		
		// Get data
		$ingredients = array();
		if ($searchTerm) {
			if (!$ingredients = $metaModel->quicksearchIngredients($searchTerm, ($page - 1) * $limit, $limit, $total)) {
				$ingredients = $metaModel->fulltextsearchIngredients($searchTerm, ($page - 1) * $limit, $limit, $total);
			}
		}
		$metaGroups = array();
		foreach ($ingredients as $ingredient) {
			if (!isset($metaGroups[$ingredient['groupId']])) {
				$metaGroups[$ingredient['groupId']] = $metaModel->getGroup($ingredient['groupId']);
			}
		}
		
		// Load template
		include(BLUPATH_TEMPLATES.'/recipes/quicksearch_ingredients.php');
	}
	
	/**
	 *	Ingredient amounts
	 *
	 *	@access public
	 */
	public function ingredient_amounts()
	{
		// Get item
		$itemsModel = BluApplication::getModel('items');
		if (!$item = $itemsModel->getItem($this->_itemId)) {
			return $this->_errorRedirect();
		}
		
		// Get ingredients
		$ingredientsModel = BluApplication::getModel('ingredients');
		$ingredients = $ingredientsModel->getRecipeIngredients($item['id']);
		
		$proposedIngredients = $item['ingredients'];
		
		// Load template
		$addIngredientsUrl = $itemsModel->getTaskLink($item['link'], 'ingredients');
		$taskUrl = $itemsModel->getTaskLink($item['link'], 'ingredient_amounts_save');
		include(BLUPATH_TEMPLATES.'/recipes/ingredient_amounts.php');
	}

	public function normalize_all_ingredients()
	{
//ini_set('max_execution_time', 864000);
		$itemsModel = BluApplication::getModel('items');
		$metaModel = BluApplication::getModel('meta');
		$ingredientsModel = BluApplication::getModel('ingredients');
		$allItems = $itemsModel->getItems();
		$count = 0;
		//echo '<table><tr><td>Plain</td><td>Quantity</td><td>Unit</td><td>Text</td><td>Normalized</td><td>Id</td></tr>';
		foreach ($allItems as $item) {
			$itemToNormalize = $itemsModel->getItem($item);
			$normalizedIngredients = $ingredientsModel->getRecipeIngredients($item);
	Utility::irc_dump('doing '.$item, 'max');
			if ($normalizedIngredients != false || $itemToNormalize['ingredients']==false) {
				continue;
			} 
			$count++;
//			if ($count<=50) continue;

			$normalizedIngredients = $ingredientsModel->normalizeIngredients($itemToNormalize['ingredients']);
	Utility::irc_dump('setting '.$item.' - '.count($normalizedIngredients).' ingredients', 'max');
			$ingredientsModel->setRecipeIngredients($item, $normalizedIngredients);
//echo '<pre>';
//		var_dump($normalizedIngredients);	
//echo '</pre>';
	//		if ($count>5) {
	//			echo '</table>'; die();
	//		}
		}
		$ingredientMetaGroups = $metaModel->getIngredientMetaGroups();
		
	}
	
	/**
	 *	Ingredient amounts save
	 *
	 *	@access public
	 */
	public function ingredient_amounts_save()
	{
		// Get item
		$itemsModel = BluApplication::getModel('items');
		if (!$item = $itemsModel->getItem($this->_itemId)) {
			return $this->_errorRedirect();
		}
		
		// Save ingredients
		if (Request::getBool('submit')) {
			$amounts = Request::getArray('amounts');
			$measure = Request::getArray('weightIds');
			$ingredients = array();
			foreach ($amounts as $ingredientId => $amount) {
				$ingredients[$ingredientId]['amount'] = $amount;
			}
			foreach ($measure as $ingredientId => $m) {
				$ingredients[$ingredientId]['weightId'] = $m;
			}
			
			$ingredientsModel = BluApplication::getModel('ingredients');
			$set = $ingredientsModel->setRecipeIngredients($item['id'], $ingredients);
			
			if ($set) {
				Messages::addMessage('Successfully set ingredient amounts for <code>'.$item['title'].'</code>.');
			} else {
				Messages::addMessage('Could not set ingredient amounts for <code>'.$item['title'].'</code>.', 'error');
			}
		}
		
		// Redirect
		return $this->_showMessages('ingredient_amounts', 'ingredient_amounts');
	}

	public function prepareCachePurge() {

		Messages::addMessage('ARE YOU SURE? CACHE PURGE WILL CAUSE MINOR INSTABILITY FOR 10 MINUTES ON BOTH ADMIN AND CONSUMER SITE. <br/>
		<a href="/oversight/recipes/fullCachePurge">YES, DO IT</a> | <a href="/oversight">NO! GET ME OUT OF HERE!</a>', 'error');
	}

	public function fullCachePurge() { // Should this be here? No, not really.
		$cacheModel = BluApplication::getModel('cache');
		$cacheModel->deleteEntriesLike('%');
		Messages::addMessage('Cache Purged. Minor service instability may persist for 10 minutes.<br/><strong>Do not use this function again within 15 minutes</strong>', 'error');
	}

	/**
	 * Download USDA Food description table
	 *
	 * @access public
	 */
	public function downloadUsdaDescriptions()
	{
		$ingredientsModel = BluApplication::getModel('ingredients');
		$descriptions = $ingredientsModel->exportUsdaFoodDes();

		$descriptionsFile = new Csv(array('NDB_No', 'Long_Desc', 'Shrt_Desc'));
		foreach ($descriptions as $row) {
			$descriptionsFile->appendRow($row);
		}

		$this->_doc->setFormat('raw');
		$this->_doc->setMimeType('text/csv');
		$this->_doc->setDisposition('attachment; file="usdaDescriptions.csv"');
		$descriptionsFile->output();
	}

	/**
	 * Export normalization mapping
	 *
	 * @access public
	 */
	public function exportNormalizationMap()
	{
		$ingredientsModel = BluApplication::getModel('ingredients');
		$normalizationMap = $ingredientsModel->exportNormalizationMap();

		$mapFile = new Csv(array('keyword', 'NDB_No'));
		foreach ($normalizationMap as $mapping) {
			$mapFile->appendRow($mapping);
		}

		$this->_doc->setFormat('raw');
		$this->_doc->setMimeType('text/csv');
		$this->_doc->setDisposition('attachment; file="normalizationMap-'.date('Ymd').'.csv"');
		$mapFile->output();
	}

	/**
	 * Import updated normalization map
	 *
	 * @access public
	 */
	public function importNormalizationMap()
	{
		// Import?
		$mapFileDetails = Request::getFile('datafile');
		if ($mapFileDetails) {
			$mapFile = Csv::read($mapFileDetails['tmp_name']);
			$mapFileData = $mapFile->get();

			$ingredientsModel = BluApplication::getModel('ingredients');
			$ingredientsModel->importNormalizationMap($mapFileData);

			Messages::addMessage('Imported normalization map!');
		}

		// Load template
		include(BLUPATH_TEMPLATES.'/recipes/import.php');
	}
}

