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

		$this->set('title_for_layout', __d('webzash', 'Tags'));

		$this->set('actionlinks', array(
			array('controller' => 'tags', 'action' => 'add', 'title' => __d('webzash', 'Add Tag')),
		));

		$this->Paginator->settings = array(
			'Tag' => array(
				'limit' => 10,
				'order' => array('Tag.title' => 'asc'),
			)
		);

		$this->set('tags', $this->Paginator->paginate('Tag'));
		return;
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {

		$this->set('title_for_layout', __d('webzash', 'Add Tag'));

		/* On POST */
		if ($this->request->is('post')) {
			$this->Tag->create();
			if (!empty($this->request->data)) {
				/* Unset ID */
				unset($this->request->data['Tag']['id']);

				/* Save tag */
				$ds = $this->Tag->getDataSource();
				$ds->begin();

				if ($this->Tag->save($this->request->data)) {
					$ds->commit();
					$this->Session->setFlash(__d('webzash', 'The tag has been created.'), 'success');
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
				} else {
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'The tag could not be saved. Please, try again.'), 'error');
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
 * @throws NotFoundException
 * @throws ForbiddenException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {

		$this->set('title_for_layout', __d('webzash', 'Edit Tag'));

		/* Check for valid tag */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'Tag not specified.'), 'error');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
		}
		$tag = $this->Tag->findById($id);
		if (!$tag) {
			$this->Session->setFlash(__d('webzash', 'Tag not found.'), 'error');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
		}

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {
			/* Set tag id */
			unset($this->request->data['Tag']['id']);
			$this->Tag->id = $id;

			/* Save tag */
			$ds = $this->Tag->getDataSource();
			$ds->begin();

			if ($this->Tag->save($this->request->data)) {
				$ds->commit();
				$this->Session->setFlash(__d('webzash', 'The tag has been updated.'), 'success');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'The tag could not be updated. Please, try again.'), 'error');
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
		$this->loadModel('Entry');

		/* GET access not allowed */
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}

		/* Check for valid tag */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'Tag not specified.'), 'error');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
		}

		/* Check if tag exists */
		if (!$this->Tag->exists($id)) {
			$this->Session->setFlash(__d('webzash', 'Tag not found.'), 'error');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
		}

		/* Check if any entries using the tag exists */
		$entries = $this->Entry->find('count', array('conditions' => array('Entry.tag_id' => $id)));
		if ($entries > 0) {
			$this->Session->setFlash(__d('webzash', 'The tag cannot be deleted since one or more entries are still using it.'), 'error');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
		}

		/* Delete tag */
		$ds = $this->Tag->getDataSource();
		$ds->begin();

		if ($this->Tag->delete($id)) {
			$ds->commit();
			$this->Session->setFlash(__d('webzash', 'The tag has been deleted.'), 'success');
		} else {
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'The tag could not be deleted. Please, try again.'), 'error');
		}

		return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
	}

	/* Authorization check */
	public function isAuthorized($user) {
		if ($this->action === 'index') {
			return $this->Permission->is_allowed('view tag', $user['role']);
		}

		if ($this->action === 'add') {
			return $this->Permission->is_allowed('add tag', $user['role']);
		}

		if ($this->action === 'edit') {
			return $this->Permission->is_allowed('edit tag', $user['role']);
		}

		if ($this->action === 'delete') {
			return $this->Permission->is_allowed('delete tag', $user['role']);
		}

		return parent::isAuthorized($user);
	}
}
