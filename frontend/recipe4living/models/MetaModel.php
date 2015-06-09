<?php
 
/**
 *	Metadata Model
 *
 *	@package BluApplication
 *	@subpackage BluModels
 */
class ClientFrontendMetaModel extends ClientMetaModel
{
	/**
	 *	The Hierarchy
	 *
	 *	@var array
	 */
	protected $_hierarchy;
	
	/**
	 *	Currently selected filters
	 *
	 *	@access protected
	 *	@var array
	 */
	protected $_filters;
	
	/**
	 * Get meta groups
	 *
	 * @access public
	 * @param bool Rebuild
	 * @return array List of meta groups
	 */
	public function getGroups($forceRebuild = false, $incRangeValues = true, $langCode = null)
	{
		$groups = parent::getGroups($forceRebuild, $incRangeValues, $langCode);
		
		// Add in special price range group
		if (BluApplication::getSetting('enablePriceRangeFilter')) {
			$groups['price'] = $this->getGroup('price', $forceRebuild, null, $incRangeValues);
		}
		
		return $groups;
	}
	
	/**
	 * Return meta group details
	 *
	 * @param int Group id 
	 * @param bool Force rebuild?
	 * @param array Optional set of hierarchy filters to use for range generations 
	 *	@param string Language code.
	 * @return array Meta group
	 */
	public function getGroup($groupId, $forceRebuild = false, $hierarchyFilters = null, $incRangeValues = true, $langCode = null)
	{
		// Default to using first level of hierarchy for relvant group values
		if ($hierarchyFilters === null && ($topLevelFilter = $this->getTopHierarchyFilter())) {
			$hierarchyFilters = array(
				$topLevelFilter['groupId'] => array(
					$topLevelFilter['id']
				)
			);
		}
		
		// Get group, with relevant values
		$group = parent::getGroup($groupId, $forceRebuild, $hierarchyFilters, $incRangeValues, $langCode);
		
		// Return
		return $group;
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
		// Get selector
		$selector = parent::getSelector($selectorId, $forceRebuild, $langCode);
		
		// Return
		return $selector;
	}
	
	/**
	 *	Clear selected filters.
	 *
	 *	@access public
	 *	@return bool Success
	 */
	public function clearFilters()
	{
		return ($this->_filters = array());
	}
	
	/**
	 *	Apply multiple filters.
	 *	For fewer cache calls.
	 *
	 *	@access public
	 *	@param array Slugs
	 *	@return bool Success
	 */
	public function applyFilters($slugs)
	{
		// Try from cache
		sort($slugs);
		$cacheKey = 'metaFilters_'.serialize($slugs);
		$filters = $this->_cache->get($cacheKey);$filters = false;$filters = false;
		if ($filters === false) {
			
			// Erase current filters - needed due to how self::applyFilter is implemented
			$oldFilters = $this->_filters;
			$this->_filters = array();
			
			// Build filters
			foreach ($slugs as $slug) {
				$this->applyFilter($slug);
			}
			
			// Store in cache
			$this->_cache->set($cacheKey, $this->_filters);
			
			// Re-apply old filters, in correct order
			foreach ($this->_filters as $groupId => $valueIds) {
				if (isset($oldFilters[$groupId])) {
					$valueIds += $oldFilters[$groupId];
				}
				$oldFilters[$groupId] = $valueIds;
			}
			$this->_filters = $oldFilters;
			
		// Manually append the raw filters
		} else {
			foreach ($filters as $groupId => $valueIds) {
				if (isset($this->_filters[$groupId])) {
					$valueIds += $this->_filters[$groupId];
				}
				$this->_filters[$groupId] = $valueIds;
			}
		}
		
		// Return
		return true;
	}
	
