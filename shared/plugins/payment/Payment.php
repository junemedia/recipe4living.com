<?php

/**
 * Payment provider base class
 *
 * @package BluApplication
 * @subpackage Payment
 */
abstract class Payment extends Plugin
{
	/**
	 * Requested redirect location and reason
	 *
	 * @var array
	 */
	private $_redirect = false;

	/**
	 * URL for callbacks
	 *
	 * @var string
	 */
	protected $_callbackUrl;

	/**
	 * Static payment provider settings
	 *
	 * @var string
	 */
	protected $_staticSettings;

	/**
	 * Front end payment provider constructor
	 */
	public function __construct($id, $callbackUrl = null)
	{
		parent::__construct($id);

		if (!$callbackUrl) {
			$callbackUrl = '/checkout/payment_callback';
		}

		// Set callback URL
		$this->_callbackUrl = ($_SERVER['SERVER_PORT'] == 80 ? 'http://' : 'https://');
		$this->_callbackUrl .= $_SERVER['SERVER_NAME'].SITEURL.$callbackUrl;
	}

	/**
	 * Process payment for order
	 *
	 * @return bool True on success, false otherwise
	 */
	abstract public function processPayment();

	/**
	 * Function called on a callback
	 */
	public function handleCallback()
	{
		return false;
	}

	/**
	 * Get extra details required type (e.g creditcard, transfer)
	 * This is used by the checkout controller to show the correct form
	 *
	 * @return string Details type
	 */
	public function getDetailsType()
	{
		$detailsType = $this->getSetting('payProvFormType');
		if ($detailsType == 'none') {
			$detailsType = false;
		}
		return $detailsType;
	}

	/**
	 * Store extra details securely in the session
	 *
	 * @param array Details
	 * @return bool True on success, false otherwise
	 */
	public function storeDetails($details)
	{
		Session::set('paymentDetails', $details);
		return true;
	}

	/**
	 * Check all the inputs are valid
	 *
	 * @return bool True if valid, false otherwise
	 */
	public function checkDetails()
	{
		return true;
	}

	/**
	 * Get extra details from session
	 *
	 * @return array Details
	 */
	public function getDetails()
	{
		return Session::get('paymentDetails');
	}

	/**
	 * Set redirect location
	 *
	 * @param string Redirect URL
	 * @param string Redirect reason
	 * @param string Redirect type ('redirect' or '3dsecure')
	 * @param array Extra results    (for 3D Secure)
	 */
	protected function setRedirect($url, $reason, $type = 'redirect', $res = null)
	{
		$this->_redirect = array('url' => $url, 'reason' => $reason, 'type' => $type, 'res' => $res);
	}

	/**
	 * Get redirect location
	 *
	 * @return string Redirect URL
	 */
	public function getRedirect()
	{
		return $this->_redirect;
	}

	/**
	 * Store transaction details
	 */
	protected function storeTransaction($localTxId, $providerTxId, $type, $success, $status, $authNum, $securityKey, $description, $avsAddress, $avsPostcode, $cvv, $threeDSecureStatus, $threeDSecureCAVV, $currency)
	{
		$query = 'INSERT INTO transactions SET
			localTxId = "'.Database::escape($localTxId).'",
			providerTxId = "'.Database::escape($providerTxId).'",
			type = "'.Database::escape($type).'",
			status = "'.($success ? 'success' : 'fail').'",
			statusDetail = "'.Database::escape($status).'",
			authNum = "'.Database::escape($authNum).'",
			securityKey = "'.Database::escape($securityKey).'",
			description = "'.Database::escape($description).'",
			providerId = "'.Database::escape($this->_id).'",
			AVSAddress = "'.Database::escape($avsAddress).'",
			AVSPostcode = "'.Database::escape($avsPostcode).'",
			AVS = "'.Database::escape('Address: '.($avsAddress ? $avsAddress : 'n/a').', Postcode: '.($avsPostcode ? $avsPostcode : 'n/a')).'",
			CVV = "'.Database::escape($cvv).'",
			3DSStatus = "'.Database::escape($threeDSecureStatus).'",
			3DSCAVV = "'.Database::escape($threeDSecureCAVV).'",
			currency = "'.Database::escape($currency).'",
			date = NOW()';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}

	/*
	 * Gets transaction details from transaction table
	 *
	 * @param int transaction id
	 */
	public function getTransactionDetails($TxKey)
	{
		$this->_db = BluApplication::getDatabase();
		$query = 'SELECT * FROM transactions WHERE TxKey="'.$TxKey.'"';
		$this->_db->setQuery($query);
		return $this->_db->loadAssoc();
	}
}
?>
