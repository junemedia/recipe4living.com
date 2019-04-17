<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="x-ua-compatible" content="IE=edge" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

  <?php if (strstr($_SERVER['REQUEST_URI'], '/links') ) { ?>
  <title>Advertise With Us</title>
  <?php } else { ?>
  <title><?= $title; ?></title>
  <?php } ?>

  <?php if (strstr($_SERVER['REQUEST_URI'],'subctr')) { ?>
  <meta name="keywords" content="recipe newsletters, free recipe newsletter, recipe newsletter, daily recipe newsletter, crockpot recipe newsletter, slow cooker recipe newsletter, free slow cooker recipes, free crockpot recipes, free crockpot recipe newsletter budget cooking recipes, budget cooking newsletter,party recipes and tips, party recipe newsletter, quick and easy recipe newsletter, quick and easy recipes" />
  <?php } else { ?>
  <meta name="keywords" content="<?= $keywords ? htmlentities($keywords, ENT_QUOTES, 'utf-8') : 'Recipe, Recipes, Easy Recipes, Food Ideas, Quick Recipes, Healthy Recipes, Cooking Tips, Cooking Ideas, Food Tips,'; ?>" />
  <?php } ?>

  <meta name="description" content="<?= $description ? htmlentities($description, ENT_QUOTES, 'utf-8') : 'Looking for recipes and cooking tips? Recipe4Living has a recipe for every occasion. Whether you are looking for a quick and easy recipe, healthy recipes, or food ideas & tips for a special occasion, we have you covered.' ?>" />
  <meta name="author" content="Recipe4Living, Recipe4Living.com" />
  <meta name="verify-v1" content="MdhXUubKMGRn6vL5WSVMEXeKt6D4mMrULy9MG+6+Zf8=" />

  <?php if ($format !='print') {?>
  <link rel="stylesheet" href="<?= SITEASSETURL; ?>/css/site.css,nav.css,index.css,articles.css,landing.css,recipes.css,account.css,static.css,stickywin.css?v=a80cb" type="text/css" media="screen" />
  <? } ?>

  <?php if ($format == 'print') {?>
  <link rel="stylesheet" href="<?= SITEASSETURL; ?>/css/print.css?v=4" type="text/css"<?php if ($format != 'print') { ?> media="print"<?php } ?> />
  <?php }?>

  <link type="text/css" href="/frontend/recipe4living/css/print.css?v=4" media="print" rel="stylesheet">
  <!--[if IE 6]><link href="<?= SITEASSETURL; ?>/css/ie6.css?v=4" rel="stylesheet" type="text/css" /><![endif]-->
  <!--[if IE 7]><link href="<?= SITEASSETURL; ?>/css/ie7.css?v=4" rel="stylesheet" type="text/css" /><![endif]-->
  <link rel="shortcut icon" href="<?= SITEASSETURL; ?>/images/favicon.ico" type="image/vnd.microsoft.icon" />
  <link rel="icon" href="<?= SITEASSETURL; ?>/images/favicon.ico" type="image/vnd.microsoft.icon" />

  <?php if (Template::get('rssUrl')) { ?>
  <link href="<?php echo SITEINSECUREURL . htmlspecialchars(Template::get('rssUrl')); ?>" rel="alternate" type="application/rss+xml" title="<?= BluApplication::getSetting('storeName'); ?> | <?= Template::get('rssTitle', 'Latest Recipes'); ?>" />
  <?php } ?>


  <?php if ($format != 'print') { ?>
  <script type="text/javascript">
    /* Define global static variables. */
    DEBUG = <?= DEBUG ? 'true' : 'false' ?>;
    SITEURL = '<?= SITEURL ?>';
    SITESECUREURL = '<?= SITESECUREURL ?>';
    SITEINSECUREURL = '<?= SITEINSECUREURL ?>';
    ASSETURL = '<?= ASSETURL ?>';
    COREASSETURL = '<?= COREASSETURL ?>';
    SITEASSETURL = '<?= SITEASSETURL ?>';
  </script>

  <?php if ((substr($_SERVER['REQUEST_URI'],0,9) != '/recipes/' && !strstr($_SERVER['REQUEST_URI'],'.htm'))) { ?>
  <script type="text/javascript" src="<?= COREASSETURL ?>/js/mootoolsCore.js,mootoolsMore.js"></script>
  <?php } ?>
  <script type="text/javascript" src="<?= COREASSETURL ?>/js/StickyWin.js,Interface.js,Nav.js,HistoryManager.js,Forms.js,BrowseArea.js,Autocompleter.js,Milkbox.js,Wizard.js,sifr.js,Slideshow.js,Articles.js?ver=6wnp"></script>

  <?php include BLUPATH_TEMPLATES.'/site/ads/adthrive_js.php'; ?>


  <script type="text/javascript" src="<?= COREASSETURL ?>/js/jquery.min.js,jquery.fancybox-1.3.4.pack.js,jquery.cookie.js"></script>

  <script type="text/javascript">
    var R4LSignUpDhtml = jQuery.noConflict();
    var R4LDhtml = jQuery.noConflict();
  </script>

  <?php if (Template::get('tinyMce')) { ?>
  <script type="text/javascript" src="<?= COREASSETURL ?>/plugins/tiny_mce/tiny_mce.js"></script>
  <script type="text/javascript" src="<?= COREASSETURL; ?>/js/TinyMCE.js"></script>
  <?php } ?>

  <?php
    // Page-specific script includes
    echo $includeScript;
    // Generic bits of header
    echo $genericHeader;
  ?>

  <script type="text/javascript">
    window.addEvent('domready', function () {

      /* Init history manager */
      HistoryManager.initialize();

      /* Get reference to body content */
      var bodyContent = $(document.body);

      /* Top nav */
      var topNav = new TopNav($('nav-top'));

      /* Standard forms */
      bodyContent.getElements('div.standardform, fieldset.standardform').each(function(formcontainer) {
        var standardForm = new StandardForm(formcontainer);
      });

      <?= $domreadyScript; ?>

      /* Popups */
      var infoPopups = new InfoPopups(bodyContent.getElements('a.info-popup'));
      var printPopups = new AssetPopups(bodyContent.getElements('a.print-popup'), {
        windowKey: 'recipe4living_print_popup'
      });

      /* Page scroll */
      var pageScroll = new PageScroll(bodyContent.getElements('a.scroll'), {
        wheelStops: false
      });

      /* Start history manager */
      HistoryManager.start();

      /* Input over text */
      $$('input.simpletext, textarea.simpletext').each(function(input) {
        new InputText(input);
      });

    });

    window.addEvent('load', function() {
      <?= $loadScript; ?>
    });
  </script>

  <?php include BLUPATH_TEMPLATES.'/site/ads/medianet_js.php'; ?>

  <?php } ?>

  <meta name="msvalidate.01" content="E03168D9BB4076DC3C37E21B03C7EE91" />
  <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>

  <?php
    $curr_title = Template::get('curr_title');
    $default_img = Template::get('default_img');
    $img_url = '';
    if ($default_img !='') {
      $img_url = 'http://'.$_SERVER['SERVER_NAME'].ASSETURL.'/itemimages/200/200/3/'.$default_img;
    }
    if ($curr_title !='' && $img_url !='') { ?>

  <meta property="og:title" content="<?php echo $curr_title; ?>" />
  <meta property="og:type" content="article" />
  <meta property="og:url" content="http://<?php echo $_SERVER['SERVER_NAME'] . htmlspecialchars($_SERVER['REQUEST_URI']); ?>" />
  <meta property="og:image" content="<?php echo $img_url; ?>" />

    <?php } ?>

