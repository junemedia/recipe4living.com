<?php

/**
 * Client-specific frontend end controller base class
 *
 * @package BluApplication
 * @subpackage FrontendControllers
 */
abstract class ClientFrontendController extends FrontendController
{
  /**
   *  Item view type
   *
   *  @access protected
   *  @var string
   */
  protected $_view = 'article_listing';

  /**
   *  Show search form?
   *
   *  @access protected
   *  @var bool
   */
  protected $_showSearch = false;

  /**
   *  Show all articles
   *
   *  @access protected
   *  @var bool
   */
  protected $_showAll = false;

  /**
   *  Current page number
   *
   *  @access protected
   *  @var int
   */
  protected $_page = 1;

  /**
   *  Enable browsing listings
   *
   *  @access protected
   *  @var bool
   */
  protected $_enableBrowse;

  /**
   *  Can have more than one search-term field
   *
   *  @access protected
   *  @var bool
   */
  protected $_enableRefineSearch;

  /**
   *  Include filters with JSON listings
   *
   *  @access protected
   *  @var bool
   */
  protected $_loadFilters;

  /**
   *  Constructor
   *
   *  @access public
   *  @param array Arguments
   */
  public function __construct($args)
  {
    parent::__construct($args);

    $recipesslug = 'recipes';
    $recipelinks = $this->_getLandingLinks($recipesslug);
    Template::set('recipelinks',$recipelinks);

    $articleslug = 'articles';
    $articlelinks = '';
    Template::set('articlelinks',$articlelinks);

  }

  public function _getLandingLinks($slug)
  {
    $metaModel = BluApplication::getModel('meta');
    $group = $metaModel->getGroup($metaModel->getGroupIdBySlug($slug));

    // Set title here as well
    Template::set('leftNavTitle', $group['name']);

    // Build links
    $links = array();
    if($group){
      foreach ($group['values'] as $category) {
        if($category['display']){
          $links['/'.$group['slug'].'/'.$category['slug']] = $category['name'];
        }
      }
    }
    // Return
    return $links;
  }

  protected function _advert ($type) {

    // Say no.
    if (!ADS) {
      return true;
    }

    switch ($type) {
    case 'WEBSITE_RIGHT_BANNER_1':
    case 'connatix_infeed':
      include(BLUPATH_TEMPLATES.'/site/ads/'.$type.'.php');
    break;

    default: break;
    }

  }

  /**
   *  Destructor
   *
   *  @access public
   */
  public function __destruct()
  {
    // Check whether can unsubscribe from newsletters
    $userModel = BluApplication::getModel('user');
    Template::set('subscribed', ($user = $userModel->getCurrentUser()) && $user['subscribed']);

    // Get total recipe count for footer.
    $itemsModel = BluApplication::getModel('items');
    $recipeCount = $itemsModel->getTotal('recipe');
    Template::set('recipeCount', $recipeCount);

    // Continue.
    parent::__destruct();
  }

  /**
   *  Load top navigation
   *
   *  @access public
   *  @param array Extra (overriding) variables
   */
  public function topnav(array $displayVars = array())
  {
    // Get models
    $userModel = BluApplication::getModel('user');
    Template::set('currentUser', $userModel->getCurrentUser());

    // Default topnav data (application details)
    Template::set(array(
      'top' => array_merge(array(
        'option' => BluApplication::getOption(),
        'task' => BluApplication::getTask(),
        'args' => BluApplication::getArgs(),
        'breadcrumbs' => $this->_doc->getBreadcrumbs()
      ), $displayVars)
    ));

    // Clean up variables
    unset($userModel);
    extract(Template::get('top'));

    // Load template
    include(BLUPATH_TEMPLATES.'/nav/top.php');
  }

  /**
   *  Left navigation
   *
   *  @access public
   *  @param array Links to display (key: link; value: title)
   */
  public function leftnav(array $links = array())
  {
    // Fallback
    if (empty($links)) {
      $links = $this->_getRecipeCategoryLinks();
    }

    // Load template
    include(BLUPATH_TEMPLATES.'/nav/left.php');
  }

  /**
   *  Get left navigation "Browse recipes" links.
   *
   *  @todo Do it properly
   *  @access public
   *  @return array Links to display.
   */
  protected function _getRecipeCategoryLinks()
  {
    // Get model
    $metaModel = BluApplication::getModel('meta');

    // Get LHS recipe categories' links
    $recipesGroupId = $metaModel->getGroupIdBySlug('recipes');
    $recipesGroup = $metaModel->getGroup($recipesGroupId);
    $links = array();
    foreach ($recipesGroup['values'] as $recipeCategory) {
      if($recipeCategory['display']){
        $links['/'.$recipeCategory['slug']] = $recipeCategory['name'];
      }
    }

    // Return
    return $links;
  }

