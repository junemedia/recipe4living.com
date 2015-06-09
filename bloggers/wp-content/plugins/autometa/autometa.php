<?php
/*
Plugin Name: AutoMeta
Plugin Version: 0.8
Plugin URI: http://boakes.org/autometa?v=0.8
Author URI: http://boakes.org
Plugin Description: Automatically generate and include HTML Meta Tags and Technorati Tags based on the full text of your post.
Plugin Author: Rich Boakes
Plugin License: GPL
*/ 

if ( ! class_exists( 'AutoMeta' ) ) {
	class AutoMeta {

		function stopWords($article) {
			global $autometa;
			$articleWords = explode(" ", $article);
			foreach ($articleWords as $word) {
				$key = array_search($word, $articleWords);
				if (in_array(strtolower(trim($w)), $autometa["common"])) {
					unset($articleWords[$key]);
				}
				if (strlen($word) < $autometa["min_word_length"]) {
					unset($articleWords[$key]);
				}
			}
			return $articleWords;    
		}

		// helps when sorting based on word rank
		function compareScores( $row1, $row2 ) {
   		return $row1['score'] < $row2['score'] ;
		}
		
		function modifyDB() {
			global $wpdb;
			$q = "ALTER TABLE ".$wpdb->posts." ADD FULLTEXT autometa ( post_content, post_title );";
			$result = $wpdb->get_var($q);
		}

		function getRank($terms, $story_id) {
   		global $wpdb;
			$scores[] = array();
			$i=0;
			foreach ($terms as $term) {
				$term=trim(stripslashes($term));
				$q = "SELECT MATCH(post_content, post_title) against ('".$term."') as score FROM ".$wpdb->posts." where id=".$story_id.";";
				$result = $wpdb->get_var($q);
				$scores[$i]['term'] = $term; 
				$scores[$i]['score'] = $result;
				$i++;
			}
			// sort the data before returning it
			uasort($scores,  array('AutoMeta', 'compareScores'));
			return $scores;
		}


		/**
		 * This method takes an array that contains words and their 
		 * similarity scores.  Each word is checked against the threshold
		 * to decide whether it should be included in the final list of
		 * keywords that prepresent the post.
		 */
		function generateKeywordList($scores, $separator = " ") {
			global $autometa;

			$debugPrint = false;

			$tot=0;
			foreach ($scores as $score) {
				$tot += $score['score'];
			}
 			$tValue = $tot /100 * $autometa["score_threshold"];

			$count=0;
			$x = '';
			foreach ($scores as $score) {

				if ($score['score'] > $tValue) {
					if ($x != '') {
						// if there's already a term, add a comma.
						$x .= $separator;
					}
					$count++;
				$x .= $score['term'];
				}

				if ($count == $autometa["max_words"]) break;
			}
	
			return $x;
		}
		
		
		/**
		 * This method checks for the existence of the autometa index option
		 * which is set when the indexes are generated.  If the index option
		 * exists, and is true, then all is well and good.  If the option
		 * is missing or not true, then a debug message is echoed so that
		 * the user can be reminded, and false is returned so that other
		 * layers of code may bork appropriately.
		 */ 
		function checkIndex() {
			$opt = get_option('autometa_index');
			if (isset($opt) && $opt=='true') {
				// the index has been created so
				// can be happy
			} else {
				echo "\n<!-- AutoMeta: No Index! -->\n";
			}
			return $opt;
		}		



		/**
		 * The autometa-data for a particular post is retrieved and if
		 * that data is of zero length (i.e. if it doesn't exist) then
		 * an attemp is made to create it.
		 *
		 * The creation process involves generating a list of all the
		 * important words in the article, by stripping out punctuation
		 * and then removing common "stop-words".
		 *
		 * The remaining words are then ranked, by using the MySQL
		 * full-text word score, and stored as autometa-data with the post.
		 */
		function updateAutoMetaData($id) {
		   global $autometa;
			
			$current = get_post_meta($id, $autometa["name"]);
		
			if (!isset($current) || (sizeof($current)==0)) {
				//prepare the content
				// $article = strip_tags($post->post_content);
				$article = strip_tags( $_POST['content'] );
		
				$article = str_replace($autometa["quotes"], " ", $article);
				$article = strtolower($article);
		
				$stopped = $this->stopWords($article, $stopwords);
				$uniques = array_unique($stopped);
				$ranked = $this->getRank($uniques, $id);
				$keywords = $this->generateKeywordList($ranked);
					
				$meta_exists=update_post_meta($id, $autometa["name"], $keywords);
				if(!$meta_exists) {
					add_post_meta($id, $autometa["name"], $keywords);	
				}
			}
		}

		function echoTechnoratiTags($head="", $tail="", $prefix="", $suffix="", $notags="<p>Not tagged yet.</p>") {
			global $am;
			$am->_echoTechnoratiTags($head, $tail, $prefix, $suffix, $notags);
		}

		/**
		 * This function will generate a list of technorati tags, wrapping
		 * the total output withing the head and tail parameters.  Each tag
		 * that is generated is itself wrapped in a prefix and suffix.  This
		 * shoudl provide enough flexibiity so that visible tag output can be
		 * tailored to any situation.
		 * 
		 * example 1:
		 *   AutoMeta::techtaglist("<h1>Technorati Tags</h1>", "", "<p>","</p>");
		 * example 2:
		 *   AutoMeta::techtaglist("<ol>", "</ol>", "<li>","</li>");
		 */
		function _echoTechnoratiTags($head="", $tail="", $prefix="", $suffix="", $notags="<p>Not tagged yet.</p>") {
			// do not show if not page or post - because it messes
			// up rankings of archive and front pages
			if (!is_page() && !is_single()) return;

			global $post, $autometa, $autometa_technorati_shown;

			// ensure that technorati tags are not printed
			// twice for the same article (which can happen
			// if the user or theme author makes an explicit
			// call to display the content.
			if (isset($autometa_technorati_shown) && ($autometa_technorati_shown)) return;
			$autometa_technorati_shown = true;

			echo "\n<!-- AutoMeta Plugin for Wordpress -->\n";
			$tags = $this->_getTagArray();
			if ($tags) {
				echo $head;
				foreach($tags as $tag) {
					echo $prefix;
					echo '<a href="http://technorati.com/tag/';
					echo rawurlencode($tag);
					echo '" rel="tag">' . $tag . "</a>";
					echo $suffix;
				}
				echo $tail;
			} else {
				echo $notags;
 			}
		}

		function getTagArray() {
			global $am;
			return $am->_getTagArray();
		}

		/**
		 * Retrieve the autometa-data entries as an array of 
		 * words that are ready to be used.
		 */
		function _getTagArray() {
			global $post, $autometa;
			$tagarrays = get_post_meta($post->ID, $autometa["name"]);
			if ($tagarrays) {
				$result = array();
				foreach($tagarrays as $tagstring) {
					$exploded = explode(" ", $tagstring);
					foreach($exploded as $tag) {
						$tag = str_replace($autometa["quotes"], " ", $tag);
						$result[]=trim($tag);
					}
				}
			}
			return $result;
		}

		/**
		 *	This function controls the inclusion of technorati tags
		 * when a post is displayed.  If called directly using:
		 * AutoMeta::includeTechnoratiTags()
		 * it will insert an unordered list of technorati tags directly
		 * into the page.  It takes one parameter, which if set to true
		 * will include the same list, however, it will wrap the list in
		 * a hidden div tag, so that the tags are not visible.
		 *
		 * this is a "private" function and should must not be called by
		 * anyone wishing to insert tags in their post - for that purpose
		 * use echoTechnoratiTags().
		 */
		function includeTechnoratiTags($hidden = false) {
			global $am;
			$am->_includeTechnoratiTags($hidden);
		}

		// private version of the above with self reference.
		function _includeTechnoratiTags($hidden = false) {
			global $autometa_technorati_shown, $am;
			if (isset($autometa_technorati_shown)) return;
			if ($hidden) { echo '<div style="visibility:hidden;"'; }
			$this->_echoTechnoratiTags(
				'<div class="technorati"><ul>', '</ul></div>',
				'<li>', '</li>'
			);
			if ($hidden) { echo '</div>'; }
		}


		function echoMetaTags() {
			// do not show if not page or post - because it messes
			// up rankings of archive and front pages
			if (!is_page() && !is_single()) return;

			global $post, $autometa;
			echo "\n\n<link rel='".$autometa["name"]."' rev='".$autometa["version"]."' href='http://boakes.org/autometa' />\n";

			$this->checkIndex();
			// There *has* to be a better way to do this!
			// It seems inefficient to retrieve the post
			// in this manner.
			the_post();
			
			$tags="";
			$sep="";
			$tagarrays = get_post_meta($post->ID, $autometa["name"]);
			if ($tagarrays) {
				foreach($tagarrays as $tagstring) {
					$exploded = explode(" ", $tagstring);
					foreach($exploded as $tag) {
					   $tags .= $sep;
					   $tags .= str_replace("-", " ", $tag);
						$sep = ", ";
					}
				}
				echo '<meta name="keywords" content="' . $tags . '" />';
			} else {
				echo "<!-- no keywords available for meta tag -->";
			}

			// There *has* to be a better way to do this!
			// It seems inefficient to retrieve the post
			// in this manner.
			rewind_posts();
			
		}

		function addAutometaToFeed($the_list, $type='default') {
			global $post;
			$tags = $this->_getTagArray();
			$the_list .= "\n\t<!-- AutoMeta Start -->";
   		foreach ($tags as $tag) {
				if ('rdf' == $type) {
					$the_list .= "\n\t<dc:subject>$tag</dc:subject>";
				} else {
					$the_list .= "\n\t<category>$tag</category>";
				}
			}
			$the_list .= "\n\t<!-- AutoMeta End -->\n\t";
			return $the_list;
		}

	} // class

} // endif

