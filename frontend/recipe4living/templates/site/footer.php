<?php if($format != 'print'){?>
<?php echo "<!--INFOLINKS_OFF-->";?><?php }?>
	<div class="clear"></div>
		<div id="footer-ad"><?= Template::get('footerAdvert'); ?></div>
	</div>
</div>









<div id="footer" class="site-wrapper screenonly">



<div style="text-align: center; margin: 0px; padding-bottom: 0px;background-color: background:#e2ded5;">
	<img src="/frontend/recipe4living/images/site/FamilyOfSites.png" border="0" />
</div>




<?php

// START OF RSS OF OTHER SITES

require_once(dirname(__FILE__).'/../../../../r4l/magpierss/rss_fetch.inc');

$ff_cacheFile = dirname(__FILE__) . '/../../../../cache_rss/ff_rss_xml.cache';
/*
$expire = time() - fileatime($ff_cacheFile);
if($expire > 86400 || !file_exists($ff_cacheFile)) {
	$rss_xml = file_get_contents("http://www.fitandfabliving.com/index.php?option=com_obrss&task=feed&id=1:rss-feed");
    $rss_xml = str_replace('<?xml-stylesheet href="http://www.fitandfabliving.com/components/com_obrss/assets/xsl/atom-to-html.xsl" type="text/xsl"?>', '' , $rss_xml);
	//$rss_xml = str_replace("?","'",$rss_xml);	// Find ?and replace with ' to avoid RSS fetch issues
	$handle = fopen($ff_cacheFile, 'w+');
	fwrite($handle, $rss_xml);
	fclose($handle);
}
*/
$ff_rss = fetch_rss("http://".$_SERVER['SERVER_NAME']."/cache_rss/ff_rss_xml.cache");


$wim_cacheFile = dirname(__FILE__) . '/../../../../cache_rss/wim_rss_xml.cache';
/*
$expire = time() - fileatime($wim_cacheFile);
if($expire > 86400 || !file_exists($wim_cacheFile)) {
	$rss_xml = file_get_contents("http://www.workitmom.com/articles/rss?format=rss");
	//$rss_xml = str_replace("?","'",$rss_xml);	// Find ?and replace with ' to avoid RSS fetch issues
	$handle = fopen($wim_cacheFile, 'w+');
	fwrite($handle, $rss_xml);
	fclose($handle);
}
*/
$wim_rss = fetch_rss("http://".$_SERVER['SERVER_NAME']."/cache_rss/wim_rss_xml.cache");


$rwm_cacheFile = dirname(__FILE__) . '/../../../../cache_rss/rwm_rss_xml.cache';
/*
$expire = time() - fileatime($rwm_cacheFile);
if($expire > 86400 || !file_exists($rwm_cacheFile)) {
	$rss_xml = file_get_contents("http://www.runningwithmascara.com/feed/");
	//$rss_xml = str_replace("?","'",$rss_xml);	// Find ?and replace with ' to avoid RSS fetch issues
	$handle = fopen($rwm_cacheFile, 'w+');
	fwrite($handle, $rss_xml);
	fclose($handle);
}
*/
$rwm_rss = fetch_rss("http://".$_SERVER['SERVER_NAME']."/cache_rss/rwm_rss_xml.cache");


$cot_cacheFile = dirname(__FILE__) . '/../../../../cache_rss/cot_rss_xml.cache';
/*
$expire = time() - fileatime($cot_cacheFile);
if($expire > 86400 || !file_exists($cot_cacheFile)) {
	$rss_xml = file_get_contents("http://www.chewonthatblog.com/feed/");
	//$rss_xml = str_replace("?","'",$rss_xml);	// Find ?and replace with ' to avoid RSS fetch issues
	$handle = fopen($cot_cacheFile, 'w+');
	fwrite($handle, $rss_xml);
	fclose($handle);
}
*/
$cot_rss = fetch_rss("http://".$_SERVER['SERVER_NAME']."/cache_rss/cot_rss_xml.cache");

?>
<div style="width: 980px;" id="mainFooterDiv">
<div class="rssdiv" id="ffrss">
<?php

