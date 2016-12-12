<?php 

/**
 * The class that takes care of the connection with the database
 */
abstract class Connection {

	/**
	 * Get an active connection to the database
	 * @return An active connection to the database
	 */
	public static function getConnection(){
		$con = mysqli_connect('lyon-forums.com.mysql', 'lyon_forums_com', 'b4wpBqhR', 'lyon_forums_com'); // attempt to connect to the database

		if (mysqli_connect_errno($con)) {
			echo 'Failed to connect to MySQL: ' . mysqli_connect_error(); // show any resulting errors
		}
		return $con;
	}

}
	
?>
