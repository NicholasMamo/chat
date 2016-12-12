<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once(realpath(dirname(__FILE__)) . "/../../files/FileManager.php");
include_once(realpath(dirname(__FILE__)) . "/../../database/MessageDB.php");

const MAXAGE = 3 * 60 * 60; // the maximum number of seconds that messages should be saved for on disk

$messages = FileManager::getAllMessages(); // get all the messages
$id = -1; // the ID of the newest message that should be archived
$time = time(); // get the current timestamp

foreach($messages as $message) { // go through each message
	if ($time - $message["timestamp"] > MAXAGE) { // if the message is too old, it should be archived
		MessageDB::addMessage($message);
		$id = $message["id"]; // update the largest ID
	} else {
		break;
	}
}

FileManager::removeMessages($id); // remove the messages

$time = time();
$date = new DateTime();
$date->setTimestamp($time);

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "N/A";
$agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "N/A";

file_put_contents(realpath(dirname(__FILE__)) . "/archive_events.txt", $date->format("l, j F Y G:i") . " - Referer: $referer - Agent: $agent\n", FILE_APPEND);

?>