  /**
   *  Get My Kitchen links.
   *
   *  @access public
   *  @return array Links to display
   */
  protected function _getMyKitchenLinks()
  {
    // Set links
    $links = array();
    $links['/account'] = 'Edit my details';
    $links['/account/recipe_box'] = 'Recipe box';
    $links['/account/my_recipes'] = 'My Recipes';
    $links['/cookbooks/favorites'] = 'Favorite Cookbooks';
    $links['/account/my_cookbooks'] = 'My Cookbooks';
    $links['/cookbooks/create'] = 'Create a Cookbook';
    $links['/account/messages'] = 'My Messages';


    // Return
    return $links;
  }

  /**
   *  Display items in a listing
   *
   *  @access protected
   *  @param array Items IDs to display (pre-sorted, pre-filtered)
   *  @param int Page number to show
   *  @param int Items per page
   *  @param string URL of controller-task callback
   *  @param string Search term
   *  @param string Search term refinement
   *  @param string Sort
   *  @param string Layout
   *  @param array Breadcrumbs
   *  @param string Document title
   *  @param string Listing title
   *  @param string Description
   */
  protected function _listItems($items, $page = 1, $limit = null, $listingBaseUrl = null, $searchTerm = null, $searchTermExtra = null, $sort = null, $layout = null, $pathway = null, $documentTitle = null, $listingTitle = null, $description = null,$iscategory = false)
  {
    // Get models
    $itemsModel = BluApplication::getModel('items');

    // Get the user, why not
    $userModel = BluApplication::getModel('user');
    $user = $userModel->getCurrentUser();

    // Get requested options
    $viewall = Request::getBool('viewall', false);
    $offset = ($page - 1) * $limit;
    $format = $this->_doc->getFormat();

    // Do some final filtering (by live flag)
    $items = $itemsModel->filterLiveItems($items);
    $numItems = count($items);

    // Store item IDs in session for browse links
    if ($this->_enableBrowse) {
      Session::set('browseItems', $items);
      Session::set('browseList', ($this->_showSearch || $this->_showAll) ? Text::get('item_browse_search_results') : $listingTitle);
    }

    // Set document title
    if ($documentTitle) {
      $this->_doc->setTitle($documentTitle);
    }

    // Get search options
    $showSearch = $this->_showSearch;
    $showSearchExtra = $this->_enableRefineSearch;
    $searchBaseUrl = $searchTerm ? '&amp;searchterm='.urlencode($searchTerm) : '';
    $searchExtraBaseUrl = $searchTermExtra ? '&amp;searchterm_extra='.urlencode($searchTermExtra) : '';

    // Add item details
    if (empty($items)) {

      // Add error message
      switch ($this->_view) {
        case 'article_listing':
          Messages::addMessage(Text::get('item_list_msg_no_items', array('itemTypePlural' => 'articles')), 'warn');
          break;

        case 'quicktip_listing':
          Messages::addMessage(Text::get('item_list_msg_no_items', array('itemTypePlural' => 'quicktips')), 'warn');
          break;

        case 'recipe_listing':
          Messages::addMessage(Text::get('item_list_msg_no_items', array('itemTypePlural' => 'recipes')), 'warn');
          break;

        case 'recipe_box':
          Messages::addMessage(Text::get('item_list_msg_recipebox_empty'), 'warn');
          break;

        case 'shopping_list':
          Messages::addMessage(Text::get('item_list_msg_shopping_list_empty'), 'warn');
          break;

        case 'my_recipes':
          Messages::addMessage(Text::get('item_list_msg_my_recipes_empty'), 'warn');
          break;

        case 'cookbook_details':
          $cookbook = $itemsModel->getCookbook($this->_cookbookId);
          $message = Text::get('cookbook_recipes_empty');
          if ($cookbook['canEdit']) {
            $message .= ' Visit the recipe and click on "Add to a cookbook" to add a recipe to this cookbook.';
            $message .= ' '.Text::get('cookbook_recipes_empty_suggest', array('link' => SITEURL.'/recipes'));
          }
          Messages::addMessage($message, 'warn');
          break;

        case 'blogs':
          Messages::addMessage(Text::get('item_list_msg_blogs_empty'), 'warn');
          break;
      }
    } else {

      // Only limit (don't offset) for rss feeds
      if ($limit) {
        if ($this->_view == 'recipe_rss') {
          $items = array_slice($items, 0, $limit, true);

        // Show all items *danger danger*
        } else if (!$viewall) {
          $items = array_slice($items, $offset, $limit, true);
        }
      }

      // Build
      $itemsModel->addDetails($items);
      foreach ($items as &$item) {

        // Modify link
        if (!ISBOT && $this->_enableBrowse && ($this->_view != 'recipe_rss')) {
          $item['link'] .= '?browse=1';
        }

        switch ($this->_view) {
          case 'cookbook_details':

            // Remove from cookbook link
            $cookbook = $itemsModel->getCookbook($this->_cookbookId);
            $item['removeLink'] = $itemsModel->getTaskLink($cookbook['link'], 'delete_recipe', $item['slug']);
            break;
        }
      }
      unset($item);
    }
    // Get pagination values
    $total = $numItems;
    if ($viewall) {
      $start = 1;
      $end = $total;
    } else {
      $start = $offset + 1;
      $end = min($offset + $limit, $total);
    }

    // Get base URLs for listing updates
    $qsSep = '?';
    $layoutBaseUrl = $listingBaseUrl.(strpos($listingBaseUrl, $qsSep) === false ? $qsSep : '&amp;').'sort='.$sort.$searchBaseUrl.$searchExtraBaseUrl.'&amp;page='.$page.'&amp;layout=';
    $paginationBaseUrl = $listingBaseUrl.(strpos($listingBaseUrl, $qsSep) === false ? $qsSep : '&amp;').'layout='.$layout.'&amp;sort='.$sort.$searchBaseUrl.$searchExtraBaseUrl.'&amp;page=';
    $searchExtraBaseUrl = $listingBaseUrl.(strpos($listingBaseUrl, $qsSep) === false ? $qsSep : '&amp;').'sort='.$sort.$searchBaseUrl.'&layout='.$layout;

    // Do pagination
    $pagination = Pagination::simple(array(
      'limit' => $limit,
      'total' => $total,
      'current' => $page,
      'url' => $paginationBaseUrl
    ));

    // Output - load template
    if ($format == 'json') {
      $response = array();
      $response['numItems'] = $numItems;
      $response['documentTitle'] = $this->_doc->getSiteTitle();

      // Meta groups (cut down, with availability)
      if ($this->_loadFilters) {
        $response['metaGroups'] = $this->_getMetaGroups();
      }

      ob_start();
    }

    switch ($this->_view) {
      case 'article_listing':
      case 'recipe_listing':
      case 'shopping_list':
      case 'recipe_box':
      case 'my_recipes':
        include(BLUPATH_TEMPLATES.'/articles/items/articles.php');
        break;

      case 'cookbook_details':
        include(BLUPATH_TEMPLATES.'/cookbooks/details/heading.php');
        include(BLUPATH_TEMPLATES.'/cookbooks/items/recipes.php');
        break;

      case 'blogs':
      case 'blog_listing':
        include(BLUPATH_TEMPLATES.'/blogs/items/blogs.php');
        break;

      case 'quicktip_listing':
        include(BLUPATH_TEMPLATES.'/quicktips/items/quicktips.php');
        break;

      case 'recipe_rss':
        $description = $this->_getDescription();
        $locale = 'en-us';  // Bodge
        include(BLUPATH_TEMPLATES.'/recipes/items/rss.php');
        break;

      case 'related_articles':
        include(BLUPATH_TEMPLATES.'/articles/items/related_articles.php');
        break;
      case 'related_articles_search':
        include(BLUPATH_TEMPLATES.'/articles/items/related_articles_search.php');
        break;
    }
    if ($format == 'json') {
      $response['items'] = ob_get_clean();

      echo json_encode($response);
    }
  }

