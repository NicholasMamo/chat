<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once(realpath(dirname(__FILE__)) . "/Connection.php");

/**
 * The class responsible for managing the parts of the database that deal with football data aggregated from http://football-data.org/
 */ 
abstract class FootballDB {
	
	/*
	 * Teams
	 */
	
	/**
	 * Check whether a team with the given ID exists
	 * @param $id The ID of the team whose existence will be checked
	 * @return A boolean indicating whether a team with the given ID exists (true) or not (false)
	 */
	public static function teamExists($id) {		
		$con = Connection::getConnection(); // establish connection
		$query = "SELECT *
						FROM `pratos_teams`
						WHERE `team_id` = '$id'";
		$result = mysqli_query($con, $query); // execute the query
		mysqli_close($con); // close the connection
		if ($result) { // if the query was successful
			return mysqli_num_rows($result) > 0; // return a boolean indicating whether the team exists
		}
		return $result; // getting to this point means that the query failed
	}
	
	/**
	 * Add the given team to the database
	 * @param $team The team to be added to the database
	 * @return A boolean indicating whether the given team could be added to database (true) or not (false)
	 */
	public static function addTeam($team) {
		if (!FootballDB::teamExists($team["id"])) { // if the team does not already exist
			$con = Connection::getConnection(); // establish connection
			$query = "INSERT INTO `pratos_teams`(`team_id`, `team_name`, `team_code`, `team_short`, `team_crest`)
							VALUES ('" . $team["id"] . "', '" . $team["name"] . "', '" . $team["code"] . "', '" . $team["shortName"] . "', '" . $team["crestURL"] . "')";
			$result = mysqli_query($con, $query); // execute the query
			mysqli_close($con); // close the connection
			return $result; // return the result of the SQL query
		} else {
			return false;
		}
	}
	
	/**
	 * Get the team with the given ID
	 * @param $id The ID of the team that will be retrieved
	 * @return The team with the given ID, if it exists
	 */
	public static function getTeam($id) {
		if (FootballDB::teamExists($id)) { // if the team exists
			$con = Connection::getConnection(); // establish connection
			$query = "SELECT *
							FROM `pratos_teams`
							WHERE `team_id` = '$id'
							LIMIT 1";
			$result = mysqli_query($con, $query); // execute the query
			mysqli_close($con); // close the connection
			return mysqli_fetch_array($result); // return the team
		} else {
			return false;
		}
	}
	
	/*
	 * Fixtures
	 */
	
	/**
	 * Check whether a fixture with the given ID exists
	 * @param $id The ID of the fixture whose existence will be checked
	 * @return A boolean indicating whether a fixture with the given ID exists (true) or not (false)
	 */
	public static function fixtureExists($id) {		
		$con = Connection::getConnection(); // establish connection
		$query = "SELECT *
						FROM `pratos_fixtures`
						WHERE `fixture_id` = '$id'";
		$result = mysqli_query($con, $query); // execute the query
		mysqli_close($con); // close the connection
		if ($result) { // if the query was successful
			return mysqli_num_rows($result) > 0; // return a boolean indicating whether the fixture exists
		}
		return $result; // getting to this point means that the query failed
	}
	
	/**
	 * Add the given fixture to the database
	 * @param $fixture The fixture to be added to the database
	 * @return A boolean indicating whether the given fixture could be added to database (true) or not (false)
	 */
	public static function addFixture($fixture) {
		if (!FootballDB::fixtureExists($fixture["id"])) { // if the fixture does not already exist
			$con = Connection::getConnection(); // establish connection
			$query = "INSERT INTO `pratos_fixtures`(`fixture_id`, `fixture_home`, `fixture_away`, `fixture_home_goals`, `fixture_away_goals`, `fixture_timestamp`)
							VALUES ('" . $fixture["id"] . "', '" . $fixture["homeID"] . "', '" . $fixture["awayID"] . "', '" . $fixture["homeGoals"] . "', '" . $fixture["awayGoals"] . "', '" . $fixture["timestamp"] . "')";
			$result = mysqli_query($con, $query); // execute the query
			mysqli_close($con); // close the connection
			return $result; // return the result of the SQL query
		} else {
			FootballDB::updateFixture($fixture); // if the fixture already exists, simply update it
		}
	}
	
