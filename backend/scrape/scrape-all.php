#!/usr/bin/php
<?php
		
		$startCategoryTitle = 'Appetizers';
		$startSubcategoryTitle = NULL;
		$startPage = 1;

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
/*
		if(!isset($startCategoryTitle) && !isset($startPage)) truncateTables();
*/
#####################################################################################################################################################################################
# scrape recipes
#####################################################################################################################################################################################
if(true) {
		$categoriesMetaGroupId = insertMetaGroupId('Top Levels'); // root metaGroup for categories

		$sectionMetaGroupId = insertMetaGroupId('Recipes');
		$sectionMetaValueId = insertMetaValue($categoriesMetaGroupId,'Recipes');
		insertCategoryRelationship($sectionMetaValueId,$sectionMetaGroupId);

		$ingredientMetaGroupId = insertMetaGroupId('Ingredients');


		$html = file_get_html(STARTURL);
		// scrape categories
		$categories = array();
		foreach($html->find('ul.menuNav li a') as $navLink) {
			$categoryTitle = cleanString($navLink->innertext);
			$categoryMetaValueId = insertMetaValue($sectionMetaGroupId,$categoryTitle);
			$categoryLinkExploded = explode('/', $navLink->href);
			$lastPart = array_pop($categoryLinkExploded);
			$categoryIdR4LArr = explode('-', $lastPart);
			$categoryId = reset($categoryIdR4LArr);
			$categories[] = array('title'=>$categoryTitle,'url'=>$navLink->href,'categoryMetaValueId'=>$categoryMetaValueId,'r4lid'=>$categoryId);
		}
		$html->__destruct();
		unset($html);
		// loop thru categories
		$totalRecipeCount = 0;
		$processCategory = false;
		for($i=0;$i<count($categories);$i++) {
			if(isset($startCategoryTitle) && !$processCategory && $startCategoryTitle==$categories[$i]['title']) {
				$processCategory = true;
			}
			if(!$processCategory) {
				continue;
			}
			$recipes = array();
			$pageUrl = MAINURL.$categories[$i]['url']; // e.g. http://www.recipe4living.com/Recipes/10-Appetizers.aspx
			$page = 0;
			$recipeCount = 0;
			$processPage = false;
			do {
				$page++;
				if(isset($startPage) && !$processPage && $startPage==$page) {
					$processPage = true;
				}
				if(!$processPage) {
					continue;
				}
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

					if(!empty($recipeId)) {
						$query = 'SELECT id FROM articles WHERE oldArticleId='.(int)$recipeId;
						$db->setQuery($query);
						$db->query();
						if($newRecipeId = $db->loadResult()) { // if article already exists
							$query = 'UPDATE articleVotes SET
										votes='.(int)$recipeRating.'
										WHERE articleId='.$newRecipeId.'
										';
							$db->setQuery($query);
							$db->query();
							$query = 'UPDATE articleViews SET
										views='.(int)$recipeHits.',
										date=NOW()
										WHERE articleId='.$newRecipeId.'
										';
							$db->setQuery($query);
							$db->query();
							insertArticleMetaValueRelationship($newRecipeId,$sectionMetaGroupId,$categories[$i]['categoryMetaValueId']);
							continue;
						}
					}


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
						$intro = cleanString($introHtml->innertext);
						$intro = strip_tags($intro);
					}
					echo 'Intro: '.$intro.NL;
					$ingredientsAllTogether = '';
					if($ingredientsAllTogetherHtml = $recipePageHtml->find('div#ingredients',0)) {
						$ingredientsAllTogether = $ingredientsAllTogetherHtml->innertext;
						$ingredientsAllTogether = preg_replace('/\s*<h\d>Ingredients<\/h\d>\s*/','',$ingredientsAllTogether);
						$ingredientsAllTogether = preg_replace('/\s*<br \/>\s*/','<br />',$ingredientsAllTogether);
						//$ingredientsAllTogether = cleanString($ingredientsAllTogether,true,false,false);
						//$ingredientsAllTogether = preg_replace('/\n{1,}/','<br />',$ingredientsAllTogether);
					}
					echo 'Ingredients All Together: '.$ingredientsAllTogether.NL;
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
							if(preg_match('/^(Yield|Yields)\:/i',$directionsSplitPartSubstr)) {
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

/*
					if(!empty($recipeId)) {
						$query = 'SELECT id FROM articles WHERE oldArticleId='.(int)$recipeId;
						$db->setQuery($query);
						$db->query();
						if($newRecipeId = $db->loadResult()) { // if article already exists
							insertArticleMetaValueRelationship($newRecipeId,$sectionMetaGroupId,$categories[$i]['categoryMetaValueId']);
							continue;
						}
					}
*/
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
								ingredients="'.Database::escape($ingredientsAllTogether).'",
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

					insertArticleMetaValueRelationship($articleId,$categoriesMetaGroupId,$sectionMetaValueId);
					insertArticleMetaValueRelationship($articleId,$sectionMetaGroupId,$categories[$i]['categoryMetaValueId']);

					if(!empty($ingredients)) {
						$ingredientCount = 0;
						foreach($ingredients as $ingredient) {
							$ingredientCount++;
							$ingredientMetaValueId = insertMetaValue($ingredientMetaGroupId,$ingredient);
							insertArticleMetaValueRelationship($articleId,$ingredientMetaGroupId,$ingredientMetaValueId);
						}
					}

					if(!empty($directionsSections)) {
						$sectionCount = 0;
						foreach($directionsSections as $directionsSection) {
							$sectionCount++;
							$directionsSectionMetaGroupId = insertMetaGroupId($directionsSection['title']);
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
									date=NOW()
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
							$authorMetaGroupId = insertMetaGroupId('Author');
							$authorMetaValueId = insertMetaValue($authorMetaGroupId,$submittedBy);
							insertArticleMetaValueRelationship($articleId,$authorMetaGroupId,$authorMetaValueId);
						}
						if(!empty($submittedByLocation)) {
							$authorsLocationMetaGroupId = insertMetaGroupId('Author\'s Location');
							$authorsLocationMetaValueId = insertMetaValue($authorsLocationMetaGroupId,$submittedByLocation);
							insertArticleMetaValueRelationship($articleId,$authorsLocationMetaGroupId,$authorsLocationMetaValueId);
						}
					}

					if(!empty($servingSize)) {
						$servingSizeMetaGroupId = insertMetaGroupId('Serving Size');
						$servingSizeMetaValueId = insertMetaValue($servingSizeMetaGroupId,$servingSize);
						insertArticleMetaValueRelationship($articleId,$servingSizeMetaGroupId,$servingSizeMetaValueId);
					}

					if(!empty($preparationTime)) {
						$preparationTimeMetaGroupId = insertMetaGroupId('Preparation Time');
						$preparationTimeMetaValueId = insertMetaValue($preparationTimeMetaGroupId,utf8_encode($preparationTime.(!empty($preparationUnit)?' '.$preparationUnit:'')));
						insertArticleMetaValueRelationship($articleId,$preparationTimeMetaGroupId,$preparationTimeMetaValueId);
					}

					if(!empty($cookingTime)) {
						$cookingTimeMetaGroupId = insertMetaGroupId('Cooking Time');
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
//die;
}
#####################################################################################################################################################################################
# scrape recipes subcategories
#####################################################################################################################################################################################
if(true) {

		$sectionMetaGroupId = insertMetaGroupId('Recipes');

		$html = file_get_html(STARTURL);
		// scrape categories
		$categories = array();
		foreach($html->find('ul.menuNav li a') as $navLink) {
			$categoryTitle = cleanString($navLink->innertext);
			$categoryMetaValueId = insertMetaValue($sectionMetaGroupId,$categoryTitle);
			$categoryLinkExploded = explode('/', $navLink->href);
			$lastPart = array_pop($categoryLinkExploded);
			$categoryIdR4LArr = explode('-', $lastPart);
			$categoryId = reset($categoryIdR4LArr);
			$categories[] = array('title'=>$categoryTitle,'url'=>$navLink->href,'categoryMetaValueId'=>$categoryMetaValueId,'r4lid'=>$categoryId);
		}
		$html->__destruct();
		unset($html);
		// loop thru categories
		$totalRecipeCount = 0;
		$processCategory = false;
		$processSubcategory = false;
		for($i=0;$i<count($categories);$i++) {

				if(isset($startCategoryTitle) && !$processCategory && $startCategoryTitle==$categories[$i]['title']) {
					$processCategory = true;
				}
				if(!$processCategory) {
					continue;
				}

				$categoryTitle = $categories[$i]['title'];
				$categoryMetaValueId = $categories[$i]['categoryMetaValueId'];


				echo '##############################################################################################################################################'.NL;
				echo $categories[$i]['title'].NL;
				echo '##############################################################################################################################################'.NL;
				$thisPageUrl = 'http://www.recipe4living.com/Recipes/Category.aspx?menuId='.$categories[$i]['r4lid'].'&page=1';
				$categoryPageHtml = file_get_html($thisPageUrl);
				$subcategoryCount = 0;
				foreach($categoryPageHtml->find('div#maincol div#narrow div.menuNav ul.menuNav li a') as $subcategoryLink) {
					$categoryMetaGroupId = insertMetaGroupId($categoryTitle);
					insertCategoryRelationship($categoryMetaValueId,$categoryMetaGroupId);
					$subcategoryTitle = cleanString($subcategoryLink->innertext);
					$categoryLink = $subcategoryLink->href;
					echo ' '.(++$subcategoryCount).' - '.$subcategoryTitle.' : '.$categoryLink.NL;
					$categoryId = getCategoryIdFromUrl($categoryLink);
					$subcategoryId = getSubcategoryIdFromUrl($categoryLink);

					if(isset($startSubcategoryTitle) && !$processSubcategory && $startSubcategoryTitle==$subcategoryTitle) {
						$processSubcategory = true;
					}
					if(!$processSubcategory) {
						continue;
					}

					$subcategoryMetaValueId = insertMetaValue($categoryMetaGroupId,$subcategoryTitle);

					$page = 0;
					$recipeCount = 0;
					$pageUrl = MAINURL.'Recipes/Category.aspx?menuId='.$categoryId.'&subMenuId='.$subcategoryId.'&page=1';
					do {
						$page++;
						$memoryUsage = memory_get_usage(true);
						echo '----------------------------------------------------------------------------------------------------------------------------------------------'.NL;
						echo '   - '.$categories[$i]['title'].NL;
						echo '   - '.$subcategoryTitle.' ('.$page.')'.NL;
						echo '   - '.$pageUrl.NL;
						echo '  Memory usage: '.round($memoryUsage/1000000,1).' MB'.NL;
						echo '----------------------------------------------------------------------------------------------------------------------------------------------'.NL;
						$subcategoryPageHtml = file_get_html($pageUrl);
						foreach($subcategoryPageHtml->find('div#categoryTable table#sorty tbody#tablebody tr td.col2 h6 a') as $recipeLink) {
							$recipeTitle = $recipeLink->innertext; 
							$recipeUrl = MAINURL.preg_replace('/^\//','',$recipeLink->href);
							$recipeId = getArticleIdFromUrl($recipeUrl);
							echo '        '.(++$recipeCount).' : ID '.$recipeId.' : '.$recipeTitle.' - '.$recipeUrl.NL;
							if($recipeId) {
								$query = 'SELECT id FROM articles WHERE oldArticleId='.(int)$recipeId;
								$db->setQuery($query);
								$db->query();
								if($articleId = $db->loadResult()) {
									echo '          ---> '.$articleId.NL;
									insertArticleMetaValueRelationship($articleId,$categoryMetaGroupId,$subcategoryMetaValueId);
									if(TEST) fgets(STDIN); 
								}
							}
						}
						if($pageLink = $subcategoryPageHtml->find('div#pagination div.pad div.paginationContent ul.pages li.next a',0)) {
							$pageUrl = MAINURL.preg_replace('/^\//','',html_entity_decode($pageLink->href));
						}
						else {
							$pageUrl = false;
						}
						$subcategoryPageHtml->__destruct();
				} while($pageUrl);
					
				}
				$categoryPageHtml->__destruct();
				unset($categoryPageHtml);
		}
//die;
}
#####################################################################################################################################################################################
# scrape articles
#####################################################################################################################################################################################

	$categoriesMetaGroupId = insertMetaGroupId('Top Levels');

	$mainSections = array();
	$mainSections[] = array('title'=>'Hints & Tips','url'=>'http://www.recipe4living.com/HintsAndTips/Default.aspx?id=144','articleType'=>'quicktip');
	$mainSections[] = array('title'=>'A Dash of Fun','url'=>'http://www.recipe4living.com/ADashOfFun/Default.aspx?id=148','articleType'=>'article');
	$mainSections[] = array('title'=>'Thinking Healthy','url'=>'http://www.recipe4living.com/ThinkingHealthy/Default.aspx?id=142','articleType'=>'article');
	foreach($mainSections as $mainSection) {

		$sectionMetaGroupId = insertMetaGroupId($mainSection['title']);
		$sectionMetaValueId = insertMetaValue($categoriesMetaGroupId,$mainSection['title']);
		insertCategoryRelationship($sectionMetaValueId,$sectionMetaGroupId);

		$html = file_get_html($mainSection['url']); // start page
		// scrape categories
		$categories = array();
		foreach($html->find('ul.menuNav li a') as $navLink) {
			$categoryTitle = cleanString($navLink->innertext);
			$categoryMetaValueId = insertMetaValue($sectionMetaGroupId,$categoryTitle);
			$url = preg_replace('/^\//','',html_entity_decode($navLink->href));
			$categories[] = array('title'=>$categoryTitle,'url'=>$url,/**/'categoryMetaValueId'=>$categoryMetaValueId/**/);
		}
		$html->__destruct();
		unset($html);

		// loop thru categories
		$totalRecipeCount = 0;
		for($i=0;$i<count($categories);$i++) {
			$pageUrl = MAINURL.$categories[$i]['url'];
			$page = 0;
			$articleCount = 0;
			do {
				$page++;
				$categoryPageHtml = file_get_html($pageUrl);
				// scrape recipe links in given category
				$j = 0;
				foreach($categoryPageHtml->find('div#categoryTable table#sorty tbody#tablebody tr td.col2 h6 a') as $recipeLink) {
					$recipeTitle = $recipeLink->innertext; 
					$recipeUrl = MAINURL.preg_replace('/^\//','',$recipeLink->href); // e.g. /Recipes/Recipe.aspx?id=50422
					$articleCount++;
					$memoryUsage = memory_get_usage(true);
					$memoryPeakUsage = memory_get_peak_usage(true);
					$timeDiff = time() - $startTime;
					echo NL.'##############################################################################################################################################'.NL;
					echo 'Memory usage: '.$memoryUsage.' Bytes = '.round($memoryUsage/1000000,1).' MB'.NL;
					echo 'Memory peak usage: '.$memoryPeakUsage.' Bytes = '.round($memoryPeakUsage/1000000,1).' MB'.NL;
					echo 'Time elapsed: '.$timeDiff.' seconds = '.round($timeDiff/60,2).' minutes = '.round($timeDiff/60/60,2).' hours'.NL;
					echo '##############################################################################################################################################'.NL;
					echo ''.$mainSection['title'].NL;
					echo ''.$categories[$i]['title'].' ('.$page.')'.NL;
					echo ''.(++$totalRecipeCount).' / '.$articleCount.' : '.$recipeTitle.' : '.$recipeUrl.NL;
					echo '##############################################################################################################################################'.NL;
					$articleId = getArticleIdFromUrl($recipeUrl);
					echo 'Recipe ID: '.$articleId.NL;
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

					if(!empty($articleId)) {
						$query = 'SELECT id FROM articles WHERE oldArticleId='.(int)$articleId;
						$db->setQuery($query);
						$db->query();
						if($newArticleId = $db->loadResult()) { // if article already exists
							$query = 'UPDATE articleVotes SET
										votes='.(int)$recipeRating.'
										WHERE articleId='.$newArticleId.'
										';
							$db->setQuery($query);
							$db->query();
							$query = 'UPDATE articleViews SET
										views='.(int)$recipeHits.',
										date=NOW()
										WHERE articleId='.$newArticleId.'
										';
							insertArticleMetaValueRelationship($newArticleId,$sectionMetaGroupId,$categories[$i]['categoryMetaValueId']);
							continue;
						}
					}


//$recipeUrl = 'http://www.recipe4living.com/Common/Article.aspx?id=61132';
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
						$submittedBy = cleanString($authorHtml->innertext);
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
						if(preg_match('/^\//',$imageUrlHtml->src)) {
							$imageUrl = MAINURL.preg_replace('/^\//','',rawurlencode($imageUrlHtml->src));
						}
						else {
							$imageUrl = rawurldecode($imageUrlHtml->src);
						}
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
						$intro = strip_tags($intro);
					}
					echo 'Intro: '.$intro.NL;

					$articleBody = '';
					$articleImages = array();
					if($articleBodyHtml = $recipePageHtml->find('div.articleBodyContent div.pad',0)) {
						$articleBody = $originalArticleBody = $articleBodyHtml->innertext;
						foreach($recipePageHtml->find('div.articleBodyContent div.pad img') as $articleImage) {
							$articleImageUrl = $articleImage->src;
							if(preg_match('/^\//',$articleImageUrl)) {
								$articleImageUrl = MAINURL.preg_replace('/^\//','',$articleImage->src);
							}
							$articleImages[] = array('title'=>'','url'=>$articleImageUrl);
						}
					}
					$articleBody = cleanString($articleBody,true,true,true);
					$articleBody = preg_replace('/\s*<br \/>\s*/','<br />',$articleBody);
					if(preg_match('/^<p><\/p>/',$articleBody)) {
						$articleBody = preg_replace('/^<p><\/p>/','',$articleBody); // remove first empty paragraph
					}
					echo 'Body: '.$articleBody.NL;

					//print_r($articleImages);

					$recipePageHtml->__destruct();
					unset($recipePageHtml);

/**/
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

					$slug = Utility::slugify($title);
					$query = 'SELECT COUNT(*) FROM articles WHERE slug="'.Database::escape($slug).'" OR slug RLIKE "^'.str_replace('_','[[.underscore.]]',$slug).'[[.underscore.]][0-9]{1,}$"';
					$db->setQuery($query);
					$db->query();
					if($slugCount = $db->loadResult()) {
						$slug = $slug.'_'.($slugCount+1);
					}
					$query = 'INSERT INTO articles SET 
								type="'.$mainSection['articleType'].'",
								title="'.Database::escape($title).'",
								author='.(int)$authorUserId.',
								body="'.Database::escape($articleBody).'",
								teaser="'.Database::escape($intro).'",
								keywords='.(!empty($metaKeywords)?'"'.Database::escape($metaKeywords).'"':'NULL').',
								description='.(!empty($metaDescription)?'"'.Database::escape($metaDescription).'"':'NULL').',
								date="'.Database::escape($date).'",
								live=1,
								featured=0,
								slug="'.Database::escape($slug).'",
								oldArticleId="'.(isset($articleId)?Database::escape($articleId):'NULL').'"
								';
					$db->setQuery($query);
					$db->query();
					$articleId = $db->getInsertID();

					insertArticleMetaValueRelationship($articleId,$categoriesMetaGroupId,$sectionMetaValueId);
					insertArticleMetaValueRelationship($articleId,$sectionMetaGroupId,$categories[$i]['categoryMetaValueId']);

					if(!empty($imageUrl)) {
						$fileBasename = basename(urldecode($imageUrl));
/*
						if(file_exists($newFile)) {
							$fileExt = pathinfo($fileBasename,PATHINFO_EXTENSION);
							$fileBasename = basename($fileBasename,$fileExt).microtime().'.'.$fileExt;
						}
*/
						$newFile = IMAGESPATH.$fileBasename;
						$query = 'INSERT INTO articleImages SET
									articleId='.$articleId.',
									filename="'.Database::escape($fileBasename).'",
									title='.(!empty($imageCaption)?'"'.Database::escape($imageCaption).'"':'null').',
									description=NULL,
									minidescription=NULL,
									sequence=0
									';
						//echo $query.NL;
						$db->setQuery($query);
						$db->query();
						// image download
						copy($imageUrl,$newFile);
					}
					$articleImageCount = 0;
					foreach($articleImages as $articleImage) {
						$fileBasename = basename(urldecode($articleImage['url']));
/*
						if(file_exists($newFile)) {
							$fileExt = pathinfo($fileBasename,PATHINFO_EXTENSION);
							$fileBasename = basename($fileBasename,$fileExt).microtime().'.'.$fileExt;
						}
*/
						$newFile = IMAGESPATH.$fileBasename;
						copy($articleImage['url'],$newFile);
						$query = 'INSERT INTO articleImages SET
									articleId='.$articleId.',
									filename="'.Database::escape($fileBasename).'",
									title=null,
									description=NULL,
									minidescription=NULL,
									sequence='.(++$articleImageCount).'
									';
						//echo $query.NL;
						$db->setQuery($query);
						$db->query();
					}
					if(!empty($recipeHits)) {
						$query = 'INSERT INTO articleViews SET
									articleId='.$articleId.',
									views='.$recipeHits.',
									date=NOW()
									';
						$db->setQuery($query);
						$db->query();
					}
					//$query = 'INSERT INTO articleRatings SET
					//			articleId='.$articleId.',
					//			userId=0,
					//			rating='.(int)$recipeRating.',
					//			date="0000-00-00";
					//			';
					$query = 'INSERT INTO articleVotes SET
								articleId='.$articleId.',
								votes='.(int)$recipeRating.'
								';
					$db->setQuery($query);
					$db->query();

					if($authorUserId===0) { // only if the user is unknown
						if(!empty($submittedBy)) {
							$authorMetaGroupId = insertMetaGroupId('Author');
							$authorMetaValueId = insertMetaValue($authorMetaGroupId,$submittedBy);
							insertArticleMetaValueRelationship($articleId,$authorMetaGroupId,$authorMetaValueId);
						}
						if(!empty($submittedByLocation)) {
							$authorsLocationMetaGroupId = insertMetaGroupId('Author\'s Location');
							$authorsLocationMetaValueId = insertMetaValue($authorsLocationMetaGroupId,$submittedByLocation);
							insertArticleMetaValueRelationship($articleId,$authorsLocationMetaGroupId,$authorsLocationMetaValueId);
						}
					}
/**/
					
					$commentPageUrl = 'http://www.recipe4living.com/Common/RecipeComments.aspx?id='.$articleId.'&dummy='.time();
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

/**/
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
/**/
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
	}

	$endTime = time();
	$timeDiff = $endTime - $startTime;
	echo NL.NL.NL.'Script executed in '.$timeDiff.' seconds = '.round($timeDiff/60/60,2).' hours'.NL.NL.NL;

#####################################################################################################################################################################################
# functions
#####################################################################################################################################################################################

	function getCategoryIdFromUrl($url) {
		$urlReplaced = preg_replace('/^.*\/Recipes\//s','',$url);
		$urlParts = explode('-',$urlReplaced);
		$categoryId = $urlParts[0];
		return $categoryId;
	}

	function getSubcategoryIdFromUrl($url) {
		$urlReplaced = preg_replace('/^.*\/Recipes\//s','',$url);
		$urlParts = explode('-',$urlReplaced);
		$subcategoryId = $urlParts[1];
		return $subcategoryId;
	}

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

	function insertMetaGroupId($group) {
		global $db;
		$slug = substr(Utility::slugify($group),0,255);
		$query = 'SELECT id FROM languageMetaGroups WHERE name="'.Database::escape(substr($group,0,255)).'" AND slug="'.Database::escape($slug).'" ORDER BY id LIMIT 1';
		$db->setQuery($query);
		$db->query();
		$metaGroupId = $db->loadResult();
		if(!$metaGroupId) {
			$query = 'SELECT COUNT(*) FROM languageMetaGroups WHERE slug="'.$slug.'" OR slug RLIKE "^'.str_replace('_','[[.underscore.]]',$slug).'[[.underscore.]][0-9]{1,}$"';
			$db->setQuery($query);
			$db->query();
			if($slugCount = $db->loadResult()) {
				$slug = $slug.'_'.($slugCount+1); // this might result in duplicate slugs if the slug is very long but it should just work for us anyway (hopefully...)
			}
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
		$slug = substr(Utility::slugify($value),0,255);
		$query = 'SELECT mv.id FROM languageMetaValues AS lmv,metaValues AS mv WHERE lmv.id=mv.id AND mv.groupId='.$groupId.' AND lmv.name="'.Database::escape(substr($value,0,255)).'"';
		$db->setQuery($query);
		$db->query();
		$metaValueId = $db->loadResult();
		if(!$metaValueId) {
/*
			$query = 'SELECT COUNT(*) FROM languageMetaValues WHERE slug="'.$slug.'" OR slug RLIKE "^'.str_replace('_','[[.underscore.]]',$slug).'[[.underscore.]][0-9]{1,}$"';
			$db->setQuery($query);
			$db->query();
			if($slugCount = $db->loadResult()) {
				$slug = $slug.'_'.($slugCount+1);
			}
*/
/*
			$query = 'SELECT COUNT(*) FROM languageMetaValues WHERE slug="'.$slug.'" OR slug RLIKE "^'.str_replace('_','[[.underscore.]]',$slug).'[[.underscore.]][0-9]{1,}$"';
			$db->setQuery($query);
			$db->query();
			$slugCount = $db->loadResult();
			if($slugCount==1) {
				$slug = $slug.'_2';
			}
			else {
				$query = 'SELECT slug FROM languageMetaValues WHERE slug="'.$slug.'" OR slug RLIKE "^'.str_replace('_','[[.underscore.]]',$slug).'[[.underscore.]][0-9]{1,}$" ORDER BY id DESC LIMIT 1';
				//echo $query.NL;
				$db->setQuery($query);
				$db->query();
				if($existingSlug = $db->loadResult()) {
					$existingSlugParts = explode('_',$existingSlug);
					$maxSlugNum = array_pop($existingSlugParts);
					//echo NL;echo NL; var_dump($maxSlugNum); echo NL;echo NL;
					$maxSlugNum = (int)$maxSlugNum;
					if($maxSlugNum>0) {
						$slug = $slug.'_'.($maxSlugNum+1);
					}
					else {
						$slug = $slug.'_'.rand(1,9999);
					}
				}
			}
*/
/**/
			$newSlug = $slug;
			$i = 1;
			$loop = 0;
			do {
				if($i>1) {
					$newSlug = $slug.'_'.$i;
				}
				$query = 'SELECT COUNT(*) FROM languageMetaValues WHERE slug="'.$newSlug.'"';
				//echo $query.NL;
				$db->setQuery($query);
				$db->query();
				$slugCount = $db->loadResult();
				$i++;
				$loop++;
			} while($slugCount>0 && $loop<=1000);
			if($loop<1000) {
				$slug = $newSlug;
			}
			else {
				$slug = $slug.'_'.rand(1001,9999);
			}
/**/
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

	function insertCategoryRelationship($valueId,$groupId) {
		global $db;
		$query = 'REPLACE INTO metaHierarchy SET
					valueId='.(int)$valueId.',
					aliasId='.(int)$groupId.',
					aliasType="group_child"
					';
		$db->setQuery($query);
		$db->query();
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
TRUNCATE TABLE `metaHierarchy`;
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
		$query = 'TRUNCATE TABLE `metaHierarchy`;';
		$db->setQuery($query);
		$db->query();
		$query = 'update users set location=null,ranking=null';
		$db->setQuery($query);
		$db->query();
	}

/*

CREATE TABLE `metaHierarchy` (
  `valueId` int(8) unsigned NOT NULL COMMENT 'Foreign key for metaValues',
  `aliasId` int(8) unsigned NOT NULL COMMENT 'Foreign key depending on aliasType',
  `aliasType` enum('group_child','group_replace','selector_child','selector_replace') NOT NULL default 'group_child' COMMENT 'Alias purpose',
  PRIMARY KEY  (`valueId`),
  KEY `alias` (`aliasId`,`aliasType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Mapping value IDs to its child group IDs';

ALTER TABLE `languageMetaValues` ADD INDEX `name_temporary_index` ( `name` );

ALTER TABLE `articles` ADD `ingredients` TEXT NULL AFTER `teaser` ;

*/

?>