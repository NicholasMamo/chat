<?php

ini_set("display_errors", "On");
error_reporting(E_ALL);

const MAX_TIME = 30; // the maximum waiting time for new messages

include_once (realpath(dirname(__FILE__)) . "/../../files/FileManager.php");
include_once (realpath(dirname(__FILE__)) . "/../other/MessageProcessor.php");

$timestamp = isset($_GET["timestamp"]) ? $_GET["timestamp"] : 0; // get the timestamp
$id = isset($_GET["id"]) ? $_GET["id"] : -1; // get the last message ID
$filename = realpath(dirname(__FILE__)) . "/../../files/messages.json"; // the name and path of the messages file
$time = time(); // the time spent on waiting

$currentmodif = filemtime($filename); // get the last time that the messages file was modified

$last = FileManager::getLastID(); // get the last ID in the file - even if the timestamp is the same for two messages, the order of IDs must be preserved

while ($id == $last && $currentmodif <= $timestamp && time() - $time < MAX_TIME) { // check if new messages have been added and the time limit has not been exceeded
	usleep(100000); // sleep 100ms to unload the CPU
	clearstatcache(); // clear any cached data about the file
	$currentmodif = filemtime($filename); // update the modification time of the filename
}

$response = array(); // a JSON array will be returned
$response["timestamp"] = $currentmodif; // update the timestamp

if (time() >= $time + MAX_TIME) { // if the time has expired
	$response["messages"] = array(); // don't waste time opening the messages file, but return an empty array
	$response["id"] = $id; // no messages were fetched
} else { // otherwise, the messages file has been updated
	$messages = FileManager::getMessages($id); // get newer messages
	for ($i = 0; $i < count($messages); $i++) { // go through each message
		$messages[$i]["message"] = MessageProcessor::process($messages[$i]["message"]); // process the message
	}
	$response["messages"] = $messages; // add the messages to the response
	if(count($messages) > 0) { // if messages were retrieved
		$response["id"] = $messages[count($messages) - 1]["id"]; // update the ID
	} else {
		$response["id"] = -1; // return the default ID
	}
}

echo json_encode($response); // echo the response as a JSON array
flush();

?>