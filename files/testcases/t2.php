<?php

/*
 * Used in conjunction with t1.php and FileManager with a sleep function to test out mutex
 */

include_once(realpath(dirname(__FILE__)) . "/../FileManager.php");

/*if (FileManager::addActiveUser("test2")) {
	echo "test2 finished";
} else {
	echo "test2 could not finish";
}*/

if (FileManager::addMessage("test2", "t2")) {
	echo "test2 finished";
} else {
	echo "test2 could not finish";
}

?>