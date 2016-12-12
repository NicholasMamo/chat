<?php

ini_set("display_errors", "On");
error_reporting(E_ALL);

/**
 * A class that is used to fetch website data
 */
 
abstract class WebsiteManager {
	
	/**
	 * Get the title of the website
	 * @param $url The URL of the webpage whose title will be fetched
	 * @return The title of the webpage, if available, or a default value if not
	 */
	public static function getTitle($url) {
		$doc = new DOMDocument(); // create a DOM document
		@$doc->loadHTMLFile($url); // load the document's HTML contents
		$title = $doc->getElementsByTagName("title"); // fetch the title tags in the document
		if ($title->length > 0) { // if there are title tags
			return $title[0]->nodeValue; //return the first one's content
		} else {
			return $url; // otherwise default to the URL
		}
	}
	
	/**
	 * Get the description of the website
	 * @param $url The URL of the webpage whose description will be fetched
	 * @return The description of the webpage, if available, or a default value if not
	 */
	public static function getDescription($url) {
		$meta = @get_meta_tags($url); // get all the metadata from the URL
		if (isset($meta["description"])) { // check whether a title is specified in the document
			return $meta["description"]; // if it is, return the title
		} else {
			return $url; // otherwise default to the URL
		}
	}
	
	/**
	 * Check whether the given URL is indeed absolute, and if not, return the absolute URL
	 * @param $url The URL to be checked
	 * @param $base The base website, to be used in case the URL provided is a relative one
	 * @return The absolute URL from the base and URL given
	 */
	public static function getAbsoluteURL($url, $base) {
		$pattern = "/\bhttps?:\/\/\b/"; // the first part of a URL should start with the access protocol
		if (!preg_match($pattern, $url)) { // if a valid access protocol is not defined
			$url = "$base/$url"; // add the base URL
		}
		return $url; // return the preprocessed URL
	}
	
	/**
	 * Get the outer HTML of the element provided
	 * @param $element The element whose outer HTML will be returned
	 * @return The outer HTML of the element provide
	 */
	public static function outerHTML($element) {
		$doc = new DOMDocument();
		$doc->appendChild($doc->importNode($element, true));
		return $doc->saveHTML();
	}
	
}

?>