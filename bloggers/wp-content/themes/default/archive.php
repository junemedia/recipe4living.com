<?php get_header(); ?>

	<div id="main-content" class="blogs">
		
		<div class="panel-left">
		
			<div class="rounded-630-grey" style="margin-bottom: 25px;">
			<div class="top"></div>
			<div class="content">
			<div class="fl">
			<?php if (have_posts()) : ?>

			<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
			<?php /* If this is a category archive */ if (is_category()) { ?>
			<h3>Viewing category &#8216;<?php echo single_cat_title(); ?>&#8217; </h3>
			
			<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
			<h3>Archive for <?php the_time('F jS, Y'); ?></h3>
			
			<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
			<h3>Archive for <?php the_time('F, Y'); ?></h3
			
			><?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
			<h3>Archive for <?php the_time('Y'); ?></h3>
			
			<?php /* If this is an author archive */ } elseif (is_author()) { ?>
			<? $author_obj = $wp_query->get_queried_object();?>	
			WP_BLOGAUTHORCHUNK
	
			<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
			<h3>Blog Archives</h3>	
			<?php } ?>
			
			</div>

			<div class="clear"></div>
			</div>
			<div class="bot"></div>
			</div>
			
			WP_BLOGIMAGE
	
			<?php while (have_posts()) : the_post(); ?>

			<div class="post">
	
				<div id="post_title">
					<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
					<p class="byline">Posted by <?php the_author_link() ?> on <?php the_time('jS F Y') ?></p>
					<p class="text-content fl">Categories: <?php the_category(', ') ?><?php edit_post_link('Edit', ' &nbsp;|&nbsp; ', ''); ?></p>
					<p class="text-content fr"><?php comments_popup_link('No Comments', '1 Comment', '% Comments'); ?></p>
					<div class="clear"></div>
				</div>
						
				<div class="entry">
				<?php $gotauth=the_author('',false);
				 the_content('<br />Read the rest of this entry'); ?>
<div class="clear"></div>
				</div>
				
			</div>

		<?php endwhile; ?>

			<div class="navigation">
				<div style="width:50%; float: left;"><?php next_posts_link('&laquo; Previous Entries') ?></div>
				<div style="width:50%; float: right;"><?php previous_posts_link('Next Entries &raquo;') ?></div>
				<div class="clear"></div>
			</div>
			
		<?php else : ?>

			<h2>Not Found</h2>
			<div class="message">
				<div class="message-error">Sorry, but you are looking for something that isn't here.</div>
			</div>
			<?php include (TEMPLATEPATH . "/searchform.php"); ?>
	
		<?php endif; ?>

		</div>


<?php get_sidebar(); ?>

	<div class="clear"></div>

	</div>
<?php get_footer(); ?>
