<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

const APITOKEN = "16419bbdd99f40edb31d5c576ee75143"; // the API token

/**
 * The class responsible for obtaining and parsing data from http://football-data.org/
 */
abstract class FootballData {
	
	/**
	 * Parse the fixtures from the given URL
	 * @param $url The URL from where the fixtures will be sought
	 * @return An array of fixtures as parsed from the given URL
	 */
	public static function parseFixtures($url) {
		$fixtureList = array(); // the array of fixtures
		
		$reqPrefs["http"]["method"] = "GET"; // the type of request
		$reqPrefs["http"]["header"] = "X-Auth-Token: " . APITOKEN; // the header data
		$stream_context = stream_context_create($reqPrefs); // create the stream context
		$response = file_get_contents($url, false, $stream_context); // get the contents of the URL
		$fixtures = json_decode($response, true); // decode the JSON response
		$fixtures = $fixtures["fixtures"]; // isolate the fixtures
		foreach ($fixtures as $fixture) { // go through each fixture
			$id = FootballData::getID($fixture["_links"]["self"]["href"]); // get the ID of the fixture
			$homeID = FootballData::getID($fixture["_links"]["homeTeam"]["href"]); // get the ID of the home team
			$awayID = FootballData::getID($fixture["_links"]["awayTeam"]["href"]); // get the ID of the away team
			$homeGoals = $fixture["result"]["goalsHomeTeam"]; // get the number of goals scored by the home team
			$awayGoals = $fixture["result"]["goalsAwayTeam"]; // get the number of goals scored by the awy team
			array_push($fixtureList, array("id" => $id, "homeID" => $homeID, "awayID" => $awayID, "homeGoals" => $homeGoals, "awayGoals" => $awayGoals, "timestamp" => FootballData::dateToTimestamp($fixture["date"]))); // construct the fixture data
		}
		return $fixtureList;
	}
	
	/**
	 * Parse the teams from the given URL
	 * @param $url The URL from where the teams will be sought
	 * @return An array of teams as parsed from the given URL
	 */
	public static function parseTeams($url) {
		$teamList = array(); // the array of fixtures
		
		$reqPrefs["http"]["method"] = "GET"; // the type of request
		$reqPrefs["http"]["header"] = "X-Auth-Token: " . APITOKEN; // the header data
		$stream_context = stream_context_create($reqPrefs); // create the stream context
		$response = file_get_contents($url, false, $stream_context); // get the contents of the URL
		$teams = json_decode($response, true); // decode the JSON response
		$teams = $teams["teams"]; // isolate the teams
		foreach ($teams as $team) { // go through each team
			$id = FootballData::getID($team["_links"]["self"]["href"]); // get the ID of the team
			array_push($teamList, array("id" => $id, "name" => $team["name"], "code" => $team["code"], "shortName" => $team["shortName"], "crestURL" => $team["crestUrl"])); // construct the team data
		}
		return $teamList; // return the team list
	}
	
	/**
	 * Get the ID from the given resource
	 * @param $resource The resource from where the ID will be extracted
	 * @return The ID extracted from the resource provided
	 */
	public static function getID($resource) {
		return substr($resource, strrpos($resource, "/") + 1);
	}
	
	/**
	 * Convert the given date string to a timestamp
	 * @param $date The date string to convert
	 * @return The timestamp (in seconds) of the date given
	 */
	public static function dateToTimestamp($date) {
		$date = new DateTime($date); // create a DateTime object
		return $date->getTimestamp(); // return the timestamp
	}
	
}

?>