<?php

/**
 * Metadata Model
 *
 * @package BluApplication
 * @subpackage BluModels
 */
class ClientMetaModel extends BluModel
{
	/**
	 *	Meta value slug mapping
	 *
	 *	@access protected
	 *	@var array
	 */
	protected $_valueSlugMapping;
	
	/**
	 *	Meta value group mapping
	 *
	 *	@access protected
	 *	@var array
	 */
	protected $_valueGroupMapping;
	
	/**
	 *	Meta group slug mapping
	 *
	 *	@access protected
	 *	@var array
	 */
	protected $_groupSlugMapping;
	
	/**
	 *	Meta group type mapping
	 *
	 *	@access protected
	 *	@var array
	 */
	protected $_groupTypeMapping;
	
	/**
	 *	Selector slug mapping
	 *
	 *	@access protected
	 *	@var array
	 */
	protected $_selectorSlugMapping;
	
	/**
	 *	Constructor
	 *
	 *	@access public
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_valueSlugMapping = array();
		$this->_valueGroupMapping = array();
		$this->_groupSlugMapping = array();
		$this->_groupTypeMapping = array();
		$this->_selectorSlugMapping = array();
	}
	
	/**
	 *	Get meta value slug mapping
	 *
	 *	@access protected
	 *	@param bool Rebuild
	 *	@param string Language code
	 *	@return array Meta value Slug to ID
	 */
	protected function _getMetaValueSlugMapping($forceRebuild = false, $langCode = null)
	{
		// Get language
		if (!$langCode) {
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
		}
		
		// Get mapping
		if ($forceRebuild || !isset($this->_valueSlugMapping[$langCode])) {
			$cacheKey = 'metaValueSlugMapping_'.$langCode;
			$this->_valueSlugMapping[$langCode] = $forceRebuild ? false : $this->_cache->get($cacheKey);
			if ($this->_valueSlugMapping[$langCode] === false) {
				$defaultLang = BluApplication::getSetting('defaultLang');
				
				$query = 'SELECT lmv.slug, lmv.id
					FROM `languageMetaValues` AS `lmv`
					LEFT JOIN metaValues AS mv ON mv.id = lmv.id
					ORDER BY FIELD(lmv.lang, "'.$this->_db->escape($defaultLang).'", "'.$this->_db->escape($langCode).'")';
				$this->_db->setQuery($query);
				$this->_valueSlugMapping[$langCode] = $this->_db->loadResultAssocArray('slug', 'id');
				
				$this->_cache->set($cacheKey, $this->_valueSlugMapping[$langCode]);
			}
		}
		return $this->_valueSlugMapping[$langCode];
	}
	
	/**
	 *	Get meta value group mapping
	 *
	 *	@access public
	 *	@param bool Rebuild
	 *	@return array Meta value ID to Meta group ID
	 */
	public function getMetaValueGroupMapping($forceRebuild =false)
	{
		if (empty($this->_valueGroupMapping)) {
			
			// Get from cache
			$cacheKey = 'metaValueGroupMapping';
			$this->_valueGroupMapping = $forceRebuild ? false : $this->_cache->get($cacheKey);
			if ($this->_valueGroupMapping === false) {
				$query = 'SELECT mv.id, mv.groupId
					FROM `metaValues` AS `mv`';
				$this->_db->setQuery($query);
				$this->_valueGroupMapping = $this->_db->loadResultAssocArray('id', 'groupId');
				
				// Store in cache
				$this->_cache->set($cacheKey, $this->_valueGroupMapping, null, null, array('compress' => true));
			}
		}
		return $this->_valueGroupMapping;
	}
	
	/**
	 *	Get meta group slug mapping
	 *
	 *	@access protected
	 *	@param bool Rebuild
	 *	@param string Language code
	 *	@return array Meta group Slug to ID
	 */
	protected function _getMetaGroupSlugMapping($forceRebuild = false, $langCode = null)
	{
		// Get language details
		if (!$langCode) {
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
		}
		
		// Get mapping
		if ($forceRebuild || !isset($this->_groupSlugMapping[$langCode])) {
			$cacheKey = 'metaGroupSlugMapping_'.$langCode;
			$this->_groupSlugMapping[$langCode] = $forceRebuild ? false : $this->_cache->get($cacheKey);
			//$this->_groupSlugMapping[$langCode] = false;
			if ($this->_groupSlugMapping[$langCode] === false) {
				$defaultLang = BluApplication::getSetting('defaultLang');
				
				$query = 'SELECT lmg.slug, lmg.id
					FROM `languageMetaGroups` AS `lmg`
					ORDER BY FIELD(lmg.lang, "'.$this->_db->escape($defaultLang).'", "'.$this->_db->escape($langCode).'")';
				$this->_db->setQuery($query);
				$this->_groupSlugMapping[$langCode] = $this->_db->loadResultAssocArray('slug', 'id');
				
				$this->_cache->set($cacheKey, $this->_groupSlugMapping[$langCode]);
			}
		}
		
		// Return
		return $this->_groupSlugMapping[$langCode];
	}
	
	/**
	 *	Get meta group type mapping
	 *
	 *	@access protected
	 *	@param bool Rebuild
	 *	@return array Meta group ID to Type
	 */
	protected function _getMetaGroupTypeMapping($forceRebuild = false)
	{
		if (empty($this->_groupTypeMapping)) {
			
			// Get from cache
			$cacheKey = 'metaGroupTypeMapping';
			$this->_groupTypeMapping = $forceRebuild ? false : $this->_cache->get($cacheKey);
			if ($this->_groupTypeMapping === false) {
				$query = 'SELECT mg.id, mg.type
					FROM metaGroups AS mg';
				$this->_db->setQuery($query);
				$this->_groupTypeMapping = $this->_db->loadResultAssocArray('id', 'type');
				$this->_groupTypeMapping['price'] = 'price';
				
				// Store in cache
				$this->_cache->set($cacheKey, $this->_groupTypeMapping);
			}
		}
		return $this->_groupTypeMapping;
	}
	
	/**
	 *	Get meta group filterable flag mapping
	 *
	 *	@access protected
	 *	@param bool Rebuild
	 *	@return array Meta group IDs
	 */
	protected function _getMetaGroupFilterableMapping($forceRebuild = false)
	{
		static $mapping;
		if ($forceRebuild || !$mapping) {
			$cacheKey = 'metaGroupFilterableMapping';
			$mapping = $forceRebuild ? false : $this->_cache->get($cacheKey);
			if ($mapping === false) {
				$query = 'SELECT mg.id
					FROM `metaGroups` AS `mg`
					WHERE mg.filterable = 1';
				$this->_db->setQuery($query);
				$mapping = $this->_db->loadResultAssocArray('id', 'id');
				$mapping['price'] = 'price';
				
				$this->_cache->set($cacheKey, $mapping);
			}
		}
		return $mapping;
	}
	
	/**
	 *	Get meta selector slug mapping
	 *
	 *	@access protected
	 *	@param bool Rebuild
	 *	@param string Language code
	 *	@return array Meta selector Slug to ID
	 */
	protected function _getMetaSelectorSlugMapping($forceRebuild = false, $langCode = null)
	{
		// Get language
		if (!$langCode) {
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
		}
		
		// Get mapping
		if (empty($this->_selectorSlugMapping[$langCode])) {
			$cacheKey = 'metaSelectorSlugMapping_'.$langCode;
			$this->_selectorSlugMapping[$langCode] = $forceRebuild ? false : $this->_cache->get($cacheKey);
			if ($this->_selectorSlugMapping[$langCode] === false) {
				$defaultLang = BluApplication::getSetting('defaultLang');
				
				$query = 'SELECT lms.slug, lms.id
					FROM `languageMetaSelectors` AS `lms`
					ORDER BY FIELD(lms.lang, "'.$this->_db->escape($defaultLang).'", "'.$this->_db->escape($langCode).'")';
				$this->_db->setQuery($query);
				$this->_selectorSlugMapping[$langCode] = $this->_db->loadResultAssocArray('slug', 'id');
				
				$this->_cache->set($cacheKey, $this->_selectorSlugMapping[$langCode]);
			}
		}
		
		// Return
		return $this->_selectorSlugMapping[$langCode];
	}
	
	/**
	 *	Get internal meta value to selector mapping
	 *
	 *	@access protected
	 *	@param bool Rebuild
	 *	@return array Meta value ID to selector ID
	 */
	protected function _getMetaValueSelectorMapping($forceRebuild = false)
	{
		static $mapping;
		if ($forceRebuild || !isset($mapping)) {
			$cacheKey = 'metaValueSelectorMapping';
			$mapping = $forceRebuild ? false : $this->_cache->get($cacheKey);
			if ($mapping === false) {
				$query = 'SELECT mh.valueId, mh.aliasId
					FROM `metaHierarchy` AS `mh`
					WHERE mh.aliasType = "selector_replace"';
				$this->_db->setQuery($query);
				$mapping = $this->_db->loadResultAssocArray('valueId', 'aliasId');
				
				$this->_cache->set($cacheKey, $mapping);
			}
		}
		return $mapping;
	}
	
	/**
	 * Get meta groups
	 *
	 *	@access public
	 *	@param bool Rebuild
	 *	@param string Language code
	 * @return array List of meta groups
	 */
	public function getGroups($forceRebuild = false, $incRangeValues = true, $langCode = null)
	{
		// Get language details
		if (!$langCode) {
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
		}
		
		// Get groups from cache/DB
		static $groups;
		if ($forceRebuild || !isset($groups[$langCode])) {
			
			// Get raw groups
			$rawGroups = $this->_getGroupStats($forceRebuild);
			
			// Add group details
			if (!empty($rawGroups)) {
				foreach ($rawGroups as $groupId => $rawGroup) {
					$groups[$langCode][$groupId] = $this->getGroup($groupId, $forceRebuild, null, $incRangeValues, $langCode);
				}
			}
		}
		
		// Return
		return $groups[$langCode];
	}
	
	/**
	 *	Get raw groups data
	 *
	 *	@access private
	 *	@param bool Force Rebuild
	 *	@return array Groups with stats
	 */
	private function _getGroupStats($forceRebuild = false)
	{
		$cacheKey = 'metaGroupStats';
		$groups = $forceRebuild ? false : $this->_cache->get($cacheKey);
		if ($groups === false) {
			
			// Get groups with stats
			$query = 'SELECT mg.id, COUNT(mv.id) AS `metaValueCount`
				FROM `metaGroups` AS `mg`
					LEFT JOIN `metaValues` AS `mv` ON mg.id = mv.groupId
				GROUP BY mg.id
				ORDER BY mg.sequence, mg.internalName';
			$this->_db->setQuery($query);
			$groups = $this->_db->loadAssocList('id');
			
			$this->_cache->set($cacheKey, $groups);
		}
		return $groups;
	}
	
