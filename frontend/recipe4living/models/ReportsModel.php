<?php

/**
 *	Reports model
 *
 *	@package BluApplication
 *	@subpackage FrontendModels
 */
class ClientFrontendReportsModel extends ClientReportsModel
{
	/**
	 *	Report a review
	 *
	 *	@access public
	 *	@param int Review ID
	 *	@param int User ID
	 *	@param string Reason
	 *	@param bool Set live immediately
	 *	@return int Report ID
	 */
	public function reportReview($reviewId, $userId, $reason = null, $live = true)
	{
		return $this->_reportComment($reviewId, $userId, $reason, $live);
	}
	
	/**
	 *	Report a comment
	 *
	 *	@access protected
	 *	@param int Comment ID
	 *	@param int User ID
	 *	@param string Reason
	 *	@param bool Set live immediately
	 *	@return int Report ID
	 */
	protected function _reportComment($commentId, $userId, $reason = null, $live = true)
	{
		// Add to database
		if (!$reportId = $this->_addReport('comment', $commentId, $userId, $reason)) {
			return false;
		}
		
		// Flush cache
		if ($live) {
			$itemsModel = BluApplication::getModel('items');
			$itemsModel->flushComment($commentId);
		}
		
		// Return
		return $reportId;
	}
	
	/**
	 *	File a report
	 *
	 *	@access protected
	 *	@param string Object type
	 *	@param int Object ID
	 *	@param int User ID
	 *	@param string Reason
	 */
	protected function _addReport($objectType, $objectId, $userId, $reason = null)
	{
		// Add to database
		$query = 'INSERT INTO `reports`
			SET `objectType` = "'.$this->_db->escape($objectType).'",
				`objectId` = '.(int) $objectId.',
				`reporter` = '.(int) $userId.',
				`time` = NOW(),
				`reason` = "'.$this->_db->escape($reason).'",
				`status` = "pending"
			ON DUPLICATE KEY UPDATE
				`reason` = "'.$this->_db->escape($reason).'"';
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Return
		return $this->_db->getInsertID();
	}
}

?>