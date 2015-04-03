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

	public $uses = array('Webzash.Tag', 'Webzash.Entry', 'Webzash.Log');

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

		$this->CustomPaginator->settings = array(
			'Tag' => array(
				'limit' => $this->Session->read('Wzsetting.row_count'),
				'order' => array('Tag.title' => 'asc'),
			)
		);

		/* Pass varaibles to view which are used in Helpers */
		$this->set('allTags', $this->Tag->fetchAll());

		$this->set('tags', $this->CustomPaginator->paginate('Tag'));
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
					$this->Log->add('Added Tag : ' . $this->request->data['Tag']['title'], 1);
					$ds->commit();
					$this->Session->setFlash(__d('webzash', 'Tag "%s" created.', $this->request->data['Tag']['title']), 'success');
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
				} else {
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'Failed to create tag. Please, try again.'), 'danger');
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

		$this->set('title_for_layout', __d('webzash', 'Edit Tag'));

		/* Check for valid tag */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'Tag not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
		}
		$tag = $this->Tag->findById($id);
		if (!$tag) {
			$this->Session->setFlash(__d('webzash', 'Tag not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
		}

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {

			/* Check if acccount is locked */
			if (Configure::read('Account.locked') == 1) {
				$this->Session->setFlash(__d('webzash', 'Sorry, no changes are possible since the account is locked.'), 'danger');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
			}

			/* Set tag id */
			unset($this->request->data['Tag']['id']);
			$this->Tag->id = $id;

			/* Save tag */
			$ds = $this->Tag->getDataSource();
			$ds->begin();

			if ($this->Tag->save($this->request->data)) {
				$this->Log->add('Edited Tag : ' . $this->request->data['Tag']['title'], 1);
				$ds->commit();
				$this->Session->setFlash(__d('webzash', 'Tag "%s" updated.', $this->request->data['Tag']['title']), 'success');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'Failed to update tag. Please, try again.'), 'danger');
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
			throw new MethodNotAllowedException();
		}

		/* Check for valid tag */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'Tag not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
		}

		/* Check if tag exists */
		$tag = $this->Tag->findById($id);
		if (!$tag) {
			$this->Session->setFlash(__d('webzash', 'Tag not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
		}

		/* Check if any entries using the tag exists */
		$entries = $this->Entry->find('count', array('conditions' => array('Entry.tag_id' => $id)));
		if ($entries > 0) {
			$this->Session->setFlash(__d('webzash', 'Tag cannot be deleted since one or more entries are still using it.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
		}

		/* Delete tag */
		$ds = $this->Tag->getDataSource();
		$ds->begin();

		if ($this->Tag->delete($id)) {
			$this->Log->add('Deleted Tag : ' . $tag['Tag']['title'], 1);
			$ds->commit();
			$this->Session->setFlash(__d('webzash', 'Tag "%s" deleted.', $tag['Tag']['title']), 'success');
		} else {
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'Failed to delete tag. Please, try again.'), 'danger');
		}

		return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
	}

	function beforeFilter() {
		parent::beforeFilter();

		/* Check if acccount is locked */
		if (Configure::read('Account.locked') == 1) {
			if ($this->action == 'add' || $this->action == 'delete') {
				$this->Session->setFlash(__d('webzash', 'Sorry, no changes are possible since the account is locked.'), 'danger');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index'));
			}
		}
	}

	/* Authorization check */
	public function isAuthorized($user) {
		if ($this->action === 'index') {
			return $this->Permission->is_allowed('view tag');
		}

		if ($this->action === 'add') {
			return $this->Permission->is_allowed('add tag');
		}

		if ($this->action === 'edit') {
			return $this->Permission->is_allowed('edit tag');
		}

		if ($this->action === 'delete') {
			return $this->Permission->is_allowed('delete tag');
		}

		return parent::isAuthorized($user);
	}
}
