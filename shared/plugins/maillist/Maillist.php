<?php

/**
 * Mailing list provider base class
 *
 * @package BluApplication
 * @subpackage Maillist
 */
class MaillistPlugin extends Plugin
{
	const ACTION_BY_USER = 1;
	const ACTION_BY_ADMIN = 2;

	/**
	 * Base mailing list provider constructor
	 */
	public function __construct($id)
	{
		parent::__construct($id, 'maillist');
	}

	/**
	 * Add a recipient to a mailing list
	 *
	 * @param string E-mail address
	 * @param string First name
	 * @param string Last name
	 * @param mixed Optional list id/array of lists to subscribe to
	 * @param int User Id
	 * @param array Array of custom fields (max. 4)
	 * @param string Optional old e-mail address of subscriber (to allow updates as appropriate)
	 * @return bool True on success, false otherwise
	 */
	public function subscribeRecipient($email, $firstName = null, $lastName = null, $lists = null, $userId = null, $custom = null, $oldEmail = null)
	{
		// Get name
		$name = trim($firstName.' '.$lastName);

		// Get existing details
		$query = 'SELECT id
		          FROM mailSubscribers
		          WHERE email = "'.Database::escape($oldEmail ? $oldEmail : $email).'"';
		$this->_db->setQuery($query);
		$subscriberId = $this->_db->loadResult();

		// Build custom fields SQL fragment
		$customFieldsQuery = '';
		for ($i = 1; $i <= 4; $i++) {
			$field = ((isset($custom[$i]) && ($custom[$i] != 'none')) ? $custom[$i] : '');
			$customFieldsQuery .= ', custom'.$i.' = "'.$field.'"';
		}

		// Update existing subscriber
		if ($subscriberId) {
			$query = 'UPDATE mailSubscribers
			          SET name = "'.$name.'",
			              email = "'.$email.'",
			              status = "subscribed"
			          WHERE id = '.(int)$subscriberId;
			$this->_db->setQuery($query);
			$this->_db->query();

		// Add subscriber to database
		}
	 	else {
			$query = 'REPLACE INTO mailSubscribers
			          SET name = "'.$name.'",
			              email = "'.$email.'",
			              joinDate = "'.date('Y-m-d').'",
			              validated = 1'.$customFieldsQuery.',
			              status = "subscribed"';
			$this->_db->setQuery($query);
			$this->_db->query();
			$subscriberId = $this->_db->getInsertID();
		}

		// Remove existing list subscribtions
		$query = 'UPDATE mailListSubscriptions
		          SET status = "unsubscribed"
		          WHERE subscriberId = "'.$subscriberId.'"';
		$this->_db->setQuery($query);
		$this->_db->query();

		// Add list subscribtions
		if (!is_array($lists)) {
			$lists = array($lists);
		}
		foreach($lists as $listId) {
			$query = 'REPLACE INTO mailListSubscriptions
			          SET subscriberId = "'.$subscriberId.'",
			              listId = "'.$listId.'",
			              status = "subscribed"';
			$this->_db->setQuery($query);
			$this->_db->query();
		}

		return true;
	}

	/**
	 * Get a recipients details
	 *
	 * @param string E-mail address
	 * @return array Recipients details on success, false otherwise
	 */
	public function getRecipient($email)
	{
		// Get details from database
		$query = 'SELECT ms.*
		          FROM mailSubscribers AS ms
		          WHERE ms.email = "'.Database::escape($email).'"';
		$this->_db->setQuery($query);
		$details = $this->_db->loadAssoc();

		// Get mailing list subscription statuses
		if ($details) {
			$query = 'SELECT *
			          FROM mailListSubscriptions
			          WHERE subscriberId = '.(int)$details['id'];
			$this->_db->setQuery($query);
			$details['lists'] = $this->_db->loadAssocList('listId');
		}

		return $details;
	}

