<?php

/**
 * Sitemap Controller
 *
 * @package BluApplication
 * @subpackage FrontendControllers
 */
class Recipe4livingSitemapController extends ClientFrontendController
{
	/**
	 * Display sitemap
	 */
	public function view()
	{
		switch ($this->_doc->getFormat()) {
			case 'xml':
				// Machine-readable xml
				echo $this->_getXmlSitemap();
				break;
				
			default:
				// Human-readable
				$pathway[] = array(Template::text('links_sitemap'), '/sitemap');
				$this->_doc->setBreadcrumbs($pathway);
				$this->_doc->setTitle(Text::get('links_sitemap'));
				include(BLUPATH_TEMPLATES.'/sitemap/main.php');
				break;
		}
	}
	
	/**
	 *	Get XML sitemap
	 *
	 *	@access protected
	 *	@return string
	 */
	protected function _getXmlSitemap()
	{
		// Get language
		$language = BluApplication::getLanguage();
		$langCode = $language->getLanguageCode();
		
		// Load from cache
		$cache = BluApplication::getCache();
		$cacheKey = 'sitemap_xml_'.$langCode;
		$sitemap = $cache->get($cacheKey);
		if ($sitemap === false) {
			ob_start();
			
			// Get hierarchy
			$metaModel = BluApplication::getModel('meta');
			$hierarchy = $metaModel->getHierarchy();

			$metaModel->addHierarchyLinks($hierarchy);	// Until latest BluCommerce MetaModel is ported over, this will have to do.
			
			// Output sitemap
			include(BLUPATH_BASE_TEMPLATES.'/sitemap/main.php');
			
			$sitemap = ob_get_clean();
			
			// Give it three days
			$cache->set($cacheKey, $sitemap, 60 * 60 * 24 * 3);
		}
		
		// Return
		return $sitemap;
	}
	
	/**
	 *	Get HTML sitemap
	 *
	 *	@access protected
	 *	@return string
	 */
	protected function _getHtmlSitemap()
	{
		// Get language
		$language = BluApplication::getLanguage();
		$langCode = $language->getLanguageCode();
		
		// Load from cache
		$cache = BluApplication::getCache();
		$cacheKey = 'sitemap_html_'.$langCode;
		$sitemap = $cache->get($cacheKey);
		//if ($sitemap === false) 
		{
			ob_start();
			
			// Get hierarchy
			$metaModel = BluApplication::getModel('meta');
			$hierarchy = $metaModel->getHierarchy();

			$metaModel->addHierarchyLinks($hierarchy);      // Until latest BluCommerce MetaModel is ported over, this will have to do.
			
			// Output sitemap
			$this->_hierarchyListing($hierarchy, true, 'head');
			
			$sitemap = ob_get_clean();
			$cache->set($cacheKey, $sitemap);
		}
		
		// Return
		return $sitemap;
	}

