<?php
//Reverting back to old comments system
// Do not delete these lines
	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
			?>

			<p class="nocomments">This post is password protected. Enter the password to view comments.<p>

			<?php
			return;
		}
	}

	/* This variable is for alternating comment background */
	$oddcomment = 'odd';
?>

<?php //comments_rss_link('Subscribe to Comments RSS Feed'); ?>				


<!-- You can start editing here. -->


<?php if ('open' == $post->comment_status) : ?>

<div id="comment" class="rounded-630-grey block">
	<div class="top"></div>
	
	<div class="content">
	<h2>Your Comment</h2>
	
	<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
	
	<p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>">logged in</a> to post a comment.</p>
	
	<?php else : ?>

	<div class="standardform">
		<div class="formholder">
		
		<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform"><div>

		<?php if ( $user_ID ) : ?>

		<p>Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="Log out of this account">Logout &raquo;</a></p>

		<?php else : ?>
		<dl>
			<dt><label for="author">Your Name <?php if ($req) echo "<span class='red-ast'>*</span>"; ?></label></dt>
			<dd><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" class="textinput" />
			<p class="text-content"><small>Will be shown publicly</small></p>
			</dd>
		
			<dt><label for="email">Your Email <?php if ($req) echo "<span class='red-ast'>*</span>"; ?></label></dt>
			<dd><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" class="textinput" /></dd>
			
			<dt><label for="url">URL:</label></dt>
			<dd><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" class="textinput" /></dd>
		</dl>
		
		<div class="clear"></div>
		
		<?php endif; ?>
		
		<!--<p><small><strong>XHTML:</strong> You can use these tags: <?php echo allowed_tags(); ?></small></p>-->
		
		<dl>
			<dt><label for="form_comment">Comment: <?php if ($req) echo "<span class='red-ast'>*</span>"; ?></label></dt>
			<dd><textarea name="comment" id="form_comment" rows="10" tabindex="4" class="textinput"></textarea></dd>
			
			<dt></dt>
			<dd><?php if (function_exists('show_subscription_checkbox')) { show_subscription_checkbox(); } ?></dd>
			
			<dt></dt>
			<dd><button name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment"><span>Submit Comment</span></button>
			<div class="required"><strong>NOTE:</strong> All fields marked <span class="red-ast">*</span> are required.</div></dd>		
		</dl>
		
		<div class="clear"></div>

		
			<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
			<?php do_action('comment_form', $post->ID); ?>
		
		</div></form>


	<?php endif; // If registration required and not logged in ?>
	</div></div>
	
	</div>
	<div class="bot"></div>
</div>

<?php if ($comments) : ?>
<div id="comments">
	<h2><?php comments_number('No comments yet', 'One comment so far...', '% comments so far...' );?></h2>

	
	<div class="item_list small_list">
	<ul class="commentlist">

	<?php foreach ($comments as $comment) : ?>

		<li class="<?php echo $oddcomment; ?>" id="comment-<?php comment_ID() ?>">
			
			<div class="body">
				<div class="reply">
					<?php if ($comment->comment_approved == '0') : ?>
					<small>Your comment is awaiting moderation.</small>
					<?php endif; ?>
					<?php comment_text() ?>
				</div>
				<p class="text-content underline">
					<cite><?php comment_author_link() ?></cite> &nbsp;|&nbsp;
					<?php comment_date('F jS, Y') ?> at <?php comment_time() ?>
				</p>
			</div>
			<div class="clear"></div>

		</li>

	<?php /* Changes every other comment to a different class */
		if ('odd' == $oddcomment) $oddcomment = "";
		else $oddcomment = 'odd';
	?>	

	<?php endforeach; /* end for each comment */ ?>

	</ul>
	</div>

 <?php else : // this is displayed if there are no comments so far ?>

	<?php if ('open' == $post->comment_status) : ?>
		<!-- If comments are open, but there are no comments. -->

	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments">Comments are closed.</p>

	<?php endif; ?>
<?php endif; ?>
</div>

<?php endif; // if you delete this the sky will fall on your head ?>
