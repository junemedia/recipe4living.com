<?php

/**
 * Search Controller
 *
 * @package BluApplication
 * @subpackage BackendControllers
 */
class WorkitmomSearchController extends ClientBackendController
{
	/**
	 * Update search table
	 */
	public function updateIndex()
	{
		$startTime = microtime(true);

		// Update index
		$searchModel = BluApplication::getModel('search');
		$searchModel->updateIndex();

		// Done!
		$endTime = microtime(true);
		$time = round($endTime - $startTime, 3);
		echo 'Search index updated in '.$time.' seconds';
	}
}

?>