	/**
	 *	Get availability of a meta group and its values, given items.
	 *
	 *	@access protected
	 *	@param int Meta group ID
	 *	@param array Items
	 *	@param array Selected filters
	 *	@param bool Whether to clear (unselect) unavailable filters
	 *	@return array Group availability and group values availability
	 */
	protected function _getGroupAvailability($groupId, $items, $filters, $clear)
	{
		// Get from cache
		$cacheKey = 'metaGroupAvailability_'.$groupId.'_'.md5(serialize(array_keys($items))).'_'.md5(serialize($filters)).($clear ? '_clear' : '');
		$availability = $this->_cache->get($cacheKey);

		if ($availability === false) {
			
			// Get group
			$availability = array();
			$group = $this->getGroup($groupId);
			
			// Build base value filter
			$baseGroupFilters = $filters ? $filters : array();
			unset($baseGroupFilters[$groupId]);
			
			// Prepare counts
			$availability['numItems'] = 0;
			$availability['numItemsAvailable'] = 0;
			$availability['numValuesWithItems'] = 0;
			$availability['numValuesWithItemsAvailable'] = 0;
			
			// Filters from aggregator values' groups?
			$availability['selected'] = array();
			if (!empty($filters[$groupId])) {
				$availability['selected'] = $filters[$groupId];
			}
			switch ($group['type']) {
				case 'price':
				case 'numberrange':
					$availability['selectedMin'] = isset($filters[$groupId]['min']) ? $filters[$groupId]['min'] : false;
					$availability['selectedMax'] = isset($filters[$groupId]['max']) ? $filters[$groupId]['max'] : false;
					break;
			}
			
			// Build value availability
			$availability['values'] = array();
			foreach ($group['values'] as $metaValueKey => $metaValue) {
				
				// Start building meta value availability
				$availability['values'][$metaValueKey] = array();
				$valueAvailability =& $availability['values'][$metaValueKey];
				
				// Build value filter
				switch ($group['type']) {
					case 'price':
					case 'numberrange':
						$valueFilters = array(
							$groupId => array(
								'min' => $metaValue['value']['min'],
								'max' => $metaValue['value']['max']
							)
						);
						break;
						
					case 'numberpick';
						$valueFilters = array(
							$groupId => array(
								(string) $metaValue['value'] => $metaValue['value']
							)
						);
						break;
						
					case 'pick':
					default:
						$valueFilters = array(
							$groupId => array(
								$metaValue['id'] => $metaValue['id']
							)
						);
						break;
				}
				
				// Check number of available items using full combination
				$valueAvailability['numItems'] = count($this->filterItems($items, $baseGroupFilters + $valueFilters));
				if ($displayableItems = $metaValue['display'] * $valueAvailability['numItems']) {
					$availability['numItems'] += $displayableItems;
					$availability['numValuesWithItems']++;
				}
				
				// Check number of available items using just the single filter
				$valueAvailability['numItemsAvailable'] = count($this->filterItems($items, $valueFilters));
				if ($displayableItemsAvailable = $metaValue['display'] * $valueAvailability['numItemsAvailable']) {
					$availability['numItemsAvailable'] += $displayableItemsAvailable;
					$availability['numValuesWithItemsAvailable']++;
				}
				
				// Set states
				if (!$clear || $valueAvailability['numItems']) {
					switch ($group['type']) {
						case 'price':
						case 'numberrange':
							$valueAvailability['selected'] = ($availability['selectedMin'] == $metaValue['value']['min']) && ($availability['selectedMax'] == $metaValue['value']['max']);
							break;
							
						case 'numberpick':
							$valueAvailability['selected'] = in_array($metaValue['value'], $availability['selected']);
							break;
							
						case 'pick':
						default:
							$valueAvailability['selected'] = in_array($metaValue['id'], $availability['selected']);
							break;
					}
				} else {
					$valueAvailability['selected'] = false;
				}
				$valueAvailability['disabled'] = ($valueAvailability['numItems'] < 1) && !$group['neverExclude'];
			}
			
			// Store in cache
			$this->_cache->set($cacheKey, $availability);
		}
		
		// Return
		return $availability;
	}
	
	/**
	 *	Get availability for a single meta selector, given items
	 *
	 *	@access protected
	 *	@param int Meta selector ID
	 *	@param array Items
	 *	@param array Selected filters
	 *	@param bool Whether to clear (unselect) unavailable filters
	 *	@return array Selector availability
	 */
	protected function _getSelectorAvailability($selectorId, $items, $filters, $clear)
	{
		// Get from cache
		$cacheKey = 'metaSelectorAvailability_'.$selectorId.'_'.md5(serialize(array_keys($items))).'_'.md5(serialize($filters)).($clear ? '_clear' : '');
		$availability = $this->_cache->get($cacheKey);
		if ($availability === false) {
			
			// Get selector
			$availability = array();
			$selector = $this->getSelector($selectorId);
			
			// Build base value filter, and value filter
			$totalFilters = $filters ? $filters : array();
			$valueFilters = array();
			/*
				foreach ($aggregator['values'] as $groupId => $values) {
					unset($totalFilters[$groupId]);
				}
				*/
			$availability['selected'] = true;
			foreach ($selector['values'] as $groupId => $values) {
				foreach ($values as $metaValue) {
					$totalFilters[$groupId][$metaValue['id']] = $metaValue['id'];
					$valueFilters[$groupId][$metaValue['id']] = $metaValue['id'];
					
					// Not all selector values were applied as filters
					if (!isset($filters[$groupId][$metaValue['id']])) {
						$availability['selected'] = false;
					}
				}
			}
			
			$availability['numItems'] = count($this->filterItems($items, $totalFilters));
			$availability['numItemsAvailable'] = count($this->filterItems($items, $valueFilters));
			
			// Set states
			if ($clear && !$availability['numItems']) {
				$availability['selected'] = false;
			}
			$availability['disabled'] = $availability['numItems'] < 1;
			
			// Store in cache
			$this->_cache->set($cacheKey, $availability);
		}
		
		// Return
		return $availability;
	}

	/**
	 * Set availability and selected status for a set of meta groups
	 *
	 * @param array Array of meta groups or meta selectors
	 * @param array Base array of available items
	 * @param array Array of selected filters
	 * @param bool Whether to clear (unselect) unavailable filters
	 */
	public function setAvailability(&$aggregators, $items, $filters, $clear = false)
	{
		// Set selected meta values
		if (!empty($aggregators)) {
			foreach ($aggregators as &$aggregator) {
				
				// Selector
				if ($aggregator['type'] == 'selector') {
					$aggregator += $this->_getSelectorAvailability($aggregator['id'], $items, $filters, $clear);
					
				// Meta group
				} else {
					$availability = $this->_getGroupAvailability($aggregator['id'], $items, $filters, $clear);
					
					// Merge in availability
					//$aggregator = array_replace_recursive($aggregator, $availability);	// I *need* PHP 5.3 damnit!
					
					// Split out value availability, because we can't do it recursively, groan.
					$valueAvailability = $availability['values'];
					unset($availability['values']);
					
					$aggregator += $availability;
					foreach ($aggregator['values'] as $key => &$value) {
						$value += (array) $valueAvailability[$key];
					}
					unset($value);
				}
			}
			unset($aggregator);
		}
	}

	/* @author by leon
	 * @return array
	 * @desc get categories by slug
	 */
	
	public function getCorrectFilterSearchCategory($slug)
	{
		$r = false;
		if($slug[1] == 'recipes')
		{
			$sql = 'SELECT id from metaValues where metaValues.internalName="' . $slug[2] . '" LIMIT 0,1';
			//echo $sql;
			$t = mysql_query($sql);
			if($t){
				$row = mysql_fetch_array($t);
				$r = $row['id'];
			}
		}
		return $r;
		//exit;
	}
	
	/* @author by leon
	 * @return array
	 * replacement of the filterItems
	 */
	public function getCorrectItems($items, $filters, $searchFlag)
	{
		//echo "<pre>";
		//print_r($filters);
		//echo "</pre>";
		//for the strange backend
		if(isset($filters[2][2]) && $filters[2][2] == 2)
		{
			unset($filters[2]);
		}
		// always have problem. Let's try the most basical way
		if(array_key_exists('4',$filters))
		{
			$type = 'recipe';
		}else{
			$type = 'article';
		}
		
		if(count($filters) == 1)
		{
			// "category";
			$fatherCategory = ($type == 'recipe')?$filters[4]:$filters[2];
			foreach($fatherCategory as $k=>$v)
			{
				$metaValueId = $v;
			}
		}else if(count($filters) == 2){
			if($searchFlag)
			{
				if(isset($filters[2]))unset($filters[2]);
				foreach($filters as $k=>$v)
				{
					foreach($v as $sk=>$sv)
					$metaValueId = $sv;
				}				
			}else{
				// "subCategory";
				if($type == 'recipe')
				{
					unset($filters[4]);
				}else{
					if(isset($filters[2]))unset($filters[2]);
				}
				foreach($filters as $k=>$v)
				{
					foreach($v as $sk=>$sv)
					$metaValueId = $sv;
				}
			}
		}
		$sql = "SELECT * FROM articleMetaValues as amv LEFT JOIN articles as a on a.id=amv.articleId WHERE amv.valueId=" . $metaValueId;
		//echo $sql;
		$items2 = $this->_db->loadArrayById($sql,'articleId');		
		$items = array_intersect($items,$items2);
		return $items;
	}
	
