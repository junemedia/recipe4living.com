#!/usr/bin/php
<?php

		$startTime = time();

		define('NL',"\n");
		define('TEST',false);

		define('HTDOCSPATH','/var/www/html/');
		define('MAINURL','http://www.recipe4living.com/');
		define('STARTURL',MAINURL.'home/');
		define('IMAGESPATH',HTDOCSPATH.'assets/recipe4living/itemimages/');
		define('LIBPATH',HTDOCSPATH.'shared/lib/');

		require_once 'simple_html_dom.php';

		require_once HTDOCSPATH.'config.php';
		require_once 'HTMLPurifier.auto.php';
		require_once LIBPATH.'BluApplication.php';
		require_once LIBPATH.'Cache.php';
		require_once LIBPATH.'Database.php';
		require_once LIBPATH.'Text.php';
		require_once LIBPATH.'Utility.php';

		define('CACHE', false);
		define('DEBUG', false);
		define('DEBUG_INFO', false);
		define('BLUPATH_BASE', HTDOCSPATH);

		$config = new Config;
		$db = Database::getInstance($config->databaseHost,$config->databaseUser,$config->databasePass,$config->databaseName);

		truncateTables();

		$categoryMetaGroupId = getMetaGroupId('Categories');
		$sectionMetaValueId = insertMetaValue($categoryMetaGroupId,'Recipes');
		$ingredientMetaGroupId = getMetaGroupId('Ingredients');


		$html = file_get_html(STARTURL);
		// scrape categories
		$categories = array();
		foreach($html->find('ul.menuNav li a') as $navLink) {
			$categortTitle = cleanString($navLink->innertext);
			$categoryMetaValueId = insertMetaValue($categoryMetaGroupId,$categortTitle);
			$categoryLinkExploded = explode('/', $navLink->href);
			$lastPart = array_pop($categoryLinkExploded);
			$categoryIdR4LArr = explode('-', $lastPart);
			$categoryId = reset($categoryIdR4LArr);
			$categories[] = array('title'=>$categortTitle,'url'=>$navLink->href,'categoryMetaValueId'=>$categoryMetaValueId,'r4lid'=>$categoryId);
		}
		$html->__destruct();
		unset($html);
		// loop thru categories
		$totalRecipeCount = 0;
		for($i=0;$i<count($categories);$i++) {
			$recipes = array();
			$pageUrl = MAINURL.$categories[$i]['url']; // e.g. http://www.recipe4living.com/Recipes/10-Appetizers.aspx
			$page = 0;
			$recipeCount = 0;
			do {
				$page++;
				echo 'Category "'.$categories[$i]['title'].'" (Page #'.$page.') : '.$pageUrl.NL;
				$thisPageUrl = 'http://www.recipe4living.com/Recipes/Category.aspx?menuId='.$categories[$i]['r4lid'].'&page='.$page;
				echo 'GETTING URL '.$thisPageUrl.NL;
				$categoryPageHtml = file_get_html($thisPageUrl);
				// scrape recipe links in given category
				$j = 0;
				foreach($categoryPageHtml->find('div#categoryTable table#sorty tbody#tablebody tr td.col2 h6 a') as $recipeLink) {
					$recipeTitle = $recipeLink->innertext; 
					$recipeUrl = MAINURL.preg_replace('/^\//','',$recipeLink->href); // e.g. /Recipes/Recipe.aspx?id=50422
					$recipeCount++;
					$memoryUsage = memory_get_usage(true);
					$memoryPeakUsage = memory_get_peak_usage(true);
					$timeDiff = time() - $startTime;
					echo NL.'##############################################################################################################################################'.NL;
					echo 'Memory usage: '.$memoryUsage.' Bytes = '.round($memoryUsage/1000000,1).' MB'.NL;
					echo 'Memory peak usage: '.$memoryPeakUsage.' Bytes = '.round($memoryPeakUsage/1000000,1).' MB'.NL;
					echo 'Time elapsed: '.$timeDiff.' seconds = '.round($timeDiff/60,2).' minutes = '.round($timeDiff/60/60,2).' hours'.NL;
					echo '##############################################################################################################################################'.NL;
					echo ''.$categories[$i]['title'].' ('.$page.')'.NL;
					echo ''.(++$totalRecipeCount).' / '.$recipeCount.' : '.$recipeTitle.' : '.$recipeUrl.NL;
					echo '##############################################################################################################################################'.NL;
					$recipeId = getArticleIdFromUrl($recipeUrl);
					echo 'Recipe ID: '.$recipeId.NL;
					$recipeHits = '';
					if($recipeHitsHtml = $categoryPageHtml->find('div#categoryTable table#sorty tbody#tablebody tr td.col3',$j)) {
						$recipeHits = cleanString($recipeHitsHtml->plaintext);
					}
					echo 'Recipe Hits: '.$recipeHits.NL;
					$recipeRating = '';
					if($recipeRatingHtml = $categoryPageHtml->find('div#categoryTable table#sorty tbody#tablebody tr td.col4',$j)) {
						$recipeRating = cleanString($recipeRatingHtml->plaintext);
					}
					echo 'Rating: '.$recipeRating.NL;
					$j++;



					$recipePageHtml = file_get_html($recipeUrl);

					$metaTitle = '';
					if($metaTitleHtml = $recipePageHtml->find('title',0)) {
						$metaTitle = cleanString(preg_replace('/ \| Recipe4Living\s*$/','',$metaTitleHtml->innertext));
						echo 'Meta Title: '.$metaTitle.NL;
					}
					$metaKeywords = '';
					if($metaKeywordsHtml = $recipePageHtml->find('meta[name=keywords]',0)) {
						$metaKeywords = cleanString($metaKeywordsHtml->content);
						echo 'Meta Keywords: '.$metaKeywords.NL;
					}
					$metaDescription = '';
					if($metaDescriptionHtml = $recipePageHtml->find('meta[name=description]',0)) {
						$metaDescription = cleanString($metaDescriptionHtml->content);
						echo 'Meta Description: '.$metaDescription.NL;
					}
					$title = '';
					if($titleHtml = $recipePageHtml->find('div.articleHeadline span',0)) {
						$title = cleanString($titleHtml->plaintext);
						echo 'Title: '.$title.NL;
					}
					$date = '';
					if($dateHtml = $recipePageHtml->find('div.articleDate span',0)) {
						$date = date('Y-m-d',strtotime(cleanString($dateHtml->plaintext)));
						echo 'Date: '.$date.NL;
					}
					$author = '';
					if($authorHtml = $recipePageHtml->find('div.articleByline span',0)) {
						if($author = cleanString($authorHtml->plaintext)) {
							echo 'Author: '.$author.NL;
						}
					}
/*
					$authorsLocation = '';
					if($authorsLocationHtml = $recipePageHtml->find('div.articleSource span',0)) {
						if($authorsLocation = cleanString($authorsLocationHtml->plaintext)) {
							echo 'Author\'s Location: '.$authorsLocation.NL;
						}
					}
*/
					$authorsLocation = '';
					if($authorsLocationHtml = $recipePageHtml->find('div.articleSource span',0)) {
						if($authorsLocation = cleanString($authorsLocationHtml->plaintext)) {
							if(empty($author)) {
								$author = $authorsLocation;
								$authorsLocation = '';
								echo 'Author: '.$author.NL;
							}
							else {
								echo 'Author\'s Location: '.$authorsLocation.NL;
							}
						}
					}
					$submittedBy = '';
					if($submittedByHtml = $recipePageHtml->find('div#submittedBy div#submittedUser a',0)) {
						$submittedBy = cleanString($submittedByHtml->innertext);
						echo 'Submitted By: '.$submittedBy.NL;
					}
					$submittedByLocation = '';
					if($submittedByHtml = $recipePageHtml->find('div#submittedBy div#submittedUser',0)) {
						$submittedByLocationHtml = $submittedByHtml->plaintext;
						if(preg_match('/\s*from\s*/',$submittedByLocationHtml)) {
							$submittedByLocation = cleanString(trim(preg_replace('/^.*\s*from\s*/s','',$submittedByLocationHtml)));
							echo 'Submitted By Location: '.$submittedByLocation.NL;
						}
					}
					if(empty($submittedBy) && !empty($author)) {
						$submittedBy = $author;
						$submittedByLocation = $authorsLocation;
					}

					$imageUrl = '';
					$imageCaption = '';
					if($imageUrlHtml = $recipePageHtml->find('div#imgwcaption img',0)) {
						//$imageUrl = preg_replace('/[^\/]*\/$/s','',MAINURL,substr_count($imageUrlHtml->src,'../')).preg_replace('/^\.\.\//','',$imageUrlHtml->src);
						$imageUrl = MAINURL.preg_replace('/^\//','',rawurlencode($imageUrlHtml->src));
						echo 'Image URL: '.$imageUrl.NL;
						if($imageCaptionHtml = $recipePageHtml->find('div#imgwcaption img p',0)) {
							if($imageCaptionStr = cleanString($imageCaptionHtml->plaintext)) {
								$imageCaption = $imageCaptionStr;
								echo 'Image Caption: '.$imageCaption.NL;
							}
						}
					}
					$intro = '';
					foreach($recipePageHtml->find('div#articleIntro p') as $introHtml) {
						$intro .= cleanString($introHtml->innertext);
						$intro = trim(preg_replace('/<a.*<\/a>/s','',$intro)); // remove all hyperlinks and strip all tags
					}
					echo 'Intro: '.$intro.NL;
					$ingredients = array();
					if($ingredientsHtml = $recipePageHtml->find('div#ingredients p',0)) {
						$ingredientsSplit = preg_split('/\s*<br \/>\s*/',$ingredientsHtml->innertext);
						$ingredients = array();
						echo 'Ingredients: '.NL;
						foreach($ingredientsSplit as $ingredient) {
							if($ingredientItem = preg_replace('/^\s*\-\s*/','',cleanString($ingredient,false,false,false))) {
								$ingredients[] = $ingredientItem;
								echo ' - '.$ingredientItem.NL;
							}
						}
					}
					$directions = '';
					foreach($recipePageHtml->find('div#directions p') as $directionsHtml) {
						$directions .= $directionsHtml->innertext;
					}

					// sometimes links are part of directions which is a big hassle
					if(preg_match('/More (Recipes |)Like This/i',$directions)) {
						$directions = preg_replace('/(<p><font.*>|<font.*>|<h5>|<p>|)\s*More (Recipes |)Like This.*$/is','',$directions); // cut off links from the end of directions
						$links = array();
						foreach($recipePageHtml->find('div#directions p a') as $linkHtml) {
							$linkRecipeId = getArticleIdFromUrl($linkHtml->href);
							$links[] = array('title'=>cleanString($linkHtml->innertext),'url'=>MAINURL.cleanString($linkHtml->href),'recipeId'=>$linkRecipeId);
						}
					}
					$directionsSections = array();
					if($directionsSplit = preg_split('/\s*(<br \/>){1,}\s*/',$directions)) {
						foreach($directionsSplit as $id=>$directionsSplitPart) {
							$directionsSplitPartSubstr = trim(substr($directionsSplitPart,0,50)); // search for subheading only in first few characters
							//if(preg_match('/[A-Za-z0-9]{1,}\:/s',$directionsSplitPartSubstr)) {
							if(preg_match('/(Yield|Yields)\:/i',$directionsSplitPartSubstr)) {
								$directionsSectionTitle = preg_replace('/\:.*$/s','',$directionsSplitPart);
								$directionsSectionText = preg_replace('/^.*\:\s*/s','',$directionsSplitPart);
								if($directionsSectionTitle=='Yields') {
									$directionsSectionTitle = 'Yield';
								}
								$directionsSections[] = array('title'=>$directionsSectionTitle,'text'=>$directionsSectionText);
								unset($directionsSplit[$id]);
							}
						}
/*
						// remove sections from directions
						if(!empty($directionsSections)) {
							$directions = '';
							$directionsSplitPartCount = 0;
							foreach($directionsSplit as $directionsSplitPart) {
								$directions .= $directionsSplitPart;
								if(++$directionsSplitPartCount<count($directionsSplit)) {
									$directions .= '<br /><br />';
								}
							}
						}
*/
					}
					$directions = trim(preg_replace('/\s*<br \/>\s*/',"<br />\n",cleanString($directions,true,false,true)));


					$links = array();
					if(empty($links)) {
						$links = array();
						foreach($recipePageHtml->find('div#directions h5 a') as $linkHtml) {
							$linkRecipeId = getArticleIdFromUrl($linkHtml->href);
							$links[] = array('title'=>cleanString($linkHtml->innertext),'url'=>MAINURL.cleanString($linkHtml->href),'recipeId'=>$linkRecipeId);
						}
					}
					echo 'Directions: '.$directions.NL;
					if(!empty($directionsSections)) {
						foreach($directionsSections as $directionsSection) {
							echo 'SECTION: '.$directionsSection['title'].': '.$directionsSection['text'].NL;
						}
					}

					if(!empty($links)) {
						echo 'Links: '.NL;
						foreach($links as $link) echo ' - '.$link['title'].' - ID:'.$link['recipeId'].' - '.$link['url'].NL;
					}

					$servingSize = '';
					if($servingSizeHtml = $recipePageHtml->find('div#articleBody div.pad div.articleBodyContent div.pad p span#servings',0)) {
						$servingSize = cleanString($servingSizeHtml->plaintext);
						echo 'Serving Size: '.$servingSize.NL;
					}

					$preparationUnit = '';
					if($preparationTimeHtml = $recipePageHtml->find('div#articleBody div.pad div.articleBodyContent div.pad p span#preperationtime',0)) {
						$preparationTime = trim(cleanString(utf8_decode($preparationTimeHtml->plaintext)));
						echo 'Preparation Time: '.$preparationTime.NL;
						if($preparationUnitHtml = $preparationTimeHtml = $recipePageHtml->find('div#articleBody div.pad div.articleBodyContent div.pad p span#preperationunit',0)) {
							$preparationUnit = cleanString(utf8_decode($preparationUnitHtml->plaintext));
							echo 'Preparation Unit: '.$preparationUnit.NL;
						}
					}

					$cookingUnit = '';
					if($cookingTimeHtml = $recipePageHtml->find('div#articleBody div.pad div.articleBodyContent div.pad p span#cookingtime',0)) {
						$cookingTime = trim(cleanString(utf8_decode($cookingTimeHtml->plaintext)));
						echo 'Cooking Time: '.$cookingTime.NL;
						if($cookingUnitHtml = $cookingTimeHtml = $recipePageHtml->find('div#articleBody div.pad div.articleBodyContent div.pad p span#cookingunit',0)) {
							$cookingUnit = cleanString(utf8_decode($cookingUnitHtml->plaintext));
							echo 'Cooking Unit: '.$cookingUnit.NL;
						}
					}
					
					$recipePageHtml->__destruct();
					unset($recipePageHtml);

					if(!empty($submittedBy) && (preg_match('/^[a-z0-9_]{3,}$/i',$submittedBy) || preg_match('/@/',$submittedBy))) {
						$authorUserId = getUserId($submittedBy);
						if($authorUserId && !empty($submittedByLocation)) {
							$query = 'UPDATE users SET location="'.Database::escape($submittedByLocation).'" WHERE id='.(int)$authorUserId;
							$db->setQuery($query);
							$db->query();
						}
					}
					else {
						$authorUserId = 0;
					}

					if(!empty($recipeId)) {
						$query = 'SELECT id FROM articles WHERE oldArticleId='.(int)$recipeId;
						$db->setQuery($query);
						$db->query();
						if($newRecipeId = $db->loadResult()) { // if article already exists
							insertArticleMetaValueRelationship($newRecipeId,$categoryMetaGroupId,$categories[$i]['categoryMetaValueId']);
							continue;
						}
					}
					$slug = Utility::slugify($title);
					$query = 'SELECT COUNT(*) FROM articles WHERE slug="'.Database::escape($slug).'" OR slug RLIKE "^'.str_replace('_','[[.underscore.]]',$slug).'[[.underscore.]][0-9]{1,}$"';
					$db->setQuery($query);
					$db->query();
					if($slugCount = $db->loadResult()) {
						$slug = $slug.'_'.($slugCount+1);
					}
					$query = 'INSERT INTO articles SET 
								type="recipe",
								title="'.Database::escape($title).'",
								author='.(int)$authorUserId.',
								body="'.Database::escape($directions).'",
								teaser="'.Database::escape($intro).'",
								keywords='.(!empty($metaKeywords)?'"'.Database::escape($metaKeywords).'"':'NULL').',
								description='.(!empty($metaDescription)?'"'.Database::escape($metaDescription).'"':'NULL').',
								date="'.Database::escape($date).'",
								live=1,
								featured=0,
								slug="'.Database::escape($slug).'",
								oldArticleId="'.(isset($recipeId)?Database::escape($recipeId):'NULL').'"
								';
					$db->setQuery($query);
					$db->query();
					$articleId = $db->getInsertID();
					insertArticleMetaValueRelationship($articleId,$categoryMetaGroupId,$sectionMetaValueId);
					insertArticleMetaValueRelationship($articleId,$categoryMetaGroupId,$categories[$i]['categoryMetaValueId']);
					if(!empty($ingredients)) {
						$ingredientCount = 0;
						foreach($ingredients as $ingredient) {
							$ingredientCount++;
							$ingredientMetaValueId = insertMetaValue($ingredientMetaGroupId,$ingredient,$ingredientCount);
							insertArticleMetaValueRelationship($articleId,$ingredientMetaGroupId,$ingredientMetaValueId);
						}
					}

					if(!empty($directionsSections)) {
						$sectionCount = 0;
						foreach($directionsSections as $directionsSection) {
							$sectionCount++;
							$directionsSectionMetaGroupId = getMetaGroupId($directionsSection['title']);
							$directionSectionMetaValueId = insertMetaValue($directionsSectionMetaGroupId,$directionsSection['text']/*,$sectionCount*/);
							insertArticleMetaValueRelationship($articleId,$directionsSectionMetaGroupId,$directionSectionMetaValueId);
						}
					}

					$sequence = 0;
					foreach($links as $link) {
						$sequence++;
						$query = 'REPLACE INTO articleRelationships SET
									articleId='.$articleId.',
									relatedOldArticleId="'.Database::escape($link['recipeId']).'",
									sequence='.$sequence.'
									';
						$db->setQuery($query);
						$db->query();
					}
					if(!empty($imageUrl)) {
						$fileBasename = basename(urldecode($imageUrl));
						$newFile = IMAGESPATH.$fileBasename;
						// uncomment following if statement if you don't want to overwrite existing images
						//if(file_exists($newFile)) {
						//	$fileExt = pathinfo($fileBasename,PATHINFO_EXTENSION);
						//	$fileBasename = basename($fileBasename,$fileExt).time().'.'.$fileExt;
						//	$newFile = IMAGESPATH.$fileBasename;
						//}
						$query = 'INSERT INTO articleImages SET
									articleId='.$articleId.',
									filename="'.Database::escape($fileBasename).'",
									title='.(!empty($imageCaption)?'"'.Database::escape($imageCaption).'"':'null').',
									description=NULL,
									minidescription=NULL,
									sequence=0
									';
						$db->setQuery($query);
						$db->query();
						// image download
						$copyResult = copy($imageUrl,$newFile);
					}
					if(!empty($recipeHits)) {
						$query = 'INSERT INTO articleViews SET
									articleId='.$articleId.',
									views='.$recipeHits.',
									date="0000-00-00"
									';
						$db->setQuery($query);
						$db->query();
					}
/*
					$query = 'INSERT INTO articleRatings SET
								articleId='.$articleId.',
								userId=0,
								rating='.(int)$recipeRating.',
								date="0000-00-00";
								';
					$db->setQuery($query);
					$db->query();
*/
					$query = 'INSERT INTO articleVotes SET
								articleId='.$articleId.',
								votes='.(int)$recipeRating.'
								';
					$db->setQuery($query);
					$db->query();

					if($authorUserId===0) { // only if the user is unknown
						if(!empty($submittedBy)) {
							$authorMetaGroupId = getMetaGroupId('Author');
							$authorMetaValueId = insertMetaValue($authorMetaGroupId,$submittedBy);
							insertArticleMetaValueRelationship($articleId,$authorMetaGroupId,$authorMetaValueId);
						}
						if(!empty($submittedByLocation)) {
							$authorsLocationMetaGroupId = getMetaGroupId('Author\'s Location');
							$authorsLocationMetaValueId = insertMetaValue($authorsLocationMetaGroupId,$submittedByLocation);
							insertArticleMetaValueRelationship($articleId,$authorsLocationMetaGroupId,$authorsLocationMetaValueId);
						}
					}

					if(!empty($servingSize)) {
						$servingSizeMetaGroupId = getMetaGroupId('Serving Size');
						$servingSizeMetaValueId = insertMetaValue($servingSizeMetaGroupId,$servingSize);
						insertArticleMetaValueRelationship($articleId,$servingSizeMetaGroupId,$servingSizeMetaValueId);
					}

					if(!empty($preparationTime)) {
						$preparationTimeMetaGroupId = getMetaGroupId('Preparation Time');
						$preparationTimeMetaValueId = insertMetaValue($preparationTimeMetaGroupId,utf8_encode($preparationTime.(!empty($preparationUnit)?' '.$preparationUnit:'')));
						insertArticleMetaValueRelationship($articleId,$preparationTimeMetaGroupId,$preparationTimeMetaValueId);
					}

					if(!empty($cookingTime)) {
						$cookingTimeMetaGroupId = getMetaGroupId('Cooking Time');
						$cookingTimeMetaValueId = insertMetaValue($cookingTimeMetaGroupId,utf8_encode($cookingTime.(!empty($cookingUnit)?' '.$cookingUnit:'')));
						insertArticleMetaValueRelationship($articleId,$cookingTimeMetaGroupId,$cookingTimeMetaValueId);
					}

					$commentPageUrl = 'http://www.recipe4living.com/Common/RecipeComments.aspx?id='.$recipeId.'&dummy='.time();
					echo '------------------------------------------------------------------------------------------------'.NL;
					echo $commentPageUrl.NL;
					$commentPageHtml = file_get_html($commentPageUrl);
					// scrape comments
					$commentCount = 0;
					foreach($commentPageHtml->find('table#commenttable tbody.tablecontent tr') as $commentHtml) {
						if(!$commentHtml->find('td',0)) {
							continue; // ???
						}
						if(($cell = $commentHtml->find('td',0)) && isset($cell->colspan) && $cell->colspan=='2') {
							continue; // skip separator
						}
						$user = null;
						$ranking = null;
						$datetime = null;
						$comment = null;
						$userRating = null;
						foreach($commentHtml->find('td') as $commentCellHtml) {
							if($commentCellHtml->class=='commentcell') {
								$userRatingHtml = $commentCellHtml->find('ul.ecicrt',0)->plaintext;
								if(preg_match('/Chef Hat/',$userRatingHtml)) {
									$userRating = preg_replace('/[^0-9]/','',$userRatingHtml);
								}
							}
							else {
								$comment = '';
								foreach($commentCellHtml->find('p') as $p) {
									if($p->class=='commentinfo') {
										$user = $p->find('a',0)->innertext;
										$commentinfoHtml = trim($p->innertext);
										$commentinfoHtmlSplit = preg_split('/\s*<br \/>\s*/',$commentinfoHtml);
										if(preg_match('/Ranking\:/',$commentinfoHtmlSplit[1])) {
											$ranking = preg_replace('/Ranking\:\s*/','',$commentinfoHtmlSplit[1]);
										}
										$datetime = date('Y-m-d H:i:s',strtotime($commentinfoHtmlSplit[2]));
									}
									else {
										$comment .= cleanString($p->plaintext,true,false,true);
									}
								}
							}
						}
						echo '------------------------------------------------------------------------------------------------'.NL;
						echo '#'.(++$commentCount).NL;
						echo 'User: '.$user.NL;
						echo 'Ranking: '.$ranking.NL;
						echo 'Date and time: '.$datetime.NL;
						echo 'Comment: '.$comment.NL;
						echo 'User rating: '.$userRating.NL;

						$userId = getUserId($user);
						if(!empty($userId)) {
							$query = 'UPDATE users SET ranking="'.Database::escape($ranking).'" WHERE id='.(int)$userId;
							$db->setQuery($query);
							$db->query();
						}
						$query = 'INSERT INTO comments SET 
									type="review",
									body="'.Database::escape($comment).'",
									objectType="article",
									objectId='.$articleId.',
									userId='.(empty($userId)?'NULL':$userId).',
									date="'.Database::escape($datetime).'",
									live=1,
									username="'.Database::escape($user).'",
									ranking="'.Database::escape($ranking).'"
									';
						$db->setQuery($query);
						$db->query();
						$commentId = $db->getInsertID();
						$query = 'INSERT INTO commentRatings SET 
									commentId='.$commentId.',
									userId='.(empty($userId)?'0':$userId).',
									rating='.$userRating.',
									date="'.Database::escape($datetime).'"
									';
						$db->setQuery($query);
						$db->query();

					}

					$commentPageHtml->__destruct();
					unset($commentPageHtml);

					if(TEST) fgets(STDIN); 


				}
				// scrape link to next page in the same category
				if($pageLink = $categoryPageHtml->find('div#pagination div.pad div.paginationContent ul.pages li.next a',0)) {
					$pageUrl = MAINURL.preg_replace('/^\//','',html_entity_decode($pageLink->href));
				}
				else {
					$pageUrl = false;
				}
				$categoryPageHtml->__destruct();
				unset($categoryPageHtml);
				if(TEST) if($page==1) $pageUrl = false;
			} while($pageUrl);
		}

		$endTime = time();
		$timeDiff = $endTime - $startTime;
		echo NL.NL.NL.'Script executed in '.$timeDiff.' seconds = '.round($timeDiff/60/60,2).' hours'.NL.NL.NL;

	function cleanString($string,$purifyHtml=false,$allowImages=false,$autoParagraph=false) {
		$string = trim(htmlspecialchars_decode(html_entity_decode($string),ENT_QUOTES));
		if($purifyHtml) {
			$string = Text::cleanHTML($string,$allowImages,$autoParagraph);
		}
		else {
			$string = strip_tags($string);
		}
		return $string;
	}
	
	function getArticleIdFromUrl($url) {
		parse_str(parse_url($url,PHP_URL_QUERY),$urlArgs);
		if(isset($urlArgs['id'])) {
			$recipeId = $urlArgs['id'];
		}
		elseif($recipeLinkReplace=preg_replace('/\-[^\s]*$/','',preg_replace('/^[^\s]*\/Recipe\//','',$url))) { // crazy regexp, but it works
			$recipeId = trim($recipeLinkReplace);
		}
		else {
			$recipeId = NULL;
		}
		return $recipeId;
	}

	function getMetaGroupId($group) {
		global $db;
		$slug = Utility::slugify($group);
		$query = 'SELECT id FROM languageMetaGroups WHERE slug="'.Database::escape($slug).'"';
		$db->setQuery($query);
		$db->query();
		$metaGroupId = $db->loadResult();
		if(!$metaGroupId) {
			$query = 'INSERT INTO metaGroups SET id=NULL,internalName="'.Database::escape($group).'"';
			$db->setQuery($query);
			$db->query();
			$metaGroupId = $db->getInsertID();
			$query = 'INSERT INTO languageMetaGroups SET id='.$metaGroupId.',name="'.Database::escape($group).'",slug="'.Database::escape($slug).'"';
			$db->setQuery($query);
			$db->query();
		}
		return $metaGroupId;
	}

	function insertMetaValue($groupId,$value,$sequence=0) {
		global $db;
		$slug = Utility::slugify($value);
		$query = 'SELECT id FROM languageMetaValues WHERE slug="'.Database::escape($slug).'"';
		$db->setQuery($query);
		$db->query();
		$metaValueId = $db->loadResult();
		if(!$metaValueId) {
			$query = 'INSERT INTO metaValues SET
						internalName="'.Database::escape($value).'",
						imageName=NULL,
						sequence='.$sequence.',
						groupId='.(int)$groupId.'
						';
			$db->setQuery($query);
			$db->query();
			$metaValueId = $db->getInsertID();
			$query = 'INSERT INTO languageMetaValues SET
						id='.$metaValueId.',
						lang="EN",
						name="'.Database::escape($value).'",
						description="'.Database::escape($value).'",
						slug="'.Database::escape($slug).'"
						';
			$db->setQuery($query);
			$db->query();
		}
		return $metaValueId;
	}

	function insertArticleMetaValueRelationship($articleId,$groupId,$valueId) {
		global $db;
		$query = 'REPLACE INTO articleMetaValues SET
					articleId='.$articleId.',
					groupId='.(int)$groupId.',
					valueId='.$valueId.',
					rawValue=0
					';
		$db->setQuery($query);
		$db->query();
	}

	function getUserId($user) {
		global $db;
		$query = 'SELECT id FROM users WHERE username="'.Database::escape($user).'"';
		$db->setQuery($query);
		$db->query();
		$userId = $db->loadResult();
		if(!$userId) {
			$query = 'SELECT id FROM users WHERE email="'.Database::escape($user).'"';
			$db->setQuery($query);
			$db->query();
			$userId = $db->loadResult();
		}
		if(!$userId && (preg_match('/^[a-z0-9_]{3,}$/i',$user) || preg_match('/@/',$user))) {
			if(preg_match('/@/',$user)) {
				$query = 'INSERT INTO users SET id=NULL,username="'.Database::escape($user).'",email="'.Database::escape($user).'"';
			}
			else {
				$query = 'INSERT INTO users SET id=NULL,username="'.Database::escape($user).'",email=NULL';
			}
			$db->setQuery($query);
			$db->query();
			$userId = $db->getInsertID();
		}
		return $userId;
	}
	
	function truncateTables() {
/*
TRUNCATE TABLE `articleImages`;
TRUNCATE TABLE `articleMetaValues`;
TRUNCATE TABLE `articleRatings`;
TRUNCATE TABLE `articleRelationships`;
TRUNCATE TABLE `articles`;
TRUNCATE TABLE `articleViews`;
TRUNCATE TABLE `commentRatings`;
TRUNCATE TABLE `comments`;
TRUNCATE TABLE `languageMetaGroups`;
TRUNCATE TABLE `languageMetaValues`;
TRUNCATE TABLE `metaGroups`;
TRUNCATE TABLE `metaValues`;
update users set location=null,ranking=null;
*/
		global $db;
		$query = 'TRUNCATE TABLE `articleImages`;';
		$db->setQuery($query);
		$db->query();
		$query = 'TRUNCATE TABLE `articleMetaValues`;';
		$db->setQuery($query);
		$db->query();
		$query = 'TRUNCATE TABLE `articleVotes`;';
		$db->setQuery($query);
		$db->query();
		$query = 'TRUNCATE TABLE `articleRelationships`;';
		$db->setQuery($query);
		$db->query();
		$query = 'TRUNCATE TABLE `articles`;';
		$db->setQuery($query);
		$db->query();
		$query = 'TRUNCATE TABLE `articleViews`;';
		$db->setQuery($query);
		$db->query();
		$query = 'TRUNCATE TABLE `commentRatings`;';
		$db->setQuery($query);
		$db->query();
		$query = 'TRUNCATE TABLE `comments`;';
		$db->setQuery($query);
		$db->query();
		$query = 'TRUNCATE TABLE `languageMetaGroups`;';
		$db->setQuery($query);
		$db->query();
		$query = 'TRUNCATE TABLE `languageMetaValues`;';
		$db->setQuery($query);
		$db->query();
		$query = 'TRUNCATE TABLE `metaGroups`;';
		$db->setQuery($query);
		$db->query();
		$query = 'TRUNCATE TABLE `metaValues`;';
		$db->setQuery($query);
		$db->query();
		$query = 'update users set location=null,ranking=null';
		$db->setQuery($query);
		$db->query();
	}
	

?>