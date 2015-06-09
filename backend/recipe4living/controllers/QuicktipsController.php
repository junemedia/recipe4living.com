<?php

/**
 *	Quicktips Controller
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class Recipe4livingQuicktipsController extends Recipe4livingArticlesController
{
	/**
	 *	Default view
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_view = 'quicktip_listing';

	/**
	 *	Current item type
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_itemType = 'quicktip';
	
	/** 
	 *	Menu slug
	 *
	 *	@access protected
	 *	@var string
	 */
	protected $_menuSlug = 'quicktip_listing';
	
	/**
	 *	Send confirmation email to item author when setting item live
	 *
	 *	@access protected
	 *	@var bool
	 */
	protected $_sendSubmissionEmail = true;
	
	/**
	 *	Prepend to base URL
	 *
	 *	@access public
	 *	@param array Arguments
	 */
	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->_baseUrl = '/quicktips/'.implode('/', $this->_args);
	}
	
	/**
	 *	Get title when filters are barren.
	 *
	 *	@access protected
	 *	@return string
	 */
	protected function _getEmptyFilterTitle()
	{
		return 'All quicktips';
	}
	
	/**
	 *	Edit quicktip
	 *
	 *	@access public
	 */
	public function edit() {
		$itemsModel = BluApplication::getModel('items');
		$quicktip = $itemsModel->getItem($this->_itemId);
		
		Template::set('tinyMce', true);
		
		// Load template
		include(BLUPATH_TEMPLATES.'/quicktips/quicktip.php');
	}
	
	/**
	 *	Save quicktip
	 *
	 *	@access public
	 */
	public function save()
	{
		$itemsModel = BluApplication::getModel('items');
		if($quicktipId = $this->_itemId) {
			$quicktip = $itemsModel->getItem($quicktipId);
			if(!$quicktip) {
				return $this->_redirect('/quicktips');
			}
			$oldSection = $itemsModel->getQuicktipSection($quicktip['id']);
		}
	
		$section = Request::getString('section');
		$title = Request::getString('title');
		$body = Request::getString('body', null, null, true);
		
		$error = false;
		
		if(empty($section)) {
			Messages::addMessage('Section cannot be empty.');
			$error = true;
		}
		if(empty($title)) {
			Messages::addMessage('Title cannot be empty.');
			$error = true;
		}
		if(empty($body)) {
			Messages::addMessage('Body cannot be empty.');
			$error = true;
		}
		
		$slug = Utility::slugify($title);
		$i = 1;
		while($itemsModel->isSlugInUse($slug, $quicktipId)) {
			$i++;
			$slug = Utility::slugify($title).'_'.$i;
			if($i>=100) {
				Messages::addMessage('Could not generate slug.');
				$error = true;
				break;
			}
		}
		
		if($error) {
			$this->edit();
			return false;
		}
		
		if($quicktipId) {
			$permissionsModel = BluApplication::getModel('permissions');
			$user = $permissionsModel->getUser();
			if(!$itemsModel->editItem($quicktipId, $title, $body, null, null, null, null, $slug, $user['id'])) {
				$error = true;
			}
		}
		elseif (!$quicktipId = $itemsModel->addQuicktip($title, $body, null, null, null, null, $slug)) {
			$error = true;
		}
		
		$metaModel = BluApplication::getModel('meta');
		$firstLetterMetaGroupId = $metaModel->getGroupIdBySlug('encyclopedia_of_tips_first_letters');
		$sectionMetaGroupId = $metaModel->getGroupIdBySlug('encyclopedia_of_tips_sections');
		if(!empty($quicktip)) {
			// delete all existing meta values first when updating quicktip
			$metaModel->deleteItemMetaValues($quicktipId, $firstLetterMetaGroupId);
			$metaModel->deleteItemMetaValues($quicktipId, $sectionMetaGroupId);
		}
		
		// Assign to corresponding first letter
		if(!empty($section)) {
			$firstLetter = strtolower(substr($section, 0, 1));
		}
		else {
			$firstLetter = strtolower(substr($title, 0, 1));
		}
		$valueId = $metaModel->getValueIdBySlugAndGroupId($firstLetter, $firstLetterMetaGroupId);
		if (!$metaModel->addItemMetaValues($quicktipId, $firstLetterMetaGroupId, $valueId)) {
			$error = true;
		}
		// Assign to section
		if($section) {
			$sectionSlug = Utility::slugify($section);
			$i = 1;
			while($metaModel->isMetaValueSlugInUse($sectionSlug)) {
				$i++;
				$sectionSlug = Utility::slugify($section).'_'.$i;
				if($i>=100) {
					Messages::addMessage('Could not generate slug.');
					$error = true;
					break;
				}
			}
			$sectionValueId = $metaModel->getValueIdBySlugAndGroupId($sectionSlug, $sectionMetaGroupId);
			if(!$sectionValueId) {
				if($sectionValueId = $metaModel->addMetaValue($sectionMetaGroupId, $section)) {
					if(!$metaModel->addLanguageMetaValue($sectionValueId, null, $section, null, null, null, null, $sectionSlug)) {
						$error = true;
					}
				}
				else {
					$error = true;
				}
			}
			if (!$metaModel->addItemMetaValues($quicktipId, $sectionMetaGroupId, $sectionValueId)) {
				$error = true;
			}
		}
		
		// delete section meta value if it's not used any more
		if(!empty($oldSection) && $oldSection!=$section) {
			$oldSectionSlug = Utility::slugify($oldSection);
			$oldSectionMetaValueId = $metaModel->getValueIdBySlug($oldSectionSlug);
			$oldSectionQuicktips = $itemsModel->getQuicktipsByMetaGroup('encyclopedia_of_tips_sections',$oldSectionMetaValueId);
			if(empty($oldSectionQuicktips[$oldSection])) {
				$metaModel->deleteMetaValue($oldSectionMetaValueId, true, true, false, $sectionMetaGroupId);
			}
		}
		
		if (!$error) {
			// clear cache
			$itemsModel->flushQuicktips();
			Messages::addMessage('Quicktip <code>'.$title.'</code> has been saved.');
			return $this->_redirect('/quicktips');
		} else {
			Messages::addMessage('Could not save quicktip, please try again.', 'error');
			$this->edit();
		}
		
	}
	
}

?>