if(!defined('RSS_TITLE_LENGTH')){
	define('RSS_TITLE_LENGTH', 30);
}

	$ff_rss = $ff_rss->items;
	$i = 0;
	echo '<ol class="rssfeed">';
	foreach ($ff_rss as $item) { ?>

		<div id="li_item"><a target="_blank" title="<?php echo $item['title']; ?>" href="<?php echo $item['link']; ?>"><?php $string = $item['title']; if (strlen($string) > RSS_TITLE_LENGTH) { $string = substr(wordwrap($string, RSS_TITLE_LENGTH), 0, strpos(wordwrap($string, RSS_TITLE_LENGTH), "\n")) . '...'; } else {$string = $item['title'];} echo $string; ?></a>
        </div>
		<?php
		$i++;
		if ($i>=3) break;
	}
	echo '</li>';
?>
</div>

<div class="rssdiv" id="wimrss">
<?php
	$wim_rss = $wim_rss->items;
	$i = 0;
	echo '<ol class="rssfeed">';
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
	echo '</ol>';
?>
</div>

<div class="rssdiv" id="rwmrss">
<?php
	/*$rwm_rss = $rwm_rss->items;
	$i = 0;
	echo '<ol class="rssfeed">';
	foreach ($rwm_rss as $item) { ?>
		<div id="li_item"><a target="_blank" title="<?php echo $item['title']; ?>" href="<?php echo $item['link']; ?>"><?php $string = $item['title']; if (strlen($string) > RSS_TITLE_LENGTH) { $string = substr(wordwrap($string, RSS_TITLE_LENGTH), 0, strpos(wordwrap($string, RSS_TITLE_LENGTH), "\n")) . '...'; } else {$string = $item['title'];} echo $string; ?></a>
        </div>
		<?php
		$i++;
		if ($i>=3) break;
	}
	echo '</ol>';*/
?>
	<ol class="rssfeed">
	<div id="li_item"><a target="_blank" title="Beautiful Appetizer Recipes" href="http://www.savvyfork.com/component/yoorecipe/category/11-appetizers.html">Beautiful Appetizer Recipes</a></div>
	<div id="li_item"><a target="_blank" title="Sumptuous Main Dish Recipes" href="http://www.savvyfork.com/component/yoorecipe/category/13-main-dishes.html">Sumptuous Main Dish Recipes</a></div>
	<div id="li_item"><a target="_blank" title="Gorgeous Dessert Recipes" href="http://www.savvyfork.com/component/yoorecipe/category/15-desserts.html">Gorgeous Dessert Recipes</a></div>
	</ol>
</div>

<div class="rssdiv" id="cotrss">
<?php
	$cot = $cot_rss->items;
	$i = 0;
	echo '<ol class="rssfeed">';
	foreach ($cot as $item) { ?>
		<div id="li_item">
            <a target="_blank" title="<?php echo $item['title']; ?>" href="<?php echo $item['link']; ?>">
            <?php 
                $string = $item['title']; 
                if (strlen($string) > RSS_TITLE_LENGTH) { 
                    $string = substr(wordwrap($string, RSS_TITLE_LENGTH), 0, strpos(wordwrap($string, RSS_TITLE_LENGTH), "\n")) . '...'; 
                }else {
                    $string = $item['title'];
                } 
                echo $string; 
            ?>
            </a>
        </div>
		<?php
		$i++;
		if ($i>=3) break;
	}
	echo '</ol>';
?>
</div>
<?php
//	END OF RSS OF OTHER SITES
?>
<div style="clear: left;"></div>              

<div class="mainFooterDiv">
	<a target="_blank" id="box-link-ff" href="http://www.fitandfabliving.com/"></a>
	<a target="_blank" id="box-link-wim" href="http://www.workitmom.com/"></a>
	<a target="_blank" id="box-link-rwm" href="http://www.savvyfork.com/"></a>
	<a target="_blank" id="box-link-cot" href="http://www.chewonthatblog.com/"></a>
</div>

<div style="clear: left;"></div>

<div id="topLinks" style="text-align: center;padding-top:150px;">
	<span style="padding-left: 0px;">
		<a href="<?= SITEURL ?>/about">About Us</a>&nbsp;|&nbsp;
		<a href="<?= SITEURL ?>/contact/">Contact Us</a>&nbsp;|&nbsp;
		<a href="<?= SITEURL ?>/press">Press Room</a>&nbsp;|&nbsp;
		<a href="<?= SITEURL ?>/sitemap">Site Map</a>&nbsp;|&nbsp;
		<a href="<?= SITEURL ?>/index/links">Advertising</a>&nbsp;|&nbsp;
		<a href="<?= SITEURL ?>/privacy">Privacy Policy</a>&nbsp;|&nbsp;
		<a href="<?= SITEURL ?>/terms">Terms of Use</a>&nbsp;|&nbsp;
		<a href="<?= SITEURL ?>/index/unsub">Unsubscribe</a>&nbsp;|&nbsp;
		<a href="<?= SITEURL; ?>/index/subctr">Manage My Newsletters</a>
	</span>
	<p class="text-content"><br>&copy; <?php echo date('Y'); ?> <a href="http://www.junemedia.com/" target="_blank">June Media Inc</a> All rights reserved</p>
