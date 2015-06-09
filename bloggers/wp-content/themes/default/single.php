<?php 
$relatedlink = get_permalink();
$addtitle = get_the_title();
get_header();
get_sidebar();
if (have_posts()) : while (have_posts()) : the_post(); 
 ?>	
<div id="main-content" class="blogs">
		<div class="panel-left">

		<wppost id="<?php the_ID(); ?>" />

		WP_BLOGIMAGE

		<div class="post">

			<div id="post_title">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<p class="byline">Posted by <?php the_author_link() ?> on <?php the_time('jS F Y') ?></p>
				<p class="text-content fl">Categories: <?php the_category(', ') ?><?php edit_post_link('Edit', ' &nbsp;|&nbsp; ', ''); ?></p>
				<p class="text-content fr"><a href="#comments"><?php comments_number('No comments yet', '1 comment', '% comments' );?></a></p>
				<div class="clear"></div>
			</div>
					
			<div class="entry">
			<?php $gotauth=the_author('',false);
			 the_content('<br />Read the rest of this entry'); ?>
			 <div class="clear"></div>
			 <a href="<?= bloginfo('url') ?>/feed/" class="rss" style="font-size:1em;">Subscribe to blog via RSS</a>
			<div class="clear"></div>
			</div>
			
		</div>


<?php include_once('share.php'); ?>
		
		<? //st_the_tags();?>
		<? /* Tags: <?=STP_GetPostTags();?>*/?>
	
	<?php /* THREE MOST RECENT POSTS? */ ?>
	WP_RECENTPOSTS
	
	<a name="comment"></a>
	<?php comments_template(); ?>
	
	<?php endwhile; else: ?>

	</div>


<?php get_sidebar(); ?>

<?php endif; ?>

	</div>
	
	WP_BOTTOMBLOCKS

	<div class="clear"></div>

</div>	

<?php get_footer(); ?>
