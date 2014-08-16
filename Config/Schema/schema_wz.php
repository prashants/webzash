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

class WebzashSchema extends CakeSchema {

	public $file = 'schema_wz.php';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $wzaccounts = array(
		'id' => array('type' => 'integer', 'null' => false, 'length' => 11, 'key' => 'primary'),
		'label' => array('type' => 'string', 'null' => true, 'default' => 'null'),
		'db_datasource' => array('type' => 'string', 'null' => true),
		'db_hostname' => array('type' => 'string', 'null' => true),
		'db_port' => array('type' => 'integer', 'null' => true),
		'db_name' => array('type' => 'string', 'null' => true),
		'db_prefix' => array('type' => 'string', 'null' => true),
		'db_username' => array('type' => 'string', 'null' => true),
		'db_password' => array('type' => 'string', 'null' => true),
		'db_persistent' => array('type' => 'string', 'null' => true),
		'db_schema' => array('type' => 'string', 'null' => true),
		'db_unixsocket' => array('type' => 'string', 'null' => true),
		'db_settings' => array('type' => 'string', 'null' => true),
		'indexes' => array(

		),
		'tableParameters' => array()
	);

	public $wzsettings = array(
		'id' => array('type' => 'integer', 'null' => true, 'key' => 'primary'),
		'email_protocol' => array('type' => 'string', 'null' => true),
		'email_host' => array('type' => 'string', 'null' => true),
		'email_port' => array('type' => 'integer', 'null' => true),
		'email_username' => array('type' => 'string', 'null' => true),
		'email_password' => array('type' => 'string', 'null' => true),
		'drcr_toby' => array('type' => 'string', 'null' => true),
		'admin_verification' => array('type' => 'integer', 'null' => true, 'default' => 'null'),
		'email_verification' => array('type' => 'integer', 'null' => true),
		'user_registration' => array('type' => 'integer', 'null' => true, 'default' => 'null'),
		'email_from' => array('type' => 'string', 'null' => true),
		'sitename' => array('type' => 'string', 'null' => true),
		'indexes' => array(

		),
		'tableParameters' => array()
	);

	public $wzuseraccounts = array(
		'id' => array('type' => 'integer', 'null' => false, 'length' => 11, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => true),
		'account_id' => array('type' => 'integer', 'null' => true),
		'indexes' => array(

		),
		'tableParameters' => array()
	);

	public $wzusers = array(
		'id' => array('type' => 'integer', 'null' => false, 'length' => 11, 'key' => 'primary'),
		'username' => array('type' => 'string', 'null' => true, 'default' => 'null'),
		'password' => array('type' => 'string', 'null' => true, 'default' => 'null'),
		'email' => array('type' => 'string', 'null' => true, 'default' => 'null'),
		'role' => array('type' => 'string', 'null' => true, 'default' => 'null'),
		'status' => array('type' => 'integer', 'null' => true, 'default' => 'null'),
		'verification_key' => array('type' => 'string', 'null' => true, 'default' => 'null'),
		'fullname' => array('type' => 'string', 'null' => true, 'default' => 'null'),
		'email_verified' => array('type' => 'integer', 'null' => true),
		'admin_verified' => array('type' => 'integer', 'null' => true),
		'all_accounts' => array('type' => 'integer', 'null' => true),
		'indexes' => array(

		),
		'tableParameters' => array()
	);

}
