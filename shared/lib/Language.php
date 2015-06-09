<?php

/**
 * Language Object
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class Language
{
	/**
	 * Current langauge code
	 *
	 * @var string
	 */
	private $_languageCode;
	
	 /**
	 * Current langauge string array
	 *
	 * @var Array
	 */
	private $_languageStrings;

	/**
	 * Language object constructor
	 *
	 * @param string Language code
	 */
	private function __construct($languageCode)
	{
		$siteId = BluApplication::getSetting('siteId');

		// Store language code
		$this->_languageCode = strtoupper($languageCode);
	}

	/**
	 * Returns a reference to the global Language object, only creating it
	 * if it doesn't already exist
	 */
	public static function getInstance($languageCode)
	{
		static $instances;
		if (!isset($instances)) {
			$instances = array();
		}
		
		$args = func_get_args();
		$signature = serialize($args);
		
		if (empty($instances[$signature])) {
			$c = __CLASS__;
			$instances[$signature] = new $c($languageCode);
		}
		return $instances[$signature];
	}

	/**
	 * Get currenct language code
	 *
	 * @return string Language code (e.g. en, fr)
	 */
	public function getLanguageCode()
	{
		return $this->_languageCode;
	}

	/**
	 * Check if given language code is valid, and we have a mappings file
	 *
	 * @param string Language code
	 * @return bool True if valid, otherwise false
	 */
	public static function isValidLanguageCode($languageCode)
	{
		str_replace('..', '', $languageCode);
		$siteId = BluApplication::getSetting('siteId');
		return ($languageCode && (strlen($languageCode) == 2) && is_dir(BLUPATH_BASE.'/frontend/'.$siteId.'/languages/'.strtolower($languageCode)));
	}
	
	/**
	 * Get all language strings
	 * 
	 * @return array Array of language strings, indexed by placeholder
	 */	 	 	 	 	
	public function getLanguageStrings()
	{
		
		
		// Load language strings if we don't have them locally 
		if (!$this->_languageStrings) {
			
			// Get cache and database objects
			$cache = BluApplication::getCache();
			$db = BluApplication::getDatabase();
	
			// Get all details from cache/db
			$this->_languageStrings = $cache->get('languageStrings_'.$this->_languageCode);
			//$this->_languageStrings = false;
			if ($this->_languageStrings === false) {
				
				$query = 'SELECT *
					FROM languageStrings
					WHERE lang = "'.strtoupper($this->_languageCode).'"';
				$db->setQuery($query);
				$this->_languageStrings = $db->loadAssocList('place');
				if (!$this->_languageStrings) {
					return false;
				}
				
				// Store in cache
				$cache->set('languageStrings_'.$this->_languageCode, $this->_languageStrings);
			}
		}
		
		return $this->_languageStrings;
	}

	/**
	 * Get the local language text string for the given placeholder
	 *
	 * @param string Text placeholder
	 */
	public function get($place)
	{
		// Get language strings
		if (!$this->_languageStrings) {
			$this->_languageStrings = $this->getLanguageStrings();
		}
		
		if (!array_key_exists($place, $this->_languageStrings)) {
			return '['.$place.']';
		}
		return $this->_languageStrings[$place]['text'];
	}

	/**
	 * Get the URL for the current page in a new language
	 *
	 * @param string New language code
	 */
	public static function getLanguageUrl($languageCode)
	{
		$languageCode = strtolower($languageCode);
		if ($languageCode == strtolower(BluApplication::getSetting('defaultLang', 'EN'))) {
			$languageCode = '';
		}

		// Get current language code
		$language = BluApplication::getLanguage();
		$currentLanguageCode = strtolower($language->getLanguageCode());

		// Get base URL
		$baseUrl = BluApplication::getSetting('baseUrl');
		$url = preg_replace('$^'.$baseUrl.'/$', '', $_SERVER['REQUEST_URI']).'/';
		if (strpos($url, $currentLanguageCode.'/') === 0) {
			$url = strtolower($languageCode).substr($url, 2);
		} else {
			$url = strtolower($languageCode).'/'.$url;
		}
		$url = htmlspecialchars($baseUrl.'/'.trim($url, '/'));
		return $url;
	}
	
	/**
	 *	Get all available languages
	 *
	 *	@static
	 *	@access public
	 *	@return array
	 */
	public static function getAvailableLanguages()
	{
		static $available;
		if (!$available) {
			$available = BluApplication::getSetting('availableLangs');
			
			// Default
			if (empty($available)) {
				$defaultLang = BluApplication::getSetting('defaultLang', 'EN');
				$available = array(
					$defaultLang => 'Default Language'
				);
			}
		}
		return $available;
	}
}
?>
