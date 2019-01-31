    <div class="clear"></div>
  </div>
</div>

<div id="footer" class="site-wrapper screenonly">

  <div style="text-align: center; margin: 0; padding-bottom: 0px;">
    <img src="/frontend/recipe4living/images/site/FamilyOfSites.png" border="0" />
  </div>


  <div id="mainFooterDiv">
    <?php include BLUPATH_TEMPLATES.'/site/rssdiv.php'; ?>

    <div id="topLinks" style="text-align: center;">
      <span style="padding-left: 0px;">
        <a href="<?= SITEURL ?>/about">About Us</a>&nbsp;|&nbsp;
        <a href="<?= SITEURL ?>/contact/">Contact Us</a>&nbsp;|&nbsp;
        <a href="<?= SITEURL ?>/press">Press Room</a>&nbsp;|&nbsp;
        <a href="<?= SITEURL ?>/sitemap">Site Map</a>&nbsp;|&nbsp;
        <a href="<?= SITEURL ?>/index/links">Advertising</a>&nbsp;|&nbsp;
        <a href="<?= SITEURL ?>/privacy">Privacy Policy</a>&nbsp;|&nbsp;
        <a href="<?= SITEURL ?>/terms">Terms of Use</a><!--&nbsp;|&nbsp;-->
        <!--<a href="<?= SITEURL; ?>/index/subctr">Manage My Newsletters</a>-->
      </span>
      <p class="text-content"><br>&copy; <?php echo date('Y'); ?> <a href="http://www.junemedia.com/" target="_blank">June Media Inc</a> All rights reserved</p>
    </div>
  </div>

</div>

<div class="clear"></div>

<?php if (ADS) { ?>
  <script type="text/javascript" src="http://edge.quantserve.com/quant.js"></script>
  <script type="text/javascript">
    _qacct="p-ed7ji9FtIlPSo";quantserve();
  </script>
<?php } ?>


<?php
  // I'm pretty sure this is all dead code, but leaving it for now
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
?>


<?php include BLUPATH_TEMPLATES.'/site/ads/liveconnect.php'; ?>
<?php include BLUPATH_TEMPLATES.'/site/ads/outbrain_js.php'; ?>

</body>
</html>
