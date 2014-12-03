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
		'id' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'length' => 11, 'key' => 'primary'),
		'label' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'db_datasource' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'db_database' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'db_host' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'db_port' => array('type' => 'integer', 'null' => true,
			'unsigned' => true, 'default' => 'null', 'length' => 11),
		'db_login' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'db_password' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'db_prefix' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'db_persistent' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'db_schema' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'db_unixsocket' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'db_settings' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'db_ssl_key' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'db_ssl_cert' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'db_ssl_ca' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8',
			'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB'
		)
	);

	public $wzsettings = array(
		'id' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'length' => 11, 'key' => 'primary'),
		'sitename' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'drcr_toby' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'enable_logging' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'default' => '0', 'length' => 1),
		'row_count' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'default' => '10', 'length' => 11),
		'user_registration' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'default' => '0', 'length' => 1),
		'admin_verification' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'default' => '0', 'length' => 1),
		'email_verification' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'default' => '0', 'length' => 1),
		'email_protocol' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'email_host' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'email_port' => array('type' => 'integer', 'null' => true,
			'unsigned' => true, 'default' => '0', 'length' => 11),
		'email_tls' => array('type' => 'integer', 'null' => true,
			'unsigned' => true, 'default' => '0', 'length' => 1),
		'email_username' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'email_password' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'email_from' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8',
			'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB'
		)
	);

	public $wzuseraccounts = array(
		'id' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'length' => 11, 'key' => 'primary'),
		'wzuser_id' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'length' => 11),
		'wzaccount_id' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'length' => 11),
		'role' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8',
			'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB'
		)
	);

	public $wzusers = array(
		'id' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'length' => 11, 'key' => 'primary'),
		'username' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'password' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'fullname' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'email' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'timezone' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'role' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'status' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'default' => '0', 'length' => 1),
		'verification_key' => array('type' => 'string', 'null' => true,
			'default' => 'null', 'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'email_verified' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'default' => '0', 'length' => 1),
		'admin_verified' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'default' => '0', 'length' => 1),
		'retry_count' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'default' => '0', 'length' => 1),
		'all_accounts' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'default' => '0', 'length' => 1),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8',
			'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB'
		)
	);

}
