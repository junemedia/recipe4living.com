<?php

/**
 * Protx Payment Provider (VPS Direct)
 *
 * @package BluApplication
 * @subpackage Payment
 */
class PaymentProtx extends Payment
{
	/**
	 * Protocol version
	 *
	 * @var string
	 */
	private $_protocol = '2.22';

	/**
	 * Protx URL
	 *
	 * @var string
	 */
	private $_protxUrl;

	/**
	 * Protx 3D URL
	 *
	 * @var string
	 */
	private $_protx3dUrl;

	/**
	 * CVV result mapping
	 *
	 * @var array
	 */
	private $_CVVResultMap;

	/**
	 * CVV result mapping
	 *
	 * @var array
	 */
	private $_AVSResultMap;

	/**
	 * Constructor
	 */
	public function __construct($callbackUrl = null)
	{
		parent::__construct(__CLASS__, $callbackUrl);

		$this->_CVVResultMap = array (
			'MATCHED' => 'MATCHED',
			'NOTMATCHED' => 'NOTMATCHED',
			'NOTPROVIDED' => 'NOTPROVIDED',
			'NOTCHECKED' => 'NOTCHECKED');

		$this->_AVSResultMap = array (
			'MATCHED' => 'MATCHED',
			'NOTMATCHED' => 'NOTMATCHED',
			'NOTPROVIDED' => 'NOTPROVIDED',
			'NOTCHECKED' => 'NOTCHECKED');

		// Set up urls
		$mode = $this->getSetting('mode');
		if ($mode == 'live') {

			// Live servers
			$this->_protxUrl = 'https://ukvps.protx.com/vspgateway/service/vspdirect-register.vsp';
			$this->_protx3dUrl = 'https://ukvps.protx.com/vspgateway/service/direct3dcallback.vsp';
			$this->_protxAuth = 'https://ukvps.protx.com/vspgateway/service/authorise.vsp';
			$this->_protxRefund = 'https://ukvps.protx.com/vspgateway/service/refund.vsp';

		} elseif ($mode == 'test') {

			// Test servers
			$this->_protxUrl = 'https://ukvpstest.protx.com/vspgateway/service/vspdirect-register.vsp';
			$this->_protx3dUrl = 'https://ukvpstest.protx.com/vspgateway/service/direct3dcallback.vsp';
			$this->_protxAuth = 'https://ukvpstest.protx.com/vspgateway/service/authorise.vsp';
			$this->_protxRefund = 'https://ukvpstest.protx.com/vspgateway/service/refund.vsp';

		} else {

			// Simulation servers
			$this->_protxUrl = 'https://ukvpstest.protx.com/VSPSimulator/VSPDirectGateway.asp';
			$this->_protx3dUrl = 'https://ukvpstest.protx.com/VSPSimulator/VSPDirectCallback.asp';
			$this->_protxAuth = 'https://ukvpstest.protx.com/VSPSimulator/VSPServerGateway.asp?service=VendorAuthoriseTx';
			$this->_protxRefund = 'https://ukvpstest.protx.com/VSPSimulator/VSPServerGateway.asp?service=VendorRefundTx';
		}
	}

	/**
	 * Process payment for order
	 *
	 * @param bool Apply AVS and CV2 validation?
	 * @return bool True on success, false otherwise
	 */
	public function processPayment($skipCV2Check = false)
	{
		// Get payment details
		$details = $this->getDetails();

		// Generate vendor Tx code
		$vendorTxCode = isset($details['txCodePrefix']) ? $details['txCodePrefix'].'_' : '';
		$vendorTxCode .= uniqid();

		// Build arguments
		$args = array();
		$args['VPSProtocol'] = $this->_protocol;
		$args['TxType'] = $this->getSetting('transactionType');
		$args['Vendor'] = $this->getSetting('vendor');
		$args['VendorTxCode'] = $vendorTxCode;
		$args['Amount'] = number_format($details['amount'], 2, '.', '');
		$args['Currency'] = $details['currency'];
		$args['Description'] = $details['description'];
		$args['BillingAddress'] = $details['billingAddress'];
		$args['BillingPostCode'] = $details['billingPostcode'];
		if (isset($details['deliveryAddress'])) {
			$args['DeliveryAddress'] = $details['deliveryAddress'];
		}
		if (isset($details['deliveryPostcode'])) {
			$args['DeliveryPostCode'] = $details['deliveryPostcode'];
		}
		$args['CustomerName'] = isset($details['customerName']) ? $details['customerName'] : $details['cardName'];
		if (isset($details['phone'])) {
			$args['ContactNumber'] = $details['customerPhone'];
		}
		if (isset($details['email'])) {
			$args['CustomerEMail'] = $details['customerEmail'];
		}
		$args['CardHolder'] = $details['cardName'];
		$args['CardNumber'] = $details['cardNumber'];
		$args['CardType'] = $details['cardType'];
		$args['CV2'] = $details['cardCVV'];
		if ($skipCV2Check) {
			$args['ApplyAVSCV2'] = 2;  // Force NO AVS/CV2 checks even if enabled on account.
		}
		$args['ExpiryDate'] = date('my', mktime(0, 0, 0, $details['expiryMonth'], 1, $details['expiryYear']));
		if (isset($details['startMonth']) && isset($details['startYear'])) {
			$args['StartDate'] = date('my', mktime(0, 0, 0, $details['startMonth'], 1, $details['startYear']));
		}
		if (isset($details['issueNum']) && $details['issueNum']>0) {
			$args['IssueNumber'] = $details['issueNum'];
		}
		$args['ClientIPAddress'] = Request::getVisitorIPAddress();
		$args['Basket'] = $this->_getBasket($details['items']);

		$postFields = $this->buildPostString($args);

		// Store vendor TX code in session
		Session::set('protxVendorTxCode', $vendorTxCode);

		// Submit to protx
		if (!$res = Utility::curl($this->_protxUrl, $postFields)) {
			Messages::addMessage(Text::get('payment_msg_error_connecting'), 'error');
			return false;
		}

		// Handle result
		return $this->_handleResult($res);
	}