</head>

<body>

<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-PPMDBL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PPMDBL');</script>
<!-- End Google Tag Manager -->


<!--Sticky Menu-->
<script type="text/javascript" src="<?= COREASSETURL; ?>/js/stickymenu.js"></script>

<div id="nav-header">
  <div class="site-wrapper">
    <div id="logo">
      <a href="<?= SITEURL; ?>/"><img style="width:387px;margin-left: -5px;" class="screenonly" alt="<?= BluApplication::getSetting('storeName'); ?>" src="<?= SITEASSETURL; ?>/images/site/R4L-Homepage-Logo.png" /></a>
      <a href="<?= SITEURL; ?>/"><img class="printonly" alt="<?= BluApplication::getSetting('storeName'); ?>" src="<?= SITEASSETURL; ?>/images/site/R4L-Homepage-Logo.png" /></a>
    </div>
    <?php $controls = (isset($_GET['controls'])) ? $_GET['controls'] : 1; ?>
    <?php if ($format == 'print' && $controls == 1) { ?>
    <div class="print-popup-controls">
      <a href="#" onclick="window.print();" class="print">Print this page</a>
      <a href="#" onclick="window.close();" class="close">Close this window</a>
    </div>
    <?php } ?>
    <div class="tagline"><h4>Easy recipes and a helping of fun from home cooks like you.</h4></div>
    <div class="standardform screenonly">
      <div id="nav-links" class="screenonly text-content fr">
        <ul>
          <li>
            <?php $currentUser = Template::get('currentUser'); ?>
            <?php if ($currentUser) { ?>
            <a href="<?= SITEURL; ?>/account/">My Account</a> / <a href="<?= SITEURL ?>/facebook/logout/">Log out</a>
            <?php } else { ?>
              <a href="<?= SITEURL ?>/facebook/login/" id="sign_in"><span>Login</span></a> /
              <a href="<?= SITEURL ?>/register/" id="join_now"><span>Register</span></a>
            <?php } ?>
          </li>
        </ul>
      </div>
      <div class="formholder">
        <form id="nav-top-form-search" action="<?= SITEURL; ?>/search" method="get" class="search fullsubmit">
          <div>
            <div class="categories">
              <label class="radio"><input class="controllerradio" name="controller" type="radio" value="recipes"<?= Template::get('searchType', 'recipes') == 'recipes' ? ' checked="checked"' : ''; ?> /> Recipes</label>
              <label class="radio"><input class="controllerradio" name="controller" type="radio" value="articles"<?= Template::get('searchType', 'recipes') == 'articles' ? ' checked="checked"' : ''; ?> /> Articles</label>
              <!--<label class="radio"><input class="controllerradio" name="controller" type="radio" value="profile"<?= Template::get('searchType', 'recipes') == 'profile' ? ' checked="checked"' : ''; ?> /> UserName</label>-->
              <div class="clear"></div>
            </div>
            <input class="textinput simpletext fl" type="text" title="Enter search keywords..." autocomplete="off" name="searchterm" value="<?= Template::get('searchTerm'); ?>" />
            <?php $recipelinks = Template::get('recipelinks'); ?>
            <button class="button-lg fl" type="submit" title="Find"><span>Search</span></button>
          </div>
        </form>
      </div>
    </div>
    <div class="clear"></div>
  </div>
</div>

<?= $topNav; ?>


<div id="content-wrapper">
  <div class="site-wrapper">