  /**
   *  Display a list of itemgroups
   *
   *  @access protected
   *  @param array Itemgroups IDs to display (pre-sorted, pre-filtered)
   *  @param int Page number to show
   *  @param int Itemgroups per page
   *  @param string URL of controller-task callback
   *  @param string Search term
   *  @param string Search term refinement
   *  @param string Sort
   *  @param string Layout
   *  @param array Breadcrumbs
   *  @param string Document title
   *  @param string Listing title
   *  @param string Description
   */
  protected function _listItemGroups($itemGroups, $page = 1, $limit = null, $listingBaseUrl = null, $searchTerm = null, $searchTermExtra = null, $sort = null, $layout = null, $pathway = null, $documentTitle = null, $listingTitle = null, $description = null)
  {
    // Get models
    $itemsModel = BluApplication::getModel('items');
    $userModel = BluApplication::getModel('user');

    // Get user, if they're hanging around
    $user = $userModel->getCurrentUser();

    // Get requested options
    $viewall = ISBOT ? true : Request::getBool('viewall', false);
    $offset = ($page - 1) * $limit;
    $format = $this->_doc->getFormat();

    // Do some final filtering (by live flag)
    $itemGroups = $itemsModel->filterLiveItemGroups($itemGroups);
    $itemGroups = $itemsModel->filterPrivateItemGroups($itemGroups);
    $numItemGroups = count($itemGroups);

    // Set document title
    if ($documentTitle) {
      $this->_doc->setTitle($documentTitle);
    }

    // Get search options
    $showSearch = $this->_showSearch;
    $showSearchExtra = $this->_enableRefineSearch;
    $searchBaseUrl = $searchTerm ? '&amp;searchterm='.urlencode($searchTerm) : '';
    $searchExtraBaseUrl = $searchTermExtra ? '&amp;searchterm_extra='.urlencode($searchTermExtra) : '';

    // Add item details
    if (empty($itemGroups)) {

      // Add error message
      switch ($this->_view) {
        case 'cookbook_add_recipe':
          Messages::addMessage(Text::get('cookbooks_add_recipe_empty', array('link' => SITEURL.'/cookbooks/create')), 'warn');
          break;

        case 'cookbook_listing':
        case 'my_cookbooks':
          Messages::addMessage(Text::get('cookbooks_empty', array('link' => SITEURL.'/cookbooks')), 'warn');
          break;
      }
    } else {

      // Show all itemgroups *danger danger*
      if (!$viewall) {
        $itemGroups = array_slice($itemGroups, $offset, $limit, true);
      }

      // Build
      $itemGroups = array_flip($itemGroups);
      foreach ($itemGroups as $itemGroupId => &$itemGroup) {
        $itemGroup = $itemsModel->getItemGroup($itemGroupId);

        // Append link
        if (!ISBOT && $this->_enableBrowse) {
          $itemGroup['link'] .= '?browse=1';
        }

        // Replace link for "Add recipe to cookbook"
        if ($this->_view == 'cookbook_add_recipe') {
          $recipe = $itemsModel->getItem($this->_itemId);
          $itemGroup['link'] = $itemsModel->getTaskLink($itemGroup['link'], 'add_recipe', $recipe['slug']);
        }

        // Save cookbook against user
        $toggleFavourite = isset($user['saves']['cookbook'][$itemGroup['id']]) ? 'remove_favorite' : 'add_favorite';
        $itemGroup['favouriteLink'] = $itemsModel->getTaskLink($itemGroup['link'], $toggleFavourite);
      }
      unset($itemGroup);
    }

    // Get pagination values
    $total = $numItemGroups;
    if ($viewall) {
      $start = 1;
      $end = $total;
    } else {
      $start = $offset + 1;
      $end = min($offset + $limit, $total);
    }

    // Get base URLs for listing updates
    $qsSep = '?';
    $layoutBaseUrl = $listingBaseUrl.$qsSep.'sort='.$sort.$searchBaseUrl.$searchExtraBaseUrl.'&amp;page='.$page.'&amp;layout=';
    $paginationBaseUrl = $listingBaseUrl.$qsSep.'layout='.$layout.'&amp;sort='.$sort.$searchBaseUrl.$searchExtraBaseUrl.'&amp;page=';
    $searchExtraBaseUrl = $listingBaseUrl.$qsSep.'sort='.$sort.$searchBaseUrl.'&layout='.$layout;

    // Do pagination
    if ($limit) {
      $pagination = Pagination::simple(array(
        'limit' => $limit,
        'total' => $total,
        'current' => $page,
        'url' => $paginationBaseUrl
      ));
    }

    // Output - load template
    if ($format == 'json') {
      $response = array();
      $response['numItems'] = $numItemGroups;
      $response['documentTitle'] = $this->_doc->getSiteTitle();

      ob_start();
    }
    switch ($this->_view) {
      case 'cookbook_add_recipe':
      case 'cookbook_listing':
      case 'my_cookbooks':
        include(BLUPATH_TEMPLATES.'/cookbooks/items/heading.php');
        include(BLUPATH_TEMPLATES.'/cookbooks/items/cookbooks.php');
        break;
    }
    if ($format == 'json') {
      $response['items'] = ob_get_clean();

      echo json_encode($response);
    }
  }