	/**
	 * Handle callback from 3D secure
	 *
	 * @return bool True on success, false otherwise
	 */
	public function handleCallback()
	{
		// Build arguments
		$args = array();
		$args['MD'] = $_POST['MD'];
		$args['PARes'] = $_POST['PaRes'];
		if (DEBUG) {
			Messages::addMessage("Sending arguments to protx:\n\n".print_r($args, true), 'debug');
		}
		$postFields = $this->buildPostString($args);

		// Submit to protx
		if (!$res = Utility::curl($this->_protx3dUrl, $postFields)) {
			Messages::addMessage(Text::get('payment_msg_error_connecting'), 'error');
			return false;
		}

		// Handle result
		return $this->_handleResult($res);
	}

	/**
	 * Parses protx result and performs required actions
	 *
	 * @param Result string
	 * @return True if result status is good, false otherwise
	 */
	private function _handleResult($res, $type = 'payment')
	{
		// Parse result
		$res = $this->parseResultString($res, "\n");
		if (DEBUG) {
			Messages::addMessage("Got response from protx:\n\n".print_r($res, true), 'debug');
		}

		// Handle result based on type
		switch ($type) {
			case 'payment': return $this->_handlePayment($res);
			case 'refund': return $this->_handleRefund($res);
		}
	}

	/**
	 * Parses protx payment result and performs required actions
	 *
	 * @param Result string
	 * @return True if result status is good, false otherwise
	 */
	private function _handlePayment($res)
	{
		// Get details
		$details = $this->getDetails();

		// Get vendor tx code
		$vendorTxCode = Session::get('protxVendorTxCode');

		// Redirect the user to 3D Secure if required
		if ($res['Status'] == '3DAUTH') {
			$this->setRedirect($this->_callbackUrl, Text::get('payment_3dsecure_title'), '3dsecure', $res);
			return true;

		} else {

			// Remove session vars as we're done
			Session::delete('protxVendorTxCode');

			// Check for success is done
			$success = (($res['Status'] == 'OK') || ($res['Status'] == 'REGISTERED') || ($res['Status'] == 'AUTHENTICATED'));

			// Store transaction details
			$this->storeTransaction(
				$vendorTxCode,
				isset($res['VPSTxId']) ? $res['VPSTxId'] : null,
				$this->getSetting('transactionType'),
				$success,
				isset($res['StatusDetail']) ? $res['StatusDetail'] : null,
				isset($res['TxAuthNo']) ? $res['TxAuthNo'] : 'Auth. only',
				isset($res['SecurityKey']) ? $res['SecurityKey'] : null,
				isset($details['description']) ? $details['description'] : null,
				isset($res['AddressResult']) ? $this->_AVSResultMap[$res['AddressResult']] : null,
				isset($res['PostCodeResult']) ? $this->_AVSResultMap[$res['PostCodeResult']] : null,
				isset($res['CV2Result']) ? $this->_CVVResultMap[$res['CV2Result']] : null,
				isset($res['3DSecureStatus']) ? $res['3DSecureStatus'] : null,
				isset($res['CAVV']) ? $res['CAVV'] : null,
				$details['currency']
			);

			// Oops
			if (!$success) {
				return false;
			}

			// Return transaction ID (or generic success if we don't got)
			return isset($res['VPSTxId']) ? $res['VPSTxId'] : false;
		}
	}

