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

/**
 * Webzash Plugin Logs Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class LogsController extends WebzashAppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {

		$this->set('title_for_layout', __d('webzash', 'Logs'));

		$this->set('actionlinks', array(
			array('controller' => 'logs', 'action' => 'clear', 'title' => __d('webzash', 'Clear Logs')),
			array('controller' => 'dashboard', 'action' => 'index', 'title' => __d('webzash', 'Back')),
		));

		$this->CustomPaginator->settings = array(
			'Log' => array(
				'limit' => 50,
				'order' => array('Log.date' => 'asc'),
			)
		);

		$this->set('logs', $this->CustomPaginator->paginate('Log'));
		return;
	}

	public function clear() {

		if ($this->Log->deleteAll(array('1 = 1'))) {
			$this->Session->setFlash(__d('webzash', 'Logs cleared.'), 'success');
		} else {
			$this->Session->setFlash(__d('webzash', 'Failed to clear logs. Please, try again.'), 'danger');
		}

		return $this->redirect(array('plugin' => 'webzash', 'controller' => 'dashboard', 'action' => 'index'));
	}

	function beforeFilter() {
		parent::beforeFilter();

		/* Check if acccount is locked */
		if (Configure::read('Account.locked') == 1) {
			if ($this->action == 'clear') {
				$this->Session->setFlash(__d('webzash', 'Sorry, no changes are possible since the account is locked.'), 'danger');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'logs', 'action' => 'index'));
			}
		}
	}

	/* Authorization check */
	public function isAuthorized($user) {
		if ($this->action === 'index') {
			return $this->Permission->is_allowed('view log');
		}

		if ($this->action === 'clear') {
			return $this->Permission->is_allowed('clear log');
		}

		return parent::isAuthorized($user);
	}
}
