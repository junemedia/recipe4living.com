<?php

/**
 *	URL parsing.
 */
class Router {
	
	/**
	 *	Parse legacy links.
	 */
	public static function parseLegacy(&$url){
		
		/* Regular expressions */
		$expressions = array();
		
		/* Remove domain names */
		$expressions = array_merge($expressions, self::_domain());
		
		/* Item types */
		$itemtypes = array(
			'article' => 'articles',
			'list' => 'checklists',
			'dailydeal' => 'dailydeals',
			'landingpage' => 'essentials',
			'interview' => 'interviews',
			'lifesaver' => 'lifesavers',
			'newsstory' => 'news',
			'question' => 'questions',
			'quicktip' => 'quicktips',
			'slideshow' => 'slideshows'
		);
		foreach($itemtypes as $typeOld => $typeNew){
			$expressions['/^(\/)?' . $typeOld . '-([0-9]*)(.*)$/'] = '\\1' . $typeNew . '/detail/\\2';
		}
		
		/* Replace */
		$url = preg_replace(array_keys($expressions), array_values($expressions), $url);
		
	}
	
	/**
	 *	Remove domain name from link.
	 */
	public static function removeDomain(&$url){
		
		/* Replace */
		$domainRegex = self::_domain();
		$url = preg_replace(array_keys($domainRegex), array_values($domainRegex), $url);
		
	}
	
	/**
	 *	"Remove domain name" regex.
	 */
	private static function _domain(){
		// okay, this is specific to Work it mom...
		return array('/^(.*)workitmom\.com(.*)$/i' => '\\2');
	}
	
	/**
	 *	Build query string part of a URL.
	 */
	public static function http_build_str(array $query, $prefix = null, $arg_separator = '&amp;'){
		if (Utility::iterable($query)){
			foreach($query as $key => &$value){
				$value = $key.'='.$value;
			}
		}
		return $prefix.implode($arg_separator, $query);
	}
	
}

?>