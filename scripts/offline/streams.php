<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once(realpath(dirname(__FILE__)) . "/../other/StreamCrawler.php");
include_once(realpath(dirname(__FILE__)) . "/../../files/FileManager.php");

$webpages = array(
						array("source"=>"http://goatd.net", "keywords"=>array(
							"lyon")),
						array("source"=>"http://www.fromhot.com/", "keywords"=>array(
							"lyon")),
						array("source"=>"http://cricfree.tv/", "keywords"=>array(
							"lyon"))
						);

$streams = StreamCrawler::crawl($webpages); // get the streams
FileManager::setStreams($streams); // update the stream list

?>