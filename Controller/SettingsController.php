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
class SettingsController extends AppController {

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
		return;
	}

/**
 * account settings method
 *
 * @return void
 */
	public function account() {
		$setting = $this->Setting->findById(1);
		if (!$setting) {
			$this->Session->setFlash(__d('webzash', 'Account settings not found.'), 'error');
			return $this->redirect(array('controller' => 'settings', 'action' => 'index'));
		}

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {
			/* Set setting id */
			unset($this->request->data['Setting']['id']);
			$this->Setting->id = 1;

			$settings = $this->request->data;
			$settings['Setting']['fy_start'] = dateToSql($this->request->data['Setting']['fy_start'], '00:00:00');
			$settings['Setting']['fy_end'] = dateToSql($this->request->data['Setting']['fy_end'], '23:59:59');

			/* Save settings */
			$ds = $this->Setting->getDataSource();
			$ds->begin();

			if ($this->Setting->save($settings, true, array('name', 'address', 'email', 'fy_start', 'fy_end', 'currency_symbol', 'date_format', 'timezone'))) {
				$ds->commit();
				$this->Session->setFlash(__d('webzash', 'Account settings has been updated.'), 'success');
				return $this->redirect(array('controller' => 'settings', 'action' => 'index'));
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'Account settings could not be updated. Please, try again.'), 'error');
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
		return;
	}

/**
 * email settings method
 *
 * @return void
 */
	public function email() {

		$setting = $this->Setting->findById(1);
		if (!$setting) {
			$this->Session->setFlash(__d('webzash', 'Account settings not found.'), 'error');
			return $this->redirect(array('controller' => 'settings', 'action' => 'index'));
		}

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {
			/* Set setting id */
			unset($this->request->data['Setting']['id']);
			$this->Setting->id = 1;

			/* Save settings */
			$ds = $this->Setting->getDataSource();
			$ds->begin();

			if ($this->Setting->save($this->request->data, true, array('email_protocol', 'email_host', 'email_port', 'email_username', 'email_password'))) {
				$ds->commit();
				$this->Session->setFlash(__d('webzash', 'Email settings has been updated.'), 'success');
				return $this->redirect(array('controller' => 'settings', 'action' => 'index'));
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'Email settings could not be updated. Please, try again.'), 'error');
				return;
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

		$setting = $this->Setting->findById(1);
		if (!$setting) {
			$this->Session->setFlash(__d('webzash', 'Account settings not found.'), 'error');
			return $this->redirect(array('controller' => 'settings', 'action' => 'index'));
		}

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {
			/* Set setting id */
			unset($this->request->data['Setting']['id']);
			$this->Setting->id = 1;

			/* Save settings */
			$ds = $this->Setting->getDataSource();
			$ds->begin();

			if ($this->Setting->save($this->request->data, true, array('print_paper_height', 'print_paper_width', 'print_margin_top', 'print_margin_bottom', 'print_margin_left', 'print_margin_right', 'print_orientation', 'print_page_format'))) {
				$ds->commit();
				$this->Session->setFlash(__d('webzash', 'Printer settings has been updated.'), 'success');
				return $this->redirect(array('controller' => 'settings', 'action' => 'index'));
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'Printer settings could not be updated. Please, try again.'), 'error');
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

		$setting = $this->Setting->findById(1);
		if (!$setting) {
			$this->Session->setFlash(__d('webzash', 'Account settings not found.'), 'error');
			return $this->redirect(array('controller' => 'settings', 'action' => 'index'));
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
				$ds->commit();
				if ($this->request->data['Setting']['account_locked'] == '1') {
					$this->Session->setFlash(__d('webzash', 'Account has been locked.'), 'success');
				} else {
					$this->Session->setFlash(__d('webzash', 'Account has been unlocked.'), 'success');
				}
				return $this->redirect(array('controller' => 'settings', 'action' => 'index'));
			} else {
				$ds->rollback();
				if ($this->request->data['Setting']['account_locked'] == '1') {
					$this->Session->setFlash(__d('webzash', 'Account could not be locked. Please, try again.'), 'error');
				} else {
					$this->Session->setFlash(__d('webzash', 'Account could not be unlocked. Please, try again.'), 'error');
				}
				return;
			}
		} else {
			$this->request->data = $setting;
			return;
		}
		return;
	}
}
