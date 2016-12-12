<?php

ini_set("display_errors", "On");
error_reporting(E_ALL);

include_once(realpath(dirname(__FILE__)) . "/../../database/phpbbDB.php");
include_once(realpath(dirname(__FILE__)) . "/../../files/FileManager.php");

if (isset($_POST["username"])) { // if the AJAX script was called correctly, a username should have been passed as a POST parameter
	$username = $_POST["username"]; // copy the username
	if ($username === "") { // basic textual validation - a username cannot be empty
		echo "Please enter a username";
	} else { // the path that checks whether the user is already online, or registerd on OLF
		if (FileManager::isActive($username)) { // if a user with the given username is already active in the chatroom
			echo "Username taken";
		} else if (phpbbDB::userExists($username)) { // if a user is not active in the chatroom, but exists as a member on OLF
			echo "User already exists";
		} else {
			echo "VALID";
		}
	}
} else {
	echo "Invalid request";
}
	
?>