	/**
	 * Filter items using meta filter
	 *
	 * @param array Array of item IDs
	 * @param array Array of meta filters IDs (grouped by meta group)
	 *	@param bool Do OR filtering
	 * @return array Array of filtered items
	 */
	public function filterItems($items, $filters, $or = false)
	{
		// Ignore groups that aren't filterable
		$filters = $this->filterFilterableGroups($filters);
		
		// Nothing 'ado.
		if (empty($items) || empty($filters)) {
			return $items;
		}

		// Get meta group type mapping
		$groupTypeMapping = $this->_getMetaGroupTypeMapping();

		// Determine valid items/build list of groups to check later
		$validMetaValues = array();
		$validGroupItems = array();
		$rangeMetaGroups = array();
		foreach ($filters as $groupId => $filterValues) {
			$groupType = $groupTypeMapping[$groupId];

			// Pick type - get valid items
			switch ($groupType) {
				case 'pick': 
				case 'numberpick':
					$validGroupItems[$groupId] = array();

					// Get relevant meta value to items mapping
					$mappingType = ($groupType == 'pick') ? 'id' : 'value';
					
					// Check mappings exist for group
					if ($metaGroupMappings = $this->_getMetaItemMapping($groupId)) {
						
						// For each meta value specified as valid by the filter...
						foreach ($filterValues as $metaValue) {

							// ...merge (OR) in items to meta group item list from the global mappings
							if (isset($metaGroupMappings[$metaValue])) {
								$validGroupItems[$groupId] = array_merge($validGroupItems[$groupId], $metaGroupMappings[$metaValue]);
							}
						}
					}
					break;

				// Number range - store to check later
				case 'numberrange':
				case 'price':
					$rangeMetaGroups[$groupId] = $groupType;
					break;
					
				// Keywords - *ignore*
				// @todo shouldn't always ignore, since we are working off the filterable flag now.
				case 'keywords':
					break;
			}
		}

		// Merge (OR) over all valid meta group items
		if ($or) {
			$validItems = array();
			foreach ($validGroupItems as $groupItems) {
				$validItems += array_flip($groupItems);
			}
			$items = array_intersect_key($items, $validItems);
			
		// Intersect (AND) over all valid meta group items
		} else {
			foreach ($validGroupItems as $groupItems) {
				$items = array_intersect_key($items, array_flip($groupItems));
			}
		}

		// Check remaining number range groups against item meta values
		if (!empty($rangeMetaGroups) && !empty($items)) {
			$invalidRangeItems = array();
			foreach ($items as $itemId => $item) {

				// Get item meta group values
				$itemMetaGroups = $this->getItemMetaGroups($itemId);

				// Check each range against item meta values
				foreach ($rangeMetaGroups as $groupId => $groupType) {
					$itemValue = false;
					switch ($groupType) {
						case 'numberrange':
							// Get item meta value (only one value for ranges)
							if (isset($itemMetaGroups[$groupId])) {
								$itemValue = reset($itemMetaGroups[$groupId]['values']);
							}
							break;
						case 'price':
//							$productValue = $product['priceGross'];
							break;
					}

					// Check if meta value falls outside filter range
					if ((!$itemValue) ||
						((!empty($filters[$groupId]['min'])) && ($itemValue < $filters[$groupId]['min'])) ||
						((!empty($filters[$groupId]['max'])) && ($itemValue > $filters[$groupId]['max']))) {
						
						// If item is not valid, add to list of invalid and skip checking of any further groups
						$invalidRangeItems[] = $itemId;
						break;
					}
				}
			}

			// Remove invalid range items
			$items = array_diff_key($items, array_flip($invalidRangeItems));
		}

		return $items;
	}
	
	/**
	 *	Filter a list of meta groups by whether they are filterable (in the sense of self::filterItems)
	 *
	 *	@access public
	 *	@param array Meta groups/filters (keyed by Meta group ID)
	 *	@return array Filtered meta groups
	 */
	public function filterFilterableGroups($groups)
	{
		// Get filterable groups
		$filterableGroups = $this->_getMetaGroupFilterableMapping();
		if (empty($filterableGroups)) {
			return $groups;
		}
		
		// Intersect keys
		$groups = array_intersect_key($groups, $filterableGroups);
		
		// Return
		return $groups;
	}

	/**
	 *	Get array of meta groups and their values for given item
	 *
	 *	@param int Item ID
	 *	@param bool Rebuild
	 *	@param string Language code
	 *	@return array Array of item meta groups and values
	 */
	public function getItemMetaGroups($itemId, $forceRebuild = false, $langCode = null, $skipCache = false)
	{
		// Get language
		if (!$langCode) {
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
		}
		
		// Load meta groups from cache/db
		$cacheKey = 'itemMetaGroups_'.$itemId.'_'.$langCode;
		$metaGroups = ($forceRebuild || $skipCache) ? false : $this->_cache->get($cacheKey);
		//$metaGroups = false;
		if ($metaGroups === false) {

			// Load item meta values from db 
			// (don't need to order by amv.valueId because we only intersect with the full group values later anyway)
			$query = 'SELECT amv.groupId, amv.valueId, amv.rawValue
				FROM `articleMetaValues` AS `amv`
					LEFT JOIN `metaGroups` AS `mg` ON amv.groupId = mg.id
				WHERE amv.articleId = '.(int) $this->_db->escape($itemId).'
				ORDER BY mg.sequence ASC,
					mg.id ASC,
					amv.rawValue ASC'; 
			//echo $query;
			$this->_db->setQuery($query);
			$metaGroups = $this->_db->loadGroupedAssocList('groupId');

			// Build meta groups
			if (!empty($metaGroups)) {
				foreach ($metaGroups as $groupId => &$metaGroup) {
					
					// Get full meta group
					$fullGroup = $this->getGroup($groupId, false, null, false, $langCode);
					
					// Fiddle with group values, namely intersect them
					switch ($fullGroup['type']) {
						case 'pick':
							$metaGroup = Arrays::filterByFlag($metaGroup, 'valueId');
							$values = Arrays::column($metaGroup, 'valueId');
							$fullGroup['values'] = $values ? array_intersect_key($fullGroup['values'], array_flip($values)) : array();
							break;
							
						case 'numberpick':
						case 'numberrange':
						case 'keywords':
						default:
							$fullGroup['values'] = Arrays::column($metaGroup, 'rawValue');
							break;
					}
					
					// Replace
					$metaGroup = $fullGroup;
				}
				unset($metaGroup);
			}

			// Store in cache
			if (!$skipCache) {
				$this->_cache->set($cacheKey, $metaGroups);
			}
		}

		// Return
		return $metaGroups;
	}

	/**
	 * Get mapping of all pick meta groups and values to items
	 *
	 * @param array Options.
	 * @return array Array of mappings
	 */
	public function getMetaItemMappings($forceRebuild = false)
	{
		static $metaMappings;
		
		// Load meta values from cache/db
		if (!isset($metaMappings)) {
			
			// Get master indices
			$rawGroups = $this->_getGroupStats();
			$typeMapping = $this->_getMetaGroupTypeMapping();
			
			// Get item mappings
			foreach ($rawGroups as $groupId => $rawGroup) {
				switch ($typeMapping[$groupId]) {
					case 'pick':
						$metaMappings['id'][$groupId] = $this->_getMetaItemMapping($groupId);
						break;
						
					case 'numberpick':
					case 'numberrange':
					case 'keywords':
					default:
						$metaMappings['value'][$groupId] = $this->_getMetaItemMapping($groupId);
						break;
				}
			}
		}
		
		// Return
		return $metaMappings;
	}
	
	/**
	 *	Build mapping of meta values to items
	 *
	 *	@access protected
	 *	@param int Meta group ID
	 *	@param bool Force Rebuild
	 *	@return array
	 */
	protected function _getMetaItemMapping($groupId, $forceRebuild = false)
	{
		// Get from cache
		$cacheKey = 'metaGroupItemMapping_'.$groupId;
		$mapping = $forceRebuild ? false : $this->_cache->get($cacheKey);
		if ($mapping === false) {
			
			// Get from DB
			$columnName = $this->_getColumnName($groupId);
			$query = 'SELECT amv.articleId, amv.'.$columnName.'
				FROM `articleMetaValues` AS `amv`
				WHERE amv.groupId = '.(int) $groupId.'
				ORDER BY amv.valueId ASC,
					amv.rawValue ASC';
			$this->_db->setQuery($query);
			$mapping = $this->_db->loadGroupedAssocList($columnName, null, 'articleId');
			
			$this->_cache->set($cacheKey, $mapping);
		}
		 
		// Return
		return $mapping;
	}
	
	/**
	 *	Get group ID by slug
	 *
	 *	@param string Slug
	 *	@param string Lang code
	 *	@return int Group ID
	 */
	public function getGroupIdBySlug($slug, $langCode = null)
	{
		// Get language
		if (!$langCode) {
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
		}
		
		// Ensure mapping exists
		if (!isset($this->_groupSlugMapping[$langCode])) {
			$this->_getMetaGroupSlugMapping(false, $langCode);
		}
		
		// Get mapped meta group ID
		return isset($this->_groupSlugMapping[$langCode][$slug]) ? $this->_groupSlugMapping[$langCode][$slug] : false;
	}
	
	/**
	 *	Get the meta group ID of a meta value
	 *
	 *	Convenience function.
	 *
	 *	@access public
	 *	@param int Meta value ID
	 *	@return int Meta group ID
	 */
	public function getGroupIdByValueId($valueId)
	{
		if (empty($this->_valueGroupMapping)) {
			$this->getMetaValueGroupMapping();
		}
		return $this->_valueGroupMapping[$valueId];
	}

	/**
	 * Return meta group details
	 *
	 * @param int Group id 
	 * @param bool Force rebuild
	 * @param array Optional set of hierarchy filters to use for range generations 
	 *	@param bool [anyone?]
	 *	@param string Language code
	 * @return array Meta group
	 */
	public function getGroup($groupId, $forceRebuild = false, $hierarchyFilters = null, $incRangeValues = true, $langCode = null)
	{
		// Wrong answer
		if (!$groupId) {
			return false;
		}
		
		static $groups;
		
		// Get language details
		if (!$langCode) {
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
		}

		// Determine cache key
		$cacheKey = 'metaGroup_'.$groupId.'_'.$langCode;
		if ($incRangeValues) {
			$cacheKey .= '_range';
		}
		
		// Range groups must also be cached against hierarchy
		$groupTypeMapping = $this->_getMetaGroupTypeMapping();
		$groupType = $groupTypeMapping[$groupId];
		if (in_array($groupType, array('price', 'numberrange'))) {
			$cacheKey .= '_'.md5(serialize($hierarchyFilters)); 
		}
		
		// Get group from cache/DB
		if ($forceRebuild || !isset($groups[$cacheKey])) {
			$metaGroup = $forceRebuild ? false : $this->_cache->get($cacheKey);
			if ($metaGroup === false) {
				
				// Get group
				if ($groupId == 'price') {
					$metaGroup = array(
						'id' => 'price',
						'type' => 'price',
						'hidden' => 0,
						'startOpen' => 0,
						'excludeValues' => null,
						'neverExclude' => 0,
						'internalName' => 'price',
						'name' => 'Price Range'
					);
				} else {
					$defaultLang = BluApplication::getSetting('defaultLang');
					
					$query = 'SELECT mg.*, lmg.*
						FROM `metaGroups` AS `mg`
							LEFT JOIN `languageMetaGroups` AS `lmg` ON lmg.id = mg.id
						WHERE mg.id = '.(int) $groupId.'
							ORDER BY FIELD(lmg.lang, "'.$this->_db->escape($defaultLang).'", "'.$this->_db->escape($langCode).'")';
					$this->_db->setQuery($query);
					$metaGroup = $this->_db->loadAssoc();
				}
				
				// Build meta values
				switch ($metaGroup['type']) {
					case 'pick':
						$metaGroup['values'] = $this->_getPickValues($metaGroup['id'], $langCode);
						break;
						
					case 'numberpick':
						$metaGroup['values'] = $this->_getNumberpickValues($metaGroup['id']);
						break;
						
					case 'price':
						if ($incRangeValues) {
							$metaGroup['values'] = $this->_getPriceValues($metaGroup['min'], $metaGroup['max'], $hierarchyFilters, $langCode);
						}
						break;
						
					case 'keywords':
						// Don't build values
						break;
				}
				
				// Store in memcache
				$this->_cache->set($cacheKey, $metaGroup);
			}
			
			// Store in local cache
			$groups[$cacheKey] = $metaGroup;
		}
		
		// Return
		return $groups[$cacheKey];
	}
	
