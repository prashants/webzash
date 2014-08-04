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
 * Webzash Plugin Wzusers Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class WzusersController extends WebzashAppController {

	public $components = array('Session', 'Paginator', 'Security', 'Webzash.Permission');

	public $helpers = array('Webzash.Generic');

	var $layout = 'manage';

/**
 * index method
 *
 * @return void
 */
	public function index() {

		$this->Wzuser->useDbConfig = 'wz';

		$this->set('actionlinks', array(
			array('controller' => 'wzusers', 'action' => 'add', 'title' => __d('webzash', 'Add User')),
			array('controller' => 'admin', 'action' => 'index', 'title' => __d('webzash', 'Back')),
		));

		$this->Paginator->settings = array(
			'Wzuser' => array(
				'limit' => 10,
				'order' => array('Wzuser.user_name' => 'desc'),
			)
		);

		$this->set('wzusers', $this->Paginator->paginate('Wzuser'));

		return;
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {

		$this->Wzuser->useDbConfig = 'wz';

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Wzaccount");
		$this->Wzaccount = new Wzaccount();
		$this->Wzaccount->useDbConfig = 'wz';

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Wzsetting");
		$this->Wzsetting = new Wzsetting();
		$this->Wzsetting->useDbConfig = 'wz';

		$wxsetting = $this->Wzsetting->findById(1);
		if (!$wxsetting) {
			$this->Session->setFlash(__d('webzash', 'Please update your setting below before adding any user'), 'error');
			return $this->redirect(array('controller' => 'wzsettings', 'action' => 'edit'));
		}

		/* Create list of wzaccounts */
		$wzaccounts = $this->Wzaccount->find('list', array(
			'fields' => array('Wzaccount.id', 'Wzaccount.name'),
			'order' => array('Wzaccount.name')
		));
		$this->set('wzaccounts', $wzaccounts);

		/* On POST */
		if ($this->request->is('post')) {
			$this->Wzuser->create();
			if (!empty($this->request->data)) {
				/* Unset ID */
				unset($this->request->data['Wzuser']['id']);

				$temp_password = $this->request->data['Wzuser']['password'];
				$this->request->data['Wzuser']['password'] = Security::hash($this->request->data['Wzuser']['password']);

				$this->request->data['Wzuser']['verification_key'] = Security::hash(uniqid() . uniqid());

				/* Save user */
				$ds = $this->Wzuser->getDataSource();
				$ds->begin();

				if ($this->Wzuser->save($this->request->data)) {
					$ds->commit();
					$this->Session->setFlash(__d('webzash', 'The user account has been created.'), 'success');
					return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
				} else {
					$this->request->data['Wzuser']['password'] = $temp_password;
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'The user account could not be saved. Please, try again.'), 'error');
					return;
				}
			} else {
				$this->Session->setFlash(__d('webzash', 'No data. Please, try again.'), 'error');
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

		$this->Wzuser->useDbConfig = 'wz';

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Wzaccount");
		$this->Wzaccount = new Wzaccount();
		$this->Wzaccount->useDbConfig = 'wz';

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Wzsetting");
		$this->Wzsetting = new Wzsetting();
		$this->Wzsetting->useDbConfig = 'wz';

		$wxsetting = $this->Wzsetting->findById(1);
		if (!$wxsetting) {
			$this->Session->setFlash(__d('webzash', 'Please update your setting below before editing any user'), 'error');
			return $this->redirect(array('controller' => 'wzsettings', 'action' => 'edit'));
		}

		/* Check for valid user */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'User account not specified.'), 'error');
			return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
		}
		$wzuser = $this->Wzuser->findById($id);
		if (!$wzuser) {
			$this->Session->setFlash(__d('webzash', 'User account not found.'), 'error');
			return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
		}

		/* Create list of wzaccounts */
		$wzaccounts = $this->Wzaccount->find('list', array(
			'fields' => array('Wzaccount.id', 'Wzaccount.name'),
			'order' => array('Wzaccount.name')
		));
		$this->set('wzaccounts', $wzaccounts);

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {
			/* Set user id */
			unset($this->request->data['Wzuser']['id']);
			$this->Wzuser->id = $id;

			/* Save user */
			$ds = $this->Wzuser->getDataSource();
			$ds->begin();

			$this->request->data['Wzuser']['verification_key'] = Security::hash(uniqid() . uniqid());

			if ($this->Wzuser->save($this->request->data, true, array('user_name', 'full_name', 'email', 'role', 'status', 'verification_key'))) {
				$ds->commit();
				$this->Session->setFlash(__d('webzash', 'The user account has been updated.'), 'success');
				return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
			} else {
				$this->request->data['Wzuser']['password'] = $temp_password;
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'The user account could not be updated. Please, try again.'), 'error');
				return;
			}
		} else {
			$this->request->data = $wzuser;
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

		$this->Wzuser->useDbConfig = 'wz';

		/* Check if valid id */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'User account not specified.'), 'error');
			return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
		}

		/* Check if user exists */
		if (!$this->Wzuser->exists($id)) {
			$this->Session->setFlash(__d('webzash', 'User account not found.'), 'error');
			return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
		}

		/* TODO : Cannot delete your own account */

		/* Delete user */
		$ds = $this->Wzuser->getDataSource();
		$ds->begin();

		if ($this->Wzuser->delete($id)) {
			$ds->commit();
			$this->Session->setFlash(__d('webzash', 'The user account has been deleted.'), 'success');
		} else {
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'The user account could not be deleted. Please, try again.'), 'error');
		}

		return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
	}

}
