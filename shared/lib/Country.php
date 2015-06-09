<?php

/**
 * Handles mapping of IP addresses to countries
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class Country
{
	/**
	 * Get list of all available countries
	 *
	 * @return array List of all countries
	 */
	public static function getCountries()
	{
		// Load contries list
		$cache = BluApplication::getCache();
		$countries = $cache->get('countries');
		if ($countries === false) {
			$db = BluApplication::getDatabase();
			$query = 'SELECT *
				FROM countries c
				ORDER BY countryName ASC';
			$db->setQuery($query);
			$countries = $db->loadAssocList('countryID');

			// Store in cache
			$cache->set('countries', $countries, 0, null, array('compress' => true));
		}
		return $countries;
	}

	/**
	 * Get country details
	 *
	 * @param int Country ID
	 * @return array Country details
	 */
	public static function getCountry($countryId)
	{
		$countries = Country::getCountries();
		return isset($countries[$countryId]) ? $countries[$countryId] : false;
	}

	/**
	 * Get country details from name (currently required as orders don't yet store ID)
	 *
	 * NOTE: Deprecated
	 *
	 * @todo: Remove all requirements on this
	 *
	 * @param string Country name (e.g. United Kingdom)
	 * @return array Country details
	 */
	public static function getCountryFromName($countryName)
	{
		$db = BluApplication::getDatabase();
		$query = 'SELECT *
			FROM countries
			WHERE countryName = "'.Database::escape($countryName).'"
			';
		$db->setQuery($query, 0, 1);
		$country = $db->loadAssoc();
		return $country;
	}



	/**
	 * Get country details for current visitor
	 *
	 * @return array Country details
	 */
	public static function getVisitorCountry()
	{
		if (!$countryId = Session::get('countryId')) {

			// Get database object
			$db = BluApplication::getDatabase();

			// Get visitors IP address
			$ip = Request::getVisitorIPAddress();
			$code = sprintf("%u", ip2long($ip));

			// Get country from database mapping
//			$query = 'SELECT c.countryID
//				FROM iptocountry AS i
//					LEFT JOIN countries AS c ON c.iso = i.ISO2
//				WHERE begin <= "'.$code.'" AND end > "'.$code.'"';
//			$db->setQuery($query, 0, 1);
//			$countryId = $db->loadResult();

			// Fall-back to site admins country
			if (!$countryId) {
				$countryId = BluApplication::getSetting('adminCountry');
			}

			// Store in session
			Session::set('countryId', $countryId);
		}

		// Return country details
		return self::getCountry($countryId);
	}

	/**
	 * Set the visitors country (called when a user logs in)
	 *
	 * @param string Country ID
	 */
	public static function setVisitorCountry($countryId)
	{
		Session::set('countryId', $countryId);
	}
}
?>
