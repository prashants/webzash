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
App::uses('AccountList', 'Webzash.Lib');

/**
 * Webzash Plugin Settings Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class SettingsController extends WebzashAppController {

	public $uses = array('Webzash.Group', 'Webzash.Ledger', 'Webzash.Entrytype',
		'Webzash.Entry', 'Webzash.Log', 'Webzash.Tag', 'Webzash.Setting',
		'Webzash.Log', 'Webzash.Wzaccount');

/**
 * index method
 *
 * @return void
 */
	public function index() {

		$this->set('title_for_layout', __d('webzash', 'Settings'));

		return;
	}

/**
 * account settings method
 *
 * @return void
 */
	public function account() {

		$this->set('title_for_layout', __d('webzash', 'Account Settings'));

		$setting = $this->Setting->findById(1);
		if (!$setting) {
			$this->Session->setFlash(__d('webzash', 'Account settings not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'index'));
		}

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {

			/* Check if acccount is locked */
			if (Configure::read('Account.locked') == 1) {
				$this->Session->setFlash(__d('webzash', 'Sorry, no changes are possible since the account is locked.'), 'danger');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'index'));
			}

			/* Set setting id */
			unset($this->request->data['Setting']['id']);
			$this->Setting->id = 1;

			$settings = $this->request->data;
			$settings['Setting']['fy_start'] = dateToSql($this->request->data['Setting']['fy_start']);
			$settings['Setting']['fy_end'] = dateToSql($this->request->data['Setting']['fy_end']);

			/* Check if any entries are beyond start and end data */
			$count = $this->Entry->find('count', array(
				'conditions' => array(
					'OR' => array(
						'Entry.date <' => dateToSql($this->request->data['Setting']['fy_start']),
						'Entry.date >' => dateToSql($this->request->data['Setting']['fy_end']),
					),
				),
			));
			if ($count != 0) {
				$this->Session->setFlash(__d('webzash', 'Failed to update account setting since there are %d entries beyond the selected financial year start and end dates.', $count), 'danger');
				return;
			}

			/* Check if financial year end is after financial year start */
			$start_date = strtotime($this->request->data['Setting']['fy_start'] . ' 00:00:00');
			$end_date = strtotime($this->request->data['Setting']['fy_end'] . ' 00:00:00');
			if ($start_date >= $end_date) {
				$this->Session->setFlash(__d('webzash', 'Failed to update account setting since financial year end should be after financial year start.'), 'danger');
				return;
			}

			/* Save settings */
			$ds = $this->Setting->getDataSource();
			$ds->begin();

			if ($this->Setting->save($settings, true, array('name', 'address', 'email', 'fy_start', 'fy_end', 'currency_symbol', 'currency_format', 'date_format'))) {
				$this->Log->add('Updated account settings', 1);
				$ds->commit();
				$this->Session->setFlash(__d('webzash', 'Account settings updated.'), 'success');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'index'));
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'Failed to update account settings. Please, try again.'), 'danger');
				return;
			}
		} else {
			$setting['Setting']['fy_start'] = dateFromSql($setting['Setting']['fy_start']);
			$setting['Setting']['fy_end'] = dateFromSql($setting['Setting']['fy_end']);
			$this->request->data = $setting;
			return;
		}

		return;
	}

