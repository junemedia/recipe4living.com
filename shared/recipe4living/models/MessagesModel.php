<?php

/**
 * For dealing with user messages (under user accounts).
 *
 *	@package BluApplication
 *	@subpackage SharedModels
 */
class ClientMessagesModel extends BluModel
{
	/**
	 * Get single message
	 *
	 * @param int Message ID
	 * @param int User ID to check for permissions
	 * @return array Message details
	 */
	public function getMessage($messageId, $userId)
	{
		// Get message details
		$query = 'SELECT *
			FROM messages AS m
			WHERE m.messageID = '.(int)$messageId;
		$this->_db->setQuery($query);
		$message = $this->_db->loadAssoc();
		if (!$message) {
			return false;
		}

		// Cast date to timestamp
		$message['sent'] = strtotime($message['sent']);

		// Set read status/check permissions
		if ($message['fromID'] == $userId) {
			$message['type'] = 'sent';
			$message['read'] = $message['fromRead'];
			$message['deleted'] = $message['sdel'];
		} elseif ($message['toID'] == $userId) {
			$message['type'] = 'received';
			$message['read'] = $message['toRead'];
			$message['deleted'] = $message['rdel'];
		} else {
			// Who the hell are you?
			return false;
		}

		$userModel = BluApplication::getModel('user');
		$message['recipient'] = $userModel->getUser($message['toID']);
		$message['sender'] = $userModel->getUser($message['fromID']);

		// Return message
		return $message;
	}

	/**
	 * Append details for each message in the given array
	 *
	 * @param array Array of messages to add details to
	 * @param int User ID to check for permissions
	 */
	public function addDetails(&$messages, $userId)
	{
		if (!empty($messages)) {
			foreach ($messages as $messageId => &$message) {
				$message = $this->getMessage($messageId, $userId);
			}
		}
	}

	/**
	 * Get all messages of a certain type, belonging to a person.
	 *
	 * @param int User ID
	 * @param string Folder (sent/deleted/inbox)
	 * @param int Offset
	 * @param int Limit
	 * @param int Set to total if passed in as true
	 * @return array List of messages
	 */
	public function getUserMessages($userId, $folder, $offset = null, $limit = null, &$total = null)
	{
		// Get message IDs
		$query = 'SELECT SQL_CALC_FOUND_ROWS m.messageID
			FROM messages AS m
			WHERE ';
		switch ($folder){
			case 'sent':
				/* Sent messages */
				$query .= 'm.fromID = '.(int)$userId.'
					AND m.sdel != 1';
				break;

			case 'deleted':
				/* Deleted received messages */
				$query .= 'm.toID = '.(int)$userId.'
					AND m.rdel = 1';
				break;

			default: case 'inbox':
				/* Received messages */
				$query .= 'm.toID = '.(int)$userId.'
					AND m.rdel != 1';
				break;
		}
		$query .= ' ORDER BY m.sent DESC';
		$this->_db->setQuery($query, $offset, $limit, $total);
		$messages = $this->_db->loadAssocList('messageID');

		// Get number of messages
		if ($total) {
			$total = (int)$this->_db->getFoundRows();
		}
		
		if (!$messages) {
			return false;
		}

		// Add message details and return
		$this->addDetails($messages, $userId);
		return $messages;
	}

	/**
	 * Get message history
	 *
	 * @param int Primary User ID
	 * @param int Secondary User ID
	 * @param int Optional end date for history (eg. yesterday)
	 * @param int Offset
	 * @param int Limit
	 * @param int Set to total if passed in as true
	 * @return array List of messages
	 */
	public function getMessageHistory($primaryUserId, $secondaryUserId, $endDate = null, $offset = null, $limit = null, &$total = null)
	{
		// Get message IDs
		$query = 'SELECT m.messageID
			FROM messages AS m
			WHERE ((m.fromID = '.(int)$primaryUserId.' AND m.toID = '.(int)$secondaryUserId.')
				OR (m.fromID = '.(int)$secondaryUserId.' AND m.toID = '.(int)$primaryUserId.'))';
		if ($endDate) {
			$query .= ' AND m.sent < "'.date('Y-m-d H:i:s', $endDate).'"';
		}
		$query .= ' ORDER BY m.sent DESC';
		$this->_db->setQuery($query, $offset, $limit, $total);
		$messages = $this->_db->loadAssocList('messageID');
		if (!$messages) {
			return false;
		}

		// Get number of messages
		if ($total) {
			$total = $this->_db->getFoundRows();
		}

		// Add message details and return
		$this->addDetails($messages, $primaryUserId);
		return $messages;
	}

	/**
	 * Sends a message
	 *
	 * @param int From User ID
	 * @param int To User ID
	 * @param string Subject
	 * @param string Message
	 * @return bool True on success, false otherwise
	 */
	public function sendMessage($fromUserId, $toUserId, $subject, $message)
	{
		// Add message
		$query = 'INSERT INTO messages
			SET fromID = '.(int)$fromUserId.',
				toID = '.(int)$toUserId.',
				subject = "'.Database::escape($subject).'",
				body = "'.Database::escape($message).'",
				fromRead = 0,
				toRead = 0,
				sDel = 0,
				rDel = 0,
				sent = NOW()';
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		$messageId = $this->_db->getInsertID();
/*
		// Flag user has a new message
		$query = 'UPDATE user_info
			SET hasgotmail = 1
			WHERE userid = '.(int)$toUserId;
		$this->_db->setQuery($query);
		$this->_db->query();

		// Add alert
		$personModel = BluApplication::getModel('person');
		$fromUser = $personModel->getPerson(array('member' => $fromUserId));
		$alertsModel = $this->getModel('alerts');
		$alertId = $alertsModel->createAlert('message', array(
			'messageId' => $messageId,
			'messageSubject' => $subject
		), $fromUserId);
		$alertsModel->applyAlert($alertId, $toUserId);
*/
		return $messageId;
	}

	/**
	 * Delete a message
	 *
	 * @param int Message ID
	 */
	public function deleteMessage($messageId, $userId)
	{
		// Get message details
		$message = $this->getMessage($messageId, $userId);
		if (!$message) {
			return false;
		}

		$fields = '';
		// Recipient or sender?
		if ($message['fromID'] == $userId) {
			$fields .= 'sdel = 1';
		}
		if ($message['toID'] == $userId) {
			if($fields) {
				$fields .= ',';
			}
			$fields .= 'rdel = 1';
		}

		// Update appropriate read flag
		$query = 'UPDATE messages
			SET '.$fields.'
			WHERE messageID = '.(int)$messageId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}

	/**
	 * Set a message as 'read' by a person
	 *
	 * @param int Message ID
	 * @param int User ID
	 * @return bool True on success, false otherwise
	 */
	public function setRead($messageId, $userId)
	{
		// Get message details
		$message = $this->getMessage($messageId, $userId);
		if (!$message) {
			return false;
		}

		// Recipient or sender?
		if ($message['fromID'] == $userId) {
			$flagField = 'fromRead';
		} elseif ($message['toID'] == $userId) {
			$flagField = 'toRead';
		} else {
			// Who the hell are you?
			return false;
		}

		// Update appropriate read flag
		$query = 'UPDATE messages
			SET '.$flagField.' = 1
			WHERE messageID = '.(int)$messageId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}

}

?>