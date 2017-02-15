<?php if ($this->_doc->getFormat() != 'print') { ?>
  <div id="fb-root"></div>
  <script>
    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
  </script>

  <div class="fb-comments" style="margin-top:20px;" data-href="http://www.recipe4living.com<?php echo substr($_SERVER["REQUEST_URI"],0,strlen($_SERVER["REQUEST_URI"])-1); ?>" data-numposts="5" data-colorscheme="light"></div>
<?php } ?>
