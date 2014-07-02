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
 * Webzash Plugin Entries Controller
 *
 * @package Webzash
 * @subpackage Webzash.Controllers
 */
class EntriesController extends WebzashAppController {


	public $helpers = array('Webzash.Generic');

	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {

		/* Setup pagination */
		$this->Paginator->settings = array(
			'Entry' => array(
				'limit' => 10,
				'order' => array('Entry.date' => 'desc'),
			)
		);

		$this->set('entries', $this->Paginator->paginate('Entry'));
		return;
	}

/**
 * show method
 *
 * @return void
 */
	public function show($entrytypeLabel = null) {
		$this->loadModel('Entrytype');

		/* Check for valid entry type */
		if (empty($entrytypeLabel)) {
			$this->Session->setFlash(__('Entry type not specified. Showing all entries.'), 'error');
			return $this->redirect(array('controller' => 'entries', 'action' => 'index'));
		}
		$entrytype = $this->Entrytype->find('first', array('conditions' => array('Entrytype.label' => $entrytypeLabel)));
		if (!$entrytype) {
			$this->Session->setFlash(__('Entry type not found. Showing all entries.'), 'error');
			return $this->redirect(array('controller' => 'entries', 'action' => 'index'));
		}

		$this->set('actionlinks', array(
			array('controller' => 'entries', 'action' => 'add', 'data' => $entrytype['Entrytype']['label'], 'title' => __('Add ') . $entrytype['Entrytype']['name']),
		));

		/* Setup pagination */
		$this->Paginator->settings = array(
			'Entry' => array(
				'limit' => 5,
				'conditions' => array('Entry.entrytype_id' => $entrytype['Entrytype']['id']),
				'order' => array('Entry.date' => 'desc'),
			)
		);

		$this->set('entries', $this->Paginator->paginate('Entry'));

		$this->set('entrytype', $entrytype);

		return;
	}

/**
 * add method
 *
 * @return void
 */
	public function add($entrytypeLabel = null) {

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

	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function delete($entrytypeLabel = null, $id = null) {
		$this->loadModel('Entryitem');
		$this->loadModel('Entrytype');

		/* Check for valid entry type */
		if (empty($entrytypeLabel)) {
			$this->Session->setFlash(__('Entry type not specified. Showing all entries.'), 'error');
			return $this->redirect(array('controller' => 'entries', 'action' => 'index'));
		}
		$entrytype = $this->Entrytype->find('first', array('conditions' => array('Entrytype.label' => $entrytypeLabel)));
		if (!$entrytype) {
			$this->Session->setFlash(__('Entry type not found. Showing all entries.'), 'error');
			return $this->redirect(array('controller' => 'entries', 'action' => 'index'));
		}

		/* GET access not allowed */
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}

		/* Check if valid id */
		if (empty($id)) {
			$this->Session->setFlash(__('Entry not specified.'), 'error');
			return $this->redirect(array('controller' => 'entries', 'action' => 'index'));
		}

		/* Check if entry exists */
		if (!$this->Entry->exists($id)) {
			$this->Session->setFlash(__('Entry not found.'), 'error');
			return $this->redirect(array('controller' => 'entries', 'action' => 'index'));
		}

		/* Delete entry items */
		if (!$this->Entryitem->deleteAll(array('Entryitem.entry_id' => $id))) {
			$this->Session->setFlash(__('The entry ledgers could not be deleted. Please, try again.'), 'error');
			return $this->redirect(array('controller' => 'entries', 'action' => 'show', $entrytype['Entrytype']['label']));
		}

		/* Delete entry */
		if ($this->Entry->delete($id)) {
			$this->Session->setFlash(__('The entry has been deleted.'), 'success');
		} else {
			$this->Session->setFlash(__('The entry could not be deleted. Please, try again.'), 'error');
		}

		return $this->redirect(array('controller' => 'entries', 'action' => 'show', $entrytype['Entrytype']['label']));
	}
}