	/**
	 * Get pick meta group values
	 *
	 * @param int Meta group Id
	 *	@param string Language code.
	 *	@param bool Include internal-only values.
	 * @return array List of possible group values
	 */
	protected function _getPickValues($groupId, $langCode = null, $internal = false)
	{
		// Get language details
		if (!$langCode) {
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
		}
		$defaultLang = BluApplication::getSetting('defaultLang');

		// Get group values
		$query = 'SELECT lmv.*, mv.*
			FROM `metaValues` AS `mv`
				LEFT JOIN `languageMetaValues` AS `lmv` ON lmv.id = mv.id
			WHERE mv.groupId = '.(int)$groupId.'
				'.($internal ? '' : 'AND mv.internal = 0').'
			ORDER BY mv.sequence, mv.internalName,
				FIELD(lmv.lang, "'.$this->_db->escape($defaultLang).'", "'.$this->_db->escape($langCode).'") DESC';
		$this->_db->setQuery($query);
		$values = $this->_db->loadAssocList('id');

		// Do language transformations
		foreach ($values as &$value) {
			if (empty($value['pageTitle'])) {
				$value['pageTitle'] = $value['name'];
			}
			if (empty($value['listingTitle'])) {
				$value['listingTitle'] = $value['name'];
			}
			if (empty($value['keywords'])) {
				$value['keywords'] = $value['name'];
			}
		}
		unset($value);

		// Return
		return $values;
	}
	
	/**
	 * Get number pick meta group values
	 *
	 * @param int Meta group Id
	 * @return array List of possible group values
	 */
	protected function _getNumberpickValues($metaGroupId)
	{
		$query = 'SELECT DISTINCT amv.rawValue AS `value`, 0 AS `default`, 1 AS `display`, amv.groupId
			FROM `articleMetaValues` AS `amv`
			WHERE amv.groupId = '.(int)$metaGroupId.'
			ORDER BY amv.rawValue ASC';
		$this->_db->setQuery($query);
		return $this->_db->loadAssocList('value');
	}
	
	/**
	 * Get price group values
	 * 
	 * @param float Set to minimum price available
	 * @param float Set to max price available
	 * @param array Hierarchy filters
	 *	@param string Language code
	 * @return array Price group ranges
	 */
	protected function _getPriceValues(&$minPrice, &$maxPrice, $hierarchyFilters = null, $langCode = null)
	{
		// Get language details
		if (!$langCode) {
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
		}
		
		// Get all products, filtered by hierarchy
		$productsModel = BluApplication::getModel('products');
		if (!$products = $productsModel->getProducts(null, 'price_asc', null, null, false, null, null, null, $hierarchyFilters, false, true, true, $langCode)) {
			return false;
		}
		
		// Calcualte desired ranges
		$numRanges = 5;
		$idealNumberPerRange = count($products) / $numRanges;
		$rangeNumber = 1;
		$prodCount = 0;
		$ranges = array('all' => array(
			'numProducts' => 0,
			'minPriceGross' => false,
			'maxPriceGross' => false,
			'default' => 0,
			'display' => 1,
			'value' => array(
				'min' => false,
				'max' => false
			)
		));
		$product = reset($products);
		$minPrice = $boundary = floor($minPrice / 5) * 5;
		while ($product) {
			
			// Create bin
			if (!isset($ranges[$rangeNumber])) {
				$ranges[$rangeNumber] = array(
					'numProducts' => 0,
					'minPriceGross' => $product['priceGross'],
					'maxPriceGross' => false,
					'default' => 0,
					'display' => 1,
					'value' => array(
						'min' => $boundary,
						'max' => false
					)
				);
			}
			$range =& $ranges[$rangeNumber];
			
			// Add product
			$range['numProducts']++;
			$range['maxPriceGross'] = $product['priceGross'];
			
			// Get next product
			$product = next($products);
			
			// Check for ideal number, and stop at next round boundary
			if ($range['numProducts'] >= $idealNumberPerRange) {
				$boundary = ceil($range['maxPriceGross'] / 5) * 5;
				if ($product['priceGross'] > $boundary) {
					$rangeNumber++;
					$range['value']['max'] = $boundary;
				}
			}
		}
		$maxPrice = $range['value']['max'] = ceil($range['maxPriceGross'] / 5) * 5;
		
		return $ranges;
	}
	
	/**
	 * Get keywords meta group values
	 *
	 * @param int Meta group Id
	 * @return array List of possible group values
	 */
	protected function _getKeywordsValues($metaGroupId)
	{
		$query = 'SELECT DISTINCT amv.rawValue AS `value`, 0 AS `default`, 1 AS `display`, amv.groupId
			FROM `articleMetaValues` AS `amv`
			WHERE amv.groupId = '.(int)$metaGroupId.'
			ORDER BY amv.rawValue ASC';
		$this->_db->setQuery($query);
		return $this->_db->loadAssocList('value');
	}
	
	/**
	 *	Get meta value details
	 *
	 *	@access public
	 *	@param int Meta value ID
	 *	@param string Language code
	 *	@return array Details
	 */
	public function getValue($valueId, $langCode = null)
	{
		if (!$langCode) {
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
		}
		
		static $metaValues;
		if (!isset($metaValues[$langCode][$valueId])) {

			// Get mapping

			if (empty($this->_valueGroupMapping)) {
				$this->getMetaValueGroupMapping();
			}
			
			// Get group
			if (!isset($this->_valueGroupMapping[$valueId])) {
				return false;
			}
			$group = $this->getGroup($this->_valueGroupMapping[$valueId], false, null, true, $langCode);
			
			// Merge in group's values
			if (empty($metaValues[$langCode])) {
				$metaValues[$langCode] = array();
			}
			$metaValues[$langCode] += $group['values'];
		}
		
		return $metaValues[$langCode][$valueId];
	}
	
	/**
	 *	Get a meta value ID by its slug
	 *
	 *	@access public
	 *	@param string Slug
	 *	@param string Language code
	 *	@return int
	 */
	public function getValueIdBySlug($slug, $langCode = null)
	{
		$slugMapping = $this->_getMetaValueSlugMapping(false, $langCode);
		if (isset($slugMapping[$slug])) {
			return $slugMapping[$slug];
		}
		
		return false;
	}
	
