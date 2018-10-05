<div id="main-content" class="static">
  <div id="column-container">

    <div id="panel-center" class="column">
      <div id="about" class="rounded">
        <div class="content">
          <h1><?= htmlentities($title); ?></h1>
          <?= Messages::getMessages(); ?>
          <?php include (BLUPATH_TEMPLATES.'/landing/details/header.php'); ?>
          <?php $this->_box($boxSlug, array('limit' => 6)); ?>
        </div>
      </div>
    </div>

    <div id="panel-left" class="column">
      <?php $this->leftnav($leftLinks); ?>
    </div>

    <div id="panel-right" class="column">
      <?php $this->_box('reference_guides', array('limit' => 10)); ?>

      <?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?>
    </div>
    <div class="clear"></div>
  </div>

</div>
