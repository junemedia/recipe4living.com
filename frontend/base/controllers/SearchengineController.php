<?php

/**
 * Robot Controller
 *
 * @package BluApplication
 * @subpackage FrontendControllers
 */
class SearchengineController extends ClientFrontendController
{
	/**
	 * Default view
	 */
	public function view()
	{
		// Nothing to see here
		return $this->_errorRedirect();
	}

	/**
	 * Robots.txt
	 */
	public function robots()
	{
		$https = isset($_SERVER['HTTPS']);

		// Bots don't get secure pages
		if ($https) {
			$disallow = array('/');

		// Get list of disallow urls
		} else {

			// Defaults
			$disallow = array(
				'/*...*',
				'/*?format=print*',
				'/alphabeticallist',
				'/oversight/',
				'/emailfriend/',
				'/quick_view/',
				'/cookbooks',
				'/save_to_recipe_box/',
				'/account/',
				'/profile/',
				'/forums/',
				'/r4l/',
				'/uploadedFiles/',
				'/blogs/',
				'/add_recipe/',
				'/delete_recipe/',
				'/Common/',
				'/edit_preview/',
				'/image_gallery/',
				'/add_favorite/',
				'/report_review/',
				'/remove_from_recipe_box/',
				'/save_to_recipe_box/',
				'/save_recipe_note/',
				'/save_rating/',
				'/add_to_cookbook/',
				'/base/',
				'/write_message/',
				'/doff_chef_hat/',
				'/edit/',
				'/edit_normalize/',
				'/giveaway2/*test*',
			);

			// Add extras
			if ($extraDisallow = BluApplication::getSetting('robotsDisallow')) {
				$extraDisallow = ($extraDisallow);
				$disallow = array_merge($disallow, $extraDisallow);
			}
		}

		// Output
		$this->_doc->setMimeType('text/plain');
		$this->_doc->setFormat('raw');

    // Please don't index our staging server, ok, thanks.
		if (defined('STAGING') && STAGING === true) {
			echo 'User-agent: *'."\r\n";
			echo 'Disallow: /'."\r\n";
		}
		else {
			echo "User-agent: Mediapartners-Google\r\n";
			echo "Disallow:\r\n";
			echo 'User-agent: *'."\r\n";
			echo 'Sitemap: http://www.recipe4living.com/xmlsitemap.xml'."\r\n";

			foreach ($disallow as $url) {
				echo 'Disallow: '.$url."\r\n";
			}
		}
	}
}
