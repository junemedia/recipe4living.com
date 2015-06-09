<?php

/**
 *	Backend Meta
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class BackendMetaModel extends ClientMetaModel
{
	/**
	 *	Get parent elements, youngest at bottom.
	 *
	 *	N.B. Some of the output value IDs may actually be links to selectors. Check self::_getMetaValueSelectorMapping.
	 *
	 *	@access public
	 *	@param int Value ID
	 *	@return array List of meta value IDs
	 */
	public function getHierarchyElementAncestry($valueId)
	{
		return parent::_getHierarchyElementAncestry($valueId);
	}
	
	/**
	 *	Add a child element to the hierarchy
	 *
	 *	@access public
	 *	@param int Parent meta value ID
	 *	@param string Child meta value internal name
	 *	@param bool Skip cache
	 *	@return int Child meta value ID
	 */
	public function addHierarchyElement($parentValueId, $name, $skipCache = false)
	{
		// Get parent meta group ID
		$query = 'SELECT mh.aliasId
			FROM `metaHierarchy` AS `mh`
			WHERE mh.valueId = '.(int) $parentValueId.'
				AND mh.aliasType = "group_child"';
		$this->_db->setQuery($query);
		if (!$groupId = $this->_db->loadResult()) {
			
			// Create the meta group
			$value = $this->getValue($parentValueId);
			$groupId = $this->addMetaGroup($value['name']);
			$this->addLanguageMetaGroup($groupId, 'EN', $value['name'], $value['name']);
			if (!$skipCache) {
				$this->_clearGroupCache($groupId);
			}
			
			// Point meta value to meta group
			$query = 'INSERT INTO `metaHierarchy`
				SET `valueId` = '.(int) $value['id'].',
					`aliasId` = '.(int) $groupId.',
					`aliasType` = "group_child"';
			$this->_db->setQuery($query);
			$this->_db->query();
		}
		
		// Create meta value for new child
		$valueId = $this->addMetaValue($groupId, $name, array(), true);
		$this->addLanguageMetaValue($valueId, 'EN', $name);
		if (!$skipCache) {
			$this->_clearGroupCache($groupId);
		}
		
		// Rebuild hierarchy
		if (!$skipCache) {
			
			// EDIT: I give up
			$cacheModel = BluApplication::getModel('cache');
			$cacheModel->deleteEntriesLike('meta');
		}
		
		// Return
		return $valueId;
	}
	
	/**
	 *	Fully remove an element and its children from the hierarchy
	 *
	 *	@access public
	 *	@param int Meta value ID
	 *	@param bool Skip cache flushing
	 *	@return bool Success
	 */
	public function deleteHierarchyElement($valueId, $skipCache = false)
	{
		$success = true;
		
		// Delete the meta value
		if (!$this->deleteMetaValue($valueId, true, true, $skipCache)) {
			return false;
		}
		
		// Break the link in the hierarchy
		$query = 'SELECT mh.aliasId, mh.aliasType
			FROM `metaHierarchy` AS `mh`
			WHERE mh.valueId = '.(int) $valueId;
		$this->_db->setQuery($query);
		$hierarchy = $this->_db->loadAssoc();
		
		switch ($hierarchy['aliasType']) {
			case 'group_child':
				
				// Delete the group
				$this->_deleteMetaGroup($groupId);
				
				// Delete the group's meta values, recursively
				$query = 'SELECT mv.id
					FROM `metaValues` AS `mv`
					WHERE mv.groupId = '.(int) $groupId;
				$this->_db->setQuery($query);
				$valueIds = $this->_db->loadResultArray();
				if (!empty($valueIds)) {
					foreach ($valueIds as $valueId) {
						if (!$this->deleteHierarchyElement($valueId, $skipCache)) {
							$success = false;
						}
					}
				}
				break;
		}
		
		$query = 'DELETE FROM `metaHierarchy`
			WHERE `valueId` = '.(int) $valueId;
		$this->_db->setQuery($query);
		$this->_db->query();
		
		// Rebuild hierarchy
		if (!$skipCache) {
			
			// EDIT: I give up
			$cacheModel = BluApplication::getModel('cache');
			$cacheModel->deleteEntriesLike('meta');
		}
		
		// Return
		return true;
	}
	
	/**
	 *	set the display of the category + the articles/recipes in it to not Live.
	 *
	 *	@access public
	 *	@param int Meta value ID
	 *	@param bool Skip cache flushing
	 */
	public function unPublishCategory($valueId, $unpublisharticles){
		
		// unset display of the category 
		$query = 'UPDATE `metaValues`
			SET `display` = 0
			WHERE `id` = '.$valueId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		if($unpublisharticles){
			//getting all the articles in this category
			$query = 'SELECT amv.articleId
						FROM `articleMetaValues` AS `amv`
						WHERE `valueId` = '.(int) $valueId;
					$this->_db->setQuery($query);
			$items = $this->_db->loadAssocList('articleId');
			$articlesIds = array_keys($items);
			$lastIndex =  count($articlesIds)-1;
			
			$itemsModel = BluApplication::getModel('items');
			// unset Live the articles in this category
			foreach($articlesIds as $id => $articlesId){
				if($id == $lastIndex){				
					$itemsModel->unsetLive($articlesId);
				}else{
					$itemsModel->unsetLive($articlesId,false,true);
				}
			}
		}
		$this->_clearValueCache($valueId);
		//recursive call
		$metaModel = BluApplication::getModel('meta');		
		$subHierarchy = $metaModel->getHierarchySiblings($valueId);
		if(!empty($subHierarchy[$valueId]['values'])){
			$children = array_keys($subHierarchy[$valueId]['values']); 
			foreach($children as $child){
				$this->unPublishCategory($child, $unpublisharticles);
			}
		}else{
			$cacheModel = BluApplication::getModel('cache');
			$cacheModel->deleteEntriesLike('metaHierarchy_');			
			return true;
		}
		$cacheModel = BluApplication::getModel('cache');
		$cacheModel->deleteEntriesLike('metaHierarchy_');
		return true;
	}
	
	/**
	 *	set the display of the category + the articles/recipes in it to Live.
	 *
	 *	@access public
	 *	@param int Meta value ID
	 *	@param bool Skip cache flushing
	 */
	public function publishCategory($valueId, $publisharticles){
		
		// unset display of the category 
		$query = 'UPDATE `metaValues`
			SET `display` = 1
			WHERE `id` = '.$valueId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		if($publisharticles){
			//getting all the articles in this category
			$query = 'SELECT amv.articleId
						FROM `articleMetaValues` AS `amv`
						WHERE `valueId` = '.(int) $valueId;
					$this->_db->setQuery($query);
			$items = $this->_db->loadAssocList('articleId');
			$articlesIds = array_keys($items);
			$lastIndex =  count($articlesIds)-1;
			
			$itemsModel = BluApplication::getModel('items');
			// unset Live the articles in this category
			foreach($articlesIds as $id => $articlesId){
				if($id == $lastIndex){				
					$itemsModel->setLive($articlesId);
				}else{
					$itemsModel->setLive($articlesId,false,true);
				}
			}
		}
		$this->_clearValueCache($valueId);
		$metaModel = BluApplication::getModel('meta');		
		$subHierarchy = $metaModel->getHierarchySiblings($valueId);
		if(!empty($subHierarchy[$valueId]['values'])){
			$children = array_keys($subHierarchy[$valueId]['values']);
			foreach($children as $child){
				$this->publishCategory($child, $publisharticles);
			}
		}else{
			$cacheModel = BluApplication::getModel('cache');
			$cacheModel->deleteEntriesLike('metaHierarchy_');			
			return true;	
		}
		$cacheModel = BluApplication::getModel('cache');
		$cacheModel->deleteEntriesLike('metaHierarchy_');		
		return true;
	}	
	
	/**
	 *	Unlink a child hierarchy element (delete the link, but not the child element - just orphan it)
	 *
	 *	@access public
	 *	@param int Meta value ID
	 *	@param bool Skip cache flushing
	 *	@return mixed Array of alias ID and alias type, if exists; true if doesn't exist; false if phailed
	 */
	public function unlinkHierarchyElement($valueId, $skipCache = false)
	{
		// Delete meta value
		if (!$this->deleteMetaValue($valueId, true, true, $skipCache)) {
			return false;
		}
		
		// Get link, if exists
		$query = 'SELECT mh.aliasId, mh.aliasType
			FROM `metaHierarchy` AS `mh`
			WHERE mh.valueId = '.(int) $valueId;
		$this->_db->setQuery($query);
		if ($link = $this->_db->loadAssoc()) {
			
			// You are the weakest link, goodbye.
			$query = 'DELETE FROM `metaHierarchy`
				WHERE `valueId` = '.(int) $valueId;
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				return false;
			}
			
		// We're all still good
		} else {
			$link = true;
		}
		
		// Rebuild cache
		if (!$skipCache) {
			$cacheModel = BluApplication::getModel('cache');
			$cacheModel->deleteEntriesLike('meta');
		}
		
		// Return
		return $link;
	}
	
	/**
	 *	Assign an item to a hierarchy element
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param int Meta value ID
	 *	@param bool Skip cache
	 *	@return bool Success
	 */
	public function addHierarchyElementItem($itemId, $valueId, $skipCache = false)
	{
		// Get hierarchy ancestry
		$parentValues = $this->_getHierarchyElementAncestry($valueId);
		$parentValues[$valueId] = $valueId;
		
		$metaValueGroupMapping = $this->getMetaValueGroupMapping();
		$parentValues = array_intersect_key($metaValueGroupMapping, $parentValues);
		$groupedParentValues = array();
		foreach ($parentValues as $valueId => $groupId) {
			$groupedParentValues[$groupId][$valueId] = $valueId;
		}
		
		// Add item to each hierarchy element
		$success = true;
		foreach ($groupedParentValues as $groupId => $values) {
			if (!$this->addItemMetaValues($itemId, $groupId, $values, $skipCache)) {
				$success = false;
			}
		}
		
		// Flush cache
		if (!$skipCache) {
			
			// Rebuild hierarchy
			// EDIT: I give up
			$cacheModel = BluApplication::getModel('cache');
			$cacheModel->deleteEntriesLike('meta');
			
			// Flush search results
			$itemsModel = BluApplication::getModel('items');
			$itemsModel->flushItemSearches();
		}
		
		// Return
		return $success;
	}
	
	/**
	 *	Remove an item from a hierarchy element
	 *
	 *	@access public
	 *	@param int Item ID
	 *	@param int Meta value ID
	 *	@param bool Skip cache
	 *	@return bool Success
	 */
	public function deleteHierarchyElementItem($itemId, $valueId, $skipCache = false)
	{
		// Delete item-metavalue mapping
		$valueGroupMapping = $this->getMetaValueGroupMapping();
		if (!$this->deleteItemMetaValue($itemId, $valueGroupMapping[$valueId], $valueId, $skipCache)) {
			return false;
		}
		
		// Flush cache
		if (!$skipCache) {
			
			// Rebuild hierarchy
			// EDIT: I give up
			$this->getGroup($valueGroupMapping[$valueId], true);
			$this->getHierarchy(true);
			
			// Flush search results
//			$itemsModel = BluApplication::getModel('items');
//			$itemsModel->flushItemSearches();
		}
		
		// Return
		return true;
	}

	/**
	 * Get raw language meta data
	 *
	 * @access public
	 * @return array Data
	 */
	public function exportLanguageMetaValues()
	{
		$query = 'SELECT mh.aliasId
			FROM metaHierarchy AS mh
			WHERE mh.aliasType LIKE "group_%"';
		$this->_db->setQuery($query);
		$hierarchyGroups = $this->_db->loadResultAssocArray('aliasId', 'aliasId');
		
		$query = 'SELECT lmv.id, lmv.name, lmv.description, lmv.keywords
			FROM languageMetaValues AS lmv
				LEFT JOIN metaValues AS mv ON lmv.id = mv.id
			WHERE mv.display = 1
				AND mv.internal = 0
				AND (mv.groupId = '.implode(' OR mv.groupId = ', $this->_db->escape($hierarchyGroups)).')';
		$this->_db->setQuery($query);
		return $this->_db->loadAssocList('id');
	}

	/**
	 * Import raw language meta data
	 *
	 * @access public
	 * @param array Data to import
	 * @return bool Success
	 */
	public function importLanguageMetaValues($rawData)
	{
		$importedMetaValues = array();

		// Push to DB
		if (!empty($rawData)) {
			foreach ($rawData as $row) {
				$row = array_map('trim', $row);

				$metaValueId = (int) $row[0];
				if (!$metaValueId) {
					continue;
				}

				$name = $row[1];
				$description = $row[2];
				$keywords = $row[3];

				$query = 'UPDATE languageMetaValues
					SET name = "'.$this->_db->escape($name).'",
						description = "'.$this->_db->escape($description).'",
						keywords = "'.$this->_db->escape($keywords).'"
					WHERE id = '.(int) $metaValueId.'
						AND lang = "EN"';
				$this->_db->setQuery($query);
				if ($this->_db->query()) {
					$importedMetaValues[$metaValueId] = $metaValueId;
				}
			}
		}

		// Flush caches
		if (!empty($importedMetaValues)) {
			$cacheModel = BluApplication::getModel('cache');
			$cacheModel->deleteEntriesLike('meta');
		}

		// Done
		return true;
	}
}

?>