 	/**
	 * Get a list of all recipients details
	 *
	 * @todo
	 * @return array Array of recipients details (including list subscriptions)
	 */
	public function getAllRecipients()
	{
		return;

		// Get subscriber details from database
		$query = 'SELECT ms.*, c.custID AS custId
		          FROM mailSubscribers AS ms
		          LEFT JOIN customers AS c ON c.Email = ms.email';
		$this->_db->setQuery($query);
		$recipients = $this->_db->loadAssocList('id');
		if (!$recipients) {
			return false;
		}

		// Get mailing list subscription statuses
		$query = 'SELECT * FROM mailListSubscriptions';
		$this->_db->setQuery($query);
		$listSubs = $this->_db->loadAssocList();
		if (!empty($listSubs)) {
			foreach ($listSubs as $listSub) {
				$subscriberId = $listSub['subscriberId'];
				$listId = $listSub['listId'];

				// Add list subscription to recipient
				if (array_key_exists($subscriberId, $recipients)) {
					if (!isset($recipients[$subscriberId]['lists'])) {
						$recipients[$subscriberId]['lists'] = array();
					}
					$recipients[$subscriberId]['lists'][$listId] = $listSub;
				}
			}
		}

		return $recipients;
	}

	/**
	 * Remove a recipient from all mailing lists
	 *
	 * @param string E-mail address
	 * @return bool True on success, false otherwise
	 */
	public function unsubscribeRecipient($email)
	{
		// Need to be able to unsubscribe people who aren't subscribed.
		$recipient = $this->getRecipient($email);

		if ($recipient == false) {
			$this->subscribeRecipient($email, null, null, array_keys($this->getMailingLists()));
		}

		$query = 'UPDATE mailSubscribers AS ms
		          LEFT JOIN mailListSubscriptions AS mls ON mls.subscriberId = ms.id
		          SET ms.status = "unsubscribed",
		              ms.unsubscribeDate = NOW(),
		              mls.status = "unsubscribed"
		          WHERE ms.email = "'.Database::escape($email).'"';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}

	/**
	 * Permanently delete a recipient (you probably don't want to do this)
	 *
	 * @param string E-mail address
	 * @return bool True on success, false otherwise
	 */
	public function deleteRecipient($email)
	{
		// Delete from database
		$query = 'DELETE FROM ms, mls
		          USING mailSubscribers AS ms
		          LEFT JOIN mailListSubscriptions AS mls ON mls.subscriberId = ms.id
		          WHERE m.email = "'.Database::escape($email).'"';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}

	/**
	 * Get a list of lists which can be subscribed to
	 *
	 * @param array Optional subscription details to use for mailing list status
	 * @return array Array of available mailing lists
	 */
	public function getMailingLists($subscription = null)
	{
		// Get mailing lists
		$query = 'SELECT *
		          FROM mailLists AS ml
		          WHERE ml.enabled = 1';
		$this->_db->setQuery($query);
		$lists = $this->_db->loadAssocList('id');
		if (!$lists) {
			return false;
		}

		// Set existing subscription status
		foreach ($lists as $listId => &$list) {
			if (isset($subscription['lists'][$listId])) {
				$list['status'] = $subscription['lists'][$listId]['status'];
			}
		 	else {
				$list['status'] = false;
			}
		}

		return $lists;
	}

 	/**
	 * Synchronise recipient lists
	 *
	 * @return bool True on success, false otherwise
	 */
	public function synchronise()
	{
		// Nothing to do - we are always in sync. with ourselves ;)
	}

	/**
	 *	Get external provider's list ID mapping
	 *
	 *	@access protected
	 *	@return array Internal list ID to External
	 */
	protected function _getListMapping()
	{
		$query = 'SELECT ml.id, ml.providerRef
		          FROM `mailLists` AS `ml`
		          WHERE ml.enabled = 1';
		$this->_db->setQuery($query);
		return $this->_db->loadResultAssocArray('id', 'providerRef');
	}
}

?>
