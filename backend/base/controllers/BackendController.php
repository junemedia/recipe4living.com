<?php

/**
 * Back end controller base class
 *
 * @package BluApplication
 * @subpackage BackendControllers
 */
abstract class BackendController
{
	/** 
	 *	Reference for current page
	 */
	protected $_uri;
	
	/**
	 * Reference to application document object
	 *
	 * @var Document
	 */
	protected $_doc;

	/**
	 * Requested arguments
	 *
	 * @var array
	 */
	protected $_args;

	/**
	 * Requested redirect location
	 *
	 * @var string
	 */
	private $_redirect = false;
	
	/**
	 *	Template data.
	 */
	protected $template_vars = array(
		'top' => array(),
		'sidebar' => array()
	);

	/**
	 * Back end controller constructor
	 */
	public function __construct($args)
	{
		// Store arguments
		$this->_args = $args;

		// Get reference to application document
		$this->_doc = BluApplication::getDocument();
	}
	
	/**
	 *	Backend controller destructor
	 *
	 *	@access public
	 */
	public function __destruct()
	{
		// Do nothing. Like Gordon.
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
	 * Set redirect location
	 *
	 * @param string Redirect URL
	 * @param string Optional message to show after redirection
	 * @param string Message type (info|warn|error)
	 * @param string Message location
	 * @param string Optional response code
	 */
	protected function _redirect($url, $msg = null, $msgType = 'info', $msgLocation = 'default', $responseCode = null)
	{
		if ($msg) {
			Messages::addMessage($msg, $msgType, $msgLocation);
		}
		if ($this->_doc->getFormat() == 'json') {
			echo json_encode(array('location' => SITEURL.$url));
		} else {
			$this->_redirect = array(
				'destination' => $url,
				'responseCode' => $responseCode);
		}
	}
	
	/**
	 * Redirect to error page
	 */
	final protected function _errorRedirect($task = 'fourOhFour', $controller = 'error')
	{
		$errorController = BluApplication::getController($controller);
		$errorController->$task();
	}

	/**
	 * Output form (or any other task) in the correct format for the current request
	 *
	 * @param string Raw/JSON form task name
	 * @param string Full site task name
	 */
	protected function _viewForm($formTask, $siteTask = 'view')
	{
		switch ($this->_doc->getFormat()) {
		case 'raw':
			$this->$formTask;
			break;

		case 'json':
			ob_start();
			$this->$formTask();
			$content = ob_get_clean();
			echo json_encode(array('form' => $content));
			break;

		default:
			$this->$siteTask();
			break;
		}
	}

	/**
	 * Output messages for the given location in the correct format for the current request
	 *
	 * @param string Raw task name
	 * @param string Full site task name
	 * @param string Messages location
	 * @param bool Whether this response completes the action
	 */
	protected function _showMessages($rawTask, $siteTask = 'view', $location = 'default', $complete = false)
	{
		switch ($this->_doc->getFormat()) {
		case 'raw':
			$this->$rawTask();
			break;

		case 'json':
			echo json_encode(array('messages' => Messages::getMessages($location),
				'complete' => $complete));
			break;

		default:
			$this->$siteTask();
			break;
		}
	}
}
?>
