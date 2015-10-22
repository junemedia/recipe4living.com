<?php

/**
 *	Workitmom Reports model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendReportsModel extends ClientReportsModel
{
	/**
	 *	Overrides WorkitmomReportsModel.
	 */
	public function getReport($id) 
	{
		if (!$report = parent::getReport($id)) {
			return false;
		}
		$report['backend_link'] = '/reports/details/'.$report['id'];
		return $report;
	}
	
	/**
	 *	Sets a report's status to 'resolved'.
	 *	Also optionally sets all other reports on the same object to resolved.
	 *
	 *	@param int Report ID
	 *	@param bool Set other reports on the same reported object to resolved too.
	 *	@return bool Success.
	 */
	public function resolveReport($reportId, $cascade = true)
	{		
		// Get report (object) details
		$report = $this->getReport($reportId);
		
		// Build arguments
		$resolve = array('status' => 'resolved');
		$resolve_criteria = array();
		$resolve_criteria['objectType'] = $report['objectType'];
		$resolve_criteria['objectId'] = $report['objectId'];
		if (!$cascade){
			$resolve_criteria['reportId'] = $report['reportId'];
		}
		
		// Commit to database
		$resolved = $this->_edit('reports', $resolve, array(), $resolve_criteria);
		
		// Return
		return $resolved;
	}
	
	/**
	 *	Sets a report's status to 'viewed' (viewed by an admin).
	 *
	 *	@param &array Report
	 *	@return bool Success.
	 */
	public function viewReport(&$report)
	{		
		// Check if can be 'viewed'
		if ($report['status'] != 'pending'){
			return false;
		}
		
		// Build arguments
		$view = array('status' => 'viewed');
		$view_criteria = array('reportId' => $report['id']);
		
		// Commit to database
		$viewed = $this->_edit('reports', $view, array(), $view_criteria);
		
		// Change report
		if ($viewed){
			$report['status'] = 'viewed';
		}
		
		// Return
		return $viewed;		
	}
	
	/**
	 *	Set a report's status as 'replied'.
	 *
	 *	N.B. doesn't actually reply to anybody, just sets the flag.
	 *
	 *	@param int Report ID
	 *	@return bool Success.
	 */
	public function replyReport($reportId)
	{
		$query = 'UPDATE `reports`
			SET `status` = "replied"
			WHERE `reportId` = '.(int) $reportId.'
				AND `status` != "resolved"';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}	
}

?>