	/**
	 *	Switch a meta value on.
	 *
	 *	Can take:
	 *		[meta value slug],
	 *		[meta group slug]:[parameters[:more parameters]]
	 *
	 *	@access public
	 *	@param string Meta value slug
	 *	@return bool Success
	 */
	public function applyFilter($slug)
	{
		// Argument is a meta group slug with parameters
		if (strpos($slug, ':') !== false) {
			$parameters = explode(':', $slug);
			$slug = array_shift($parameters);
			
			// Price range filter bodge
			if (strtolower($slug) == 'price') {
				$group = array(
					'id' => 'price',
					'type' => 'price'
				);
				
			// Get group
			} else {
				$slugMapping = $this->_getMetaGroupSlugMapping();
				if (!isset($slugMapping[$slug])) {
					return false;
				}
				$group = $this->getGroup($slugMapping[$slug]);
			}
			
			// Apply filters
			switch ($group['type']) {
				case 'pick':
					// Naughty, shouldn't use meta value slugs with :'s
					$success = true;
					foreach ($parameters as $metaValueSlug) {
						if (!$this->_applyPickFilter($metaValueSlug)) {
							$success = false;
						}
					}
					return $success;
					
				case 'numberpick':
					return $this->_applyNumberpickFilter($group['id'], $parameters);
					
				case 'numberrange':
				case 'price':
					return $this->_applyNumberrangeFilter($group['id'], reset($parameters), end($parameters));
					
				case 'keywords':
				default:
					// Not implemented
					return false;
			}
			
		// Argument is a meta selector slug or meta value slug.
		} else {
			
			// Try meta selector
			$metaSelectorSlugMapping = $this->_getMetaSelectorSlugMapping();
			if (isset($metaSelectorSlugMapping[$slug])) {
				return $this->_applySelectorFilter($metaSelectorSlugMapping[$slug]);
			}
			
			// Do meta value.
			return $this->_applyPickFilter($slug);
		}
	}
	
	/**
	 *	Apply a pick filter
	 *
	 *	If filter is a top level filter, only apply if no other top level filters exist.
	 *
	 *	@access protected
	 *	@param string Meta value slug
	 *	@return bool Success
	 */
	protected function _applyPickFilter($slug)
	{
		// Get mappings
		$slugMapping = $this->_getMetaValueSlugMapping();
		$groupMapping = $this->getMetaValueGroupMapping();
		
		// Search IDs by slug
		if (!isset($slugMapping[$slug])) {
			return false;
		}
		$valueId = $slugMapping[$slug];
		$groupId = $groupMapping[$valueId];
		
		// Top level malarky.
		$this->_setActive($valueId);
		
		// Apply filter
		if (isset($this->_filters[$groupId][$valueId])) {
			return true;
		}
		return ($this->_filters[$groupId][$valueId] = $valueId);
	}
	
	/**
	 *	Apply a numberpick filter.
	 *
	 *	@access protected
	 *	@param int Meta group ID
	 *	@param array Values
	 *	@return bool Success
	 */
	protected function _applyNumberpickFilter($groupId, $values)
	{
		// No values to apply
		if (empty($values)) {
			return true;
		}
		
		// Get group
		if (!$group = $this->getGroup($groupId)) {
			return false;
		}
		
		// Validate meta values
		$values = array_flip($values);
		$values = array_intersect_key($values, $group['values']);
		if (empty($values)) {
			return false;
		}
		
		// Append meta values
		$metaValues = array_keys($values);
		if (!isset($this->_filters[$group['id']])) {
			$this->_filters[$group['id']] = array();
		}
		
		// Merge with existing
		$success = true;
		foreach ($metaValues as $metaValue) {
			if (!$this->_filters[$group['id']][$metaValue] = $metaValue) {
				$success = false;
			}
		}
		
		// Return
		return $success;
	}
	
	/**
	 *	Apply a numberrange filter.
	 *
	 *	N.B. careful with the values. Empty string means it's not set.
	 *
	 *	@access protected
	 *	@param int Meta group ID
	 *	@param string Minimum value
	 *	@param string Maximum value
	 *	@return bool Success
	 */
	protected function _applyNumberrangeFilter($groupId, $minValue, $maxValue)
	{
		// Existing bounds
		$filter = array(
			'min' => isset($this->_filters[$groupId]['min']) ? $this->_filters[$groupId]['min'] : false,
			'max' => isset($this->_filters[$groupId]['max']) ? $this->_filters[$groupId]['max'] : false
		);
		
		// Overriding bounds
		$filter['min'] = ($minValue === '') ? false : (double) $minValue;
		$filter['max'] = ($maxValue === '') ? false : (double) $maxValue;
		
		// Reinstate
		return $this->_filters[$groupId] = $filter;
	}
	
