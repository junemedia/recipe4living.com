<?php


class Recipe4livingConfigController extends ClientBackendController
{
	protected $_baseUrl = '/config';


	/**
	 * Allow admin users to edit a config value
	 */
	public function edit() 
	{
		$key = $this->_args[0];

		// Get the current valueof the setting, and ensure it is an array (This doesn't work for other types of config setting at the moment).
		$current = BluApplication::getSetting($key);
		if (!$current || !is_array($current)) {
			return $this->_redirect('/');
		}
		
		if (Request::getArray('value')) {
			return $this->update($key);
		}

		// Show option to change
		$baseUrl = $this->_baseUrl;
		include(BLUPATH_TEMPLATES.'/config/edit.php');
	}

	/**
	 * Update a config value
	 */
	public function update($key) 
	{
		$configModel = BluApplication::getModel('config');
		$value = Request::getArray('value');

		// Clean the input
		$value = array_filter($value);

		if ($configModel->update($key,$value)) {
			Messages::addMessage('Config setting updated successfully','info');
		}
		return $this->_redirect($this->_baseUrl.'/edit/'.$key);
	}
}
