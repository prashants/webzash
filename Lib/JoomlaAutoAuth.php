<?php
/**
 * The MIT License (MIT)
 *
 * Webzash - Easy to use web based double entry accounting software
 *
 * Copyright (c) 2015 Keith Neely <keithn@kfnwebsolutions.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/* JOOMLA DB SETTINGS */
define("JOOMLA_DB_HOSTNAME", "localhost");
define("JOOMLA_DB_NAME", "");
define("JOOMLA_DB_USERNAME", "");
define("JOOMLA_DB_PASSWORD", "");
define("JOOMLA_DB_PREFIX", "");		/* joomla database prefix, INCLUDE UNDERSCORE (_) */

define("JOOMLA_AllAccounts", 1);	/* Set to 1 to enable Joomla created accounts access to all accounts in Webzash */
define("JOOMLA_Role", "guest");		/* Default user role for Joomla created accounts */
define("JOOMLA_TZ", "UTC");			/* Default timezone for Joomla created accounts */

/* JOOMLA URLS */
define("JOOMLA_LOGOUT_URL", "");	/* http://www.example.com/index.php?option=com_users&task=logout */
define("JOOMLA_LOGIN_URL", "");		/* http://www.example.com/index.php?option=com_users&task=login */


class JoomlaAutoAuth {

	public $logout_url = JOOMLA_LOGOUT_URL;
	public $login_url = JOOMLA_LOGIN_URL;

	public $username;
	public $fullname;
	public $email;

	public function checkPassword($username, $password) {
		$Link = mysqli_connect(JOOMLA_DB_HOSTNAME, JOOMLA_DB_USERNAME, JOOMLA_DB_PASSWORD, JOOMLA_DB_NAME);
		if (!$Link){
			echo "Unable to connect to Joomla DB";
			return false;
		}

		$JUsername = "";
		$SQL_GetSessions = "SELECT * FROM `" . JOOMLA_DB_PREFIX . "session`";
		$R_GetSessions = mysqli_query($Link, $SQL_GetSessions);
		while ($row = mysqli_fetch_assoc($R_GetSessions))
		{
			foreach ($_COOKIE as $Key => $C){
				if ($row['session_id'] == $C){
					/* found session, valid user login */
					$JUsername = $row['username'];
					break 2;
				}
			}
		}

		if ($JUsername == ""){
			header("location: " . JOOMLA_LOGIN_URL);
		}

		$SQL_GetUser = "SELECT * FROM `" . JOOMLA_DB_PREFIX . "users` WHERE `username`='$JUsername' LIMIT 1;";
		$R_GetUser = mysqli_query($Link, $SQL_GetUser);
		if (mysqli_num_rows($R_GetUser) != 1){
			echo "User not found.";
			return false;
		}
		$JUser = mysqli_fetch_assoc($R_GetUser);
		if (!$JUser){
			echo "Invalid Joomal user";
			return false;
		}

		/* Setup tpauth object variables */
		$username = $JUsername;
		$fullname = $JUser['name'];
		$email = $JUser['email'];

		/*
		if (false) {
			echo 'You are logged in as:<br />';
			echo 'User name: ' . $user->username . '<br />';
			echo 'Real name: ' . $user->name . '<br />';
			echo 'User ID  : ' . $user->id . '<br />';

			return array('username'=>'admin');
		} else{
			echo "Not logged in.";
			return false;
		}
		*/

		return true;
	}

	public function getUserDetails() {
		return array(
			'status' => TRUE,
			'username' => $username,
			'fullname' => $fullname,
			'email' => $email,
		);
	}
}
?>