	/**
	 *	Apply a meta selector filter.
	 *
	 *	@access protected
	 *	@param int Meta selector ID
	 *	@return bool Success
	 */
	protected function _applySelectorFilter($selectorId)
	{
		// Get selector
		if (!$selector = $this->getSelector($selectorId)) {
			return false;
		}
		
		// Get mappings
		$groupTypeMapping = $this->_getMetaGroupTypeMapping();
		
		// Apply values
		$success = true;
		foreach ($selector['values'] as $groupId => $values) {
			switch ($groupTypeMapping[$groupId]) {
				case 'pick':
					foreach ($values as $value) {
						if (!$this->applyFilter($value['slug'])) {
							$success = false;
						}
					}
					break;
			}
		}
		
		// Return
		return $success;
	}
	
	/**
	 *	Get current filters.
	 *
	 *	Return a lightweight array of Meta group ID => array of Meta values(/ IDs)
	 *
	 *	@access public
	 *	@param int Meta group ID
	 *	@return array Filters.
	 */
	public function getFilters($groupId = null)
	{
		// Return the whole lot.
		if (empty($groupId)) {
			return empty($this->_filters) ? array() : $this->_filters;
		}
		
		// Limit by group
		if (isset($this->_filters[$groupId])) {
			return $this->_filters[$groupId];
		}
		
		// Fail
		return false;
	}
	
	/**
	 *	Check if any filters have been applied
	 *
	 *	@access public
	 *	@return bool
	 */
	public function filtersSet()
	{
		return !empty($this->_filters);
	}
	
	/**
	 *	Apply the default filters
	 *
	 *	@access public
	 *	@return bool Success
	 */
	public function applyDefaultFilters()
	{
		// Get filters
		$cacheKey = 'metaDefaultFilters';
		$filters = $this->_cache->get($cacheKey);
		if ($filters === false) {
			$filters = array();
			if ($metaGroups = $this->getGroups()) {
				
				// Build
				foreach ($metaGroups as $groupId => $metaGroup) {
					if (empty($metaGroup['values'])) {
						continue;
					}
					
					foreach ($metaGroup['values'] as $key => $metaValue) {
						if ($metaValue['default']) {
							$filters[$groupId][$key] = $key;
						}
					}
				}
			}
			$this->_cache->set($cacheKey, $filters);
		}
		
		// Apply filters
		return ($this->_filters = $filters);
	}
	
	/**
	 *	Get a link for a filter toggler.
	 *
	 *	@access protected
	 *	@param array Meta value details
	 *	@param array Meta group details
	 *	@param bool Whether the given value turned out to be on or not (discrete values only.)
	 *	@return string Link.
	 */
	protected function _getLink($metaValue, $metaGroup, &$selected = null)
	{
		$groupId = $metaGroup['id'];
		
		// Construct temporary filters, depending on currently selected filters.
		$filters = $this->_filters;
		switch ($metaGroup['type']) {
			case 'pick':
			case 'numberpick':
				$value = ($metaGroup['type'] == 'pick') ? $metaValue['id'] : $metaValue['value'];
				if (isset($filters[$groupId][(string) $value])) {
					// Unset
					unset($filters[$groupId][(string) $value]);
					if (empty($filters[$groupId])) {
						unset($filters[$groupId]);
					}
					$selected = false;
				} else {
					// Set
					$filters[$groupId][(string) $value] = $value;
					$selected = true;
				}
				break;
				
			case 'numberrange':
			case 'price':
				$filter = array(
					'min' => isset($filters[$groupId]['min']) ? $filters[$groupId]['min'] : false,
					'max' => isset($filters[$groupId]['max']) ? $filters[$groupId]['max'] : false
				);
				
				// Overriding bounds
				$value = $metaValue['value'];
				if (isset($value['min'])) {
					$filter['min'] = $value['min'];
				}
				if (isset($value['max'])) {
					$filter['max'] = $value['max'];
				}
				
				$filters[$groupId] = $filter;
				break;
				
			case 'keywords':
				// Not implemented
				break;
		}
		
		// Build link
		$link = $this->buildLink($filters);
		return $link;
	}
	
