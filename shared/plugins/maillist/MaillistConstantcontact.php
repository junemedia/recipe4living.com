<?php

/**
 * Constant contact mailing list provider base class
 *
 * @package BluApplication
 * @subpackage Maillist
 */
class MaillistConstantcontact extends MaillistPlugin
{
	/**
	 * User name
	 *
	 * @var string
	 */
	private $_userName;

	/**
	 * User password
	 *
	 * @var string
	 */
	private $_password;

	/**
	 * API key
	 *
	 * @var string
	 */
	private $_apiKey;

	/**
	 * Digest auth string
	 *
	 * @var string
	 */
	private $_authString;

    /**
     * Constant contact mailing list provider constructor
     */
    public function __construct()
    {
        parent::__construct('constantcontact');

    	// Get settings
    	$this->_userName = $this->getSetting('userName');
    	$this->_password = $this->getSetting('password');
    	$this->_apiKey = $this->getSetting('apiKey');
	$this->_curlHeader = array('Content-Type: application/atom+xml');

    	// Build digest auth string
    	$this->_authString = $this->_apiKey.'%'.$this->_userName.':'.$this->_password;
    }

	/**
	* Add a recipient to a mailing list
	*
	* @param string E-mail address
	* @param string First name
	* @param string Last name
	* @param mixed Optional list id/array of lists to subscribe to
	* @param int User Id
	* @param array Array of custom fields (max. 15)
	* @param string Optional old e-mail address of subscriber (to allow updates as appropriate)
	* @param int Subscribe action source
	* @return bool True on success, false otherwise
	*/
	public function subscribeRecipient($email, $firstName = null, $lastName = null, $lists = null, $userId = null, $custom = null, $oldEmail = null, $actionBy = MaillistPlugin::ACTION_BY_USER)
	{
    		// Add to CC
		$this->_subscribeRecipient($email, $firstName, $lastName, $lists, $userId, $custom, $oldEmail, $actionBy);

		// Add to our DB if all went OK
		return parent::subscribeRecipient($email, $firstName, $lastName, $lists, $userId, $custom);
	}

	/**
	* Add a recipient to a constant contact mailing list only
	*
	* @param string E-mail address
	* @param string First name
	* @param string Last name
	* @param mixed Optional list id/array of lists to subscribe to
	* @param int User Id
	* @param array Array of custom fields (max. 15)
	* @param string Optional old e-mail address of subscriber (to allow updates as appropriate)
	* @param int Subscribe action source
	* @return bool True on success, false otherwise
	*/
	private function _subscribeRecipient($email, $firstName = null, $lastName = null, $lists = null, $userId = null, $custom = null, $oldEmail = null, $actionBy = MaillistPlugin::ACTION_BY_USER)
	{
	        // Get array of lists to subscribe to
	    	if (!is_array($lists)) {
				$lists = array($lists);
	    	}

	    	// Get array of available mailing lists
	    	$availableLists = $this->getMailingLists();

	    	// Get users address details (if we have one)
	    	$address = false;
	    	if ($userId) {
	    		$userModel = BluApplication::getModel('user');
	    		$user = $userModel->getUser($userId);

	    		if ($user['defaultBillingAddressId']) {
					$addressesModel = BluApplication::getModel('addresses');
					$address = $addressesModel->getAddress($user['defaultBillingAddressId']);
	    		}
	    	}

	    	// Check whether contact already exists
			$details = $this->_findRecipient($oldEmail ? $oldEmail : $email);
			if (isset($details['@attributes']['id'])) {
				$id = $details['@attributes']['id'];
			} else {
				$id = null;
			}

    	// Build contact entry
		$contactEntry = '
			<entry xmlns="http://www.w3.org/2005/Atom">
				<title type="text"></title>
				<updated>'.date('Y-m-d\Th:i:s.u\Z').'</updated>
				<author></author>
				<id>'.($id ? $id : 'data:,none').'</id>
				<summary type="text">Contact</summary>
				<content type="application/vnd.ctct+xml">
					<Contact xmlns="http://ws.constantcontact.com/ns/1.0/">
						<EmailAddress>'.$email.'</EmailAddress>
						<EmailType>HTML</EmailType>
						<FirstName>'.ucfirst($firstName).'</FirstName>
						<LastName>'.ucfirst($lastName).'</LastName>';

		// Address details
		if ($userId && $address) {
			$contactEntry .= '
						<CompanyName>'.$address['aCompany'].'</CompanyName>
						<HomePhone>'.$address['aTelephone'].'</HomePhone>
						<Addr1>'.$address['aAddress1'].'</Addr1>
						<Addr1>'.$address['aAddress2'].'</Addr1>
						<City>'.$address['aCity'].'</City>
						<StateName>'.$address['aCounty'].'</StateName>
						<CountryName>'.$address['aCountry'].'</CountryName>
						<PostalCode>'.$address['aZip'].'</PostalCode>';
		}

		// Custom fields
		if (!empty($custom)) {
			$i = 1;
			foreach ($custom as $customField) {
				$contactEntry .= '
							<Custom'.$i.'>'.$customField.'</Custom'.$i.'>';
				$i++;

				// Limit to 15 custom fields
				if ($i > 15) {
					break;
				}
			}
		}

		// Contact lists

		/* TODO: Remove hard-coding of action to ACTION_BY_CUSTOMER when ConstantContact fix their API */

		$contactEntry .= '
						<OptInSource>'.($actionBy == MaillistPlugin::ACTION_BY_USER ? 'ACTION_BY_CUSTOMER' : 'ACTION_BY_CUSTOMER').'</OptInSource>
						<ContactLists>';
		foreach ($lists as $listId) {
			$contactEntry .= '
							<ContactList id="http://api.constantcontact.com/ws/customers/'.$this->_userName.'/lists/'.$availableLists[$listId]['providerRef'].'" />';
		}

		// Close entry
		$contactEntry .= '
						</ContactLists>
					</Contact>
				</content>
			</entry>';

		// Submit/update contact entry
		$info = null;
		if ($id) {
			$result = Utility::curl(
				$id,
				$contactEntry,
				'',
				120,
				$this->_curlHeader,
				$this->_authString,
				'PUT',
				$info
			);
			$success = ($info['http_code'] == 204);

		} else {
			$result = Utility::curl(
				'http://api.constantcontact.com/ws/customers/'.$this->_userName.'/contacts',
				$contactEntry,
				'',
				120,
				$this->_curlHeader,
				$this->_authString,
				null,
				$info
			);
			$success = ($info['http_code'] == 201);
		}

		return $success;
    }

