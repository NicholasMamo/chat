<?php

include_once(realpath(dirname(__FILE__)) . "/../../files/FileManager.php");

$response = array("title"=>"", "content"=>""); // by default, the response is almost empty

if (isset($_GET["type"])) { // if the type was passed on
	$type = $_GET["type"]; // get the type
	if ($type == "users" && isset($_GET["recency"])) { // if the type of the area is the online users section and a recency parameter has been passed on
		$recency = $_GET["recency"]; // get the recency parameter
		$response["title"] = "WHO'S ONLINE?"; // set the title
		$response["content"] = FileManager::getActiveUsers($recency); // get the list of active users
	} else if ($type == "streams") { // if the type of the area is the streams section
		$response["title"] = "STREAMS"; // set the title
		$response["content"] = FileManager::getAllStreams(); // get all the streams
	}
}

echo json_encode($response); // return the JSON representation of the response

?>