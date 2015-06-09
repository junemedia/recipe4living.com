<?php

/**
 * Breadcrumbs Helper
 *
 * Provides helper functions to display breadcrumb links
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class Breadcrumbs
{
	/**
	 * Stores an array of the links, in order
	 */
	private $_crumbs = array();

	/**
	 * Return the whole breadcrumb trail, in (X)HTML.
	 *
	 * @return string.
	 */
	public function get($linkLast = false, array $overrideSettings = array())
	{
		$html = '';

		// Get pathway
		$pathway = $this->_crumbs;

		// No crumbs, nothing to do
		if (empty($pathway)) {
			return $html;
		}

		// Get settings
		$settings = BluApplication::getSetting('breadCrumbs');
		if ($settings) {
			$settings = unserialize($settings);
		} else {
			$settings = array(
				'levels' => '10',
				'characters-each' => '100',
				'separator' => '>',
				'max-total-characters' => '200',
				'include-home' => true
			);
		}
		$settings = array_merge($settings, $overrideSettings);
		$sep = ' '.$settings['separator'].' ';

		// Add home link?
		if ($settings['include-home']) {
			if (!is_array($pathway)) {
				$pathway = array();
			}
			array_unshift($pathway, array(
				'text' => 'Home',
				'link' => '/'
			));
		}

		// Limit total number of items
		if (count($pathway) > (int)$settings['levels']) {
			$pathway = array_slice($pathway, 0, (int)$settings['levels']);
		}

		// Build output pathway
		$totalChars = 0;
		$outputPathway = array();
		$numItems = count($pathway);
		$i = 1;
		foreach ($pathway as $item) {

			// Store item title
			$item['title'] = $item['text'];

			// Check space available before we hit max char limit
			if ($settings['max-total-characters']) {
				$available = $settings['max-total-characters'] - $totalChars;
			} else {
				$available = false;
			}

			// Impose per-item char limit
			if (($i < $numItems) && $settings['characters-each']) {
				if ($available !== false) {
					$available = min($available, $settings['characters-each']);
				} else {
					$available = $settings['characters-each'];
				}
			}

			// Have a sensible amount of space to fill?
			if (($available !== false) && ($available < 5)) {
				break;
			}

			// Trim text
			if (($available !== false) && (strlen($item['text']) > $available)) {
				$item['text'] = substr($item['text'], 0, $available).'&#8230;';
			}

			// Add character length to total
			$totalChars += strlen($item['text']);

			// Add to output pathway
			$outputPathway[] = $item;

			$i++;
		}

		// Output pathway
		$numItems = count($outputPathway);
		$i = 1;
		foreach ($outputPathway as $item) {
			$text = $item['text'];
			$link = $item['link'];
			$title = $item['title'];

			if (($i < $numItems) || $linkLast) {
				$html.= '<a href="'.SITEURL.$link.'" title="'.htmlspecialchars($title).'">'.$text.'</a>';
				if ($i < $numItems) {
					$html .= $sep;
				}
			} else {
				$html.= '<span title="'.htmlspecialchars($title).'">'.$text.'</span>';
			}
			$i++;
		}

		return $html;
	}

	/**
	 * Add an extra breadcrumb link to the end of the breadcrumb.
	 *
	 * @args (string) text: the text to display
	 * @args (string) link: the URL for the breadcrumb link, *without* SITEURL.
	 */
	public function add($text, $link = null)
	{
		// Add to list.
		$this->_crumbs[] = array(
			'text' 	=> 	$text,
			'link'	=>	$link
		);

		// Exit
		return $this;
	}

}

?>