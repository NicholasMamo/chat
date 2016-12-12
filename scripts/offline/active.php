<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once(realpath(dirname(__FILE__)) . "/../../files/FileManager.php");
include_once(realpath(dirname(__FILE__)) . "/../../database/MessageDB.php");

const MAXACTIVE = 5 * 60; // the maximum number of minutes that users should be retained in the list of active files

$active = FileManager::getActiveUsers(MAXACTIVE); // get the list of active users

FileManager::setActiveUsers($active); // update the set of active users

?>