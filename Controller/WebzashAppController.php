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

App::uses('AppController', 'Controller');

/**
 * Webzash App Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class WebzashAppController extends AppController {

	public $helpers = array('Webzash.Generic',
		'Html' => array('className' => 'BoostCake.BoostCakeHtml'),
		'Form' => array('className' => 'BoostCake.BoostCakeForm'),
	);

	public $components = array(
		'Security', 'Session', 'Paginator', 'Webzash.Permission', 'Webzash.Generic',
		'Webzash.CustomPaginator',
		'Auth' => array(
			'loginRedirect' => array(
				'plugin' => 'webzash',
				'controller' => 'dashboard',
				'action' => 'index',
			),
			'logoutRedirect' => array(
				'plugin' => 'webzash',
				'controller' => 'wzusers',
				'action' => 'login',
			),
			'loginAction' => array(
				'plugin' => 'webzash',
				'controller' => 'wzusers',
				'action' => 'login',
			),
			'authenticate' => array(
				'Form' => array(
					'fields' => array('username' => 'username', 'password' => 'password'),
					'userModel' => 'Wzuser',
				),
			),
			'flash' => array(
				'element' => 'danger',
				'key' => 'auth',
				'params' => array(
					'class' => 'alert-danger',
				),
			),
			'authorize' => array('Controller'),
		)
	);

	function beforeFilter() {

		/* Read URL to get the controller name */
		$url_params = Router::getParams();

		/* Load account setting only if the controller is NOT in admin sections */
		if ($url_params['controller'] == 'admin' || $url_params['controller'] == 'wzusers' ||
			$url_params['controller'] == 'wzaccounts' || $url_params['controller'] == 'wzsettings') {
			return;
		}

		if (!$this->Auth->user('id')) {
			return;
		}

		/* Load account related settings and entry types */
		$account_id = CakeSession::read('ActiveAccount.id');
		if (empty($account_id)) {
			$this->Session->setFlash(__d('webzash', 'Please choose a account.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'account'));
		}

		/* Write settings */
		App::import("Webzash.Model", "Setting");
		$Setting = new Setting();

		$setting = '';
		try {
			$setting = $Setting->findById(1);
		} catch (Exception $e) {
			CakeSession::delete('ActiveAccount.id');
			CakeSession::delete('ActiveAccount.account_role');
			$this->Session->setFlash(__d('webzash', 'Settings table is missing. Please check whether this is a valid account database.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'account'));
		}
		if (!$setting) {
			CakeSession::delete('ActiveAccount.id');
			CakeSession::delete('ActiveAccount.account_role');
			$this->Session->setFlash(__d('webzash', 'Account settings not found. Please check if the database settings are correct.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'account'));
		}

		/* Check if database version is correct */
		if ($setting['Setting']['database_version'] < 5) {
			CakeSession::delete('ActiveAccount.id');
			CakeSession::delete('ActiveAccount.account_role');
			$this->Session->setFlash(__d('webzash', 'You are connecting to a database which belongs to older version of this application. Please check the Wiki in the help section on how to upgrade your database.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'account'));
		}
		if ($setting['Setting']['database_version'] > 5) {
			CakeSession::delete('ActiveAccount.id');
			CakeSession::delete('ActiveAccount.account_role');
			$this->Session->setFlash(__d('webzash', 'You are connecting to a database which belongs to newer version of this application. Please upgrade this application before you can connect to the database.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'account'));
		}

		Configure::write('Account.name', $setting['Setting']['name']);
		Configure::write('Account.address', $setting['Setting']['address']);
		Configure::write('Account.email', $setting['Setting']['email']);
		Configure::write('Account.currency_symbol', $setting['Setting']['currency_symbol']);
		$dateFormat = explode('|', $setting['Setting']['date_format']);
		Configure::write('Account.dateformatPHP', $dateFormat[0]);
		Configure::write('Account.dateformatJS', $dateFormat[1]);
		Configure::write('Account.startdate', $setting['Setting']['fy_start']);
		Configure::write('Account.enddate', $setting['Setting']['fy_end']);
		Configure::write('Account.locked', $setting['Setting']['account_locked']);
		Configure::write('Account.email_use_default', $setting['Setting']['email_use_default']);

		/* Write entry types */
		App::import("Webzash.Model", "Entrytype");
		$Entrytype = new Entrytype();

		$rawentrytypes = '';
		try {
			$rawentrytypes = $Entrytype->find('all');
		} catch (Exception $e) {
			CakeSession::delete('ActiveAccount.id');
			CakeSession::delete('ActiveAccount.account_role');
			$this->Session->setFlash(__d('webzash', 'Entry types table is missing. Please check whether this is a valid account database.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'account'));
		}

		$entrytypes = array();
		foreach ($rawentrytypes as $entrytype) {
			$entrytypes[$entrytype['Entrytype']['id']] = array(
				'prefix' => $entrytype['Entrytype']['prefix'],
				'suffix' => $entrytype['Entrytype']['suffix'],
				'zero_padding' => $entrytype['Entrytype']['zero_padding'],
				'label' => $entrytype['Entrytype']['label'],
				'name' => $entrytype['Entrytype']['name'],
			);
		}

		Configure::write('Account.ET', $entrytypes);
	}

	public function isAuthorized($user) {
		/* Admin can access every action */
		if (isset($user['role']) && $user['role'] === 'admin') {
			return true;
		}

		/* Default deny */
		return false;
	}
}