	/**
	 *	Get a meta value ID by its slug and group ID
	 *
	 *	@access public
	 *	@param string Slug
	 *	@param string Language code
	 *	@return int
	 */
	public function getValueIdBySlugAndGroupId($slug, $groupId, $langCode = 'EN')
	{
		$query = 'SELECT lmv.id FROM languageMetaValues AS lmv, metaValues AS mv WHERE lmv.slug="'.$this->_db->escape($slug).'" AND lmv.lang="'.$this->_db->escape($langCode).'" AND lmv.id=mv.id AND mv.groupId='.(int)$groupId;
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
	
	/**
	 *	Get selector
	 *
	 *	@access public
	 *	@param int Selector ID
	 *	@param bool Rebuild cache
	 *	@param bool Language code
	 *	@return array.
	 */
	public function getSelector($selectorId, $forceRebuild = false, $langCode = null)
	{
		// Get language details
		if (!$langCode) {
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
		}
		
		// Get group from cache/DB
		$cacheKey = 'metaSelector_'.$selectorId.'_'.$langCode;
		$selector = $forceRebuild ? false : $this->_cache->get($cacheKey);
		if ($selector === false) {
			$defaultLang = BluApplication::getSetting('defaultLang');
			
			// Get base details.
			$query = 'SELECT lms.*, ms.*
				FROM `metaSelectors` AS `ms`
					LEFT JOIN `languageMetaSelectors` AS `lms` ON ms.id = lms.id
				WHERE ms.id = '.(int) $selectorId.'
					ORDER BY FIELD(lms.lang, "'.$this->_db->escape($defaultLang).'", "'.$this->_db->escape($langCode).'")';
			$this->_db->setQuery($query);
			$selector = $this->_db->loadAssoc();
			$selector['type'] = 'selector';	// Yum, this is for self::setAvailability.
			
			// Do language transformations
			if (empty($selector['pageTitle'])) {
				$selector['pageTitle'] = $selector['name'];
			}
			if (empty($selector['listingTitle'])) {
				$selector['listingTitle'] = $selector['name'];
			}
			if (empty($selector['keywords'])) {
				$selector['keywords'] = $selector['name'];
			}
			
			// Get values
			$query = 'SELECT msv.groupId, msv.valueId, msv.valueRaw
				FROM `metaSelectorValues` AS `msv`
				WHERE msv.selectorId = '.(int) $selectorId;
			$this->_db->setQuery($query);
			$selector['values'] = $this->_db->loadAssocList();
			
			// Pull out the relevant column from the results, depending on the group type
			if (!empty($selector['values'])) {
				$groups = array();
				$groupTypeMapping = $this->_getMetaGroupTypeMapping();
				foreach ($selector['values'] as $selectorValue) {
					$groupId = $selectorValue['groupId'];
					switch ($groupTypeMapping[$groupId]) {
						case 'pick':
							$groups[$groupId][$selectorValue['valueId']] = $selectorValue['valueId'];
							break;
							
						case 'numberpick':
						case 'keywords':
							$groups[$groupId][(string) $selectorValue['value']] = $selectorValue['value'];
							break;
							
						default:
							// Not implemented
							break;
					}
				}
				$selector['values'] = $groups;
			}
			
			// Build value details
			$this->_addSelectorValues($selector);
			
			// Get the groups to display the selector in
			$query = 'SELECT mv.groupId
				FROM `metaValues` AS `mv`
					LEFT JOIN `metaHierarchy` AS `mh` ON mh.valueId = mv.id
				WHERE mh.aliasId = '.(int) $selectorId.'
					AND mh.aliasType = "selector_replace"';
			$this->_db->setQuery($query);
			$selector['groups'] = $this->_db->loadResultArray();
			if (!empty($selector['groups'])) {
				$selector['groups'] = array_combine($selector['groups'], $selector['groups']);
			}
			
			// Store in cache
			$this->_cache->set($cacheKey, $selector);
		}
		
		// Return
		return $selector;
	}
	
	/**
	 *	Append selector value details
	 *
	 *	@access protected
	 *	@param array Selector
	 */
	protected function _addSelectorValues(&$selector)
	{
		// Any values at all?
		if (empty($selector['values'])) {
			return true;
		}
		
		// Get meta group and copy value details
		$selectorValues = array();
		foreach ($selector['values'] as $groupId => $values) {
			
			// Get group
			$selectorValues[$groupId] = array();
			$group = $this->getGroup($groupId);
			
			// Get value
			switch ($group['type']) {
				case 'pick':
					foreach ($values as $valueId) {
						if (!isset($group['values'][$valueId])) {
							continue 2;
						}
						$selectorValues[$groupId][$valueId] = $group['values'][$valueId];
					}
					break;
					
				case 'numberpick':
				case 'numberrange':
				case 'keywords':
					// @todo
					break;
					
				default:
					// Not implemented
					break;
			}
		}
		
		// Replace into selector object
		$selector['values'] = $selectorValues;
	}
	
	/**
	 *	Get meta hierarchy.
	 *
	 *	@access public
	 *	@param bool Rebuild
	 *	@param string Language code
	 *	@param bool Include item details where appropriate
	 *	@return array Hierarchy
	 */
	public function getHierarchy($forceRebuild = false, $langCode = null, $includeItems = false)
	{
		// Get language details
		if (!$langCode) {
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
		}
		
		static $hierarchy;
		if ($forceRebuild || !isset($hierarchy[$langCode])) {
			
			// Get group from cache/DB
			$cacheKey = 'metaHierarchy_'.$langCode.($includeItems ? '_items' : '');
			$hierarchy[$langCode] = false;
			$hierarchy[$langCode] = $forceRebuild ? false : $this->_cache->get($cacheKey);
			if ($hierarchy[$langCode] === false) {
				
				// Get top level
				$groupSlugMapping = $this->_getMetaGroupSlugMapping(false, 'EN');
				if (isset($groupSlugMapping['top_levels'])) {
					
					// Get meta values (include internal-only values too!)
					$group = $this->getGroup($groupSlugMapping['top_levels']);
					$topLevelValues = $this->_getPickValues($group['id'], $langCode, true);
					
					// Get global filters
					$globalFilters = BluApplication::getSetting('metaHierarchyFilters', array());
					
					// Build hierarchy
					$hierarchy[$langCode] = $this->_buildHierarchy($topLevelValues, $globalFilters, $langCode, $includeItems);
					
				}
				
				// Store in cache
				$this->_cache->set($cacheKey, $hierarchy[$langCode]);
			}
		}
		return $hierarchy[$langCode];
	}
	
	/**
	 *	Build meta hierarchy
	 *
	 *	@access private
	 *	@param array Current hierarchy
	 *	@param array Filters
	 *	@param string Language code
	 *	@param bool Include items for setting availability
	 *	@return array Complete hierarchy
	 */
	private function _buildHierarchy(array $hierarchy, array $filters, $langCode, $includeItems = false)
	{
		// Expand aliases, if any.
		$this->_buildHierarchyReplacements($hierarchy, $langCode);
		
		// Set availability for this hierarchy level.
		$this->_buildHierarchyAvailability($hierarchy, $filters, $langCode, $includeItems);
		
		// Build children.
		if (!empty($hierarchy)) {
			
			static $aliases;
			if (!isset($aliases)) {
				$query = 'SELECT mh.valueId, mh.aliasId AS `id`, mh.aliasType AS `type`
					FROM `metaHierarchy` AS `mh`
					WHERE mh.aliasType = "group_child"
						OR mh.aliasType = "selector_child"';
				$this->_db->setQuery($query);
				$aliases = $this->_db->loadAssocList('valueId');
			}
			
			if (!empty($aliases)) {
				foreach ($hierarchy as $metaValueId => &$metaValue) {
					
					// Get alias
					if (!isset($aliases[$metaValueId])) {
						continue;
					}
					
					// Prepare to pass current filter to children.
					$childFilters = $filters;
					if (isset($metaValue['type']) && $metaValue['type'] == 'selector') {
						// @todo add selector's values to $childFilters
					} else {
						$childFilters[$metaValue['groupId']][$metaValueId] = $metaValueId;
					}
					
					// Parse alias
					switch ($aliases[$metaValueId]['type']) {
						case 'group_child':
							// Append sub hierarchy, based on the meta group's values
							$group = $this->getGroup($aliases[$metaValueId]['id'], false, null, true, $langCode);
							
							// Get the internal-only values too
							switch ($group['type']) {
								case 'pick':
									$group['values'] = $this->_getPickValues($group['id'], $langCode, true);
									break;
							}
							
							// Append subhierarchy. Assume selectors can't have children, otherwise it gets messy.
							if (!empty($group['values'])) {
								$childValues = $this->_buildHierarchy($group['values'], $childFilters, $langCode, $includeItems);
								if (!empty($childValues)) {
									if (empty($metaValue['values'])) {
										$metaValue['values'] = array();
									}
									$metaValue['values'] += $childValues;
								}
							}
							break;
							
						case 'selector_child':
							// Append sub hierarchy, based on the selector's values.
							// @todo - similar to group_child: append selector's values' details to $metaValue recursively.
							break;
					}
				}
			}
			unset($metaValue);
		}
		
		// Return
		return $hierarchy;
	}
	
	/**
	 *	Expand the hierarchy by replacing meta values with their aliases
	 *
	 *	@access private
	 *	@param array Hierarchy
	 *	@param string Language code
	 */
	private function _buildHierarchyReplacements(array &$hierarchy, $langCode)
	{
		// Nothing ado.
		if (empty($hierarchy)) {
			return true;
		}
		
		// Get replacements
		static $replacements;
		if (!isset($replacements)) {
			$query = 'SELECT mh.valueId, mh.aliasId, mh.aliasType
				FROM `metaHierarchy` AS `mh`
				WHERE mh.aliasType = "group_replace"
					OR mh.aliasType = "selector_replace"';
			$this->_db->setQuery($query);
			$replacements = $this->_db->loadAssocList('valueId');
		}
		if (empty($replacements)) {
			return true;
		}
		
		// Prepare
		$expandedHierarchy = array();
		foreach ($hierarchy as $metaValueId => $metaValue) {
			
			// Does this branch use alias replacement?
			if (isset($replacements[$metaValueId])) {
				$aliasId = $replacements[$metaValueId]['aliasId'];
				switch ($replacements[$metaValueId]['aliasType']) {
					case 'group_replace':
						// Get group
						$group = $this->getGroup($aliasId, false, null, true, $langCode);
						
						// Use group's values
						if (!empty($group['values'])) {
							foreach ($group['values'] as $childValueId => $childValue) {
								$expandedHierarchy[$childValueId] = $childValue;
							}
						}
						break;
						
					case 'selector_replace':
						// Get selector
						if (!$selector = $this->getSelector($aliasId, false, $langCode)) {
							continue;
						}
						
						// Append original meta value ID for reference
						$selector['aliasId'] = $metaValueId;

						// Use selector itself
						$expandedHierarchy[$metaValueId] = $selector;
						break;
				}
			} else {
				
				// Direct copy-over
				$expandedHierarchy[$metaValueId] = $metaValue;
			}
		}
		
		// Output
		$hierarchy = $expandedHierarchy;
	}
	
	/**
	 *	Set availability for (sub-)hierarchy.
	 *
	 *	@access private
	 *	@param array Hierarchy
	 *	@param array Filters
	 *	@param string Language code
	 *	@param bool Include items when setting availability.
	 */
	private function _buildHierarchyAvailability(array &$hierarchy, array $filters, $langCode, $includeItems = false)
	{
		// Get items
		static $allItems;
		if (!isset($allItems[$langCode])) {
			$itemsModel = BluApplication::getModel('items');
			$allItems[$langCode] = $itemsModel->getItems();
		}
		
		// Set availability for each one (we need to do them individually, because of selectors, grr)
		$availabilityHierarchy = array();
		$groupsCache = array();
		foreach ($hierarchy as $metaValueId => $metaValue) {
			
			// If selector, leave be.
			$isSelector = isset($metaValue['type']) && $metaValue['type'] == 'selector';
			if ($isSelector) {
				$availability = array(
					$metaValue['id'] => $metaValue
				);
			
			// Otherwise, get group details
			} else {
				$groupId = $metaValue['groupId'];
				if (!isset($groupsCache[$groupId])) {
					$groupsCache[$groupId] = $this->getGroup($groupId, false, null, true, $langCode);
					$groupsCache[$groupId]['values'] = array();
				}
				
				// Only set availability for single value within the group
				$group = $groupsCache[$groupId];
				$group['values'][$metaValueId] = $metaValue;
				$availability = array(
					$groupId => $group
				);
			}
			
			// Set availability
			$this->setAvailability($availability, $allItems[$langCode], $filters, false, false, $includeItems);
			
			// Parse back out
			if ($isSelector) {
				$selector = reset($availability);
				$availabilityHierarchy[$metaValueId] = $selector;
			} else {
				$group = reset($availability);
				$value = reset($group['values']);
				$availabilityHierarchy[$metaValueId] = $value;
			}
		}
		
		// Replace
		$hierarchy = $availabilityHierarchy;
	}
	
	/**
	 *	Get a list of all elements in the (sub-)hierarchy
	 *
	 *	not used anyway yet, may be useful.
	 *
	 *	@access private
	 *	@param array Hierarchy
	 *	@return array Meta value IDs
	 */
	private function _getHierarchyElementIds($searchHierarchy = null)
	{
		// First time round...
		if (is_null($searchHierarchy)) {
			$cacheKey = 'metaHierarchyElements';
			$elements = $this->_cache->get($cacheKey);
			if ($elements !== false) {
				return $elements;
			}
			
			// Prepare full hierarchy
			$searchHierarchy = $this->getHierarchy();
		}
		
		// Build
		$elements = array();
		if (!empty($searchHierarchy)) {
			foreach ($searchHierarchy as $valueId => $element) {
				$elements[$valueId] = $valueId;
				if (!empty($element['values'])) {
					$elements += $this->_getHierarchyElementIds($element['values']);
				}
			}
		}
		
		// Store in cache, only for first time.
		if (isset($cacheKey)) {
			$this->_cache->set($cacheKey, $elements);
		}
		
		// Return
		return $elements;
	}
	
	/**
	 *	Get meta value/selector details
	 *
	 *	@access protected
	 *	@param int Value ID
	 *	@return array Details
	 */
	protected function _getHierarchyElement($valueId)
	{
		// Get mapping
		$valueSelectorMapping = $this->_getMetaValueSelectorMapping();
		
		// Is selector?
		if (isset($valueSelectorMapping[$valueId])) {
			return $this->getSelector($valueSelectorMapping[$valueId]);
		}
		
		// Is meta value?
		return $this->getValue($valueId);
	}
	
	/**
	 *	Get parent elements, youngest at bottom.
	 *
	 *	N.B. Some of the output value IDs may actually be links to selectors. Check self::_getMetaValueSelectorMapping.
	 *
	 *	@access protected
	 *	@param int Value ID
	 *	@return array List of meta value IDs
	 */
	protected function _getHierarchyElementAncestry($valueId)
	{
		// Get all ancestries
		static $parents;//$parents=array();
		if (empty($parents)) {
			$cacheKey = 'metaHierarchyLineages';
			$parents = $this->_cache->get($cacheKey);
//$parents=false;
			if ($parents === false) {
				$parents = $this->_buildHierarchyAncestries($this->getHierarchy(), array());
				$this->_cache->set($cacheKey, $parents);
			}
		}
			
		// Return
		return isset($parents[$valueId]) ? $parents[$valueId] : false;
	}
	
	/**
	 *	Clear element ancestries
	 *
	 *	@access protected
	 *	@return bool Success
	 */
	protected function _flushHierarchyAncestries()
	{
		$cacheKey = 'metaHierarchyLineages';
		return $this->_cache->delete($cacheKey);
	}
	
	/**
	 *	Get parent value ID mappings
	 *
	 *	@access private
	 *	@param array Hierarchy
	 *	@param array Parent value IDs
	 *	@return array List of ancestries.
	 */
	private function _buildHierarchyAncestries($hierarchy, array $parents)
	{
		$output = array();
		if (!empty($hierarchy)) {
			foreach ($hierarchy as $valueId => $element) {
				
				// Add to list
				$output[$valueId] = $parents;
				
				// If selector, don't do children.
				if (isset($element['type']) && $element['type'] == 'selector') {
					continue;
				}
				
				// If meta value, do children, if they exist
				if (!empty($element['values'])) {
					$thisParents = $parents;
					$thisParents[$valueId] = $valueId;
					$output += $this->_buildHierarchyAncestries($element['values'], $thisParents);
				}
			}
		}
		return $output;
	}
	
	/**
	 *	Intersect filters with the Hierarchy, giving lowest common filter with its ancestry.
	 *
	 *	@access public
	 *	@param array Filters/Meta groups
	 *	@param array Search hierarchy. Used for recursion.
	 *	@return array Meta value IDs, in order of eldest to youngest.
	 */
	public function intersectHierarchy($filters, $hierarchy = null)
	{
		// Fallback hierarchy
		if (is_null($hierarchy)) {
			$hierarchy = $this->getHierarchy();
		}
		
		// Get bottom hierarchy filter.
		if (!$bottom = self::_intersectHierarchy($filters, $hierarchy, true)) {
			return false;
		}
		
		// Get ancestry
		$isSelector = isset($bottom['type']) && $bottom['type'] == 'selector';
		$filters = $this->_getHierarchyElementAncestry($isSelector ? $bottom['aliasId'] : $bottom['id']);
		$filters[] = $bottom;
		
		// We only want to return value IDs
		$valueIds = array();
		foreach ($filters as $element) {
			
			// If selector, pull out its values
			if (isset($element['type']) && $element['type'] == 'selector') {
				foreach ($element['values'] as $values) {
					foreach ($values as $value) {
						$valueIds[$value['id']] = $value['id'];
					}
				}
				
			// Append single value ID
			} else {
				$valueIds[$element['id']] = $element['id'];
			}
		}
		
		// Return
		return $valueIds;
	}
	
	/**
	 *	Intersect the Hierarchy for a single element (either highest or lowest).
	 *
	 *	@static
	 *	@access protected
	 *	@param array Filters
	 *	@param array (Sub)hierarchy
	 *	@param bool True for lowest common element, false for highest (i.e. first element found)
	 *	@return array List of Meta value/selector details
	 */
	protected static function _intersectHierarchy($filters, $hierarchy, $checkChildren = true)
	{
		// Emptiness
		if (empty($filters) || empty($hierarchy)) {
			return false;
		}
		
		// Get things from the current level
		$hierarchyFilter = false;
		foreach ($hierarchy as $element) {
			
			// If meta selector, check all its values
			if (isset($element['type']) && $element['type'] == 'selector') {
				foreach ($element['values'] as $groupId => $values) {
					
					// HACK HACK HACK....data error
					if (empty($values)) {
						continue 2;
					}
					
					foreach ($values as $valueId => $value) {
						if (!isset($filters[$groupId][$valueId])) {
							continue 3;
						}
					}
				}
				
				// Check if filter already exists from this level
				if (!empty($hierarchyFilter)) {
					return false;
				}
				
				// Selector is good to go.
				$hierarchyFilter = $element;
				
			// If meta value
			} else {
				$groupId = $element['groupId'];
				$valueId = $element['id'];
				
				// Check if meta value is selected
				if (isset($filters[$groupId][$valueId])) {
					
					// Check no filter already exists from this level
					if (!empty($hierarchyFilter)) {
						return false;
					}
					
					// Meta value is good to go...
					$valueHierarchyFilter = $element;
				}
				
				// ...unless one of its descendants is selected
				if ($checkChildren && !empty($element['values'])) {
					$childHierarchyFilter = self::_intersectHierarchy($filters, $element['values'], $checkChildren);
					if (!empty($childHierarchyFilter)) {
						
						// Check no filter already exists from this level, bar the meta value
						if (!empty($hierarchyFilter)) {
							return false;
						}
						
						// Child filter is good to go. Replaces meta value, if set.
						$valueHierarchyFilter = $childHierarchyFilter;
					}
				}
				
				// Set
				if (isset($valueHierarchyFilter)) {
					$hierarchyFilter = $valueHierarchyFilter;
					unset($valueHierarchyFilter);
				}
			}
		}
		
		// Return
		return $hierarchyFilter;
	}
	
	/**
	 *	Add a meta value.
	 *
	 *	@access public
	 *	@param int Meta group ID
	 *	@param string Meta value's internal name.
	 *	@param mixed Other meta value details (e.g. sequence, color, ...)
	 *	@param bool Skip cache rebuild.
	 *	@return int Meta value ID.
	 */
	public function addMetaValue($groupId, $internalName, array $details = array(), $skipCache = false)
	{
		// Get meta group type
		$groupTypeMapping = $this->_getMetaGroupTypeMapping();
		if (isset($groupTypeMapping[$groupId])) {
			
			// Apparently out of date, eh? Either that or we're doing something stupid.
			$groupTypeMapping = $this->_getMetaGroupTypeMapping(true);
		}
		$groupType = $groupTypeMapping[$groupId];
		
		// Non-discrete values?
		switch ($groupType) {
			case 'pick':
				// Fine.
				break;
				
			default:
				// Not fine. Not allowed.
				return false;
				break;
		}
		
		// Validation
		if (!$internalName) {
			return false;
		}
		$imageName = isset($details['imageName']) ? $details['imageName'] : false;
		$sequence = isset($details['sequence']) ? $details['sequence'] : 0;
		$display = isset($details['display']) ? $details['display'] : true;
		$default = isset($details['default']) ? $details['default'] : false;
		$featured = isset($details['featured']) ? $details['featured'] : false;
		
		// Commit to database
		$valueId = $this->_addValue($groupId, $internalName, $imageName, null, $sequence, $display, $default, $featured, false);
		
		// Clear cache
		if (!$skipCache) {
			$this->_clearGroupCache($groupId);
		}
		
		// Return meta value ID
		return $valueId;
	}
	
	/**
	 *	Add a meta value to the database
	 *
	 *	@access protected
	 *	@param int Group ID
	 *	@param string Internal name
	 *	@param string Image name
	 *	@param string Colour
	 *	@param int Priority
	 *	@param bool Displayable
	 *	@param bool Default
	 *	@param bool Featured
	 *	@param bool Internal
	 *	@return int Meta value ID
	 */
	protected function _addValue($groupId, $internalName, $imageName, $colour, $priority, $display, $default, $featured, $internal)
	{
		// Add to database
		$query = 'INSERT INTO `metaValues`
			SET `groupId` = '.(int) $groupId.',
				`internalName` = "'.$this->_db->escape($internalName).'",
				`imageName` = "'.$this->_db->escape($imageName).'",
				`color` = "'.$this->_db->escape($colour).'",
				`sequence` = '.(int) $priority.',
				`display` = '.(int) (bool) $display.',
				`default` = '.(int) (bool) $default.',
				`featured` = '.(int) (bool) $featured.',
				`internal` = '.(int) (bool) $internal;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Get meta value ID.
		if (!$valueId = $this->_db->getInsertID()) {
			return false;
		}
		
		// Return
		return $valueId;
	}
	
	/**
	 *	Add a language entry to an existing meta value
	 *
	 *	@access public
	 *	@param int Meta value ID
	 *	@param string Language code
	 *	@param string Name
	 *	@param string Description
	 *	@param string Custom document keywords
	 *	@param string Custom document title
	 *	@param string Custom listings page title
	 *	@param string Custom slug
	 *	@param string Custom document meta description
	 *	@return bool Success
	 */
	public function addLanguageMetaValue($valueId, $langCode = null, $name = null, $description = null, $keywords = null, $pageTitle = null, $listingTitle = null, $slug = null, $pageDescription = null)
	{
		// Get language
		if (!$langCode) {
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
		}
		
		// Generate slug
		if (!$slug) {
			$slug = Utility::slugify($name);
		}
		
		// Query
		$query = 'INSERT INTO `languageMetaValues`
			SET `id` = '.(int) $valueId.',
				`lang` = "'.$this->_db->escape($langCode).'",
				`name` = "'.$this->_db->escape($name).'",
				`description` = "'.$this->_db->escape($description).'",
				`generic` = "'.$this->_db->escape(serialize(null)).'",
				`slug` = "'.$this->_db->escape($slug).'",
				`keywords` = "'.$this->_db->escape($keywords).'",
				`pageTitle` = "'.$this->_db->escape($pageTitle).'",
				`listingTitle` = "'.$this->_db->escape($listingTitle).'",
				`pageDescription` = "'.$this->_db->escape($pageDescription).'"';
		$this->_db->setQuery($query);
		return (bool) $this->_db->query();
	}
	
	/**
	 *	Update meta value details
	 *
	 *	N.B. you can't change group ID. If you ever do need to, in the future, remember the productMetaValues table.
	 *
	 *	@access public
	 *	@param int Meta value ID
	 *	@param array Details
	 *	@return bool Success
	 */
	public function updateMetaValue($valueId, array $details, $skipCache = false)
	{
		// Validate
		$validDetails = array();
		if (isset($details['internalName'])) {
			$validDetails['internalName'] = '"'.$this->_db->escape($details['internalName']).'"';
		}
		if (isset($details['images']['main'])) {
			$validDetails['imageName'] = '"'.$this->_db->escape($details['images']['main']).'"';
		}
		if (isset($details['sequence'])) {
			$validDetails['sequence'] = (int) $details['sequence'];
		}
		if (isset($details['display'])) {
			$validDetails['display'] = (int) (bool) $details['display'];
		}
		if (isset($details['default'])) {
			$validDetails['default'] = (int) (bool) $details['default'];
		}
		if (isset($details['featured'])) {
			$validDetails['featured'] = (int) (bool) $details['featured'];
		}
		if (empty($validDetails)) {
			return false;
		}
		
		// Commit to database
		$query = 'UPDATE `metaValues` SET ';
		foreach ($validDetails as $key => &$value) {
			$value = '`'.$key.'` = '.$value;
		}
		unset($value);
		$query .= implode(', ', $validDetails).' WHERE `id` = '.(int) $valueId;
		$this->_db->setQuery($query);
		$updated = $this->_db->query();
		
		// Cache
		if (!$skipCache) {
			$this->_clearValueCache($valueId);
		}
		
		// Return
		return $updated;
	}
	
	/**
	 *	Update a language entry for a meta value
	 *
	 *	@access public
	 *	@param int Meta Value ID
	 *	@param string Language code
	 *	@param string Name
	 *	@param string Description
	 *	@param string Keywords
	 *	@param string Page title
	 *	@param string Listing title
	 *	@param string Slug
	 *	@param string Page description
	 *	@return bool Success
	 */
	public function updateLanguageMetaValue($valueId, $langCode, $name = null, $description = null, $keywords = null, $pageTitle = null, $listingTitle = null, $slug = null, $pageDescription = null)
	{
		// Update database
		$query = 'UPDATE `languageMetaValues`
			SET `name` = "'.$this->_db->escape($name).'",
				`description` = "'.$this->_db->escape($description).'",
				`generic` = "'.$this->_db->escape(serialize(null)).'",
				'.($slug ? '`slug` = "'.$this->_db->escape($slug).'",' : '').'
				`keywords` = "'.$this->_db->escape($keywords).'",
				`pageTitle` = "'.$this->_db->escape($pageTitle).'",
				`listingTitle` = "'.$this->_db->escape($listingTitle).'",
				`pageDescription` = "'.$this->_db->escape($pageDescription).'"
			WHERE `id` = '.(int) $valueId.'
				AND `lang` = "'.$this->_db->escape($langCode).'"';
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Update cache
		$this->_clearValueCache($valueId);
		
		$cacheModel = BluApplication::getModel('cache');
		$cacheModel->deleteEntriesLike('meta');
		
		// Return
		return true;
	}
	
	/**
	 *	Delete all language entries for a meta value.
	 *
	 *	@access public
	 *	@param int Meta value ID
	 *	@param bool Success
	 */
	public function deleteLanguageMetaValues($valueId)
	{
		$query = 'DELETE FROM `languageMetaValues`
			WHERE `id` = '.(int) $valueId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Clear the cache for anything related to a meta value
	 *
	 *	I.e. its group.
	 *
	 *	@access public
	 *	@param int Meta value ID
	 */
	protected function _clearValueCache($valueId)
	{
		// Get group ID
		$query = 'SELECT mv.groupId
			FROM `metaValues` AS `mv`
			WHERE mv.id = '.(int) $valueId;
		$this->_db->setQuery($query);
		if (!$groupId = $this->_db->loadResult()) {
			return false;
		}
		
		// Clear group
		return $this->_clearGroupCache($groupId);
	}
	
	/**
	 *	Clear the cache for anything related to a meta group.
	 *
	 *	Use when modifying the group's meta values too.
	 *
	 *	@access protected
	 *	@param int Meta group ID
	 */
	protected function _clearGroupCache($groupId)
	{
		$cacheModel = BluApplication::getModel('cache');
		
		$cacheModel->deleteEntry('metaGroupStats');
		$cacheModel->deleteEntriesLike('metaAvailability_');
		$cacheModel->deleteEntry('metaGroupItemMapping_'.$groupId);
		$cacheModel->deleteEntry('metaDefaultFilters');
		$cacheModel->deleteEntriesLike('metaGroup_'.$groupId.'_');
		$cacheModel->deleteEntriesLike('metaIndexGroups_');
		$cacheModel->deleteEntriesLike('metaValuePopup_');
		
		$cacheModel->deleteEntriesLike('metaValueSlugMapping_');
		$this->_valueSlugMapping = array();
		$cacheModel->deleteEntry('metaValueGroupMapping');
		$cacheModel->deleteEntriesLike('metaGroupSlugMapping_');
		$cacheModel->deleteEntry('metaGroupTypeMapping');
		$this->_groupTypeMapping = array();
		
		$cacheModel->deleteEntry('metaGroupFilterableMapping');
	}
	
	/**
	 *	Clear selector cache
	 *
	 *	@access protected
	 *	@param int Selector ID
	 */
	protected function _clearSelectorCache($selectorId)
	{
		$cacheModel = BluApplication::getModel('cache');
		
		$cacheModel->deleteEntriesLike('metaSelector_'.$selectorId.'_');
		$cacheModel->deleteEntriesLike('metaSelectorSlugMapping_');
		$this->_selectorSlugMapping = array();
	}
	
	/**
	 *	Might as well make it public
	 *
	 *	@access public
	 *	@param int Meta Group ID
	 */
	public function flushGroup($groupId)
	{
		$this->_clearGroupCache($groupId);
	}
	
	/**
	 *	Add item-metavalue mappings.
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param int Meta group ID
	 *	@param mixed Raw meta values (or meta value IDs for pick meta groups).
	 *	@param bool Whether or not to skip the cache clearing
	 *	@return bool Whether article-metavalue combination is in database.
	 */
	public function addItemMetaValues($itemId, $metaGroupId, $metaValues, $skipCache = true)
	{
		// Validate
		if (!is_array($metaValues)) {
			$metaValues = (array) $metaValues;
		}
		
		// Get meta group type
		$groupTypeMapping = $this->_getMetaGroupTypeMapping();
		$groupType = $groupTypeMapping[$metaGroupId];
		$metaValueColumn = $this->_getColumnName($metaGroupId);
		
		// Get item's current meta values.
		$query = 'SELECT amv.'.$metaValueColumn.'
			FROM `articleMetaValues` AS `amv`
			WHERE amv.articleId = "'.$this->_db->escape($itemId).'"
				AND amv.groupId = '.(int) $metaGroupId;
		$this->_db->setQuery($query);
		$articleMetaValues = $this->_db->loadResultArray();

		// Prepare input
		$metaValues = array_filter($metaValues);						// No false values (or their loose equivalents, i.e. 0, null etc)
		$metaValues = array_unique($metaValues);						// Unique metavalues
		$metaValues = array_diff($metaValues, $articleMetaValues);		// Remove existing metavalues

		if (!empty($metaValues)) {
			
			// Insert new meta values.
			foreach ($metaValues as &$metaValue) {
				
				// Validate meta value
				switch ($groupType) {
					case 'pick':
						// Validate meta value ID.
						if (!$metaValue = (int) $metaValue) {
							continue 2;		// Skip this metavalue altogther.
						}
						break;
						
					case 'keywords':
						// Usual validation
						$metaValue = '"'.$this->_db->escape($metaValue).'"';
						break;
						
					default:
						// Type validation.
						$metaValue = (double) $metaValue;
						break;
				}
				
				// Insert value
				$query = 'INSERT INTO `articleMetaValues`
					SET `articleId` = "'.$this->_db->escape($itemId).'",
						`groupId` = '.(int) $metaGroupId.',
						`'.$metaValueColumn.'` = '.$metaValue;
				$this->_db->setQuery($query);
				if (!$this->_db->query()) {
					$metaValue = false;
				}
				
			}
			unset($metaValue);
		
			// Extra stuff
			switch ($groupType) {
				case 'numberpick':
				case 'numberrange':
				/*
					// Update min/max values for meta group
					$query = 'UPDATE `metaGroups` AS `mg`
						SET `max` = (
								SELECT MAX(pmv.metaValue) 
								FROM `productMetaValues` AS `pmv`
								WHERE pmv.metaGroupId = mg.id
								GROUP BY pmv.metaGroupId
							),
							`min` = (
								SELECT MIN(pmv.metaValue) 
								FROM `productMetaValues` AS `pmv`
								WHERE pmv.metaGroupId = mg.id
								GROUP BY pmv.metaGroupId
							)
						WHERE `id` = '.(int) $metaGroupId;
					$this->_db->setQuery($query);
					if (!$this->_db->query()) {
						return false;
					}
				*/
					break;

				default:
					// Nothing special
					break;
			}
		
		}
		
		// Failed?
		if (in_array(false, $metaValues, true)) {
			return false;
		}
		
		// Clear cache
		if (!$skipCache) {
			$this->_clearGroupCache($metaGroupId);
			
			$itemsModel = BluApplication::getModel('items');
			$itemsModel->flushItemMeta($itemId);
		}
		$cacheModel = BluApplication::getModel('cache');
		$cacheModel->deleteEntriesLike('itemMetaGroups_'.$itemId);
		// Return
		return true;
	}
	
	/**
	 *	Remove an item-metavalue mapping.
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param int Meta group ID
	 *	@param mixed Raw meta value (or meta value ID for pick meta groups).
	 * 	@param bool Whether to skip the cache bit or not
	 *	@return bool Whether item-metavalue combination has been removed from database.
	 */
	public function deleteItemMetaValue($itemId, $metaGroupId, $metaValue, $skipCache = false)
	{
		// Validate the meta value according to the group type
		$metaValues = array($metaValue);
		$metaValueColumn = $this->_getColumnName($metaGroupId, $metaValues);
		$metaValue = reset($metaValues);
		
		// Delete
		$query = 'DELETE FROM `articleMetaValues`
			WHERE `articleId` = "'.$this->_db->escape($itemId).'"
				AND `groupId` = '.(int) $metaGroupId.'
				AND `'.$metaValueColumn.'` = '.$metaValue;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Clear cache
		if (!$skipCache) {
			$this->_clearGroupCache($metaGroupId);
			
			$itemsModel = BluApplication::getModel('items');
			$itemsModel->flushItemMeta($itemId);
		}
		
		// Return
		return true;
	}
	
	/**
	 *	Remove all meta value mappings from an item for a group
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param int Meta group ID
	 * 	@param bool Whether to skip the cache bit or not
	 *	@return bool Success
	 */
	public function deleteItemMetaValues($itemId, $metaGroupId, $skipCache = false)
	{
		// Just delete
		$query = 'DELETE FROM `articleMetaValues`
			WHERE `articleId` = '.(int) $itemId.'
				AND `groupId` = '.(int) $metaGroupId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Flush
		if (!$skipCache) {
			$this->_clearGroupCache($metaGroupId);
			
			$itemsModel = BluApplication::getModel('items');
			$itemsModel->flushItemMeta($itemId);
		}
		
		// Return
		return true;
	}
	
	/**
	 *	Get the correct column name for `articleMetaValues` table, depending on meta group type. 
	 *
	 *	Simple factorisation.
	 *
	 *	@access protected
	 *	@param int Meta group ID
	 *	@return string Column name.
	 */
	protected function _getColumnName($groupId)
	{
		$groupTypeMapping = $this->_getMetaGroupTypeMapping();
		switch ($groupTypeMapping[$groupId]) {
			case 'pick':
				return 'valueId';
				
			case 'keywords':
			default:
				return 'rawValue';
		}
	}
	
	/**
	 *	Meta value slug availability
	 *
	 *	@access public
	 *	@param string Slug
	 *	@param string Language code
	 *	@param int Value ID exception
	 *	@param bool Check meta selectors too.
	 *	@return bool Available
	 */
	public function metaValueSlugAvailable($slug, $langCode, $valueId = null, $checkMetaSelectors = true)
	{
		// Check meta values
		$query = 'SELECT lmv.slug
			FROM `languageMetaValues` AS `lmv`
			WHERE lmv.slug = "'.$this->_db->escape($slug).'"
				AND lmv.lang = "'.$this->_db->escape($langCode).'"';	
		if ($valueId) {
			// It's OK if slug-holder is myself.
			$query .= '
				AND lmv.id != '.(int) $valueId;
		}
		$this->_db->setQuery($query);
		if (count($this->_db->loadAssoc())) {
			return false;
		}
		/*	THERE ARE NO META SELECTORS YET
		// Check meta selectors
		if ($checkMetaSelectors && !$this->metaSelectorSlugAvailable($slug, $langCode, null, false)) {
			return false;
		}
		*/
		// Win
		return true;
	}
	
	/**
	 *	Add a meta group.
	 *
	 *	Use only for migration purposes
	 *
	 *	@access public
	 *	@param string Internal name
	 *	@param string Type
	 *	@param int Priority
	 *	@param bool Hidden
	 *	@param bool Start open
	 *	@param bool Internal
	 *	@return int Meta group ID
	 */
	public function addMetaGroup($internalName, $type = 'pick', $priority = 0, $hidden = false, $startOpen = false, $internal = false, $skipCache = false)
	{
		$query = 'INSERT INTO `metaGroups`
			SET `internalName` = "'.$this->_db->escape($internalName).'",
				`type` = "'.$this->_db->escape($type).'",
				`sequence` = '.(int) $priority.',
				`hidden` = '.(int) (bool) $hidden.',
				`startOpen` = '.(int) (bool) $startOpen.',
				`internal` = '.(int) (bool) $internal.',
				`excludeValues` = "show_available",
				`filterable` = 1';
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		$metaGroupId = $this->_db->getInsertID();
		
		// Clear cache
		if (!$skipCache) {
			$this->_clearGroupCache($metaGroupId);
		}	
		
		return $metaGroupId;
	}
	
	/**
	 *	Add a language meta group
	 *
	 *	@access public
	 *	@param int Meta group ID
	 *	@param string Language code
	 *	@param string Singular name
	 *	@param string Plural name
	 *	@param string Description
	 *	@param string Slug
	 *	@return bool Success
	 */
	public function addLanguageMetaGroup($groupId, $langCode, $singular, $plural, $description = null, $slug = null)
	{
		// Query builder
		$params = array();
		$params['name'] = $singular;
		$params['plural'] = $plural;
		if (!is_null($description)) {
			$params['description'] = $description;
		}

		// Other important things
		$params['slug'] = empty($slug) ? Utility::slugify($params['plural']) : $slug;
		
		// Format
		foreach ($params as $field => &$value) {
			$value = '`'.$field.'` = "'.$this->_db->escape($value).'"';
		}
		unset($value);
		
		// Execute
		$query = 'INSERT INTO `languageMetaGroups`
			SET `id` = '.(int) $groupId.',
				`lang` = "'.$this->_db->escape($langCode).'",
				'.implode(', ', $params);
		$this->_db->setQuery($query);
		$added = $this->_db->query();
		
		// Return
		return (bool) $added;
	}
	
	/**
	 *	Deletes a meta value.
	 *
	 *	@access public
	 *	@param int Meta value ID
	 *	@param bool Remove language counterpart
	 *	@param bool Remove item associations
	 *	@param bool Don't flush cache
	 *	@return bool Success.
	 */
	public function deleteMetaValue($metaValueId, $removeLanguageMappings = true, $removeItemMappings = true, $skipCache = false, $groupId = null)
	{
		// Get meta group ID
		$query = 'SELECT mv.groupId
			FROM `metaValues` AS `mv`
			WHERE mv.id = '.(int) $metaValueId;
		$this->_db->setQuery($query);
		if (!$metaGroupId = (int) $this->_db->loadResult()) {
			return false;
		}
		
		if($groupId && $groupId!=$metaGroupId) {
			return false;
		}

		// Delete meta value.
		$query = 'DELETE FROM `metaValues`
			WHERE `id` = '.(int) $metaValueId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Delete language mappings
		if ($removeLanguageMappings) {
			$query = 'DELETE FROM `languageMetaValues`
				WHERE `id` = '.(int) $metaValueId;
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				return false;
			}
		}
		
		// Delete item mappings
		if ($removeItemMappings) {
			
			// Get items to flush
			if (!$skipCache) {
				$query = 'SELECT amv.articleId
					FROM `articleMetaValues` AS `amv`
					WHERE `valueId` = '.(int) $metaValueId;
				$this->_db->setQuery($query);
				$items = $this->_db->loadResultArray();
			}
			
			$query = 'DELETE FROM `articleMetaValues`
				WHERE `valueId` = '.(int) $metaValueId;
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				return false;
			}
			
			// Flush items
			if (!$skipCache && !empty($items)) {
				$itemsModel = BluApplication::getModel('items');
				foreach ($items as $itemId) {
					$itemsModel->flushItemMeta($itemId);
				}
			}
		}
		
		// Delete selector mappings
		$query = 'SELECT msv.selectorId
			FROM `metaSelectorValues` AS `msv`
			WHERE msv.valueId = '.(int) $metaValueId;
		$this->_db->setQuery($query);
		$selectors = $this->_db->loadResultArray();
		
		$query = 'DELETE FROM `metaSelectorValues`
			WHERE `valueId` = '.(int) $metaValueId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		if (!$skipCache && !empty($selectors)) {
			foreach ($selectors as $selectorId) {
				$this->_clearSelectorCache($selectorId);
			}
		}
		
