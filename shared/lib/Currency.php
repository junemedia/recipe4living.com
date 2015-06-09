<?php

/**
 * Currency Helper Object
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class Currency
{
	/**
	 * Currency code
	 *
	 * @var string
	 */
	private $_currencyCode = null;

	/**
	 * Currency object constructor
	 *
	 * @param string Currency code
	 */
	private function __construct($currencyCode)
	{
		// Store currency code
		$this->_currencyCode = $currencyCode;
	}

	/**
	 * Returns a reference to the global Currency object, only creating it
	 * if it doesn't already exist
	 */
	public static function getInstance($currencyCode)
	{
		static $instances;
		if (!isset($instances)) {
			$instances = array();
		}
		
		$args = func_get_args();
		$signature = serialize($args);
		
		if (empty($instances[$signature])) {
			$c = __CLASS__;
			$instances[$signature] = new $c($currencyCode);
		}
		return $instances[$signature];
	}

	/**
	 * Get current currency code
	 *
	 * @return string Currency code
	 */
	public function getCurrencyCode()
	{
		return $this->_currencyCode;
	}
	
	/**
	 * Get all currency details
	 * 
	 * @return array Array of all currency details, indexed by code
	 */
	public function getAllCurrencyDetails()
	{
		static $currencyDetails;
		
		// Get cache and database objects
		$cache = BluApplication::getCache();
		$db = BluApplication::getDatabase();

		// Get all details from cache/db
		if (!$currencyDetails) {
			$currencyDetails = $cache->get('currencyDetails');
			if ($currencyDetails === false) {
				
				$query = 'SELECT *
					FROM currency
					WHERE enabled = 1
					ORDER BY sequence';
				$db->setQuery($query);
				$currencyDetails = $db->loadAssocList('code');
				if (!$currencyDetails) {
					return false;
				}
				
				// Store in cache
				$cache->set('currencyDetails', $currencyDetails);
			}
		}
		
		return $currencyDetails;
	}	 	 	 

	/**
	 * Get currency details (defaults to current currency)
	 *
	 * @param string Currency code
	 * @return array Currency details
	 */
	public function getCurrencyDetails($currencyCode = null)
	{
		// Default to current currency
		if (!$currencyCode) {
			$currencyCode = $this->getCurrencyCode();
		}

		// Get all currency details
		$currencyDetails = $this->getAllCurrencyDetails();
		
		// Check requested currency exists
		if (!isset($currencyDetails[$currencyCode])) {
			return false;
		}
		
		// Return details
		return $currencyDetails[$currencyCode];
	}
	
	public function getCurrencyJSON($currencyDetails = null) {
		if (!$currencyDetails) {
			$currencyDetails = $this->getCurrencyDetails();
		}
		return json_encode(array(
			'code' => $currencyDetails['code'],
			'symbol' => htmlentities($currencyDetails['symbol'], null, 'UTF-8'),
			'format' => $currencyDetails['format'],
			'symbolPosition' => $currencyDetails['symbolPosition'],
			'conversionRate' => $this->getConversionRate()
		));
		
	}
	
	/**
	 * Get conversion rate
	 * 
	 * @param string From currency code
	 * @param string To currency code
	 * @return float Conversion rate	 
	 */
	public function getConversionRate($from = null, $to = null)
	{
		// Default to converting from application default base price
		if (!$from) {
			$from = BluApplication::getSetting('baseCurrency');
		}
		
		// Default to converting to current currency code
		if (!$to) {
			$to = $this->getCurrencyCode();
		}
	
		// Check for simple case
		if ($from == $to) {
			return 1;
		}
		
		// Get spot rates
		$fromDetails = $this->getCurrencyDetails($from);
		$toDetails = $this->getCurrencyDetails($to);
		if (!$fromDetails && !$toDetails) {
			return false;
		}
		
		// Return the conversion rate
		return ($fromDetails['spotRate'] / $toDetails['spotRate']);		
	}	 
	
	/**
	 * Convert amount from one currency to another
	 * 
	 * @param string From currency code
	 * @param string To currency code
	 * @param float Amount (in from currency) 
	 */	 	 	 	 	 	
	public function convert($amount, $from = null, $to = null)
	{
		// Convert amount to float
		$amount = (float)$amount;

		// Do the conversion and return amount
		$conversionRate = $this->getConversionRate($from, $to);		
		return $amount * $conversionRate;		
	}
	
	/**
	 * Format an amount with a given style
	 * 
	 * @param float Amount
	 * @param string Style
	 * @return string Formatted amount	 
	 */
	public function format($amount, $includeSymbol = true, $currencyCode = null)
	{
		// Convert amount to float
		$amount = (float)$amount;
		
		// Get currency details
		$currencyDetails = $this->getCurrencyDetails($currencyCode);
		
		// Apply appropriate format
		switch($currencyDetails['format']) {
			case 'dotThousandsCommaDecimal':
				$ret = number_format($amount, 2, ',', '.');
				break;
			case 'dotThousandsNoDecimals':
				$str = number_format($amount, 2, '', '.');
				$ret = substr($str, 0, -3);
				break;
			case 'spaceThousandsCommaDecimal':
				$ret = number_format($amount, 2, ',', ' ');
				break;
			case 'indian':
				list($digits, $idecimals) = explode('.', number_format($amount, 2));
				if (($len = strlen($digits)) >= 5) {
					$bit = substr($digits, 0, $len - 3) / 100;
					$ret = number_format($bit, 2, ',', ',').','.substr($digits, $len - 3).'.'.$idecimals;
				} else {
					$ret = number_format($amount, 2);
				}
				break;
			case 'noDecimals':
				$str = number_format($amount, 2, '', ',');
				$ret = substr($str, 0, -3);
				break;
			case 'realNoDecimals':
				$ret = round($amount);
				break;
			case 'spaceThousandsDotDecimal':
				$ret = number_format($amount, 2, '.', ' ');
				break;
			case 'apostropheThousandsNoDecimals':
				$ret = number_format($amount, 0, '.', "'");
				break;
			case 'apostropheThousandsDotDecimal':
				$ret = number_format($amount, 2, '.', "'");
				break;
			case 'standard': default:
				$ret = number_format($amount, 2);
				break;
		}
		
		// Include currency symbol
		if ($includeSymbol) {
			switch ($currencyDetails['symbolPosition']) {
			case 'after':
				$ret = $ret.$currencyDetails['symbol'];
				break;
			case 'before': default:
				$ret = $currencyDetails['symbol'].$ret;
				break;
			}
		}
		
		// Return formatted value
		return $ret;
	}	 	 	
}
?>
