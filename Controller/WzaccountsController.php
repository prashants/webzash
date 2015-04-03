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
 * Webzash Plugin Wzaccounts Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class WzaccountsController extends WebzashAppController {

	public $uses = array('Webzash.Wzaccount', 'Webzash.Wzuser',
		'Webzash.Wzuseraccount', 'Webzash.Setting');

	var $layout = 'admin';

/**
 * index method
 *
 * @return void
 */
	public function index() {

		$this->set('title_for_layout', __d('webzash', 'Accounts'));

		$this->Wzaccount->useDbConfig = 'wz';

		$this->set('actionlinks', array(
			array('controller' => 'wzaccounts', 'action' => 'add', 'title' => __d('webzash', 'Add Account Config')),
			array('controller' => 'wzaccounts', 'action' => 'create', 'title' => __d('webzash', 'Create Account')),
			array('controller' => 'admin', 'action' => 'index', 'title' => __d('webzash', 'Back')),
		));

		$this->CustomPaginator->settings = array(
			'Wzaccount' => array(
				'limit' => $this->Session->read('Wzsetting.row_count'),
				'order' => array('Wzaccount.label' => 'asc'),
			)
		);

		$this->set('wzaccounts', $this->CustomPaginator->paginate('Wzaccount'));

		return;
	}

/**
 * create method
 *
 * @return void
 */
	public function create() {

		$this->set('title_for_layout', __d('webzash', 'Create new account'));

		$this->Wzaccount->useDbConfig = 'wz';

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {

			/* Check is database if anything else other than MySQL */
			if ($this->request->data['Wzaccount']['db_datasource'] == 'Database/Sqlserver') {
				$this->Session->setFlash(__d('webzash', 'Sorry, currently MS SQL Server is not supported. We might add it soon, if you want to help let us know.'), 'danger');
				return;
			}
			if ($this->request->data['Wzaccount']['db_datasource'] == 'Database/Postgres') {
				$this->Session->setFlash(__d('webzash', 'Sorry, currently Postgres SQL Server is not supported. We might add it soon, if you want to help let us know.'), 'danger');
				return;
			}

			/* Check if label already exists */
			$count = $this->Wzaccount->find('count', array('conditions' => array(
				'Wzaccount.label' => $this->request->data['Wzaccount']['label'],
			)));
			if ($count != 0) {
				$this->Session->setFlash(__d('webzash', 'Label is already in use. Please, try again.'), 'danger');
				return;
			}

			/* Check if all values required are present */
			if (empty($this->request->data['Wzaccount']['label'])) {
				$this->Session->setFlash(__d('webzash', 'Label is required.'), 'danger');
				return;
			}
			if (empty($this->request->data['Wzaccount']['name'])) {
				$this->Session->setFlash(__d('webzash', 'Company / Personal Name is required.'), 'danger');
				return;
			}
			if (empty($this->request->data['Wzaccount']['date_format'])) {
				$this->Session->setFlash(__d('webzash', 'Date format is required.'), 'danger');
				return;
			}
			if (empty($this->request->data['Wzaccount']['fy_start'])) {
				$this->Session->setFlash(__d('webzash', 'Financial year start is required.'), 'danger');
				return;
			}
			if (empty($this->request->data['Wzaccount']['fy_end'])) {
				$this->Session->setFlash(__d('webzash', 'Financial year end is required.'), 'danger');
				return;
			}
			if (empty($this->request->data['Wzaccount']['db_database'])) {
				$this->Session->setFlash(__d('webzash', 'Database name is required.'), 'danger');
				return;
			}
			if (empty($this->request->data['Wzaccount']['db_host'])) {
				$this->Session->setFlash(__d('webzash', 'Database host is required.'), 'danger');
				return;
			}
			if (empty($this->request->data['Wzaccount']['db_port'])) {
				$this->Session->setFlash(__d('webzash', 'Database port is required.'), 'danger');
				return;
			}
			if (empty($this->request->data['Wzaccount']['db_login'])) {
				$this->Session->setFlash(__d('webzash', 'Database login is required.'), 'danger');
				return;
			}

			/* Check financial year start is before end */
			$fy_start = strtotime($this->request->data['Wzaccount']['fy_start'] . ' 00:00:00');
			$fy_end = strtotime($this->request->data['Wzaccount']['fy_end'] . ' 00:00:00');
			if ($fy_start >= $fy_end) {
				$this->Session->setFlash(__d('webzash', 'Financial year start date cannot be after end date.'), 'danger');
				return;
			}

			/* Check email */
			if (!filter_var($this->request->data['Wzaccount']['email'], FILTER_VALIDATE_EMAIL)) {
				$this->Session->setFlash(__d('webzash', 'Email address is invalid.'), 'danger');
				return;
			}

			/* Only check for valid input data, save later */
			$check_data = array('Wzaccount' => array(
				'label' => $this->request->data['Wzaccount']['label'],
				'db_datasource' => $this->request->data['Wzaccount']['db_datasource'],
				'db_database' => $this->request->data['Wzaccount']['db_database'],
				'db_host' => $this->request->data['Wzaccount']['db_host'],
				'db_port' => $this->request->data['Wzaccount']['db_port'],
				'db_login' => $this->request->data['Wzaccount']['db_login'],
				'db_password' => $this->request->data['Wzaccount']['db_password'],
				'db_prefix' => strtolower($this->request->data['Wzaccount']['db_prefix']),
				'db_schema' => '',
				'db_unixsocket' => '',
				'db_settings' => $this->request->data['Wzaccount']['db_settings'],
			));
			if ($this->request->data['Wzaccount']['db_persistent'] == 1) {
				$check_data['Wzaccount']['db_persistent'] = 1;
			} else {
				$check_data['Wzaccount']['db_persistent'] = 0;
			}
			$this->Wzaccount->set($check_data);
			if (!$this->Wzaccount->validates()) {
				foreach ($this->Wzaccount->validationErrors as $field => $msg) {
					$errmsg = $msg[0];
					break;
				}
				$this->Session->setFlash($errmsg, 'danger');
				return;
			}

			/* Create account database configuration */
			$wz_newconfig['datasource'] = $this->request->data['Wzaccount']['db_datasource'];
			$wz_newconfig['database'] = $this->request->data['Wzaccount']['db_database'];
			$wz_newconfig['host'] = $this->request->data['Wzaccount']['db_host'];
			$wz_newconfig['port'] = $this->request->data['Wzaccount']['db_port'];
			$wz_newconfig['login'] = $this->request->data['Wzaccount']['db_login'];
			$wz_newconfig['password'] = $this->request->data['Wzaccount']['db_password'];
			$wz_newconfig['prefix'] = strtolower($this->request->data['Wzaccount']['db_prefix']);
			if ($this->request->data['Wzaccount']['db_persistent'] == 1) {
				$wz_newconfig['persistent'] = TRUE;
			} else {
				$wz_newconfig['persistent'] = FALSE;
			}
			/**
			 * TODO
			 * $wz_newconfig['schema'] = $this->request->data['Wzaccount']['db_schema'];
			 * $wz_newconfig['unixsocket'] = $this->request->data['Wzaccount']['db_unixsocket'];
			 */
			$wz_newconfig['settings'] = $this->request->data['Wzaccount']['db_settings'];

			/* Create account database config and try to connect to it */
			try {
				ConnectionManager::create('wz_newconfig', $wz_newconfig);
			} catch (Exception $e) {
				$this->Session->setFlash(__d('webzash', 'Cound not connect to database. Please, check your database settings.'), 'danger');
				return;
			}

			/*****************************************************/
			/****************** MYSQL SPECIFIC *******************/
			/*****************************************************/
			if ($this->request->data['Wzaccount']['db_datasource'] == 'Database/Mysql') {


				/* Connection successfull, next check if any table names clash */
				$db = ConnectionManager::getDataSource('wz_newconfig');
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
					$wz_newconfig['prefix'] . 'entries',
					$wz_newconfig['prefix'] . 'entryitems',
					$wz_newconfig['prefix'] . 'entrytypes',
					$wz_newconfig['prefix'] . 'groups',
					$wz_newconfig['prefix'] . 'ledgers',
					$wz_newconfig['prefix'] . 'logs',
					$wz_newconfig['prefix'] . 'settings',
					$wz_newconfig['prefix'] . 'tags',
				);

				/* Check if any table from $new_table already exists */
				$table_exisits = false;
				foreach ($existing_tables as $row => $table_1) {
					foreach ($table_1 as $row => $table_2) {
						foreach ($table_2 as $row => $table) {
							if (in_array(strtolower($table), $new_tables)) {
								$table_exisits = TRUE;
								$this->Session->setFlash(__d('webzash', 'Table with the same name as "%s" already existsin the "%s" database. Please, use another database or use a different prefix.', $table, $wz_newconfig['database']), 'danger');
							}
						}
					}
				}
				if ($table_exisits == TRUE) {
					return;
				}

				/**
				 * At this point the connection is successfull and there are no table clashes,
				 * we can create the application specific tables.
				 */

				/* Read the MySQL database creation schema from the Config folder */
				App::uses('File', 'Utility');
				$schema_filepath = App::pluginPath('Webzash') . 'Config/Schema.Mysql.sql';
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

				/* Read the database trigger from the Config folder */
				$triggers_filepath = App::pluginPath('Webzash') . 'Config/Triggers.Mysql.sql';
				$triggers_file = new File($triggers_filepath, false);
				$triggers = $triggers_file->read(true, 'r');

				/* Add prefix to the table names in the database triggers */
				$final_triggers = str_replace('%_PREFIX_%', $wz_newconfig['prefix'], $triggers);

				/* Add database triggers */
				try {
					$db->rawQuery($final_triggers);
				} catch (Exception $e) {
					$this->Session->setFlash(__d('webzash', 'Oh Snap ! Something went wrong while adding database triggers. Please try again.'), 'danger');
					return;
				}

				/* Read the intial data from the Config folder */
				$initdata_filepath = App::pluginPath('Webzash') . 'Config/InitialData.Mysql.sql';
				$initdata_file = new File($initdata_filepath, false);
				$initdata = $initdata_file->read(true, 'r');

				/* Add prefix to the table names in the intial data */
				$final_initdata = str_replace('%_PREFIX_%', $wz_newconfig['prefix'], $initdata);

				/* Add initial data */
				try {
					$db->rawQuery($final_initdata);
				} catch (Exception $e) {
					$this->Session->setFlash(__d('webzash', 'Oh Snap ! Something went wrong while adding initial data. Please try again.'), 'danger');
					return;
				}

				/******* Create settings *******/
				$this->Setting->useDbConfig = 'wz_newconfig';

				$account_setting = array('Setting' => array(
					'id' => '1',
					'name' => $this->request->data['Wzaccount']['name'],
					'address' => $this->request->data['Wzaccount']['address'],
					'email' => $this->request->data['Wzaccount']['email'],
					'fy_start' => dateToSql($this->request->data['Wzaccount']['fy_start']),
					'fy_end' => dateToSql($this->request->data['Wzaccount']['fy_end']),
					'currency_symbol' => $this->request->data['Wzaccount']['currency_symbol'],
					'date_format' => $this->request->data['Wzaccount']['date_format'],
					'timezone' => 'UTC',
					'manage_inventory' => 0,
					'account_locked' => 0,
					'email_use_default' => 1,
					'email_protocol' => 'Smtp',
					'email_host' => '',
					'email_port' => 0,
					'email_tls' => 0,
					'email_username' => '',
					'email_password' => '',
					'email_from' => '',
					'print_paper_height' => 0.0,
					'print_paper_width' => 0.0,
					'print_margin_top' => 0.0,
					'print_margin_bottom' => 0.0,
					'print_margin_left' => 0.0,
					'print_margin_right' => 0.0,
					'print_orientation' => 'P',
					'print_page_format' => 'H',
					'database_version' => '5',
				));
				$this->Setting->create();
				if (!$this->Setting->save($account_setting)) {
					foreach ($this->Setting->validationErrors as $field => $msg) {
						$errmsg = $msg[0];
						break;
					}

					$this->Session->setFlash(__d('webzash', 'Account database created, but account settings could not be saved. Please, try again. Error is : "%s".', $errmsg), 'danger');
					return;
				}

				/******* Add to wzaccount table *******/
				$account_config = array('Wzaccount' => array(
					'label' => $this->request->data['Wzaccount']['label'],
					'db_datasource' => $this->request->data['Wzaccount']['db_datasource'],
					'db_database' => $this->request->data['Wzaccount']['db_database'],
					'db_host' => $this->request->data['Wzaccount']['db_host'],
					'db_port' => $this->request->data['Wzaccount']['db_port'],
					'db_login' => $this->request->data['Wzaccount']['db_login'],
					'db_password' => $this->request->data['Wzaccount']['db_password'],
					'db_prefix' => strtolower($this->request->data['Wzaccount']['db_prefix']),
					'db_schema' => '',
					'db_unixsocket' => '',
					'db_settings' => $this->request->data['Wzaccount']['db_settings'],
				));
				if ($this->request->data['Wzaccount']['db_persistent'] == 1) {
					$account_config['Wzaccount']['db_persistent'] = 1;
				} else {
					$account_config['Wzaccount']['db_persistent'] = 0;
				}

				/* Save database configuration */
				$this->Wzaccount->create();
				if ($this->Wzaccount->save($account_config)) {
					$this->Session->setFlash(__d('webzash', 'Account created.'), 'success');
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzaccounts', 'action' => 'index'));
				} else {
					foreach ($this->Wzaccount->validationErrors as $field => $msg) {
						$errmsg = $msg[0];
						break;
					}
					$this->Session->setFlash(__d('webzash', 'Account database created, but account config could not be saved. Please, try again. Error is : "%s".', $errmsg), 'danger');
					return;
				}

			} /* END MySQL Specific */
		}
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {

		$this->set('title_for_layout', __d('webzash', 'Add Account Config'));

		$this->Wzaccount->useDbConfig = 'wz';
		$this->Wzuser->useDbConfig = 'wz';

		/* Create list of wzusers */
		$wzusers = $this->Wzuser->find('list', array(
			'fields' => array('Wzuser.id', 'Wzuser.username'),
			'order' => array('Wzuser.username')
		));
		$this->set('wzusers', $wzusers);

		/* On POST */
		if ($this->request->is('post')) {
			$this->Wzaccount->create();
			if (!empty($this->request->data)) {
				/* Unset ID */
				unset($this->request->data['Wzaccount']['id']);

				/* Save account */
				$ds = $this->Wzaccount->getDataSource();
				$ds->begin();

				if ($this->Wzaccount->save($this->request->data)) {
					$ds->commit();
					$this->Session->setFlash(__d('webzash', 'Account config added.'), 'success');
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzaccounts', 'action' => 'index'));
				} else {
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'Failed to add account config. Please, try again.'), 'danger');
					return;
				}
			} else {
				$this->Session->setFlash(__d('webzash', 'No data. Please, try again.'), 'danger');
				return;
			}
		}
	}


