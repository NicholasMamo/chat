<?php

ini_set("display_errors", "On");
error_reporting(E_ALL);

/**
 * The class that manages cookies
 */
abstract class CookieManager {
	
	/**
	 * Create the cookie with the given identifier
	 * @param $identifier The identifier that will be stored in the cookie
	 * @return The security token generated
	 */
	public static function setCookie($identifier) {
		$expire = time()+60*60*24; // cookie session length is one day
		$token = rand (0, 1000000); // generate a token for the user
		
		$cookie = array(); // create the cookie
		$cookie["identifier"] = $identifier; // set the identifier
		$cookie["token"] = $token; // set the token
		//if (CookieManager::cookiesAllowed()) {
		if (true) {
			setcookie("login", json_encode($cookie), $expire, "/"); // create the cookie and make it applicable in the whole website
		}
		return $token;
	}
	
	/**
	 * Check whether the user is logged in or not
	 * @return A boolean indicating whether the user is logged in (true) or not (false)
	 */
	public static function isLoggedIn() {
		return isset($_COOKIE["login"]);
	}
	
	/**
	 * Log out from the website, effectively removing the cookie
	 */
	public static function logout() {
		$expire = time(); // cookie session length is instantaneous
		setcookie("login", "", $expire, "/"); // create the cookie, which will be destroyed right away
	}
	
	/**
	 * Get the identifier that is stored in the cookie
	 * @return The identifier that is stored in the cookie
	 */
	public static function getIdentifier() {
		if (CookieManager::isLoggedIn()) { // check whether the user is logged in at all
			$cookie = json_decode($_COOKIE["login"], true); // retrieve and decode the contents of the cookie
			return $cookie["identifier"];
		} else {
			return "";
		}
	}
	
	/**
	 * Get the token that is stored in the cookie
	 * @return The token that is stored in the cookie
	 */
	public static function getToken() {
		if (CookieManager::isLoggedIn()) { // check whether the user is logged in at all
			$cookie = json_decode($_COOKIE["login"], true); // retrieve and decode the contents of the cookie
			return $cookie["token"];
		} else {
			return 0;
		}
	}
	
	/**
	 * Check whether the correct token is correct
	 * @param $token The token to be checked
	 * @return A boolean indicating whether the cookie token and the token provided match (true) or not (false)
	 */
	public static function checkToken($token) {
		return $token == CookieManager::getToken();
	}
	
	/**
	 * Check whether the user has authorized cookies on his browser for this website
	 * @return A boolean indicating whether the user has authorized cookies for this website (true) or not (false)
	 */
	public static function cookiesAllowed() {
		return isset($_COOKIE["cookies_allowed"]);
	}
	
	/**
	 * Create a cookie indicating whether the user has authorized cookies for this website
	 * @parameter $allowed A boolean indicating whether the user has authorized cookies for this website
	 */
	public static function setCookiesAllowed($allowed) {
		if ($allowed) { // if cookies are authorized
			$expire = time()+60*60*24*365; // cookie session length is one year
			setcookie("cookies_allowed", true, $expire, "/"); // create the cookie
		} else {
			$expire = time(); // cookie session length is instantaneous
			setcookie("cookies_allowed", true, $expire, "/"); // create the cookie, which will be destroyed right away
		}
	}

}

?>