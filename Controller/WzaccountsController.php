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

	var $layout = 'manage';

/**
 * index method
 *
 * @return void
 */
	public function index() {

		$this->Wzaccount->useDbConfig = 'wz';

		$this->set('actionlinks', array(
			array('controller' => 'wzaccounts', 'action' => 'add', 'title' => __d('webzash', 'Add Account Config')),
			array('controller' => 'wzaccounts', 'action' => 'create', 'title' => __d('webzash', 'Create Account')),
			array('controller' => 'admin', 'action' => 'index', 'title' => __d('webzash', 'Back')),
		));

		$this->Paginator->settings = array(
			'Wzaccount' => array(
				'limit' => 10,
				'order' => array('Wzaccount.name' => 'desc'),
			)
		);

		$this->set('wzaccounts', $this->Paginator->paginate('Wzaccount'));

		return;
	}

/**
 * create method
 *
 * @return void
 */
	public function create() {
		$this->set('actionlinks', array(
			array('controller' => 'admin', 'action' => 'index', 'title' => __d('webzash', 'Back')),
		));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {

		$this->Wzaccount->useDbConfig = 'wz';

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Wzuser");
		$this->Wzuser = new Wzuser();
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

				/* Save user */
				$ds = $this->Wzaccount->getDataSource();
				$ds->begin();

				if ($this->Wzaccount->save($this->request->data)) {
					$ds->commit();
					$this->Session->setFlash(__d('webzash', 'The account config has been created.'), 'success');
					return $this->redirect(array('controller' => 'wzaccounts', 'action' => 'index'));
				} else {
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'The account config could not be saved. Please, try again.'), 'error');
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

		$this->Wzaccount->useDbConfig = 'wz';

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Wzuser");
		$this->Wzuser = new Wzuser();
		$this->Wzuser->useDbConfig = 'wz';

		/* Check for valid account */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'Account not specified.'), 'error');
			return $this->redirect(array('controller' => 'wzaccounts', 'action' => 'index'));
		}
		$wzaccount = $this->Wzaccount->findById($id);
		if (!$wzaccount) {
			$this->Session->setFlash(__d('webzash', 'Account not found.'), 'error');
			return $this->redirect(array('controller' => 'wzaccounts', 'action' => 'index'));
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
				$this->Session->setFlash(__d('webzash', 'The account config has been updated.'), 'success');
				return $this->redirect(array('controller' => 'wzaccounts', 'action' => 'index'));
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'The account config could not be updated. Please, try again.'), 'error');
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

		/* Check if valid id */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'Account not specified.'), 'error');
			return $this->redirect(array('controller' => 'wzaccounts', 'action' => 'index'));
		}

		/* Check if account exists */
		if (!$this->Wzaccount->exists($id)) {
			$this->Session->setFlash(__d('webzash', 'Account not found.'), 'error');
			return $this->redirect(array('controller' => 'wzaccounts', 'action' => 'index'));
		}

		/* Delete account */
		$ds = $this->Wzaccount->getDataSource();
		$ds->begin();

		/* TODO : Delete database */
		if ($this->Wzaccount->delete($id)) {
			$ds->commit();
			$this->Session->setFlash(__d('webzash', 'The account config has been deleted. Please delete the account data manually.'), 'success');
		} else {
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'The account config could not be deleted. Please, try again.'), 'error');
		}

		return $this->redirect(array('controller' => 'wzaccounts', 'action' => 'index'));
	}

}
