<?php

/*
 * Used in conjunction with t2.php and FileManager with a sleep function to test out mutex
 */

include_once(realpath(dirname(__FILE__)) . "/../FileManager.php");

/*if (FileManager::addActiveUser("test1")) {
	echo "test1 finished";
} else {
	echo "test1 could not finish";
}*/

if (FileManager::addMessage("test1", "t1")) {
	echo "test1 finished";
} else {
	echo "test1 could not finish";
}

?>