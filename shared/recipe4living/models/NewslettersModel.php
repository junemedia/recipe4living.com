<?php

/**
 *	Newsletters model
 *
 *	@package BluApplication
 *	@subpackage SharedModels
 */
class ClientNewslettersModel extends BluModel {

	/**
	 *	Get campaigns
	 *
	 *	@access public
	 *  @param string newsletter
	 *	@return array campaigns
	 */
  public function getCampaigns($newsletter) {
    $one_day = 60 * 60 * 24;
    $two_wks = $one_day * 14;

    $day = time();
    $end = $day + $two_wks;
    $day -= ($one_day * 3);

    $campaigns = array();
    while ($day < $end) {
      array_push($campaigns, date('D, M d, Y', $day));
      $day += $one_day;
    }

    return $campaigns;
  }
}
