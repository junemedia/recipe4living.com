<?php

/**
 * Index Controller
 *
 * @package BluApplication
 * @subpackage FrontendControllers
 */
class Recipe4livingIndexController extends ClientFrontendController
{

  /**
   *  Display home page
   *
   *  @access public
   */
  public function view()
  {
    // Get featured recipes with images
    $itemsModel = BluApplication::getModel('items');

    // Get recently added recipes with images
    $recentlyAddedRecipes = $itemsModel->getRecentRecipes();
    shuffle($recentlyAddedRecipes);
    $recentlyAddedRecipes = array_slice(($recentlyAddedRecipes),0,10);

    // Get blog post
    $blogsModel = BluApplication::getModel('blogs');
    $chewonthatBlog = $blogsModel->getChewonthatBlog(0, 1);

    // Get popular search terms
    $serachTermsModel = BluApplication::getModel('searchterms');
    //$popularSearchTerms = $serachTermsModel->getPopularSearchTerms();

    // Number of chefs in the kitchen
    $userModel = BluApplication::getModel('user');
    $latestUserCount = $userModel->getLatestUserCount();
    // Number of recipes added in the last 7 days
    $latestRecipeCount = $itemsModel->getLatestRecipeCount();

    // Polls
    $pollsModel = BluApplication::getModel('polls');
    $polls = $pollsModel->getPolls();
    $pollVoteUrl = '/index/vote';

    // Slideshows
    $slideshowsModel = BluApplication::getModel('slideshows');
    $total = null;
    $slideshows = $slideshowsModel->getSlideshows(1,10,$total,true,true);

    // Display popup?
    $_noPopup = Request::getBool('nopopup');
    $_cookieSet = Request::getBool('indexPopupDisplayed', false, 'cookie');
    Template::set('showPopup', !DEBUG && !$_noPopup && !$_cookieSet);
    if (!$_cookieSet) {
      Request::setCookie('indexPopupDisplayed', true, 60 * 60 * 24 * 365);
    }

    // Set document title
    $this->_doc->setTitle('Easy Recipes, Cooking Tips, and Menus');

    // Load page template
    include (BLUPATH_TEMPLATES.'/index/landing.php');
  }

  /**
   *  Contact page
   *
   *  @access public
   */
  public function contact()
  {
    // Set up prefilled text
    $subject = Request::getString('subject');
    $email = Request::getString('email');
    $name = Request::getString('name');
    $comment = Request::getString('comment');

    // Display
    $this->_doc->setTitle('Contact Us');
    include (BLUPATH_TEMPLATES.'/static/contact.php');
  }

  /**
   *  Add to address book page
   *
   *  @access public
   */
  public function add_to_address_book()
  {
    $this->_doc->setTitle('Add Us To Your Address Book');
    include (BLUPATH_TEMPLATES.'/static/add_to_address_book.php');
  }