if ( ! class_exists( 'AutoMeta_Admin' ) ) {
	class AutoMeta_Admin {

	function check_option($name, $default) {
		global $autometa;
		$x = get_option( $autometa["name"]."_".$name );
		if ($x=="") {
			update_option( $autometa["name"]."_".$name, $default );
			return $default;
		}
		return $x;
	}

   function insertPostUI() {
      global $wpdb,$post;

		//todo find a better way of getting the post id
		if (AutoMeta_Admin::hasMetaData($post->id)) {
			$regen = ' checked="unchecked" ';
			$genmsg = '';
		} else {
			$regen = ' checked="checked" ';
			$genmsg = 'Generate new AutoMeta Tags when saved?';
      }
	   ?>

		<fieldset id="autometadiv" class="dbx-box">
      	<h3 class="dbx-handle">Automatic Metadata</h3>
      	<div id="autometaUI" class="dbx-content">
				<?php
					$tags = $this->_getTagArray();
					if ($tags) {
			   		foreach ($tags as $tag) {
							echo "<em>$tag</em><br />";
						}			
						?>
						<br />
						<label class="selectit"><input type="checkbox" value="closed" name="autometa_generate" />Re-place these tags with a freshly generated set when the post is saved?</label>
						<?php
					} else {
						?>
				<label class="selectit"><input type="checkbox" value="closed" name="autometa_generate" checked />Generate new AutoMeta Tags when saved?</label>
						<?php
					}
				?>
			</div>
      </fieldset>
		
   <?php
   }
	
	function hasMetaData($id) {
		global $autometa;
		$current = get_post_meta($id, $autometa["name"]);
		return (isset($current) && (sizeof($current)>0));
	}

		function addConfigPage() {
			global $wpdb;
			if ( function_exists('add_submenu_page') ) {
				add_submenu_page('plugins.php', 'AutoMeta Configuration', 'AutoMeta', 1, basename(__FILE__), array('AutoMeta_Admin','configPage'));
			}
		} 

		function configPage() {
			global $autometa, $am;

			if ( isset($_POST['submit']) ) {
				check_admin_referer();
				$am->modifyDB();
				update_option('autometa_index', 'true');
			}

			$opt = get_option('autometa_index');

			if (isset($opt) && $opt=='true') {
				?>
				<div class="wrap">
					<h2>AutoMeta Configuration</h2>
					<p>The <a href="http://boakes.org/autometa?v=<?php echo $autometa["version"]; ?>">AutoMeta plugin</a>
						is correctly configured and ready for use, and
						the necessary indexes have been created.</p>
				</div>
				<div class="wrap">
					<h2>Contribute!</h2>
					<p>You (yes you!) can play a very valuable part in the further development of the AutoMeta plugin.
					Your comments and ideas can all help to extend it's capabilities and improve the way it operates so
					please <ol><li>have a think about how it helps, and ...</li><li>how it could be improved, then ...</li>
					<li><?php echo '<a href="http://boakes.org/autometa?v='.$autometa["version"].'">'; ?>
					add your thoughts here</a>.</li></ol></p>
					<p>If you are pleased, delighted, overjoyed or generally gushing with happiness about the functionality that 
					AutoMeta provides you with, then <a href="post.php">blogging about your delight</a> will only serve to reinforce those
					joy joy feelings.  Writing about it and linking to it helps the plugin because a greater awareness 
					increases the quality and quantity of suggestions that can go into it.</p>
				</div>
<!--
// COMING soon ... options!
				<div class="wrap">
					<h2>Options</h2>

					<table class="optiontable"> 
						<tr valign="top"> 
							<th scope="row">Homepage Metadata:</th> 
								<td><input type="text" name="default_meta" size="40"/><br />Default metadata for home page.  It is used for all general pages that do not have automatic tags associated with them.</td> 
</tr> 
						<tr valign="top"> 
							<th scope="row">Capability Switches</th> 
								<td>
														<p><input type="checkbox" name="disable_meta_tags" />Disable meta tag inclusion.</p>
						<p><input type="checkbox" name="disable_technorati_tags" />Disable technorati tag inclusion.</p>
						<p><input type="checkbox" name="disable_footer_credit" />Disable footer credit.</p>

</td> 
</tr> 
</table>
				</div>
				-->
				<?php
			} else {
				?>
				<div class="wrap">
					<h2>AutoMeta Configuration</h2>
					<p>The <a href="http://boakes.org/autometa?v=<?php echo $autometa["version"]; ?>">AutoMeta plugin</a>
						attempts to automatically generate 
						keywords that can be used in html meta tags.  To do this
						it is necessary to index the content of your posts.</p>
					<p>Depending on your machine, the number of posts, and
					   the overall size of each post, this can take anywhere
						from a couple of seconds to tens of minutes (for
						really HUGE blogs that sport many hundreds of long
						articles).</p>  
					<p>Several plugins use this technique so it is possible that
						your blog already has the index, however, if you find that no
						keywords are being suggested, then it is likely that you need
						to add the index.  To do so, press the button.</p>
					<form action="" method="post" id="analytics-conf" style="margin: auto; width: 25em; ">
						<p class="submit"><input type="submit" name="submit" value="Add the index &raquo;" /></p>
					</form>
				</div>
			<?php
			} //endif
		} // end configPage
	} // end class AutoMeta_Admin
} //endif





