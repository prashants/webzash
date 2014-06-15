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
 * Webzash Plugin Groups Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class GroupsController extends WebzashAppController {

	public $components = array('Session');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->redirect(array('controller' => 'accounts', 'action' => 'show'));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {

		/* Create list of parent groups */
		$parents = $this->Group->find('list', array(
			'fields' => array('Group.id', 'Group.name'),
			'order' => array('Group.name')
		));
		$this->set('parents', $parents);

		/* On POST */
		if ($this->request->is('post')) {
			$this->Group->create();
			if (!empty($this->request->data)) {
				/* Unset ID */
				unset($this->request->data['Group']['id']);

				/* Save group */
				if ($this->Group->save($this->request->data)) {
					$this->Session->setFlash(__('The account group has been created.'), 'default', array(), 'success');
					return $this->redirect(array('controller' => 'accounts', 'action' => 'show'));
				} else {
					$this->Session->setFlash(__('The account group could not be saved. Please, try again.'), 'default', array(), 'error');
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
		/* Check for valid group */
		if (!$id) {
			throw new NotFoundException(__('Invalid account group.'));
		}
		$group = $this->Group->findById($id);
		if (!$group) {
			throw new NotFoundException(__('Invalid account group.'));
		}
		if ($id <= 4) {
			throw new ForbiddenException(__('Cannot edit basic account groups.'));
		}

		/* Create list of parent groups */
		$parents = $this->Group->find('list', array(
			'conditions' => array('Group.id !=' => $id),
			'fields' => array('Group.id', 'Group.name'),
			'order' => array('Group.name')
		));
		$this->set('parents', $parents);

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {
			/* Set group id */
			$this->Group->id = $id;

			/* Check if group and parent group are not same */
			if ($id == $this->request->data['Group']['parent_id']) {
				$this->Session->setFlash(__('The account group and parent group cannot be same.'), 'default', array(), 'error');
				return;
			}

			/* Save group */
			if ($this->Group->save($this->request->data)) {
				$this->Session->setFlash(__('The account group has been updated.'), 'default', array(), 'success');
				return $this->redirect(array('controller' => 'accounts', 'action' => 'show'));
			} else {
				$this->Session->setFlash(__('The account group could not be updated. Please, try again.'), 'default', array(), 'error');
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
		if (!$id) {
			throw new NotFoundException(__('Invalid account group.'));
		}

		/* Check if group exists */
		if (!$this->Group->exists($id)) {
			throw new NotFoundException(__('Invalid account group.'));
		}

		/* Check if group can be deleted */
		if ($id <= 4) {
			throw new ForbiddenException(__('Cannot delete basic account groups.'));
		}

		/* Check if any child groups exists */
		$child = $this->Group->find('count', array('conditions' => array('parent_id' => $id)));
		if ($child > 0) {
			$this->Session->setFlash(__('The account group could not be deleted since it has one or more child account groups still present.'), 'default', array(), 'error');
			return $this->redirect(array('controller' => 'accounts', 'action' => 'show'));
		}

		/* TODO : Check if any child ledgers exists */

		/* Delete group */
		if ($this->Group->delete($id)) {
			$this->Session->setFlash(__('The account group has been deleted.'), 'default', array(), 'success');
		} else {
			$this->Session->setFlash(__('The account group could not be deleted. Please, try again.'), 'default', array(), 'error');
		}

		return $this->redirect(array('controller' => 'accounts', 'action' => 'show'));
	}

/**
 * showgross method
 *
 * Checks if the top level parent group is either income or expenses, if yes
 * show the "Affects gross profit/loss calculations" checkbox in the view
 *
 * @return boolean
 */
	public function showgross() {
		$this->layout = null ;

		/* Read parent id from url get request */
		$parentID = (int)$this->request->query('id');

		/* If parent id is null, return NO */
		if (!$parentID) {
			$this->set("status", array("status" => "NO"));
			return;
		}

		/* Locate the top most parent id */
		$curParentID = $parentID;
		for ( ; $curParentID > 4 ; ) {
			$parentGroup = $this->Group->find('first', array('conditions' => array('Group.id' => $curParentID)));
			if ($parentGroup) {
				$curParentID = $parentGroup['Group']['parent_id'];
			} else {
				break;
			}
		}

		/* If the top most parent id is income or expense return YES */
		if ($curParentID == 3 || $curParentID == 4) {
			$this->set("status", array("status" => "YES"));
		} else {
			$this->set("status", array("status" => "NO"));
		}
		return;
	}
}
