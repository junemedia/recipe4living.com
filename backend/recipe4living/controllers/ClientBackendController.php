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
      'name' => 'Config',
      'link' => SITEURL.'/config/edit/adminEmail',
      'on' => Array('config'),
      'children' => Array(
        SITEURL.'/config/edit/adminEmail' => 'Contact form recipients'
      )
    );

    /*
    $nav[] = Array(
      'name' => 'Categories',
      'link' => SITEURL . '/categories',
      'on' => Array('category_listing', 'category'),
      'children' => Array(
        SITEURL.'/articles/new_list' => 'category - live',
      )
    );

    $nav[] = Array(
      'name' => 'Groups',
      'link' => SITEURL . '/meta',
      'on' => Array('meta', 'meta_add_value', 'meta_edit_value'),
      'children' => Array()
    );

    $nav[] = Array(
      'name' => 'Blogs',
      'link' => SITEURL.'/blogs',
      'on' => Array('blog_listing', 'blog_details'),
      'children' => Array(
        FRONTENDSITEURL.'/blogs/share' => 'Create blog post',
        SITEURL.'/blogs?view=deleted' => 'Deleted blog posts'
      )
    );

    $nav[] = Array(
      'name' => 'Tips',
      'link' => SITEURL.'/quicktips',
      'on' => Array('quicktip_listing', 'quicktip_details'),
      'children' => Array(
        SITEURL.'/quicktips/edit' => 'Create tip'
      )
    );

    $nav[] = Array(
      'name' => 'Reviews',
      'link' => SITEURL . '/comments',
      'on' => Array('comment_listing', 'comment_details'),
      'children' => Array()
    );

    $nav[] = Array(
      'name' => 'Search Terms',
      'link' => SITEURL . '/searchterms',
      'on' => Array('searchterm_listing'),
      'children' => Array()
    );

    $nav[] = Array(
      'name' => 'Text',
      'link' => SITEURL . '/languages',
      'on' => Array('languages'),
      'children' => Array(
      )
    );

    $nav[] = Array(
      'name' => 'Images',
      'link' => SITEURL . '/assets',
      'on' => Array('images'),
      'children' => Array()
    );

    $nav[] = Array(
      'name' => 'Files',
      'link' => SITEURL . '/assets/pdf',
      'on' => Array('files'),
      'children' => Array()
    );

    $nav[] = Array(
      'name' => 'Article History',
      'link' => SITEURL . '/articlehistory',
      'on' => Array('article_history'),
      'children' => Array()
    );

    $nav[] = Array(
      'name' => 'Polls',
      'link' => SITEURL . '/polls',
      'on' => Array('polls'),
      'children' => Array(
        SITEURL.'/polls/poll' => 'Create poll'
      )
    );

    $nav[] = Array(
      'name' => 'Slideshows',
      'link' => SITEURL . '/slideshows',
      'on' => Array('slideshows'),
      'children' => Array(
        SITEURL.'/slideshows/slideshow' => 'Create slideshow'
      )
    );
    */

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
