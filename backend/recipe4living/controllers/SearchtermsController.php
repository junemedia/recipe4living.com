<?php

/**
 *	Search terms controller
 *
 *	@package BluApplication
 *	@subpackage BackendControllers
 */
class Recipe4livingSearchtermsController extends ClientBackendController
{

	protected $_menuSlug = 'searchterm_listing';

	/**
	 * Base url
	 *
	 * @var string Base url
	 */
	protected $_baseUrl = '/searchterms';

	public function view()
	{
		// Get models
		$searchTermModel = BluApplication::getModel('searchterms');

		$page = Request::getInt('page', 1);
		$sort = Request::getString('sort', 'date_desc');

		$limit = 20;
		$total = NULL;

		$searchTerms = $searchTermModel->getSearchTerms($page, $limit, $total, $sort);
		$searchTermModel->getSearchTermCounts($searchTerms);
		
		$pagination = Pagination::simple(array(
			'limit' => $limit,
			'total' => $total,
			'current' => $page,
			'url' => '?sort='.$sort.'&amp;page='
		));
		
		// Load template
		include(BLUPATH_TEMPLATES.'/search_terms/view.php');
	}
	
	public function download() {
		// Get models
		$searchTermModel = BluApplication::getModel('searchterms');
		$sort = Request::getString('sort', 'date_desc');
		$total = null;
		$searchTerms = $searchTermModel->getSearchTerms(1, 20000, $total, $sort);
		$searchTermModel->getSearchTermCounts($searchTerms);
		if($searchTerms) {
			$outputFile = BLUPATH_BASE.'/uploads/'.uniqid().time().'.csv';
			$f = fopen($outputFile,'w');
			$header = array('Date','Search Term','Refine Search Term','Results','Count');
			fputcsv($f,$header);
			foreach($searchTerms as $searchTerm) {
				$row = array();
				$row['searched'] = date('Y-m-d',$searchTerm['searched']);
				$row['term'] = $searchTerm['term'];
				$row['termExtra'] = $searchTerm['termExtra'];
				$row['resultCount'] = $searchTerm['resultCount'];
				$row['count'] = $searchTerm['count'];
				fputcsv($f,$row);
			}
			fclose($f);
			header('Content-Type: text/octet-stream');
			header('Content-Length: '.filesize($outputFile));
			header('Content-Disposition: attachment; filename="search_terms.csv"');
			readfile($outputFile);
			unlink($outputFile);
		}
		die;
	}

}

?>