	/**
	* Get a recipients details
	*
	* @param string E-mail address
	* @return array Recipients details on success, false otherwise
	*/
	public function getRecipient($email, $includeProviderDetails = false)
	{
	// Get base details
		$details = parent::getRecipient($email);

		// Get CC overview and full details
		if ($includeProviderDetails) {
			if ($contact = $this->_findRecipient($email)) {
				if ($contactDetails = $this->_getRecipient($contact['@attributes']['id'])) {
					$details['providerDetails'] = array_merge($contact, $contactDetails);
				} else {
					$details['providerDetails'] = $contact;
				}
			}
		}

		// Return details
		return $details;
	}

	/**
	* Get a list of all recipients details
	*
	* @return array Array of recipients details (including list subscriptions)
	*/
	private function _getAllRecipients()
	{
	// Get contact list
	$info = null;
		$result = Utility::curl(
			'http://api.constantcontact.com/ws/customers/'.$this->_userName.'/contacts',
			null,
			'',
			120,
			$this->_curlHeader,
			$this->_authString,
			'GET',
			$info
		);
		$success = ($info['http_code'] == 200);
		if (!$success) {
			return false;
		}

		@$feed = simplexml_load_string($result);
		if ($feed && isset($feed->entry)) {
			$recipients = array();
			foreach ($feed->entry as $entry) {
				$recipients[] = Utility::objectToArray($entry);
			}
			return $recipients;
		} else {
			return false;
		}
	}

	/**
	* Find a recipients overview details by querying the collection
	*
	* @param string E-mail address
	* @return array Recipients details on success, false otherwise
	*/
	private function _findRecipient($email)
	{
	// Find contact
	$info = null;
		$result = Utility::curl(
			'http://api.constantcontact.com/ws/customers/'.$this->_userName.'/contacts',
			'email='.urlencode($email),
			'',
			120,
			$this->_curlHeader,
			$this->_authString,
			'GET',
			$info
		);
		$success = ($info['http_code'] == 200);
		if (!$success) {
			return false;
		}

		// Retrieve entry
		@$feed = simplexml_load_string($result);
		if ($feed && isset($feed->entry->content->Contact)) {
			return Utility::objectToArray($feed->entry->content->Contact);
		} else {
			return false;
		}
	}

