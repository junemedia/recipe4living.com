<?php

/**
 * Document Object
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class Document
{
	/**
	 * Requested output format
	 *
	 * @var string
	 */
	private $_format = 'site';

	/**
	 * Document title
	 *
	 * @var string
	 */
	private $_title;

	/**
	 * Document description
	 *
	 * @var string
	 */
	private $_description;

	/**
	 * Document keywords
	 *
	 * @var string
	 */
	private $_keywords;

	/**
	 * Script to add to header
	 *
	 * @var string
	 */
	private $_script = array();

	/**
	 * Document body contents
	 *
	 * @var array
	 */
	private $_contents = array(
		'topnav' => null,
		'body' => null
	);

	/**
	 * Document content type
	 *
	 * @var string
	 */
	private $_mimeType;

	/**
	 * Document encoding
	 *
	 * @var string
	 */
	private $_encoding;

	/**
	 * Maxmimum length of time to cache document for
	 *
	 * @var int
	 */
	private $_maxAge = 0;

	/**
	 * Whether or not the client cache item must be re-validated on every use
	 * 
	 * @var bool
	 */
	private $_mustRevalidate = true;

	/**
	 * Last modified date
	 *
	 * @var int
	 */
	private $_mdate;

	/**
	 * ETag
	 *
	 * @var string
	 */
	private $_etag;

	/**
	 * Accept-Ranges
	 *
	 * @var string
	 */
	private $_acceptRanges;

	/**
	 * HTTP Status
	 *
	 * @var string
	 */
	private $_status;

	/**
	 * Content disposition
	 *
	 * @var string
	 */
	private $_disposition;
	
	/**
	 * Document authentication
	 * 
	 * @var string
	 */
	private $_authentication;

	/**
	 * Generic header tags
	 *
	 * @var string
	 */
	private $_genericHeader;

	/**
	 * Header advert
	 *
	 * @var string
	 */
	private $_headerAd;

		/**
	 * Header advert page
	 *
	 * @var string
	 */
	private $_headerAdPage;

	/**
	 * Arguments for bread crumbs
	 *
	 * $var array
	 */
	private $_breadcrumbs;
	
	/**
	 *	Display contents
	 *
	 *	@access protected
	 *	@var bool
	 */
	protected $_displayContents = true;
	
	/**
	 * Document constructor
	 *
	 * @param string Document format
	 */
	public function __construct($format = 'site')
	{
		$this->_format = $format;

		// Set default content type
		switch ($format) {
			case 'json' :
				$this->_mimeType = 'application/json';
				break;
			case 'xml' :
				$this->_mimeType = 'application/xml';
				break;
			default :
				$this->_mimeType = 'text/html';
				break;
		}
	}

	/**
	 * Returns a reference to the global Document object, only creating it
	 * if it doesn't already exist
	 *
	 * @param string Document format
	 * @return Document A document object
	 */
	public static function &getInstance($format)
	{
		static $instances;
		if (!isset($instances)) {
			$instances = array();
		}

		$args = func_get_args();
		$signature = serialize($args);
		if (empty($instances[$signature])) {
			$c = __CLASS__;
			$instances[$signature] = new $c($format);
		}
		return $instances[$signature];
	}

	/**
	 * Set document format
	 *
	 * @param string Document format
	 */
	public function setFormat($format)
	{
		$this->_format = $format;
	}

	/**
	 * Get document format
	 *
	 * @return string Document format
	 */
	public function getFormat()
	{
		return $this->_format;
	}

	/**
	 * Set document title
	 *
	 * @param string Document title
	 */
	public function setTitle($title)
	{
		$this->_title = $title;
	}

	/**
	 * Get document title
	 *
	 * @return string Document title
	 */
	public function getTitle()
	{
		return $this->_title;
	}


	/**
	 * Get full site title (including store name)
	 *
	 * @return string Full site title
	 */
	public function getSiteTitle()
	{
		$title = $this->_title;
		if (!$title) { $title = BluApplication::getSetting('homeTitle'); }
		if ($title) { $title.= ' | '; }
		$title.= BluApplication::getSetting('storeName');
		return $title;
	}

	/**
	 * Set document description
	 *
	 * @param string Document description
	 */
	public function setDescription($description)
	{
		$this->_description = $description;
	}

	/**
	 * Get document description
	 *
	 * @return string Document description
	 */
	public function getDescription()
	{
		return $this->_description;
	}

	/**
	 * Get site description (uses document description if available,
	 * otherwise default store description)
	 *
	 * @return string Site description
	 */
	public function getSiteDescription()
	{
		return ($this->_description ? $this->_description : BluApplication::getSetting('storeDescription'));
	}

	/**
	 * Set document keywords
	 *
	 * @param string Document keywords
	 */
	public function setKeywords($keywords)
	{
		$this->_keywords = $keywords;
	}

	/**
	 * Get document keywords
	 *
	 * @return string Document keywords
	 */
	public function getKeywords()
	{
		return $this->_keywords;
	}

	/**
	 * Get site keywords (uses document keywords if available,
	 * otherwise default store keywords)
	 *
	 * @return string Site description
	 */
	public function getSiteKeywords()
	{
		return ($this->_keywords ? $this->_keywords : BluApplication::getSetting('storeKeywords'));
	}

	/**
	 * Set document body contents
	 *
	 * @param string contents
	 */
	public function setContents($contents, $type = null)
	{
		// Some sort of validation...
		if (!in_array($type, array('topnav', 'body'))){
			$type = 'body';
		}
		
		// Set
		$this->_contents[$type] = $contents;
	}

	/**
	 * Get document body contents
	 *
	 * @return string Body contents
	 */
	public function getContents($type = null)
	{
		// Some sort of validation...
		if (!in_array($type, array('topnav', 'body'))){
			$type = 'body';
		}
		
		// Get
		return $this->_contents[$type];
	}

	/**
	 * Set content mime type
	 *
	 * @param string Content mime type
	 */
	public function setMimeType($mimeType)
	{
		$this->_mimeType = $mimeType;
	}

	/**
	 * Get content type
	 *
	 * @return string Content mime type
	 */
	public function getMimeType()
	{
		return $this->_mimeType;
	}

	/**
	 * Set content encoding
	 *
	 * @param string Encoding type
	 */
	public function setEncoding($encoding)
	{
		$this->_encoding = $encoding;
	}

	/**
	 * Get content encoding
	 *
	 * @return string Encoding type
	 */
	public function getEncoding()
	{
		return $this->_encoding;
	}

	public function setGenericHeader($genericHeader) {
		$this->_genericHeader = $genericHeader;
	}

	public function getGenericHeader() {
		return $this->_genericHeader;
	}

	/**
	 *	Set current page to display (for purposes of determining ad zones).
	 */
	public function setAdPage($page) {
		$this->_headerAdPage = $page;
	}

	/**
	 *	Get the current ad zone page.
	 */
	public function getAdPage() {
		return isset($this->_headerAdPage) ? $this->_headerAdPage : null;
	}
	
	
	/**
	 * Set cache maximum age
	 *
	 * @param int Maximum time to cache document for in seconds
	 */
	public function setMaxAge($maxAge)
	{
		$this->_maxAge = $maxAge;
	}

	/**
	 * Get cache maximum age
	 *
	 * @return int Maximum time to cache document for in seconds
	 */
	public function getMaxAge()
	{
		return $this->_maxAge;
	}

	/**
	 * Sets the document modified date
	 *
	 * @param string Last modified date
	 */
	public function setModifiedDate($date)
	{
		$this->_mdate = $date;
	}

	/**
	 * Gets the document modified date
	 *
	 * @return string Last modified date
	 */
	public function getModifiedDate()
	{
		return $this->_mdate;
	}

	/**
	 * Sets the document ETag
	 *
	 * @param string Document ETag
	 */
	public function setETag($etag)
	{
		$this->_etag = $etag;
	}

	/**
	 * Gets the document ETag
	 *
	 * @return string Document ETag
	 */
	public function getETag()
	{
		return $this->_etag;
	}

	/**
	 * Sets the document HTTP status
	 *
	 * @param string HTTP status
	 */
	public function setStatus($status)
	{
		$this->_status = $status;
	}

	/**
	 * Gets the document HTTP status
	 *
	 * @return string HTTP status
	 */
	public function getStatus()
	{
		return $this->_status;
	}

	/**
	 * Sets array for bread crumbs
	 *
	 * @param array
	 */
	public function setBreadcrumbs($breadcrumbs)
	{
		$this->_breadcrumbs = $breadcrumbs;
	}

	/**
	 * Gets array for bread crumbs
	 *
	 * @return array of arguments for bread crumbs
	 */
	public function getBreadcrumbs()
	{
		return $this->_breadcrumbs;
	}

	/**
	 * Set content disposition
	 *
	 * @param string Disposition type
	 */
	public function setDisposition($disposition)
	{
		$this->_disposition = $disposition;
	}

	/**
	 * Get content disposition
	 *
	 * @return string Disposition type
	 */
	public function getDisposition()
	{
		return $this->_disposition;
	}
	
	/**
	 * Set authentication status
	 *
	 * @param string $authentication Authentication type
	 */
	public function setAuthentication($authentication)
	{
		$this->_authentication = $authentication;
	}
	
	/**
	 * Get authentication status
	 *
	 * @return string Authentication type
	 */
	public function getAuthentication()
	{
		return $this->_authentication;
	}
	
	/**
	 *	Don't display contents (excl. headers)
	 *
	 *	@access public
	 */
	public function hideContents()
	{
		$this->_displayContents = false;
	}

	/**
	 * Add header script
	 *
	 * @param string Window event to add script to
	 */
	public function addScript($script, $event = 'domready')
	{
		if (!isset($this->_script[$event])) {
			$this->_script[$event] = $script;
		} else {
			$this->_script[$event].= $script;
		}
	}

	/**
	 * Return header script
	 *
	 * @param string Window event for which to retrieve script
	 * @return string Script
	 */
	public function getScript($event = 'domready')
	{
		if (!isset($this->_script[$event])) {
			return false;
		}

		// Get script contents
		$script = $this->_script[$event];

		// Minify
		if (!DEBUG) {
		//	$script = JSMin::minify($script);
		}

		return $script;
	}

	/**
	 * Render document
	 */
	public function render()
	{
		// Get format
		$format = $this->_format;

		// Get contents
		$contents = $this->getContents();

		// Send headers
		if (!headers_sent()) {

			// Powered by
			//header('X-Powered-By: BluApplication');

			if ($this->_authentication) {
				header('WWW-Authenticate: '.$this->_authentication);
			}

			// HTTP status?
			if ($this->_status) {
				header($this->_status);
			}

			// Last modified date
			if ($this->_mdate) {
				header('Last-Modified: '.gmdate('D, d M Y H:i:s', $this->_mdate).' GMT');
			}

			// Etag
			if ($this->_etag) {
				header('Etag: "'.$this->_etag.'"');
			}
			
			// Accept ranges.
			if ($this->_acceptRanges) {
				header('Accept-ranges: '.$this->_acceptRanges);
			}

			// Cache?
			if ($this->_maxAge > 0) {
				header('Cache-Control: max-age='.$this->_maxAge.($this->_mustRevalidate ? ', must-revalidate' : ''));
				header('Expires: '.gmdate('D, d M Y H:i:s', time() + $this->_maxAge).' GMT');
			} else {
				header('Expires: Mon, 1 Jan 2001 00:00:00 GMT'); // Expires in the past
				header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // Always modified
				header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
				header('Pragma: no-cache'); // HTTP 1.0
			}

			// Content type
			if ($contents) {
				if ($format == 'asset') {
					header('Content-type: '.$this->_mimeType);
				} else {
					header('Content-type: '.$this->_mimeType.'; charset=utf-8');
				}
			}

			// Content encoding
			if ($this->_encoding) {
				header('Content-Encoding: '.$this->_encoding);
			}
			// Content disposition
			if ($this->_disposition) {
				header('Content-Disposition: '.$this->_disposition);
			}
		}

		// Display contents
		if ($this->_displayContents) {
			
			// Send asset contents
			if ($format == 'asset') {
				echo $contents;

			// Deal with standard content
			} else {

				// Get text for template
				$storename = BluApplication::getSetting('storeName');
				$title = ($format == 'popup') ? $this->getTitle() : $this->getSiteTitle();
				$description = $this->getSiteDescription();
				$keywords = $this->getSiteKeywords();

				// Get scripts
				$includeScript = $this->getScript('includes');
				$domreadyScript = $this->getScript('domready');
				$loadScript = $this->getScript('load');
				$googleAnalReadyScript = $this->getScript('googleAnalReady');

				// Get current option name
				$option = BluApplication::getOption();
				$task = BluApplication::getTask();

				// Get language code
				$language = BluApplication::getLanguage();
				$languageCode = $language->getLanguageCode();
				
				// Get top nav
				$topNav = $this->getContents('topnav');

				// Get breadcrumbs
				$breadcrumbs = $this->_breadcrumbs ? $this->_breadcrumbs->get() : false;

				// Include site header
				switch ($format) {
				case 'site':
					/* Get ad content */
					$genericHeader = $this->getGenericHeader();
					$headerAd = Template::get('headerAdvert');
					
					/* Continue to 'print' case. */
					
				case 'print':
					include ((SITEEND == 'backend' ? BLUPATH_BASE_TEMPLATES : BLUPATH_TEMPLATES).'/site/header.php');
					break;

				case 'popup':
					include ((SITEEND == 'backend' ? BLUPATH_BASE_TEMPLATES : BLUPATH_TEMPLATES).'/popup/header.php');
					break;

				case 'simple':
					include ((SITEEND == 'backend' ? BLUPATH_BASE_TEMPLATES : BLUPATH_TEMPLATES).'/simple/header.php');
					break;

				case 'xml':
					echo '<?xml version="1.0" encoding="UTF-8"?'.'>';
					break;
				}

				// Send contents
				echo $contents;

				// Include site footer
				switch ($format) {
				case 'site':
				case 'print':
					include ((SITEEND == 'backend' ? BLUPATH_BASE_TEMPLATES : BLUPATH_TEMPLATES).'/site/footer.php');
					break;

				case 'popup':
					include ((SITEEND == 'backend' ? BLUPATH_BASE_TEMPLATES : BLUPATH_TEMPLATES).'/popup/footer.php');
					break;

				case 'simple':
					include ((SITEEND == 'backend' ? BLUPATH_BASE_TEMPLATES : BLUPATH_TEMPLATES).'/simple/footer.php');
					break;

				case 'xml':
					// Do nothing
					break;
				}
			}
		}
	}
}
?>