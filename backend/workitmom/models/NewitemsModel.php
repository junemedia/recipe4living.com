<?php

/**
 *	Workitmom Items model
 *
 *	@package BluApplication
 *	@subpackage BackendModels
 */
class ClientBackendNewitemsModel extends ClientNewitemsModel
{
	/**
	 *	Set status of an item. (Live or pending)
	 *
	 *	@param int Item ID
	 *	@param bool Status (Live or not)
	 *	@return bool Success.
	 */
	public function setStatus($itemId, $live){
		
		// Sanitise
		$itemId = (int) $itemId;
		$live = (bool) $live;
		
		// Update
		$query = 'UPDATE `article` AS `a`
			SET a.articleLive = '.(int) $live.'
			WHERE a.articleID = '.$itemId;
		$this->_db->setQuery($query);
		return (bool) $this->_db->query();
		
	}
	
	/**
	 *	Get item.
	 *
	 *	@param int Item ID.
	 *	@return array Item.
	 */
	public function getItem($itemId){
		
		// Parent
		if (!$item = parent::getItem($itemId)){
			return false;
		}
		
		// Backend link
		$item['backend_link'] = $this->getBackendLink($item);
		
		// Return
		return $item;
		
	}
	
	/**
	 *	Edit an item.
	 *
	 *	@param int Item ID
	 *	@param array Data to update
	 *	@return bool Success.
	 */
	public function edit($itemId, array $edit)
	{
		$edit = $this->_db->escape($edit);
		$query = 'UPDATE `article`
			SET `articleTitle` = "'.$edit['title'].'",
				`articleSubTitle` = "'.$edit['subtitle'].'",
				`articleBody` = "'.$edit['body'].'"
			WHERE `articleID` = '.(int) $itemId;
		$this->_db->setQuery($query);
		return $this->_db->query();
	}
	
	/**
	 *	Get backend link for an item. (Without SITEURL)
	 *
	 *	@param array Item.
	 *	@return string Link.
	 */
	public function getBackendLink($item){
		
		// Build link.
		$link = '/';
		switch($item['type']){
			case 'article':
				$link .= 'articles';
				break;

			case 'news':
				$link .= 'news';
				break;

			case 'lifesaver':
				$link .= 'lifesavers';
				break;

			case 'list':
			case 'checklist':
				$link .= 'checklists';
				break;

			case 'quicktip':
				$link .= 'quicktips';
				break;

			case 'landingpage':
			case 'essential':
				$link .= 'essentials';
				break;

			case 'question':
				$link .= 'questions';
				break;

			case 'interview':
				$link .= 'interviews';
				break;

			case 'slideshow':
				$link .= 'slideshows';
				break;
		}
		$link .= '/details/'.$item['id'];
		
		// Return
		return $link;
		
	}	
}

?>