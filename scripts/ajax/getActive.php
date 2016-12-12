<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once(realpath(dirname(__FILE__)) . "/../../files/FileManager.php");

if (isset($_GET["recency"])) { // a correct call would have a recency parameter
	$recency = $_GET["recency"]; // get the recency value
	echo count(FileManager::getActiveUsers($recency)); // get the list of active users and return their count, minus the chatbot
}

?>