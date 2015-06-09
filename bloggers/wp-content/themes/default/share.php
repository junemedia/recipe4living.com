<?

// Share-this icons.
foreach (array('delicious', 'digg', 'facebook', 'kirtsy', 'reddit') as $ext) $$ext = SITEASSETURL . '/images/site/share_'.$ext.'.gif';

ob_start(); the_permalink(); $share_link = ob_get_clean();
ob_start(); the_title(); $share_title = ob_get_clean();

?>

<div id="share_post" class="screenonly">
	<div id="sharethis">
		<strong>Share this on:</strong>
		<ul>
			<li id="digg">&nbsp;<a target="_blank" href="http://digg.com/submit?phase=2&amp;url=<?=$share_link?>&amp;title=<?=trim(strip_tags($share_title));?>">Digg</a></li>
			<li id="facebook">&nbsp;<a target="_blank" href="http://www.facebook.com/share.php?u=<?=$share_link?>">Facebook</a></li>    
			<li id="reddit">&nbsp;<a target="_blank" href="http://reddit.com/submit?url=<?=$share_link?>&amp;title=<?=trim(strip_tags($share_title));?>">Reddit</a></li>
			<li id="delicious">&nbsp;<a target="_blank" href="http://del.icio.us/post?url=<?=$share_link?>&amp;title=<?=trim(strip_tags($share_title));?>">del.icio.us</a></li>
			<li id="kirtsy" class="last">&nbsp;<a target="_blank" href="http://www.kirtsy.com/submit.php?url=<?=$share_link?>">kirtsy</a></li>
		</ul>
	</div>
	<div class="clear2"></div>
</div>

<div class="divider"></div>