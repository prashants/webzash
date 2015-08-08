<?php
/**
 * The MIT License (MIT)
 *
 * Webzash - Easy to use web based double entry accounting software
 *
 * Copyright (c) 2014 Prashant Shah <pshah.mumbai@gmail.com>
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

define("JOOMLA_PATH", "FULL_FILESYSTEM_PATH_TO_JOOMLA_SETUP"); /* without trailing slash */
                                                                /* i.e. : "/var/www/joomla" */
define("JOOMLA_DB_HOSTNAME", "localhost");
define("JOOMLA_DB_NAME", "");
define("JOOMLA_DB_USERNAME", "");
define("JOOMLA_DB_PASSWORD", "");
define("JOOMLA_DB_USER_TABLE", "_users"); /* joomla database "user" table name */

define("JOOMLA_SITE_URL", "http://127.0.0.1");
define("JOOMLA_LOGIN_URL", "http://127.0.0.1");
define("JOOMLA_LOGOUT_URL", "http://127.0.0.1");

class JoomlaAuth {

        var $site_url = JOOMLA_SITE_URL;
        var $login_url = JOOMLA_LOGIN_URL;
        var $logout_url = JOOMLA_LOGOUT_URL;
        var $default_email = '';
        var $admin_username = '';

        public function checkPassword($username, $password) {
		/* Initialize joomla system */
		// define('_JEXEC', 1);
                //
		// define('JPATH_BASE', '');
                //
		// require_once(JPATH_BASE .DS.'includes'.DS.'defines.php');
		// require_once(JPATH_BASE .DS.'includes'.DS.'framework.php');
                //
		// JDEBUG ? $_PROFILER->mark('afterLoad') : null;
		// $mainframe = JFactory::getApplication('site');
		// $mainframe->initialise();
		// JPluginHelper::importPlugin('system');
		// JDEBUG ? $_PROFILER->mark('afterInitialise') : null;
		// $mainframe->triggerEvent('onAfterInitialise');
                //
		// $joomla_user = JFactory::getUser();
                //
                // return $joomla_user->username;

                /* Initialize joomla system */
                // define( '_JEXEC', 1 );
                // //define('JPATH_BASE', dirname(__FILE__) );//this is when we are in the root
                // define('JPATH_BASE', '');
                // //define( 'DS', DIRECTORY_SEPARATOR );
                //
                // require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
                // require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
                //
                // $mainframe =& JFactory::getApplication('site');
                // $mainframe->initialise();
                //
                // $joomla_user = JFactory::getUser();
                //
                // return $joomla_user->username;

                /* Directly connecting to Joomal database to verify password */
                $conn_str = "mysql:host=".JOOMLA_DB_HOSTNAME.";dbname=".JOOMLA_DB_NAME;

                try {
                        $conn = new PDO($conn_str, JOOMLA_DB_USERNAME, JOOMLA_DB_PASSWORD);
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch(PDOException $e) {
                        echo "Connection to CMS failed"; // $e->getMessage();
                        return false;
                }

                $stmt = $conn->prepare("SELECT id, name, username, password FROM " .
                        JOOMLA_DB_USER_TABLE . " WHERE username = :username LIMIT 1");

                $stmt->execute(array(':username' => $username));
                $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $row = $stmt->fetch();

                if (!$row) {
                        return false;
                }

                /* Include joomal password checker */
                include JOOMLA_PATH . '/libraries/phpass/PasswordHash.php';
                $phpass = new PasswordHash(10, true);
                $status = $phpass->CheckPassword($password, $row['password']);

                return $status;
        }

		public function getUserDetails() {
			return array('status' => FALSE);
		}
}
