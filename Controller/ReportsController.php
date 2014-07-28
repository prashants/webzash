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
App::uses('AccountList', 'Webzash.Lib');

/**
 * Webzash Plugin Reports Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class ReportsController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	public $components = array('Paginator');

	public $helpers = array('Webzash.Generic');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		return;
	}

/**
 * balancesheet method
 *
 * @return void
 */
	public function balancesheet() {
		return;
	}

/**
 * profitloss method
 *
 * @return void
 */
	public function profitloss() {
		return;
	}

/**
 * trialbalance method
 *
 * @return void
 */
	public function trialbalance() {
		$accountlist = new AccountList();
		$accountlist->start(0);
		$this->set('accountlist', $accountlist);
		return;
	}

/**
 * ledgerstatement method
 *
 * @return void
 */
	public function ledgerstatement() {
		$this->loadModel('Ledger');
		$this->loadModel('Entry');
		$this->loadModel('Entryitem');

		/* Create list of ledgers to pass to view */
		$ledgers = $this->Ledger->find('list', array(
			'fields' => array('Ledger.id', 'Ledger.name'),
			'order' => array('Ledger.name')
		));
		$this->set('ledgers', $ledgers);

		if ($this->request->is('post')) {
			/* If valid data then redirect with POST values are URL parameters so that pagination works */
			if (empty($this->request->data['Report']['ledger_id'])) {
				$this->Session->setFlash(__d('webzash', 'Invalid ledger'), 'error');
				return $this->redirect(array('controller' => 'reports', 'action' => 'ledgerstatement'));
			}

			if ($this->request->data['Report']['custom_period'] == 1) {
				return $this->redirect(array('controller' => 'reports', 'action' => 'ledgerstatement',
					'ledgerid' => $this->request->data['Report']['ledger_id'],
					'customperiod' => 1,
					'startdate' => $this->request->data['Report']['startdate'],
					'enddate' => $this->request->data['Report']['enddate'],
				));
			} else {
				return $this->redirect(array('controller' => 'reports', 'action' => 'ledgerstatement',
					'ledgerid' => $this->request->data['Report']['ledger_id'],
				));
			}
		}

		$this->set('showEntries', false);

		/* Check if ledger id is set in parameters, if not return and end view here */
		if (empty($this->passedArgs['ledgerid'])) {
			return;
		}

		$ledgerId = $this->passedArgs['ledgerid'];

		/* Check if ledger exists */
		if (!$this->Ledger->exists($ledgerId)) {
			$this->Session->setFlash(__d('webzash', 'Ledger not found'), 'error');
			return $this->redirect(array('controller' => 'reports', 'action' => 'ledgerstatement'));
		}

		$this->request->data['Report']['ledger_id'] = $ledgerId;

		/* Set the approprite search conditions if custom date is selected */
		$conditions = array();
		$conditions['Entryitem.ledger_id'] = $ledgerId;
		if (!empty($this->passedArgs['customperiod'])) {
			$this->request->data['Report']['custom_period'] = $this->passedArgs['customperiod'];
		}
		if (!empty($this->passedArgs['startdate'])) {
			/* TODO : Validate date */
			$this->request->data['Report']['startdate'] = $this->passedArgs['startdate'];
			$conditions['Entry.date >='] = dateToSql($this->passedArgs['startdate'], '00:00:00');
		}
		if (!empty($this->passedArgs['enddate'])) {
			/* TODO : Validate date */
			$this->request->data['Report']['enddate'] = $this->passedArgs['enddate'];
			$conditions['Entry.date <='] = dateToSql($this->passedArgs['enddate'], '23:59:00');
		}

		/* Setup pagination */
		$this->Paginator->settings = array(
			'Entry' => array(
				'fields' => array('Entry.*', 'Entryitem.*'),
				'limit' => 10,
				'order' => array('Entry.date' => 'desc'),
				'conditions' => $conditions,
				'joins' => array(
					array(
						'table' => 'entryitems',
						'alias' => 'Entryitem',
						'conditions' => array(
							'Entry.id = Entryitem.entry_id'
						)
					),
				),
			),
		);

		$this->set('entries', $this->Paginator->paginate('Entry'));
		$this->set('showEntries', true);

		return;
	}

