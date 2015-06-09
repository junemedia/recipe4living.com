<?php get_header(); 
global $fspath;
include_once($fspath."lib/blogs.php");
		$author_obj = $wp_query->get_queried_object();
		//Break from MVC
/*		$wimblogobj=new blogs();
		$bloginfq=mysql_query("SELECT blogID from blogs WHERE blogHosted='".$blog_id."'");
		$bloginf=$wimblogobj->getbloginfo(mysql_result($bloginfq,0,0));
		$hblog=$bloginf[0];
*/
global $related,$relatedlink;
$relatedlink = get_permalink();

$related =  STP_GetCurrentTagSet();
get_sidebar(); ?>

		<div style="float:left; width:440px; ">
		<div id="blogwhite">
		<?
//		
		
		/*if ($hblog['blogImage']) {
		?>
			<img src="<?=$sitepath?>images/blogheader/<?=$hblog['blogImage']?>" alt="<?=$hblog['blogTitle']?>" style="border: 0px; margin: 0px; padding: 0px" usemap="#blogheadmap" />
			<map id="blocheadmap" name="blogheadmap">
			<?=$hblog['blogImageMap']?>
			</map>
		<?
		}else {
		?>
		<div id="orangebox">
			<div class="top"></div>
			<div class="bg">
				<div class="textpad"><span class="introwhite"><?=$hblog['firstname']." ".$hblog['lastname'];?>: <?=$hblog['blogTitle'];?></span></div>
				<div class="padd2">
					<div id="featuredentrepreneur" style="height:auto;">
						<div style="vertical-align:top;float:left; width:125px;"><a href="<?=$sitepath;?>profile-<?=$hblog['username'];?>"><img src="<?=$sitepath;?>userimages/122/<?=$hblog['userImage']?$hblog['userImage']:'default.gif';?>" border="0" class="imgbrd3" style="margin:0px;"/></a></div>
						<div id="featentreptext" style="width:365px;">
							<div class="font14b"><?=$hblog['firstname']." ".$hblog['lastname'];?>: <?=$hblog['blogTitle'];?></div>
							<div style="line-height:14px;"><?=$hblog['blogDescription'];?></div>
						</div>
						<div class="clearer"></div>
					</div>
				</div>
			</div>
			<div class="bot"></div>
		</div>
		<? } */ ?>
		<div class="clearer" style="height:15px;"></div>

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>
				<div style="margin-bottom: 15px;">
				<div class="posttitle2"><h3><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><strong><?php the_title(); ?></strong></a></h3></div>
					<div id="commentarea">
						<div class="blogauthor">Posted by <?php the_author_link() ?> on <?php the_time('F jS, Y') ?>  </div>
						<div class="blogcomm">Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></div>
						</div>
					<div id="blogdesc">
				<?php $gotauth=the_author('',false);
				 the_content('Read the rest of this entry &raquo;'); ?>
				 <div style="clear:both;"></div>
				</div>
			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>
	</div>
	</div>


<?php get_footer(); ?>
