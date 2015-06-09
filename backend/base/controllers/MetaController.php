<?php

/**
 *	Meta controller.
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class MetaController extends ClientBackendController
{
	
	/**
	 *	Menu slug
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_menuSlug = 'meta';

	/**
	 * Default view
	 */	 	
	public function view()
	{
		// Get all groups
		$metaModel = BluApplication::getModel('meta');
		$allGroups = $metaModel->getGroups();
		
		// Load template
		include(BLUPATH_BASE_TEMPLATES.'/meta/view.php');
	}
	
	/**
	 *	Get all meta groups used by a product.
	 */
	public function itemValues()
	{
		$itemSlug = Request::getString('slug');
		
		// Get item ID
		$itemsModel = BluApplication::getModel('items');
		$itemId = $itemsModel->getItemId($itemSlug);
		
		// Get meta groups
		$metaModel = BluApplication::getModel('meta');
		$itemMetaGroups = $metaModel->getItemMetaGroups($itemId, false, null, true);
		$allGroups = $metaModel->getGroups();
		
		// Display
		return include(BLUPATH_BASE_TEMPLATES.'/meta/item_values.php');
	}
	
	/**
	 *	Add an item-metavalue mapping.
	 */
	public function addItemValue()
	{
		// Get request
		$itemSlug = Request::getString('slug');
		$metaGroupId = Request::getInt('group');
		$metaValue = Request::getVar('value');
		
		// Update meta group
		$itemsModel = BluApplication::getModel('items');
		$metaModel = BluApplication::getModel('meta');
		$itemId = $itemsModel->getItemId($itemSlug);
		$updated = $metaModel->addProductMetaValues($itemId, $metaGroupId, $metaValue, false, true);
		
		// Return to editing product values
		if ($updated) {		
			Messages::addMessage('Meta value added', 'info');
		}
		return $this->productValues();
	}
	
	/**
	 *	Remove an item-metavalue mapping.
	 */
	public function deleteItemValue()
	{
		// Get request
		$itemSlug = Request::getString('slug');
		$metaGroupId = Request::getInt('group');
		$metaValue = Request::getVar('value');
		
		// Commit to model
		$itemsModel = BluApplication::getModel('items');
		$metaModel = BluApplication::getModel('meta');
		$itemId = $itemsModel->getItemId($itemSlug);
		$deleted = $metaModel->deleteProductMetaValue($itemId, $metaGroupId, $metaValue);
		
		// Return to editing product values
		if ($deleted) {
			Messages::addMessage('Meta value deleted', 'info');
		}
		return $this->productValues();
	}
	
	/**
	 *	Interface for adding a new meta value.
	 */
	public function addValue()
	{
		// Get meta group
		$metaGroupId = Request::getInt('group');
		$metaModel = BluApplication::getModel('meta');
		$metaGroup = $metaModel->getGroup($metaGroupId);
		
		// Get available languages
		$availableLanguages = Language::getAvailableLanguages();
		
		// New defaults
		$internalName = '';
		$hidden = false;
		$default = false;
		$featured = false;
		$order = 0;
		$languages = array(
			array(
				'langCode' => '',
				'name' => '',
				'description' => '',
				'keywords' => '',
				'pageTitle' => '',
				'listingTitle' => '',
				'slug' => '',
				'pageDescription' => ''
			)
		);
		$mainImage = false;
		$images = array(
			'main' => false
		);
		$valueId = '';
		$groupId = $metaGroup['id'];
		
		// Template
		return include(BLUPATH_BASE_TEMPLATES.'/meta/edit_value.php');
	}
	
	/**
	 *	Edit an existing meta value
	 *
	 *	@access public
	 */
	public function editValue()
	{
		// Get request
		$metaModel = BluApplication::getModel('meta');
		$metaValueId = Request::getInt('value');
		
		// Get available languages
		$availableLanguages = Language::getAvailableLanguages();
			
		// Get default meta value
		$metaValue = $metaModel->getValue($metaValueId);
		
		// Languages
		$languages = array();
		foreach ($availableLanguages as $langCode => $language) {
			$metaValue = $metaModel->getValue($metaValueId, $langCode);
			$languages[] = array(
				'langCode' => $langCode,
				'name' => $metaValue['name'],
				'description' => $metaValue['description'],
				'keywords' => $metaValue['keywords'],
				'pageTitle' => $metaValue['pageTitle'],
				'listingTitle' => $metaValue['listingTitle'],
				'slug' => $metaValue['slug'],
				'pageDescription' => $metaValue['pageDescription']
			);
		}
		
		// Base details
		$internalName = $metaValue['internalName'];
		$hidden = !$metaValue['display'];
		$default = $metaValue['default'];
		$featured = $metaValue['featured'];
		$order = $metaValue['sequence'];
		$images = array(
			'main' => $metaValue['imageName']
		);
		$valueId = $metaValue['id'];
		$groupId = $metaValue['groupId'];
		
		// Template
		return include(BLUPATH_BASE_TEMPLATES.'/meta/edit_value.php');
	}
	
	/**
	 *	Update/insert a meta value
	 *
	 *	@access public
	 */
	public function saveValue()
	{
		// Prepare
		$success = true;
		$availableLanguages = Language::getAvailableLanguages();
		$metaModel = BluApplication::getModel('meta');
		$valueId = Request::getInt('valueId');
		
		// Get request, and validation.
		if (!$groupId = Request::getInt('group')) {
			if ($this->_doc->getFormat() != 'json') {
				Messages::addMessage('Could not obtain meta group ID. Please inform the administrator.', 'error');
			}
			$success = false;
		}
		if (!$internalName = Request::getString('internal')) {
			Messages::addMessage('Please fill in the internal name.', 'error');
			$success = false;
		}
		if ($languages = Request::getArray('languages')) {
			foreach ($languages as $index => &$language) {
				
				// If all except "language code" are empty, just ignore.
				$empty = true;
				foreach ($language as $key => $languageField) {
					if ($key == 'code') {
						continue;
					}
					if (!empty($languageField)) {
						$empty = false;
					}
				}
				if ($empty) {
					unset($languages[$index]);
					continue;
				}
				
				// Check if has name
				if (empty($language['name'])) {
					Messages::addMessage('Please fill in the '.$availableLanguages[$language['code']].' name.', 'error');
					$success = false;
					break;
				}
				
				// Check if slug in use
				if (empty($language['slug'])) {
					$language['slug'] = Utility::slugify($language['name']);
				}
				if (!$metaModel->metaValueSlugAvailable($language['slug'], $language['code'], $valueId)) {
					Messages::addMessage('The slug <code>'.$language['slug'].'</code> ('.$internalName.' - '.$availableLanguages[$language['code']].') is already in use.', 'error');
					$success = false;
					break;
				}
			}
			unset($language);
		} else {
			Messages::addMessage('Please enter at least one group of language-specific information.', 'error');
			$success = false;
		}
		if (!$success) {
			return false;
		}
		$hidden = Request::getBool('hidden');
		$default = Request::getBool('default');
		$featured = Request::getBool('featured');
		$order = Request::getInt('order');
		$images = Request::getFiles('images');
		
		// Save images, if any
		$uploadedImages = array();
		if (!empty($images)) {
			foreach ($images as $key => $image) {
				if (!Upload::isValid($image)) {
					continue;
				}
				if (!$hashedImageName = Upload::saveFile($image, BLUPATH_ASSETS.'/metaimages/')) {
					Messages::addMessage('Could not upload file <code>'.$image['name'].'</code>. Please try again.', 'error');
					continue;
				}
				$uploadedImages[$key] = $hashedImageName;
			}
		}
		
		// Do we update existing...?
		if ($valueId) {
			if (!$metaModel->updateMetaValue($valueId, array(
				'internalName' => $internalName,
				'images' => $uploadedImages,
				'sequence' => $order,
				'display' => !$hidden,
				'default' => $default,
				'featured' => $featured
			), true)) {
				$success = false;
			}
			
			// Remove languages, we are adding them back later
			if (!$metaModel->deleteLanguageMetaValues($valueId)) {
				$success = false;
			}
			
		// ...or add new?
		} else {
			$valueId = $metaModel->addMetaValue($groupId, $internalName, array(
				'display' => !$hidden,
				'default' => $default,
				'featured' => $featured,
				'sequence' => $order,
				'images' => $uploadedImages
			), true);
		}
		
		// Languages
		if ($valueId) {
			foreach ($languages as $language) {
				if (!$metaModel->addLanguageMetaValue($valueId, $language['code'], $language['name'], $language['description'], $language['keywords'], $language['pageTitle'], $language['listingTitle'], $language['slug'], $language['pageDescription'])) {
					$success = false;
				}
			}
		} else {
			$success = false;
		}
		
		// Flush cache
		$metaModel->flushGroup($groupId);
		
		// Back to value listing
		if ($success) {
			Messages::addMessage('Meta value <code>'.$internalName.'</code> saved', 'info');
		}
		return $this->view();
	}
	
	/**
	 *	Delete a meta value.
	 */
	public function deleteValue()
	{
		// Get request
		$metaValueId = Request::getInt('value');
		
		// Delete from model
		$metaModel = BluApplication::getModel('meta');
		$deleted = $metaModel->deleteMetaValue($metaValueId, array(), false);
		
		// Back to listing
		if ($deleted) {
			Messages::addMessage('Meta value deleted', 'info');
		}
		$this->view();
	}
	
	/**
	 *	Delete a meta value's image
	 *
	 *	@access public
	 */
	public function deleteValueImage()
	{
		// Get request
		$metaValueId = Request::getInt('value');
		$type = Request::getCmd('type', 'main');
		
		// Delete from model
		$metaModel = BluApplication::getModel('meta');
		$deleted = $metaModel->updateMetaValue($metaValueId, array(
			'images' => array($type => '')
		));
		
		// Back to listing
		if ($deleted) {
			Messages::addMessage('Meta value image deleted', 'info');
		}
		$this->view();
	}
	
	/**
	 *	View selectors
	 *
	 *	@access public
	 */
	public function selectors()
	{
		// Get all selectors
		$metaModel = BluApplication::getModel('meta');
		$selectors = $metaModel->getSelectors();
		
		// Template
		include(BLUPATH_BASE_TEMPLATES.'/meta/selectors.php');
	}
	
	/**
	 *	Add a new meta selector
	 *
	 *	@access public
	 */
	public function addSelector()
	{
		// Get available languages
		$availableLanguages = Language::getAvailableLanguages();
		
		// Get all meta values
		$metaModel = BluApplication::getModel('meta');
		$allGroups = $metaModel->getGroups();
		
		// Prepare empty meta selector
		$selectorId = '';
		$selectorValues = array();
		$selectorGroups = array();
		$internalName = '';
		$hidden = false;
		$order = 0;
		$images = array(
			'main' => false
		);
		$languages = array(
			array(
				'langCode' => '',
				'name' => '',
				'description' => '',
				'slug' => '',
				'keywords' => '',
				'pageTitle' => '',
				'listingTitle' => '',
				'pageDescription' => ''
			)
		);
		
		// Load template
		return include(BLUPATH_BASE_TEMPLATES.'/meta/edit_selector.php');
	}
	
	/**
	 *	Interface for adding a new meta selector
	 *
	 *	@access public
	 */
	public function editSelector()
	{
		// Get request
		$selectorId = Request::getInt('selector');
		
		// Get available languages
		$availableLanguages = Language::getAvailableLanguages();
		
		// Get all meta values
		$metaModel = BluApplication::getModel('meta');
		$allGroups = $metaModel->getGroups();
		
		// Get meta selector
		$languages = array();
		foreach ($availableLanguages as $langCode => $language) {
			$selector = $metaModel->getSelector($selectorId, false, $langCode);
			$languages[] = array(
				'langCode' => $langCode,
				'name' => $selector['name'],
				'description' => $selector['description'],
				'slug' => $selector['slug'],
				'keywords' => $selector['keywords'],
				'pageTitle' => $selector['pageTitle'],
				'listingTitle' => $selector['listingTitle'],
				'pageDescription' => $selector['pageDescription']
			);
		}
		$selectorValues = $selector['values'];
		$selectorGroups = $selector['groups'];
		$internalName = $selector['internalName'];
		$hidden = !$selector['display'];
		$order = $selector['sequence'];
		$images = array(
			'main' => $metaValue['imageName']
		);
		
		// Load template
		return include(BLUPATH_BASE_TEMPLATES.'/meta/edit_selector.php');
	}
	
	/**
	 *	Submit a new meta selector.
	 *
	 *	@access public
	 */
	public function saveSelector()
	{
		// Prepare
		$success = true;
		$availableLanguages = Language::getAvailableLanguages();
		$metaModel = BluApplication::getModel('meta');
		$selectorId = Request::getInt('selectorId');
		
		// Get request, and validation.
		if (!$internalName = Request::getString('internal')) {
			Messages::addMessage('Please fill in the internal name.', 'error');
			$success = false;
		}
		if (($languages = Request::getArray('languages')) && !empty($languages)) {
			foreach ($languages as $index => &$language) {
				
				// If all except "language code" are empty, just ignore.
				$empty = true;
				foreach ($language as $key => $languageField) {
					if ($key == 'code') {
						continue;
					}
					if (!empty($languageField)) {
						$empty = false;
					}
				}
				if ($empty) {
					unset($languages[$index]);
					continue;
				}
				
				// Check if has name
				if (empty($language['name'])) {
					Messages::addMessage('Please fill in the '.$availableLanguages[$language['code']].' name.', 'error');
					$success = false;
					break;
				}
				
				// Check if slug in use
				if (empty($language['slug'])) {
					$language['slug'] = Utility::slugify($language['name']);
				}
				if (!$metaModel->metaSelectorSlugAvailable($language['slug'], $language['code'], $selectorId)) {
					Messages::addMessage('The slug <code>'.$language['slug'].'</code> ('.$internalName.' - '.$availableLanguages[$language['code']].') is already in use.', 'error');
					$success = false;
					break;
				}
			}
			unset($language);
		} else {
			Messages::addMessage('Please enter at least one group of language-specific information.', 'error');
			$success = false;
		}
		if ((!$values = Request::getArray('values')) || empty($values)) {
			Messages::addMessage('Please enter at least one meta value.', 'error');
			$success = false;
		}
		if (!$success) {
			return false;
		}
		$hidden = Request::getBool('hidden');
		$order = Request::getInt('order');
		$images = Request::getFiles('images');
		$groups = Request::getArray('groups');
		
		// Save images, if any
		$uploadedImages = array();
		if (!empty($images)) {
			foreach ($images as $key => $image) {
				if (!Upload::isValid($image)) {
					continue;
				}
				if (!$hashedImageName = Upload::saveFile($image, BLUPATH_ASSETS.'/metaimages/')) {
					Messages::addMessage('Could not upload file <code>'.$image['name'].'</code>. Please try again.', 'error');
					continue;
				}
				$uploadedImages[$key] = $hashedImageName;
			}
		}
		
		// Do we update existing...?
		if ($selectorId) {
			if (!$metaModel->updateMetaSelector($selectorId, array(
				'internalName' => $internalName,
				'display' => !$hidden,
				'images' => $uploadedImages,
				'sequence' => $order
			), true)) {
				$success = false;
			}
			
			// Remove languages, we are adding them back later
			if (!$metaModel->deleteLanguageMetaSelectors($selectorId)) {
				$success = false;
			}
			
			// Remove value assignments, we are adding them back later
			if (!$metaModel->deleteMetaSelectorValues($selectorId, true)) {
				$success = false;
			}
			
			// Remove group assignments, we are adding them back later
			if (!$metaModel->deleteMetaSelectorGroups($selectorId, true)) {
				$success = false;
			}
			
		// ...or add new?
		} else {
			$selectorId = $metaModel->addMetaSelector($internalName, array(
				'display' => !$hidden,
				'images' => $uploadedImages,
				'sequence' => $order
			), true);
		}
		
		if ($selectorId && $success) {
			
			// Languages
			foreach ($languages as $language) {
				if (!$metaModel->addLanguageMetaSelector($selectorId, $language['code'], $language['name'], $language['description'], $language['keywords'], $language['pageTitle'], $language['listingTitle'], $language['slug'], $language['pageDescription'])) {
					$success = false;
				}
			}
			
			// Values
			foreach ($values as $groupId => $groupValues) {
				foreach ($groupValues as $value) {
					if (!$metaModel->addMetaSelectorValue($selectorId, $groupId, $value, true)) {
						$success = false;
					}
				}
			}
			
			// Groups
			if (!empty($groups)) {
				foreach ($groups as $groupId) {
					if (!$metaModel->addMetaSelectorGroup($selectorId, $groupId, 'selector_replace', true)) {
						$success = false;
					}
				}
			}
		} else {
			$success = false;
		}
		
		// Flush cache
		$metaModel->clearMetaSelectorCache($selectorId);
		//$metaModel->rebuildHierarchy(); // we do this in the GBoD already.
		
		// Back to value listing
		if ($success) {
			Messages::addMessage('Meta selector <code>'.$internalName.'</code> saved', 'info');
		}
		return $this->selectors();
	}
	
	/**
	 *	Delete a meta selector.
	 *
	 *	@access public
	 */
	public function deleteSelector()
	{
		// Get request
		$selectorId = Request::getInt('selector');
		
		// Delete from model
		$metaModel = BluApplication::getModel('meta');
		$deleted = $metaModel->deleteMetaSelector($selectorId, false);
		
		// Back to listing
		if ($deleted) {
			Messages::addMessage('Meta selector deleted', 'info');
		}
		return $this->selectors();
	}
	
	/**
	 *	Delete a meta selector's image
	 *
	 *	@access public
	 */
	public function deleteSelectorImage()
	{
		// Get request
		$metaSelectorId = Request::getInt('selector');
		
		// Delete from model
		$metaModel = BluApplication::getModel('meta');
		$deleted = $metaModel->updateMetaSelector($metaSelectorId, array(
			'images' => array($type => '')
		));
		
		// Back to listing
		if ($deleted) {
			Messages::addMessage('Meta selector image deleted', 'info');
		}
		$this->view();
	}
	
	/**
	 *	Push changes live
	 *
	 *	@access public
	 */
	public function purgeQueue()
	{
		// No queue to purge
		
		// Rebuild
		$metaModel = BluApplication::getModel('meta');
		$metaModel->rebuildCoreData();
		
		// Back to listing
		Messages::addMessage('Meta data rebuilt.', 'info');
		$this->view();
	}
}

?>
