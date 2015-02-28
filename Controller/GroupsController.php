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
 * Webzash Plugin Groups Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class GroupsController extends WebzashAppController {

	public $uses = array('Webzash.Group', 'Webzash.Ledger', 'Webzash.Log');

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

		$this->set('title_for_layout', __d('webzash', 'Add Account Group'));

		/* Create list of parent groups */
		$parentGroups = new GroupTree();
		$parentGroups->Group = &$this->Group;
		$parentGroups->current_id = -1;
		$parentGroups->build(0);
		$parentGroups->toList($parentGroups, -1);
		$this->set('parents', $parentGroups->groupList);

		/* On POST */
		if ($this->request->is('post')) {
			$this->Group->create();
			if (!empty($this->request->data)) {
				/* Unset ID */
				unset($this->request->data['Group']['id']);

				/* If code is empty set it as NULL */
				if (empty($this->request->data['Group']['code'])) {
					$this->request->data['Group']['code'] = NULL;
				}

				/* Save group */
				$ds = $this->Group->getDataSource();
				$ds->begin();

				if ($this->Group->save($this->request->data)) {
					$this->Log->add('Added Group : ' . $this->request->data['Group']['name'], 1);
					$ds->commit();
					$this->Session->setFlash(__d('webzash', 'Account group "%s" created.', $this->request->data['Group']['name']), 'success');
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
				} else {
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'Failed to create account group. Please, try again.'), 'danger');
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
 * @throws ForbiddenException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {

		$this->set('title_for_layout', __d('webzash', 'Edit Account Group'));

		/* Check for valid group */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'Account group not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
		}
		$group = $this->Group->findById($id);
		if (!$group) {
			$this->Session->setFlash(__d('webzash', 'Account group not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
		}
		if ($id <= 4) {
			$this->Session->setFlash(__d('webzash', 'Cannot edit basic account groups.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
		}

		/* Create list of parent groups */
		$parentGroups = new GroupTree();
		$parentGroups->Group = &$this->Group;
		$parentGroups->current_id = $id;
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

			/* Set group id */
			unset($this->request->data['Group']['id']);
			$this->Group->id = $id;

			/* Check if group and parent group are not same */
			if ($id == $this->request->data['Group']['parent_id']) {
				$this->Session->setFlash(__d('webzash', 'Account group and parent group cannot be same.'), 'danger');
				return;
			}

			/* If code is empty set it as NULL */
			if (empty($this->request->data['Group']['code'])) {
				$this->request->data['Group']['code'] = NULL;
			}

			/* Save group */
			$ds = $this->Group->getDataSource();
			$ds->begin();

			if ($this->Group->save($this->request->data)) {
				$this->Log->add('Edited Group : ' . $this->request->data['Group']['name'], 1);
				$ds->commit();
				$this->Session->setFlash(__d('webzash', 'Account group "%s" updated.', $this->request->data['Group']['name']), 'success');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'Failed to update account group. Please, try again.'), 'danger');
				return;
			}
		} else {
			$this->request->data = $group;
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
			$this->Session->setFlash(__d('webzash', 'Account group not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
		}

		/* Check if group exists */
		$group = $this->Group->findById($id);
		if (!$group) {
			$this->Session->setFlash(__d('webzash', 'Account group not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
		}

		/* Check if group can be deleted */
		if ($id <= 4) {
			$this->Session->setFlash(__d('webzash', 'Cannot delete basic account groups.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
		}

		/* Check if any child groups exists */
		$child = $this->Group->find('count', array('conditions' => array('Group.parent_id' => $id)));
		if ($child > 0) {
			$this->Session->setFlash(__d('webzash', 'Account group cannot be deleted since it has one or more child group accounts still present.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
		}

		/* Check if any child ledgers exists */
		$child = $this->Ledger->find('count', array('conditions' => array('Ledger.group_id' => $id)));
		if ($child > 0) {
			$this->Session->setFlash(__d('webzash', 'Account group cannot not be deleted since it has one or more child ledger accounts still present.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
		}

		/* Delete group */
		$ds = $this->Group->getDataSource();
		$ds->begin();

		if ($this->Group->delete($id)) {
			$this->Log->add('Deleted Group : ' . $group['Group']['name'], 1);
			$ds->commit();
			$this->Session->setFlash(__d('webzash', 'Account group "%s" deleted.', $group['Group']['name']), 'success');
		} else {
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'Failed to delete account group. Please, try again.'), 'danger');
		}

		return $this->redirect(array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show'));
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
			return $this->Permission->is_allowed('add group');
		}

		if ($this->action === 'edit') {
			return $this->Permission->is_allowed('edit group');
		}

		if ($this->action === 'delete') {
			return $this->Permission->is_allowed('delete group');
		}

		return parent::isAuthorized($user);
	}
}
