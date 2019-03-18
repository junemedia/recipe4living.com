<?php Messages::getMessages(); ?>

<div id="main-content" class="index">
  <div id="column-container">

    <div id="lc" style="overflow: hidden; width: 650px; margin-left: -160px; float: left;">

      <!-- SlideShow -->
      <div>
        <?php $this->_box('slideshow', array('limit' => 7)); ?>
      </div>

      <!-- Center -->
      <div id="panel-center" class="column">

        <?php $this->_box('middle_featured_recipes', array('limit' => 4,'boxid'=>33)); ?>
        <div class="clear"></div>

      </div>

      <!-- Left -->
      <div id="panel-left" class="column">
        <?php $this->leftnav(); ?>
      </div>
    </div>

    <!-- Right -->
    <div id="panel-right" class="column">
      <?php include(BLUPATH_TEMPLATES.'/polls/polls.php') ?>

      <?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?>
    </div>

  </div>
  <div class="clear"></div>
</div>
