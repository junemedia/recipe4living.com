<?php

/**
 * Paypal Express Payment Provider
 *
 * @package BluApplication
 * @subpackage Payment
 */
class PaymentPaypal extends Payment
{
	/**
	 * Base submission arguments
	 *
	 * @var array
	 */
	private $_baseArgs;

	/**
	 * Submission URL
	 *
	 * @var string
	 */
	private $_submitUrl;

	/**
	 * Redirection URL
	 *
	 * @var string
	 */
	private $_redirectUrl;

	/**
	 * Constructor
	 */
	public function __construct($callbackUrl = null)
	{
		parent::__construct(__CLASS__, $callbackUrl);

		// Build basic submission arguments
		$this->_baseArgs = array(
			'USER' => $this->getSetting('USER'),
			'PWD' => $this->getSetting('PWD'),
			'SIGNATURE' => $this->getSetting('SIGNATURE'),
			'VERSION' => $this->getSetting('VERSION')
		);
		$this->_submitUrl = 'https://api-3t'.($this->getSetting('TEST') ? '.sandbox' : '').'.paypal.com/nvp';
		$this->_redirectUrl = 'https://www'.($this->getSetting('TEST') ? '.sandbox' : '').'.paypal.com/cgi-bin';
	}

	/**
	 * Process payment for order
	 *
	 * @return bool True on success, false otherwise
	 */
	public function processPayment()
	{
		// Get payment details
		$details = $this->getDetails();

		// Process payment
		$args = array(
			'METHOD' => 'SetExpressCheckout',
			'AMT' => $details['amount'],
			'CURRENCYCODE' => $details['currency'],
			'RETURNURL' => $this->_callbackUrl,
			'CANCELURL' => $this->_callbackUrl,
			'HDRIMG' => '',
			'SOLUTIONTYPE' => 'Sole',
			'HDRBORDERCOLOR' => 'FFFFFF',
			'DESC' => $details['description']
		);
		$args = array_merge($this->_baseArgs, $args);
		if (DEBUG) {
			Messages::addMessage("Sending arguments to PayPal:\n\n".print_r($args, true), 'debug');
		}
		$postFields = $this->buildPostString($args);

		// Submit to paypal
		if (!$res = Utility::curl($this->_submitUrl, $postFields)) {
			Messages::addMessage('Sorry, there was an error connecting to the payment provider. Please try again.', 'error');
			return false;
		}

		// Parse result
		$res = $this->parseResultString($res);
		if (DEBUG) {
			Messages::addMessage("Got response from PayPal:\n\n".print_r($res, true), 'debug');
		}

		// Check result
		if ($res['ACK'] != 'Success') {
			if (DEBUG) {
				Messages::addMessage('Error: '.$res['L_LONGMESSAGE0'], 'error');
			}
			return false;
		}

		// Redirect to paypal to complete order
		$url = $this->_redirectUrl.'/webscr?cmd=_express-checkout&token='.$res['TOKEN'].'&useraction=commit';
		$this->setRedirect($url, 'Please wait while we redirect you to PayPal to complete your payment.');
		return true;
	}

	/**
	 * Handle callback from paypal checkout
	 *
	 * @return bool True on success, false otherwise
	 */
	public function handleCallback()
	{
		// Get data from request
		$token = Request::getString('token');
		$payerID = Request::getString('PayerID');

		// Get payment details
		$details = $this->getDetails();

		// Get checkout details
		$args = array(
			'METHOD' => 'GetExpressCheckoutDetails',
			'TOKEN' => $token
		);
		$args = array_merge($this->_baseArgs, $args);
		if (DEBUG) {
			Messages::addMessage("Sending arguments to PayPal:\n\n".print_r($args, true), 'debug');
		}
		$postFields = $this->buildPostString($args);

		// Submit to paypal
		if (!$res = Utility::curl($this->_submitUrl, $postFields)) {
			Messages::addMessage('Sorry, there was an error connecting to the payment provider. Please try again.', 'error');
			return false;
		}

		// Parse result
		$res = $this->parseResultString($res);
		if (DEBUG) {
			Messages::addMessage("Got response from PayPal:\n\n".print_r($res, true), 'debug');
		}

		// Check result
		if ($res['ACK'] != 'Success') {
			if (DEBUG) {
				Messages::addMessage('Error: '.$res['L_LONGMESSAGE0'], 'error');
			}
			return false;
		}

		// Push the payment through
		$args = array(
			'METHOD' => 'DoExpressCheckoutPayment',
			'AMT' => $details['amount'],
			'CURRENCYCODE' => $details['currency'],
			'PAYMENTACTION' => 'Sale',
			'TOKEN' => $token,
			'PAYERID' => $payerID
		);
		$args = array_merge($this->_baseArgs, $args);
		if (DEBUG) {
			Messages::addMessage("Sending arguments to PayPal:\n\n".print_r($args, true), 'debug');
		}
		$postFields = $this->buildPostString($args);

		// Submit to paypal
		if (!$res = Utility::curl($this->_submitUrl, $postFields)) {
			Messages::addMessage('Sorry, there was an error connecting to the payment provider. Please try again.', 'error');
			return false;
		}

		// Parse result
		$res = $this->parseResultString($res);
		if (DEBUG) {
			Messages::addMessage("Got response from PayPal:\n\n".print_r($res, true), 'debug');
		}

		// Check result
		$success = ($res['ACK'] == 'Success');
		if ($success) {
			if ($res['PAYMENTSTATUS'] == 'Completed') {
				$authNum = $res['TOKEN'];
				$statusInfo = null;
			} else {
				$authNum = null;
				$statusInfo = 'Pending Reason: '.$res['PENDINGREASON'];
			}
		} else {
			$authNum = null;
			$statusInfo = 'Error: '.$res['L_LONGMESSAGE0'];
		}

		// Store transaction details
		$this->storeTransaction(
			null,
			isset($res['TRANSACTIONID']) ? $res['TRANSACTIONID'] : null,
			'Sale',
			$success,
			$statusInfo,
			$authNum,
			null,
			isset($details['description']) ? $details['description'] : null,
			null,
			null,
			null,
			null,
			null,
			$details['currency']
		);

		// Oops
		if (!$success) {
			// Error output if debug
			if (DEBUG) {
				Messages::addMessage($statusInfo, 'error');
			}
			return false;
		}

		// Return token (or generic success if we don't got)
		return isset($res['TRANSACTIONID']) ? $res['TRANSACTIONID'] : true;
	}

	/**
	 * Store extra details securely in the session
	 *
	 * @param array Raw un-cleaned details
	 * @return bool True on success, false otherwise
	 */
	public function storeDetails($vars)
	{
		$details['description'] = Request::getString('description', null, $vars);
		$details['amount'] = Request::getFloat('amount', 0, $vars);
		$details['currency'] = Request::getString('currency', 'GBP', $vars);

		return parent::storeDetails($details);
	}
}

?>
