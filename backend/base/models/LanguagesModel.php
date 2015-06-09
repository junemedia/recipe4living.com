<?php

/**
 *	Backend Languages model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class BackendLanguagesModel extends LanguagesModel
{
	/**
	 *	Get a language string
	 *
	 *	@access public
	 *	@param Language string ID
	 *	@return array
	 */
	public function getLanguageString($languageStringId)
	{
		$query = 'SELECT ls.*
			FROM `languageStrings` AS `ls`
			WHERE ls.id = '.(int) $languageStringId;
		$this->_db->setQuery($query);
		return $this->_db->loadAssoc();
	}
	
	/**
	 *	Update a language string
	 *
	 *	@access public
	 *	@param int Language string ID
	 *	@param string Text
	 *	@return bool Success
	 */
	public function updateLanguageString($languageStringId, $text)
	{
		// Update database
		$query = 'UPDATE `languageStrings`
			SET `text` = "'.$this->_db->escape($text).'"
			WHERE `id` = '.(int) $languageStringId;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			return false;
		}
		
		// Update cache.
		$languageString = $this->getLanguageString($languageStringId);
		$this->clearLanguageStrings($languageString['lang']);
		
		// Return
		return true;
	}
	
	/**
	 *	Clear language strings cache for a language
	 *
	 *	@access public
	 *	@param string Language code
	 *	@return bool Success
	 */
	public function clearLanguageStrings($langCode)
	{
		return $this->_cache->delete('languageStrings_'.$langCode);
	}
}

?>