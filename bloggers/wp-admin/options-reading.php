<?php
require_once('admin.php');

$title = __('Reading Settings');
$parent_file = 'options-general.php';

include('admin-header.php');
?>

<div class="wrap">
<h2><?php _e('Reading Settings') ?></h2>
<form name="form1" method="post" action="options.php">
<?php wp_nonce_field('reading-options') ?>
<input type='hidden' name='option_page' value='reading' />
<table class="form-table">
<?php if ( get_pages() ): ?>
<tr valign="top">
<th scope="row"><?php _e('Front page displays')?></th>
<td>
	<p><label>
		<input name="show_on_front" type="radio" value="posts" class="tog" <?php checked('posts', get_option('show_on_front')); ?> />
		<?php _e('Your latest posts'); ?>
	</label>
	</p>
	<p><label>
		<input name="show_on_front" type="radio" value="page" class="tog" <?php checked('page', get_option('show_on_front')); ?> />
		<?php printf(__('A <a href="%s">static page</a> (select below)'), 'edit-pages.php'); ?>
	</label>
	</p>
<ul>
	<li><?php printf(__('Front page: %s'), wp_dropdown_pages("name=page_on_front&echo=0&show_option_none=".__('- Select -')."&selected=" . get_option('page_on_front'))); ?></li>
	<li><?php printf(__('Posts page: %s'), wp_dropdown_pages("name=page_for_posts&echo=0&show_option_none=".__('- Select -')."&selected=" . get_option('page_for_posts'))); ?></li>
</ul>
<?php if ( 'page' == get_option('show_on_front') && get_option('page_for_posts') == get_option('page_on_front') ) : ?>
<div id="front-page-warning" class="updated fade-ff0000">
	<p>
		<?php _e('<strong>Warning:</strong> these pages should not be the same!'); ?>
	</p>
</div>
<?php endif; ?>
</td>
</tr>
<?php endif; ?>
<tr valign="top">
<th scope="row"><?php _e('Blog pages show at most') ?></th>
<td>
<input name="posts_per_page" type="text" id="posts_per_page" value="<?php form_option('posts_per_page'); ?>" size="3" /> <?php _e('posts') ?>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Syndication feeds show the most recent') ?></th>
<td><input name="posts_per_rss" type="text" id="posts_per_rss" value="<?php form_option('posts_per_rss'); ?>" size="3" /> <?php _e('posts') ?></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('For each article in a feed, show') ?> </th>
<td>
<p><label><input name="rss_use_excerpt"  type="radio" value="0" <?php checked(0, get_option('rss_use_excerpt')); ?>	/> <?php _e('Full text') ?></label><br />
<label><input name="rss_use_excerpt" type="radio" value="1" <?php checked(1, get_option('rss_use_excerpt')); ?> /> <?php _e('Summary') ?></label></p>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Encoding for pages and feeds') ?></th>
<td><input name="blog_charset" type="text" id="blog_charset" value="<?php form_option('blog_charset'); ?>" size="20" class="code" /><br />
<?php _e('The character encoding you write your blog in (UTF-8 is <a href="http://developer.apple.com/documentation/macos8/TextIntlSvcs/TextEncodingConversionManager/TEC1.5/TEC.b0.html">recommended</a>)') ?></td>
</tr>
</table>
<p class="submit">
<input type="hidden" name="action" value="update" />
<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
</p>
</form>
</div>
<?php include('./admin-footer.php'); ?>
