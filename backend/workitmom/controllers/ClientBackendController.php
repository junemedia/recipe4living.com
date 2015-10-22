<?php

/**
 * Back end controller base class
 *
 * @package BluApplication
 * @subpackage BackendControllers
 */
abstract class ClientBackendController extends BackendController
{
	/**
	 *	Shortcut.
	 */
	final protected static function &getModel($name)
	{
		return BluApplication::getModel($name);
	}
	
	/**
	 *	Shortcut for setting template variables.
	 */
	protected function set_template_vars()
	{
		$toMerge = func_get_args();
		array_unshift($toMerge, $this->template_vars);
		$this->template_vars = call_user_func_array('array_merge_recursive', $toMerge);
	}
	
	/**
	 *	Display person
	 *
	 *	@param PersonModel::getPerson first argument.
	 */
	public function display_person($identifier = null)
	{
		/* Get user */
		$personModel = $this->getModel('newperson');
		$identifier = Utility::coalesce($identifier, array('username' => $this->_args[0]));
		$person = $personModel->getPerson($identifier);
		
		/* Set page meta */
		$this->_doc->setTitle('User: '.$person['name']);
		
		/* Load template */
		include(BLUPATH_TEMPLATES.'/site/person.php');
		
	}
}
?>