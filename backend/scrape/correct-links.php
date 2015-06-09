#!/usr/bin/php
<?php

		$startTime = time();

		define('NL',"\n");
		define('TEST',true);

		define('HTDOCSPATH','/var/www/html/');
		define('MAINURL','http://www.recipe4living.com/');
		define('STARTURL',MAINURL.'home/');
		define('IMAGESPATH',HTDOCSPATH.'assets/recipe4living/itemimages/');
		define('LIBPATH',HTDOCSPATH.'shared/lib/');

		//require_once 'simple_html_dom.php';

		require_once HTDOCSPATH.'config.php';
		require_once 'HTMLPurifier.auto.php';
		require_once LIBPATH.'BluApplication.php';
		require_once LIBPATH.'Cache.php';
		require_once LIBPATH.'Database.php';
		require_once LIBPATH.'Text.php';
		require_once LIBPATH.'Utility.php';
		//require_once HTDOCSPATH.'frontend/recipe4living/models/ItemsModel.php';


		define('CACHE', false);
		define('DEBUG', false);
		define('DEBUG_INFO', false);
		define('BLUPATH_BASE', HTDOCSPATH);

function __autoload($className)
{
	// Miscellaneous
	if (file_exists(BLUPATH_BASE.'/shared/lib/'.$className.'.php')) {
		require_once(BLUPATH_BASE.'/shared/lib/'.$className.'.php');
		return;
	}
	if (file_exists(BLUPATH_BASE.'/shared/objects/'.$className.'.php')) {
		require_once(BLUPATH_BASE.'/shared/objects/'.$className.'.php');
		return;
	}
	if (file_exists(BLUPATH_BASE.'/shared/interfaces/'.$className.'.php')) {
		require_once(BLUPATH_BASE.'/shared/interfaces/'.$className.'.php');
		return;
	}
	
	// Allows more complex inheritance while staying sane.
	$fail = false;
	$siteId = BluApplication::getSetting('siteId');
	
	$path = BLUPATH_BASE.'/'.SITEEND;
	if (strpos($className, ucfirst($siteId)) === 0) {
		$path .= '/'.$siteId;
		$className = substr($className, strlen($siteId));
	} else {
		$path .= '/base';
	}
	
	if (strpos($className, 'Controller') !== false) {
		$path .= '/controllers';
	} else {
		$fail = true;
	}
	
	if (!$fail && file_exists($path.'/'.$className.'.php')) {
		require_once($path.'/'.$className.'.php');
		return;
	}
	
	// Fail
	//trigger_error('Could not find '.$className, E_USER_ERROR);
}
$itemsModel = BluApplication::getModel('items');


		$config = new Config;
		$db = Database::getInstance($config->databaseHost,$config->databaseUser,$config->databasePass,$config->databaseName);

		$query = 'SELECT id,title,body,teaser,slug,oldArticleId FROM articles /*WHERE type!="recipe" LIMIT 10000*/';
		$db->setQuery($query);
		$articles = $db->loadAssocList();

		for($i=0;$i<count($articles);$i++) {
			$article = $articles[$i];
			//echo $article['body'].NL;
			$body = $article['body'];
			
			echo '--------------------------------------------------------------------------------------------------------------------------------------------'.NL;
			echo 'ID: '.$articles[$i]['id'].' - '.$articles[$i]['oldArticleId'].' - '.$articles[$i]['title'].NL;
			echo '--------------------------------------------------------------------------------------------------------------------------------------------'.NL;

			$j = 0;
			$offset = 0;
			$hrefArray = array();
			do {
				$j++;
				$pos1 = strpos($article['body'],'href="',$offset);
				$pos2 = strpos($article['body'],'"',$pos1+strlen('href="')+1);
				$offset = $pos2 + 1;
				if($pos1!==false) {
					//echo $j.' - '.$pos1.' - '.$pos2.NL;
					$href = substr($article['body'],$pos1+strlen('href="'),$pos2-$pos1-strlen('href="'));
					$hrefArray[] = $href;
				}
			} while($pos1!==false && $pos2<=strlen($article['body']) && $j<=1000);
			foreach($hrefArray as $url) {
				echo $url;
				$articleId = getArticleIdFromUrl($url);
				//echo $articleId.NL;
				$query = 'SELECT id FROM articles WHERE oldArticleId="'.Database::escape($articleId).'"';
				$db->setQuery($query);
				if($newId = $db->loadResult()) {
					$newLink = NULL;
					if($item = $itemsModel->getItem($newId)) {
						$newLink = $item['link'];
						//var_dump($item['link']);
						//die;
						echo '   ---> '.$newLink.NL;
						$body = str_replace($url,$newLink,$body);
					}
				}
				else {
					echo '   ID "'.$articleId.'" not found'.NL;
				}
			}

			$k = 0;
			$offset = 0;
			$srcArray = array();
			$dimesionsArray = array();
			do {
				$k++;
				$pos1 = strpos($article['body'],'src="',$offset);
				$pos2 = strpos($article['body'],'"',$pos1+strlen('src="')+1);
				$offset = $pos2 + 1;
				if($pos1!==false) {
					//echo $k.' - '.$pos1.' - '.$pos2.NL;
					$src = substr($article['body'],$pos1+strlen('src="'),$pos2-$pos1-strlen('src="'));
					$srcArray[] = $src;
					$dims = array();
					$width = NULL;
					$height = NULL;
					$beforeSrcStr = substr($article['body'],0,$pos1);
					$imgTagStart = substr($beforeSrcStr,strrpos($beforeSrcStr,'<img'));
					$afterSrcStr = substr($article['body'],$pos2);
					$imgTagEnd = substr($afterSrcStr,1,strpos($afterSrcStr,'>'));
					$wpos1 = strpos($imgTagStart,'width="');
					if($wpos1!==false) {
						$width = substr($imgTagStart,$wpos1+strlen('width="'),strpos($imgTagStart,'"',$wpos1+strlen('width="')+1)-$wpos1-strlen('width="'));
					}
					else {
						$wpos1 = strpos($imgTagEnd,'width="');
						if($wpos1!==false) {
							$width = substr($imgTagEnd,$wpos1+strlen('width="'),strpos($imgTagEnd,'"',$wpos1+strlen('width="')+1)-$wpos1-strlen('width="'));
						}
					}
					$hpos1 = strpos($imgTagStart,'height="');
					if($hpos1!==false) {
						$height = substr($imgTagStart,$hpos1+strlen('height="'),strpos($imgTagStart,'"',$hpos1+strlen('height="')+1)-$hpos1-strlen('height="'));
					}
					else {
						$hpos1 = strpos($imgTagEnd,'height="');
						if($hpos1!==false) {
							$height = substr($imgTagEnd,$hpos1+strlen('height="'),strpos($imgTagEnd,'"',$hpos1+strlen('height="')+1)-$hpos1-strlen('height="'));
						}
					}
					if((!empty($width) && preg_match('/^\d+$/',$width)) || (!empty($height) && preg_match('/^\d+$/',$height))) {
						$dims['w'] = $width;
						$dims['h'] = $height;
//var_dump($imgTagStart); echo NL;
//var_dump($imgTagEnd); echo NL;
//print_r($dims);
					}
					$dimesionsArray[] = $dims;
				}
			} while($pos1!==false && $pos2<=strlen($article['body']) && $k<=1000);
			foreach($srcArray as $id=>$url) {
				echo $url;
				$imageFileName = getImageFileNameFromUrl($url);
				$query = 'SELECT filename FROM articleImages WHERE filename="'.$imageFileName.'"';
				$db->setQuery($query);
				if(preg_match('/^\/uploads/',$url) || preg_match('/^\/uploadedImages/',$url)) {
					$url = MAINURL.preg_replace('/^\//','',$url);
				}
				if($filename = $db->loadResult()) {
					if(!file_exists(IMAGESPATH.$filename) && !preg_match('/^\/assets\//',$url)) {
						copy($url,IMAGESPATH.$filename);
					}
				}
				else {
					if(!preg_match('/^\/assets\//',$url) && copy($url,IMAGESPATH.$imageFileName)) {
						$query = 'INSERT INTO articleImages SET articleId='.$articles[$i]['id'].' AND filename="'.Database::escape($imageFileName).'"';
						$db->setQuery($query);
						$db->query();
						$filename = $imageFileName;
					}
					else {
						$filename = '-------------------------';
					}
				}
				if(file_exists(IMAGESPATH.$filename)) {
					$imagesize = getimagesize(IMAGESPATH.$filename);
					if(!empty($dimensionsArray[$id]['w']) && !empty($dimensionsArray[$id]['h'])) {
						$x = $dimensionsArray[$id]['w'];
						$y = $dimensionsArray[$id]['h'];
					}
					elseif(!empty($dimensionsArray[$id]['w']) && empty($dimensionsArray[$id]['h'])) {
						$x = $dimensionsArray[$id]['w'];
						$y = round($imagesize[1]/$imagesize[0]*$x);
					}
					elseif(empty($dimensionsArray[$id]['w']) && !empty($dimensionsArray[$id]['h'])) {
						$y = $dimensionsArray[$id]['h'];
						$x = round($imagesize[0]/$imagesize[1]*$y);
					}
					else {
						if(!empty($imagesize[0]) && !empty($imagesize[1])) {
							if($imagesize[0]>450) {
								$x = 450;
								$y = round($imagesize[1]/$imagesize[0]*450);
							}
							else {
								$x = $imagesize[0];
								$y = $imagesize[1];
							}
						}
						else {
							die('No information');
						}
					}
					$newUrl = '/assets/itemimages/'.$x.'/'.$y.'/1/'.$filename;
					$body = str_replace($url,$newUrl,$body);
					echo '   ---> '.$newUrl.NL;
				}
				else {
					echo '   No filename'.NL;
				}
//fgets(STDIN);
			}

			//print_r($hrefArray);
			//echo $body.NL.NL;
			if($body!=$article['body']) {
				$query = 'UPDATE articles SET body="'.Database::escape($body).'" WHERE id='.$articles[$i]['id'];
				$db->setQuery($query);
				$db->query();
//die;
			}

//fgets(STDIN);

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
		if(!preg_match("/^[0-9]{1,}$/",$recipeId)) {
			$recipeId = NULL;
		}
		return $recipeId;
	}

	function getImageFileNameFromUrl($url) {
		$urlExploded = explode('/',$url);
		$imageFileName = array_pop($urlExploded);
		return urldecode($imageFileName);
	}


?>
