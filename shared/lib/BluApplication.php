<?php

/**
 * BluApplication BluApplication
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class BluApplication
{
	/**
	 *	Backend switch.
	 */
	const BACKEND_SWITCH = 'oversight';

	/**
	 * Requested uri
	 *
	 * @var string
	 */
	private static $_uri;

	/**
	 * Requested argumemts (parsed from URL)
	 *
	 * @var array
	 */
	private static $_args;

	/**
	 * Requested config (DEBUG only)
	 *
	 * @var string
	 */
	private static $_configName = false;

	/**
	 * Requested language
	 *
	 * @var string
	 */
	private static $_languageCode;

	private static $_cacheKey;
	private static $_viewCache;
	private static $_cachedDocument;

	/**
	 * Requested currency
	 *
	 * @var string
	 */
	private static $_currencyCode;

	/**
	 * Requested option (i.e. controller)
	 *
	 * @var string
	 */
	private static $_option = 'index';

	/**
	 * Requested task (i.e. controller method)
	 *
	 * @var string
	 */
	protected static $_task = 'view';

	/**
	 * Redirect url requested by dispatched controller
	 *
	 * @var string
	 */
	private $_redirect = false;

	/**
	 * Breadcrumbs.
	 *
	 * @var string
	 */
	private static $_breadcrumbs = null;

	/**
	 *	Settings
	 *
	 *	@access private
	 *	@var array
	 */
	private static $_settings;

	/**
	 *	BluApplication object constructor
	 *
	 *	@access private
	 */
	private function __construct()
	{
		$siteId = self::getSiteId();

		// Get config name from domain if in DEBUG mode
		//}

		// Set cache directory as soon as we know it
		define('BLUPATH_CACHE', BLUPATH_BASE.'/cache/'.$siteId);
				/*
         * Added by Leon
         * Deal with duplicated links such as artiles/articles/recipes/xxx.htm
         * Find out the true links for the duplicated items
         */
		//echo "<!--" . $args . "-->";
		/*
		*
		* @param string Configuration setting name
		*	@param mixed Fallback value
		* @return mixed Configuration setting value
		*/
		// Register error handler
		register_shutdown_function(array(new Error(),'shutdownHandler'));
		set_exception_handler(array(new Error(), 'handleException'));
		set_error_handler(array(new Error(),'handleError'));

		Session::start();


		$baseUrl = self::getSetting('baseUrl');

		// Set default timezone
		date_default_timezone_set(self::getSetting('timeZone'));

		// Perform internal routing
		self::_applyRedirectMap();

		// Get arguments from request URI
		self::$_uri = preg_replace('$^'.$baseUrl.'/$', '', $_SERVER['REQUEST_URI']).'/';
		$args = Request::getString('args', self::$_uri);
		$qsPos = strpos($args, '?');
		if ($qsPos !== FALSE) {
			$args = substr($args, 0, $qsPos);
		}
		$args = trim($args, '/');


		/*
		 * Added by Leon
		 * Deal with duplicated links such as artiles/articles/recipes/xxx.htm
		 * Find out the true links for the duplicated items
		 */

		if(!empty($args) && (substr($args,-4) == ".htm"))
		{
			$filters = array("article/", "articles/", "recipe/", "recipes/");
			//$args = str_replace($filters, "", $args);

			$tmpArgs = explode('/', trim($args));
			$slug = $tmpArgs[(count($tmpArgs)-1)];
			$slug = substr($slug, 0, -4);

			$typeQuery = "SELECT type,slug from articles WHERE slug like '" . $slug . "' LIMIT 0,1";
			//echo $typeQuery . "<br>";
			$r = mysql_query($typeQuery);
			if($r)
			{
				while($row = mysql_fetch_array($r))
				{
					$args = str_replace($filters, $row["type"] . 's/', $args);
				}
			}
		}
		//echo "<!--" . $args . "-->";

		// add more custom links
		if(trim($args) == "articles/a_dash_of_fun/current_contests") $args = "articles/enter_our_giveaways.htm";
		if(trim($args) == "articles/facebook/forgot-password-form") $args = "facebook/login/forgot-password-form";

		if (!empty($args)) {
			$args = explode('/', trim($args, '/'));
		}

		// Has bot?
		define('ISBOT', Request::isBot());

		// Set up session

		// Detect site-end and set default controller
		if (!empty($args) && ($args[0] == self::BACKEND_SWITCH)) {
			array_shift($args);
			define('SITEEND', 'backend');
		} else {
			define('SITEEND', 'frontend');
		}

		// Check if visitor is banned
		$visitorIP = Request::getVisitorIPAddress();
		$permissionsModel = BluApplication::getModel('permissions');
		if ($permissionsModel->isBannedIP($visitorIP)) {
			die('You are banned.');
		}

		// Get visitor country
		$visitorCountry = Country::getVisitorCountry();

		// Shift language from arguments
		if (!empty($args) && Language::isValidLanguageCode(strtolower($args[0]))) {
			$languageCode = array_shift($args);
		} elseif (Language::isValidLanguageCode($visitorCountry['countryLangCode'])) {
			$languageCode = $visitorCountry['countryLangCode'];
		} else {
			$languageCode = self::getSetting('defaultLang', 'en');
		}
		self::$_languageCode = strtolower($languageCode);

		// Override default controller as appropriate
		if (SITEEND == 'backend') {
			self::$_option = self::getSetting('defaultBackendController');
		} elseif (!empty($args)) {
			self::$_option = self::getSetting('defaultFrontendController');
		}

		// Site URLs (these are defined for convenience as they are used *everywhere*)
		define('SITEURL', $baseUrl.(SITEEND == 'backend' ? '/'.self::BACKEND_SWITCH : '').(self::$_languageCode == 'en' ? '' : '/'.self::$_languageCode));
		define('FRONTENDSITEURL', $baseUrl.(self::$_languageCode == 'en' ? '' : '/'.self::$_languageCode));
		define('SITESECUREURL', 'https://'.$_SERVER['HTTP_HOST'].SITEURL);
		define('SITEINSECUREURL', 'http://'.$_SERVER['HTTP_HOST'].SITEURL);
		define('FRONTENDSITESECUREURL', 'https://'.$_SERVER['HTTP_HOST'].FRONTENDSITEURL);
		define('FRONTENDSITEINSECUREURL', 'http://'.$_SERVER['HTTP_HOST'].FRONTENDSITEURL);
		define('ASSETURL', $baseUrl.'/assets');
		define('SITEASSETURL', $baseUrl.'/'.SITEEND.'/'.$siteId);
		define('COREASSETURL', $baseUrl.'/'.SITEEND.'/base');

		// Do redirects based upon site specific rules
		if ($redirect = Request::getRedirect(self::$_uri)) {   // This should be better (use document)
			header('Location: '.$redirect['destinaton'], true, $redirect['responseCode']);
			die();
		}

		// Determine controller
		$option = strtolower(Request::getString('controller', empty($args[0]) ? '' : $args[0]));
		if ($option && ($option != 'frontend') && ($option != 'backend')) {
			$controllerName = ucfirst($option).'Controller';
			if (file_exists(BLUPATH_BASE.'/'.SITEEND.'/base/controllers/'.$controllerName.'.php') || file_exists(BLUPATH_BASE.'/'.SITEEND.'/'.$siteId.'/controllers/'.$controllerName.'.php')) {
				if (!empty($args) && ($option == $args[0])) {
					array_shift($args);
				}
				self::$_option = $option;
			}
		}

		// Determine controller name
		$option = strtolower(Request::getString('controller', empty($args[0]) ? '' : $args[0]));
		$controllerName = false;
		if ($option && !in_array($option, array('frontend', 'clientfrontend', 'backend'))) {
			if ($controllerName = self::_includeController($option)) {
				if (!empty($args) && ($option == $args[0])) {
					array_shift($args);
				}
				self::$_option = $option;
			}
		}
		if (!$controllerName) {
			if (!defined("CLI")) $controllerName = self::_includeController(self::$_option);
		}

		//$controllerName = ucfirst(strtolower(self::$_option)).'Controller';
		//$controllerName = self::_includeController(self::$_option);

		// Admin auth for backend
		if (SITEEND == 'backend' && !$permissionsModel->allowAdminAccess($controllerName, $siteId)) {
			$controllerName = false;
			self::$_option = false;
			$document = BluApplication::getDocument();
			$document->setStatus('HTTP/1.1 401 Unauthorized');
			$document->setAuthentication('Basic realm="'.self::getSetting('storeName').' Admin"');
			$document->hideContents();

		// Staging server authentication
		} else if (STAGING && !$permissionsModel->allowStagingAccess()) {
			$controllerName = false;
			self::$_option = false;
			$document = BluApplication::getDocument();
			$document->setStatus('HTTP/1.1 401 Unauthorized');
			$document->setAuthentication('Basic realm="'.self::getSetting('storeName').' Staging"');
			$document->hideContents();
		}

		// Shift task from arguments
		if (!empty($args)) {
			$task = strtolower($args[0]);
			if (method_exists($controllerName, $task)) {
				array_shift($args);
				self::$_task = $task;
			}
		}

		// Override task in request?
		if ($task = Request::getString('task')) {
			$task = strtolower($task);
			if (method_exists($controllerName, $task)) {
				self::$_task = $task;
			}
		}

		// Store remaining arguments
		self::$_args = $args;
		if (Session::get('UserID') == false) {
			$cache = self::getCache();
			if ($_SERVER['REQUEST_URI'] != 'contact') {
				self::$_cacheKey = 'viewcache_'.$_SERVER['REQUEST_URI'];

				self::$_cachedDocument =  $cache->get(self::$_cacheKey, null, 0, false);

				if (self::$_cachedDocument !== false) {
					$cache->increment(self::$_cacheKey.'_views', $siteId);
					self::$_viewCache = true;
					if (!DEBUG) return;
				}
			}
		} else {
			self::$_viewCache = self::$_cachedDocument = false;
		}
		if (DEBUG) self::$_viewCache = self::$_cachedDocument = false;


		// Set template path
		define('BLUPATH_TEMPLATES', BLUPATH_BASE.'/'.SITEEND.'/'.$siteId.'/templates');
		define('BLUPATH_BASE_TEMPLATES', BLUPATH_BASE.'/'.SITEEND.'/base/templates');
		define('BLUPATH_LANGUAGE', BLUPATH_BASE.'/'.SITEEND.'/'.$siteId.'/languages/'.self::$_languageCode);
		define('BLUPATH_ASSETS', BLUPATH_BASE.'/assets/'.$siteId);
	}

	/**
	 *	Apply the redirect mappings to the request URI and GET/POST
	 *
	 *	@static
	 *	@access private
	 */
	private static function _applyRedirectMap()
	{
		// Apply redirect mappings
		$routesModel = self::getModel('routes');
		$mapped = $routesModel->route(urldecode($_SERVER['REQUEST_URI']), $rerouted);
		if ($rerouted) {
			$appendTrailingSlash = ($mapped = preg_replace('/^(.*)\/$/', '$1', $mapped));

			// Parse out base url and get vars from mapping
			$urlPieces = explode('?', $mapped);
			$mappedUri = $urlPieces[0];
			if (isset($urlPieces[1])) {
				$getVars = explode('&', $urlPieces[1]);

				// Push get vars into request
				if (!empty($getVars)) {
					foreach($getVars as $var) {
						if (strpos($var, '=') === false) {
							continue;
						}
						list($key, $value) = explode('=', $var);
						$_GET[$key] = $value;
						$_REQUEST[$key] = $value;
					}
				}
			}

			if ($appendTrailingSlash) {
				$mapped .= '/';
			}
		}

		// Store new request URI
		$_SERVER['REQUEST_URI'] = $mapped;
	}

	/**
	 * Returns a reference to the global {@link BluApplication} object, only creating it
	 * if it doesn't already exist
	 */
	public static function &getInstance()
	{
		static $instance;
		if (!$instance) {
			$c = __CLASS__;
			$instance = new $c();
		}
		return $instance;
	}

	/**
	 * Get site Id
	 *
	 * @return string siteId
	 */
	public static function getSiteId() {
		$config = new Config();
		$settings = get_object_vars($config);
		return $settings['siteId'];
	}

	/**
	 * Get configuration setting
	 *
	 * @param string Configuration setting name
	 *	@param mixed Fallback value
	 * @return mixed Configuration setting value
	 */
	public static function getSetting($item, $default = null)
	{
		// Load configuration settings
		if (empty(self::$_settings)) {

			// Load static config
			$config = new Config();
			self::$_settings = get_object_vars($config);

			$siteId = self::$_settings['siteId'];

			// Multisites
			if (!isset(self::$_settings['sites'])) {
				self::$_settings['sites'] = array($siteId => $siteId);
			}

			// Load database settings
			$cache = self::getCache();
			$dbSettings = $cache->get('settings');
			if ($dbSettings === false) {
				$db = self::getDatabase();

				// Get for site specific if there is an entry in siteId - new clients
				$query = 'SELECT c.*
					FROM `config` AS `c`
					WHERE c.siteId = "'.$db->escape($siteId).'"';
				$db->setQuery($query);
				$dbSettings = $db->loadResultAssocArray('configKey', 'configValue');

				// if there is no entry in config siteId column get everything what is blank - old clients
				if (empty($dbSettings)) {
					$query = 'SELECT c.*
						FROM `config` AS `c`
						WHERE c.siteId = ""';
					$db->setQuery($query);
					$dbSettings = $db->loadResultAssocArray('configKey', 'configValue');
				}

				// Unserialize all settings
				foreach ($dbSettings as &$dbSetting) {
					$dbSetting = unserialize($dbSetting);
				}

				// Store in cache
				$cache->set('settings', $dbSettings);
			}

			// One settings array to rule them all
			self::$_settings = array_merge(self::$_settings, $dbSettings);
		}

		// Return setting, or default if not set
		if (strpos($item, ':')) {
			list($parentItem, $childItem) = explode(':', $item);
			return isset(self::$_settings[$parentItem][$childItem]) ? self::$_settings[$parentItem][$childItem] : $default;
		} else {
			return isset(self::$_settings[$item]) ? self::$_settings[$item] : $default;
		}
	}

	/**
	 * Get the global database object
	 *
	 * @return object Database
	 */
	public static function &getDatabase()
	{
		static $instance;
		if (!$instance) {
			// MultiDatabase!
			if ($databases = self::getSetting('databases', false)) {
				$database = $databases[array_rand($databases)];
			} else {
				$database = Array ( 	'databaseHost' => self::getSetting('databaseHost'),
							'databaseUser' => self::getSetting('databaseUser'),
							'databasePass' => self::getSetting('databasePass'),
							'databaseName' => self::getSetting('databaseName'));
			}

			$instance = Database::getInstance($database['databaseHost'], $database['databaseUser'], $database['databasePass'], $database['databaseName']);
		}
		return $instance;
	}

	/**
	 * Get the global cache object
	 *
	 * @return object Cache
	 */
	public static function &getCache()
	{
		static $instance;
		if (!$instance) {
			$instance = Cache::getInstance(self::getSetting('memcacheHost'), self::getSetting('memcachePort'), self::getSetting('caches:data', null));
		}
		return $instance;
	}

	/**
	 * Get the global language object
	 *
	 * @return object Language
	 */
	public static function &getLanguage()
	{
		static $instance;

		// Determine language to use and create language object
		if (!$instance) {
			$instance = Language::getInstance(self::$_languageCode);
		}
		return $instance;
	}

	/**
	 * Get a data model object
	 *
	 * @param string Model name
	 * @return FrontendModel Model instance
	 */
	public static function &getModel($name)
	{
		static $instances;
		if (!isset($instances)) {
			$instances = array();
		}

		// Get model name
		$name = ucfirst(strtolower($name)).'Model';
		$foundName = $name;

		// Get model arguments
		$args = func_get_args();
		$args = array_slice($args, 1);
		$signature = $name.'_'.serialize($args);

		// Load model instance
		if (empty($instances[$signature])) {
			$siteId = self::getSetting('siteId');

			// Include base shared model
			require_once(BLUPATH_BASE.'/shared/base/models/BluModel.php');

			// Include shared model if exists
			$sharedModelPath = BLUPATH_BASE.'/shared/base/models/'.$name.'.php';
			if (file_exists($sharedModelPath)) {
				require_once($sharedModelPath);
				$foundName = $name;
			}

			// Include site shared model if exists
			$siteSharedModelPath = BLUPATH_BASE.'/shared/'.$siteId.'/models/'.$name.'.php';
			$baseSharedModelPath = BLUPATH_BASE.'/shared/base/models/Client'.$name.'.php';
			if (file_exists($siteSharedModelPath)) {
				require_once($siteSharedModelPath);
				$foundName = 'Client'.$name;
			} else if (file_exists($baseSharedModelPath)) {
				require_once($baseSharedModelPath);
				$foundName = 'Client'.$name;
			}

			// RoutesModel needs to load without a SITEEND
			if (defined('SITEEND')) {

				// Include end model if exists
				$endModelPath = BLUPATH_BASE.'/'.SITEEND.'/base/models/'.$name.'.php';
				if (file_exists($endModelPath)) {
					require_once($endModelPath);
					$foundName = ucfirst(SITEEND).$name;
				}

				// Load site model if exists
				$siteModelPath = BLUPATH_BASE.'/'.SITEEND.'/'.$siteId.'/models/'.$name.'.php';
				if (file_exists($siteModelPath)) {
					require_once($siteModelPath);
					$foundName = 'Client'.ucfirst(SITEEND).$name;
				}
			}

			// Get model instance
			if (count($args)) {
				$reflectionObj = new ReflectionClass($foundName);
				$instances[$signature] = $reflectionObj->newInstanceArgs($args);
			} else {
				$instances[$signature] = new $foundName();
			}
		}
		return $instances[$signature];
	}

	/**
	 * 	Get a data model object - pretty much a direct copy from getModel function.
	 *	The need for this is that we need to separate classes representing real-world concepts from classes that control them, even though both types need access to the database and thus both categorise as models.
	 *
	 * 	@param string Model name
	 * 	@return BluObject Model instance
	 */
	public static function getObject($name)
	{
		// Get model name
		$name = ucfirst(strtolower($name)).'Object';

		// Get model arguments
		$args = func_get_args();
		$args = array_slice($args, 1);

		// Load site-specific model if exists
		$siteId = self::getSetting('siteId');
		$siteObjectPath = BLUPATH_BASE.'/'.SITEEND.'/'.$siteId.'/models/objects/'.$name.'.php';
		if (file_exists($siteObjectPath)) {
			require_once ($siteObjectPath);
			$name = ucfirst($siteId).$name;
		}

		// Get model instance
		$reflectionObj = new ReflectionClass($name);
		return $reflectionObj->newInstanceArgs($args);

	}

	/**
	 * Get details of given plugins
	 *
	 * @param string Plugin type
	 * @param bool Enabled status
	 */
	public static function getPlugins($type = null, $enabled = null)
	{
		$siteId = self::getSetting('siteId');

		$cache = self::getCache();
		$cacheKey = 'plugins_'.$type.'_'.$enabled;
		$plugins = $cache->get($cacheKey);
		if ($plugins === false) {
			$db = self::getDatabase();

			// Build where clauses
			$where = array();
			if ($type !== null) {
				$where[] = ' type = "'.$db->escape($type).'"';
			}
			if ($enabled !== null) {
				$where[] = ' enabled = '.($enabled ? 1 : 0);
			}

			// Get plugins
			$query = 'SELECT *
				FROM plugins WHERE siteId = "'.$db->escape($siteId).'"';
			if (!empty($where)) {
				$query .= ' AND '.implode(' AND ', $where);
			}
			$query .= ' ORDER BY sequence';
			$db->setQuery($query);
			$plugins = $db->loadAssocList();

			// Unserialize settings
			if (!empty($plugins)) {
				foreach ($plugins as &$plugin) {
					$plugin['settings'] = unserialize($plugin['settings']);
				}
			}

			// Store in cache
			$cache->set($cacheKey, $plugins);
		}

		return $plugins;
	}

	/**
	 * Include source files required for the given plugin
	 *
	 * @param string Plugin type
	 * @param string Plugin id
	 * @return string Plugin class name
	 */
	private static function _includePlugin($type, $name)
	{
		$type = strtolower($type);
		$name = ucfirst(strtolower($name));

		// Include base plugin
		require_once(BLUPATH_BASE.'/shared/plugins/Plugin.php');

		// Include type base plugin
		$typeBaseFile = BLUPATH_BASE.'/shared/plugins/'.$type.'/'.ucfirst($type).'.php';
		if (!file_exists($typeBaseFile)) {
			return false;
		}
		require_once($typeBaseFile);

		// Include the plugin itself
		$pluginFile = BLUPATH_BASE.'/shared/plugins/'.$type.'/'.ucfirst($type).$name.'.php';
		if (!file_exists($pluginFile)) {
			return false;
		}
		require_once($pluginFile);

		// Return class name
		return ucfirst($type).$name;
	}

	/**
	 * Get a plugin object
	 *
	 * @param string Plugin name
	 * @return Plugin Plugin instance
	 */
	public static function &getPlugin($type, $name)
	{
		static $instances;
		if (!isset($instances)) {
			$instances = array();
		}

		// Get plugin name
		$type = strtolower($type);
		$name = ucfirst(strtolower($name));

		// Get plugin arguments
		$args = func_get_args();
		$args = array_slice($args, 1);
		$signature = $type.$name.'_'.serialize($args);

		// Load plugin instance
		if (empty($instances[$signature])) {

			// Include base plugin, type base plugin, and the plugin itself
			$pluginName = self::_includePlugin($type, $name);
			if (!$pluginName) {
				return false;
			}

			// Get plugin instance
			if (count($args)) {
				$reflectionObj = new ReflectionClass($pluginName);
				$instances[$signature] = $reflectionObj->newInstanceArgs($args);
			} else {
				$instances[$signature] = new $pluginName();
			}
		}

		return $instances[$signature];
	}

	/**
	 * Get the current default plugin of the given type
	 * (first enabled plugin of type according to sequence)
	 *
	 * @return Plugin Default plugin instance
	 */
	public static function &getDefaultPlugin($type)
	{
		$type = strtolower($type);

		// Get default plugin name
		$plugins = self::getPlugins($type, true);
		if (empty($plugins)) {
			$ret = false;
			return $ret;
		}

		// Return default plugin instance
		$defaultPlugin = reset($plugins);
		return self::getPlugin($type, $defaultPlugin['id']);
	}

	/**
	 * Include source files required for the given controller
	 *
	 * @param string Controller name
	 * @return string The name of the found controller, or false
	 */
	private static function _includeController($name)
	{
		// Get controller name
		$name = ucfirst(strtolower($name)).'Controller';
		$foundName = false;

		// Prepare
		$siteId = self::getSetting('siteId');
		// Include end base controller
		require_once(BLUPATH_BASE.'/'.SITEEND.'/base/controllers/'.ucfirst(SITEEND).'Controller.php');
//if (CLI) echo (BLUPATH_BASE.'/'.SITEEND.'/base/controllers/'.ucfirst(SITEEND).'Controller.php');

		// Include client-specific end base controller
		$baseControllerPath = BLUPATH_BASE.'/'.SITEEND.'/base/controllers/Client'.ucfirst(SITEEND).'Controller.php';
		$siteBaseControllerPath = BLUPATH_BASE.'/'.SITEEND.'/'.$siteId.'/controllers/Client'.ucfirst(SITEEND).'Controller.php';
		if (file_exists($siteBaseControllerPath)) {
//if (CLI) echo $siteBaseControllerPath;
			require_once($siteBaseControllerPath);
		} elseif (file_exists($baseControllerPath)) {
//if (CLI) echo $baseControllerPath;
			require_once($baseControllerPath);
		}

		// Include end controller if exits
		$endControllerPath = BLUPATH_BASE.'/'.SITEEND.'/base/controllers/'.$name.'.php';
//if (CLI) echo $endControllerPath;
		if (file_exists($endControllerPath)) {
			require_once($endControllerPath);
			$foundName = $name;
		}

		// Load site-specific end controller if exists
		$siteControllerPath = BLUPATH_BASE.'/'.SITEEND.'/'.$siteId.'/controllers/'.$name.'.php';
//if (CLI) echo $siteControllerPath;
		if (file_exists($siteControllerPath)) {
			require_once ($siteControllerPath);
			$foundName = ucfirst($siteId).$name;
		}

		return $foundName;
	}

	/**
	 * Get a controller instance
	 *
	 * @param string Controller name
	 * @return FrontendController Controller instance
	 */
	public static function getController($name, $args = null)
	{
		// Include controller
		$foundName = self::_includeController($name);
		if (!$foundName) {
			return false;
		}

		// Get controller instance
		$controller = new $foundName($args);
		return $controller;
	}

	/**
	 * Get a module helper object
	 *
	 * @param string name
	 * @return Modules instance
	 */
	public static function &getModules($name)
	{
		static $instances;
		if (!isset($instances)) {
			$instances = array();
		}

		// Get modules-helper name
		$name = ucfirst(strtolower($name)).'Modules';

		// Get modules-helper arguments
		$args = func_get_args();
		$args = array_slice($args, 1);
		$signature = $name.'_'.serialize($args);

		// Load modules-helper instance
		if (empty($instances[$signature])) {
			// Load site-specific modules-helper if exists
			$siteId = self::getSetting('siteId');
			$siteModulesPath = BLUPATH_BASE.'/'.SITEEND.'/'.$siteId.'/modules/'.$name.'.php';
			if (file_exists($siteModulesPath)) {
				require_once ($siteModulesPath);
				$name = ucfirst($siteId).$name;
			}

			// Get modules-helper instance
			$reflectionObj = new ReflectionClass($name);
			$instances[$signature] = $reflectionObj->newInstanceArgs($args);
		}
		return $instances[$signature];
	}

	/**
	 * Get the global document object
	 *
	 * @return object Document
	 */
	public static function &getDocument()
	{
		static $instance;
		if (!$instance) {
			$format = Request::getString('format', 'site');
			$instance = Document::getInstance($format);
		}
		return $instance;
	}

	/**
	 * Shortcut to get the current user
	 *
	 * @return PersonObject or null
	 */
	public static function getUser()
	{
		$userModel = self::getModel('user');
		return $userModel->getCurrentUser();
	}

	/**
	 * Get the requested arguments
	 *
	 * @return array Array of arguments
	 */
	public static function getArgs()
	{
		return self::$_args;
	}

	/**
	 * Get the option requested
	 *
	 * @return string The current option name
	 */
	public static function getOption()
	{
		return self::$_option;
	}

	/**
	 * Get the task requested
	 *
	 * @return string The current task name
	 */
	public static function getTask()
	{
		return self::$_task;
	}

	/**
	 * Get the associated breadcrumb trail.
	 *
	 * @return string
	 */
	public static function getBreadcrumbs()
	{
		return self::$_breadcrumbs;
	}



	/**
	 * Dispatches the application
	 *
	 * Pulls the relevant options from the request and calls the correct
	 * controller method.
	 */
	public function dispatchRaw($controller,$task=null,$args=Array())
	{
		if (!$task) {
			$task = 'view';
		}

		// Render controller
		$controller = $this->getController($controller, $args);
		if ($controller != false) {
			$controller->$task();

			// Get redirect if requested
			$this->_redirect = $controller->getRedirect();
		} else {
			return false;
		}
	}


	/**
	 * Dispatches the application
	 *
	 * Pulls the relevant options from the request and calls the correct
	 * controller method.
	 */
	public function dispatch()
	{
		if (!self::getOption()) {
			return;
		}

		$args = self::getArgs();
		$option = self::getOption();

//		$cache = self::getCache();


		$siteEnd = 'unknown';

		if (defined('SITEEND')) {
	 		$siteEnd = SITEEND;
		}

		if (($option == 'recipes' || $option == 'index' || $option == 'profile' || $option == 'articles') && (self::$_task != 'contact') && Session::get('UserID') == false && $siteEnd != 'backend') {
			self::$_viewCache = true;
		} else {
			self::$_viewCache = false;
		}

		if (DEBUG || (defined('NOVIEWCACHE') && NOVIEWCACHE)) self::$_viewCache = self::$_cachedDocument = false;
//		self::$_cachedDocument =  $cache->get(self::$_cacheKey, null, 0, false);

		if (self::$_cachedDocument !== false && self::$_viewCache == true) {
			return;
		}


		// Start breadcrumb instance
		self::$_breadcrumbs = new Breadcrumbs();

		// Buffer output
		ob_start();

		// Render controller
		$controller = $this->getController($option, $args);
		$taskName = self::$_task;
		$controller->$taskName();

		// Get redirect if requested
		$this->_redirect = $controller->getRedirect();

		// Set document contents
		$document = $this->getDocument();
		$document->setContents(ob_get_clean());
		$document->setBreadcrumbs(self::$_breadcrumbs);

		// Get top nav, after main controller task has been completed.
		switch ($document->getFormat()) {
			case 'site':
			case 'print':
				// Not backend
				if (SITEEND == 'backend') {
					break;
				}

				// Get top nav
				ob_start();
				$controller->topnav();
				$document->setContents(ob_get_clean(), 'topnav');
				break;
		}
	}

	/**
	 * Renders the BluApplication
	 */
	public function render()
	{

		if (self::$_cachedDocument !== false && self::$_viewCache == true) {
//			if ($_SERVER['REMOTE_ADDR'] == '87.80.43.97')
//				Utility::irc_dump('successfully served a cached document '.$_SERVER['REQUEST_URI']. ' - '.memory_get_peak_usage(), 'max');
			echo self::$_cachedDocument;
			return;
		}

//			if ($_SERVER['REMOTE_ADDR'] == '87.80.43.97')
//			Utility::irc_dump('successfully served a NON-cached document '.$_SERVER['REQUEST_URI']. ' - '.memory_get_peak_usage(), 'max');
		// Redirect if requested
		if ($redirect = $this->_redirect) {

			// Append referrer for return redirections from login
			$path = str_replace('return=1', 'redirect='.base64_encode(self::$_uri), $redirect['destination']);
			$url = ($_SERVER['SERVER_PORT'] == 80 ? 'http://' : 'https://');
			$url.= $_SERVER['SERVER_NAME'].SITEURL.$path;
			if ($redirect['responseCode']) {
				header('Location: '.$url, true, $redirect['responseCode']);
			} else {
				header('Location: '.$url, true);
			}
			return;
		}

		// Force caching-off for backend.
		if (SITEEND == 'backend'){
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		}


		// Get document
		$document = $this->getDocument();
		$format = $document->getFormat();

		// Buffer output (optionally using Gzip)
		$useGzip = $format == 'asset' ? false : self::getSetting('useGzip', true);
		$bufferCallback = ($useGzip ? 'ob_gzhandler' : null);
		ob_start($bufferCallback);

		// Render document
		$document->render();

		if (self::$_viewCache == true) {
			$cache = self::getCache();
			$cache->set(self::$_cacheKey, ob_get_contents(), 600);
		}

		// Flush buffer
		ob_flush();
		//$this->saveScriptLog();

		/**
		 * @desc We need to make sure we close the db connections
		 * @author Leon Zhao
		 */
		$db = BluApplication::getDatabase();
		$r = $db->close();

	}
	public function saveScriptLog()
	{
		date_default_timezone_set('America/Chicago');
		global $timeStartLog;
		$data = array();
		$timeEndLog = microtime(true);
		$data['start'] = date('Y-m-d H:i:s', $timeStartLog);
		$data['end'] = date('Y-m-d H:i:s', $timeEndLog);
		$data['life'] = round($timeEndLog - $timeStartLog, 2);
		$data['script_url'] = $_SERVER['REQUEST_URI'];
		$data['client_ip_address'] = $_SERVER['REMOTE_ADDR'];
		$data['server_address'] = $_SERVER['SERVER_ADDR'];

		$sql = "INSERT INTO `log_script_life` (`id`, `start`, `end`, `life`, `script_url`, `client_ip_address`, `server_address`)
				VALUES (
					'NULL',
					'" . $data['start'] . "',
					'" . $data['end'] . "',
					'" . $data['life'] . "',
					'" . $data['script_url'] . "',
					'" . $data['client_ip_address'] . "',
					'" . $data['server_address'] . "'
				)";
		mysql_query($sql);
	}
}
?>
