<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once(realpath(dirname(__FILE__)) . "/WebsiteManager.php");

/**
 * The class that is responsible for crawling webpages, looking for streams
 */
abstract class StreamCrawler {
	
	/**
	 * Go through the webpages, and look for the streams associated with the keywords stored with each webpage
	 * @param @webpages The array of webpages and keywords used to retrieve a list of streams
	 * @return A list of streams found in the given webpages with the 
	 */
	public static function crawl_deprecated($webpages) {
		$streams = array(); // the array of streams
		foreach ($webpages as $webpage) { // go through each webpage
			$source = $webpage["source"]; // get the source
			$document = file_get_contents($source); // get the content of the webpage
			$keywords = $webpage["keywords"]; // get the keywords for the webpage
			foreach ($keywords as $keyword) { // go through each keyword
				$keyword = strtolower($keyword); // get the lower case of the keyword
				$pattern = "/<a.*?href\s*?=\s*?\"(.+?)\".*?>.*$keyword.*<\/a>/i"; // the pattern to look for in the webpage
				preg_match_all($pattern, $document, $matches); // find all matches
				$links = $matches[1]; // isolate the streams
				foreach ($links as $link) { // go through each stream
					array_push($streams, array("source"=>$source, "stream"=>$link)); // add the link to the array of streams
				}
			}
		}
		return $streams;
	}
	
	/**
	 * Go through the webpages, and look for the streams associated with the keywords stored with each webpage
	 * @param @webpages The array of webpages and keywords used to retrieve a list of streams
	 * @return A list of streams found in the given webpages with the 
	 */
	public static function crawl($webpages) {
		$streams = array(); // the array of streams
		foreach ($webpages as $webpage) { // go through each webpage
			$source = $webpage["source"]; // get the source
			$keywords = $webpage["keywords"]; // get the keywords for the webpage
			$doc = new DOMDocument(); // create a DOM document
			@$doc->loadHTMLFile($source); // load the document's HTML contents
			$links = $doc->getElementsByTagName("a"); // fetch the links in the document
			foreach($links as $link) { // go through each link
				foreach ($keywords as $keyword) { // go through each keyword
					$pattern = "/<a.*?href\s*?=\s*?\"(.+?)\".*?>.*$keyword.*<\/a>/i"; // the pattern to look for in the webpage
					preg_match_all($pattern, WebsiteManager::outerHTML($link), $matches); // find all matches
					if (count($matches) > 0) {
						$linkMatches = $matches[1]; // isolate the streams
						foreach ($linkMatches as $linkMatch) { // go through each stream
							array_push($streams, array("source"=>$source, "stream"=>WebsiteManager::getAbsoluteURL($linkMatch, $source))); // add the link to the array of streams
						}
					}
				}
			}
		}
		return $streams;
	}
	
}

?>