$autometa["name"] = 'autometa';

$am = new AutoMeta();
$ama = new AutoMeta_Admin();

// Select your preferred stop list language
require_once(dirname(__FILE__). "/stoplist/en.php");

// If you want to place technorati tags somewhere in your blog yourself
// just place the following code in your theme:
//   technorati_tag(false, ", ");
// technorati tags are automatically inserted invisibly in the footer so
// if you have added visible tags youurself using the above code you can
// disable the hidden tags by setting the $autometa["tags_in_footer"]
// variable to false;
// NOTE: this is a historic capability and is not really useful now that
// technorati have stated they don't parse the whole page, so footering is
// a waste of time - the RSS feed is what matters most.
$autometa["tags_in_footer"] = $ama->check_option("tags_in_footer", false);

// if you wish to disable meta keywords in the header, that can be achieved
// by setting $autometa["meta_in_head"] to false;
$autometa["meta_in_head"] = $ama->check_option("meta_in_header", true);

// threshold is a percentage value, only words which score above 
// "threshold%" of the total document score get through.
// Lower the threshold and the more words get through.
$autometa["score_threshold"]= $ama->check_option("wordscore_threshold", 1);

// maxwords is the maximum numberof words that will be included
// so if there are 25 words above the threshold, and maxwords is 10,
// only the highest scoring 10 words will pass.
$autometa["max_words"] = $ama->check_option("word_count", 8);

