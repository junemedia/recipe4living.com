<?php

/**
 *	Article history controller
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class Recipe4livingArticlehistoryController extends ClientBackendController
{

	protected $_menuSlug = 'article_history';

	/**
	 * Base url
	 *
	 * @var string Base url
	 */
	protected $_baseUrl = '/articlehistory';

	public function view()
	{
		
		// Get models
		$articleHistoryModel = BluApplication::getModel('articlehistory');
		
		$page = Request::getInt('page', 1);
		$sort = Request::getString('sort', 'date_desc');
		
		// Clear search
		if(Request::getBool('clear')) {
			return $this->_redirect($this->_baseUrl);
		}
		
		// Set search parameters
		$urlArgsArray = array();
		if($articleTitle = Request::getString('article_title')) {
			$urlArgsArray['article_title'] = $articleTitle;
		}
		
		$baseUrl = $this->_baseUrl;
		$filterArray = $urlArgsArray;
		
		$sortPageUrl = SITEURL . $baseUrl . '?' . http_build_query($urlArgsArray);
		$urlArgsArray['sort'] = $sort;
		
		$urlArgsArray['page'] = '';
		$paginationUrl = SITEURL . $baseUrl . '?' . http_build_query($urlArgsArray);
		$urlArgsArray['page'] = $page;
		$pageUrl = SITEURL . $baseUrl . '?' . http_build_query($urlArgsArray);
		
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$total = null;
		$articleHistoryList = $articleHistoryModel->getArticleHistoryList($offset, $limit, $total, $sort, $filterArray);
		
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => $paginationUrl
		));
		
		Session::set('articleHistoryListDisplayArgs', $urlArgsArray);
		
		// Load template
		include(BLUPATH_TEMPLATES.'/article_history/view.php');
		
	}

	public function history()
	{
		// Get models
		$articleHistoryModel = BluApplication::getModel('articlehistory');
		$itemsModel = BluApplication::getModel('items');
		
		$articleId = Request::getInt('articleId');
		$article = $itemsModel->getItem($articleId);
		if(!$article) {
			$this->_redirect($this->_baseUrl);
		}
		
		$page = Request::getInt('page', 1);
		$sort = Request::getString('sort', 'date_desc');
		
		$filterArray = array('articleId'=>$articleId);
		
		$baseUrl = $this->_baseUrl;
		
		$sortPageUrl = SITEURL . $baseUrl . '/history?' . http_build_query($filterArray);
		
		$filterArray['sort'] = $sort;
		$filterArray['page'] = '';
		$paginationUrl = SITEURL . $baseUrl . '/history?' . http_build_query($filterArray);
		
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$total = null;
		$articleHistoryList = $articleHistoryModel->getArticleHistoryList($offset, $limit, $total, $sort, $filterArray);
		
		foreach($articleHistoryList as $revisionId=>$revision) {
			$diff = Utility::getDiff($revision['oldValue'],$revision['newValue']);
			if($revision['type']=='body') {
				$articleHistoryList[$revisionId]['diff'] = $diff;
				$plainOldValue = trim(str_replace('&nbsp;','',strip_tags($revision['oldValue'],'<img>')));
				$plainNewValue = trim(str_replace('&nbsp;','',strip_tags($revision['newValue'],'<img>')));
				$noHtmlDiff = Utility::getDiff($plainOldValue,$plainNewValue);
				if(!$noHtmlDiff) {
					$noHtmlDiff = $plainOldValue;
				}
				$decodedNoHtmlDiff = html_entity_decode($noHtmlDiff);
				if($images = Utility::parseImageTags($decodedNoHtmlDiff)) {
					foreach($images as $image) {
						$noHtmlDiff = str_replace(htmlentities($image['img']),$image['img'],$noHtmlDiff);
					}
				}
				$articleHistoryList[$revisionId]['noHtmlDiff'] = $noHtmlDiff;
			}
			else {
				$articleHistoryList[$revisionId]['diff'] = $diff;
			}
		}
		
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => $paginationUrl
		));
		
		// Load template
		include(BLUPATH_TEMPLATES.'/article_history/history.php');
		
	}
	
	function revert() {
		// Get models
		$articleHistoryModel = BluApplication::getModel('articlehistory');
		$itemsModel = BluApplication::getModel('items');
		$permissionsModel = BluApplication::getModel('permissions');
		
		$revisionId = Request::getInt('revisionId');
		
		if($revision = $articleHistoryModel->getRevision($revisionId)) {
		
			if($article = $itemsModel->getItem($revision['articleId'])) {
			
				$user = $permissionsModel->getUser();
				
				if($revision['newValue']!=$article[$revision['type']]) {
					switch($revision['type']) {
						case 'title':
							$itemsModel->editItem($article['id'], $revision['newValue'], null, null, null, null, null, null, $user['id']);
							Messages::addMessage('Title has been upadated.', 'info');
							break;
						case 'teaser':
							$itemsModel->editItem($article['id'], null, null, $revision['newValue'], null, null, null, null, $user['id']);
							Messages::addMessage('Summary/Blurb has been upadated.', 'info');
							break;
						case 'body':
							$itemsModel->editItem($article['id'], null, $revision['newValue'], null, null, null, null, null, $user['id']);
							Messages::addMessage('Directions/Body have been upadated.', 'info');
							break;
						case 'ingredients':
							$ingredients = explode("\n",$revision['newValue']);
							if($article['ingredients']!=$ingredients) {
								$itemsModel->proposeIngredients($article['id'], $ingredients, $user['id']);
								Messages::addMessage('Ingredients have been upadated.', 'info');
							}
							else {
								Messages::addMessage('Article has NOT been updated. Current value is the same.', 'error');
							}
							break;
					}
				}
				else {
					Messages::addMessage('Article has NOT been updated. Current value is the same.', 'error');
				}
				
				return $this->_redirect($this->_baseUrl.'/history?articleId='.$article['id']);
			
			}
		
		}
		
		return $this->_redirect($this->_baseUrl);
		
	}
	
	function revertToOriginal() {
		// Get models
		$articleHistoryModel = BluApplication::getModel('articlehistory');
		$itemsModel = BluApplication::getModel('items');
		$permissionsModel = BluApplication::getModel('permissions');
		
		$articleId = Request::getInt('articleId');
		$article = $itemsModel->getItem($articleId);
		
		if(!$article) {
			$this->_redirect($this->_baseUrl);
		}
		
		$titleRevision = $articleHistoryModel->getOldestRevision($articleId,'title');
		$teaserRevision = $articleHistoryModel->getOldestRevision($articleId,'teaser');
		$bodyRevision = $articleHistoryModel->getOldestRevision($articleId,'body');
		$ingredientsRevision = $articleHistoryModel->getOldestRevision($articleId,'ingredients');
		
		$user = $permissionsModel->getUser();
		
		if($titleRevision || $teaserRevision || $bodyRevision) {
			$itemsModel->editItem($article['id'], ($titleRevision?$titleRevision['oldValue']:null), ($bodyRevision?$bodyRevision['oldValue']:null), ($teaserRevision?$teaserRevision['oldValue']:null), null, null, null, null, $user['id']);
		}
		if($ingredientsRevision) {
			$ingredients = explode("\n",$ingredientsRevision['oldValue']);
			$itemsModel->proposeIngredients($article['id'], $ingredients, $user['id']);
		}
		
		Messages::addMessage('Article has been upadated to the original version.', 'info');
		
		return $this->_redirect($this->_baseUrl.'/history?articleId='.$articleId);
		
	}
	
}

?>
