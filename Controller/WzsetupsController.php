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
		return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzsetups', 'action' => 'install'));
	}

/**
 * setup method
 *
 * @return void
 */
	public function install() {

		App::uses('File', 'Utility');

		$this->set('title_for_layout', __d('webzash', 'Welcome to ' . Configure::read('Webzash.AppName')
			. ' v' . Configure::read('Webzash.AppVersion') . ' Installer'));

		if (!is_writable(CONFIG)) {
			$this->Session->setFlash(__d('webzash', 'Error ! The "app/Config" folder is not writable.'), 'danger');
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
				'db_prefix' => strtolower($this->request->data['Wzsetup']['db_prefix']),
			));
			if ($this->request->data['Wzsetup']['db_persistent'] == 1) {
				$check_data['Wzsetup']['db_persistent'] = 1;
			} else {
				$check_data['Wzsetup']['db_persistent'] = 0;
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
			$wz_newconfig['prefix'] = strtolower($this->request->data['Wzsetup']['db_prefix']);
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
			} else if ($this->request->data['Wzaccount']['db_datasource'] == 'Database/Postgres') {
				$schema_filepath = App::pluginPath('Webzash') . 'Config/MasterSchema.Postgres.sql';
			}
			$schema_file = new File($schema_filepath, false);
			$schema = $schema_file->read(true, 'r');

			/* Add prefix to the table names in the schema */
			$final_schema = str_replace('%_PREFIX_%', $wz_newconfig['prefix'], $schema);

			/* Create tables */
			try {
				$db->rawQuery($final_schema);
			} catch (Exception $e) {
				$this->Session->setFlash(__d('webzash', 'Oh Snap ! Something went wrong while creating the database tables. Please check your settings and try again.'), 'danger');
				return;
			}

			/* Write database configuration to file */
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

	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow('index', 'install');
	}

}
