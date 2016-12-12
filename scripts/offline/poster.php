<?php

const FORUMID = 52; // the forum where the post should be posted
const DAYS = 4; // the number of days before the game when the topic should be created

ini_set('display_errors', 'On');
error_reporting(E_ALL);

/*
 * Connect to phpbB
 */
if (!defined('IN_PHPBB')) {
	define('IN_PHPBB', true);
}
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : realpath(dirname(__FILE__)) . "/../../../";
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include_once($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_user.'.$phpEx);

global $user;
global $auth;
$user->session_begin(); // start session management

$auth->login("Pratos", "Memo1996", false); // log in as the chatbot
$auth->acl($user->data);
$user->setup();

include_once(realpath(dirname(__FILE__)) . "/../../database/FootballDB.php");

$next = FootballDB::getNextFixture(); // fetch the next game
if ($next && !FootballDB::getPosted($next["fixture_id"])) { // if there is an upcoming game and its topic has not been opened
	$timestamp = $next["fixture_timestamp"]; // get the game's timestamp
	if ($timestamp - time() < DAYS * 24 * 60 * 60) { // if the game is close
		$home = FootballDB::getTeam($next["fixture_home"]); // get the home team's information
		$away = FootballDB::getTeam($next["fixture_away"]); // get the away team's information
		
		$date = new DateTime(); // create the DateTime object
		$date->setTimestamp($timestamp); // set the date to be the game's date
		$date->setTimezone(new DateTimeZone("Europe/Paris")); // set the timezone
		
		$opening = new DateTime(); // create the DateTime object for the time when the chatroom will open
		$opening->setTimestamp($timestamp - 12 * 60 * 60); // set the opening time to be twelve hours before the game's date
		$opening->setTimezone(new DateTimeZone("Europe/Paris")); // set the timezone
		
		$subject = $home["team_name"] . " - " . $away["team_name"] . " (" . $date->format("l, j F Y G:i") . ")"; // create the subject of the topic
		$username = "Pratos"; // set the username of the poster
		$message = "[align=center][fimg=200, 200]" . $home["team_crest"] . "[/fimg][fimg=200, 200]" . $away["team_crest"] . "[/fimg]";
		$message .= "\n[size=150]" . $home["team_name"] . " - " . $away["team_name"] . "[/size]";
		$message .= "\n" . $date->format("l j F Y G:i") . " (Paris Time)";
		$message .= "\n\n[url=http://chat.lyon-forums.com]Chatroom open at " . $opening->format("G:i") . "[/url][/align]"; // create the message of the topic
		
		generate_text_for_storage($message, $uid, $bitfield, $flags, true); // generate the data required by PHPBB to post a new topic
		
		// set the data required to post a topic on PHPBB
		$data = array( 
			// General Posting Settings
			'forum_id'            => FORUMID,    // The forum ID in which the post will be placed. (int)
			'topic_id'            => 0,    // Post a new topic or in an existing one? Set to 0 to create a new one, if not, specify your topic ID here instead.
			'icon_id'            => false,    // The Icon ID in which the post will be displayed with on the viewforum, set to false for icon_id. (int)

			// Defining Post Options
			'enable_bbcode'    => true,    // Enable BBcode in this post. (bool)
			'enable_smilies'    => true,    // Enabe smilies in this post. (bool)
			'enable_urls'        => true,    // Enable self-parsing URL links in this post. (bool)
			'enable_sig'        => true,    // Enable the signature of the poster to be displayed in the post. (bool)

			// Message Body
			'message'            => $message,        // Your text you wish to have submitted. It should pass through generate_text_for_storage() before this. (string)
			'message_md5'    => md5($message),// The md5 hash of your message

			// Values from generate_text_for_storage()
			'bbcode_bitfield'    => $bitfield,    // Value created from the generate_text_for_storage() function.
			'bbcode_uid'        => $uid,        // Value created from the generate_text_for_storage() function.

			// Other Options
			'post_edit_locked'    => 0,        // Disallow post editing? 1 = Yes, 0 = No
			'topic_title'        => $subject,    // Subject/Title of the topic. (string)

			// Email Notification Settings
			'notify_set'        => false,        // (bool)
			'notify'            => false,        // (bool)
			'post_time'         => 0,        // Set a specific time, use 0 to let submit_post() take care of getting the proper time (int)
			'forum_name'        => '',        // For identifying the name of the forum in a notification email. (string)

			// Indexing
			'enable_indexing'    => true,        // Allow indexing the post? (bool)

			// 3.0.6
			'force_approved_state'    => true, // Allow the post to be submitted without going into unapproved queue

			// 3.1-dev, overwrites force_approve_state
			'force_visibility'            => true, // Allow the post to be submitted without going into unapproved queue, or make it be deleted
		);

		$url = submit_post("post",  $subject,  $username,  POST_NORMAL,  $poll,  $data); // submit the post
		FootballDB::setPosted($next["fixture_id"], $url); // mark the game as having its topic posted
	}
}

?>