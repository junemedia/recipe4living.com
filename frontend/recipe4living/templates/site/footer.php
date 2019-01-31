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

<!-- Quantcast Tag -->
<script type="text/javascript">
  var _qevents = _qevents || [];

  (function() {
    var elem = document.createElement('script');
    elem.src = (document.location.protocol == "https:" ? "https://secure" : "http://edge") + ".quantserve.com/quant.js";
    elem.async = true;
    elem.type = "text/javascript";
    var scpt = document.getElementsByTagName('script')[0];
    scpt.parentNode.insertBefore(elem, scpt);
  })();

  _qevents.push({
    qacct:"p-ed7ji9FtIlPSo"
  });
</script>

<noscript>
  <div style="display:none;">
    <img src="//pixel.quantserve.com/pixel/p-ed7ji9FtIlPSo.gif" border="0" height="1" width="1" alt="Quantcast"/>
  </div>
</noscript>
<!-- End Quantcast tag -->

<?php include BLUPATH_TEMPLATES.'/site/ads/liveconnect.php'; ?>
<?php include BLUPATH_TEMPLATES.'/site/ads/outbrain_js.php'; ?>

</body>
</html>
