<?php

ini_set("display_errors", "On");
error_reporting(E_ALL);

include_once (realpath(dirname(__FILE__)) . "/../../database/CookieManager.php");
include_once (realpath(dirname(__FILE__)) . "/../../files/FileManager.php");

$username = CookieManager::getIdentifier(); // get the username of the logged in user
if (!FileManager::updateTimestamp($username)) { // if updating the user's timestamp does not work
	if (CookieManager::isLoggedIn()) { // if the user is logged in
		FileManager::addActiveUser($username, CookieManager::getToken()); // ensure the user is listed as active
	}
}	

?>