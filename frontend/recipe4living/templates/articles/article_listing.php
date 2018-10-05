<div id="main-content" class="recipes">
  <div id="column-container" style="<?php echo ($iscategory)?'padding-left:0px;width:645px;':'';?>">

    <div id="panel-center" class="column">
      <?php if ($iscategory) { ?>
      <div class="category_hubs">
        <h3 style="float:left"><a href="/">Home</a> <?php if(isset($breadcrumbs[1][1])){?>&gt;<?php }?>
          <a href="<?php echo isset($breadcrumbs[1][1])?$breadcrumbs[1][1]:'';?>">
            <?php echo isset($breadcrumbs[1][0])?$breadcrumbs[1][0]:'';?>
          </a>
        </h3>
      </div>
      <?php }?>
      <?php $this->view_items(); ?>
    </div>

    <?php if(!$iscategory){?>
    <div id="panel-left" class="column">
      <?php $this->leftnav(); ?>
    </div>
    <?php }?>

    <div id="panel-right" class="column">
      <?php if (!$iscategory) {
        $this->_box('reference_guides', array('limit' => 10));
      } ?>

      <?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?>
    </div>
    <div class="clear"></div>
  </div>
</div>
