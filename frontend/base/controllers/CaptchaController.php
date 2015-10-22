<?php

/**
 * Captcha Controller
 *
 * @package BluApplication
 * @subpackage FrontendControllers
 */
class CaptchaController extends ClientFrontendController
{
	/**
	 * Display a captcha
	 */
	public function view()
	{
		$captcha = new Captcha();
		$captcha->generateCode();
		$im = $captcha->generateImage();
		header('Content-type: image/jpg');
		imagejpeg($im, NULL, 100);
	}

	/**
	 * Check a captcha
	 */
	public function check()
	{
		$code = Request::getString('captcha');
		echo json_encode(Captcha::checkCode($code));
	}
	
	/**
	 *	Captcha helptext
	 *
	 *	@access public
	 */
	public function help()
	{
		// Set title
		if (!$title = Text::get('captcha_help', array('captcha_help' => ''))) {
			$title = 'Why do I need this?';
		}
		$this->_doc->setTitle($title);
		
		// Load template
		include (BLUPATH_BASE_TEMPLATES.'/captcha/whatsthis.php');
	}
}
?>
