<?php

include_once (realpath(dirname(__FILE__)) . "/../../files/FileManager.php");
include_once(realpath(dirname(__FILE__)) . "/../../database/MessageDB.php");

/*$messages = FileManager::getAllMessages(); // get all the messages
if (count($messages) > 100) { // if there are more than a hundred messages
	$new = array_slice($messages, -50); // retain just the last 50 messages
	if (time() - $new[0]["timestamp"] > 30) { // if the message is more than thirty seconds old
		foreach ($messages as $message) { // go through all messages
			if ($message["id"] < $new[0]["id"]) { // if the message should be removed
				MessageDB::addMessage($message); // add the message to the database
			}
		}
		FileManager::setMessages($new); // update the list of messages
	}
}*/

$messages = FileManager::getAllMessages();
echo count($messages);

?>