<?php if (!isset($_GET['cid'])) { ?>
  <?php if (strtoupper(Template::get('aol_video_within_article')) == 'N') { ?>
    <div id="video_container" class="screenonly">
    <script type="text/javascript" src="http://pshared.5min.com/Scripts/PlayerSeed.js?sid=179&amp;width=445&amp;height=405&amp;fallback=6&amp;fallbackType=category&amp;relatedMode=2&amp;videoControlDisplayColor=%23DCD6C6&amp;categories=6"></script>
    </div>
  <?php } ?>
<?php } ?>
