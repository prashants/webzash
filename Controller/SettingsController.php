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
 * Webzash Plugin Settings Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class SettingsController extends WebzashAppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	public $helpers = array('Webzash.Timezone');

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

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Entry");
		$this->Entry = new Entry();

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Log");
		$this->Log = new Log();

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
			$temp = $this->Entry->find('count', array(
				'conditions' => array(
					'OR' => array(
						'Entry.date <' => dateToSql($this->request->data['Setting']['fy_start']),
						'Entry.date >' => dateToSql($this->request->data['Setting']['fy_end']),
					),
				),
			));
			if ($temp != 0) {
				$this->Session->setFlash(__d('webzash', 'Failed to update account setting since there are %d entries beyond the selected financial year start and end dates.', $temp), 'danger');
				return;
			}

			/* Save settings */
			$ds = $this->Setting->getDataSource();
			$ds->begin();

			if ($this->Setting->save($settings, true, array('name', 'address', 'email', 'fy_start', 'fy_end', 'currency_symbol', 'date_format'))) {
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

		$this->set('title_for_layout', __d('webzash', 'Carry Forward'));

		return;
	}

/**
 * email settings method
 *
 * @return void
 */
	public function email() {

		$this->set('title_for_layout', __d('webzash', 'Email Settings'));

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Log");
		$this->Log = new Log();

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

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Log");
		$this->Log = new Log();

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

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Log");
		$this->Log = new Log();

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
