<?php


class ClientBackendConfigModel extends BluModel 
{

	/**
	 * Update a config setting.  Currently only deals with arrays.
	 *
	 * @param $key		Config key to update
	 * @param $value	Value to set it to
	 */
	public function update($key,$value) 
	{
		$value = serialize($value);
		$query = 'UPDATE `config`
			SET `configValue` = "'.$this->_db->escape($value).'"
			WHERE `configKey` = "'.$this->_db->escape($key).'"';
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}

		$this->_cache->delete('settings');
		return true;
	}
}
