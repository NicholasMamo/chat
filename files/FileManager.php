<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once(realpath(dirname(__FILE__)) . "/../database/phpbb.php");

/**
 * The class that is responsible of managing files
 */
abstract class FileManager {
	
	/**
	 * Active users
	 */
	
	/**
	 * Add an active user to the chatroom
	 * @param $username The user that will be marked as active in the chatroom
	 * @param $token The user's security token
	 * @return A boolean indicating whether the user can be marked as active (true) or not (false)
	 */
	public static function addActiveUser($username, $token) {
		$mutex = FileManager::getMutex("active.json");  // wait until mutex is obtained
		if ($mutex) { // if the mutex could be obtained
			$active = fopen(realpath(dirname(__FILE__)) . "/active.json", "r+"); // open the file containing a list of active people
			$activeUsers = fgets($active); // get the list of active users
			$users = json_decode($activeUsers, true); // decode the list of active users
			$clean = strtolower($username); // get the clean version of the username
			if (!isset($users[$clean]) && isset($users["chatbot"])) { // if the user is not already active, and the chatbot is in the list (which should always be the case unless the file is malformatted)
				$id = $users["chatbot"]["id"]++; // the new user will take the chatbot's ID, and the chatbot's ID will be incremented
				$users[$clean] = array("username" => $username, "id" => $id, "active" => time(), "token" => $token); // create an array with the user's information
			} else {
				return false;
			}
			rewind($active); // rewind the file so that any new data will override the previous data
			ftruncate($active, 0); // truncate (clear) the file
			fwrite($active, json_encode($users)); // write the JSON encoding of the users
			FileManager::releaseMutex($mutex); // release the mutex
			fclose($active); // close the file
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * set a list of active users to the chatroom
	 * @param $users The list of users that will be added as active to the chatroom
	 * @return A boolean indicating whether the users could be added to the chatroom (true) or not (false)
	 */
	public static function setActiveUsers($users) {
		$success = true; // a booleam indicating whether all users could be added or not
		$mutex = FileManager::getMutex("active.json");  // wait until mutex is obtained
		if ($mutex) { // if the mutex could be obtained
			$active = fopen(realpath(dirname(__FILE__)) . "/active.json", "r+"); // open the file containing a list of active people
			$activeUsers = FileManager::getAllUsers(); // get the list of users
			if (isset($activeUsers["chatbot"])) { // if the chatbot is in the list (which should always be the case unless the file is malformatted)
				$chatbot = $activeUsers["chatbot"]; // get the chatbot
				$activeUsers = array(); // reset the array of users
				$activeUsers["chatbot"] = $chatbot; // create an array of the chatbot
				foreach ($users as $clean => $user) { // go through each user
					if ($clean != "chatbot") { // if this is not the chatbot
						$clean = strtolower($user["username"]); // get the clean version of the username					
						$activeUsers[$clean] = $user; // add the user to the list
					}
				}
				rewind($active); // rewind the file so that any new data will override the previous data
				ftruncate($active, 0); // truncate (clear) the file
				fwrite($active, json_encode($activeUsers)); // write the JSON encoding of the users
			} else {
				$success = false;
			}
			FileManager::releaseMutex($mutex); // release the mutex
			fclose($active); // close the file
			return true;
		} else {
			return false;
		}
		return $success;
	}
	
	/**
	 * Update the user's timestamp
	 * @param $username The user whose timestamp will be updated
	 * @return A boolean indicating whether the user's timestamp could be updated (true) or not (false)
	 */
	public static function updateTimestamp($username) {
		$mutex = FileManager::getMutex("active.json");  // wait until mutex is obtained
		if ($mutex) { // if the mutex could be obtained
			$active = fopen(realpath(dirname(__FILE__)) . "/active.json", "r+"); // open the file containing a list of active people
			$activeUsers = fgets($active); // get the list of active users
			$users = json_decode($activeUsers, true); // decode the list of active users
			$clean = strtolower($username); // get the clean version of the username
			if (isset($users[$clean])) { // if the user is in the file
				$users[$clean]["active"] = time(); // update the user's timestamp
			} else {
				return false;
			}
			rewind($active); // rewind the file so that any new data will override the previous data
			ftruncate($active, 0); // truncate (clear) the file
			fwrite($active, json_encode($users)); // write the JSON encoding of the users
			FileManager::releaseMutex($mutex); // release the mutex
			fclose($active); // close the file
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Get all the users who were recently active in the chatroom
	 * @return All the users currently stored on the server
	 */
	public static function getAllUsers() {
		$users = fopen(realpath(dirname(__FILE__)) . "/active.json", "r+"); // open the file containing a list of users
		$chatUsers = fgets($users); // get the list of users
		$chatUsers = json_decode($chatUsers, true); // decode the list of messages
		fclose($users); // close the file
		return $chatUsers;
	}
	
	/**
	 * Get the list of active users
	 * @param $recency The activity threshold (in minutes)
	 * @return The list of active users
	 */
	public static function getActiveUsers($recency) {
		$active = array(); // the array of active users
		
		$users = FileManager::getAllUsers(); // get all the messages
		foreach ($users as $user) { // go through each user
			if ((time() - $user["active"]) / 60 < $recency) { // if the user was recently active
				array_push($active, $user); // add the user to the list of active users
			}
		}
		
		return $active;
	}
	
	/**
	 * Remove the user with the given username from the list of active users
	 * @param $username The username of the user who will be removed
	 * @return A boolean indicating whether the user could be removed (true) or not (false)
	 */
	public static function removeActiveUser($username) {
		$users = FileManager::getAllUsers(); // get all the users
		$clean = strtolower($username); // get the clean version of the username
		if (isset($users[$clean])) { // if the user exists
			unset($users[$clean]); // remove the user
		}
		return FileManager::setActiveUsers($users); // update the list of active users
	}
	
	/**
	 * Check whether the user with the given username is active in the chatroom
	 * @param $username The user whose activity will be checked
	 * @return A boolean indicating whether the user is active in the chatroom (true) or not (false)
	 */
	public static function isActive($username) {
		$active = fopen(realpath(dirname(__FILE__)) . "/active.json", "r+"); // open the file containing a list of active people
		$activeUsers = fgets($active); // get the list of active users
		$users = json_decode($activeUsers, true); // decode the list of active users
		$clean = strtolower($username); // get the clean version of the username
		$isActive = isset($users[$clean]); // check whether the user is active
		fclose($active); // close the file
		return $isActive;
	}
	
	/**
	 * Get the token of the user with the given username
	 * @param $username The user whose token will be retrieved
	 * @return The user's security token
	 */
	public static function getToken($username) {
		$active = fopen(realpath(dirname(__FILE__)) . "/active.json", "r+"); // open the file containing a list of active people
		$activeUsers = fgets($active); // get the list of active users
		$users = json_decode($activeUsers, true); // decode the list of active users
		$clean = strtolower($username); // get the clean version of the username
		$token = 0; // the default token
		if (isset($users[$clean])) { // if the user exists
			$token = $users[$clean]["token"]; // update the temporary token
		}
		fclose($active); // close the file
		return $token;
	}
	
	/**
	 * Messages
	 */
	
	/**
	 * Add a message to the chatroom
	 * @param $message The message that will be posted to the chatroom
	 * @param $username The user that posted the message
	 * @return A boolean indicating whether the message could be posted (true) or not (false)
	 */
	public static function addMessage($message, $username) {
		$mutex = FileManager::getMutex("messages.json");  // wait until mutex is obtained
		if ($mutex) { // if the mutex could be obtained
			$messages = fopen(realpath(dirname(__FILE__)) . "/messages.json", "r+"); // open the file containing a list of messages
			$chatMessages = fgets($messages); // get the list of messages
			$chatMessages = json_decode($chatMessages, true); // decode the list of messages
			$clean = strtolower($username); // get the clean version of the username
			array_push($chatMessages, array("clean"=>$clean, "message"=>$message, "timestamp"=>time(), "avatar"=>phpbb::getUserAvatar($clean), "id"=>FileManager::getNextID())); // add the message to the array of messages
			rewind($messages); // rewind the file so that any new data will override the previous data
			ftruncate($messages, 0); // truncate (clear) the file
			fwrite($messages, json_encode($chatMessages)); // write  the JSON encoding of the users
			FileManager::releaseMutex($mutex); // release the mutex
			fclose($messages); // close the file
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Set all the messages given as the file contents
	 * @param $chatMessages The messages to set
	 * @return A boolean indicating whether all the messages could be set (true) or not (false)
	 */
	public static function setMessages($chatMessages) {
		$mutex = FileManager::getMutex("messages.json");  // wait until mutex is obtained
		if ($mutex) { // if the mutex could be obtained
			$messages = fopen(realpath(dirname(__FILE__)) . "/messages.json", "w+"); // open the file containing a list of messages
			rewind($messages); // rewind the file so that any new data will override the previous data
			ftruncate($messages, 0); // truncate (clear) the file
			fwrite($messages, json_encode($chatMessages)); // write  the JSON encoding of the users
			FileManager::releaseMutex($mutex); // release the mutex
			fclose($messages); // close the file
			return true;
		}
		return true;
	}
	
	/**
	 * Get all the messages currently stored on the server (not database)
	 * @return All the messages currently stored on the server
	 */
	public static function getAllMessages() {
		$messages = fopen(realpath(dirname(__FILE__)) . "/messages.json", "r+"); // open the file containing a list of messages
		$chatMessages = fgets($messages); // get the list of messages
		$chatMessages = json_decode($chatMessages, true); // decode the list of messages
		fclose($messages); // close the file
		return $chatMessages;
	}
	
	/**
	 * Get all the messages currently stored on the server (not database) that have not been shown to the user yet
	 * @param $id The ID of the last message that was shown to the user
	 * @return All the messages currently stored on the server that are newer than the given ID
	 */
	public static function getMessages($id) {
		$new = array(); // the array of new messages
		$old = false; // a boolean indicating whether an old message has been found
		
		$messages = FileManager::getAllMessages(); // get all the messages
		$i = count($messages); // new messages are at the end
		while ($i-- > 0 && !$old) { // keep reading messages until no other messages remain, or an old message is found
			if ($messages[$i]["id"] > $id) { // if the message is new
				array_push($new, $messages[$i]); // add it to the array of new messages
			} else {
				$old = true; // mark the boolean as having found an old message
			}
		}
		return array_reverse($new); // reverse the array to retain messages in correct chronological order
	}
	
	/**
	 * Remove all messages with an ID equal to, or less than the one given
	 * @param $id The ID of the newest message that should be removed
	 * @return A boolean indicating whether the messages could be removed (true) or not (false)
	 */
	public static function removeMessages($id) {
		$retained = array(); // the array of retained messages
		$old = false; // a boolean indicating whether an old message has been found
		
		$messages = FileManager::getAllMessages(); // get all the messages
		$i = count($messages); // new messages are at the end
		while ($i-- > 0 && !$old) { // keep reading messages until no other messages remain, or an old message is found
			if ($messages[$i]["id"] > $id) { // if the message should be retained
				array_push($retained, $messages[$i]); // add it to the array of retained messages
			} else {
				$old = true; // mark the boolean as having found an old message
			}
		}
		return FileManager::setMessages(array_reverse($retained)); // reverse the array to retain messages in correct chronological order
	}
	
	/**
	 * Get the last message ID from the file
	 * @return The last message ID
	 */
	public static function getLastID() {
		$messages = fopen(realpath(dirname(__FILE__)) . "/messages.json", "r+"); // open the file containing a list of messages
		$chatMessages = fgets($messages); // get the list of messages
		$chatMessages = json_decode($chatMessages, true); // decode the list of messages
		fclose($messages); // close the file
		if (count($chatMessages) > 0) { // if there are chat messages
			return $chatMessages[count($chatMessages) - 1]["id"]; // get and return the last message ID
		} else {
			return -1; // return the default ID
		}
	}
	
	/**
	 * Get the next message ID from the file
	 * @return The next message ID
	 */
	public static function getNextID() {
		$messages = fopen(realpath(dirname(__FILE__)) . "/messages.json", "r+"); // open the file containing a list of messages
		$chatMessages = fgets($messages); // get the list of messages
		$chatMessages = json_decode($chatMessages, true); // decode the list of messages
		fclose($messages); // close the file
		if (count($chatMessages) > 0) { // if there are messages
			return $chatMessages[count($chatMessages) - 1]["id"]  + 1; // get the next the ID
		} else {
			return 0; // return the default value
		}
	}
	
	/**
	 * Streams
	 */
	
	/**
	 * Add a stream
	 * @param $stream The URL of the stream that will be added
	 * @return A boolean indicating whether the stream could be added (true) or not (false)
	 */
	public static function addStream($stream) {
		$mutex = FileManager::getMutex("streams.json");  // wait until mutex is obtained
		if ($mutex) { // if the mutex could be obtained
			$streams = fopen(realpath(dirname(__FILE__)) . "/streams.json", "r+"); // open the file containing a list of messages
			$streamList = fgets($streams); // get the list of messages
			$streamList = json_decode($streamList, true); // decode the list of messages
			array_push($streamList, array("url"=>$stream)); // add the stream to the end of the list of streams
			rewind($streams); // rewind the file so that any new data will override the previous data
			ftruncate($streams, 0); // truncate (clear) the file
			fwrite($streams, json_encode($streamList)); // write  the JSON encoding of the streams
			FileManager::releaseMutex($mutex); // release the mutex
			fclose($streams); // close the file
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Remove all existing streams with the new list of given streams
	 * @param $streamList The list of streams that will be added to the list of streams
	 * @return A boolean indicating whether the streams could be added (true) or not (false)
	 */
	public static function setStreams($streamList) {
		$mutex = FileManager::getMutex("streams.json");  // wait until mutex is obtained
		if ($mutex) { // if the mutex could be obtained
			$streams = fopen(realpath(dirname(__FILE__)) . "/streams.json", "w+"); // open the file containing a list of messages
			fwrite($streams, json_encode($streamList)); // write the JSON encoding of the streams
			FileManager::releaseMutex($mutex); // release the mutex
			fclose($streams); // close the file
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Get all the streams currently stored on the server (not database)
	 * @return All the streams currently stored on the server
	 */
	public static function getAllStreams() {
		$streams = fopen(realpath(dirname(__FILE__)) . "/streams.json", "r+"); // open the file containing a list of streams
		$streamList = fgets($streams); // get the list of streams
		$streamList = json_decode($streamList, true); // decode the list of streams
		fclose($streams); // close the file
		return $streamList;
	}
	
	/**
	 * General file functions
	 */

	 /**
	  * Get the name of the file without the extension
	  * @param $filename The filename
	  * @return The name of the file without the extension
	  */
	 public static function getName($filename) {
		 return substr($filename, 0, strpos($filename, "."));
	 }
	 
	 /**
	  * Get the file extension
	  * @param $filename The filename
	  * @return The file extension
	  */
	 public static function getExtension($filename) {
		 return substr($filename, strpos($filename, "."));
	 }
	 
	 /**
	  * Get mutual-exclusive access (mutex) on the given file
	  * @param $filename The filename
	  * @return The mutex obtained, or false if it cannot be created
	  */
	public static function getMutex($filename) {
		if (file_exists(realpath(dirname(__FILE__)) . "/$filename")) { // ensure that the file exists
			$name = FileManager::getName($filename); // get the name part of the filename
			$mutex = "$name.mutex";
			if (!file_exists(realpath(dirname(__FILE__)) . "/$mutex")) { // ensure that the mutex exists
				fclose(fopen(realpath(dirname(__FILE__)) . "/$mutex", "w")); // create the mutex file if it doesn't exist
			}
			$mutex = fopen(realpath(dirname(__FILE__)) . "/$mutex", "r+"); // open the mutex file in read mode
			while (!flock($mutex, LOCK_EX)) {}  // acquire an exclusive lock
			return $mutex;
		} else {
			return false;
		}
	}
	
	/**
	  * Release mutual-exclusive access (mutex) on the given file
	  * @param $mutex The mutex that will be released
	  * @return A boolean indicating whether the mutex could be released (true) or not (false)
	  */
	public static function releaseMutex($mutex) {
		if (file_exists(realpath(dirname(__FILE__)) . "/$mutex")) { // ensure that the file exists
			flock($mutex, LOCK_UN); // release the mutex
			fclose($mutex);
		} else {
			return false;
		}
	}
	
	/**
	 * Reset the file with the contents of another file
	 * @param $file The file whose contents will be reset
	 * @param $reset The file whose contents will be written into the original file
	 * @return A boolean indicating whether the file was reset (true) or not (false)
	 */
	public static function resetFile($file, $reset) {
		if (file_exists(realpath(dirname(__FILE__)) . "/$file") && file_exists(realpath(dirname(__FILE__)) . "/$reset"))  { // make sure that both files exist
			$file = fopen(realpath(dirname(__FILE__)) . "/$file", "w+"); // open the file that will be reset
			$reset = fopen(realpath(dirname(__FILE__)) . "/$reset", "r"); // open the file that contains the contents that will be written into the original file
			if ($file && $reset) { // if both files could be opened
				$content = ""; // initialize the content to be empty
				while ($line = fgets($reset)) { // read lines repeatedly until there is nothing more to read
					$content .= "$line\n"; // add the line to the content
				}
				fwrite($file, $content); // write the content to the original file
				fclose($file); // close the original file
				fclose($reset); // close the file
				return true;
			}
		} else {
			return false;
		}
	}
	
}

?>