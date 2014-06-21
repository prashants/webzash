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
 * Webzash Plugin Tags Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class TagsController extends WebzashAppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->set('actionlinks', array(
			array('controller' => 'tags', 'action' => 'add', 'title' => __('Add Tag')),
		));
		$this->set('tags', $this->Tag->find('all', array('order' => array('Tag.title'))));
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
			$this->Tag->create();
			if (!empty($this->request->data)) {
				/* Unset ID */
				unset($this->request->data['Tag']['id']);

				/* Save tag */
				if ($this->Tag->save($this->request->data)) {
					$this->Session->setFlash(__('The tag has been created.'), 'default', array(), 'success');
					return $this->redirect(array('controller' => 'tags', 'action' => 'index'));
				} else {
					$this->Session->setFlash(__('The tag could not be saved. Please, try again.'), 'default', array(), 'error');
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
		/* Check for valid tag */
		if (!$id) {
			throw new NotFoundException(__('Invalid tag.'));
		}
		$tag = $this->Tag->findById($id);
		if (!$tag) {
			throw new NotFoundException(__('Invalid tag.'));
		}

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {
			/* Set tag id */
			unset($this->request->data['Tag']['id']);
			$this->Tag->id = $id;

			/* Save tag */
			if ($this->Tag->save($this->request->data)) {
				$this->Session->setFlash(__('The tag has been updated.'), 'default', array(), 'success');
				return $this->redirect(array('controller' => 'tags', 'action' => 'index'));
			} else {
				$this->Session->setFlash(__('The tag could not be updated. Please, try again.'), 'default', array(), 'error');
				return;
			}
		} else {
			$this->request->data = $tag;
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
			//throw new MethodNotAllowedException();
		}

		/* Check if valid id */
		if (!$id) {
			throw new NotFoundException(__('Invalid tag.'));
		}

		/* Check if tag exists */
		if (!$this->Tag->exists($id)) {
			throw new NotFoundException(__('Invalid tag.'));
		}

		/* TODO : Check if any vouchers using the tag exists */
		/*
		$active = $this->Voucher->find('count', array('conditions' => array('tag_id' => $id)));
		if ($active > 0) {
			$this->Session->setFlash(__('The tag cannot be deleted since one or more vouchers are using it.'), 'default', array(), 'error');
			return $this->redirect(array('controller' => 'tags', 'action' => 'index'));
		}
		*/

		/* Delete tag */
		if ($this->Tag->delete($id)) {
			$this->Session->setFlash(__('The tag has been deleted.'), 'default', array(), 'success');
		} else {
			$this->Session->setFlash(__('The tag could not be deleted. Please, try again.'), 'default', array(), 'error');
		}

		return $this->redirect(array('controller' => 'tags', 'action' => 'index'));
	}

}
