<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once(realpath(dirname(__FILE__)) . "/Connection.php");

/**
 * The class responsible for managing the parts of the database that deal with messaging
 */ 
abstract class MessageDB {
	
	/**
	 * Get the next message ID
	 * @return The next message ID
	 */
	public static function getNextID() {
		$con = Connection::getConnection(); // establish connection
		$query = "SELECT `message_id`
						FROM `pratos_messages`
						ORDER BY `message_id` DESC
						LIMIT 1";
		$result = mysqli_query($con, $query); // execute the query
		mysqli_close($con); // close the connection
		if ($result) { // if the query was successful
			if (mysqli_num_rows($result) > 0) { // if there are messages stored
				$row = mysqli_fetch_array($result); // fetch the row
				return $row["message_id"] + 1; // return the next ID
			} else {
				return 0; // return the default value
			}
		}
		return $result; // getting to this point means that the query failed
	}
	
	/**
	 * Add the given message to the database
	 * @param $message The message to be added to the database
	 * @return A boolean indicating whether the message was added to the database (true) or not (false)
	 */
	public static function addMessage($message) {
		$con = Connection::getConnection(); // establish connection
		$id = MessageDB::getNextID(); // get the next ID in the database
		$query = "INSERT INTO `pratos_messages`(`message_id`, `message_poster`, `message_content`, `message_timestamp`)
						VALUES ('$id', '" . MessageDB::DBReady($message["clean"]) . "', '" . MessageDB::DBReady($message["message"]) . "', '" . $message["timestamp"] . "')";
		$result = mysqli_query($con, $query); // execute the query
		mysqli_close($con); // close the connection
		return $result; // return a boolean indicating whether the message was added to the database or not
	}
	
	/**
	 * Manipulate the given string to make it compatible with SQL queries
	 * @param $string The string to manipulate
	 * @return The string, compatible with SQL queries
	 */
	public static function DBReady($string) {
		$string = htmlspecialchars($string, ENT_QUOTES); // ensure that the string may be used with SQL queries by encoding quotes
		$string = str_replace(array("\e", "\n", "\t", "\r", "\$", "\\"), array("\\e", "\\n", "\\t", "\\r", "\\$", "\\\\"), $string); // deal with escape sequenes
		return $string;
	}
	
}

?>