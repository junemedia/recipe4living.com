<div id="main-content" class="recipe">
  <div id="column-container" style="padding-left:0px;width:645px;">

    <div id="panel-center" class="column">
      <?= Messages::getMessages(); ?>

      <?php  if ($this->_doc->getFormat() != 'print') { $this->category_hubs(); }?>

      <?php include(BLUPATH_TEMPLATES.'/articles/details/title.php'); ?>

      <?php
      $currUrl = urlencode('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
      $currImage = urlencode('http://'.$_SERVER['SERVER_NAME'].ASSETURL.'/itemimages/200/200/3/' .$item['image']['filename']);
      ?>

      <div id="post">
        <?php $temp_img = $item['image']['filename']; if (strstr($item['image']['filename'],'avatar')) { $item['image']['filename']='';} ?>
        <?php if ($item['image']['filename'] !='') { ?>
        <?php if ($this->_doc->getFormat() != 'print') {?>
        <div class="im screenonly">
          <img width="350" width="350" src="<?= ASSETURL; ?>/itemimages/400/400/3/<?= $item['image']['filename']; ?>" class="photo" alt="<?= (!empty($item['default_alt']))?$item['default_alt']:$item['title'];?>" style="float:left;margin:0 20px 20px 0;"/>
          <div class="gallery-link"> </div>
        </div>
        <?php }
        } else { ?>
        <meta itemprop="image" content="<?= ASSETURL; ?>/itemimages/200/200/3/<?php echo $temp_img; ?>">
        <?php } ?>

        <div class="teaser fl">
          <div class="user-info ">
            <a href="<?= SITEURL; ?>/profile/<?= $item['author']['username']; ?>" style="float:left;margin-right:10px;clear:none;width:65px;height:65px;">
              <img width="65" height="65" alt="" src="<?= ASSETURL; ?>/userimages/65/65/1/<?= $item['author']['image'];?>">
            </a>

            <div style="float:left;margin-top:8px;">
              <a href="<?= SITEURL; ?>/profile/<?= $item['author']['username']; ?>" style="font-size:16px;font-weight:bold;color:#0F52B0;">
                <?php $displayname = $item['author']['firstname'].' '.$item['author']['lastname'];
                if($displayname != " " && $displayname != "" && !is_null($displayname))
                echo $displayname;
                else
                {
                $displayname = "Recipe4Living";
                echo "Recipe4Living";
                }
                ?>
              </a>
              <span style="display:block;font-size:12px;color:#4E4E4E;">
                <?php
                $CommunityContributor = array('vanessalantigua','jennkendall','kianishimine','meaghansloand','ashleighevans','daniellegalian','dawnharris','samanthas','katherineb','morganq','shalaynep','stephaniek','haleysoehn','kaitlynconn','kelleygrant','erincullen', 'laurabolbat', 'tamarlange', 'rachelfarnsworth');
                if(trim($displayname) == "Recipe4Living") {
                echo '';
                } else if($item['author']['type'] == 'admin') {
                if (in_array(strtolower($item['author']['username']),$CommunityContributor)) { echo "Community Contributor"; } else { echo "Editor"; }
                } else {
                echo "Community Contributor";
                } ?>
              </span>
            </div>
          </div>
          <div class="clear" style="margin:25px 0;"></div>
          <?php if ($item['image']['filename'] !='') { ?>
          <p class="snippet">
          <?php } else { ?>
          <p class="snippet" style="width:460px;">
          <?php } ?>
          <?= $item['teaser']; ?></p>
          <?php $format = $this->_doc->getFormat(); ?>

        </div>
        <?php if ($this->_doc->getFormat() != 'print') {?>
        <div class="clear"></div>
        <div class="share_recipe screenonly">
          <div class="share_title" style="margin-bottom: 15px;"><h2>Share Article</h2></div>
          <div class="share_icon">
            <ul>
              <li><a href="http://www.facebook.com/sharer.php?u=<?php echo $currUrl;?>" target="_blank"><img src="<?= SITEASSETURL; ?>/images/site/R4l-facebook-box.png"/></a></li>
              <li><a href="https://twitter.com/share?original_referer=<?php echo $currUrl;?>" target="_blank"><img src="<?= SITEASSETURL; ?>/images/site/R4l-twitter-box.png"/></a></li>
              <li><a href="http://pinterest.com/pin/create/button/?url=<?php echo $currUrl;?>&media=<?=$currImage;?>" target="_blank"><img src="<?= SITEASSETURL; ?>/images/site/R4l-pinterest-box.png"/></a></li>
              <li><a href="https://plus.google.com/share?url=<?php echo $currUrl;?>" target="_blank"><img src="<?= SITEASSETURL; ?>/images/site/R4l-googleplus-box.png"/></a></li>
              <li class="share_print"><a href="?format=print" class="print-popup"><img src="<?= SITEASSETURL; ?>/images/site/R4l-print-box.png"/></a></li>
              <li class="first"><span  class='st_email_button' displayText='Email'></span></li>
            </ul>
            <script type="text/javascript">var switchTo5x=false;</script>
            <script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
            <script type="text/javascript">stLight.options({publisher:'0541fe9f-2a3f-4c01-ac74-8f02c84e7fde'});</script>
          </div>
        </div>
        <?php }?>
        <div class="clear"></div>

        <div class="entry">
          <?php include_once(BLUPATH_TEMPLATES.'/site/ads/AOL_VIDEOS.php'); ?>

          <?php $this->view_page(); ?>
          <div class="clear"></div>
        </div>

        <?php include BLUPATH_TEMPLATES.'/site/ads/medianet_604x250.php'; ?>

        <?php $this->_addReview(); ?>

        <?php $this->reviews(); ?>

        <div class="clear"></div>
      </div>

      <div class="conversationalist ad">
        <!-- FM Test STAMP ("Home") 500x250 Zone -->
        <script type='text/javascript' src='http://static.fmpub.net/zone/2560'></script>
      </div>
    </div>

    <div id="panel-right" class="column screenonly">
      <div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>

      <?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?>
    </div>
    <div class="clear"></div>
  </div>
</div>
