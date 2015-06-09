<?php

/**
 *	Arcamax mailing list provider base class
 *
 *	@package BluApplication
 *	@subpackage Maillist
 */
class MaillistArcamax extends MaillistPlugin
{
	/**
	 *	Arcamax URL
	 *
	 *	@var string
	 */
	const URL = 'https://www.arcamax.com/esp/bin/espsub';
	
	/**
	 *	User name
	 *
	 *	@access private
	 *	@var string
	 */
	private $_userName;

	/**
	 *	User password
	 *
	 *	@access private
	 *	@var string
	 */
	private $_password;

	/**
	 *	Subcampids
	 *
	 *	@access private
	 *	@var array
	 */
	private $_subcampids;

    /**
     *	Arcamax mailing list provider constructor
	 *
	 *	@access public
     */
    public function __construct()
	{
		parent::__construct('arcamax');
		
		// Get settings
		$this->_userName = $this->getSetting('userName');
		$this->_password = $this->getSetting('password');
		$this->_subcampids = array(
			'subscribe' => $this->getSetting('subscribe_subcampid'),
			'unsubscribe' => $this->getSetting('unsubscribe_subcampid')
		);
	}

	/**
	 *	Add a recipient to a mailing list
	 *
	 *	@access public
	 *	@param string E-mail address
	 *	@param string First name
	 *	@param string Last name
	 *	@param mixed Optional list id/array of lists to subscribe to
	 *	@param int User Id
	 *	@param array Array of custom fields (max. 15)
	 *	@param string Optional old e-mail address of subscriber (to allow updates as appropriate)
	 *	@param int Subscribe action source
	 *	@return bool True on success, false otherwise
	 */
	public function subscribeRecipient($email, $firstName = null, $lastName = null, $lists = null, $userId = null, $custom = null, $oldEmail = null, $actionBy = MaillistPlugin::ACTION_BY_USER)
	{
    	// Add to Arcamax
		$this->_subscribeRecipient($email, $lists);

		// Add to our DB if all went OK
		return parent::subscribeRecipient($email, $firstName, $lastName, $lists, $userId, $custom);
	}

	/**
	 *	Add a recipient to an Arcamax mailing list only
	 *
	 *	@access protected
	 *	@param string E-mail address
	 *	@param mixed Optional list id/array of lists to subscribe to
	 *	@return bool True on success, false otherwise
	 */
	protected function _subscribeRecipient($email, $lists = null)
	{
		// Get external mailing list IDs
		if (!is_array($lists)) {
			$lists = (array) $lists;
		}
		$listMapping = $this->_getListMapping();
		$lists = array_intersect_key($listMapping, array_flip($lists));
		
		// Prepare curl
		$postFields = array(
			'email' => $email,
			'sublists' => implode(',', $lists),
			'subcampid' => $this->_subcampids['subscribe'],
			'ipaddr' => Request::getVisitorIPAddress()
		);

		// Arcamax are less than helpful and don't support multipart/form-data, so need to send as application/x-www-form-urlencoded
		foreach ($postFields as $key => &$field) {
			$field = urlencode($key).'='.urlencode($field);
		}
		unset($field);
		$postFields = implode('&', $postFields);
		
		// Fetch response.
		$response = Utility::curl(self::URL, $postFields, null, null, null, array(
			'method' => 'basic',
			'username' => $this->_userName,
			'password' => $this->_password
		));
		
		// Test for the word "error" in the response string
		return !strstr($response, 'error');
	}

	/**
	 *	Remove a recipient from all mailing lists
	 *
	 *	@param string E-mail address
	 *	@param int Subscribe action source
	 *	@return bool True on success, false otherwise
	 */
	public function unsubscribeRecipient($email, $actionBy = MaillistPlugin::ACTION_BY_USER)
	{
		// Remove from Arcamax
		$this->_unsubscribeRecipient($email, $actionBy);

		// Remove from our DB
		return parent::unsubscribeRecipient($email, $actionBy);
	}
	
	/**
	 *	Remove a recipient from Arcamax mailing list
	 *
	 *	@access protected
	 *	@param string Email address
	 *	@param int Unsubscribe action source
	 *	@return bool Success
	 */
	protected function _unsubscribeRecipient($email, $actionBy = MaillistPlugin::ACTION_BY_USER)
	{
		// Get external mailing list IDs
		$recipient = $this->getRecipient($email);
		if (empty($recipient['lists'])) {
			return true;
		}
		$listMapping = $this->_getListMapping();
		$lists = array_intersect_key($listMapping, $recipient['lists']);
		
		// Prepare curl
		$postFields = array(
			'email' => $email,
			'unsublists' => implode(',', $lists),
			'subcampid' => $this->_subcampids['unsubscribe'],
			'ipaddr' => Request::getVisitorIPAddress()
		);

		// Arcamax are less than helpful and don't support multipart/form-data, so need to send as application/x-www-form-urlencoded
		foreach ($postFields as $key => &$field) {
			$field = urlencode($key).'='.urlencode($field);
		}
		unset($field);
		$postFields = implode('&', $postFields);

		// Fetch response.
		$response = Utility::curl(self::URL, $postFields, null, null, null, array(
			'method' => 'basic',
			'username' => $this->_userName,
			'password' => $this->_password
		));
		
		// Test for the word "error" in the response string
		return !strstr($response, 'error');
	}
}

?>