	/**
	 * Store extra details securely in the session
	 *
	 * @param array Raw un-cleaned details
	 * @return bool True on success, false otherwise
	 */
	public function storeDetails($vars)
	{
		// Get existing details
		$currentDetails = $this->getDetails();

		// Get card number
		$cardNumber = Request::getVar('cardNumber', null, $vars);

		// Re-submit with matching masked card?
		if (isset($currentDetails['cardMasked']) && ($cardNumber == $currentDetails['cardMasked'])) {
			$details['cardNumber'] = $currentDetails['cardNumber'];
			$details['cardMasked'] = $currentDetails['cardMasked'];

		} else {
			// Store cleaned credit card number
			$details['cardNumber'] = preg_replace('/[^0-9]/', '', $cardNumber);

			// Store masked credit card number
			$len = strlen($cardNumber);
			if ($len > 4) {
				$details['cardMasked'] = str_repeat('*', $len-4);
			}
			$details['cardMasked'] .= substr($cardNumber, -4);
		}

		// Store credit card type
		$details['cardType'] = CreditCard::getType($details['cardNumber']);

		// Get remaining data from details
		$details['cardName'] = Request::getString('cardName', null, $vars);
		$details['cardCVV'] = Request::getString('cardCVV', null, $vars);

		$details['expiryMonth'] = Request::getInt('expiryMonth', null, $vars);
		$details['expiryYear'] = Request::getInt('expiryYear', null, $vars);

		$details['startMonth'] = Request::getInt('startMonth', null, $vars);
		$details['startYear'] = Request::getInt('startYear', null, $vars);

		$details['issueNum'] = Request::getInt('issueNum', null, $vars);

		$details['billingAddress'] = Request::getString('billingAddress', null, $vars);
		$details['billingPostcode'] = Request::getString('billingPostcode', null, $vars);

		$details['deliveryAddress'] = Request::getString('deliveryAddress', null, $vars);
		$details['deliveryPostcode'] = Request::getString('deliveryPostcode', null, $vars);

		$details['customerName'] = Request::getString('customerName', null, $vars);
		$details['customerPhone'] = Request::getString('customerPhone', null, $vars);
		$details['customerEmail'] = Request::getString('customerEmail', null, $vars);

		$details['description'] = Request::getString('description', null, $vars);
		$details['amount'] = Request::getFloat('amount', 0, $vars);
		$details['items'] = Request::getVar('items', array(), $vars);
		$details['currency'] = Request::getString('currency', 'GBP', $vars);

		return parent::storeDetails($details);
	}

	/**
	 * Check all the inputs are valid
	 *
	 * @return bool True if valid, false otherwise
	 */
	public function checkDetails()
	{
		// Get payment details
		$details = $this->getDetails();

		// Optimistic
		$valid = true;

		// Check required details have been supplied
		if (!$details['cardName'] || !$details['cardNumber'] || !is_numeric($details['cardCVV']) || !$details['expiryMonth'] || !$details['expiryYear'] ||
			!$details['billingAddress'] || !$details['billingPostcode'] || !$details['description']) {
			Messages::addMessage(Text::get('global_msg_complete_required_fields'), 'error');
			return false;
		}

		$validTypes = array('VISA', 'MC', 'DELTA', 'SOLO', 'MAESTRO', 'UKE', 'AMEX', 'DC', 'JCB');

		// Check for card type
		if (!$details['cardType']) {
			Messages::addMessage(Text::get('payment_msg_cannot_detect_card_type'), 'error');
			$valid = false;

		// Check for accepted card type
		} elseif (!in_array($details['cardType'], $validTypes)) {
			Messages::addMessage(Text::get('payment_msg_cannot_process_card_type', array('cardType' => $details['cardType'])), 'error');
			$valid = false;
		}

		// Check for start date and issue number if switch
		if (($details['cardType'] == 'MAESTRO') && !(($details['startMonth'] && $details['startYear']) || is_numeric($details['issueNum']))) {
			Messages::addMessage(Text::get('payment_msg_enter_switch_details'), 'error');
			$valid = false;
		}

		return $valid;
	}

	/**
	 * Get basket parameter for submission to protx
	 *
	 * @param array Array of basket items
	 * @return string Basket parameter
	 */
	private function _getBasket($basketItems)
	{
		$basketString = count($basketItems);
		foreach ($basketItems as $name => $details) {
			$netAmount = $details['netAmount'];
			$taxAmount = $details['taxAmount'];
			$grossAmount = $details['netAmount'] + $details['taxAmount'];
			$quantity = $details['quantity'];
			$basketString .= ':'.$name.':'.$quantity.':'.number_format($netAmount, 2).':'.number_format($taxAmount, 2).':'.number_format($grossAmount, 2).':'.number_format($grossAmount * $quantity, 2);
		}
		return $basketString;
	}
}

?>
