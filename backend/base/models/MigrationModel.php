<?php

/**
 *	Migration
 */
class BackendMigrationModel extends BluModel
{
	/**
	 *	Allow database errors
	 *
	 *	@access public
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_db->allowErrors(true);
	}
	
	/**
	 *	Migrate config settings (to be serialized)
	 *
	 *	@access public
	 *	@return bool Success
	 */
	public function migrateSettings()
	{
		// Get raw from database
		$query = 'SELECT c.*
			FROM `config` AS `c`';
		$this->_db->setQuery($query);
		$settings = $this->_db->loadAssocList();
		if (empty($settings)) {
			return true;
		}
		
		// Try to unserialize each - if doesn't work, we know it's unserialized in the database
		$success = true;
		foreach ($settings as $setting) {
			if (unserialize($setting['configValue']) === false && serialize(false) != $setting['configValue']) {
				$query = 'UPDATE `config`
					SET `configValue` = "'.$this->_db->escape(serialize($setting['configValue'])).'"
					WHERE `configKey` = "'.$this->_db->escape($setting['configKey']).'"
						AND `siteId` = "'.$this->_db->escape($setting['siteId']).'"';
				$this->_db->setQuery($query);
				if (!$this->_db->query()) {
					$success = false;
				}
			}
		}
		
		// Return
		return $success;
	}
}

?>