		// Clear cache
		if (!$skipCache) {
			$this->_clearGroupCache($metaGroupId);
		}
		
		// Return
		return true;
	}
	
	/**
	 *	Delete meta group
	 *	N.B. Doesn't delete meta values belonging to the group, nor clean up hierarchy links.
	 *
	 *	@access protected
	 *	@param int Meta group ID
	 *	@param bool Skip cache
	 *	@return bool Success
	 */
	protected function _deleteMetaGroup($groupId, $skipCache = false)
	{
		// Delete main group
		$query = 'DELETE FROM `metaGroups`
			WHERE `id` = '.(int) $groupId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Delete languages
		$query = 'DELETE FROM `languageMetaGroups`
			WHERE `id` = '.(int) $groupId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Flush cache
		if (!$skipCache) {
			$this->_clearGroupCache($groupId);
		}
		
		// Return
		return true;
	}
	
	/**
	 *	Get the siblings of an element within The Hierarchy, including self.
	 *
	 *	@access public
	 *	@param int Meta value ID
	 *	@param string Language Code
	 *	@return array Parent element values, i.e. siblings
	 */
	public function getHierarchySiblings($valueId, $langCode = null)
	{
		// Get language code
		if (!$langCode) {
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
		}
		
		// Get lookup ancestry
		if (!$ancestry = $this->_getHierarchyElementAncestry($valueId)) {
			$ancestry = array();
		}
		
		// Get full hierarchy up to parent
		$hierarchy = $this->getHierarchy(false, $langCode);
		if (!empty($ancestry)) {
			foreach ($ancestry as $ancestorValueId) {
				$hierarchy = $hierarchy[$ancestorValueId]['values'];
			}
		}
		
		// Return
		return $hierarchy;
	}
	
	/**
	 *	Get full link of an element from The Hierarchy
	 *
	 *	@access public
	 *	@param int Meta Value ID
	 *	@param string Language code
	 *	@return string Link
	 */
	public function getFullLink($valueId, $langCode = null)
	{
		// Get language code
		if (!$langCode) {
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
		}
		
		// Get links
		static $links;
		if (empty($links)) {
			$cacheKey = 'metaHierarchyLinks_'.$langCode;
			$links = $this->_cache->get($cacheKey);
			if ($links === false) {
				$hierarchy = $this->getHierarchy();
				$links = $this->_getFullLinks($hierarchy, $langCode);
				
				$this->_cache->set($cacheKey, $links);
			}
		}
		
		// Return
		return isset($links[$valueId]) ? $links[$valueId] : false;
	}
	
	/**
	 *	Build hierarchy links
	 *
	 *	@access private
	 *	@param array (Sub) hierarchy
	 *	@param string Language code
	 *	@return array Meta value IDs => Links
	 */
	private function _getFullLinks($hierarchy, $langCode)
	{
		$links = array();
		
		if (!empty($hierarchy)) {
			
			// Get value ID to slug mapping;
			static $flipped;
			if (!$flipped) {
				$valueSlugMapping = $this->_getMetaValueSlugMapping(false, $langCode);
				$flipped = array_flip($valueSlugMapping);
			}
			
			foreach ($hierarchy as $element) {
				
				// Build link
				$link = '';
				if ($ancestry = $this->_getHierarchyElementAncestry($element['id'])) {
					foreach ($ancestry as $ancestorValueId) {
						$link .= '/'.$flipped[$ancestorValueId];
					}
				}
				$link .= '/'.$flipped[$element['id']];
				
				// Good to go
				$links[$element['id']] = $link;
				
				// Children?
				if (!empty($element['values'])) {
					if ($childLinks = $this->_getFullLinks($element['values'], $langCode)) {
						$links += $childLinks;
					}
				}
			}
		}
		
		return $links;
	}
	
	/**
	 *	Get children from a hierarchy element
	 *
	 *	@access public
	 *	@param int Meta value ID
	 *	@return array Children
	 */
	public function getHierarchyChildren($valueId)
	{
		// Get element parents.
		$ancestry = $this->_getHierarchyElementAncestry($valueId);
		
		// Drill down hierarchy
		$children = $this->getHierarchy();
		foreach ($ancestry as $ancestorId) {
			$children = $children[$ancestorId]['values'];
		}
		
		// Return
		return $children;
	}
	
	/**
	 *	Add meta value details
	 *
	 *	@access protected
	 *	@param array Meta values
	 */
	protected function _addMetaValueDetails(&$metaValues)
	{
		if (!empty($metaValues)) {
			$metaValues = array_flip($metaValues);
			foreach ($metaValues as $metaValueId => &$metaValue) {
				$metaValue = $this->getValue($metaValueId);
			}
			unset($metaValue);
		}
	}
	
	/**
	 *	Check if a meta value is in the hierarchy
	 *
	 *	@access public
	 *	@param int Meta value ID
	 *	@return bool In hierarchy
	 */
	public function inHierarchy($valueId)
	{
		// Get the ancestry
		$ancestry = $this->_getHierarchyElementAncestry($valueId);
		
		// Bogus value?
		return $ancestry !== false;
	}
	
	/**
	 *	Check if slug is in use
	 *
	 *	@access public
	 *	@param string Slug
	 *	@param int Item ID exception
	 *	@return bool
	 */
	public function isMetaValueSlugInUse($slug, $valueId = null, $lang = 'EN')
	{
		// Check database
		$query = 'SELECT lmv.id
			FROM `languageMetaValues` AS `lmv`
			WHERE lmv.slug = "'.$this->_db->escape($slug).'" AND lang="'.$this->_db->escape($lang).'"';
		if ($valueId) {
			// It's OK if slug-holder is myself.
			$query .= '
				AND lmv.id != '.(int) $valueId;
		}
		$this->_db->setQuery($query);
		return (bool) $this->_db->loadResult();
	}
    
    public function buildMeta($groupId)
    {
        $this->_getMetaItemMapping($groupId, true);
    }
}

?>