/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {

		$this->set('title_for_layout', __d('webzash', 'Edit Account Config'));

		$this->Wzaccount->useDbConfig = 'wz';
		$this->Wzuser->useDbConfig = 'wz';

		/* Check for valid account */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'Account not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzaccounts', 'action' => 'index'));
		}
		$wzaccount = $this->Wzaccount->findById($id);
		if (!$wzaccount) {
			$this->Session->setFlash(__d('webzash', 'Account not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzaccounts', 'action' => 'index'));
		}

		/* Create list of wzusers */
		$wzusers = $this->Wzuser->find('list', array(
			'fields' => array('Wzuser.id', 'Wzuser.username'),
			'order' => array('Wzuser.username')
		));
		$this->set('wzusers', $wzusers);

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {
			/* Set user id */
			unset($this->request->data['Wzaccount']['id']);
			$this->Wzaccount->id = $id;

			/* Save account config */
			$ds = $this->Wzaccount->getDataSource();
			$ds->begin();

			if ($this->Wzaccount->save($this->request->data)) {
				$ds->commit();
				$this->Session->setFlash(__d('webzash', 'Updated account config.'), 'success');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzaccounts', 'action' => 'index'));
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'Failed to update account config. Please, try again.'), 'danger');
				return;
			}
		} else {
			$this->request->data = $wzaccount;
			return;
		}
	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		/* GET access not allowed */
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}

		$this->Wzaccount->useDbConfig = 'wz';
		$this->Wzuseraccount->useDbConfig = 'wz';

		/* Check if valid id */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'Account not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzaccounts', 'action' => 'index'));
		}

		/* Check if account exists */
		if (!$this->Wzaccount->exists($id)) {
			$this->Session->setFlash(__d('webzash', 'Account not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzaccounts', 'action' => 'index'));
		}

		/* Delete account */
		$ds = $this->Wzaccount->getDataSource();
		$ds->begin();

		/* TODO : Delete database */
		if (!$this->Wzaccount->delete($id)) {
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'Failed to delete account config. Please, try again.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzaccounts', 'action' => 'index'));
		}

		/* Delete user - account association */
		if (!$this->Wzuseraccount->deleteAll(array('Wzuseraccount.wzaccount_id' => $id))) {
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'Failed to delete user-account relationship. Please, try again.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzaccounts', 'action' => 'index'));
		}

		/* Success */
		$ds->commit();
		$this->Session->setFlash(__d('webzash', 'Account config deleted. Please delete the account database manually.'), 'success');

		return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzaccounts', 'action' => 'index'));
	}

	/* Authorization check */
	public function isAuthorized($user) {
		if ($this->action === 'index') {
			return $this->Permission->is_admin_allowed();
		}

		if ($this->action === 'create') {
			return $this->Permission->is_admin_allowed();
		}

		if ($this->action === 'add') {
			return $this->Permission->is_admin_allowed();
		}

		if ($this->action === 'edit') {
			return $this->Permission->is_admin_allowed();
		}

		if ($this->action === 'delete') {
			return $this->Permission->is_admin_allowed();
		}

		return parent::isAuthorized($user);
	}
}