	/**
	 *	Get a link for a selector toggler
	 *
	 *	@access protected
	 *	@param array Meta selector values
	 *	@return string Link
	 */
	protected function _getSelectorLink(array $selectorValues)
	{
		// Construct temporary filters
		$filters = $this->_filters;
		
		// Find out which of the selector's values are already enabled
		$selectedValues = array();
		$nonSelectedValues = array();
		foreach ($selectorValues as $groupId => $values) {
			foreach ($values as $value) {
				if (isset($filters[$value['groupId']][$value['id']])) {
					$selectedValues[$value['groupId']][$value['id']] = $value['id'];
				} else {
					$nonSelectedValues[$value['groupId']][$value['id']] = $value['id'];
				}
			}
		}
		
		// Either add them *all* in...
		if (!empty($nonSelectedValues)) {
			foreach ($nonSelectedValues as $groupId => $valueIds) {
				foreach ($valueIds as $valueId) {
					$filters[$groupId][$valueId] = $valueId;
				}
			}
			
		// ...or take them *all* out
		} else {
			foreach ($selectedValues as $groupId => $valueIds) {
				foreach ($valueIds as $valueId) {
					unset($filters[$groupId][$valueId]);
				}
			}
		}
		
		// Build link
		$link = $this->buildLink($filters);
		return $link;
	}
	
	/**
	 *	Build link for a filter toggler.
	 *
	 *	@access public
	 *	@param array Filters
	 *	@return string Link
	 */
	public function buildLink(array $filters)
	{
		// Convert filters to slugs.
		$slugs = array();
		if (empty($filters)) {
			
			// Show all products
			$slugs[] = '_all';
			
		} else {
			foreach ($filters as $groupId => $values) {
				
				// Sort, so link is unique (SEO?)
				natcasesort($values);
				
				// Get group
				$group = $this->getGroup($groupId);
				
				// Build link part.
				switch ($group['type']) {
					case 'pick':
						foreach ($values as $valueId) {
							if (isset($group['values'][$valueId]['slug'])) {
								$slugs[] = $group['values'][$valueId]['slug'];
							}
						}
						break;
						
					case 'numberpick':
						if (isset($group['slug'])) {
							$slugs[] = $group['slug'].':'.implode(':', $values);
						}
						break;
						
					case 'numberrange':
						if (isset($group['slug'])) {
							$slug = $group['slug'];
							$slug .= ':'.(string) $values['min'];
							$slug .= ':'.(string) $values['max'];
							$slugs[] = $slug;
						}
						break;
						
					case 'price':
						$slug = 'price';
						$slug .= ':'.(string) $values['min'];
						$slug .= ':'.(string) $values['max'];
						$slugs[] = $slug;
						break;
						
					case 'keywords':
						// Not implemented
						break;
				}
			}
		}
		
		// Return link
		$link = '/'.implode('/', $slugs);
		return $link;
	}
	
	/**
	 * Add links to meta groups
	 * 
	 * @param string Optional query string params to append to links (e.g search term)
	 * @param array Meta groups to add links to
	 */
	public function addLinks(&$metaGroups, $qsParams = null)
	{
		if (empty($metaGroups)) {
			return;
		}
		
		foreach ($metaGroups as $groupId => &$metaGroup) {
			if (empty($metaGroup['values'])) {
				continue;
			}
			foreach ($metaGroup['values'] as $valueId => &$metaValue) {
				if (!empty($metaValue['disabled'])) {
					$metaValue['link'] = '#';
				} else {
					$metaValue['link'] = $this->_getLink($metaValue, $metaGroup);
					if (!empty($qsParams)) {
						$metaValue['link'] .= '?'.http_build_query($qsParams, '', '&amp;');
					}
				}
			}
			unset($metaValue);
		}
		unset($metaGroup);
		
		return true;
	}
	
	/**
	 *	Add links to hierarchy
	 *
	 *	@access public
	 *	@param array Hierarchy
	 */
	public function addHierarchyLinks(&$hierarchy)
	{
		if (empty($hierarchy)) {
			return;
		}
		
		foreach ($hierarchy as $valueId => &$element) {
			
			// Prepare
			$isSelector = isset($element['type']) && $element['type'] == 'selector';
			
			// Add link
			if (!empty($element['disabled'])) {
				$element['link'] = '#';
			} else if ($isSelector) {
				$element['link'] = $this->_getSelectorLink($element['values']);
			} else {
				$metaGroup = $this->getGroup($element['groupId']);
				$element['link'] = $this->_getLink($element, $metaGroup);
			}
			
			// Do children
			if (!$isSelector && !empty($element['values'])) {
				$children = $element['values'];
				$this->addHierarchyLinks($children);
				$element['values'] = $children;
			}
		}
		unset($element);
		
		return true;
	}
	
