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

App::uses('WebzashAppController', 'Webzash.Controller');
App::uses('ConnectionManager', 'Model');

/**
 * Webzash Plugin Wzsetups Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class WzsetupsController extends WebzashAppController {

	var $layout = 'setup';

/**
 * index method
 *
 * @return void
 */
	public function index() {

		$this->set('title_for_layout', __d('webzash', 'Welcome to %s Installer', Configure::read('Webzash.AppName') .
			' v' . Configure::read('Webzash.AppVersion')));

		/* Check if application already installed */
		if (!$this->checkOkToInstall()) {
			$this->Session->setFlash(__d('webzash', 'Application already installed. Please contact your administrator.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'login'));
		}
	}

/**
 * install method
 *
 * @return void
 */
	public function install() {

		App::uses('File', 'Utility');

		$this->set('title_for_layout', __d('webzash', 'Welcome to %s Installer', Configure::read('Webzash.AppName') .
			' v' . Configure::read('Webzash.AppVersion')));

		if (!is_writable(CONFIG)) {
			$this->Session->setFlash(__d('webzash', 'Error ! The "app/Config" folder is not writable.'), 'danger');
		}

		/* Check if application already installed */
		if (!$this->checkOkToInstall()) {
			$this->Session->setFlash(__d('webzash', 'Application already installed. Please contact your administrator.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'login'));
		}

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {

			/* Only check for valid input data, save later */
			$check_data = array('Wzsetup' => array(
				'db_datasource' => $this->request->data['Wzsetup']['db_datasource'],
				'db_database' => $this->request->data['Wzsetup']['db_database'],
				'db_schema' => $this->request->data['Wzsetup']['db_schema'],
				'db_host' => $this->request->data['Wzsetup']['db_host'],
				'db_port' => $this->request->data['Wzsetup']['db_port'],
				'db_login' => $this->request->data['Wzsetup']['db_login'],
				'db_password' => $this->request->data['Wzsetup']['db_password'],
				'db_prefix' => $this->request->data['Wzsetup']['db_prefix'],
			));
			if ($this->request->data['Wzsetup']['db_persistent'] == 1) {
				$check_data['Wzsetup']['db_persistent'] = 1;
			} else {
				$check_data['Wzsetup']['db_persistent'] = 0;
			}
			if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Mysql') {
				if ($this->request->data['Wzsetup']['db_schema'] != "") {
					$this->Session->setFlash(__d('webzash', 'Database schema should be empty for MySQL since it is not supported.'), 'danger');
					return;
				}
			}

			$this->Wzsetup->set($check_data);
			if (!$this->Wzsetup->validates()) {
				foreach ($this->Wzsetup->validationErrors as $field => $msg) {
					$errmsg = $msg[0];
					break;
				}
				$this->Session->setFlash($errmsg, 'danger');
				return;
			}

			/* Create account database configuration */
			$wz_newconfig['datasource'] = $this->request->data['Wzsetup']['db_datasource'];
			$wz_newconfig['database'] = $this->request->data['Wzsetup']['db_database'];
			$wz_newconfig['schema'] = $this->request->data['Wzsetup']['db_schema'];
			$wz_newconfig['host'] = $this->request->data['Wzsetup']['db_host'];
			$wz_newconfig['port'] = $this->request->data['Wzsetup']['db_port'];
			$wz_newconfig['login'] = $this->request->data['Wzsetup']['db_login'];
			$wz_newconfig['password'] = $this->request->data['Wzsetup']['db_password'];
			$wz_newconfig['prefix'] = $this->request->data['Wzsetup']['db_prefix'];
			if ($this->request->data['Wzsetup']['db_persistent'] == 1) {
				$wz_newconfig['persistent'] = TRUE;
			} else {
				$wz_newconfig['persistent'] = FALSE;
			}

			/* Create account database config and try to connect to it */
			try {
				ConnectionManager::create('wz_newconfig', $wz_newconfig);
			} catch (Exception $e) {
				$this->Session->setFlash(__d('webzash', 'Cound not connect to database. Please, check your database settings.'), 'danger');
				return;
			}

			/* Connection successfull, next check if any table names clash */
			$db = ConnectionManager::getDataSource('wz_newconfig');

			if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Mysql') {
				$existing_tables = $db->query("show tables");
				/*
				Format of $existing_tables is
				array(
					0 => array(
						'TABLE_NAMES' => array(
							'Tables_in_<dbname>' => 'entries'
						)
					),
					...
				*/
				/* Array of new tables that are to be created */
				$new_tables = array(
					$wz_newconfig['prefix'] . 'wzaccounts',
					$wz_newconfig['prefix'] . 'wzsettings',
					$wz_newconfig['prefix'] . 'wzuseraccounts',
					$wz_newconfig['prefix'] . 'wzusers',
				);

				/* Check if any table from $new_table already exists */
				$table_exisits = false;
				foreach ($existing_tables as $row => $table_1) {
					foreach ($table_1 as $row => $table_2) {
						foreach ($table_2 as $row => $table) {
							if (in_array(strtolower($table), $new_tables)) {
								$table_exisits = TRUE;
								$this->Session->setFlash(__d('webzash', 'Table with the same name as "%s" already existsin the "%s" database. Please use another database or use a different prefix.', $table, $wz_newconfig['database']), 'danger');
							}
						}
					}
				}
				if ($table_exisits == TRUE) {
					return;
				}
			}

			/**
			 * At this point the connection is successfull and there are no table clashes,
			 * we can create the application specific tables.
			 */

			/* Read the database creation schema from the Config folder */
			if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Mysql') {
				$schema_filepath = App::pluginPath('Webzash') . 'Config/MasterSchema.MySQL.sql';
			} else if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Postgres') {
				$schema_filepath = App::pluginPath('Webzash') . 'Config/MasterSchema.Postgres.sql';
			}
			$schema_file = new File($schema_filepath, false);
			$schema = $schema_file->read(true, 'r');

			/* Add prefix / schema to the table names */
			$final_schema = '';
			$replace_prefix_schema_str = '';
			if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Mysql') {
				$replace_prefix_schema_str = $wz_newconfig['prefix'];
				$final_schema = str_replace('%_PREFIX_%', $wz_newconfig['prefix'], $schema);
			} else if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Postgres') {
				$replace_prefix_schema_str = $wz_newconfig['prefix'];
				$tmp_schema = str_replace('%_PREFIX_%', $wz_newconfig['prefix'], $schema);
				if ($wz_newconfig['schema'] != "") {
					$final_schema = str_replace('%_SCHEMA_%', $wz_newconfig['schema'] . '.', $tmp_schema);
				} else {
					$final_schema = str_replace('%_SCHEMA_%', '', $tmp_schema);
				}
			}

			/* Create tables */
			try {
				$db->rawQuery($final_schema);
			} catch (Exception $e) {
				$this->Session->setFlash(__d('webzash', 'Oh Snap ! Something went wrong while creating the database tables. Please check your settings and try again.'), 'danger');
				return;
			}

			/* insert admin user */
			$db->query('INSERT INTO ' . $replace_prefix_schema_str . 'wzusers ' .
				'(id, username, password, fullname, email, timezone, role, status, verification_key, email_verified, admin_verified, retry_count, all_accounts, default_account) VALUES ' .
				'(1, \'admin\', \'\', \'Administrator\', \'\', \'UTC\', \'admin\', 1, \'\', 1, 1, 0, 1, 0);');

			/* Since manually inserted data with id, postgres does not update sequence hence updating sequence */
			if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Postgres') {
				$db->query('SELECT setval((select pg_get_serial_sequence(\'' . $replace_prefix_schema_str . 'wzaccounts\', \'id\')), (SELECT MAX(id) from ' . $replace_prefix_schema_str . 'wzaccounts));');
				$db->query('SELECT setval((select pg_get_serial_sequence(\'' . $replace_prefix_schema_str . 'wzusers\', \'id\')), (SELECT MAX(id) from ' . $replace_prefix_schema_str . 'wzusers));');
				$db->query('SELECT setval((select pg_get_serial_sequence(\'' . $replace_prefix_schema_str . 'wzsettings\', \'id\')), (SELECT MAX(id) from ' . $replace_prefix_schema_str . 'wzsettings));');
				$db->query('SELECT setval((select pg_get_serial_sequence(\'' . $replace_prefix_schema_str . 'wzuseraccounts\', \'id\')), (SELECT MAX(id) from ' . $replace_prefix_schema_str . 'wzuseraccounts));');
			}

			/* Write database configuration to file */
			$database_settings = '';
			if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Mysql') {
				/* Schema not used */
				$database_settings = '<' . '?' . 'php' . "\n" .
				'	$wz[\'datasource\'] = \'' . $wz_newconfig['datasource'] . '\';' . "\n" .
				'	$wz[\'database\'] = \'' . $wz_newconfig['database'] . '\';' . "\n" .
				'	$wz[\'host\'] = \'' . $wz_newconfig['host'] . '\';' . "\n" .
				'	$wz[\'port\'] = \'' . $wz_newconfig['port'] . '\';' . "\n" .
				'	$wz[\'login\'] = \'' . $wz_newconfig['login'] . '\';' . "\n" .
				'	$wz[\'password\'] = \'' . $wz_newconfig['password'] . '\';' . "\n" .
				'	$wz[\'prefix\'] = \'' . $wz_newconfig['prefix'] . '\';' . "\n" .
				'	$wz[\'encoding\'] = \'utf8\';' . "\n" .
				'	$wz[\'persistent\'] = \'' . $wz_newconfig['persistent'] . '\';' . "\n" .
				'?' . '>';
			} else if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Postgres') {
				/* Use schema only if set */
				if ($wz_newconfig['schema'] == "") {
					/* if schema is empty then dont add it to config file else it will give error */
					$database_settings = '<' . '?' . 'php' . "\n" .
					'	$wz[\'datasource\'] = \'' . $wz_newconfig['datasource'] . '\';' . "\n" .
					'	$wz[\'database\'] = \'' . $wz_newconfig['database'] . '\';' . "\n" .
					'	$wz[\'host\'] = \'' . $wz_newconfig['host'] . '\';' . "\n" .
					'	$wz[\'port\'] = \'' . $wz_newconfig['port'] . '\';' . "\n" .
					'	$wz[\'login\'] = \'' . $wz_newconfig['login'] . '\';' . "\n" .
					'	$wz[\'password\'] = \'' . $wz_newconfig['password'] . '\';' . "\n" .
					'	$wz[\'prefix\'] = \'' . $wz_newconfig['prefix'] . '\';' . "\n" .
					'	$wz[\'encoding\'] = \'utf8\';' . "\n" .
					'	$wz[\'persistent\'] = \'' . $wz_newconfig['persistent'] . '\';' . "\n" .
					'?' . '>';
				} else {
					$database_settings = '<' . '?' . 'php' . "\n" .
					'	$wz[\'datasource\'] = \'' . $wz_newconfig['datasource'] . '\';' . "\n" .
					'	$wz[\'database\'] = \'' . $wz_newconfig['database'] . '\';' . "\n" .
					'	$wz[\'schema\'] = \'' . $wz_newconfig['schema'] . '\';' . "\n" .
					'	$wz[\'host\'] = \'' . $wz_newconfig['host'] . '\';' . "\n" .
					'	$wz[\'port\'] = \'' . $wz_newconfig['port'] . '\';' . "\n" .
					'	$wz[\'login\'] = \'' . $wz_newconfig['login'] . '\';' . "\n" .
					'	$wz[\'password\'] = \'' . $wz_newconfig['password'] . '\';' . "\n" .
					'	$wz[\'prefix\'] = \'' . $wz_newconfig['prefix'] . '\';' . "\n" .
					'	$wz[\'encoding\'] = \'utf8\';' . "\n" .
					'	$wz[\'persistent\'] = \'' . $wz_newconfig['persistent'] . '\';' . "\n" .
					'?' . '>';
				}
			}

			$database_settings_file = new File(CONFIG . 'webzash.php', true, 0600);
			if (!$database_settings_file->write($database_settings)) {
				$database_settings_file->close();
				$this->Session->setFlash(__d('webzash', 'Failed to write database settings to "app/Config/webzash.php". You will have to manually create the file with the necessary database settings.'), 'danger');
				return;
			}
			$database_settings_file->close();

			/* All done */
			$this->Session->setFlash(__d('webzash', 'Setup completed successfully.'), 'success');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'login'));
		}
		return;
	}