//$minScore=2;
$autometa["min_word_length"]= $ama->check_option("min_word_length", 3);


$autometa["quotes"] = array( "\x27", "\x22", "\x60", "\t","\n","\r"," ",",",".","/","¬","#",";",":","@","~","[","]","{","}","=","-","+",")","(","*","&","^","%","$","£","<",">","?","!" );

// including the version in the options allows for easier upgrades
// later, i.e. should the data format change, the last version can
// be checked and the appropriate modifications made.
$autometa["version"]  = $ama->check_option("version", "0.8");

// when rss/atom/rdf etc output is genereated, call the autometa
// method that appends the metadata to the list of categories.
add_filter('the_category_rss',	array(&$am, 'addAutometaToFeed'));

// whenever the post is edited, published or saved, refresh the tags
add_action('edit_post',		array(&$am, 'updateAutoMetaData'));
add_action('publish_post',	array(&$am, 'updateAutoMetaData'));
add_action('save_post',		array(&$am, 'updateAutoMetaData'));

if ($autometa["meta_in_head"]) {
	add_action('wp_head', 	array(&$am, 'echoMetaTags'));
}

if ($autometa["tags_in_footer"]) {
	add_action('wp_footer', array(&$am, '_includeTechnoratiTags'));
}

// This is the start of the code for the admin ui enhancements which 
// will make the whole experience "clickable" without the user needing
// to configure this code at all.
//
add_action('admin_menu', 			array(&$ama,'addConfigPage'));
//add_action('dbx_post_sidebar',	array('AutoMeta_Admin', 'insertPostUI'));

?>