	/**
	 *	Add hierarchy element's links. Lol PHP.
	 *
	 *	@access public
	 *	@param array Hierarchy element.
	 */
	public function addHierarchyElementLinks(&$element)
	{
		$subHierarchy = array($element['id'] => $element);
		$this->addHierarchyLinks($subHierarchy);
		$element = current($subHierarchy);
		return true;
	}
	
	/**
	 *	Get base link.
	 *
	 *	Link limited by Hierarchy filters.
	 *
	 *	@param int Optional number of levels to limit base link to
	 *	@param array Optional query string params to append to link
	 *	@access public
	 *	@return string Link
	 */
	public function getBaseLink($numLevels = false, $qsParams = null)
	{
		// Base filters
		$bottom = $this->getBottomHierarchyFilter();
		$isSelector = isset($bottom['type']) && $bottom['type'] == 'selector';
		$filters = $this->_getHierarchyElementAncestry($isSelector ? $bottom['aliasId'] : $bottom['id']);
		$this->_addMetaValueDetails($filters);
		$filters[] = $bottom;
		
		// Grab filters for generating link.
		$metaGroups = array();
		if (!empty($filters)) {
			$level = 0;
			foreach ($filters as $element) {
				$level++;
				
				// If selector, add all its values
				if (isset($element['type']) && $element['type'] == 'selector') {
					foreach ($element['values'] as $groupId => $values) {
						foreach ($values as $value) {
							$metaGroups[$groupId][$value['id']] = $value['id'];
						}
					}
					
				// Add single group value
				} else {
					$metaGroups[$element['groupId']][$element['id']] = $element['id'];
				}
				
				// Break if we've hit the required level
				if (($numLevels !== false) && ($level >= $numLevels)) {
					break;
				}
			}
		}
		
		// Build link
		$link = $this->buildLink($metaGroups);
		
		// Append QS params if we have any
		if (!empty($qsParams)) {
			$link .= '?'.http_build_query($qsParams, '', '&amp;');
		}
		
		return $link;
	}
	
	/**
	 *	Get title-link pairs for hierarchy filters
	 *
	 *	@access public
	 *	@return array Breadcrumbs
	 */
	public function getBreadcrumbs()
	{
		// Prepare
		$pathway = array();
		
		// Get filter ancestry
		if (!$this->hasDisplayableFilters()) {
			return $pathway;
		}
		$bottom = $this->getBottomHierarchyFilter();
		$isSelector = isset($bottom['type']) && $bottom['type'] == 'selector';
		$filters = $this->_getHierarchyElementAncestry($isSelector ? $bottom['aliasId'] : $bottom['id']);
		$this->_addMetaValueDetails($filters);
		$filters[] = $bottom;
		
		// Build pathway
		$parentFilters = array();
		foreach ($filters as $element) {
			
			// Build parent filters, for generating link.
			if (isset($element['type']) && $element['type'] == 'selector') {
				foreach ($element['values'] as $groupId => $values) {
					foreach ($values as $value) {
						$parentFilters[$groupId][$value['id']] = $value['id'];
					}
				}
			} else {
				$parentFilters[$element['groupId']][$element['id']] = $element['id'];
			}
			
			$pathway[] = array(
				$element['name'],
				$this->buildLink($parentFilters)
			);
		}
		
		// Return
		return $pathway;
	}
	
	/**
	 *	Get the unique filter only from the highest level of The Hierarchy, if it exists.
	 *	
	 *	@access public
	 *	@return array Meta value/selector details
	 */
	public function getTopHierarchyFilter()
	{
		static $filter;
		if (!isset($filter)) {
			
			// No filters set...yet.
			if (!$this->filtersSet()) {
				return false;
			}
			
			// Do intersection
			$filter = self::_intersectHierarchy($this->_filters, $this->getHierarchy(), false);
		}
		return $filter;
	}
	
	/**
	 *	Get the lowest common Hierarchy filter.
	 *	
	 *	@access public
	 *	@return array Meta value/selector details
	 */
	public function getBottomHierarchyFilter()
	{
		static $filter;
		if (!isset($filter)) {
			
			// No filters set...yet.
			if (!$this->filtersSet()) {
				return false;
			}
			
			// Do intersection
			$filter = self::_intersectHierarchy($this->_filters, $this->getHierarchy());
		}
		return $filter;
	}
	
