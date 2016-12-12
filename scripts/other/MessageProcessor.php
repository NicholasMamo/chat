<?php

ini_set("display_errors", "On");
error_reporting(E_ALL);

include_once (realpath(dirname(__FILE__)) . "/WebsiteManager.php");

/**
 * A class that is used to process messages
 */

abstract class MessageProcessor {
	
	/**
	 * The function that takes in a message, applies processing and returns the new message
	 * @param $message The initial message
	 * @return The processed message
	 */
	public static function process($message) {
		$message = MessageProcessor::processURL($message); // check for and upgrade URLs
		return $message; // return the processed message
	}
	
	/**
	 * Preprocess the URL to ensure that it is in the correct form
	 * @param $url The URL to be preprocessed
	 * @return The preprocessed URL
	 */
	public static function preprocessURL($url) {
		$pattern = "/\bhttps?:\/\/\b/"; // the first part of a URL should start with the access protocol
		if (!preg_match($pattern, $url)) { // if a valid access protocol is not defined
			$url = "http://" . $url; // add a default access protocol
		}
		return $url; // return the preprocessed URL
	}
	
	/**
	 * Check for URLs in the message and upgrade them
	 * @param $message The message where URLs will be sought
	 * @return The processed message
	 */
	public static function processURL($message) {
		$pattern = "/(https?:\/\/)?[a-z|A-Z|0-9|\-|\.]+\.(com|net|org|co\.uk|fr)(\/.*)?\/?/"; // the pattern to look for to identify strings
		preg_match_all($pattern, $message, $matches); // look for matches and store them in the matches array
		$matches = $matches[0]; // the first index is where matches are (the topmost level)
		foreach ($matches as $match) {
			$url = MessageProcessor::preprocessURL($match); // preprocess the URL if need be
			$pos = strpos($message, $match); // get the position of the occurrance
			$message = substr_replace($message, "<a alt = \"" . WebsiteManager::getDescription($url) . "\" href = \"" . $url . "\" target = \"_blank\">" . WebsiteManager::getTitle($url) . "</a>", $pos, strlen($match)); // upgrade the first URL found
		}
		return $message;
	}
	
}

?>