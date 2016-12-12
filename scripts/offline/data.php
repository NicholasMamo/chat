<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once(realpath(dirname(__FILE__)) . "/../../database/FootballDB.php");
include_once(realpath(dirname(__FILE__)) . "/../other/FootballData.php");

$teams = FootballData::parseTeams("http://api.football-data.org/v1/competitions/434/teams"); // get the teams
foreach ($teams as $team) { // go through each team
	FootballDB::addTeam($team); // add (or update) the team
}

$fixtures = FootballData::parseFixtures("http://api.football-data.org/v1/teams/523/fixtures"); // get the fixtures
foreach ($fixtures as $fixture) { // go through each fixture
	FootballDB::addFixture($fixture); // add (or update) the fixture
}

?>