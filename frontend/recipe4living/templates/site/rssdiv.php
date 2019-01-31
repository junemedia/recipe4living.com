<?php
// START OF RSS OF OTHER SITES
require_once(dirname(__FILE__).'/../../../../r4l/magpierss/rss_fetch.inc');
$ff_cacheFile = dirname(__FILE__) . '/../../../../cache_rss/ff_rss_xml.cache';
$ff_rss = fetch_rss("http://".$_SERVER['SERVER_NAME']."/cache_rss/ff_rss_xml.cache");
$wim_cacheFile = dirname(__FILE__) . '/../../../../cache_rss/wim_rss_xml.cache';
$wim_rss = fetch_rss("http://".$_SERVER['SERVER_NAME']."/cache_rss/wim_rss_xml.cache");
$rwm_cacheFile = dirname(__FILE__) . '/../../../../cache_rss/rwm_rss_xml.cache';
$rwm_rss = fetch_rss("http://".$_SERVER['SERVER_NAME']."/cache_rss/rwm_rss_xml.cache");

if (!defined('RSS_TITLE_LENGTH')) { define('RSS_TITLE_LENGTH', 30); }
?>

    <div class="rssdiv">
      <div class="ffrss">
        <a href="http://www.fitandfabliving.com/" id="box-link-ff">Fit and Fab Living</a>

        <ol class="rssfeed">
        <?php
          $ff_rss = $ff_rss->items;
          $i = 0;
          foreach ($ff_rss as $item) { ?>
            <div id="li_item">
              <a target="_blank" title="<?php echo $item['title']; ?>" href="<?php echo $item['link']; ?>">
                <?php $string = $item['title']; if (strlen($string) > RSS_TITLE_LENGTH) { $string = substr(wordwrap($string, RSS_TITLE_LENGTH), 0, strpos(wordwrap($string, RSS_TITLE_LENGTH), "\n")) . '...'; } else {$string = $item['title'];} echo $string; ?></a>
            </div>

            <?php
            $i++;
            if ($i >= 3) { break; }
          }
        ?>
        </ol>
      </div>

      <div class="wimrss">
        <a href="http://www.workitmom.com/" id="box-link-wim">Work It Mom!</a>

        <ol class="rssfeed">
        <?php
          $wim_rss = $wim_rss->items;
          $i = 0;
          foreach ($wim_rss as $item) { ?>
            <div id="li_item">
              <a target="_blank" title="<?php echo $item['title']; ?>" href="<?php echo $item['link']; ?>">
                <?php $string = $item['title'];
                        if (strlen($string) > RSS_TITLE_LENGTH) {
                            $string = substr(wordwrap($string, RSS_TITLE_LENGTH), 0, strpos(wordwrap($string, RSS_TITLE_LENGTH), "\n")) . '...';
                        } else {$string = $item['title'];} echo $string; ?>
                    </a>
                </div>
            <?php
            $i++;
            if ($i>=3) break;
          }
        ?>
        </ol>
      </div>

      <div class="rwmrss">
        <a href="http://www.savvyfork.com/" id="box-link-rwm">Running With Mascara</a>
        <ol class="rssfeed">
          <div id="li_item"><a target="_blank" title="Beautiful Appetizer Recipes" href="http://www.savvyfork.com/component/yoorecipe/category/11-appetizers.html">Beautiful Appetizer Recipes</a></div>
          <div id="li_item"><a target="_blank" title="Sumptuous Main Dish Recipes" href="http://www.savvyfork.com/component/yoorecipe/category/13-main-dishes.html">Sumptuous Main Dish Recipes</a></div>
          <div id="li_item"><a target="_blank" title="Gorgeous Dessert Recipes" href="http://www.savvyfork.com/component/yoorecipe/category/15-desserts.html">Gorgeous Dessert Recipes</a></div>
        </ol>
      </div>

      <div style="float:none; width: 100%; clear: left;"></div>
    </div>
