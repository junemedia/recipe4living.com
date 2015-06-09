<?php

/**
 *	Language strings admin
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class LanguagesController extends ClientBackendController
{
	/**
	 *	Language object
	 *
	 *	@access protected
	 *	@var Language
	 */
	protected $_language;
	
	/**
	 *	Menu slug
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_menuSlug = 'languages';
	
	/**
	 *	Constructor
	 *
	 *	@access public
	 */
	public function __construct($args)
	{
		parent::__construct($args);
		
		// Set current language
		$this->_language = BluApplication::getLanguage();
	}
	
	/**
	 *	List language strings
	 *
	 *	@access public
	 */
	public function view()
	{
		// Get models
		$languagesModel = BluApplication::getModel('languages');
		
		// Get language strings
		$languageStrings = $this->_language->getLanguageStrings();
		
		// Display variables
		$title = 'Text entries';
		$languages = Language::getAvailableLanguages();
		$currentLangCode = $this->_language->getLanguageCode();
		
		// Load template
		include(BLUPATH_BASE_TEMPLATES.'/languages/view.php');
	}
	
	/**
	 *	Save a language string
	 *
	 *	@access public
	 */
	public function saveLanguageString()
	{
		// Deal with request
		if ($languageStringId = Request::getInt('languageStringId')) {
			$text = Request::getString('text', null, 'default', true);
			
			// Update model
			$languagesModel = BluApplication::getModel('languages');
			$updated = $languagesModel->updateLanguageString($languageStringId, $text);
			$languageString = $languagesModel->getLanguageString($languageStringId);
			if ($updated) {
				Messages::addMessage('Text updated for <code>'.$languageString['place'].'</code>.', 'info');
			} else {
				Messages::addMessage('Text could not be updated for <code>'.$languageString['place'].'</code>', 'error');
			}
		}
		
		// Return
		return $this->view();
	}
}

?>