	/**
	* Get a recipients full details
	*
	* @param string Contact link
	* @return array Recipients details on success, false otherwise
	*/
	private function _getRecipient($link)
	{
	// Get full details
	$info = null;
	$header = null;
	$result = Utility::curl(
			$link,
			null,
			'',
			120,
			$header,
			$this->_authString,
			null,
			$info
		);
		$success = ($info['http_code'] == 200);
	if (!$success) {
			return false;
		}

		// Retrieve entry
		@$feed = simplexml_load_string($result);
		if ($feed && isset($feed->content->Contact)) {
			return Utility::objectToArray($feed->content->Contact);
		} else {
			return false;
		}
	}

	/**
	* Remove a recipient from all mailing lists
	*
	* @param string E-mail address
	* @param int Subscribe action source
	* @return bool True on success, false otherwise
	*/
	public function unsubscribeRecipient($email, $actionBy = MaillistPlugin::ACTION_BY_USER)
	{
	// Remove from CC
	$this->_unsubscribeRecipient($email, $actionBy);

		// Remove from our DB
	return parent::unsubscribeRecipient($email, $actionBy);
	}

    /**
     * Remove a recipient from all constant contact mailing lists
     *
     * @param string E-mail address
     * @param int Subscribe action source
     * @return bool True on success, false otherwise
     */
    private function _unsubscribeRecipient($email, $actionBy = MaillistPlugin::ACTION_BY_USER)
    {
		// Check whether contact already exists
    	if ($contact = $this->_findRecipient($email)) {
    		$id = $contact['@attributes']['id'];

    		// Remove contact from all lists
    		$contactEntry = '
			<entry xmlns="http://www.w3.org/2005/Atom">
				<title type="text"></title>
				<updated>'.date('Y-m-d\Th:i:s.u\Z').'</updated>
				<author></author>
				<id>'.$id.'</id>
				<summary type="text">Contact</summary>
				<content type="application/vnd.ctct+xml">
					<Contact xmlns="http://ws.constantcontact.com/ns/1.0/">
						<EmailAddress>'.$contact['EmailAddress'].'</EmailAddress>
						<OptOutSource>'.($actionBy == MaillistPlugin::ACTION_BY_USER ? 'ACTION_BY_CONTACT' : 'ACTION_BY_CUSTOMER').'</OptOutSource>
						<ContactLists></ContactLists>
					</Contact>
				</content>
			</entry>';

			// Send request
			$info = null;
    		$result = Utility::curl(
				$id,
				$contactEntry,
				'',
				120,
				$this->_curlHeader,
				$this->_authString,
				'PUT',
				$info
			);
			$success = ($info['http_code'] == 204);
    	} else {
    		$success = true;
    	}

    	return $success;
    }

    /**
     * Permanently opt-out a recipient
     *
     * @param string E-mail address
     * @return bool True on success, false otherwise
     */
	public function deleteRecipient($email)
    {
    	// Check whether contact already exists
    	if ($contact = $this->_findRecipient($email)) {
    		$id = $contact['@attributes']['id'];

    		// Delete contact entry
    		$info = null;
			$result = Utility::curl(
				$id,
				'',
				'',
				120,
				$this->_curlHeader,
				$this->_authString,
				'DELETE',
				$info
			);
			$success = ($info['http_code'] == 204);
    	} else {
    		$success = true;
    	}

    	// Bail if we couldn't delete from CC
    	if (!$success) {
    		return false;
    	}

		// Delete from our DB
    	return parent::deleteRecipient($email);
    }

