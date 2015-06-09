<?php

/**
 * Mailing List Helper
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class MailList
{
	/**
	 * Add contact to mailing list
	 *
	 * @param string Email address
	 * @param string First name
	 * @param string Last name
	 * @param string Address line 1
	 * @param string Address line 2
	 * @param string City
	 * @param string State
	 * @param string Zip
	 * @param string Country
	 * @param string Validated
	 * @param string Format (HTML or Text)
	 * @param string Shoe size as custom for Shoon
	 */
	public static function add($email, $firstName = null, $lastName = null, $address1 = null, $address2 = null, $city = null, $state = null, $zip = null, $country = null, $validated = 0, $type = 'H', $custom = null, $listSelections = null)
	{
		// Build info
		$info = array();
		if ($firstName) { $info['First_Name'] = $firstName; }
		if ($lastName) { $info['Last_Name'] = $lastName; }
		if ($address1) { $info['Address_Line_1'] = $address1; }
		if ($address2) { $info['Address_Line_2'] = $address2; }
		if ($city) { $info['City'] = $city; }
		if ($state) { $info['State'] = $state; }
		if ($zip) { $info['Postal_Code'] = $zip; }
		if ($country) { $info['Country'] = $country; }
		$customFieldsQuery = '';
		if (is_array($custom)) {
			$i = 1;
			foreach($custom as $customfield) {
				$info['Custom_field_'.$i] = $customfield;
				$customFieldsQuery .= 'genericText'.$i++.' = "'.$customfield.'",';
			}
		}
		$info['Email_Type'] = $type;

		// Get name
		$name = trim($firstName.' '.$lastName);

		// Add to database
		$db = BluApplication::getDatabase();
		$query = 'REPLACE INTO maillist
		  SET name = "'.$name.'",
				email = "'.$email.'",
				joinDate = "'.date("Y-m-d").'",'
				.$customFieldsQuery.
				'validated = '.(int)$validated;
		$db->setQuery($query);
		$db->query();
		$thatMLID = $db->getInsertID();
		foreach($listSelections as $listId) {
			$query = 'REPLACE INTO maillistsubscriptions
				SET subscriberId = "'.$thatMLID.'",
					listId = "'.$listId.'"';
			$db->setQuery($query);
			$db->query();
		}

	   // Add to constact contact
	   $constantContact = self::_getConstantContact();
	   return $constantContact->add($email, $info);
	}

	/**
	 * Remove contact from mailing list
	 *
	 * @param string Email address
	 * @return bool True on success, false otherwise
	 */
	public static function remove($email)
	{
		// Delete from database
		$db = BluApplication::getDatabase();
		$query = 'DELETE FROM maillist
			WHERE email = "'.$email.'"';
		$db->setQuery($query);
		$db->query();

		// Remove from constant contact
		$constantContact = self::_getConstantContact();
		return $constantContact->remove($email);
	}

	/**
	 * Gets constant contact settings amd instantiates constantcontact object
	 *
	 * @return ConstantContact Constant contact instance
	 */
	private static function _getConstantContact()
	{
		static $constanceContant;

		if (!is_object($constanceContant)) {

			// Instantiate constant contact object
			$constanceContant = new ConstantContact();

			// Load settings
			$constantContactSettings = unserialize(BluApplication::getSetting('pluginConstantContact'));
			$constanceContant->setUsername($constantContactSettings['username']);
			$constanceContant->setPassword($constantContactSettings['password']);
			$constanceContant->setCategory($constantContactSettings['constCategory']);

		}

		// Return instance
		return $constanceContant;
	}

}

?>