	/**
	 *	Outputs a full nested listing of the site hierarchy
	 *
	 *	@access protected
	 *	@param array Hierarchy to output
	 *	@param bool Expand all subhierarchies
	 *	@param string Format for the top hierarchy level
	 *	@param bool Whether we are at the top level
	 *	@param array Parent filters
	 *	@param array Items displayed in the child (passed out to parent)
	 */
	protected function _hierarchyListing($hierarchy, $expandAll = false, $topLevelFormat = 'list', $topLevel = true, array $parentFilters = array(), array &$itemsAlreadyDisplayed = array(),$level = 0)
	{
		// Nothing 'ado.
		if (empty($hierarchy)) {
			return;
		}
		
		// Get model
		$itemsModel = BluApplication::getModel('items');
		
		// Determine format
		$format = $topLevel ? $topLevelFormat : 'list';
		
		// Let's output this baby
		if ($format == 'list') {
			echo '<ul class="sections">';
		}
		foreach ($hierarchy as $item) {
			
			// Not displayable, or no items
			if (!($item['display'] && $item['numItems'])) {
				continue;
			}
			
			// Prepare filters
			$filters = $parentFilters;
			if (empty($item['selector'])) {
				$filters[$item['groupId']][$item['id']] = $item['id'];
			} else {
				foreach ($item['values'] as $values) {
					foreach ($values as $value) {
						$filters[$value['groupId']][$value['id']] = $value['id'];
					}
				}
			}
			
			// Set css classes
			$css = array('parent', strtolower($item['slug']));
			if (!empty($item['active'])){
				$css[] = 'active';
			}
			if (!empty($item['values'])){
				$css[] = 'parent';
			}
			
			// Display
			if ($format == 'list') {
				echo '<li class="'.implode(' ', $css).'">';
			} elseif ($format == 'head') {
				$level = 0;
				echo '<h2>';
			}
			$image = ($format == 'head')?'expand.png':'bullet.png';
			if (!empty($item['values'])){
				echo '<a style="background: url('.SITEASSETURL.'/images/site/'.$image.') no-repeat 0 2px;padding-left: 10px;cursor: pointer;" class="expand" id="'.$level.$item['id'].'"></a>';			
			}
			echo '<a href="'.SITEURL.$item['link'].'">'.htmlspecialchars($item['name']).'</a>';
			if ($format == 'head') {
				echo '</h2>';
			}
			if (($expandAll || !empty($item['active']))) {
				
				// Keep a record of the items displayed in children
				$itemsDisplayedByChildren = array();
				
				// If not selector, show children
				if (empty($item['selector']) && !empty($item['values'])) {
					if (!empty($item['values'])){
						echo '<div class="'.$level.$item['id'].'" style="display:block" >';
					} 
					$this->_hierarchyListing($item['values'], $expandAll, $topLevelFormat, false, $filters, $itemsDisplayedByChildren,++$level);
					echo '</div>';
				}
				$itemsAlreadyDisplayed = array_merge($itemsAlreadyDisplayed, $itemsDisplayedByChildren);
				/* TOO MANY RECIPES, PAGE TAKES AGES TO LOAD, SO WE'RE DOING WITHOUT!
				// Show items not already shown by children
				$items = $itemsModel->getItems(null, null, null, $filters);
				$items = array_diff_key($items, array_flip($itemsDisplayedByChildren));
				if (!empty($items)) {
					echo '<ul class="items">';
					foreach ($items as $item) {
						$itemsAlreadyDisplayed[] = $item['id'];
						echo '<li><a href="'.SITEURL.$item['link'].'">'.$item['title'].'</a></li>';
					}
					echo '</ul>';
				}*/
			}
			if ($format == 'list') {
				echo '</li>';
			}
		}
		if ($format == 'list') {
			echo '</ul>';
		}
	}
	
	/**
	 *	Output hierarchy listing.
	 *
	 *	@access protected
	 *	@param array Meta value.
	 *	@param string Change frequency
	 *	@param double Internal priority
	 *	@param array Parent filters
	 */
	protected function _showItem($item, $frequency, $priority, array $filters = array())
	{
		// Already displayed items
		static $itemsAlreadyDisplayed = array();

		// Disabled items
		if (!empty($item['disabled'])) {
			return false;
		}
		
		// Show current item
		$link = SITEINSECUREURL.SITEURL.$item['link'];
		include(BLUPATH_BASE_TEMPLATES.'/sitemap/item.php');
		
		// Prepare filters
		if (empty($item['selector'])) {
			$filters[$item['groupId']][$item['id']] = $item['id'];
		} else {
			foreach ($item['values'] as $values) {
				foreach ($values as $value) {
					$filters[$value['groupId']][$value['id']] = $value['id'];
				}
			}
		}

		// Children
		if (empty($item['selector']) && !empty($item['values'])) {
			foreach ($item['values'] as $subItem) {
				$this->_showItem($subItem, 'monthly', 0.6, $filters);
			}
		}
return;	// TOO MANY RECIPES, PAGE TAKES AGES TO LOAD, SO WE'RE DOING WITHOUT!
		// Items
		$itemsModel = BluApplication::getModel('items');
		$items = $itemsModel->getItems(null, null, null, $filters);
		$items = array_diff_key($items, array_flip($itemsAlreadyDisplayed));
		if (!empty($items)) {
			foreach ($items as $item) {
				$itemsAlreadyDisplayed[] = $item['id'];
				
				// Display
				$link = SITEINSECUREURL.$item['link'];
				$frequency = 'weekly';
				$priority = 0.8;
				include(BLUPATH_BASE_TEMPLATES.'/sitemap/item.php');
			}
		}
	}
}

?>