/**
 * upgrade method
 *
 * @return void
 */
	public function upgrade() {

		$this->set('title_for_layout', __d('webzash', 'Upgrade to %s', Configure::read('Webzash.AppName') .
			' v' . Configure::read('Webzash.AppVersion')));

		if (!is_writable(CONFIG)) {
			$this->Session->setFlash(__d('webzash', 'Error ! The "app/Config" folder is not writable.'), 'danger');
		}

		/* Check if application already installed */
		if (!$this->checkOkToInstall()) {
			$this->Session->setFlash(__d('webzash', 'Application already installed. Please contact your administrator.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'login'));
		}

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {

			/* Only check for valid input data, save later */
			$check_data = array('Wzsetup' => array(
				'db_datasource' => $this->request->data['Wzsetup']['db_datasource'],
				'db_database' => $this->request->data['Wzsetup']['db_database'],
				'db_schema' => $this->request->data['Wzsetup']['db_schema'],
				'db_host' => $this->request->data['Wzsetup']['db_host'],
				'db_port' => $this->request->data['Wzsetup']['db_port'],
				'db_login' => $this->request->data['Wzsetup']['db_login'],
				'db_password' => $this->request->data['Wzsetup']['db_password'],
				'db_prefix' => $this->request->data['Wzsetup']['db_prefix'],
			));
			if ($this->request->data['Wzsetup']['db_persistent'] == 1) {
				$check_data['Wzsetup']['db_persistent'] = 1;
			} else {
				$check_data['Wzsetup']['db_persistent'] = 0;
			}
			if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Mysql') {
				if ($this->request->data['Wzsetup']['db_schema'] != "") {
					$this->Session->setFlash(__d('webzash', 'Database schema should be empty for MySQL since it is not supported.'), 'danger');
					return;
				}
			}
			$this->Wzsetup->set($check_data);
			if (!$this->Wzsetup->validates()) {
				foreach ($this->Wzsetup->validationErrors as $field => $msg) {
					$errmsg = $msg[0];
					break;
				}
				$this->Session->setFlash($errmsg, 'danger');
				return;
			}
			if (empty($this->request->data['Wzsetup']['old_sqlite']['name'])) {
				$this->Session->setFlash(__d('webzash', 'Old version 2.x master data file named "webzash.sqlite" cannot be empty.'), 'danger');
				return;
			}

			/*****************************************************************/
			/***** Temporarily save old master data from sqlite database *****/
			/*****************************************************************/

			$wz_oldconfig['datasource'] = 'Database/Sqlite';
			$wz_oldconfig['database'] = $this->request->data['Wzsetup']['old_sqlite']['tmp_name'];
			$wz_oldconfig['encoding'] = 'utf8';
			$wz_oldconfig['persistent'] = false;

			try {
				ConnectionManager::create('wz_oldconfig', $wz_oldconfig);
			} catch (Exception $e) {
				$this->Session->setFlash(__d('webzash', 'Failed to open "webzash.sqlite".'), 'danger');
				return;
			}
			$db_old = ConnectionManager::getDataSource('wz_oldconfig');
			$wz_old_accounts = $db_old->fetchAll("SELECT id, label, db_datasource, db_database, db_host, db_port, db_login, db_password, db_prefix, db_persistent, db_schema, db_unixsocket, db_settings, ssl_key, ssl_cert, ssl_ca FROM wzaccounts");
			$wz_old_users = $db_old->fetchAll("SELECT id, username, password, fullname, email, timezone, role, status, verification_key, email_verified, admin_verified, retry_count, all_accounts FROM wzusers");
			$wz_old_settings = $db_old->fetchAll("SELECT id, sitename, drcr_toby, enable_logging, row_count, user_registration, admin_verification, email_verification, email_protocol, email_host, email_port, email_tls, email_username, email_password, email_from FROM wzsettings");
			$wz_old_useraccounts = $db_old->fetchAll("SELECT id, wzuser_id, wzaccount_id, role FROM wzuseraccounts");

			/*************************************/
			/***** Setup new master database *****/
			/*************************************/

			/* Create account database configuration */
			$wz_newconfig['datasource'] = $this->request->data['Wzsetup']['db_datasource'];
			$wz_newconfig['database'] = $this->request->data['Wzsetup']['db_database'];
			$wz_newconfig['schema'] = $this->request->data['Wzsetup']['db_schema'];
			$wz_newconfig['host'] = $this->request->data['Wzsetup']['db_host'];
			$wz_newconfig['port'] = $this->request->data['Wzsetup']['db_port'];
			$wz_newconfig['login'] = $this->request->data['Wzsetup']['db_login'];
			$wz_newconfig['password'] = $this->request->data['Wzsetup']['db_password'];
			$wz_newconfig['prefix'] = $this->request->data['Wzsetup']['db_prefix'];
			/* TODO SCHEMA */
			if ($this->request->data['Wzsetup']['db_persistent'] == 1) {
				$wz_newconfig['persistent'] = TRUE;
			} else {
				$wz_newconfig['persistent'] = FALSE;
			}

			/* Create account database config and try to connect to it */
			try {
				ConnectionManager::create('wz_newconfig', $wz_newconfig);
			} catch (Exception $e) {
				$this->Session->setFlash(__d('webzash', 'Cound not connect to database. Please, check your database settings.'), 'danger');
				return;
			}

			/* Connection successfull, next check if any table names clash */
			$db_new = ConnectionManager::getDataSource('wz_newconfig');

			if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Mysql') {
				$existing_tables = $db_new->query("show tables");
				/*
				Format of $existing_tables is
				array(
					0 => array(
						'TABLE_NAMES' => array(
							'Tables_in_<dbname>' => 'entries'
						)
					),
					...
				*/
				/* Array of new tables that are to be created */
				$new_tables = array(
					$wz_newconfig['prefix'] . 'wzaccounts',
					$wz_newconfig['prefix'] . 'wzsettings',
					$wz_newconfig['prefix'] . 'wzuseraccounts',
					$wz_newconfig['prefix'] . 'wzusers',
				);

				/* Check if any table from $new_table already exists */
				$table_exisits = false;
				foreach ($existing_tables as $row => $table_1) {
					foreach ($table_1 as $row => $table_2) {
						foreach ($table_2 as $row => $table) {
							if (in_array(strtolower($table), $new_tables)) {
								$table_exisits = TRUE;
								$this->Session->setFlash(__d('webzash', 'Table with the same name as "%s" already existsin the "%s" database. Please use another database or use a different prefix.', $table, $wz_newconfig['database']), 'danger');
							}
						}
					}
				}
				if ($table_exisits == TRUE) {
					return;
				}
			}

			/**
			 * At this point the connection is successfull and there are no table clashes,
			 * we can create the application specific tables.
			 */

			/* Read the database creation schema from the Config folder */
			if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Mysql') {
				$schema_filepath = App::pluginPath('Webzash') . 'Config/MasterSchema.MySQL.sql';
			} else if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Postgres') {
				$schema_filepath = App::pluginPath('Webzash') . 'Config/MasterSchema.Postgres.sql';
			}
			$schema_file = new File($schema_filepath, false);
			$schema = $schema_file->read(true, 'r');

			/* Add prefix / schema to the table names */
			$final_schema = '';
			$replace_prefix_schema_str = '';
			if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Mysql') {
				$replace_prefix_schema_str = $wz_newconfig['prefix'];
				$final_schema = str_replace('%_PREFIX_%', $wz_newconfig['prefix'], $schema);
			} else if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Postgres') {
				$replace_prefix_schema_str = $wz_newconfig['prefix'];
				$tmp_schema = str_replace('%_PREFIX_%', $wz_newconfig['prefix'], $schema);
				if ($wz_newconfig['schema'] != "") {
					$final_schema = str_replace('%_SCHEMA_%', $wz_newconfig['schema'] . '.', $tmp_schema);
				} else {
					$final_schema = str_replace('%_SCHEMA_%', '', $tmp_schema);
				}
			}

			/* Create tables */
			try {
				$db_new->rawQuery($final_schema);
			} catch (Exception $e) {
				$this->Session->setFlash(__d('webzash', 'Oh Snap ! Something went wrong while creating the database tables. Please check your settings and try again.'), 'danger');
				return;
			}

			/**************************************************/
			/***** Import old master data to new database *****/
			/**************************************************/

			foreach ($wz_old_accounts as $new_account) {
				$db_new->query('INSERT INTO ' . $replace_prefix_schema_str . 'wzaccounts ' .
					'(id, label, db_datasource, db_database, db_host, db_port, db_login, db_password, db_prefix, db_persistent, db_schema, db_unixsocket, db_settings, ssl_key, ssl_cert, ssl_ca) VALUES ' .
					'(' . $new_account[0]['id'] . ',' .
					'\'' . $new_account[0]['label'] . '\',' .
					'\'' . $new_account[0]['db_datasource'] . '\',' .
					'\'' . $new_account[0]['db_database'] . '\',' .
					'\'' . $new_account[0]['db_host'] . '\',' .
					$new_account[0]['db_port'] . ',' .
					'\'' . $new_account[0]['db_login'] . '\',' .
					'\'' . $new_account[0]['db_password'] . '\',' .
					'\'' . $new_account[0]['db_prefix'] . '\',' .
					'\'' . $new_account[0]['db_persistent'] . '\',' .
					'\'' . $new_account[0]['db_schema'] . '\',' .
					'\'' . $new_account[0]['db_unixsocket'] . '\',' .
					'\'' . $new_account[0]['db_settings'] . '\',' .
					'\'' . $new_account[0]['ssl_key'] . '\',' .
					'\'' . $new_account[0]['ssl_cert'] . '\',' .
					'\'' . $new_account[0]['ssl_ca'] . '\'' .
					');');
			}
			foreach ($wz_old_users as $new_user) {
				$db_new->query('INSERT INTO ' . $replace_prefix_schema_str . 'wzusers ' .
					'(id, username, password, fullname, email, timezone, role, status, verification_key, email_verified, admin_verified, retry_count, all_accounts) VALUES ' .
					'(' . $new_user[0]['id'] . ',' .
					'\'' . $new_user[0]['username'] . '\',' .
					'\'' . $new_user[0]['password'] . '\',' .
					'\'' . $new_user[0]['fullname'] . '\',' .
					'\'' . $new_user[0]['email'] . '\',' .
					'\'' . $new_user[0]['timezone'] . '\',' .
					'\'' . $new_user[0]['role'] . '\',' .
					$new_user[0]['status'] . ',' .
					'\'' . $new_user[0]['verification_key'] . '\',' .
					$new_user[0]['email_verified'] . ',' .
					$new_user[0]['admin_verified'] . ',' .
					$new_user[0]['retry_count'] . ',' .
					$new_user[0]['all_accounts'] .
					');');
			}
			foreach ($wz_old_settings as $new_setting) {
				$db_new->query('INSERT INTO ' . $replace_prefix_schema_str . 'wzsettings ' .
					'(id, sitename, drcr_toby, enable_logging, row_count, user_registration, admin_verification, email_verification, email_protocol, email_host, email_port, email_tls, email_username, email_password, email_from) VALUES ' .
					'(' . $new_setting[0]['id'] . ',' .
					'\'' . $new_setting[0]['sitename'] . '\',' .
					'\'' . $new_setting[0]['drcr_toby'] . '\',' .
					$new_setting[0]['enable_logging'] . ',' .
					$new_setting[0]['row_count'] . ',' .
					$new_setting[0]['user_registration'] . ',' .
					$new_setting[0]['admin_verification'] . ',' .
					$new_setting[0]['email_verification'] . ',' .
					'\'' . $new_setting[0]['email_protocol'] . '\',' .
					'\'' . $new_setting[0]['email_host'] . '\',' .
					$new_setting[0]['email_port'] . ',' .
					$new_setting[0]['email_tls'] . ',' .
					'\'' . $new_setting[0]['email_username'] . '\',' .
					'\'' . $new_setting[0]['email_password'] . '\',' .
					'\'' . $new_setting[0]['email_from'] . '\'' .
					');');
			}
			foreach ($wz_old_useraccounts as $new_useraccount) {
				$db_new->query('INSERT INTO ' . $replace_prefix_schema_str . 'wzuseraccounts ' .
					'(id, wzuser_id, wzaccount_id, role) VALUES ' .
					'(' . $new_useraccount[0]['id'] . ',' .
					$new_useraccount[0]['wzuser_id'] . ',' .
					$new_useraccount[0]['wzaccount_id'] . ',' .
					'\'' . $new_useraccount[0]['role'] . '\'' .
					');');
			}

			/* Since manually inserted data with id, postgres does not update sequence hence updating sequence */
			if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Postgres') {
				$db_new->query('SELECT setval((select pg_get_serial_sequence(\'' . $replace_prefix_schema_str . 'wzaccounts\', \'id\')), (SELECT MAX(id) from ' . $replace_prefix_schema_str . 'wzaccounts));');
				$db_new->query('SELECT setval((select pg_get_serial_sequence(\'' . $replace_prefix_schema_str . 'wzusers\', \'id\')), (SELECT MAX(id) from ' . $replace_prefix_schema_str . 'wzusers));');
				$db_new->query('SELECT setval((select pg_get_serial_sequence(\'' . $replace_prefix_schema_str . 'wzsettings\', \'id\')), (SELECT MAX(id) from ' . $replace_prefix_schema_str . 'wzsettings));');
				$db_new->query('SELECT setval((select pg_get_serial_sequence(\'' . $replace_prefix_schema_str . 'wzuseraccounts\', \'id\')), (SELECT MAX(id) from ' . $replace_prefix_schema_str . 'wzuseraccounts));');
			}

			/* Write database configuration to file */
			$database_settings = '';
			if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Mysql') {
				/* Use prefix. Schema not used */
				$database_settings = '<' . '?' . 'php' . "\n" .
				'	$wz[\'datasource\'] = \'' . $wz_newconfig['datasource'] . '\';' . "\n" .
				'	$wz[\'database\'] = \'' . $wz_newconfig['database'] . '\';' . "\n" .
				'	$wz[\'host\'] = \'' . $wz_newconfig['host'] . '\';' . "\n" .
				'	$wz[\'port\'] = \'' . $wz_newconfig['port'] . '\';' . "\n" .
				'	$wz[\'login\'] = \'' . $wz_newconfig['login'] . '\';' . "\n" .
				'	$wz[\'password\'] = \'' . $wz_newconfig['password'] . '\';' . "\n" .
				'	$wz[\'prefix\'] = \'' . $wz_newconfig['prefix'] . '\';' . "\n" .
				'	$wz[\'encoding\'] = \'utf8\';' . "\n" .
				'	$wz[\'persistent\'] = \'' . $wz_newconfig['persistent'] . '\';' . "\n" .
				'?' . '>';
			} else if ($this->request->data['Wzsetup']['db_datasource'] == 'Database/Postgres') {
				/* Use schema */
				if ($wz_newconfig['schema'] == "") {
					/* If schema is empty then dont add it to config file else it will give error */
					$database_settings = '<' . '?' . 'php' . "\n" .
					'	$wz[\'datasource\'] = \'' . $wz_newconfig['datasource'] . '\';' . "\n" .
					'	$wz[\'database\'] = \'' . $wz_newconfig['database'] . '\';' . "\n" .
					'	$wz[\'host\'] = \'' . $wz_newconfig['host'] . '\';' . "\n" .
					'	$wz[\'port\'] = \'' . $wz_newconfig['port'] . '\';' . "\n" .
					'	$wz[\'login\'] = \'' . $wz_newconfig['login'] . '\';' . "\n" .
					'	$wz[\'password\'] = \'' . $wz_newconfig['password'] . '\';' . "\n" .
					'	$wz[\'prefix\'] = \'' . $wz_newconfig['prefix'] . '\';' . "\n" .
					'	$wz[\'encoding\'] = \'utf8\';' . "\n" .
					'	$wz[\'persistent\'] = \'' . $wz_newconfig['persistent'] . '\';' . "\n" .
					'?' . '>';
				} else {
					$database_settings = '<' . '?' . 'php' . "\n" .
					'	$wz[\'datasource\'] = \'' . $wz_newconfig['datasource'] . '\';' . "\n" .
					'	$wz[\'database\'] = \'' . $wz_newconfig['database'] . '\';' . "\n" .
					'	$wz[\'schema\'] = \'' . $wz_newconfig['schema'] . '\';' . "\n" .
					'	$wz[\'host\'] = \'' . $wz_newconfig['host'] . '\';' . "\n" .
					'	$wz[\'port\'] = \'' . $wz_newconfig['port'] . '\';' . "\n" .
					'	$wz[\'login\'] = \'' . $wz_newconfig['login'] . '\';' . "\n" .
					'	$wz[\'password\'] = \'' . $wz_newconfig['password'] . '\';' . "\n" .
					'	$wz[\'prefix\'] = \'' . $wz_newconfig['prefix'] . '\';' . "\n" .
					'	$wz[\'encoding\'] = \'utf8\';' . "\n" .
					'	$wz[\'persistent\'] = \'' . $wz_newconfig['persistent'] . '\';' . "\n" .
					'?' . '>';
				}
			}

			$database_settings_file = new File(CONFIG . 'webzash.php', true, 0600);
			if (!$database_settings_file->write($database_settings)) {
				$database_settings_file->close();
				$this->Session->setFlash(__d('webzash', 'Failed to write database settings to "app/Config/webzash.php". You will have to manually create the file with the necessary database settings.'), 'danger');
				return;
			}
			$database_settings_file->close();

			/* All done */
			$this->Session->setFlash(__d('webzash', 'Setup completed successfully.'), 'success');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'login'));
		}
	}

	function checkOkToInstall() {

		/* Load the master database configuration in $wz */
		if (file_exists(CONFIG . 'webzash.php')) {
			require_once(CONFIG . 'webzash.php');
		} else {
			return TRUE;
		}

		/* Check $wz */
		if (!isset($wz)) {
			return TRUE;
		}

		/* Create master database config and try to connect to it */
		App::uses('ConnectionManager', 'Model');
		try {
			ConnectionManager::create('wz_test', $wz);
		} catch (Exception $e) {
			return TRUE;
		}

		return FALSE;
	}

	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow('index', 'install', 'upgrade');
	}

}