  /**
   *  Contact submission
   *
   *  @access public
   */
  public function contact_send()
  {
    // Validate
    $errors = false;
    $requireCaptcha = true;

    // Get data from request
    $contactFields = BluApplication::getSetting('contactFields');
    foreach ($contactFields as $v) {
      $vars[$v] = $$v = Request::getString($v);
    }
    $captcha = Request::getString('captcha');

    // Check required fields
    $requiredFields = BluApplication::getSetting('contactRequiredFields');
    $missingRequired = false;
    foreach ($requiredFields as $v) {
      if (!$vars[$v]) {
        $missingRequired = true;
        break;
      }
    }

    // Missing required field?
    if ($missingRequired) {
      Messages::addMessage(Text::get('global_msg_complete_required_fields'), 'error', 'contact');
      $errors = true;

    // Check for valid e-mail address
    } elseif (!Email::isEmailAddress($email)) {
      Messages::addMessage(Text::get('global_msg_enter_valid_email'), 'error', 'contact');
      $errors = true;

    // Check captcha
    } elseif ($requireCaptcha && !Captcha::checkCode($captcha)) {
      Messages::addMessage(Text::get('global_captcha_msg_enter'), 'error', 'contact');
      $errors = true;
    }
    if ($errors) {
      return $this->_showMessages('contact', 'contact', 'contact');
    }

    // Get recipient mail
    $toEmail = (array) BluApplication::getSetting('adminEmail');
//    $toEmail[] = 'info@blubolt.com';
    $toName = BluApplication::getSetting('storeName');

    // Build replacement vars array
    $vars['date'] = date('l d/m/y').' at '.date('H:i');
    foreach ($vars as $v){
      if ($v == '') {
        $v = '[none]';
      }
    }

    // Get friendly reply-to name and email
    $replyEmail = $vars['email'];
    if (isset($vars['name'])) {
      $replyName = $vars['name'];
    } elseif (isset($vars['firstName']) && isset($vars['lastName'])) {
      $replyName = $vars['firstName'].' '.$vars['lastName'];
    } else {
      $replyName = $vars['email'];
    }

    // Convert comment to HTML
    if (isset($vars['comment'])) {
      $vars['comment'] = nl2br($vars['comment']);
    }

    // Do some jigging about with the subject
    switch ($vars['subject']) {
      case 'feedback':
        $vars['subject'] = 'Ideas to improve Recipe4Living';
        break;

      case 'support':
        $vars['subject'] = 'Website Support & Help';
        break;

      case 'press':
        $vars['subject'] = 'Press';
        break;

      case 'bizdev':
        $vars['subject'] = 'Business Development';
        break;

      default:
        $vars['subject'] = '(no subject)';
        break;
    }

    // Send the email(s)
    $emailMsg = new Email();
    $emailMsg->setReplyTo($replyEmail, $replyName);
    $sent = array();
    foreach ($toEmail as $email) {
      $sent[] = $emailMsg->quickSend($email, $toName, 'Contact request', 'contact', $vars);
    }

    // Oops, something went wrong sending the mail
    if (in_array(false, $sent)) {
      Messages::addMessage(Text::get('contact_msg_fail'), 'error', 'contact');
      return $this->_showMessages('contact', 'contact', 'contact');
    }

    // Success!
    Messages::addMessage(Text::get('contact_msg_success'), 'info', 'contact');
    return $this->_showMessages('contact', 'contact', 'contact', true);
  }

  /**
   *  About page
   *
   *  @access public
   */
  public function about()
  {
    $boxModel = BluApplication::getModel('boxout');
    $box = $boxModel->getBoxBySlug('about_us');
    include (BLUPATH_TEMPLATES.'/box/about_us.php');
  }

  /**
   *  Links page
   *
   *  @access public
   */
  public function links()
  {
    include (BLUPATH_TEMPLATES.'/static/links.php');
  }

  /**
   *  Press page
   *
   *  @access public
   */
  public function press()
  {
    // Get models
    $boxModel = BluApplication::getModel('boxout');

    $this->_doc->setTitle('Press Releases');
    include (BLUPATH_TEMPLATES.'/static/press.php');
  }

  /**
   *  Product Review Program page
   *
   *  @access public
   */
  public function review_program()
  {
    $this->_doc->setTitle('Product Review Program');
    include (BLUPATH_TEMPLATES.'/static/review_program.php');
  }

  /**
   *  RSS page
   *
   *  @access public
   */
  public function rss()
  {
    include (BLUPATH_TEMPLATES.'/static/rss.php');
  }

  /**
   *  Community page
   *
   *  @access public
   */
  public function community()
  {
    echo 'this is the community page';
  }

  /**
   *  Privacy page
   *
   *  @access public
   */
  public function privacy()
  {
    $this->_doc->setTitle('Privacy');
    include (BLUPATH_TEMPLATES.'/static/privacy.php');
  }

  /**
   *  Terms page
   *
   *  @access public
   */
  public function terms()
  {
    $this->_doc->setTitle('Terms of Use');
    $format = $this->_doc->getFormat();
    include (BLUPATH_TEMPLATES.'/static/terms.php');
  }

  /**
   *  Contest rules page
   *
   *  @access public
   */
  public function contestrules()
  {
    include (BLUPATH_TEMPLATES.'/static/contestrules.php');
  }


  /**
   *  A Holiday Contest rules page
   *
   *  @access public
   */
  public function holidaycontestrules()
  {
    include (BLUPATH_TEMPLATES.'/static/holidaycontestrules.php');
  }

  /**
   *  Abuse page
   *
   *  @access public
   */
  public function abuse()
  {
    echo 'this is the abuse page';
  }

  /**
   *  Forums page
   *
   *  @access public
   */
  public function forums()
  {
    include (BLUPATH_TEMPLATES.'/static/forums.php');
  }

  /**
   *  Help page
   *
   *  @access public
   */
  public function help()
  {
    include (BLUPATH_TEMPLATES.'/static/help.php');
  }

