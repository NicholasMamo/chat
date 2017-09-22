-- phpMyAdmin SQL Dump
-- version 3.5.8.1
-- http://www.phpmyadmin.net
--
-- Host: lyon-forums.com.mysql:3306
-- Generation Time: Jul 17, 2017 at 06:05 PM
-- Server version: 10.1.23-MariaDB-1~xenial
-- PHP Version: 5.4.45-0+deb7u8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `lyon_forums_com`
--
CREATE DATABASE `lyon_forums_com` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `lyon_forums_com`;

-- --------------------------------------------------------

--
-- Table structure for table `pratos_fixtures`
--

CREATE TABLE IF NOT EXISTS `pratos_fixtures` (
  `fixture_id` int(11) NOT NULL COMMENT 'The fixture''s ID',
  `fixture_home` int(11) NOT NULL COMMENT 'The ID of the home team',
  `fixture_away` int(11) NOT NULL COMMENT 'The ID of the away team',
  `fixture_home_goals` int(11) NOT NULL DEFAULT '-1' COMMENT 'The number of goals scored by the home team',
  `fixture_away_goals` int(11) NOT NULL DEFAULT '-1' COMMENT 'The number of goals scored by the away team',
  `fixture_timestamp` bigint(20) NOT NULL COMMENT 'The timestamp when the game should be played',
  `fixture_topic_url` varchar(1024) NOT NULL DEFAULT '' COMMENT 'A link to the PHPBB topic of the fixture',
  PRIMARY KEY (`fixture_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='The table storing data about fixtures';

-- --------------------------------------------------------

--
-- Table structure for table `pratos_messages`
--

CREATE TABLE IF NOT EXISTS `pratos_messages` (
  `message_id` int(11) NOT NULL COMMENT 'The message ID within the database, different from the JSON file',
  `message_poster` varchar(256) NOT NULL COMMENT 'The username of the user who posted the message',
  `message_content` text NOT NULL COMMENT 'The actual, plain text content of the message',
  `message_timestamp` bigint(20) NOT NULL COMMENT 'The UTC time when the message was posted in seconds',
  `is_spoiler` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'A boolean indicating whether the message is a spoiler (true) or not (false)',
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='The table of messages that were posted';

-- --------------------------------------------------------

--
-- Table structure for table `pratos_teams`
--

CREATE TABLE IF NOT EXISTS `pratos_teams` (
  `team_id` int(11) NOT NULL COMMENT 'The team''s ID',
  `team_name` varchar(256) NOT NULL COMMENT 'The team''s name',
  `team_code` varchar(8) NOT NULL COMMENT 'The team''s code',
  `team_short` varchar(128) NOT NULL COMMENT 'The team''s shorthand name',
  `team_crest` varchar(512) NOT NULL COMMENT 'The URL to the team''s crest',
  PRIMARY KEY (`team_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='The table storing data about teams';

-- --------------------------------------------------------

--
-- Table structure for table `predictor_log`
--

CREATE TABLE IF NOT EXISTS `predictor_log` (
  `eventID` int(11) NOT NULL,
  `typeID` int(11) NOT NULL,
  `resultID` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  PRIMARY KEY (`eventID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `predictor_log_event`
--

CREATE TABLE IF NOT EXISTS `predictor_log_event` (
  `eventID` int(11) NOT NULL,
  `eventDateTime` datetime NOT NULL,
  PRIMARY KEY (`eventID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `predictor_log_result`
--

CREATE TABLE IF NOT EXISTS `predictor_log_result` (
  `resultID` int(11) NOT NULL,
  `resultDescription` varchar(255) NOT NULL,
  PRIMARY KEY (`resultID`),
  UNIQUE KEY `resultDescription` (`resultDescription`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `predictor_log_type`
--

CREATE TABLE IF NOT EXISTS `predictor_log_type` (
  `typeID` int(11) NOT NULL,
  `typeDescription` varchar(255) NOT NULL,
  PRIMARY KEY (`typeID`),
  UNIQUE KEY `typeDescription` (`typeDescription`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `predictor_match`
--

CREATE TABLE IF NOT EXISTS `predictor_match` (
  `matchID` int(11) NOT NULL,
  `homeTeamID` int(11) NOT NULL,
  `awayTeamID` int(11) NOT NULL,
  `homeTeamScore` int(11) NOT NULL,
  `awayTeamScore` int(11) NOT NULL,
  `matchDate` datetime NOT NULL,
  PRIMARY KEY (`matchID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `predictor_matchday`
--

CREATE TABLE IF NOT EXISTS `predictor_matchday` (
  `matchdayID` int(11) NOT NULL,
  PRIMARY KEY (`matchdayID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `predictor_matchday_match (matchdayID, matchID)`
--

CREATE TABLE IF NOT EXISTS `predictor_matchday_match (matchdayID, matchID)` (
  `matchdayID` int(11) NOT NULL,
  `matchID` int(11) NOT NULL,
  UNIQUE KEY `matchID` (`matchID`),
  UNIQUE KEY `matchID_2` (`matchID`),
  UNIQUE KEY `matchID_3` (`matchID`),
  UNIQUE KEY `matchID_4` (`matchID`),
  UNIQUE KEY `matchID_5` (`matchID`),
  UNIQUE KEY `matchID_6` (`matchID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `predictor_prediction`
--

CREATE TABLE IF NOT EXISTS `predictor_prediction` (
  `predictionID` int(11) NOT NULL,
  `predictionHome` int(11) NOT NULL,
  `predictionAway` int(11) NOT NULL,
  `score` int(11) DEFAULT NULL,
  PRIMARY KEY (`predictionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `predictor_prediction_match`
--

CREATE TABLE IF NOT EXISTS `predictor_prediction_match` (
  `predictionID` int(11) NOT NULL,
  `matchID` int(11) NOT NULL,
  `username` varchar(99) NOT NULL,
  UNIQUE KEY `predictionID` (`predictionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `predictor_prediction_user`
--

CREATE TABLE IF NOT EXISTS `predictor_prediction_user` (
  `predictionID` int(11) NOT NULL,
  `username` int(11) NOT NULL,
  UNIQUE KEY `predictionID` (`predictionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `predictor_season`
--

CREATE TABLE IF NOT EXISTS `predictor_season` (
  `seasonYear` int(11) NOT NULL,
  PRIMARY KEY (`seasonYear`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `predictor_season_match`
--

CREATE TABLE IF NOT EXISTS `predictor_season_match` (
  `seasonID` int(11) NOT NULL,
  `matchday` int(11) NOT NULL,
  `matchID` int(11) NOT NULL,
  UNIQUE KEY `matchdayID` (`matchID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `predictor_team`
--

CREATE TABLE IF NOT EXISTS `predictor_team` (
  `teamID` int(11) NOT NULL,
  `teamName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `teamCrest` text CHARACTER SET latin1 NOT NULL,
  `teamAltName` varchar(255) CHARACTER SET latin1 NOT NULL,
  `teamStadium` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '""',
  `teamColor` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT '000,000,000',
  PRIMARY KEY (`teamID`),
  UNIQUE KEY `teamName` (`teamName`),
  UNIQUE KEY `teamAltName` (`teamAltName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `predictor_user`
--

CREATE TABLE IF NOT EXISTS `predictor_user` (
  `username` varchar(255) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
