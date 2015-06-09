<?php

/**
 *	Recipe4living Assets model
 *
 *	@package BluApplication
 *	@subpackage FrontendModels
 */
class ClientFrontendAssetsModel extends ClientAssetsModel
{
	
	/**
	 *	Get images submitted by a user
	 *
	 *	@access public
	 *	@param int User ID
	 *	@return array
	 */
	public function getUserArticleImages($userId, $offset = 0, $limit = 20)
	{
		$itemsModel = BluApplication::getModel('items');
		$query = 'SELECT articleId, type, filename, title, description, minidescription,alt FROM articleImages WHERE (type="default" OR type="inline" OR type="gallery") AND userId='.(int)$userId;
		$this->_db->setQuery($query, $offset, $limit);
		$images = $this->_db->loadAssocList();
		foreach($images as &$image) {
			$image['link'] = ASSETURL.'/itemimages/300/300/3/'.$image['filename'];
			$image['item'] = $itemsModel->getItem($image['articleId']);
		}
		return $images;
	}
	
}

?>
