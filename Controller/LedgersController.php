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
App::uses('GroupTree', 'Webzash.Lib');

/**
 * Webzash Plugin Ledgers Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class LedgersController extends WebzashAppController {

	public $uses = array('Webzash.Ledger', 'Webzash.Group', 'Webzash.Entryitem',
		'Webzash.Log');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {

		$this->set('title_for_layout', __d('webzash', 'Add Account Ledger'));

		/* Create list of parent groups */
		$parentGroups = new GroupTree();
		$parentGroups->Group = &$this->Group;
		$parentGroups->current_id = -1;
		$parentGroups->build(0);
		$parentGroups->toList($parentGroups, -1);
		$this->set('parents', $parentGroups->groupList);

		/* On POST */
		if ($this->request->is('post')) {
			$this->Ledger->create();
			if (!empty($this->request->data)) {
				/* Unset ID */
				unset($this->request->data['Ledger']['id']);

				/* If code is empty set it as NULL */
				if (strlen($this->request->data['Ledger']['code']) <= 0) {
					$this->request->data['Ledger']['code'] = NULL;
				}

				/* If opening balance is not set or empty make it 0 */
				if (empty($this->request->data['Ledger']['op_balance'])) {
					$this->request->data['Ledger']['op_balance'] = 0;
				}

				/* Count number of decimal places */
				if (countDecimal($this->request->data['Ledger']['op_balance']) > Configure::read('Account.decimal_places')) {
					$this->Session->setFlash(__d('webzash', 'Invalid amount specified. Maximum %s decimal places allowed.', Configure::read('Account.decimal_places')), 'danger');
					return;
				}

				/* Save ledger */
				$ds = $this->Ledger->getDataSource();
				$ds->begin();

				if ($this->Ledger->save($this->request->data)) {
					$this->Log->add('Added Ledger : ' . $this->request->data['Ledger']['name'], 1);
					$ds->commit();
					$this->Session->setFlash(__d('webzash', 'Account ledger "%s" created.', $this->request->data['Ledger']['name']), 'success');
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
				} else {
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'Failed to create account ledger. Please, try again.'), 'danger');
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
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {

		$this->set('title_for_layout', __d('webzash', 'Edit Account Ledger'));

		/* Check for valid ledger */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'Account ledger not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
		}
		$ledger = $this->Ledger->findById($id);
		if (!$ledger) {
			$this->Session->setFlash(__d('webzash', 'Account ledger not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
		}

		/* Create list of parent groups */
		$parentGroups = new GroupTree();
		$parentGroups->Group = &$this->Group;
		$parentGroups->current_id = -1;
		$parentGroups->build(0);
		$parentGroups->toList($parentGroups, -1);
		$this->set('parents', $parentGroups->groupList);

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {

			/* Check if acccount is locked */
			if (Configure::read('Account.locked') == 1) {
				$this->Session->setFlash(__d('webzash', 'Sorry, no changes are possible since the account is locked.'), 'danger');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
			}

			/* If code is empty set it as NULL */
			if (strlen($this->request->data['Ledger']['code']) <= 0) {
				$this->request->data['Ledger']['code'] = NULL;
			}

			/* If opening balance is not set or empty make it 0 */
			if (empty($this->request->data['Ledger']['op_balance'])) {
				$this->request->data['Ledger']['op_balance'] = 0;
			}

			/* Count number of decimal places */
			if (countDecimal($this->request->data['Ledger']['op_balance']) > Configure::read('Account.decimal_places')) {
				$this->Session->setFlash(__d('webzash', 'Invalid amount specified. Maximum %s decimal places allowed.', Configure::read('Account.decimal_places')), 'danger');
				return;
			}

			/* Set ledger id */
			unset($this->request->data['Ledger']['id']);
			$this->Ledger->id = $id;

			/* Save ledger */
			$ds = $this->Ledger->getDataSource();
			$ds->begin();

			if ($this->Ledger->save($this->request->data)) {
				$this->Log->add('Edited Ledger : ' . $this->request->data['Ledger']['name'], 1);
				$ds->commit();
				$this->Session->setFlash(__d('webzash', 'Account ledger "%s" updated.', $this->request->data['Ledger']['name']), 'success');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'Failed to update account ledger. Please, try again.'), 'danger');
				return;
			}
		} else {
			$this->request->data = $ledger;
			return;
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {

		/* GET access not allowed */
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}

		/* Check if valid id */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'Account ledger not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
		}

		/* Check if ledger exists */
		$ledger = $this->Ledger->findById($id);
		if (!$ledger) {
			$this->Session->setFlash(__d('webzash', 'Account ledger not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
		}

		/* Check if any entry item using this ledger still exists */
		$entries = $this->Entryitem->find('count', array('conditions' => array('Entryitem.ledger_id' => $id)));
		if ($entries > 0) {
			$this->Session->setFlash(__d('webzash', 'Account ledger cannot not be deleted since it has one or more entries still present.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
		}

		/* Delete ledger */
		$ds = $this->Ledger->getDataSource();
		$ds->begin();

		if ($this->Ledger->delete($id)) {
			$this->Log->add('Deleted Ledger : ' . $ledger['Ledger']['name'], 1);
			$ds->commit();
			$this->Session->setFlash(__d('webzash', 'Account ledger "%s" deleted.', $ledger['Ledger']['name']), 'success');
		} else {
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'Failed to delete account ledger. Please, try again.'), 'danger');
		}

		return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
	}

/**
 * closing balance method
 *
 * Return closing balance for the ledger
 *
 * @return void
 */
	public function cl() {
		$this->layout = null;

		/* Read ledger id from url get request */
		$id = (int)$this->request->query('id');

		/* Check if valid id */
		if (!$id) {
			$this->set('cl', array('cl' => array('dc' => '', 'amount' => '')));
			return;
		}

		/* Check if ledger exists */
		$ledger = $this->Ledger->findById($id);
		if (!$ledger) {
			$this->set('cl', array('cl' => array('dc' => '', 'amount' => '')));
			return;
		}

		$cl = $this->Ledger->closingBalance($id);

		$status = 'ok';
		/* If its a cash or bank account and closing balance is Cr then negative balance */
		if ($ledger['Ledger']['type'] == 1) {
			if ($cl['dc'] == 'C') {
				$status = 'neg';
			}
		}

		/* Return closing balance */
		$this->set('cl', array('cl' => array(
			'dc' => $cl['dc'],
			'amount' => $cl['amount'],
			'status' => $status,
		)));

		return;
	}

	function beforeFilter() {
		parent::beforeFilter();

		/* Check if acccount is locked */
		if (Configure::read('Account.locked') == 1) {
			if ($this->action == 'add' || $this->action == 'delete') {
				$this->Session->setFlash(__d('webzash', 'Sorry, no changes are possible since the account is locked.'), 'danger');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
			}
		}
	}

	/* Authorization check */
	public function isAuthorized($user) {
		if ($this->action === 'add') {
			return $this->Permission->is_allowed('add ledger');
		}

		if ($this->action === 'edit') {
			return $this->Permission->is_allowed('edit ledger');
		}

		if ($this->action === 'delete') {
			return $this->Permission->is_allowed('delete ledger');
		}

		return parent::isAuthorized($user);
	}
}
