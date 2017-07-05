<?php

/**
 * Slideshow Controller
 *
 * @package BluApplication
 * @subpackage FrontendControllers
 */
class Recipe4livingSlideshowsController extends ClientFrontendController
{
  /**
   * Display slideshow
   */
  public function view() {
    $this->_doc->setTitle('Slideshows');
    include(BLUPATH_TEMPLATES.'/slideshows/view.php');
  }

  /**
   * Display list of slideshows
   */
  public function slideshowlist() {
    $slideshowsModel = BluApplication::getModel('slideshows');

    $page = Request::getInt('page', 1);

    $limit = 9;
    $total = NULL;

    $slideshows = $slideshowsModel->getSlideshows($page, $limit, $total, true);
    if (!$slideshows) {
      return $this->_redirect('/');
    }

    $pagination = Pagination::simple(array(
      'limit' => $limit,
      'total' => $total,
      'current' => $page,
      'url' => '?page='
    ));

    switch ($this->_doc->getFormat()) {
      case 'json':
        ob_start();
        break;
    }
    include(BLUPATH_TEMPLATES.'/slideshows/slideshow_list.php');
    switch ($this->_doc->getFormat()) {
      case 'json':
        $response = array();
        $response['items'] = ob_get_clean();
        echo json_encode($response);
        break;
    }
  }

  /**
   * Display slideshow
   */
  public function slideshow() {
    $slideshowsModel = BluApplication::getModel('slideshows');

    $slideshowId = reset($this->_args);

    $slideshow = $slideshowsModel->getSlideshow($slideshowId);
    if (!$slideshow) {
      return $this->_redirect('/slideshows');
    }

    $this->_doc->setTitle($slideshow['title']);
    include(BLUPATH_TEMPLATES.'/slideshows/slideshow.php');
  }

  /**
   * Display slideshow item
   */
  public function slideshowitem() {
    $slideshowsModel = BluApplication::getModel('slideshows');

    $slideshowId = reset($this->_args);
    $page = Request::getInt('page', 1);

    $limit = 1;

    $slideshowItems = $slideshowsModel->getSlideshowItems($slideshowId);
    $slideshowItem = $slideshowItems[$page-1];

    $total = count($slideshowItems);

    $pagination = Pagination::simple(array(
      'limit' => $limit,
      'total' => $total,
      'current' => $page,
      'url' => '?page='
    ));

    switch ($this->_doc->getFormat()) {
      case 'json':
        ob_start();
        break;
    }
    include(BLUPATH_TEMPLATES.'/slideshows/slideshow_item.php');
    switch ($this->_doc->getFormat()) {
      case 'json':
        $response = array();
        $response['items'] = ob_get_clean();
        echo json_encode($response);
        break;
    }
  }
}

?>