	/**
	 *	Whether current filter combination has displayable title/keywords/breadcrumbs etc.
	 *
	 *	@access public
	 *	@return bool
	 */
	public function hasDisplayableFilters()
	{
		$filter = $this->getBottomHierarchyFilter();
		return !empty($filter);
	}
	
	/**
	 *	Get hierarchy filter info: html title tag.
	 *
	 *	@access public
	 *	@return string
	 */
	public function getPageTitle()
	{
		return $this->_getTitle('pageTitle');
	}
	
	/**
	 *	Get hierarchy filter info: products listing page title
	 *
	 *	@access public
	 *	@return string
	 */
	public function getListingTitle()
	{
		// Just do bottom one
		if (!$filter = $this->getBottomHierarchyFilter()) {
			return false;
		}
		
		// Return
		return $filter['listingTitle'];
	}
	
	/**
	 *	Get hierarchy filter info: some kind of title
	 *
	 *	@access protected
	 *	@param string Kind of title
	 *	@return string The title.
	 */
	protected function _getTitle($titleType)
	{
		// Get lowest common filter.
		if (!$filter = $this->getBottomHierarchyFilter()) {
			return false;
		}
		
		// If selector, use alias value ID
		if (isset($filter['type']) && $filter['type'] == 'selector') {
			$valueId = $filter['aliasId'];

		// If meta value, use original ID
		} else {
			$valueId = $filter['id'];
		}

		// Get element ancestry.
		$parentFilters = $this->_getHierarchyElementAncestry($valueId);
		$this->_addMetaValueDetails($parentFilters);
		
		// Get title: filter names reversed.
		$title = array();
		if (!empty($parentFilters)) {
			foreach ($parentFilters as $parentFilter) {
				$title[] = $parentFilter[$titleType];
			}
		}
		$title[] = $filter[$titleType];
		$title = array_reverse($title);
		$title = implode(' | ', $title);
		
		// Return
		return $title;
	}
	
	/**
	 *	Get hierarchy filter info: html meta description tag.
	 *
	 *	@access public
	 *	@return string
	 */
	public function getDescription()
	{
		// Get meta value
		if (!$metaValue = $this->getDisplayInfo()) {
			return false;
		}
		
		// Get description
		$description = empty($metaValue['pageDescription']) ? $metaValue['name'] : $metaValue['pageDescription'];
		return $description;
	}
	
	/**
	 *	Get hierarchy filter info: html meta keywords tag
	 *
	 *	@access public
	 *	@return string
	 */
	public function getKeywords()
	{
		// Get lowest common filter.
		if (!$filter = $this->getBottomHierarchyFilter()) {
			return false;
		}
		
		// Get ancestry filters.
		$parentFilters = $this->_getHierarchyElementAncestry($filter['id']);
		$this->_addMetaValueDetails($parentFilters);
		
		// Get keywords
		$keywords = array();
		if (!empty($parentFilters)) {
			foreach ($parentFilters as $parentFilter) {
				$keywords[] = $parentFilter['keywords'];
			}
		}
		$keywords[] = $filter['keywords'];
		$keywords = array_reverse($keywords);
		$keywords = implode(', ', $keywords);
		
		// Return
		return $keywords;
	}
	
	/**
	 *	Get hierarchy filter info: product listing page description stuff.
	 *
	 *	Basically returns the meta value.
	 *
	 *	@access public
	 *	@return array
	 */
	public function getDisplayInfo()
	{
		return $this->getBottomHierarchyFilter();
	}
	
	/**
	 *	Whether to show a "section listing" page, rather than a "products listing" page.
	 *
	 *	@todo
	 *
	 *	@access public
	 *	@return bool
	 */
	public function showSectionListing()
	{return false;
		// Get bottom filter.
		if (!$bottomFilter = $this->getBottomHierarchyFilter()) {
			return false;
		}
		
		// Doesn't have children?
		if (empty($bottomFilter['values'])) {
			return false;
		}
		
		// Has filters other than bottom filter (and its parents)?
		$parents = $this->_getHierarchyElementAncestry($bottomFilter['id']);
		$ancestryFilters = array();
		if (!empty($parents)) {
			$valueGroupMapping = $this->getMetaValueGroupMapping();
			foreach ($parents as $valueId) {
				$ancestryFilters[$valueGroupMapping[$valueId]][$valueId] = $valueId;
			}
		}
		$ancestryFilters[$bottomFilter['groupId']][$bottomFilter['id']] = $bottomFilter['id'];
		
		// ...from other groups?
		if (count(array_diff_key($this->_filters, $ancestryFilters))) {
			return false;
		}
		
		// ...from the same group?
		foreach ($this->_filters as $groupId => $valueIds) {
			if (count(array_diff_key($valueIds, $ancestryFilters[$groupId]))) {
				return false;
			}
		}
		
		// Win.
		return true;
	}
	