/**
 * carry forward to next financial year method
 *
 * @return void
 */
	public function cf() {

		$this->set('title_for_layout', __d('webzash', 'Carry Forward Account'));

		$this->Wzaccount->useDbConfig = 'wz';

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {

			/* Check if database engine is supported */
			if ($this->request->data['Wzaccount']['db_datasource'] == 'Database/Sqlserver') {
				$this->Session->setFlash(__d('webzash', 'Sorry, currently MS SQL Server is not supported. We might add it soon, if you want to help let us know.'), 'danger');
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
			if (empty($this->request->data['Wzaccount']['db_password'])) {
				$this->Session->setFlash(__d('webzash', 'Database password is required.'), 'danger');
				return;
			}

			/* Check financial year start is before end */
			$fy_start = strtotime($this->request->data['Wzaccount']['fy_start'] . ' 00:00:00');
			$fy_end = strtotime($this->request->data['Wzaccount']['fy_end'] . ' 00:00:00');
			if ($fy_start >= $fy_end) {
				$this->Session->setFlash(__d('webzash', 'Financial year start date cannot be after end date.'), 'danger');
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
				'db_schema' => $this->request->data['Wzaccount']['db_schema'],
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
			$wz_newconfig['schema'] = $this->request->data['Wzaccount']['db_schema'];
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

			/* Read old settings */
			$this->OldSetting = new $this->Setting;
			$old_account_setting = $this->OldSetting->findById(1);
			if (!$old_account_setting) {
				$this->Session->setFlash(__d('webzash', 'Could not read original settings. Please, try again.'), 'danger');
				return;
			}

			/* Connection successfull, next check if any table names clash */
			$db = ConnectionManager::getDataSource('wz_newconfig');

			if ($this->request->data['Wzaccount']['db_datasource'] == 'Database/Mysql') {
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
			}

			/**
			 * At this point the connection is successfull and there are no table clashes,
			 * we can create the application specific tables.
			 */

			/* Read the MySQL database creation schema from the Config folder */
			App::uses('File', 'Utility');
			if ($this->request->data['Wzaccount']['db_datasource'] == 'Database/Mysql') {
				$schema_filepath = App::pluginPath('Webzash') . 'Config/Schema.Mysql.sql';
			} else if ($this->request->data['Wzaccount']['db_datasource'] == 'Database/Postgres') {
				$schema_filepath = App::pluginPath('Webzash') . 'Config/Schema.Postgres.sql';
			}
			$schema_file = new File($schema_filepath, false);
			$schema = $schema_file->read(true, 'r');

			/* Add prefix to the table names in the schema */
			$prefix_schema = str_replace('%_PREFIX_%', $wz_newconfig['prefix'], $schema);

			/* Add decimal places */
			$final_schema = str_replace('%_DECIMAL_%', $old_account_setting['Setting']['decimal_places'], $prefix_schema);

			/* Create tables */
			try {
				$db->rawQuery($final_schema);
			} catch (Exception $e) {
				$this->Session->setFlash(__d('webzash', 'Oh Snap ! Something went wrong while creating the database tables. Please check your settings and try again.'), 'danger');
				return;
			}

			/******* Add initial data ********/

			/* CF groups and ledgers */
			$assetsList = new AccountList();
			$assetsList->Group = &$this->Group;
			$assetsList->Ledger = &$this->Ledger;
			$assetsList->only_opening = false;
			$assetsList->start_date = null;
			$assetsList->end_date = null;
			$assetsList->affects_gross = -1;
			$assetsList->start(1);

			$this->_extract_groups_ledgers($assetsList, true);

			$liabilitiesList = new AccountList();
			$liabilitiesList->Group = &$this->Group;
			$liabilitiesList->Ledger = &$this->Ledger;
			$liabilitiesList->only_opening = false;
			$liabilitiesList->start_date = null;
			$liabilitiesList->end_date = null;
			$liabilitiesList->affects_gross = -1;
			$liabilitiesList->start(2);

			$this->_extract_groups_ledgers($liabilitiesList, true);

			$incomesList = new AccountList();
			$incomesList->Group = &$this->Group;
			$incomesList->Ledger = &$this->Ledger;
			$incomesList->only_opening = false;
			$incomesList->start_date = null;
			$incomesList->end_date = null;
			$incomesList->affects_gross = -1;
			$incomesList->start(3);

			$this->_extract_groups_ledgers($incomesList, false);

			$expenseList = new AccountList();
			$expenseList->Group = &$this->Group;
			$expenseList->Ledger = &$this->Ledger;
			$expenseList->only_opening = false;
			$expenseList->start_date = null;
			$expenseList->end_date = null;
			$expenseList->affects_gross = -1;
			$expenseList->start(4);

			$this->_extract_groups_ledgers($expenseList, false);

			$this->NewGroup = new $this->Group;
			$this->NewGroup->useDbConfig = 'wz_newconfig';

			foreach ($this->groups_list as $row => $group) {
				$this->NewGroup->create();
				if (!$this->NewGroup->save($group, false)) {
					$this->Session->setFlash(__d('webzash', 'Account database created, but could not carry forward account groups. Please, try again.'), 'danger');
					return;
				}
			}

			$this->NewLedger = new $this->Ledger;
			$this->NewLedger->useDbConfig = 'wz_newconfig';

			foreach ($this->ledgers_list as $row => $ledger) {
				$this->NewLedger->create();
				if (!$this->NewLedger->save($ledger, false)) {
					$this->Session->setFlash(__d('webzash', 'Account database created, but could not carry forward account ledgers. Please, try again.'), 'danger');
					return;
				}
			}

			/* CF Entrytypes */
			$this->OldEntrytype = new $this->Entrytype;
			$old_entrytypes = $this->OldEntrytype->find('all');

			$this->NewEntrytype = new $this->Entrytype;
			$this->NewEntrytype->useDbConfig = 'wz_newconfig';

			foreach ($old_entrytypes as $row => $entrytype) {
				$this->NewEntrytype->create();
				if (!$this->NewEntrytype->save($entrytype)) {
					$this->Session->setFlash(__d('webzash', 'Account database created, but could not carry forward entrytypes. Please, try again.'), 'danger');
					return;
				}
			}

			/* CF Tags */
			$this->OldTag = new $this->Tag;
			$old_tags = $this->OldTag->find('all');

			$this->NewTag = new $this->Tag;
			$this->NewTag->useDbConfig = 'wz_newconfig';

			foreach ($old_tags as $row => $tag) {
				$this->NewTag->create();
				if (!$this->NewTag->save($tag)) {
					$this->Session->setFlash(__d('webzash', 'Account database created, but could not carry forward tags. Please, try again.'), 'danger');
					return;
				}
			}

			/* CF settings */
			$this->NewSetting = new $this->Setting;
			$this->NewSetting->useDbConfig = 'wz_newconfig';

			$new_account_setting = array('Setting' => array(
				'id' => '1',
				'name' => $this->request->data['Wzaccount']['name'],
				'address' => $old_account_setting['Setting']['address'],
				'email' => $old_account_setting['Setting']['email'],
				'fy_start' => dateToSql($this->request->data['Wzaccount']['fy_start']),
				'fy_end' => dateToSql($this->request->data['Wzaccount']['fy_end']),
				'currency_symbol' => $old_account_setting['Setting']['currency_symbol'],
				'currency_format' => $old_account_setting['Setting']['currency_format'],
				'decimal_places' => $old_account_setting['Setting']['decimal_places'],
				'date_format' => $this->request->data['Wzaccount']['date_format'],
				'timezone' => 'UTC',
				'manage_inventory' => 0,
				'account_locked' => 0,
				'email_use_default' => $old_account_setting['Setting']['email_use_default'],
				'email_protocol' => $old_account_setting['Setting']['email_protocol'],
				'email_host' => $old_account_setting['Setting']['email_host'],
				'email_port' => $old_account_setting['Setting']['email_port'],
				'email_tls' => $old_account_setting['Setting']['email_tls'],
				'email_username' => $old_account_setting['Setting']['email_username'],
				'email_password' => $old_account_setting['Setting']['email_password'],
				'email_from' => $old_account_setting['Setting']['email_from'],
				'print_paper_height' => $old_account_setting['Setting']['print_paper_height'],
				'print_paper_width' => $old_account_setting['Setting']['print_paper_width'],
				'print_margin_top' => $old_account_setting['Setting']['print_margin_top'],
				'print_margin_bottom' => $old_account_setting['Setting']['print_margin_bottom'],
				'print_margin_left' => $old_account_setting['Setting']['print_margin_left'],
				'print_margin_right' => $old_account_setting['Setting']['print_margin_right'],
				'print_orientation' => $old_account_setting['Setting']['print_orientation'],
				'print_page_format' => $old_account_setting['Setting']['print_page_format'],
				'database_version' => $old_account_setting['Setting']['database_version'],
				'settings' => $old_account_setting['Setting']['settings'],
			));
			$this->NewSetting->create();
			if (!$this->NewSetting->save($new_account_setting)) {
				foreach ($this->NewSetting->validationErrors as $field => $msg) {
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
				'db_schema' => $this->request->data['Wzaccount']['db_schema'],
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
		}
	}

	var $groups_list = array();
	var $ledgers_list = array();

	/**
	 * Extract the list of groups and ledgers from AccountList object
	 * and update the globla variables $group_list and $ledger_list
	 */
	public function _extract_groups_ledgers($accountlist, $calculate_closing)
	{
		if ($accountlist->id != NULL) {
			$group_item = array(
				'Group' => array(
					'id' => $accountlist->id,
					'parent_id' => $accountlist->g_parent_id,
					'name' => $accountlist->name,
					'code' => $accountlist->code,
					'affects_gross' => $accountlist->g_affects_gross,
				)
			);
			array_push($this->groups_list, $group_item);
		}
		foreach ($accountlist->children_ledgers as $row => $data)
		{
			$ledger_item = array(
				'Ledger' => array(
					'id' => $data['id'],
					'group_id' => $data['l_group_id'],
					'name' => $data['name'],
					'code' => $data['code'],
					'type' => $data['l_type'],
					'reconciliation' => $data['l_reconciliation'],
					'notes' => $data['l_notes'],
				)
			);
			if ($calculate_closing) {
				$ledger_item['Ledger']['op_balance'] = $data['cl_total'];
				$ledger_item['Ledger']['op_balance_dc'] = $data['cl_total_dc'];
			} else {
				$ledger_item['Ledger']['op_balance'] = '0.00';
				$ledger_item['Ledger']['op_balance_dc'] = 'D';
			}
			array_push($this->ledgers_list, $ledger_item);
		}
		foreach ($accountlist->children_groups as $row => $data)
		{
			$this->_extract_groups_ledgers($data, $calculate_closing);
		}
	}

/**
 * email settings method
 *
 * @return void
 */
	public function email() {

		$this->set('title_for_layout', __d('webzash', 'Email Settings'));

		$setting = $this->Setting->findById(1);
		if (!$setting) {
			$this->Session->setFlash(__d('webzash', 'Account settings not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'index'));
		}

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {

			/* Check if acccount is locked */
			if (Configure::read('Account.locked') == 1) {
				$this->Session->setFlash(__d('webzash', 'Sorry, no changes are possible since the account is locked.'), 'danger');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'index'));
			}

			/* Set setting id */
			unset($this->request->data['Setting']['id']);
			$this->Setting->id = 1;

			if (empty($this->request->data['Setting']['email_port'])) {
				$this->request->data['Setting']['email_port'] = 0;
			}

			/* Save settings */
			$ds = $this->Setting->getDataSource();
			$ds->begin();

			/* If use default email is checked then only update that field */
			if ($this->request->data['Setting']['email_use_default'] == 1) {
				if ($this->Setting->save($this->request->data, true, array('email_use_default'))) {
					$this->Log->add('Updated email settings', 1);
					$ds->commit();
					$this->Session->setFlash(__d('webzash', 'Email settings updated.'), 'success');
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'index'));
				} else {
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'Failed to update email settings. Please, try again.'), 'danger');
					return;
				}
			} else {
				if ($this->Setting->save($this->request->data, true, array('email_use_default', 'email_protocol', 'email_host', 'email_port', 'email_tls', 'email_username', 'email_password', 'email_from'))) {
					$this->Log->add('Updated email settings', 1);
					$ds->commit();
					$this->Session->setFlash(__d('webzash', 'Email settings updated.'), 'success');
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'index'));
				} else {
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'Failed to update email settings. Please, try again.'), 'danger');
					return;
				}
			}
		} else {
			$this->request->data = $setting;
			return;
		}
	}