</div>
</div>

</div>



	
  


		
	<div class="clear"></div>
	


	<?php if (DEBUG_INFO && isset($debugInfo)) { echo $debugInfo; } ?>

	
	

	<?php if (ADS) {
			$array_url_to_hide_vibrant_code = array('giveaway','login','register','share','delicious_and_gluten_free_with_tasteguru_it_s_true','stonefire');
				
				if ($_SERVER['REQUEST_URI'] != '') {
					$found_in_array = false;
					foreach ($array_url_to_hide_vibrant_code as $temp_slug) {
						if (strstr($_SERVER['REQUEST_URI'], $temp_slug)) {
							$found_in_array = true;
							break;
						}
					}
				} else {
					$found_in_array = true;
				}
				
				if (isset($_GET['cid'])) { $found_in_array = true; }
		?>

		<!-- FM Tracking Pixel -->
		<script type='text/javascript' src='http://static.fmpub.net/site/recipe4livingco'></script>
		<!-- FM Tracking Pixel -->
		
		<?php if ($found_in_array == false) {
			echo '<script type="text/javascript">var infolinks_pid = 1863387;var infolinks_wsid = 0;</script><script type="text/javascript" src="http://resources.infolinks.com/js/infolinks_main.js"></script>';
		} ?>
		
		<script type="text/javascript" src="http://edge.quantserve.com/quant.js"></script>
		<script type="text/javascript">
			_qacct="p-ed7ji9FtIlPSo";quantserve();
		</script>
	<?php } ?>
	
	<!-- LiveRamp --><iframe name="_rlcdn" width=0 height=0 frameborder=0 src="http://rc.rlcdn.com/381139.html"></iframe><!-- LiveRamp -->
    <!-- BEGIN SiteCTRL Script -->
    <script type="text/javascript">
    if(document.location.protocol=='http:'){
     var Tynt=Tynt||[];Tynt.push('dRKpBGeA8r5kFwacwqm_6l');
     (function(){var s=document.createElement('script');s.async="async";s.type="text/javascript";s.src='http://tcr.tynt.com/ti.js';var h=document.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);})();
    }
    </script>
    <!-- END SiteCTRL Script -->
    <?php
    
         $encryption_key = "36851a80704f3cbe";
    
        function lr_encrypt($input, $encryption_key) {
          $input = pkcs5_pad($input, mcrypt_get_block_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC));
          $td = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');
          $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
          mcrypt_generic_init($td, $encryption_key, $iv);
          $data = mcrypt_generic($td, $input);
          mcrypt_generic_deinit($td);
          mcrypt_module_close($td);
          return array(bin2hex($data), bin2hex($iv), 'bf-cbc');
        }
         
        function pkcs5_pad($text, $blocksize) {
          $pad = $blocksize - (strlen($text) % $blocksize);
          return $text . str_repeat(chr($pad), $pad);
        }
         
        
            
        // LiveRamp Email, we use cookie of Email to get it.
        if(isset($_COOKIE['EMAIL_ID'])){
            // We load the LiveRamp if the email already set
            $email = strtolower($_COOKIE['EMAIL_ID']);
            list($xs, $v, $c) = lr_encrypt(sha1($email), $encryption_key);
            $liveramp = '<iframe name="_rlcdn" width=0 height=0 frameborder=0 src="http://ei.rlcdn.com/366718.html?xs='.$xs.'&c='.$c.'&v='.$v.'"></iframe>';
            echo $liveramp;
        }
    
    
    ?>
    
<!-- pub exchange tags scripts START -->    
<script>(function(d, s, id) {
var js, pjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id; js.async = true;
js.src = "//cdn.pubexchange.com/modules/partner/recipe_4_living";
pjs.parentNode.insertBefore(js, pjs);
}(document, "script", "pubexchange-jssdk"));</script>
<!-- pub exchange tags scripts END -->
    
    
</body>
</html>