  /**
   *  Get sort order
   *
   *  @access protected
   *  @return string
   */
  protected function _getSort()
  {
    static $sort;

    if (!isset($sort)) {
      if ($this->_showSearch) {
        $sort = 'relevance';
      } else {
        $sort = BluApplication::getSetting('defaultItemsSort', 'name_asc');
      }
      $sort = Request::getString('sort', $sort);
    }

    return $sort;
  }

  /**
   *  Get listing limit
   *
   *  @access protected
   *  @return int Limit
   */
  protected function _getLimit()
  {
    return BluApplication::getSetting('listingLength', 15);
  }

  /**
   *  Get layout
   *
   *  @access protected
   *  @return string
   */
  protected function _getLayout()
  {
    static $layout;

    if (!isset($layout)) {
      if (ISBOT) {
        $layout = 'grid';
      } else {
        $default = 'grid';
        $layout = Request::getString('layout', Session::get('layout', $default));
      }
    }

    return $layout;
  }

  /**
   *  Get breadcrumbs
   *
   *  @access protected
   *  @return array Breadcrumbs pathway
   */
  protected function _getBreadcrumbs()
  {
    static $breadcrumbs;

    // Build breadcrumbs
    if (!$breadcrumbs) {
      $breadcrumbs = array();
      if ($this->_showSearch) {
        $breadcrumbs[] = array('Search', '/search');
      } else {
        $metaModel = BluApplication::getModel('meta');
        $breadcrumbs = $metaModel->getBreadcrumbs();
        if ($this->_showAll || empty($breadcrumbs)) {
          $breadcrumbs[] = array($this->_getEmptyFilterTitle(), '/_all');
        }
      }
    }

    return $breadcrumbs;
  }

