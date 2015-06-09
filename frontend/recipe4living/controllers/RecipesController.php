<?php

/**
 *	Recipes Controller
 *
 *	@package BluApplication
 *	@subpackage FrontendControllers
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
	 *	Prepend to base URL
	 *
	 *	@access public
	 *	@param array Arguments
	 */
	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->_baseUrl = '/recipes/'.implode('/', $args);

		if ($this->_view == 'recipe_listing') {
			Template::set(array(
				'rssUrl' => '/rss/'.implode('/', $args),
				'rssTitle' => $this->_getTitle('listingTitle')
			));
		}
		
		Template::set('searchType', 'recipes');
	}

	/**
	 *	Display nutritional data
	 *
	 *	@access public
	 *	@param bool Inline (within recipe details page)
	 */
	public function nutrition($inline = false)
	{
		$servingSize = Request::getVar('servingsize',1);
		
		// Get recipe
		$itemsModel = BluApplication::getModel('items');
		if (!$item = $itemsModel->getItem($this->_itemId)) {
			return $this->_errorRedirect();
		}

		// Get nutrition
		$ingredientsModel = BluApplication::getModel('ingredients');
		$recipeNutrition = $ingredientsModel->getRecipeNutrition($item['id'],$servingSize);

		// Get all nutrients
		$allNutrients = $ingredientsModel->getNutrients();

		// Define template variables
		if ($inline) {
			
			// No nutrition? Probably because the admin hasn't set them up yet. Die.
			if (empty($recipeNutrition['nutrition'])) {
				return false;
			}
			
			// Calories
			$calories = round(array_sum($recipeNutrition['calories']));

			// Monounsaturated fat
			$monounsaturatedFat = 0;
			if (!empty($recipeNutrition['nutrition'][645])) {
				$monounsaturatedFat = $recipeNutrition['nutrition'][645];
			}

			// Polyunsaturated fat
			$polyunsaturatedFat = 0;
			if (!empty($recipeNutrition['nutrition'][646])) {
				$polyunsaturatedFat = $recipeNutrition['nutrition'][646];
			}

			// Trans fat
			$transFat = $polyunsaturatedFat + $monounsaturatedFat;

			// Saturated fat
			$saturatedFat = 0;
			if (!empty($recipeNutrition['nutrition'][606])) {
				$saturatedFat = $recipeNutrition['nutrition'][606];
			}
			

			// Total fat
			$totalFat = round($monounsaturatedFat + $polyunsaturatedFat + $saturatedFat, 1);

			// Detailed nutrition link
			$detailedLink = $itemsModel->getTaskLink($item['link'], 'nutrition');
			
		// Get full nutrition
		} else {
			
			// Remove silly nutrients
			foreach ($allNutrients as $nutrientId => $nutrient) {
				if (is_numeric(substr($nutrient['NutrDesc'], 0, 1))) {
					unset($allNutrients[$nutrientId]);
				}
			}

			// Set document title
			$this->_doc->setTitle('Nutrition information | '.$item['title']);
			Template::set('isPopup', $this->_doc->getFormat() == 'popup');
		}

		// Load template
		include(BLUPATH_TEMPLATES.'/recipes/nutrition.php');
	}
	
	/**
	 * Related Categories
	 */
	public function related_categories() 
	{
		// Get item
		$itemsModel = BluApplication::getModel('items');
		if (!$item = $itemsModel->getItem($this->_itemId)) {
			return $this->_errorRedirect();
		}

		// Get categories
		$metaModel = BluApplication::getModel('meta');
		$itemMetaGroups = $metaModel->getItemMetaGroups($item['id']);
		$categories = array();
		foreach ($itemMetaGroups as $metaGroup) {
			if ($metaGroup['slug'] == 'top_levels'){
				continue;
			} 
			if ($metaGroup['excludeValues'] == 'show_available'){ //hack to skip the categories choosed from USDA
				continue;
			} 
			if ($metaGroup['slug'] == 'author'){ //hack to skip the author
				continue;
			}			
			if($metaGroup['values']){
				foreach ($metaGroup['values'] as $metaValue) {
					if (!is_array($metaValue) || !$metaValue['display']){ 
						continue;
					}
					$categories[] = Array(
						'parent' => $metaGroup['name'],
						'link' => $metaValue['slug'],
						'name' => $metaValue['name']
					);
				}
			}
		}
		
		include(BLUPATH_TEMPLATES.'/recipes/related_categories.php');
	}

    function category_hubs(){
        // Get item
        $itemsModel = BluApplication::getModel('items');
        if (!$item = $itemsModel->getItem($this->_itemId)) {
            return $this->_errorRedirect();
        }

        // Get categories
        $metaModel = BluApplication::getModel('meta');
        $itemMetaGroups = $metaModel->getItemMetaGroups($item['id']);
        $categories = array();
        foreach ($itemMetaGroups as $metaGroup) {
            if (isset($metaGroup['slug']) && $metaGroup['slug'] == 'top_levels'){
                continue;
            } 
            if (isset($metaGroup['excludeValues']) && $metaGroup['excludeValues'] == 'show_available'){ //hack to skip the categories choosed from USDA
                continue;
            } 
            if (isset($metaGroup['author']) && $metaGroup['slug'] == 'author'){ //hack to skip the author
                continue;
            }            
            if($metaGroup['values']){
                foreach ($metaGroup['values'] as $metaValue) {
                    if (!is_array($metaValue) || !$metaValue['display']){ 
                        continue;
                    }
                    $categories[] = Array(
                        'parent' => $metaGroup['name'],
                        'link' => $metaValue['slug'],
                        'name' => $metaValue['name']
                    );
                }
            }
        }
        include(BLUPATH_TEMPLATES.'/recipes/details/category_hubs.php');
                
    }
    
	/**
	 *	Conversion calculator
	 *
	 *	@access public
	 */
	public function calculator()
	{
		// Get request
		$from = Request::getString('calculate_from');
		$to = Request::getString('calculate_to');
		$value = Request::getFloat('calculate_value');
		
		// Get ratios (US measurements)
		$ratios = array();
		$ratios['c']['c'] = 1;
		$ratios['c']['fl_oz'] = 8.11536544;
		$ratios['c']['l'] = 0.24;
		$ratios['c']['ml'] = $ratios['c']['l'] * 1000;
		$ratios['c']['pt'] = 0.50721034;
		$ratios['c']['tbsp'] = 16;
		$ratios['c']['tsp'] = 48;
		$ratios['c']['fl_qt'] = 0.25;
		$ratios['c']['fl_gal'] = 0.0625;
		$ratios['c']['lb'] = 0.219066866866;
		$ratios['c']['oz'] = 3.505069869848;
		
		$ratios['fl_oz']['c'] = 1 / $ratios['c']['fl_oz'];
		$ratios['fl_oz']['fl_oz'] = 1;
		$ratios['fl_oz']['l'] = 0.02957353;
		$ratios['fl_oz']['ml'] = $ratios['fl_oz']['l'] * 1000;
		$ratios['fl_oz']['pt'] = 0.0625;
		$ratios['fl_oz']['tbsp'] = 1.97156864;
		$ratios['fl_oz']['tsp'] = 5.91470592;
		$ratios['fl_oz']['fl_qt'] = 0.031249999999;
		$ratios['fl_oz']['fl_gal'] = 0.0078124999999;
		$ratios['fl_oz']['lb'] = 0.027383358358;
		$ratios['fl_oz']['oz'] = 0.438133733731;
		
		$ratios['l']['c'] = 1 / $ratios['c']['l'];
		$ratios['l']['fl_oz'] = 1 / $ratios['fl_oz']['l'];
		$ratios['l']['l'] = 1;
		$ratios['l']['ml'] = 1000;
		$ratios['l']['pt'] = 2.11337642;
		$ratios['l']['tbsp'] = 66.66666667;
		$ratios['l']['tsp'] = 200;
		$ratios['l']['fl_qt'] = 1.0566882094;
		$ratios['l']['fl_gal'] = 0.26417205236;
		$ratios['l']['lb'] = 0.925941501176;
		$ratios['l']['oz'] = 14.815064018824;
		
		$ratios['ml']['c'] = $ratios['l']['c'] / 1000;
		$ratios['ml']['fl_oz'] = $ratios['l']['fl_oz'] / 1000;
		$ratios['ml']['l'] = 1 / 1000;
		$ratios['ml']['ml'] = 1;
		$ratios['ml']['pt'] = $ratios['l']['pt'] / 1000;
		$ratios['ml']['tbsp'] = $ratios['l']['tbsp'] / 1000;
		$ratios['ml']['tsp'] = $ratios['l']['tsp'] / 1000;
		$ratios['ml']['fl_qt'] = 0.0010566882094;
		$ratios['ml']['fl_gal'] = 0.00026417205236;
		$ratios['ml']['lb'] = 0.000925941501176;
		$ratios['ml']['oz'] = 0.014815064018824;
		
		$ratios['pt']['c'] = 1 / $ratios['c']['pt'];
		$ratios['pt']['fl_oz'] = 1 / $ratios['fl_oz']['pt'];
		$ratios['pt']['l'] = 1 / $ratios['l']['pt'];
		$ratios['pt']['ml'] = 1 / $ratios['ml']['pt'];
		$ratios['pt']['pt'] = 1;
		$ratios['pt']['tbsp'] = 31.54509824;
		$ratios['pt']['tsp'] = 94.63529472;
		$ratios['pt']['fl_qt'] = 0.5;
		$ratios['pt']['fl_gal'] = 0.125;
		$ratios['pt']['lb'] = 0.438133733731;
		$ratios['pt']['oz'] = 7.010139739696;
		
		$ratios['tbsp']['c'] = 1 / $ratios['c']['tbsp'];
		$ratios['tbsp']['fl_oz'] = 1 / $ratios['fl_oz']['tbsp'];
		$ratios['tbsp']['l'] = 1 / $ratios['l']['tbsp'];
		$ratios['tbsp']['ml'] = 1 / $ratios['ml']['tbsp'];
		$ratios['tbsp']['pt'] = 1 / $ratios['pt']['tbsp'];
		$ratios['tbsp']['tbsp'] = 1;
		$ratios['tbsp']['tsp'] = 3;
		$ratios['tbsp']['fl_qt'] = 0.015625;
		$ratios['tbsp']['fl_gal'] = 0.0039062499999;
		$ratios['tbsp']['lb'] = 0.013691679179;
		$ratios['tbsp']['oz'] = 0.219066866866;
		
		$ratios['tsp']['c'] = 1 / $ratios['c']['tsp'];
		$ratios['tsp']['fl_oz'] = 1 / $ratios['fl_oz']['tsp'];
		$ratios['tsp']['l'] = 1 / $ratios['l']['tsp'];
		$ratios['tsp']['ml'] = 1 / $ratios['ml']['tsp'];
		$ratios['tsp']['pt'] = 1 / $ratios['pt']['tsp'];
		$ratios['tsp']['tbsp'] = 1 / $ratios['tbsp']['tsp'];
		$ratios['tsp']['tsp'] = 1;
		$ratios['tsp']['fl_qt'] = 0.0052083333334;
		$ratios['tsp']['fl_gal'] = 0.0013020833333;
		$ratios['tsp']['lb'] = 0.00456389306;
		$ratios['tsp']['oz'] = 0.073022288955;

		$ratios['fl_qt']['c'] = 4;
		$ratios['fl_qt']['fl_oz'] = 32.000000001;
		$ratios['fl_qt']['l'] = 0.946352946;
		$ratios['fl_qt']['ml'] = 946.352946;
		$ratios['fl_qt']['pt'] = 2;
		$ratios['fl_qt']['tbsp'] = 64.000000001;
		$ratios['fl_qt']['tsp'] = 192;
		$ratios['fl_qt']['fl_qt'] = 1;
		$ratios['fl_qt']['fl_gal'] = 0.25;
		$ratios['fl_qt']['lb'] = 0.876267467462;
		$ratios['fl_qt']['oz'] = 14.020279479392;
		
		$ratios['fl_gal']['c'] = 16;
		$ratios['fl_gal']['fl_oz'] = 128;
		$ratios['fl_gal']['l'] = 3.785411784 ;
		$ratios['fl_gal']['ml'] = 3785.411784;
		$ratios['fl_gal']['pt'] = 8;
		$ratios['fl_gal']['tbsp'] = 256;
		$ratios['fl_gal']['tsp'] = 767.99999999;
		$ratios['fl_gal']['fl_qt'] = 4;
		$ratios['fl_gal']['fl_gal'] = 1;
		$ratios['fl_gal']['lb'] = 3.505069869848;
		$ratios['fl_gal']['oz'] = 56.08111791757;

		$ratios['lb']['c'] = 1 / $ratios['c']['lb'];
		$ratios['lb']['fl_oz'] = 1 / $ratios['fl_oz']['lb'];
		$ratios['lb']['l'] = 1 / $ratios['l']['lb'];
		$ratios['lb']['ml'] = 1 / $ratios['ml']['lb'];
		$ratios['lb']['pt'] = 1 / $ratios['pt']['lb'];
		$ratios['lb']['tbsp'] = 1 / $ratios['tbsp']['lb'];
		$ratios['lb']['tsp'] = 1 / $ratios['tsp']['lb'];
		$ratios['lb']['fl_qt'] = 1 / $ratios['fl_qt']['lb'];
		$ratios['lb']['fl_gal'] = 1 / $ratios['fl_gal']['lb'];
		$ratios['lb']['lb'] = 1;
		$ratios['lb']['oz'] = 16;
				
		$ratios['oz']['c'] = 1 / $ratios['c']['oz'];
		$ratios['oz']['fl_oz'] = 1 / $ratios['fl_oz']['oz'];
		$ratios['oz']['l'] = 1 / $ratios['l']['oz'];
		$ratios['oz']['ml'] = 1 / $ratios['ml']['oz'];
		$ratios['oz']['pt'] = 1 / $ratios['pt']['oz'];
		$ratios['oz']['tbsp'] = 1 / $ratios['tbsp']['oz'];
		$ratios['oz']['tsp'] = 1 / $ratios['tsp']['oz'];
		$ratios['oz']['fl_qt'] = 1 / $ratios['fl_qt']['oz'];
		$ratios['oz']['fl_gal'] = 1 / $ratios['fl_gal']['oz'];
		$ratios['oz']['lb'] = 1 / $ratios['lb']['oz'];
		$ratios['oz']['oz'] = 1;
		
		// Some display variables
		$ratioNames = array();
		$ratioNames['c'] = 'cups';
		$ratioNames['fl_oz'] = 'fl oz';
		$ratioNames['l'] = 'liters';
		$ratioNames['ml'] = 'ml';
		$ratioNames['pt'] = 'pints';
		$ratioNames['tbsp'] = 'tbsps';
		$ratioNames['tsp'] = 'tsps';
		$ratioNames['fl_qt'] = 'qt (liquid)';
		$ratioNames['fl_gal'] = 'gal (liquid)';
		$ratioNames['lb'] = 'pound';		
		$ratioNames['oz'] = 'oz';
		
		// Do some converting
		$result = null;
		if (isset($ratios[$from][$to])) {
			$result = $ratios[$from][$to] * $value;
			
		// Tried but phailed
		} else if ($from && $to) {
			Messages::addMessage('Cannot convert from '.$ratioNames[$from].' to '.$ratioNames[$to].'.', 'error');
		}
		
		// Load template
		switch ($this->_doc->getFormat()) {
			case 'json':
				ob_start();
				break;
		}
		include(BLUPATH_TEMPLATES.'/recipes/calculator.php');
		switch ($this->_doc->getFormat()) {
			case 'json':
				$response = array();
				$response['form'] = ob_get_clean();
				echo json_encode($response);
				break;
		}
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
	 *	RSS Feeds
	 *
	 *	@access public
	 */
	public function rss()
	{
		$this->_doc->setFormat('xml');
		$this->_view = 'recipe_rss';
		return $this->view();
	}

	/**
	 *	Add the current recipe to a cookbook (you get to pick one)
	 *
	 *	@access public
	 */
	public function add_to_cookbook()
	{
		// Check for a recipe
		$itemsModel = BluApplication::getModel('items');
		if (!$recipe = $itemsModel->getItem($this->_itemId)) {
			return $this->_errorRedirect();
		}
		
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = $itemsModel->getTaskLink($recipe['link'], 'add_to_cookbook');
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('cookbook_add_recipe_login', array(
				'recipe' => $recipe['title']
			)), 'warn');
		}
		
		// Load template
		include(BLUPATH_TEMPLATES.'/recipes/add_to_cookbook.php');
	}
	
	/**
	 *	Cookbooks available for adding recipes to
	 *
	 *	@access public
	 */
	public function add_to_cookbook_items()
	{
		// Check for a recipe
		$itemsModel = BluApplication::getModel('items');
		if (!$recipe = $itemsModel->getItem($this->_itemId)) {
			return $this->_errorRedirect();
		}
		
		// Check for user
		if (!$user = $this->_requireUser()) {
			$url = $itemsModel->getTaskLink($recipe['link'], 'add_to_cookbook');
			$url = '/account/login?redirect='.base64_encode($url);
			return $this->_redirect($url, Text::get('cookbook_add_recipe_login', array(
				'recipe' => $recipe['title']
			)), 'warn');
		}
		
		// Get search
		$searchTerm = Request::getString('searchTerm');
		if ($searchTermExtra = Request::getString('searchterm_extra')) {
			$searchTerm = trim($searchTerm.' '.$searchTermExtra);
		}
		
		// Get/set sort
		$sort = $this->_getSort();
		Session::set('sort', $sort);
		
		// Get all available cookbooks
		$cookbooks = $itemsModel->getCookbooks(null, null, $sort, $searchTerm);
		$cookbooks = $itemsModel->filterLiveCookbooks($cookbooks);
		if ($user['type'] != 'admin') {
			$cookbooks = $itemsModel->filterAuthorCookbooks($cookbooks, $user['id']);
		}
		
		// Restrict
		$page = Request::getInt('page', 1);
		$limit = $this->_getLimit();
		if ($limit) {
			$cookbooks = array_slice($cookbooks, ($page - 1) * $limit, $limit, true);
		}

		// Get/set layout and ordering
		$layout = $this->_getLayout();
		Session::set('layout', $layout);
		
		// Do display stuff
		$baseUrl = $itemsModel->getTaskLink($recipe['link'], 'add_to_cookbook');
		$pathway = $this->_getBreadcrumbs();
		$documentTitle = $listingTitle = 'Select a cookbook';
		
		// Display
		$this->_view = 'cookbook_add_recipe';
		return $this->_listItemGroups($cookbooks, 1, null, $baseUrl, $searchTerm, $searchTermExtra, $sort, $layout, $pathway, $documentTitle, $listingTitle);
	}
	

	public function shopping_list() 
	{
		include(BLUPATH_TEMPLATES.'/recipes/shoppinglist.php');
	}
	/**
	 * Show the shopping list
	 * 
	 * @access public
	 */
	public function shopping_list_items()
	{
		$shoppinglist = Session::get('shoppinglist');
		$hiddenIngredients = Session::get('shoppinglist_hiddeningredients',array());	

		// Get recipe information
		$itemsModel = BluApplication::getModel('items');
		$recipes = array();
		foreach ($shoppinglist as $item) {
			$recipes[] = $itemsModel->getItem($item['id']);
			$recipes[count($recipes)-1]['portions'] = ($item['portions']?$item['portions']:1);
                }
		if (!count(array_filter($recipes))) {
			Messages::addMessage('Your shopping list is empty. Click <a href="'.SITEURL.'/recipes">here</a> to browse our recipes!','warn');
			echo Messages::getMessages();
			return;
		}
		$groupBy = Request::getString('group','foodGroup'); //recipes|fooodGroup
		
		// Merge ingredients
		$ingredients = array();
		foreach ($recipes as $recipeId => &$recipe) {
			if (!count($recipe['tidyIngredients'])) continue;
			

			// Work out how many portions the entered recipe caters for.
			$recipePortions = 1;
			if (isset($recipe['metaportions'])) { 	// THIS DOES NOT WORK BECAUSE IT IS WRONG. Change it.
				$recipePortions = $recipe['metaportions'];
			}
			// Work out scaling factor
			$portions = $recipe['portions']/$recipePortions;


			foreach ($recipe['tidyIngredients'] as $ing) {
				
				// Filter out corrupt data (strings? wha...?)
				if (!is_array($ing)) {
					continue;
				}

				// Check if the product is hidden
				if (isset($hiddenIngredients[$ing['NDB_No']])) {
					continue;
				}

				// Group the product, and get info about it
				$groupKey = ($groupBy == 'foodGroup')?$ing['details']['FdGrp_Cd']:$recipeId;
				if (isset($ingredients[$groupKey]) && array_key_exists($ing['NDB_No'],$ingredients[$groupKey])) {
					$ingredients[$groupKey][$ing['NDB_No']]['amount'] += $ing['amount']*$ing['details']['weights'][$ing['weightId']]['Gm_Wgt'] * $portions;
				}
				else {
					$data = Array(
						'amount' => $ing['amount'] * $ing['details']['weights'][$ing['weightId']]['Gm_Wgt'] * $portions,
						'weight' => $ing['details']['weights'][$ing['weightId']],
						'text' => $ing['details']['Long_Desc'],
						'foodGroup' => $ing['details']['FdGrp_Cd'],
						'units' => $ing['details']['weights'],
						'recipe' => $recipeId,
					);
					$ingredients[$groupKey][$ing['NDB_No']] = $data;
				}
			}
		}
		unset($recipe);

		$foodGroups = Array(
			'0100' => 'Dairy and Egg Products',
			'0200' => 'Spices and Herbs',
			'0300' => 'Baby Foods',
			'0400' => 'Fats and Oils',
			'0500' => 'Poultry Products',
			'0600' => 'Soups, Sauces, and Gravies',
			'0700' => 'Sausages and Luncheon Meats',
			'0800' => 'Breakfast Cereals',
			'0900' => 'Fruits and Fruit Juices',
			'1000' => 'Pork Products',
			'1100' => 'Vegetables and Vegetable Products',
			'1200' => 'Nut and Seed Products',
			'1300' => 'Beef Products',
			'1400' => 'Beverages',
			'1500' => 'Finfish and Shellfish Products',
			'1600' => 'Legumes and Legume Products',
			'1700' => 'Lamb, Veal, and Game Products',
			'1800' => 'Baked Products',
			'1900' => 'Sweets',
			'2000' => 'Cereal Grains and Pasta',
			'2100' => 'Fast Foods',
			'2200' => 'Meals, Entrees, and Sidedishes',
			'2500' => 'Snacks',
			'3500' => 'Ethnic Foods',
			'3600' => 'Restaurant Foods'
		);

		include(BLUPATH_TEMPLATES.'/recipes/shoppinglist_items.php');
	}

	private function _calculateUnitSort($a,$b) {
		if ($a['gm'] > $b['gm']) return +1;
		elseif ($a['gm'] < $b['gm']) return -1;
		
		if (!isset($a['min'])) $a['min'] = 0;
		if (!isset($b['min'])) $b['min'] = 0;
		if ($a['min'] > $b['min']) return +1;
		elseif ($a['min'] < $b['min']) return -1;
		return 0;
	}
	private function _calculateUnit($gm_weight,$units)
	{
		// What is the difference between a clove and a cloves of garlic? None, except you need 3 clove to get a cloves.  
		// We shall ignore that piece of USDA logic, and move to a cloves when we have more than 1.
		uasort($units,array($this,'_calculateUnitSort'));
		$current = reset($units);
		foreach ($units as &$unit) {
			if ($gm_weight < $unit['gm'] || ($current['gm'] == $unit['gm'] && round($gm_weight/$current['gm']) <= 1)) {
				$current['quantity'] = $gm_weight/$current['gm'];
				return $current;
			}
			$current = $unit;
		}
		$current['quantity'] = $gm_weight/$current['gm'];
		return $current;
	}


	public function shopping_list_hideingredient()
	{
		$hidden = Session::get('shoppinglist_hiddeningredients',array());
		// Add ID of ingredient
		$hidden[$this->_args[0]] = 1;
		Session::set('shoppinglist_hiddeningredients',$hidden);

		$this->_redirect('/recipes/shopping_list');
	}
	/**
	 * Add an item to the shopping list
	 *
	 * @access public
	 */
	public function shopping_list_add($redirect=true)
	{
		$shoppinglist = Session::get('shoppinglist');
		
		// Get the recipe to add
                $itemsModel = BluApplication::getModel('items');
                if (!$recipe = $itemsModel->getItem($this->_itemId)) {
                        return $this->_errorRedirect();
                }

		$portions = Request::getInt('portions',1);

		// check if it is in the list
		$found = false;
		foreach ($shoppinglist as $item) {
			if ($item['id'] == $this->_itemId) {
				$found = true;
				break;
			}
		}

		// Add it
		if (!$found) {
			$shoppinglist[] = Array('id' => $this->_itemId,'portions' => $portions);
			Session::set('shoppinglist',$shoppinglist);
		}
		
		// Redirect to shopping_list
		if ($redirect) {
			Messages::addMessage('Added recipe to your shopping list. <a href="'.SITEURL.'/recipes/shopping_list">Click here to view your shopping list.</a>','info');
			$this->_redirect($recipe['link']);
		}
	}
	

	/**
	 * Remove an item from the shopping list
	 *
	 * @access public
	 */
	public function shopping_list_remove($redirect=true)
	{
		$shoppinglist = Session::get('shoppinglist');

		// Get the recipe to add
                $itemsModel = BluApplication::getModel('items');
                if (!$recipe = $itemsModel->getItem($this->_itemId)) {
                        return $this->_errorRedirect();
                }

		// Remove it
		foreach ($shoppinglist as $listKey => $listValue) {
			if ($listValue['id'] == $this->_itemId)
				unset($shoppinglist[$listKey]);
		}
		if ($redirect)
			Messages::addMessage('Removed recipe from your shopping list','info');
		$shoppinglist = array_values($shoppinglist);
		Session::set('shoppinglist',$shoppinglist);

		// Redirect to shopping_list
		if ($redirect)
			$this->_redirect('/recipes/shopping_list');
	}

	/**
	 * Update an item in the shopping list
	 *
	 * @access public
	 */
	public function shopping_list_edit()
	{
		$this->shopping_list_remove(false);
		$this->shopping_list_add(false);
		$this->_redirect('/recipes/shopping_list');
	}

	/**
	 *	Advanced search
	 *
	 *	@access public
	 */
	public function advanced_search()
	{
		// Do redirect
		if (Request::getBool('submit')) {
			$url = '';
			
			// Build redirect URL
			switch (Request::getString('searchtype')) {
				case 'cookbook':
					$url .= '/cookbooks';
					break;
					
				case 'recipe':
				default:
					
					// Add meta filter slugs
					if ($metaFilters = Request::getArray('filters')) {
						$metaFilters = array_filter($metaFilters);
					}
					if (empty($metaFilters)) {
						$url .= '/recipes';
					} else {
						sort($metaFilters);
						foreach ($metaFilters as $slug) {
							$url .= '/'.$slug;
						}
					}
					break;
			}
			
			$url .= '?advanced=1';	// Such a bodge....
			if ($searchTerm = Request::getString('searchterm')) {
				$url .= '&searchterm='.urlencode($searchTerm);
			}
			
			// Kthxbai
			return $this->_redirect($url);
		}
		
		// Get meta filters - this is something of a cludge.
		$metaModel = BluApplication::getModel('meta');
		$hierarchy = $metaModel->getHierarchy();
		$filters = array();	
		foreach ($hierarchy[2]['values'] as $filterId => $subHierarchy) {
			if (!empty($subHierarchy['values'])) {
				$filters[$filterId] = $subHierarchy;
			}
		}
		
		// Load template
		$this->_doc->setTitle('Advanced Search');
		include(BLUPATH_TEMPLATES.'/recipes/advanced_search.php');
	}
	
	/**
	 *	Recipe count for given filters
	 *
	 *	@access public
	 */
	public function advanced_search_count()
	{
		// Prepare
		$itemsModel = BluApplication::getModel('items');
		$count = null;
		$searchTerm = Request::getString('searchterm');
		
		// Get thingymabobs
		switch (Request::getString('searchtype')) {
			case 'cookbook':
				
				// Get all cookbooks
				$cookbooks = $itemsModel->getCookbooks(null, null, null, $searchTerm);
				
				// Return
				$count = count($cookbooks);
				break;
				
			case 'recipe':
			default:
				
				// Apply filters, if given
				if ($metaFilters = Request::getArray('filters')) {
					$metaFilters = array_filter($metaFilters);
				}
				if (empty($metaFilters)) {
					$metaFilters = array();	// So that it fits in with ItemsModel::getItems.
				} else {
					
					// Parse out filter IDs
					$metaModel = BluApplication::getModel('meta');
					$metaModel->applyFilters($metaFilters);
					$metaFilters = $metaModel->getFilters();
				}
				
				// Get recipes
				$items = $itemsModel->getItems(null, null, null, $metaFilters, $searchTerm);
				$items = $itemsModel->filterTypeItems($items, 'recipe');
				
				// Return
				$count = count($items);
				break;
		}
		
		// Output
		echo json_encode($count);
	}
}
