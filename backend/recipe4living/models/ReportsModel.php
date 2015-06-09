<?php

/**
 *	Reports model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendReportsModel extends ClientReportsModel
{
	/**
	 *	Set the status of a report
	 *
	 *	@access public
	 *	@param int Report ID
	 *	@param string Status
	 *	@param bool Set live immediately
	 *	@return bool Success
	 */
	public function setStatus($reportId, $status, $live = true)
	{
		// Add to database
		$query = 'UPDATE `reports`
			SET `status` = "'.$this->_db->escape($status).'"
			WHERE `id` = '.(int) $reportId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Flush cache
		if ($live) {
			$query = 'SELECT r.objectType, r.objectId
				FROM `reports` AS `r`
				WHERE r.id = '.(int) $reportId;
			$this->_db->setQuery($query);
			$object = $this->_db->loadAssoc();
			switch ($object['objectType']) {
				case 'comment':
					$itemsModel = BluApplication::getModel('items');
					$itemsModel->flushItemComments($object['objectId']);
					break;
			}
		}
		
		// Return
		return true;
	}
}

?>