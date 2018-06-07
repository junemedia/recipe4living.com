<?php

/**
 *  Recipe4living Routing Model
 *
 *  @package BluApplication
 *  @subpackage SharedModels
 */
class ClientRoutesModel extends RoutesModel
{
  /**
   *  Load routes mapping into object variable.
   *
   *  @access protected
   */
  protected function _loadRoutes()
  {
    // Load parent routes
    parent::_loadRoutes();

    // Landing listings
    $this->_routes['/^\/hints_tips(.*)$/'] = '/articles/hints_tips$1';
    $this->_routes['/^\/a_dash_of_fun(.*)$/'] = '/articles/a_dash_of_fun$1';
    $this->_routes['/^\/thinking_healthy\/(.+)$/'] = '/articles/thinking_healthy/$1';

    // Landing pages
    $landingPages = array(
      'hints_and_tips',
      'dash_of_fun',
      'thinking_healthy'
    );
    foreach ($landingPages as $task) {
      $this->_routes['/^\/'.$task.'(.*)$/'] = '/landing/'.$task.'$1';
    }

    // Old site redirects - these need to show up with .html, so needs to be internal redirect rather than HTTP redirect.
    $this->_routes['/^\/contestrules\.html$/'] = '/index/contestrules';
    $this->_routes['/^\/terms\.html$/'] = '/index/terms';
    $this->_routes['/^\/privacy\.html$/'] = '/index/privacy';

    // Static pages
    $staticPages = array(
      'contact',
      'add_to_address_book',
      'about',
      'links',
      'faq',
      'press',
      'review_program',
      'submission',
      'privacy',
      'terms',
      'abuse',
      'forums',
      'help',
      'product_tester'
    );
    foreach ($staticPages as $task) {
      $this->_routes['/^\/'.$task.'(.*)$/'] = '/index/'.$task.'$1';
    }
    $this->_routes['/^\/rss\/?$/'] = '/index/rss';
  }

  /**
   *  Google webmaster tools: route check file to index controller.
   *
   *  @access protected
   *  @param string Original URI
   *  @param bool Re-routed or not
   *  @return string Mapped URI
   */
  protected function _routeGoogleWebmaster($originalUri, &$routed = false)
  {
    // Do parent
    $mappedUri = parent::_routeGoogleWebmaster($originalUri, $routed);

    // Do further redirect
    if ($routed) {
      $mappedUri = '/searchengine/google';
    }

    // Return
    return $mappedUri;
  }
}

?>
