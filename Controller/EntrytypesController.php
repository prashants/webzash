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
 * Webzash Plugin Entrytypes Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class EntrytypesController extends WebzashAppController {

	public $uses = array('Webzash.Entrytype', 'Webzash.Entry', 'Webzash.Log');

/**
 * index method
 *
 * @return void
 */
	public function index() {

		$this->set('title_for_layout', __d('webzash', 'Entry Types'));

		$this->set('actionlinks', array(
			array('controller' => 'entrytypes', 'action' => 'add', 'title' => __d('webzash', 'Add Entry Type')),
		));

		$this->CustomPaginator->settings = array(
			'Entrytype' => array(
				'limit' => $this->Session->read('Wzsetting.row_count'),
				'order' => array('Entrytype.id' => 'asc'),
			)
		);

		$this->set('entrytypes', $this->CustomPaginator->paginate('Entrytype'));
		return;
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {

		$this->set('title_for_layout', __d('webzash', 'Add Entry Type'));

		/* On POST */
		if ($this->request->is('post')) {
			$this->Entrytype->create();
			if (!empty($this->request->data)) {
				/* Unset ID */
				unset($this->request->data['Entrytype']['id']);

				$this->request->data['Entrytype']['base_type'] = '1'; /* Unused */

				/* If zero padding is not set or empty make it 0 */
				if (!isset($this->request->data['Entrytype']['zero_padding'])) {
					$this->request->data['Entrytype']['zero_padding'] = '0';
				}
				if (empty($this->request->data['Entrytype']['zero_padding'])) {
					$this->request->data['Entrytype']['zero_padding'] = '0';
				}

				/* Save entry type */
				$ds = $this->Entrytype->getDataSource();
				$ds->begin();

				if ($this->Entrytype->save($this->request->data)) {
					$this->Log->add('Added Entry Type : ' . $this->request->data['Entrytype']['name'], 1);
					$ds->commit();
					$this->Session->setFlash(__d('webzash', 'Entry Type "%s" created.', $this->request->data['Entrytype']['name']), 'success');
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entrytypes', 'action' => 'index'));
				} else {
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'Failed to create entry type. Please, try again.'), 'danger');
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

		$this->set('title_for_layout', __d('webzash', 'Edit Entry Type'));

		/* Check for valid entry type */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'Entry type not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entrytypes', 'action' => 'index'));
		}
		$entrytype = $this->Entrytype->findById($id);
		if (!$entrytype) {
			$this->Session->setFlash(__d('webzash', 'Entry type not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entrytypes', 'action' => 'index'));
		}

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {

			/* Check if acccount is locked */
			if (Configure::read('Account.locked') == 1) {
				$this->Session->setFlash(__d('webzash', 'Sorry, no changes are possible since the account is locked.'), 'danger');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entrytypes', 'action' => 'index'));
			}

			/* Set entry type id */
			unset($this->request->data['Entrytype']['id']);
			$this->Entrytype->id = $id;

			$this->request->data['Entrytype']['base_type'] = '1'; /* Unused */

			/* If zero padding is not set or empty make it 0 */
			if (!isset($this->request->data['Entrytype']['zero_padding'])) {
				$this->request->data['Entrytype']['zero_padding'] = '0';
			}
			if (empty($this->request->data['Entrytype']['zero_padding'])) {
				$this->request->data['Entrytype']['zero_padding'] = '0';
			}

			/* Save entry type */
			$ds = $this->Entrytype->getDataSource();
			$ds->begin();

			if ($this->Entrytype->save($this->request->data)) {
				$this->Log->add('Edited Entrytype : ' . $this->request->data['Entrytype']['name'], 1);
				$ds->commit();
				$this->Session->setFlash(__d('webzash', 'Entry type "%s" updated.', $this->request->data['Entrytype']['name']), 'success');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entrytypes', 'action' => 'index'));
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'Failed to update entry type. Please, try again.'), 'danger');
				return;
			}
		} else {
			$this->request->data = $entrytype;
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
			$this->Session->setFlash(__d('webzash', 'Entry type not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entrytypes', 'action' => 'index'));
		}

		/* Check if entry type exists */
		$entrytype = $this->Entrytype->findById($id);
		if (!$entrytype) {
			$this->Session->setFlash(__d('webzash', 'Entry type not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entrytypes', 'action' => 'index'));
		}

		/* Check if any entry using the entry type exists */
		$entries = $this->Entry->find('count', array('conditions' => array('Entry.entrytype_id' => $id)));
		if ($entries > 0) {
			$this->Session->setFlash(__d('webzash', 'Entry type cannot be deleted since one or more entries are still using it.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entrytypes', 'action' => 'index'));
		}

		/* Delete entry type */
		$ds = $this->Entrytype->getDataSource();
		$ds->begin();

		if ($this->Entrytype->delete($id)) {
			$this->Log->add('Deleted Entrytype : ' . $entrytype['Entrytype']['name'], 1);
			$ds->commit();
			$this->Session->setFlash(__d('webzash', 'Entry type "%s" deleted.', $entrytype['Entrytype']['name']), 'success');
		} else {
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'Failed to delete entry type. Please, try again.'), 'danger');
		}

		return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entrytypes', 'action' => 'index'));
	}

	function beforeFilter() {
		parent::beforeFilter();

		/* Check if acccount is locked */
		if (Configure::read('Account.locked') == 1) {
			if ($this->action == 'add' || $this->action == 'delete') {
				$this->Session->setFlash(__d('webzash', 'Sorry, no changes are possible since the account is locked.'), 'danger');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entrytypes', 'action' => 'index'));
			}
		}
	}

	/* Authorization check */
	public function isAuthorized($user) {
		if ($this->action === 'index') {
			return $this->Permission->is_allowed('view entrytype');
		}

		if ($this->action === 'add') {
			return $this->Permission->is_allowed('add entrytype');
		}

		if ($this->action === 'edit') {
			return $this->Permission->is_allowed('edit entrytype');
		}

		if ($this->action === 'delete') {
			return $this->Permission->is_allowed('delete entrytype');
		}

		return parent::isAuthorized($user);
	}
}
