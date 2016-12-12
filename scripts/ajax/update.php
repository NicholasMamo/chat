<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once(realpath(dirname(__FILE__)) . "/../../files/FileManager.php");

$response = array("content"=>""); // by default, the response is almost empty

if (isset($_GET["type"])) { // if the type was passed on
	$type = $_GET["type"]; // get the type
	if ($type == "users") { // if the type of the area is the online users section
		if (isset($_GET["recency"])) { // if the activity threshold is set
			$recency = $_GET["recency"]; // get the threshold
			$response["content"] = FileManager::getActiveUsers($recency); // get the list of active users
		}
	} else if ($type == "streams") { // if the type of the area is the streams section
		$response["content"] = FileManager::getAllStreams(); // get all the streams
	}
}

echo json_encode($response); // return the JSON representation of the response

?>