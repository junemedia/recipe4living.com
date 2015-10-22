<?php

/**
 * Text Helper Object
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class Text
{
	/**
	 * Get HTML Purifier instance
	 *
	 * @return HTMLPurifier Purifier instance
	 */
	public static function getHTMLPurifier($forbiddenElements = array(), $autoParagraph = false) {

		// Create config
		$config = HTMLPurifier_Config::createDefault();
    	$config->set('HTML', 'ForbiddenElements', $forbiddenElements);
    	$config->set('AutoFormat', 'AutoParagraph', $autoParagraph);

    	// Instantiate new purifier
		$purifier = new HTMLPurifier($config);

		return $purifier;
	}

	/**
	 * Gets local language text for a given placholder
	 * according to the current application language
	 *
	 * @param string Text placeholder
	 * @return string Local language text
	 */
	public static function get($place, $replace = null)
	{
		static $language;

		if (!$language)
			$language = BluApplication::getLanguage();
		$text = $language->get($place);

		// Perform replacements
		if (!empty($replace)) {
			foreach ($replace as $key => $value) {
				$text = str_replace('['.$key.']', $value, $text);
			}
		}

		return $text;
	}

	/**
	 * Clean a HTML string
	 *
	 * @param string Dirty html
	 * @return string Cleaned html
	 */
	public static function cleanHTML($html, $allowImages = true, $autoParagraph = true)
	{
		// Disallow images?
		$forbiddenElements = array('h1', 'h2');
		if (!$allowImages) {
			$forbiddenElements[] = 'img';
		}

		// Replace multiple br with double newline ready for auto-paragraph
		$html = preg_replace('/(<br( *)(\/?)>)+/', "\r\n\r\n", $html);

		// Run HTML through purifier
		$purifier = self::getHTMLPurifier($forbiddenElements, $autoParagraph);
		$html = $purifier->purify($html);

		// Return purified HTML
		return $html;
	}

	/**
	 * Trims text to a fixed lenth, keeping words intact
	 *
	 * @param string Text to trim
	 * @return string Trimmed text
	 */
	public static function trim($srcString, $trimLength = 150, $allowHTML = false, $allowImages = true, $autoParagraph = true)
	{
		// Grab a chunk of text from source (longer than we require to allow for html)
		$srcString = trim($srcString);
		$tmpString = substr($srcString, 0, ($trimLength * 2));

		// Strip/purify HTML
		if ($allowHTML) {
			$tmpString = self::cleanHTML($tmpString, $allowImages, $autoParagraph);
		} else {
			$tmpString = strip_tags($tmpString);
		}

		// Temporarily replace spaces within HTML tags so that they're not used as word boundaries
		$count = true;
		while ($count) {
			$tmpString = preg_replace('/\<([^>]+) ([^>]+)\>/', '<$1|$2>', $tmpString, -1, $count);
		}

		// Add words until we hit our length limit
		$srcWords = explode(' ', $tmpString);
		$retWords = array();
		$length = 0;
		while ((list(, $word) = each($srcWords)) && ($length < $trimLength)) {
			$length += strlen(strip_tags($word))+1;
			$retWords[] = $word;
		}

		// Implode words and add ellipsis if required
		$retString = implode(' ', $retWords);
		if (count($retWords) < count($srcWords)) {
			$retString .= '&#8230;';
		}

		// Put back spaces in HTML tags
		$count = true;
		while ($count) {
			$retString = preg_replace('/\<([^>]+)\|([^>]+)\>/', '<$1 $2>', $retString, -1, $count);
		}

		// Purify HTML
		if ($allowHTML) {
			$retString = self::cleanHTML($retString, $allowImages, $autoParagraph);
		}

		return $retString;
	}

	/**
	 *	Just to make things consistent.
	 */
	public static function escapeslashes($str){
		return addslashes($str);
	}
	
	/**
	 *	Escape "smart quotes" (i.e. Microsoft Office replacement characters).
	 *
	 *	References from 
	 *		http://uk2.php.net/manual/en/function.chr.php
	 *
	 *	@static
	 *	@access public
	 *	@param string|array Input
	 *	@return string|array Escaped text
	 */
	public static function escape_smart_characters($input)
	{
		// Recurse
		if (is_array($input)) {
			$output = array();
			foreach ($input as $k => $v) {
				$output[$k] = self::escape_smart_characters($v);
			}
		} else {
			
			// Replacements, eugh
			$replacements = array(
				chr(130) => ',',    // baseline single quote
				chr(131) => 'NLG',  // florin
				chr(132) => '"',    // baseline double quote
				chr(133) => '...',  // ellipsis
				chr(134) => '**',   // dagger (a second footnote)
				chr(135) => '***',  // double dagger (a third footnote)
				chr(136) => '^',    // circumflex accent
				chr(137) => 'o/oo', // permile
				chr(138) => 'Sh',   // S Hacek
				chr(139) => '<',    // left single guillemet
				chr(140) => 'OE',   // OE ligature
				chr(145) => "'",    // left single quote
				chr(146) => "'",    // right single quote
				chr(147) => '"',    // left double quote
				chr(148) => '"',    // right double quote
				chr(149) => '-',    // bullet
				chr(150) => '-',    // endash
				chr(151) => '--',   // emdash
				chr(152) => '~',    // tilde accent
				chr(153) => '(TM)', // trademark ligature
				chr(154) => 'sh',   // s Hacek
				chr(155) => '>',    // right single guillemet
				chr(156) => 'oe',   // oe ligature
				chr(159) => 'Y',    // Y Dieresis
			);
			
			// Add fractions too
			$replacements = array_merge($replacements, array(
				chr(188) => '1/4',    // Quarter
				chr(189) => '1/2',    // Half
				chr(190) => '3/4',    // Three-quarters		
			));
			
			// Prepare replacements
			$r = array();
			foreach ($replacements as $key => $value) {
				$r['/.'.$key.'/'] = $value;
			}
			
			// Replace
			$output = preg_replace(array_keys($r), $r, $input);
		}
		
		// Return
		return $output;
	}

	/**
	 *	Add an 's' on the end of a word.
	 */
	public static function pluralise($count, $singular = null, $plural = null){
	
		/* Legacy */
		if (is_null($singular)){ $singular = ''; }	
		if (is_null($plural)){ $plural = $singular.'s'; }
		
		/* Return word */
		return $count != 1 ? $plural : $singular;
		
	}
	
	/**
	 *	Enable links.
	 */
	public static function enableLinks($haystack, array $options = array()){
		
		/* Get new <a> attributes */
		$attributes = isset($options['attributes']) ? $options['attributes'] : array();
		$attributes = array_merge(array('target' => '_blank'), $attributes);
		foreach($attributes as $key => &$value){
			$value = $key.'="'.$value.'"';
		}
		
		/* Strip other interfering tags. */
		$haystack = strip_tags($haystack);
		
		/* Regex to use */
		$match = isset($options['match']) ? $options['match'] : Utility::VALID_URL;
		$replace = isset($options['replace']) ? $options['replace'] : '\\0';
		$display = isset($options['display']) ? $options['display'] : '\\0';
		
		/* Replace links. */
		return preg_replace('/'.$match.'/i', '<a '.implode(' ', $attributes).' href="'.$replace.'">'.$display.'</a>', $haystack);
		
	}
	
	/**
	 *	Print boolean
	 */
	public static function fromBoolean($var, $true = 'True', $false = 'False'){
		if (!is_bool($var)){ return $var; }
		return $var ? $true : $false;
	}

	/**
	 *	Takes a SimpleXMLElement and returns its content as an XML string
	 *
	 *	@static
	 *	@access public
	 *	@param SimpleXMLElement
	 *	@param bool Whether to just return content
	 *	@return string
	 */
	public static function asXml(SimpleXMLElement $element, $removeTags = true)
	{
		// Get element details
		$string = $element->asXml();
		
		// Strip current tags, to leave just the content
		if ($removeTags) {
			$tag = $element->getName();
			$string = substr($string, strlen($tag) + 2, strlen($string) - (2 * strlen($tag)) - 5);
		}
		
		// Return
		return $string;
	}
	
	/**
	 *	Filter a string by common words
	 *
	 *	@static
	 *	@access public
	 *	@param string Text to filter
	 *	@param array Extra words to filter by (case insensitive)
	 *	@return array Words
	 */
	public static function filterCommonWords($text, array $extraFilters = array())
	{
		// Get words to filter by
		$filters = array_merge(self::_getCommonWords(), $extraFilters);
		
		// Run through regex filter
		$words = preg_split('/[^A-Za-z]+/', $text);
		$words = preg_replace('/^('.implode('|', $filters).'|..|.)$/i', '', $words);
		
		// Return
		$words = array_filter($words);
		return $words;
	}
	
	/**
	 *	Get common but meaningless words.
	 *
	 *	@static
	 *	@access protected
	 *	@return array
	 */
	protected static function _getCommonWords()
	{
		return array(
			'a',
			'able',
			'about',
			'across',
			'add',
			'after',
			'all',
			'almost',
			'also',
			'am',
			'among',
			'an',
			'and',
			'any',
			'are',
			'as',
			'at',
			'be',
			'because',
			'been',
			'but',
			'by',
			'can',
			'cannot',
			'could',
			'dear',
			'did',
			'do',
			'does',
			'either',
			'else',
			'even',
			'ever',
			'every',
			'for',
			'from',
			'get',
			'got',
			'had',
			'has',
			'have',
			'he',
			'her',
			'hers',
			'him',
			'his',
			'how',
			'however',
			'i',
			'if',
			'in',
			'into',
			'is',
			'it',
			'its',
			'it\'s',
			'just',
			'least',
			'let',
			'like',
			'likely',
			'make',
			'may',
			'me',
			'might',
			'more',
			'most',
			'must',
			'my',
			'neither',
			'no',
			'nor',
			'not',
			'of',
			'off',
			'often',
			'on',
			'only',
			'or',
			'other',
			'our',
			'own',
			'rather',
			'said',
			'say',
			'says',
			'she',
			'should',
			'since',
			'so',
			'some',
			'than',
			'that',
			'the',
			'their',
			'them',
			'then',
			'there',
			'these',
			'they',
			'this',
			'tis',
			'to',
			'too',
			'twas',
			'us',
			'wants',
			'was',
			'we',
			'were',
			'what',
			'when',
			'where',
			'which',
			'while',
			'who',
			'whom',
			'why',
			'will',
			'with',
			'would',
			'yet',
			'you',
			'your'
		);
	}
	
	/**
	 *	Turn CR, LF and NL'd plain text into HTML
	 *
	 *	@static
	 *	@access public
	 *	@param string Text
	 *	@param string Paragraph class
	 *	@return string HTML
	 */
	public static function toHtml($plainText, $css = false)
	{
		$text = str_replace("\r\n", "\n", $plainText);
		$text = '<p'.($css ? ' class="'.$css.'"' : '').'>'.preg_replace("/\n\n+/", '</p><p>', $text).'</p>';
		$text = str_replace("\n", '<br />', $text);
		return $text;
	}

    public static function formatBytes($size, $precision = 2) { 

        $base = log($size) / log(1024);
        $suffixes = array('', 'k', 'M', 'G', 'T');   

        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
 
    }
	
	/**
    * Singularizes English nouns.
    *
	* @static
    * @access public
    * @param string $word English noun to singularize
    * @return string Singular noun.
    */
    public static function singularize($word)
    {
        $singular = array (
        '/(quiz)zes$/i' => '\1',
        '/(matr)ices$/i' => '\1ix',
        '/(vert|ind)ices$/i' => '\1ex',
        '/^(ox)en/i' => '\1',
        '/(alias|status)es$/i' => '\1',
        '/([octop|vir])i$/i' => '\1us',
        '/(cris|ax|test)es$/i' => '\1is',
        '/(shoe)s$/i' => '\1',
        '/(o)es$/i' => '\1',
        '/(bus)es$/i' => '\1',
        '/([m|l])ice$/i' => '\1ouse',
        '/(x|ch|ss|sh)es$/i' => '\1',
        '/(m)ovies$/i' => '\1ovie',
        '/(s)eries$/i' => '\1eries',
        '/([^aeiouy]|qu)ies$/i' => '\1y',
        '/([lr])ves$/i' => '\1f',
        '/(tive)s$/i' => '\1',
        '/(hive)s$/i' => '\1',
        '/([^f])ves$/i' => '\1fe',
        '/(^analy)ses$/i' => '\1sis',
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
        '/([ti])a$/i' => '\1um',
        '/(n)ews$/i' => '\1ews',
        '/s$/i' => '',
        );

        $uncountable = array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep');

        $irregular = array(
        'person' => 'people',
        'man' => 'men',
        'child' => 'children',
        'sex' => 'sexes',
        'move' => 'moves');

        $lowercased_word = strtolower($word);
        foreach ($uncountable as $_uncountable){
            if(substr($lowercased_word,(-1*strlen($_uncountable))) == $_uncountable){
                return $word;
            }
        }

        foreach ($irregular as $_plural=> $_singular){
            if (preg_match('/('.$_singular.')$/i', $word, $arr)) {
                return preg_replace('/('.$_singular.')$/i', substr($arr[0],0,1).substr($_plural,1), $word);
            }
        }

        foreach ($singular as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }

        return $word;
    }

}

?>
