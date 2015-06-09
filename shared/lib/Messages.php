<?php

/**
 * Messages Helper
 *
 * Provides helper functions to display messages
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class Messages
{
	/**
	 * Add a message to the session stack
	 *
	 * @param string Message text
	 * @param string Message type
	 */
	public static function addMessage($text, $type = 'info', $location = 'default')
	{
		$_SESSION['messages'][$location][] = array('text' => $text, 'type' => $type);
	}

	/**
	 * Return the count of messages on the stack ready to be got and shown
	 *
	 * @return int Number of messages
	 */
	public static function countMessages($location = 'default')
	{
		return isset($_SESSION['messages'][$location]) ? count($_SESSION['messages'][$location]) : 0;
	}

	/**
	 * Get outstanding HTML formatted messages and clear stack
	 *
	 * @param int Page for which to create a link
	 * @param string Base URL to use for link
	 * @param int Current page
	 * @return string HTML formatted link/placeholder
	 */
	public static function getMessages($location = 'default')
	{
		$html = '';
		if (self::countMessages($location)) {
			while ($message = array_shift($_SESSION['messages'][$location])) {
				if (!trim($message['text'])){
					continue;
				} else if ($message['type'] == 'debug') {
					$html.= '<pre class="message">'.$message['text'].'</pre>';
				} else {
					$html.= '<div class="message message-'.$message['type'].'">'.$message['text'].'</div>';
				}
			}
		}
		return $html;
	}
}
?>