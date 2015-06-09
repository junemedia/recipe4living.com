<?php

/**
 * Quick fix, needs to be implemented properly sooner or later.
 */
class Uri {

	public static function pluralise($singular){

		switch(strtolower($singular)){
			case 'article':
				return 'articles';
				break;

			case 'news':
				return 'news';
				break;

			case 'lifesaver':
				return 'lifesavers';
				break;

			case 'list':
				/* Same as checklist */

			case 'checklist':
				return 'checklists';
				break;

			case 'quicktip':
				return 'quicktips';
				break;

			case 'landingpage':
				/* Same as essential */

			case 'essential':
				return 'essentials';
				break;

			case 'question':
				return 'questions';
				break;

			case 'interview':
				return 'interviews';
				break;

			case 'slideshow':
				return 'slideshows';
				break;

			default:
				return null;
				break;
		}

	}

	/**
	 *	Bleurgh..
	 */
	public static function build(BluModel $obj, $incSiteUrl = true){
		switch(get_class($obj)){
			case 'ArticleObject':
				/* Same as Slideshow */

			case 'NewsObject':
				/* Same as Slideshow */

			case 'InterviewObject':
				/* Same as Slideshow */

			case 'QuicktipObject':
				/* Same as Slideshow */
				
			case 'ListObject':
				/* Same as Slideshow */
				
			case 'LandingpageObject':
				/* Same as Slideshow */

			case 'SlideshowObject':
				$url = self::pluralise(strtolower(preg_replace('/Object$/', '', get_class($obj)))).'/detail/'.$obj->id.'/'.Utility::seo($obj->title);
				break;
				
			case 'QuestionObject':
				/* Same as Lifesaver */

			case 'LifesaverObject':
				$url = self::pluralise(strtolower(preg_replace('/Object$/', '', get_class($obj)))) . '/detail/' . $obj->id;
				break;

			case 'MemberblogpostObject':
				//$url = 'blogs/members/' . $obj->author->username . '/' . $obj->id;
				$url = 'blogs/members/' . $obj->author->username . '/' . $obj->id.'/asdf'.$obj->title;
				break;

			case 'MemberphotoObject':
				$photosModel = BluApplication::getModel('photos');
				$photoalbum = $photosModel->getPhotoalbum($obj);
				$page = $photoalbum->getPosition($obj) + 1;
				$url = 'photoalbum/detail/' . $obj->author->username . '/?page=' . $page;
				break;

			case 'RecipeObject':
//				$url = 'quickrecipes/detail/' . $obj->id;
				$url = $obj->xlink;
				return $url;
				break;

			case 'PersonObject':
				$url = 'profile/' . $obj->username;
				break;

			default:
				return null;
				break;
		}
		$url = preg_replace('/\/$/', '', $url);
		$url = preg_replace('/^\//', '', $url);
		$url = '/'.$url;

		if ($incSiteUrl) {
			$url = SITEURL.$url;
		}

		return $url;
	}

}

?>
