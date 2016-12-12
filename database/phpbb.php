<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once(realpath(dirname(__FILE__)) . "/phpbbDB.php");

/*
 * Connect to phpbB
 */
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : realpath(dirname(__FILE__)) . "/../../";
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.'.$phpEx);

$request->enable_super_globals(); // important to be able to access POST data

/**
 * The class that connects to the phpBB and retrieves data
 */
class phpbb {
	
	/**
	 * Create the connection class
	 */
	public function __construct() {
		global $user;
		global $auth;
		$user->session_begin(); // start session management
		$auth->acl($user->data);
	}
	
	/**
	 * Get the username of the user that is logged in
	 * @return The username of the user that is logged in
	 */
	public function getUsername() {
		global $user;
		return $user->data["username"];
	}
	
	/**
	 * Get the avatar of the user with the given username
	 * @param $username The username of the user whose avatar will be fetched
	 * @return The avatar of the user with the given username
	 */
	public static function getUserAvatar($username) {
		$data = phpbbDB::getAvatar($username); // get the data about the user's avatar
		$avatar = get_user_avatar($data["user_avatar"], $data["user_avatar_type"], $data["user_avatar_width"], $data["user_avatar_height"], "$username's avatar"); // get the uesr's avatar URL
		if ($data["user_avatar_type"] != "avatar.driver.remote") { // if the avatar is uploaded to OLF, then its path has to be modified
			$avatar = "<img src = \"http://lyon-forums.com" . substr($avatar, strpos($avatar, "/download/file.php?")); // fix the URL to work with a different subdirectory
		}
		$pattern = "/src\s*=\s*\"(.+?)\"/"; // the pattern used to match the URL
		preg_match($pattern, $avatar, $matches); // extract the URL and store it in an array
		return $matches[1]; // return the URL found
	}
	
}

?>