  /**
   *  Get title
   *
   *  @param string Title type
   *  @return string
   */
  protected function _getTitle($titleType)
  {
    static $title;

    // Build title
    if (empty($title[$titleType])) {
      if ($this->_showSearch) {
        if ($titleType == 'listingTitle') {
          $title[$titleType] = 'Search for &#145;'.Request::getString('searchterm').'&#146;';
        } else {
          $title[$titleType] = 'Search';
        }
      } else {
        $metaModel = BluApplication::getModel('meta');
        if ($metaModel->hasDisplayableFilters()) {
          if ($titleType == 'listingTitle') {
            $title[$titleType] = $metaModel->getListingTitle();
          } else {
            $title[$titleType] = $metaModel->getPageTitle();
          }
        }
        if (empty($title[$titleType])) {
          $title[$titleType] = $this->_getEmptyFilterTitle();
        }
      }
    }

    return $title[$titleType];
  }

  /**
   *  Get description
   *
   *  @access protected
   *  @return string
   */
  protected function _getDescription()
  {
    // Take from filters.
    $metaModel = BluApplication::getModel('meta');
    if ($description = $metaModel->getDescription()) {
      $description .= ' - ';
    }
    $description .= Text::get('listing_meta_description');

    // Return
    return $description;
  }

  /**
   * Get  meta groups with availability and status details
   *
   * @return array Meta groups
   */
  protected function _getMetaGroups()
  {
    $metaModel = BluApplication::getModel('meta');
    $filters = $metaModel->getFilters();

    $itemsModel = BluApplication::getModel('items');
    $items = $itemsModel->getItems();

    // Get meta groups, with availability and status details
    $metaGroups = $metaModel->getGroups();
    $this->_filterMeta($metaGroups);
    $metaModel->setAvailability($metaGroups, $items, $filters);

    // Add links
    $params = array();
    $searchTerm = Request::getString('searchterm');
    if ($searchTerm) {
      $params['searchterm'] = $searchTerm;
    }
    $metaModel->addLinks($metaGroups, $params);

    // Compact
    foreach ($metaGroups as $groupId => $metaGroup) {
      $values = array();
      if (!empty($metaGroup['values'])) {
        foreach ($metaGroup['values'] as $metaValueId => $metaValue) {
          $values[$metaValueId] = array(
            'numItems' => $metaValue['numItems'],
            'selected' => $metaValue['selected'],
            'link' => $metaValue['link']
          );
        }
      }
      $response['metaGroups'][$groupId] = array(
        'neverExclude' => $metaGroup['neverExclude'],
        'values' => $values
      );
    }

    // Return meta groups
    return $metaGroups;
  }

  /**
   *  Custom filtering of filters
   *
   *  @param &array Meta groups
   */
  protected function _filterMeta(&$metaGroups)
  {
    // Clear hidden meta groups
    foreach ($metaGroups as $groupId => $metaGroup) {
      if ($metaGroup['hidden']) {
        unset($metaGroups[$groupId]);
      }
    }
  }
}

?>