	/**
	 *	Get hierarchy, with "selected" flag set.
	 *
	 *	@access public
	 *	@param bool Rebuild
	 *	@param string Language code
	 *	@return array Hierarchy
	 */
	public function getHierarchy($forceRebuild = false, $langCode = null, $includeProducts = false)
	{
		// Get from memory
		if (!isset($this->_hierarchy)) {
			$hierarchy = parent::getHierarchy($forceRebuild, $langCode, $includeProducts);
			
			// Set all top level meta values as inactive initially.
			if (!empty($hierarchy)) {
				foreach ($hierarchy as &$topLevel) {
					$topLevel['active'] = false;
				}
				unset($topLevel);
			}
			
			// Store at object level
			$this->_hierarchy = $hierarchy;
		}
		return $this->_hierarchy;
	}
	
	/**
	 *	Set a meta value as "active".
	 *
	 *	@access protected
	 *	@param int Meta value ID
	 *	@return bool Success
	 */
	protected function _setActive($valueId)
	{
		// Build hierarchy, if not built.
		if (empty($this->_hierarchy)) {
			$this->getHierarchy();
		}
		
		// If not a Top level, screw it
		if (!isset($this->_hierarchy[$valueId])) {
			return false;
		}
		
		// Set the Top level as "active"
		return ($this->_hierarchy[$valueId]['active'] = true);
	}
	
	/**
	 *	Checks if a meta value has been set as a filter.
	 *
	 *	@access public
	 *	@param string Meta value slug
	 *	@return bool Success
	 */
	public function hasFilter($slug)
	{
		// No filters
		if (!$this->filtersSet()) {
			return false;
		}
		
		// Get indices
		$metaValueSlugMapping = $this->_getMetaValueSlugMapping();
		if (!isset($metaValueSlugMapping[$slug])) {
			return false;
		}
		
		// Get IDs
		$valueId = $metaValueSlugMapping[$slug];
		
		$metaValueGroupMapping = $this->getMetaValueGroupMapping();
		$groupId = $metaValueGroupMapping[$valueId];
		
		// Search
		return isset($this->_filters[$groupId][$valueId]);
	}

	/**
	 *	Return meta groups to show on the home page at the given level
	 *
	 *	@param int Level (1 for main, 2 for advanced)
	 *	@param bool Rebuild
	 *	@return array Meta groups
	 */
	public function getIndexGroups($level, $forceRebuild = false)
	{
		// Get all meta groups and group by level
		static $levelMetaGroups;
		if ($forceRebuild || !isset($levelMetaGroups)) {
			
			// Get language
			$language = BluApplication::getLanguage();
			$langCode = $language->getLanguageCode();
			
			// Memcache?
			$cacheKey = 'metaIndexGroups_'.$langCode;
			$levelMetaGroups = $forceRebuild ? false : $this->_cache->get($cacheKey);
			if ($levelMetaGroups === false) {
				
				// Build
				$levelMetaGroups = array();
				$allGroups = $this->getGroups(false, true, $langCode);
				foreach ($allGroups as $groupId => $metaGroup) {
					if ($metaGroup['indexLevel']) {
						$levelMetaGroups[$metaGroup['indexLevel']][$groupId] = $metaGroup;
					}
				}
				
				$this->_cache->set($cacheKey, $levelMetaGroups);
			}
		}
		
		// Return only the relevant level
		return isset($levelMetaGroups[$level]) ? $levelMetaGroups[$level] : false;
	}
	
	/**
	 *	Get parent of an element in The Hierarchy
	 *
	 *	@access public
	 *	@param int Meta Value ID
	 *	@return array Parent details
	 */
	public function getParent($valueId)
	{
		if (!$ancestry = $this->_getHierarchyElementAncestry($valueId)) {
			return false;
		}
		
		if (!$parentMetaValueId = end($ancestry)) {
			return false;
		}
		
		return $this->_getHierarchyElement($parentMetaValueId);
	}
	
}
 
?>