/**
 * printer settings method
 *
 * @return void
 */
	public function printer() {

		$this->set('title_for_layout', __d('webzash', 'Printer Settings'));

		$setting = $this->Setting->findById(1);
		if (!$setting) {
			$this->Session->setFlash(__d('webzash', 'Account settings not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'index'));
		}

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {

			/* Check if acccount is locked */
			if (Configure::read('Account.locked') == 1) {
				$this->Session->setFlash(__d('webzash', 'Sorry, no changes are possible since the account is locked.'), 'danger');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'index'));
			}

			/* Set setting id */
			unset($this->request->data['Setting']['id']);
			$this->Setting->id = 1;

			/* Save settings */
			$ds = $this->Setting->getDataSource();
			$ds->begin();

			if ($this->Setting->save($this->request->data, true, array('print_paper_height', 'print_paper_width', 'print_margin_top', 'print_margin_bottom', 'print_margin_left', 'print_margin_right', 'print_orientation', 'print_page_format'))) {
				$this->Log->add('Updated printer settings', 1);
				$ds->commit();
				$this->Session->setFlash(__d('webzash', 'Printer settings updated.'), 'success');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'index'));
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'Failed to update printer settings. Please, try again.'), 'danger');
				return;
			}
		} else {
			$this->request->data = $setting;
			return;
		}
	}

