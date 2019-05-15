<?php if ($this->_doc->getFormat() != 'print' && !empty($item['related'])) { ?>

<br><br>
<div class="block screenonly" id="similar-dishes">
  <h2>Similar Dishes</h2>
  <div class="similar_dishes">
    <ul class="thumb-list" style="margin-bottom:0px;">
      <?php foreach ($item['related'] as $relatedItem) { ?>
      <li>
        <div class="im">
          <a href="<?= SITEURL.$relatedItem['link']; ?>">
            <img width="100" height="100" alt="<?= (!empty($relatedItem['default_alt']))?$relatedItem['default_alt']:'';?>" src="<?= ASSETURL; ?>/itemimages/120/120/3/<?= isset($relatedItem['featuredImage']['filename']) ? $relatedItem['featuredImage']['filename'] : $relatedItem['image']['filename']; ?>" />
          </a>
        </div>
        <div class="desc">
          <h5 style="font-size:12px"><a href="<?= SITEURL.$relatedItem['link']; ?>"><?= Text::trim($relatedItem['title'], 25); ?></a></h5><?php if(strlen($relatedItem['title'])<=15)echo "<br>"?>
          <div class="rating text-content fl">
            <?php include(BLUPATH_TEMPLATES.'/articles/items/similar_rating.php'); ?>
          </div>
        </div>
        <div class="clear"></div>
      </li>
      <?php } ?>
    </ul>
  </div>

  <div class="clear"></div>
</div>

<?php } ?>