    /**
     * Synchronise recipient lists
     *
     * @return True on success, false otherwise
     */
    public function synchronise()
    {
    	// Get all mailing lists, and index by provider reference
    	$mailLists = $this->getMailingLists();
    	$mailListsByRef = array();
    	if (!empty($mailLists)) {
    		foreach ($mailLists as $listId => &$list) {
    			$mailListsByRef[$list['providerRef']] =& $list;
    		}
    		unset($list);
    	}

    	// Keep list of all e-mail addresses for sync.
    	$emails = array();

    	// Get all recipients in our DB and index by e-mail
		$dbRecipients = $this->getAllRecipients();
    	$dbRecipientsByEmail = array();
    	if (!empty($dbRecipients)) {
			foreach ($dbRecipients as &$recipient) {
				$email = $recipient['email'];
				$dbRecipientsByEmail[$email] =& $recipient;
				$emails[$email] = $email;
			}
			unset($recipient);
    	}

		// Get list of all provider recipients and index by e-mail
		$providerRecipients = $this->_getAllRecipients();
		$providerRecipientsByEmail = array();
		if (!empty($providerRecipients)) {
			foreach ($providerRecipients as &$recipient) {
				$email = $recipient['content']['Contact']['EmailAddress'];
				$providerRecipientsByEmail[$email] =& $recipient;
				$emails[$email] = $email;
			}
			unset($recipient);
		}

		if (DEBUG) echo '<pre>';

		// Compare update timestamps for each e-mail recipient
		foreach ($emails as $email) {

			// Get DB recipient
			if (array_key_exists($email, $dbRecipientsByEmail)) {
				$dbRecipient = $dbRecipientsByEmail[$email];
			} else {
				$dbRecipient = false;
			}

			// Get provider recipient
			if (array_key_exists($email, $providerRecipientsByEmail)) {
				$providerRecipient = $providerRecipientsByEmail[$email];
			} else {
				$providerRecipient = false;
			}

			// Database recipient more recent than provider
			if ($dbRecipient && (($providerRecipient == false) || (strtotime($providerRecipient['updated']) < strtotime($dbRecipient['updateDate'])))) {

				if (DEBUG) echo '<br /><br />Updating CC:<br />';

				// Get data
				$status = $dbRecipient['status'];
				$email = $dbRecipient['email'];
				$name = $dbRecipient['name'];
				$nameDelimPos = strpos($name, ' ');
				$firstName = trim(substr($name, 0, $nameDelimPos));
				$lastName = trim(substr($name, $nameDelimPos));
				$userId = $dbRecipient['custId'];
				$custom = array(
					$dbRecipient['custom1'],
					$dbRecipient['custom2'],
					$dbRecipient['custom3'],
					$dbRecipient['custom4']
				);

				// Get mailing lists to subscribe to
				if (!empty($dbRecipient['lists']) && is_array($dbRecipient['lists'])) {
					$lists = array_keys($dbRecipient['lists']);
				} else {
					$lists = array();
				}

				// Update provider
				if ($status == 'subscribed') {
					if (DEBUG) var_dump('subscribe', $email, $firstName, $lastName, $lists, $userId, $custom);
					$this->_subscribeRecipient($email, $firstName, $lastName, $lists, $userId, $custom, null, MaillistPlugin::ACTION_BY_ADMIN);
				} else {
					if (DEBUG) var_dump('unsubscribe', $email);
					$this->_unsubscribeRecipient($email, MaillistPlugin::ACTION_BY_ADMIN);
				}

			// Provider more recent than database?
			} elseif ($providerRecipient && (($dbRecipient == false) || (strtotime($dbRecipient['updateDate']) < strtotime($providerRecipient['updated'])))) {

				if (DEBUG) echo '<br /><br />Updating DB:<br />';

				// Get full contact details
				$recipientDetails = $this->_getRecipient($providerRecipient['id']);

				// Get data
				$status = $recipientDetails['Status'];
				$email = $recipientDetails['EmailAddress'];
				$firstName = $recipientDetails['FirstName'];
				$lastName = $recipientDetails['LastName'];
				$custom = array(
					$recipientDetails['CustomField1'],
					$recipientDetails['CustomField2'],
					$recipientDetails['CustomField3'],
					$recipientDetails['CustomField4']
				);

				// Get mailing lists to subscribe to
				$lists = array();
				if (isset($recipientDetails['ContactLists']['ContactList'])) {
					$providerLists = $recipientDetails['ContactLists']['ContactList'];
					if (isset($providerLists['link'])) {
						$providerLists = array($providerLists);
					}

					if (!empty($providerLists)) {
						foreach ($providerLists as $providerList) {
							$listId = $providerList['@attributes']['id'];
							$listRef = substr($listId, strrpos($listId, '/') + 1);
							$lists[] = $mailListsByRef[$listRef]['id'];
						}
					}
				}

				// Update database
				if ($status == 'Active') {
					if (DEBUG) var_dump('subscribe', $email, $firstName, $lastName, $lists, null, $custom);
					parent::subscribeRecipient($email, $firstName, $lastName, $lists, null, $custom, null, MaillistPlugin::ACTION_BY_ADMIN);
				} else {
					if (DEBUG) var_dump('unsubscribe', $email);
					parent::unsubscribeRecipient($email, MaillistPlugin::ACTION_BY_ADMIN);
				}
			}
		}
    	if (DEBUG) echo '</pre>';

		return true;
    }

}
?>
