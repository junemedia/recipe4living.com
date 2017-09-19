<?php

/**
 *  Client Backend Controller
 *
 *  @package BluApplication
 *  @subpackage BackendControllers
 */
class ClientBackendController extends BackendController {
  /**
   *  Constructor
   *
   *  @access array Arguments
   */
  public function __construct($args) {
    parent::__construct($args);

    // Set top navigation
    $this->_setTopNav();
  }

  /**
   *  Set top navigation
   *
   *  @access protected
   */
  protected function _setTopNav() {
    $userModel = BluApplication::getModel('user');
    $user = $userModel->getCurrentUser();

    $nav = Array();

    $nav[] = Array(
      'name' => 'Articles',
      'link' => SITEURL.'/articles',
      'on' => Array('article_listing', 'article_details'),
      'children' => Array(
        FRONTENDSITEURL.'/articles/share' => 'Create article',
        SITEURL.'/articles?view=deleted' => 'Deleted articles'
      )
    );

    $nav[] = Array(
      'name' => 'Recipes',
      'link' => SITEURL.'/recipes',
      'on' => Array('recipe_listing', 'recipe_details'),
      'children' => Array(
        FRONTENDSITEURL.'/share' => 'Create recipe',
        SITEURL.'/recipes?view=deleted' => 'Deleted recipes',
        SITEURL.'/ingredients' => 'Ingredient Review Tool',
        SITEURL.'/ingredients/default_value_list' => 'Ingredient Database',
      )
    );

    $nav[] = Array(
      'name' => 'Users',
      'link' => SITEURL . '/users',
      'on' => Array('user_listing', 'user_details'),
      'children' => Array()
    );

    $boxNav = array(
      'name' => 'Content',
      'link' => SITEURL.'/box',
      'on' => Array('box', 'box_details'),
      'children' => Array()
    );
    $boxModel = BluApplication::getModel('boxout');
    $boxes = $boxModel->getBoxes();
    foreach ($boxes as $box) {
      $boxNav['children'][SITEURL.'/box/details/'.$box['id']] = $box['internalName'];
    }
    $nav[] = $boxNav;

    $nav[] = Array(
      'name' => 'Newsletters',
      'link' => SITEURL . '/newsletters',
      'on' => Array('newsletters'),
      'children' => Array(
        SITEURL.'/newsletters/daily'       => 'Daily',
        SITEURL.'/newsletters/singleserve' => 'Single Serving',
        SITEURL.'/newsletters/onepot'      => 'One Pot Wonders',
        SITEURL.'/newsletters/copycat'     => 'Copycat Classics',
        SITEURL.'/newsletters/quickeasy'   => 'Quick & Easy',
        SITEURL.'/newsletters/secondsend'  => 'Second Helping'
      )
    );

    $nav[] = Array(
      'name' => 'Config',
      'link' => SITEURL.'/config/edit/adminEmail',
      'on' => Array('config'),
      'children' => Array(
        SITEURL.'/config/edit/adminEmail' => 'Contact form recipients'
      )
    );

    if(isset($this->_menuSlug)) {
      $menu_slug = $this->_menuSlug;
    }
    else {
      $menu_slug = null;
    }

    ob_start();
    include(BLUPATH_TEMPLATES.'/site/topnav.php');
    $topnav = ob_get_contents();
    ob_end_clean();

    $this->_doc->setContents($topnav, 'topnav');
  }
}

?>