/**
 * reconciliation method
 *
 * @return void
 */
	public function reconciliation() {
		$this->loadModel('Ledger');
		$this->loadModel('Entry');
		$this->loadModel('Entryitem');

		/* Create list of ledgers to pass to view */
		$ledgers = $this->Ledger->find('list', array(
			'fields' => array('Ledger.id', 'Ledger.name'),
			'order' => array('Ledger.name'),
			'conditions' => array('Ledger.reconciliation' => '1'),
		));
		$this->set('ledgers', $ledgers);

		if ($this->request->is('post')) {

			/* Ledger selection form submitted */
			if (!empty($this->request->data['Report']['submitledger'])) {

				/* If valid data then redirect with POST values are URL parameters so that pagination works */
				if (empty($this->request->data['Report']['ledger_id'])) {
					$this->Session->setFlash(__d('webzash', 'Invalid ledger'), 'error');
					return $this->redirect(array('controller' => 'reports', 'action' => 'reconciliation'));
				}

				if ($this->request->data['Report']['custom_period'] == 1) {
					return $this->redirect(array('controller' => 'reports', 'action' => 'reconciliation',
						'ledgerid' => $this->request->data['Report']['ledger_id'],
						'showall' => $this->request->data['Report']['showall'],
						'customperiod' => 1,
						'startdate' => $this->request->data['Report']['startdate'],
						'enddate' => $this->request->data['Report']['enddate'],
					));
				} else {
					return $this->redirect(array('controller' => 'reports', 'action' => 'reconciliation',
						'ledgerid' => $this->request->data['Report']['ledger_id'],
						'showall' => $this->request->data['Report']['showall'],
					));
				}

			} else if (!empty($this->request->data['ReportRec']['submitrec'])) {

				/* Reconciliation form submitted */
				foreach ($this->request->data['ReportRec'] as $row => $recitem) {
					if (empty($recitem['id'])) {
						continue;
					}
					if (!empty($recitem['recdate'])) {
						$recdate = dateToSql($recitem['recdate']);
						if (!$recdate) {
							$this->Session->setFlash(__d('webzash', 'Invalid date'), 'error');
							continue;
						}
					} else {
						$recdate = '';
					}

					$this->Entryitem->id = $recitem['id'];
					if (!$this->Entryitem->read()) {
						continue;
					}
					$this->Entryitem->saveField('reconciliation_date', $recdate);
				}
				/* Unset all POST data so that data for reconciliation date is loaded from database */
				unset($this->request->data['ReportRec']);

			} else {
				return $this->redirect(array('controller' => 'reports', 'action' => 'reconciliation'));
			}
		}

		$this->set('showEntries', false);

		/* Check if ledger id is set in parameters, if not return and end view here */
		if (empty($this->passedArgs['ledgerid'])) {
			return;
		}

		$ledgerId = $this->passedArgs['ledgerid'];

		/* Check if ledger exists */
		if (!$this->Ledger->exists($ledgerId)) {
			$this->Session->setFlash(__d('webzash', 'Ledger not found'), 'error');
			return $this->redirect(array('controller' => 'reports', 'action' => 'reconciliation'));
		}

		$this->request->data['Report']['ledger_id'] = $ledgerId;
		$this->request->data['Report']['showall'] = $this->passedArgs['showall'];

		/* Set the approprite search conditions if custom date is selected */
		$conditions = array();
		$conditions['Entryitem.ledger_id'] = $ledgerId;
		if (!empty($this->passedArgs['customperiod'])) {
			$this->request->data['Report']['custom_period'] = $this->passedArgs['customperiod'];
		}
		if (!empty($this->passedArgs['startdate'])) {
			/* TODO : Validate date */
			$this->request->data['Report']['startdate'] = $this->passedArgs['startdate'];
			$conditions['Entry.date >='] = dateToSql($this->passedArgs['startdate'], '00:00:00');
		}
		if (!empty($this->passedArgs['enddate'])) {
			/* TODO : Validate date */
			$this->request->data['Report']['enddate'] = $this->passedArgs['enddate'];
			$conditions['Entry.date <='] = dateToSql($this->passedArgs['enddate'], '23:59:00');
		}
		if (!empty($this->passedArgs['showall'])) {
			/* nothing to do */
		} else {
			$conditions['Entryitem.reconciliation_date'] = null;
		}

		/* Setup pagination */
		$this->Paginator->settings = array(
			'Entry' => array(
				'fields' => array('Entry.*', 'Entryitem.*'),
				'limit' => 10,
				'order' => array('Entry.date' => 'desc'),
				'conditions' => $conditions,
				'joins' => array(
					array(
						'table' => 'entryitems',
						'alias' => 'Entryitem',
						'conditions' => array(
							'Entry.id = Entryitem.entry_id'
						)
					),
				),
			),
		);

		$this->set('entries', $this->Paginator->paginate('Entry'));
		$this->set('showEntries', true);

		return;
	}
}
