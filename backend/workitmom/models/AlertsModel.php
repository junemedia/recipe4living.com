<?php

/**
 *	Workitmom Alerts Model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendAlertsModel extends ClientAlertsModel
{
	/**
	 * Get alerts ready for e-mailing
	 *
	 * @param string Alert frequency
	 * @return array List of emails
	 */
	public function getAlertsToEmail($frequency = 'immediate')
	{
		$query = 'SELECT x.alertID, x.userID
			FROM xrefuseralert AS x
				LEFT JOIN alerts AS a ON a.alertID = x.alertID
				LEFT JOIN userAlertPrefs AS uap ON a.alertType = uap.alertType AND uap.userID = x.userID
				LEFT JOIN defaultAlertPrefs AS dap ON a.alertType = dap.alertType
				LEFT JOIN users AS u ON u.UserID = x.userID
			WHERE x.emailed = 0
				AND x.seen = 0
				AND ((uap.uapID IS NOT NULL AND uap.emailAt = "'.Database::escape($frequency).'")
					OR dap.emailAt = "'.Database::escape($frequency).'")
				AND u.terminatedtime = 0
			ORDER BY x.userID, a.alertType, a.alertTime DESC
			LIMIT 0, 5000';
		$this->_db->setQuery($query);
		$alerts = $this->_db->loadAssocList('alertID');
		if (!$alerts) {
			return false;
		}

		// Get alert details
		foreach ($alerts as $alertId => &$alert) {
			$alert = array_merge($alert, $this->getAlert($alert['userID'], $alertId));
		}

		return $alerts;
	}

	/**
	 * Flag alert emailed
	 *
	 * @param int Alert ID
	 * @return bool True on success, false otherwise
	 */
	public function flagEmailSent($userId, $alertId)
	{
		$query = 'UPDATE xrefuseralert
			SET emailed = 1
			WHERE alertID = '.(int)$alertId.'
				AND userID = '.(int)$userId;
		$this->_db->setQuery($query);
		$this->_db->query();
	}
}

?>