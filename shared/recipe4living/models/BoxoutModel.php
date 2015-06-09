<?php

/**
 *	Box model
 *
 *	@package BluApplication
 *	@subpackage SharedModels
 */
class ClientBoxoutModel extends BoxoutModel
{
	/**
	 *	Daily chef can only have one content entry
	 *
	 *	@access protected
	 *	@param array Box
	 *	@param array Arguments
	 */
	protected function _preTransformFeaturedUsers(&$box, array $arguments)
	{
		// Daily chef homepage module
		if ($box['slug'] == 'daily_chef') {
			
			// Auto-generate random user (with recipes and profile image) if none set
			if (empty($box['content']/*['info']['user']*/)) {
				
				$query = 'SELECT u.id
					FROM `users` AS `u`
						LEFT JOIN `articles` AS `a` ON u.id = a.author
						LEFT JOIN `userInfo` AS `ui` ON u.id = ui.userId
					WHERE a.id IS NOT NULL
						AND ui.image != "" AND ui.private = 0 AND a.live = 1 AND (a.goLiveDate IS NULL OR a.goLiveDate<=NOW()) AND a.type = "recipe"
					GROUP BY u.id
					ORDER BY RAND()';
				$this->_db->setQuery($query, 0, 1);
				$userId = $this->_db->loadResult();
				
				$box['content'][40]['info']['user'] = $userId;
			}
			
			// Set it to clear daily
			$box['expiry'] = 60 * 60 * 24;
		}
		
		// Parent call
		parent::_preTransformFeaturedUsers($box, $arguments);
		
		// Daily chef homepage module
		if ($box['slug'] == 'daily_chef') {
			
			// Set flag for admin
			$box['canAdd'] = true;
			$box['canDelete'] = true;
		}
	}
}

?>