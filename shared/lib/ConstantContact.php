<?php

/**
 * Constant Contact
 *
 * NOTE:
 * Please visit http://www.constantcontact.com to register for a constant contact account.
 * Developers are required to go through Constant Contact (support@constantContact.com) to get access to the Constant Contact APIs (Specifically, developers are required to accept the API usage terms and conditions).
 */

/**
 * Class to interact with the constant contact API
 * This class enables you to easily subscribe and unsubscribe members
 *
 * @package ConstantContact
 * @license http://www.php.net/license/3_01.txt PHP License 3.01
 * @author James Benson
 * @link http://www.jamesbenson.co.uk
 * @see http://www.constantcontact.com
 */
class ConstantContact
{
	/**
	 * API URL used to subscribe
	 *
	 * @var string
	 */
	var $add_subscriber_url = "http://ui.constantcontact.com/roving/wdk/API_AddSiteVisitor.jsp";

	/**
	 * API URL used to unsubscribe
	 *
	 * @var string
	 */
	var $remove_subscriber_url = 'http://ui.constantcontact.com/roving/wdk/API_UnsubscribeSiteVisitor.jsp';

	/**
	 * Your CC account username
	 *
	 * @var string
	 */
	private $_username = '';

	/**
	 * Your CC account password
	 *
	 * @var string
	 */
	private $_password = '';

	/**
	 * The category to subscribe a member to
	 *
	 * @var string
	 */
	private $_category = '';

	/**
	 * Sets the username used to access the CC API
	 *
	 * @param string The username for your CC account
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * Sets the password used to access the CC API
	 *
	 * @param string The password for your CC account
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}

	/**
	 * Sets the category used to add subscribers into
	 *
	 * @param string The interest category subscribers are added to
	 */
	public function setCategory($category)
	{
		$this->category = $category;
	}

	/**
	 * Get the username used to access the CC API
	 */
	public function getUsername()
	{
		return urlencode($this->username);
	}

	/**
	 * Get the password used to access the CC API
	 */
	public function getPassword()
	{
		return urlencode($this->password);
	}

	/**
	 * Get the interest category
	 */
	public function getCategory()
	{
		return urlencode($this->category);
	}

	/**
	 * Add a subscriber to your constant contact account, can be used to update their details too
	 *
	 * @param string The email to subscribe
	 * @param array An array of extra fields to pass to CC, see docs for possible values
	 * @return bool True on success false on failure
	 */
	public function add($email, $extra_fields = array())
	{
		$email = urlencode(strip_tags($email));
		$data = 'loginName='.$this->getUsername();
		$data.= '&loginPassword='.$this->getPassword();
		$data.= '&ea='.$email;
		$data.= '&ic='.$this->getCategory();

		if (is_array($extra_fields)) {
			foreach($extra_fields as $k => $v) {
				$data.= "&".urlencode(strip_tags($k))."=".urlencode(strip_tags($v));
			}
		}
		
		return $this->_send($data, $this->add_subscriber_url);
	}

	/**
	 * Remove a subscriber from your constant contact account
	 *
	 * @param	 string	The email to unsubscribe
	 * @return	bool	  true on success false on failure
	 */
	public function remove($email)
	{
		$email = urlencode(strip_tags($email));
		$data = 'loginName='.$this->getUsername();
		$data.= '&loginPassword='.$this->getPassword();
		$data.= '&ea='.$email;
		
		return $this->_send($data, $this->remove_subscriber_url);
	}

	/**
	 * Method used to send the data to the CC server
	 *
	 * @access private
	 */
	private function _send($data, $url)
	{
		$Furl = $url."?".$data;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $Furl);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		$contents = curl_exec($ch);

		return (trim($contents) == 0);
	}
}
?>
