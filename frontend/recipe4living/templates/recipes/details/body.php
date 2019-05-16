  <?php
    // BODGE BODGE BODGE BODGE
    $showPreparation = false;
    $showCooking = false;
    if ($item['preparation_time']['quantity'] && $item['preparation_time']['measure']) {
      $showPreparation = true;
    }
    if ($item['cooking_time']['quantity'] && $item['cooking_time']['measure']) {
      $showCooking = true;
    }
  ?>
  <div id="contentbody">
    <div class="entry_middle">
      <div class="entry_left">
        <?php if ($showPreparation || $showCooking) { ?>
        <div id="recipe-time" class="block">
          <h2>Time needed</h2>
          <div class="content">
          <?php if ($showPreparation) { ?>
            <span>
              <?php if ($item['preparation_time']['measure'] == 'minutes') { ?>
              <meta itemprop="prepTime" content="PT<?= $item['preparation_time']['quantity']; ?>M"><?= $item['preparation_time']['quantity'].' min'; ?>
              <?php } else if ($item['preparation_time']['measure'] == 'hours') { ?>
              <meta itemprop="prepTime" content="PT<?= $item['preparation_time']['quantity']; ?>H"><?= $item['preparation_time']['quantity'].' hour'; ?>
              <?php } else if ($item['preparation_time']['measure'] == 'days') { ?>
              <?= $item['preparation_time']['quantity']; ?> <?= $item['preparation_time']['measure']; ?>
              <?php } ?>
            </span> preparation
          <?php } ?>
          <?php if ($showPreparation && $showCooking) { ?> + <?php } ?>
          <?php if ($showCooking) { ?>
            <span>
              <?php if ($item['cooking_time']['measure'] == 'minutes') { ?>
              <meta itemprop="cookTime" content="PT<?= $item['cooking_time']['quantity']; ?>M"><?= $item['cooking_time']['quantity'].' min'; ?>
              <?php } else if ($item['cooking_time']['measure'] == 'hours') { ?>
              <meta itemprop="cookTime" content="PT<?= $item['cooking_time']['quantity']; ?>H"><?= $item['cooking_time']['quantity'].' hour'; ?>
              <?php } else if ($item['cooking_time']['measure'] == 'days') { ?>
              <?= $item['cooking_time']['quantity']; ?> <?= $item['cooking_time']['measure']; ?>
              <?php } ?>
            </span> cooking
          <?php } ?>
          </div>
        </div>
        <?php } ?>

        <?php if (!empty($item['yield']['quantity'])) { ?>
        <div id="recipe-servingSize" class="block">
          <h2>Serving Size / Yield</h2>
          <div class="content">
            <span class="servingsize" itemprop="recipeYield">
              <?php
                echo $item['yield']['quantity'].' ';
                if ($item['yield']['measure'] == 'people') {
                  echo 'serving';
                  if ($item['yield']['quantity'] > 1) {
                    echo 's';
                  }
                }
                else {
                  echo $item['yield']['measure'];
                }
              ?>
            </span>
          </div>
        </div>
        <?php } ?>

        <?php if (!empty($item['ingredients'])) { ?>
        <div id="recipe-ingredients" class="block">
          <h2>Ingredients</h2>
          <div class="content">
            <ul>
              <?php foreach ($item['ingredients'] as $ingredient) { ?>
              <li class="ingredient" itemprop="ingredients"><?= $ingredient; ?></li>
              <?php } ?>
            </ul>
          </div>
        </div>
        <?php } ?>

      </div> <!--End entry left-->

      <?php if ($this->_doc->getFormat() != 'print') { ?>
      <div class="entry_right screenonly">
        <div class="rounded" id="recipe-title">
          <div class="content">
            <span class="item" style="text-align: center;" >
              <h2 style="line-height: 1.25em; color: rgb(255, 255, 255); margin-bottom: 0px;" class="fn">Our Readers Also Loved</h2>
            </span>
          </div>
        </div>

        <div class="right_content">
          <?php if (!empty($item['readerloved'])) { ?>
          <ul class="thumb-list">
            <?php foreach ($item['readerloved'] as $key=>$ritem) { ?>
            <li>
              <div class="im">
                <a href="<?= SITEURL.$ritem['link']; ?>">
                  <img width="75" height="75" alt="<?= isset($ritem['featuredImage']['filename']) ? $ritem['featured_alt'] : $ritem['default_alt'];?>" src="<?= ASSETURL; ?>/itemimages/75/75/3/<?= isset($ritem['featuredImage']['filename']) ? $ritem['featuredImage']['filename'] : $ritem['image']['filename']; ?>" />
                </a>
              </div>

              <div class="desc">
                <h5><a href="<?= SITEURL.$ritem['link']; ?>"><?= $ritem['title']; ?></a></h5>
                <div class="rating text-content fl">
                  <?php include(BLUPATH_TEMPLATES.'/articles/items/reader_loved_rating.php'); ?>
                </div>
              </div>

              <div class="clear"></div>
            </li>
            <?php } ?>
          </ul>
          <?php } else { ?>
            No matched recipes.
          <?php }?>
        </div>
      </div><!--End entry right-->
      <?php }?>
    </div><!--END entry middle-->

    <div class="clear"></div>
      <div id="recipe-directions" class="block" >
        <h2>Directions</h2>
        <div class="content">
          <span class="instructions" itemprop="recipeInstructions">
            <?= Text::toHtml($item['body']); ?>
          </span>
        </div>
      </div>
    </div>

    <?php include(BLUPATH_TEMPLATES.'/box/addalt.php'); ?>

    <br>
    <?php include BLUPATH_TEMPLATES.'/site/ads/lockerdome.html'; ?>

    <div class="ad screenonly">
      <?php include BLUPATH_TEMPLATES.'/site/ads/medianet_bta.html'; ?>
    </div>

    <br>
    <div class="ad screenonly">
      <?php include BLUPATH_TEMPLATES.'/site/ads/outbrain_smartfeed.html'; ?>
    </div>

    <?php include BLUPATH_TEMPLATES.'/recipes/details/similar-dishes.php'; ?>
