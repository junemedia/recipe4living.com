<?php
	global $blog_id;
?>
<!-- BEGIN RIGHT HAND CONTENT -->
<!-- BEGIN RIGHT HAND SIDE -->
<div class="panel-right">

	<h3><a href="<?= bloginfo('url') ?>/feed/" class="rss">Subscribe to blog via RSS</a></h3>

	WP_NEWSLETTER

	<?php if ( !function_exists('dynamic_sidebar')  || !dynamic_sidebar() ) : ?><div class="clear"></div>
	<?php endif; ?>
	WP_SLIDESHOW
	
	<!-- Blogs-->
	<?php /*
	<?php
		// Banner for Cornered Office
		if ($blog_id == 4) {
	?>
		<div style="width:300px;text-align:center;padding-bottom:12px;"><a href="http://www.amazon.com/dp/1556527721/wantnot-20"><img src="<?= $sitepath; ?>/images/blogheader/sleep2.png"></a></div>
	<?php
		// Banner for Affordable Luxuries
		} elseif ($blog_id == 27) {
	?>
		<div id="bg_affordable">
			Have a product you can't live without? Send tips and ideas to us at <a href="mailto:ideas@workitmom.com">ideas@workitmom.com</a>. We can't guarantee that we'll write about them, but we'll definitely check them out.<br />
			<br />
			Have you created a product that's perfect for working moms? Email <a href="mailto:victoria@workitmom.com">victoria@workitmom.com</a> to talk about ways to promote it to our audience.
		</div>
	<?php } ?>
	*/ ?>

	<?php //include($fspath.'page_partials/rightnav/blog-latestcomments.php'); ?>
	WP_SIDEBARCHUNK	

	<?php
	/*
		if ($blog_id == 27) {
		?>

	WP_MINI_AD
	<div class="clear" style="margin-bottom: 25px;"></div>

		<?php
		}
	*/
	?>
	
	<?php
		// Daily Grommet for Affordable Luxuries
		if ($blog_id == 27) {
	?>
			<script type="text/javascript">
				var grommet_affiliate_code = '2975928724';
			</script>
			<script type="text/javascript" src="http://www.dailygrommet.com/widgets/300_video.js"></script>

			<div class="clear" style="margin-bottom: 25px;"></div>

	<?php
		// Popular posts module
		} else {
			echo ' WP_POPULARPOSTS ';
		}
	?>

	<div class="rounded-300-blue block">
		<div class="top"></div>
		<div class="content">

			<!-- Categories -->
			<h2>Browse Blog Categories</h2>
			<ul class="text-content">
				<?php wp_list_categories('title_li='); ?>
			</ul>

		</div>
		<div class="bot"></div>
	</div>

	<div class="rounded-300-grey block">
		<div class="top"></div>
		<div class="content">

			<h2>Blog Archives</h2>
			<ul class="text-content">
				<?php wp_get_archives('type=monthly&limit=4'); ?>
			</ul>

			<!-- Archives-->
			<?php
			/*global $related,$relatedlink;
			if ($related && $relatedlink) {
				global $related,$alsolike;
				$alsolike = $related;
				$alsolikelink = $relatedlink;
				if (substr($alsolikelink,0,7) == 'http://') {
					$alsolikelink = str_replace(str_replace('www.','',$sitepath),'',str_replace('www.','',$alsolikelink));
				}
				if ($blog_id != 27){
					include($fspath.'page_partials/rightnav/alsolike.php');
				}
			}
			include($fspath.'page_partials/marketfeatures.php');*/
			?>

		</div>
		<div class="bot"></div>
	</div>
	
	<?php include (TEMPLATEPATH . '/searchform.php'); ?>
	WP_SIDEBARCHUNK	
	

</div>
<!-- END RIGHT HAND SIDE -->
