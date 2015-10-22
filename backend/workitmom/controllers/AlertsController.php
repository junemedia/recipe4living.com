<?php

/**
 * Alerts Controller
 *
 * @package BluApplication
 * @subpackage BackendControllers
 */
class WorkitmomAlertsController extends ClientBackendController
{
	/**
	 * Send alert emails
	 */
	public function sendAlertEmails()
	{
		$startTime = microtime(true);

		// Get alert frequency to send
		$frequency = Request::getCmd('frequency', 'immediate');

		// Get alerts to send e-mails about
		$alertsModel = BluApplication::getModel('alerts');
		$alerts = $alertsModel->getAlertsToEmail($frequency);
		if (!$alerts) {
			echo 'No alert emails to send';
			return true;
		}

		// Determine listing format to use
		$format = 'format_email'.($frequency == 'daily' ? '_daily' : '');
		$template = 'alertupdate'.($frequency == 'daily' ? '_daily' : '');

		// Build alert lines for each user
		$userAlerts = array();
		foreach ($alerts as $alert) {
			$userAlerts[$alert['userID']][] = $alert;
		}

		// Send e-mails
		$emailsSent = 0;
		$personModel = BluApplication::getModel('newperson');
		foreach ($userAlerts as $userId => $alerts) {

			// Get user details
			$user = $personModel->getPerson(array('member' => $userId));

			// Build alert lines
			$alertLines = '';
			foreach ($alerts as $alert) {
				$alertLines .= '<li>'.$alert[$format].'</li>';
			}

			// Send email
			$emailMsg = new Email();
			$vars = array(
				'name' => $user['name'],
				'alerts' => $alertLines
			);
			$emailMsg->quickSend($user['email'], $user['name'], 'Work It, Mom! Updates', $template, $vars);

			// Flag as sent
			foreach ($alerts as $alert) {
				$alertsModel->flagEmailSent($user['UserID'], $alert['alertID']);
			}
			$emailsSent ++;
		}

		// Done!
		$endTime = microtime(true);
		$time = round($endTime - $startTime, 3);
		echo $emailsSent.' emails sent in '.$time.' seconds';
	}
}

?>
