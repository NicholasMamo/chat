<?php

const ENABLED = false; // a boolean indicating whether the cron job is enabled (true) or not (false)

ini_set('display_errors', 'On');
error_reporting(E_ALL);

if (ENABLED) { // if the cron job is enabled
	include_once(realpath(dirname(__FILE__)) . "/active.php"); // remove inactive users
	include_once(realpath(dirname(__FILE__)) . "/archive.php"); // remove old messages
	include_once(realpath(dirname(__FILE__)) . "/data.php"); // update the football data
	include_once(realpath(dirname(__FILE__)) . "/poster.php"); // check whether any new game topics should be published
	include_once(realpath(dirname(__FILE__)) . "/streams.php"); // update the stream list

	$time = time(); // get the current timestamp
	$date = new DateTime(); // create a new DateTime object
	$date->setTimestamp($time); // set its timestamp to the current date

	$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "N/A"; // check the referrer
	$agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "N/A"; // check the user agent

	file_put_contents(realpath(dirname(__FILE__)) . "/events.txt", $date->format("l, j F Y G:i") . " - Referer: $referer - Agent: $agent\n", FILE_APPEND); // log the request
}

?>