<div id="main-content" class="recipe">

  <div id="column-container" style="padding-left:0px;width:645px;">

    <!-- start of class hrecipe -->
    <div itemscope itemtype="http://schema.org/Recipe">
      <div id="panel-center" class="column">
        <div style="overflow: hidden;">

          <!-- Slide Title First-->
          <div id="recipe-title" class="rounded">
            <div class="content">
              <span class="item"><h1 style="font-size: 1.25em; line-height: 1.25em; color:#ffffff; margin-bottom: 0;" class="fn"><?php echo $item['title'];?> </h1></span>
            </div>
          </div>

          <!-- Left column -->
          <div style=" width: 300px; float: left;">
            <!-- Recipe Image -->
            <img style="margin: 5px;" src="<?= ASSETURL; ?>/itemimages/300/300/3/<?= $pages['image']['filename']; ?>" alt="">
          </div>

          <!-- Right Column-->
          <div style="width: 320px; float: right;">

            <!-- Pagination-->
            <div id="slidePagination" style="float: right; overflow: hidden; width: 100%; margin: 5px;">
              <a href="<?php echo $pageLinkPre;?>"><img src="/frontend/recipe4living/images/site/slideshows_back.png" alt="" style="float: left;"></a>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <a href="<?php echo $pageLinkNext;?>"><img src="/frontend/recipe4living/images/site/slideshows_forward.png" alt="" style="float: right;"></a>
            </div>

            <!-- Preview gallery-->
            <div>
              <?php
              foreach($slideAll as $key=> $value){
              $order = $itemsModel->getSlideOrderBySlidePageArticleId($item['id'], $value['id']);
              ?>
              <a href="<?php echo $pageLinkPre = '/slidearticles/details/'. $arg[0] . '/' . $order;?>">
                <img style="<?php  if($value['id'] == $pages['id']){?> border: red 5px solid;<?php }else { ?> border: white 5px solid; <?php } ?>" src="<?= ASSETURL; ?>/itemimages/50/50/3/<?= $value['image']['filename']; ?>" alt="">
              </a>
              <?php } ?>
            </div>

            <!-- Main Content-->
            <div id="slidePageTitle" style="font-size: 16px; margin: 5px; font-family: Arial, Helvetica, sans-serif;"><a href="<?php echo $pages['link'];?>" target="_blank"><?php echo $pages['title']?></a></div>
            <div id="slidePageContent" style="margin: 5px; font-family: Arial, Helvetica, sans-serif; font-size: 16px;"><?php if(trim($pages['slide_desc']) == "") {echo $pages['description'];}else{echo $pages['slide_desc'];}?></div>


            <!-- Jump the Recipe page-->
            <a href="<?php echo $pages['link'];?>" target="_blank">Get the recipe here!</a>

            <!-- Social Sharing Buttons -->
            <div id="shareButton">
              <div class="nav-icon-item" style="margin-top: 10px;">
                <span>Share:</span>
                <a href="http://goo.gl/8XKWk" target="_blank"><img width="30" height="30" src="<?= SITEASSETURL; ?>/images/site/R4l-facebook-box.png"/></a>
                <a href="http://goo.gl/QWWuy" target="_blank"><img width="30" height="30"  src="<?= SITEASSETURL; ?>/images/site/R4l-twitter-box.png"/></a>
                <a href="http://goo.gl/LBcYo" target="_blank"><img  width="30" height="30" src="<?= SITEASSETURL; ?>/images/site/R4l-pinterest-box.png"/></a>
                <a href="http://bit.ly/ZxqJ3w" target="_blank"><img width="30" height="30"  src="<?= SITEASSETURL; ?>/images/site/R4l-googleplus-box.png"/></a>
              </div>
            </div>
          </div>
        </div>

        <div class="clear2"></div>
        <?php include BLUPATH_TEMPLATES.'/site/ads/lockerdome.html'; ?>

        <div class="clear2"></div>
        <?php include BLUPATH_TEMPLATES.'/site/ads/medianet_604x250.php'; ?>

        <div class="clear"></div>
      </div>
    </div>


    <div id="panel-right" class="column screenonly">
      <div class="ad"><?php $this->_advert('openx_300x250atf'); ?></div>

      <?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?>
    </div>
    <div class="clear"></div>
  </div>
</div>
