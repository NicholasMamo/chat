<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once(realpath(dirname(__FILE__)) . "/Connection.php");

/**
 * The class that connects to the phpBB database
 */
abstract class phpbbDB {
	
	/*
	 * Insert a new user into the database
	 * @param $username The new user to be added into the databse
	 * @return A boolean indicating whether the user exists (true) or not (false)
	 */
	public static function userExists($username) {
		$con = Connection::getConnection(); // establish connection
		$clean = strtolower($username); // get the clean version of the username
		$query = "SELECT *
						FROM `phpbb_users`
						WHERE `username_clean` = '$clean'";
		$result = mysqli_query($con, $query); // execute the query
		mysqli_close($con); // close the connection
		if ($result) { // if the query was successful
			return mysqli_num_rows($result) > 0; // a user with the given username exists if the query yields one or more rows
		}
		return $result; // getting to this point means that the query failed
	}
	
	/*
	 * Get a user's avatar from the database
	 * @param $username The user whose avatar will be fetched
	 * @return Data about the user's avatar
	 */
	public static function getAvatar($username) {
		$con = Connection::getConnection(); // establish connection
		$clean = strtolower($username); // get the clean version of the username
		$query = "SELECT `user_avatar`, `user_avatar_type`, `user_avatar_width`, `user_avatar_height`
						FROM `phpbb_users`
						WHERE `username_clean` = '$clean'";
		$result = mysqli_query($con, $query); // execute the query
		mysqli_close($con); // close the connection
		if ($result) { // if the query was successful
			return mysqli_fetch_array($result); // return the first row
		}
		return $result; // getting to this point means that the query failed
	}
	
}

?>