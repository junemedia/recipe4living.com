<?php if (!isset($_GET['cid'])) { ?>
<div class="netseer_ad screenonly">
	<script type="text/javascript">	netseer_tag_id = "10387";netseer_ad_width = "445";netseer_ad_height = "50";netseer_task = "ad";netseer_endpoint = "contextlinks.netseer.com";netseer_imp_type = "1"; netseer_imp_src = "2"; </script>
	<script src="http://contextlinks.netseer.com/dsatserving2/scripts/netseerads.js" type="text/javascript"></script>
</div>
<?php if (strtoupper(Template::get('aol_video_within_article')) == 'N') { ?>
<div id="video_container" class="screenonly">
<script type="text/javascript" src="http://pshared.5min.com/Scripts/PlayerSeed.js?sid=179&amp;width=445&amp;height=405&amp;fallback=6&amp;fallbackType=category&amp;relatedMode=2&amp;videoControlDisplayColor=%23DCD6C6&amp;categories=6"></script>
</div>
<?php } ?>
<?php } ?>