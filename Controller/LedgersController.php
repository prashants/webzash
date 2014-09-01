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
 * Webzash Plugin Ledgers Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class LedgersController extends WebzashAppController {

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

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Group");
		$this->Group = new Group();

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Log");
		$this->Log = new Log();

		/* Create list of parent groups */
		$parents = $this->Group->find('list', array(
			'fields' => array('Group.id', 'Group.name'),
			'order' => array('Group.name')
		));
		$this->set('parents', $parents);

		/* On POST */
		if ($this->request->is('post')) {
			$this->Ledger->create();
			if (!empty($this->request->data)) {
				/* Unset ID */
				unset($this->request->data['Ledger']['id']);

				/* If opening balance is not set or empty make it 0 */
				if (!isset($this->request->data['Ledger']['op_balance'])) {
					$this->request->data['Ledger']['op_balance'] = 0;
				}
				if (empty($this->request->data['Ledger']['op_balance'])) {
					$this->request->data['Ledger']['op_balance'] = 0;
				}

				/* Save ledger */
				$ds = $this->Ledger->getDataSource();
				$ds->begin();

				if ($this->Ledger->save($this->request->data)) {
					$this->Log->add('Added Ledger : ' . $this->request->data['Ledger']['name'], 1);
					$ds->commit();
					$this->Session->setFlash(__d('webzash', 'The account ledger has been created.'), 'success');
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
				} else {
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'The account ledger could not be saved. Please, try again.'), 'danger');
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

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Group");
		$this->Group = new Group();

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Log");
		$this->Log = new Log();

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
		$parents = $this->Group->find('list', array(
			'fields' => array('Group.id', 'Group.name'),
			'order' => array('Group.name')
		));
		$this->set('parents', $parents);

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {
			/* Set ledger id */
			unset($this->request->data['Ledger']['id']);
			$this->Ledger->id = $id;

			/* Save ledger */
			$ds = $this->Ledger->getDataSource();
			$ds->begin();

			if ($this->Ledger->save($this->request->data)) {
				$this->Log->add('Edited Ledger : ' . $this->request->data['Ledger']['name'], 1);
				$ds->commit();
				$this->Session->setFlash(__d('webzash', 'The account ledger has been updated.'), 'success');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'The account ledger could not be updated. Please, try again.'), 'danger');
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

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Entryitem");
		$this->Entryitem = new Entryitem();

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Log");
		$this->Log = new Log();

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
			$this->Session->setFlash(__d('webzash', 'The account ledger cannot not be deleted since it has one or more entries still present.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
		}

		/* Delete ledger */
		$ds = $this->Ledger->getDataSource();
		$ds->begin();

		if ($this->Ledger->delete($id)) {
			$this->Log->add('Deleted Ledger : ' . $ledger['Ledger']['name'], 1);
			$ds->commit();
			$this->Session->setFlash(__d('webzash', 'The account ledger has been deleted.'), 'success');
		} else {
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'The account ledger could not be deleted. Please, try again.'), 'danger');
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
			$this->set('cl', array('cl' => array('dc' => '', 'balance' => '')));
			return;
		}

		/* Check if ledger exists */
		$ledger = $this->Ledger->findById($id);
		if (!$ledger) {
			$this->set('cl', array('cl' => array('dc' => '', 'balance' => '')));
			return;
		}

		$cl = closingBalance($id);

		/* Return closing balance */
		$this->set('cl', array('cl' => array(
			'dc' => $cl['dc'],
			'balance' => $cl['balance'],
		)));

		return;
	}

	/* Authorization check */
	public function isAuthorized($user) {
		if ($this->action === 'add') {
			return $this->Permission->is_allowed('add ledger', $user['role']);
		}

		if ($this->action === 'edit') {
			return $this->Permission->is_allowed('edit ledger', $user['role']);
		}

		if ($this->action === 'delete') {
			return $this->Permission->is_allowed('delete ledger', $user['role']);
		}

		return parent::isAuthorized($user);
	}
}