  /**
   *  Product tester page
   *
   *  @access public
   */
  public function product_tester()
  {
    include (BLUPATH_TEMPLATES.'/static/product_tester.php');
  }

  /**
   *  Left navigation
   *
   *  @access public
   *  @param array Links
   */
  public function leftnav(array $links = array())
  {
    return parent::leftnav(array_merge($this->_getRecipeCategoryLinks(), $links));
  }

    /**
   *  open advertiser link in footer to links.php
   *
   */
  public function advertiser() {
  Template::set('advertiser', true);
  include (BLUPATH_TEMPLATES.'/static/links.php');
}

  /**
   *  Display info popup
   *
   *  @access public
   */
  public function info()
  {
    // Get template
    $template = BLUPATH_TEMPLATES.'/static/info/'.implode('/', $this->_args).'.php';
    if (!file_exists($template)) {
      return false;
    }

    // Load page template
    include($template);
  }

  /**
   *  Mailing list signup
   *
   *  @access public
   */
  public function maillist_signup()
  {
    // Get data from request
    $name = Request::getString('name');
    $nameDelimPos = strpos($name, ' ');
    $firstName = trim(substr($name, 0, $nameDelimPos));
    $lastName = trim(substr($name, $nameDelimPos));
    $city = Request::getString('city');
    $email = Request::getString('email');
    $mailLists = Request::getVar('maillists');
    if (!empty($mailLists)) {
      $mailLists = array_keys($mailLists);
    }
    $mailCustom = Request::getVar('custom');

    // Validate
    if (!Email::isEmailAddress($email)) {
      Messages::addMessage(Text::get('global_msg_enter_valid_email'), 'error', 'maillist_signup');
      return $this->_showMessages('maillist_form','maillist_form', 'maillist_signup', false);
    }

    // Add to mailing list
    /* @var $maillistPlugin Maillist */
    $maillistPlugin = BluApplication::getDefaultPlugin('maillist');
    $maillistPlugin->subscribeRecipient($email, $firstName, $lastName, $mailLists, null, $mailCustom);
    Messages::addMessage(Text::get('site_msg_maillist_signup'), 'info','maillist_signup');

    // Show confirmation
    return $this->_showMessages('maillist_form','maillist_form', 'maillist_signup', true);
  }

  /**
   *  Mail list form
   *
   *  @access public
   */
  public function maillist_form()
  {
    // Get data from request
    $name = Request::getString('name');
    $email = Request::getString('email');
    $metaModel = BluApplication::getModel('meta');
    $chosenMailLists = Request::getVar('maillists');
    $mailCustom = Request::getVar('mailcustom');

    // Get mailing list details
    /* @var $maillistPlugin Maillist */
    $maillistPlugin = BluApplication::getDefaultPlugin('maillist');
    $mailSubscription = $maillistPlugin->getRecipient($email);
    $mailLists = $maillistPlugin->getMailingLists($mailSubscription);
    foreach ($mailLists as $listId => &$list) {
      if (isset($chosenMailLists[$listId])) {
        $list['status'] = 'subscribe';
      }
    }
    unset($list);

    // Set document meta
    $this->_doc->setTitle(Template::text('site_newsletter_title'));

    // Show form
    include (BLUPATH_TEMPLATES.'/site/newsletterform.php');
  }

  /**
   *  Unsubscribe from the mailing lists
   *
   *  @access public
   */
  public function maillist_unsubscribe()
  {
    include(BLUPATH_TEMPLATES.'/site/unsubscribe.php');
  }

  /**
   *  Show form
   *
   *  @access protected
   */
  protected function _unsubscribe()
  {
    // Set document meta
    $this->_doc->setTitle(Text::get('site_newsletter_unsubscribe'));

    // Show form
    include(BLUPATH_TEMPLATES.'/site/unsubscribeform.php');
  }

  /**
   *  Unsubscribe from the mailing lists
   *
   *  @access public
   */
  public function maillist_unsubscribe_save()
  {
    // Has credentials?
    if (!$user = $this->_requireUser()) {
      $url = '/maillist_unsubscribe_save';
      $url = '/account/login?redirect='.base64_encode($url);
      return $this->_redirect($url, Text::get('site_newsletter_unsubscribe_login'), 'warn');

    // Has permissions?
    } else if ($user['email'] != Request::getString('email')) {
      Messages::addMessage(Text::get('site_newsletter_unsubscribe_fail_perm'), 'error');

    // Unsubscribe
    } else {
      $maillistPlugin = BluApplication::getDefaultPlugin('maillist');
      $maillistPlugin->unsubscribeRecipient($user['email']);
      Messages::addMessage(Text::get('site_newsletter_unsubscribe_success'), 'info');
    }

    // Redirect
    return $this->_showMessages('_unsubscribe', '_unsubscribe');
  }


