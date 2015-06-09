<?php

/**
 * Template Helper Object
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class Template
{
	/**
	 * Current script event name
	 *
	 * @var string
	 */
	private static $_scriptEvent;
	
	/**
	 *	Display variables
	 *
	 *	@access protected
	 *	@var array
	 */
	protected static $_storage = array(
		'top' => array(),		// Reserved for top nav.
		'sidebar' => array()	// Reserved for sidebar.
	);

	/**
	 * Gets local language text for a given placholder
	 * according to the current application language
	 *
	 * @param string Text placeholder
	 * @param string Array of key value replacements
	 * @return string Local language text
	 */
	public static function text($place, $replace = null)
	{
		return Text::get($place, $replace);
	}

	/**
	 * Transform bbcode to html
	 *
	 * @param string bbcode
	 * @return string html
	 */
	public static function bbcode($text)
	{
		// Replace urls
		$pattern[] = '/\[url[=](.+?)\](.+?)\[\/url\]/i';
		$replacement[] = '<a href="\\1" target="_blank">\\2</a>';

		// Replace images
		$pattern[] = '/\[img\](.+?)\[\/img\]/i';
		$replacement[] = '<img src="\\1" alt="" />';

		// Formatting
		$pattern[] = '/\[b\](.*?)\[\/b\]/i';
		$replacement[] = '<strong>\\1</strong>';
		$pattern[] = '/\[i\](.*?)\[\/i\]/i';
		$replacement[] = '<em>\\1</em>';

		// Replace bbcode
		$text = preg_replace($pattern, $replacement, $text);

		// Replace smilies
		$text = Template::smilies($text);

		return nl2br($text);
	}

	/**
	 * Transforms smilies into pretty pictures
	 *
	 * @param string ;) text
	 * @return string <img /> text
	 */
	public static function smilies($text)
	{
		$smilies = array(
			':)' => 'grin',
			':-)' => 'grin',
			':(' => 'sad',
			':-(' => 'sad',
			':o' => 'shock',
			':D' => 'lol',
			';)' => 'wink',
			';-)' => 'wink',
			';(' => 'cry',
			':p' => 'tongue',
			':P' => 'tongue',
			'8-)' => 'cool',
			':lol:' => 'lol',
			':confused:' => 'confused',
			':grin:' => 'grin',
			':mad:' => 'mad',
			':sad:' => 'sad',
			':wink:' => 'wink',
			':cry:' => 'cry',
			':biglol:' => 'biglol',
			':cool:' => 'cool',
			':rofl:' => 'lol',
			':tongue:' => 'tongue',
			':smirk:' => 'smirk',
			':eek:' => 'eek',
			':rolleyes:' => 'rolleyes',
			':up:' => 'up',
			':down:' => 'down'
		);
		foreach ($smilies as $code => &$smilie) {
			$smilie = '<img src="'.SITEASSETURL.'/images/smilies/'.$smilie.'.gif" alt="'.$code.'" />';
		}
		return str_replace(array_keys($smilies), array_values($smilies), $text);
	}

	/**
	 * Get button image (html)
	 *
	 * @param string Button ID
	 * @param string Image type
	 * @return string HTML fragment (src="image.type" alt="alt text")
	 */
	public static function buttonImage($id, $type='png')
	{
		$language = BluApplication::getLanguage();
		$languageCode = strtolower($language->getLanguageCode());

		// Determine file path
		$file = SITEASSETURL.'/languages/'.$languageCode.'/buttons/'.$id.'.'.$type;

		// Build html fragment
		$html = 'src="'.$file.'" alt="'.Text::get('button_'.$id).'"';
		return $html;
	}

	/**
	 * Return price of item in current currency
	 *
	 * @param int Amount
	 * @return string Formatted price
	 */
	public static function price($amount)
	{
		return '$'.number_format($amount, 2);
	}

	/**
	 * Return date/time formatted according to variance from current time
	 *
	 * @return string Formatted date/time
	 */
	public static function time($time, $alwaysIncludeTime = false, $alwaysExcludeTime = false)
	{
		// Get timestamp to print
		$time = is_int($time) ? $time : strtotime($time);

		// Get current timestamp
		$now = time();

		// Today?
		if (date('Y-m-d', $now) == date('Y-m-d', $time)) {
			$text = 'Today '.(!$alwaysExcludeTime ? date('H:i', $time) : '');

		// Yesterday?
		} elseif (date('Y-m-d', $now - 86400) == date('Y-m-d', $time)) {
			$text = 'Yesterday '.(!$alwaysExcludeTime ? date('H:i', $time) : '');

		// This week?
		} elseif (date('W', $now - (86400)) == date('W', $time)) {
			$text = date('l '.($alwaysIncludeTime ? 'H:i' : ''), $time);

		// Last week? - leaving this out for now
		/*} elseif (date('W', $now - (86400 * 7)) == date('W', $time)) {
			$text = 'Last '.date('l '.($alwaysIncludeTime ? 'H:i' : ''), $time);*/

		// Full date
		} else {
			$text = date('F j, Y '.($alwaysIncludeTime ? 'H:i' : ''), $time);
		}

		return $text;
	}

	/**
	 * Return formatted filesize
	 *
	 * @param int File size in bytes
	 * @param int Rounding precision
	 * @return string Formatted file size
	 */
	public static function fileSize($size, $round = 0) {
	    $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
	    $total = count($sizes);
	    for ($i=0; $size > 1024 && $i < $total; $i++) {
			$size /= 1024;
		}
	    return round($size, $round).' '.$sizes[$i];
	}

	/**
	 * Start a script block.  Subsequent code will either be ouput in-line,
	 * or appended to the global window domready event as applicable
	 *
	 * @param string Window event to add script to if possible
	 */
	public static function startScript($event = 'domready')
	{
		// Store event
		self::$_scriptEvent = $event;

		// Get document
		$document = BluApplication::getDocument();
		$format = $document->getFormat();

		// Output script start block if not in site format or attaching to domready
		if (($format == 'raw') || ($format == 'popup') || ($format == 'json') || ($event == 'inline')) {
			echo '<script type="text/javascript" language="Javascript">//<![CDATA['."\n";

		// Buffer script output
		} else {
			ob_start();
		}
	}

	/**
	 * End a script block
	 */
	public static function endScript()
	{
		// Get event
		$event = self::$_scriptEvent;

		// Get document
		$document = BluApplication::getDocument();
		$format = $document->getFormat();

		// Output script end block if not in site format or attaching to domready
		if (($format == 'raw') || ($format == 'popup') || ($format == 'json') || ($event == 'inline')) {
			echo "\n".'//]]></script>';

		// Get buffer contents and append to document header scripts
		} else {
			$script = ob_get_clean();
			$document->addScript($script, $event);
		}
	}

	/**
	 *	Include an entire script
	 */
	public static function includeScript($scriptPath, $scriptDescription = null)
	{
		// Format script include
		$scriptDescription = !is_null($scriptDescription) && strlen($scriptDescription) > 0?'id="'.$scriptDescription.'" ':'';
		$scriptInclude = '<script '.$scriptDescription.'type="text/javascript" src="'.$scriptPath.'"></script>' . "\n";

		// Include script include
		$document = BluApplication::getDocument();
		$document->addScript($scriptInclude, 'includes');
	}

	/**
	 * Set document title (used by popups)
	 *
	 * @param string Title text
	 */
	public static function setTitle($title)
	{
		$document = BluApplication::getDocument();
		$document->setTitle($title);
	}

	/**
	 * Prints time since given time
	 *
	 * @param string Time string
	 * @return string Time since that time
	 */
	public static function timeSince($o)
	{
		$original = strtotime($o);
		$chunks = array(
			array(60 * 60 * 24 * 365 , 'year'),
			array(60 * 60 * 24 * 30 , 'month'),
			array(60 * 60 * 24 * 7, 'week'),
			array(60 * 60 * 24 , 'day'),
			array(60 * 60 , 'hour'),
			array(60 , 'minute'),
		);
		$since = time() - $original;
		for ($i = 0, $j = count($chunks); $i < $j; $i++) {
			$seconds = $chunks[$i][0];
			$name = $chunks[$i][1];
			if (($count = floor($since / $seconds)) != 0) break;
		}
		$print = ($count == 1) ? '1 '.$name : "$count {$name}s";
		if ($i + 1 < $j) {
			$seconds2 = $chunks[$i + 1][0];
			$name2 = $chunks[$i + 1][1];
			if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
				$print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
			}
		}
		return $print.' ago';
	}

	/**
	 *	Print out tags.
	 */
	public static function tags($data, $type = null, $prefixedText = true){

		/* Prepare final string. */
		$html = '';

		/* Print tags */
		if (Utility::is_loopable($data)){

			/* Build array of tags */
			$html_pieces = array();
			foreach($data as $tag){

				/* Recursive call */
				ob_start();
				$func = __FUNCTION__;
				self::$func($tag, $type, false);
				$html_pieces[] = ob_get_clean();

			}

			/* Print list of tags */
			$html = implode(", ", $html_pieces);

		} else if ($data) {

			/* Build query string */
			$qstr = 'search=' . urlencode($data) . ($type ? '&type=' . $type : '');

			/* Print individual tag */
			$html = '<a href="' . SITEURL . '/search?' . $qstr . '">' . $data . '</a>';

		} else {

			/* Die */
			return false;

		}

		/* Output text */
		if ($prefixedText){ echo ', tagged '; }
		echo $html;

		/* Exit */
		return true;

	}

	/**
	 * Item count string
	 *
	 * @param int Total number of items
	 * @param string Singular item (e.g. 'product')
	 * @param string Plural item (e.g. 'products')
	 * @return string 'There are x of y'
	 */
	public static function itemCount($total, $singular, $plural)
	{
		$text = 'There '.($total == 1 ? 'is' : 'are').
			' <strong>'.($total == 0 ? 'no' : $total).'</strong> '.
			($total == 1 ? $singular : $plural).'.';

		return $text;
	}

	/**
	 *	Tries to echo a string.
	 *	If null given or $text not defined, don't do anything.
	 */
	public static function out(&$text, $type = null){

		/* Test */
		if (!$text) {
			return false;
		}

		/* Display */
		switch (strtolower($type)){
			case 'bool':
				/* Same as boolean */

			case 'boolean':
				return true;

			case 'string':
				/* Same as default */

			default:
				echo $text;
				break;
		}

		/* Exit */
		return true;

	}
	
	/**
	 *	Generate an image for the item.
	 */
	public static function image($object, $width = 60, $height = null, $zoomCrop = 1){
		
		/* Start building the path... */
		$path = '';
		
		/* Get item image */
		if ($object instanceof ItemObject){
			
			/* Get item image. */
			$filename = $object->image;
			if ($filename){
				$dir = ($object instanceof SlideshowObject ? 'slideshow' : 'item');
			} else {
				/* Item image doesn't exist: get item author's image. */
				return self::image($object->author, $width, $height, $zoomCrop);
			}
			
		} else if ($object instanceof PersonObject) {
			
			/* Get user image */
			$filename = isset($object->image) ? $object->image : null;
			$dir = 'user';
			
		} else if ($object instanceof MemberphotoObject) {
			
			/* Get photo image */
			$filename = isset($object->image) ? $object->image : null;
			$dir = 'user';
			
		} else if ($object instanceof SlideObject) {
		
			/* Get slide image. */
			$filename = isset($object->image) ? $object->image : null;
			$dir = 'slideshow';
		
		} else if (Utility::iterable($object) && isset($object['image'])){
			
			/* This is from something from a "new" model. */
			$filename = $object['image'];
			$dir = $object['imageDirectory'] ? $object['imageDirectory'] : 'item';
		
		} else {
			/* Bogus */
			$filename = null;
			$dir = null;
		}
		
		if (!$filename || !$dir) {
			
			/* Fail - not implemented: return default image */
			$filename = 'default.jpg';
			$dir = 'temp';
			
		}
		$path = ASSETURL.'/'.$dir.'images/';

		/* Dimensions */
		$width = (int) $width;
		$height = is_null($height) ? $width : (int) $height;
		$zoomCrop = (int) $zoomCrop;
		$path .= $width . '/' . $height . '/' . $zoomCrop . '/';
		
		/* Filename */
		$path .= $filename;
		
		/* ...done. */
		echo $path;
		
	}
	
	/**
	 *	Generate a URL for something.
	 */
	public static function link($type, $args){
		
		/* Distinguish object type. */
		switch(strtolower($type)){
			case 'featuredblog':
				if (!isset($args['path'])){ return null; }
				$link = substr($args['path'], 1);
				break;
				
			case 'featuredblogpost':
				if (!isset($args['guid'])){ return null; }
				$link = preg_replace('/^(.*)workitmom.com\/(.*)$/', '\\2', $args['guid']);
				break;
				
			case 'person':
				if (!isset($args->profileURL)){ return null; }
				$link = preg_replace('/^\/(.*)/', '\\1', $args->profileURL);
				$link = preg_replace('/(.*)\/$/', '\\1', $link);
				break;
			
			default:
				return null;
				break;
		}
		
		/* Display link. */
		echo SITEURL . '/' . $link . '/';
	}
	
	/**
	 *	Shortcut.
	 */
	public static function comment_count($count){
		return self::pluralise($count, 'comment');
	}
	
	/**
	 *	Display date
	 */
	public static function date($datetime, $format = null){
		if (!$format){
			$format = BluApplication::getSetting('dateFormat', 'jS F Y');
		}
		echo date($format, strtotime($datetime));
	}
	
	/**
	 *	Pluralise.
	 */
	public static function pluralise($count, $singular, $plural = null){
		echo (int)$count.' '.Text::pluralise($count, $singular, $plural);
	}
	
	/**
	 *	Set a display variable
	 *
	 *	@access public
	 *	@param mixed array or Key
	 *	@param mixed Value
	 *	@return bool Success
	 */
	public static function set($key, $value = null)
	{
		// Multiple sets
		if (is_array($key)) {
			return self::$_storage = array_merge_recursive(self::$_storage, $key);
		}
		
		// Single set
		return self::$_storage[$key] = $value;
	}
	
	/**
	 *	Get a display variable
	 *
	 *	@access public
	 *	@param string Key
	 *	@param mixed Fallback
	 *	@return bool Success
	 */
	public static function get($key, $default = null)
	{
		// Get key
		if (isset(self::$_storage[$key])) {
			return self::$_storage[$key];
		}
		
		// Fail
		return $default;
	}
}

?>