/**
 * backup method
 *
 * @return void
 */
	public function backup() {
		return;
	}

/**
 * lock account method
 *
 * @return void
 */
	public function lock() {

		$this->set('title_for_layout', __d('webzash', 'Lock Account'));

		$setting = $this->Setting->findById(1);
		if (!$setting) {
			$this->Session->setFlash(__d('webzash', 'Account settings not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'index'));
		}

		$this->set('locked', $setting['Setting']['account_locked']);

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {
			/* Set setting id */
			unset($this->request->data['Setting']['id']);
			$this->Setting->id = 1;

			/* Save settings */
			$ds = $this->Setting->getDataSource();
			$ds->begin();

			if ($this->Setting->save($this->request->data, true, array('account_locked'))) {
				if ($this->request->data['Setting']['account_locked'] == '1') {
					$this->Log->add('Account locked', 1);
				} else {
					$this->Log->add('Account unlocked', 1);
				}
				$ds->commit();
				if ($this->request->data['Setting']['account_locked'] == '1') {
					$this->Session->setFlash(__d('webzash', 'Account locked.'), 'success');
				} else {
					$this->Session->setFlash(__d('webzash', 'Account unlocked.'), 'success');
				}
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'index'));
			} else {
				$ds->rollback();
				if ($this->request->data['Setting']['account_locked'] == '1') {
					$this->Session->setFlash(__d('webzash', 'Failed to lock account. Please, try again.'), 'danger');
				} else {
					$this->Session->setFlash(__d('webzash', 'Failed to unlock account. Please, try again.'), 'danger');
				}
				return;
			}
		} else {
			$this->request->data = $setting;
			return;
		}
		return;
	}

	public function beforeFilter() {
		parent::beforeFilter();

		/* Skip the ajax/javascript fields from Security component to prevent request being blackholed */
		$this->Security->unlockedActions = array('email');
	}

	/* Authorization check */
	public function isAuthorized($user) {
		if ($this->action === 'index') {
			return $this->Permission->is_allowed('change account settings');
		}

		if ($this->action === 'account') {
			return $this->Permission->is_allowed('change account settings');
		}

		if ($this->action === 'cf') {
			return $this->Permission->is_allowed('cf account');
		}

		if ($this->action === 'email') {
			return $this->Permission->is_allowed('change account settings');
		}

		if ($this->action === 'printer') {
			return $this->Permission->is_allowed('change account settings');
		}

		if ($this->action === 'backup') {
			return $this->Permission->is_allowed('backup account');
		}

		if ($this->action === 'lock') {
			return $this->Permission->is_allowed('change account settings');
		}

		return parent::isAuthorized($user);
	}
}