	/**
	 * Update the given fixture in the database if it exists
	 * @param $fixture The fixture to be updated in the database
	 * @return A boolean indicating whether the given fixture could be updated in the database (true) or not (false)
	 */
	public static function updateFixture($fixture) {
		if (FootballDB::fixtureExists($fixture["id"])) { // if the fixture exists
			$con = Connection::getConnection(); // establish connection
			$query = "UPDATE `pratos_fixtures`
							SET `fixture_home` = '" . $fixture["homeID"] . "', `fixture_away`= '" . $fixture["awayID"] . "', `fixture_home_goals` = '" . $fixture["homeGoals"] . "', `fixture_away_goals` = '" . $fixture["awayGoals"] . "', `fixture_timestamp` = '" . $fixture["timestamp"] . "'
							WHERE `fixture_id` = '" . $fixture["id"] . "'";
			$result = mysqli_query($con, $query); // execute the query
			mysqli_close($con); // close the connection
			return $result; // return the result of the SQL query
		} else {
			FootballDB::addFixture($fixture); // if the fixture does not already exist, simply add it
		}
	}
	
	/**
	 * Check whether a topic has been posted for the fixture with the given ID
	 * @param $id The ID of the fixture whose topic has to be checked
	 * @return A boolean indicating whether the given fixture has had its topic opened (true) or not (false)
	 */
	public static function getPosted($id) {
		if (FootballDB::fixtureExists($id)) { // if the fixture exists
			$con = Connection::getConnection(); // establish connection
			$query = "SELECT `fixture_topic_url`
							FROM `pratos_fixtures`
							WHERE `fixture_id` = '$id'
							LIMIT 1";
			$result = mysqli_query($con, $query); // execute the query
			mysqli_close($con); // close the connection
			$fixture = mysqli_fetch_array($result); // get the fixture's data
			return $fixture["fixture_topic_url"] != ""; // check whether the topic has been opened and return a boolean indicating this
		} else {
			return false;
		}
	}
	
	/**
	 * Mark the fixture with the given ID as posted, or not posted
	 * @param $id The ID of the fixture whose topic has been opened, or closed
	 * @param $posted A boolean indicating whether a topic has been opened for the fixture with the given ID (true) or not (false)
	 * @return A boolean indicating whether the given fixture could be updated in the database (true) or not (false)
	 */
	public static function setPosted($id, $url) {
		if (FootballDB::fixtureExists($id)) { // if the fixture exists
			$con = Connection::getConnection(); // establish connection
			$query = "UPDATE `pratos_fixtures`
							SET `fixture_topic_url` = '$url'
							WHERE `fixture_id` = '$id'";
			$result = mysqli_query($con, $query); // execute the query
			mysqli_close($con); // close the connection
			return $result; // return the result of the SQL query
		} else {
			return false;
		}
	}
	
	/**
	 * Get the next fixture from the database
	 * @return The next fixture, if any, that is set to be played
	 */
	public static function getNextFixture() {
		$con = Connection::getConnection(); // establish connection
		$query = "SELECT *
						FROM `pratos_fixtures`
						WHERE `fixture_timestamp` > '" . time() . "'
						ORDER BY `fixture_timestamp` ASC
						LIMIT 1";
		$result = mysqli_query($con, $query); // execute the query
		mysqli_close($con); // close the connection
		if (mysqli_num_rows($result) > 0) { // if there is an upcoming fixture
			return mysqli_fetch_array($result); // return the fixture
		} else {
			return false;
		}
	}
	
	/**
	 * Get the last fixture from the database
	 * @return The last fixture, if any, that was played
	 */
	public static function getLastFixture() {
		$con = Connection::getConnection(); // establish connection
		$query = "SELECT *
						FROM `pratos_fixtures`
						WHERE `fixture_timestamp` < '" . time() . "'
						ORDER BY `fixture_timestamp` DESC
						LIMIT 1";
		$result = mysqli_query($con, $query); // execute the query
		mysqli_close($con); // close the connection
		if (mysqli_num_rows($result) > 0) { // if there is a previous fixture
			return mysqli_fetch_array($result); // return the fixture
		} else {
			return false;
		}
	}
	
}

?>