  /**
   *  giveaway page
   *
   *  @access public
   */
  public function giveaway()
  {
    //Template::set('giveaway', true);
    //$this->_doc->setTitle("Contest - Recipe4Living.com");
    //include(BLUPATH_TEMPLATES.'/static/giveaway.php');
    header('Location: http://win.recipe4living.com');
  }


  /**
   *  newsletters page
   *
   *  @access public
   */
  public function newsletters()
  {
    Template::set('newsletters', true);
    include(BLUPATH_TEMPLATES.'/static/newsletters.php');
  }

  /**
   *  Paula deen page
   *
   *  @access public
   */
  public function paula_deen()
  {
    Template::set('paula_deen', true);
    include(BLUPATH_TEMPLATES.'/static/paula_deen.php');
  }



  /**
   *  Meebo page
   *
   *  @access public
   */
  public function meebo()
  {
    Template::set('meebo', true);
    include(BLUPATH_TEMPLATES.'/static/meebo.php');
  }



  /**
   *  Coupons page
   *
   *  @access public
   */
  public function coupons()
  {
    Template::set('coupons', true);
    include(BLUPATH_TEMPLATES.'/static/coupons.php');
  }



  /**
   *  subctr page
   *
   *  @access public
   */
  public function subctr()
  {
    Template::set('subctr', true);
    include(BLUPATH_TEMPLATES.'/static/subctr.php');
  }



  /**
   *  store page
   *
   *  @access public
   */
  public function store()
  {
    Template::set('store', true);
    include(BLUPATH_TEMPLATES.'/static/store.php');
  }




  /**
   *  Unsub page
   *
   *  @access public
   */
  public function unsub()
  {
    Template::set('unsub', true);
    include(BLUPATH_TEMPLATES.'/static/unsub.php');
  }






  /**
   *  NEW unsubscribe page
   *
   *  @access public
   */
  public function unsubscribe()
  {
    Template::set('unsubscribe', true);
    $this->_doc->setTitle("Recipe4Living.com Newsletter Unsubscribe");
    include(BLUPATH_TEMPLATES.'/static/unsubscribe.php');
  }





  /**
   *  Test page
   *
   *  @access public
   */
  public function test()
  {
    Template::set('test', true);
    include(BLUPATH_TEMPLATES.'/static/test.php');
  }



  /**
   *  soloconf page
   *
   *  @access public
   */
  public function soloconf()
  {
    Template::set('soloconf', true);
    include(BLUPATH_TEMPLATES.'/static/soloconf.php');
  }



  /**
   *  faq page
   *
   *  @access public
   */
  public function faq()
  {
    Template::set('faq', true);
    include(BLUPATH_TEMPLATES.'/static/faq.php');
  }




  /**
   *  Poll voting
   *
   *  @access public
   */
  public function vote()
  {
    // Get models
    $pollsModel = BluApplication::getModel('polls');

    $pollId = Request::getInt('pollId');
    $statementId = $selectedStatementId = Request::getInt('statement');

    $format = $this->_doc->getFormat();

    // Validation
    $poll = $pollsModel->getPoll($pollId);
    if(!$poll || !$poll['live']) {
      return $this->_redirect(SITEURL);
    }
    $statement = $pollsModel->getPollStatement($statementId);
    if(!$statement || $statement['pollId']!=$pollId) {
      return $this->_redirect(SITEURL);
    }

    // Add vote
    $result = $pollsModel->addVote($pollId, $statementId);
    // Refresh results
    $pollsModel->updatePollResults($pollId);

    if ($format == 'json') {
      if($result) {
        $pollResults = $pollsModel->getPollResults($pollId);
        $poll = $pollsModel->getPoll($pollId);
        $pollVoteUrl = '/index/vote';
        ob_start();
        include (BLUPATH_TEMPLATES.'/polls/items/poll.php');
        $html = ob_get_contents();
        ob_end_clean();
        $response = array();
        $response = array('form'=>$html);
      }
      else {
        $response = array('messages'=>'Sorry, we could not process your vote. Please try again later.');
      }
      echo json_encode($response);
      exit(0);
    }
    else {
      return $this->_redirect(SITEURL);
    }
  }
}

?>
