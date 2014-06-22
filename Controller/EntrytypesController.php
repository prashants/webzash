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

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->set('actionlinks', array(
			array('controller' => 'entrytypes', 'action' => 'add', 'title' => __('Add Entry Type')),
		));
		$this->set('entrytypes', $this->Entrytype->find('all', array('order' => array('Entrytype.id'))));
		return;
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {

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
				if ($this->Entrytype->save($this->request->data)) {
					$this->Session->setFlash(__('The entry type has been created.'), 'default', array(), 'success');
					return $this->redirect(array('controller' => 'entrytypes', 'action' => 'index'));
				} else { return;
					$this->Session->setFlash(__('The entry type could not be saved. Please, try again.'), 'default', array(), 'error');
					return;
				}
			} else {
				$this->Session->setFlash(__('No data. Please, try again.'), 'default', array(), 'error');
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
		/* Check for valid entry type */
		if (!$id) {
			throw new NotFoundException(__('Invalid entry type.'));
		}
		$entrytype = $this->Entrytype->findById($id);
		if (!$entrytype) {
			throw new NotFoundException(__('Invalid entry type.'));
		}

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {
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
			if ($this->Entrytype->save($this->request->data)) {
				$this->Session->setFlash(__('The entry type has been updated.'), 'default', array(), 'success');
				return $this->redirect(array('controller' => 'entrytypes', 'action' => 'index'));
			} else {
				$this->Session->setFlash(__('The entry type could not be updated. Please, try again.'), 'default', array(), 'error');
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
		if (!$id) {
			throw new NotFoundException(__('Invalid entry type.'));
		}

		/* Check if entry type exists */
		if (!$this->Entrytype->exists($id)) {
			throw new NotFoundException(__('Invalid entry type.'));
		}

		/* TODO : Check if any vouchers using the entry type exists */
		/*
		$active = $this->Voucher->find('count', array('conditions' => array('entrytype_id' => $id)));
		if ($active > 0) {
			$this->Session->setFlash(__('The entry type cannot be deleted since one or more vouchers are using it.'), 'default', array(), 'error');
			return $this->redirect(array('controller' => 'entrytypes', 'action' => 'index'));
		}
		*/

		/* Delete entry type */
		if ($this->Entrytype->delete($id)) {
			$this->Session->setFlash(__('The entry type has been deleted.'), 'default', array(), 'success');
		} else {
			$this->Session->setFlash(__('The entry type could not be deleted. Please, try again.'), 'default', array(), 'error');
		}

		return $this->redirect(array('controller' => 'entrytypes', 'action' => 'index'));
	}

}
