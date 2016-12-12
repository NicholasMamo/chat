<?php

const DURATION = 150; // the duration (minutes) for which the chatroom should remain open
const ADVANCE = 60 * 12; // the number of minutes the chatroom should open before a game

const FORCEOPEN = false; // a boolean indicating whether the chatroom is forcibly-open (true) or not (false)
/*
 * Make sure that the chatroom is always operating from http://chat.lyon-forums.com/, and not from http://lyon-forums.com/chat as it could interfere with AJAX script-calling
 */
if ($_SERVER['HTTP_HOST'] != "chat.lyon-forums.com") {
	header("Location: http://chat.lyon-forums.com");
}

ini_set("display_errors", "On");
error_reporting(E_ALL);

include_once(realpath(dirname(__FILE__)) . "/database/phpbb.php");
include_once(realpath(dirname(__FILE__)) . "/database/CookieManager.php");
include_once(realpath(dirname(__FILE__)) . "/database/FootballDB.php");
include_once(realpath(dirname(__FILE__)) . "/files/FileManager.php");

$next = FootballDB::getNextFixture(); // get the next fixture
$last = FootballDB::getLastFixture(); // get the last fixture

$stillOpen = false; // a boolean indicating whether the chatroom is still open because of an ongoing fixture (the last fixture) (true) or not (false)
$toOpen = false; // a boolean indicating whether the chatroom is open because of the next fixture starts soon (true) or not (false)
$nextTimestamp = 0; // the timestamp of the next fixture
$lastTimestamp = 0; // the timestamp of the last fixture
$time = time(); // get the current timestamp

if ($next) { // if there is a next fixture
	$nextTimestamp = htmlspecialchars_decode($next["fixture_timestamp"]); // update the timestamp of the next fixture
	$url = $next["fixture_topic_url"]; // get the fixture URL
	$prefix = "/customers/4/c/8/lyon-forums.com/httpd.www/"; // the prefix of the URL returned by PHPBB
	$url = "http://lyon-forums.com/" . substr($url, strlen($prefix));
}

if ($last) { // if there is a last fixture
	$lastTimestamp = $last["fixture_timestamp"]; // update the timestamp of the last fixture
}

$stillOpen = ($time - $lastTimestamp)/60 < DURATION; // the chatroom should be open if the previous game hasn't finished yet
$toOpen = ($nextTimestamp - $time)/60 < ADVANCE; // the chatroom should be open if the next game starts soon

if ($stillOpen || $toOpen || FORCEOPEN) { // if there is a scenario such that the chatroom should open
	$request->enable_super_globals(); // important to be able to access POST data
	$phpbb = new phpbb();
	$username = $phpbb->getUsername(); // get the username of the logged-in user at http://lyon-forums.com/
	if ($username != "Anonymous" && !CookieManager::isLoggedIn()) { // if the user has not logged in to the chat room, but is logged in to OLF
		$token = CookieManager::setCookie($username); // set the cookie to the retrieved username and retrieve the security token
		FileManager::addActiveUser($username, $token); // set the user as active
		header("Location: http://chat.lyon-forums.com"); // refresh the page for the cookie to come into effect
	} else if (isset($_POST["username"])) { // if the user has chosen a username
		$username = $_POST["username"]; // retrieve the username
		$token = CookieManager::setCookie($username); // set the cookie to the retrieved username and retrieve the security token
		FileManager::addActiveUser($username, $token); // set the user as active
		header("Location: http://chat.lyon-forums.com");  // refresh the page for the cookie to come into effect
	}

	if (CookieManager::isLoggedIn() && FileManager::isActive(CookieManager::getIdentifier()) && CookieManager::checkToken(FileManager::getToken(CookieManager::getIdentifier()))) { // if the user is logged in, both with cookie session data and as an active user
		include_once(realpath(dirname(__FILE__)) . "/chat.php"); // show the chatroom
	} else {
		if (CookieManager::isLoggedIn()) { // if the user is logged in and it shows in the cookie
			FileManager::removeActiveUser(CookieManager::getIdentifier()); // remove the user from the list of active users if necessary
			CookieManager::logout(); // log out from the cookie
			header("Location: http://chat.lyon-forums.com");  // refresh the page for the cookie to come into effect
		}
		include_once(realpath(dirname(__FILE__)) . "/login.php"); // otherwise show the login page
	}	
} else {
	include_once(realpath(dirname(__FILE__)) . "/closed.php"); // otherwise show the login page
}

?>