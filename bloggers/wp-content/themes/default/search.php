<?php get_header(); ?>

	<div id="main-content" class="blogs">
		
		<div class="panel-left">
		
			WP_BLOGIMAGE

			<?php if (have_posts()) : ?>

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
		
				<div>Sorry, but you are looking for something that isn't here. Please try another search.</div>
			
			
	
			<?php endif; ?>

		</div>
	
<?php 
	get_sidebar(); 
?>

	<div class="clear"></div>
	</div>
