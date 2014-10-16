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
class ReportsController extends WebzashAppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->set('title_for_layout', __d('webzash', 'Reports'));

		return;
	}

/**
 * balancesheet method
 *
 * @return void
 */
	public function balancesheet() {

		$this->set('title_for_layout', __d('webzash', 'Balance Sheet'));

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Group");
		$this->Group = new Group();

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Ledger");
		$this->Ledger = new Ledger();

		/**********************************************************************/
		/*********************** BALANCESHEET CALCULATIONS ********************/
		/**********************************************************************/

		/* Liabilities */
		$bsheet['liabilities_total'] = 0;
		$liabilities_groups = $this->Group->find('all', array('conditions' => array('Group.parent_id' => 2)));

		foreach ($liabilities_groups as $row => $group) {
			$bsheet['liabilities_list'][$row] = new AccountList();
			$bsheet['liabilities_list'][$row]->start($group['Group']['id']);

			if ($bsheet['liabilities_list'][$row]->cl_total_dc == 'C') {
				$bsheet['liabilities_total'] = calculate($bsheet['liabilities_total'], $bsheet['liabilities_list'][$row]->cl_total, '+');
			} else {
				$bsheet['liabilities_total'] = calculate($bsheet['liabilities_total'], $bsheet['liabilities_list'][$row]->cl_total, '-');
			}
		}

		/* Assets */
		$bsheet['assets_total'] = 0;
		$assets_groups = $this->Group->find('all', array('conditions' => array('Group.parent_id' => 1)));

		foreach ($assets_groups as $row => $group) {
			$bsheet['assets_list'][$row] = new AccountList();
			$bsheet['assets_list'][$row]->start($group['Group']['id']);

			if ($bsheet['assets_list'][$row]->cl_total_dc == 'D') {
				$bsheet['assets_total'] = calculate($bsheet['assets_total'], $bsheet['assets_list'][$row]->cl_total, '+');
			} else {
				$bsheet['assets_total'] = calculate($bsheet['assets_total'], $bsheet['assets_list'][$row]->cl_total, '-');
			}
		}

		/* Calculating total */
		$bsheet['total'] = calculate($bsheet['assets_total'], $bsheet['liabilities_total'], '-');

		/* Profit and loss calculations */
		$income = new AccountList();
		$income->start(3);
		$expense = new AccountList();
		$expense->start(4);

		if ($income->cl_total_dc == 'C') {
			$income_total = $income->cl_total;
		} else {
			$income_total = calculate($income->cl_total, 0, 'n');
		}
		if ($expense->cl_total_dc == 'D') {
			$expense_total = $expense->cl_total;
		} else {
			$expense_total = calculate($expense->cl_total, 0, 'n');
		}

		$bsheet['pandl'] = calculate($income_total, $expense_total, '-');

		/* Difference in opening balance */
		$bsheet['opdiff'] = $this->Ledger->getOpeningDiff();
		if (calculate($bsheet['opdiff']['opdiff_balance'], 0, '==')) {
			$bsheet['is_opdiff'] = false;
		} else {
			$bsheet['is_opdiff'] = true;
		}

		$this->set('bsheet', $bsheet);

		return;
	}

/**
 * profitloss method
 *
 * @return void
 */
	public function profitloss() {

		$this->set('title_for_layout', __d('webzash', 'Profit and Loss Statement'));

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Group");
		$this->Group = new Group();

		/**********************************************************************/
		/*********************** GROSS CALCULATIONS ***************************/
		/**********************************************************************/

		/* Gross P/L : Expenses */
		$pandl['gross_expense_total'] = 0;
		$gross_expense_groups = $this->Group->find('all', array('conditions' => array('Group.parent_id' => 4, 'Group.affects_gross' => 1)));

		foreach ($gross_expense_groups as $row => $group) {
			$pandl['gross_expense_list'][$row] = new AccountList();
			$pandl['gross_expense_list'][$row]->start($group['Group']['id']);

			if ($pandl['gross_expense_list'][$row]->cl_total_dc == 'D') {
				$pandl['gross_expense_total'] = calculate($pandl['gross_expense_total'], $pandl['gross_expense_list'][$row]->cl_total, '+');
			} else {
				$pandl['gross_expense_total'] = calculate($pandl['gross_expense_total'], $pandl['gross_expense_list'][$row]->cl_total, '-');
			}
		}

		/* Gross P/L : Incomes */
		$pandl['gross_income_total'] = 0;
		$gross_income_groups = $this->Group->find('all', array('conditions' => array('Group.parent_id' => 3, 'Group.affects_gross' => 1)));

		foreach ($gross_income_groups as $row => $group) {
			$pandl['gross_income_list'][$row] = new AccountList();
			$pandl['gross_income_list'][$row]->start($group['Group']['id']);

			if ($pandl['gross_income_list'][$row]->cl_total_dc == 'C') {
				$pandl['gross_income_total'] = calculate($pandl['gross_income_total'], $pandl['gross_income_list'][$row]->cl_total, '+');
			} else {
				$pandl['gross_income_total'] = calculate($pandl['gross_income_total'], $pandl['gross_income_list'][$row]->cl_total, '-');
			}
		}

		/* Calculating Gross P/L */
		$pandl['gross_pl'] = calculate($pandl['gross_income_total'], $pandl['gross_expense_total'], '-');

		/**********************************************************************/
		/************************* NET CALCULATIONS ***************************/
		/**********************************************************************/

		/* Net P/L : Expenses */
		$pandl['net_expense_total'] = 0;
		$net_expense_groups = $this->Group->find('all', array('conditions' => array('Group.parent_id' => 4, 'Group.affects_gross' => 0)));

		foreach ($net_expense_groups as $row => $group) {
			$pandl['net_expense_list'][$row] = new AccountList();
			$pandl['net_expense_list'][$row]->start($group['Group']['id']);

			if ($pandl['net_expense_list'][$row]->cl_total_dc == 'D') {
				$pandl['net_expense_total'] = calculate($pandl['net_expense_total'], $pandl['net_expense_list'][$row]->cl_total, '+');
			} else {
				$pandl['net_expense_total'] = calculate($pandl['net_expense_total'], $pandl['net_expense_list'][$row]->cl_total, '-');
			}
		}

		/* Net P/L : Incomes */
		$pandl['net_income_total'] = 0;
		$net_income_groups = $this->Group->find('all', array('conditions' => array('Group.parent_id' => 3, 'Group.affects_gross' => 0)));

		foreach ($net_income_groups as $row => $group) {
			$pandl['net_income_list'][$row] = new AccountList();
			$pandl['net_income_list'][$row]->start($group['Group']['id']);

			if ($pandl['net_income_list'][$row]->cl_total_dc == 'C') {
				$pandl['net_income_total'] = calculate($pandl['net_income_total'], $pandl['net_income_list'][$row]->cl_total, '+');
			} else {
				$pandl['net_income_total'] = calculate($pandl['net_income_total'], $pandl['net_income_list'][$row]->cl_total, '-');
			}
		}

		/* Calculating Net P/L */
		$pandl['net_pl'] = calculate($pandl['net_income_total'], $pandl['net_expense_total'], '-');
		$pandl['net_pl'] = calculate($pandl['net_pl'], $pandl['gross_pl'], '+');

		$this->set('pandl', $pandl);

		return;
	}

/**
 * trialbalance method
 *
 * @return void
 */
	public function trialbalance() {

		$this->set('title_for_layout', __d('webzash', 'Trial Balance'));

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

		$this->set('title_for_layout', __d('webzash', 'Ledger Statement'));

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Ledger");
		$this->Ledger = new Ledger();

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Entry");
		$this->Entry = new Entry();

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Entryitem");
		$this->Entryitem = new Entryitem();

		/* Create list of ledgers to pass to view */
		$ledgers = $this->Ledger->find('list', array(
			'fields' => array('Ledger.id', 'Ledger.name'),
			'order' => array('Ledger.name')
		));
		$this->set('ledgers', $ledgers);

		if ($this->request->is('post')) {
			/* If valid data then redirect with POST values are URL parameters so that pagination works */
			if (empty($this->request->data['Report']['ledger_id'])) {
				$this->Session->setFlash(__d('webzash', 'Invalid ledger.'), 'danger');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'ledgerstatement'));
			}

			if ($this->request->data['Report']['custom_period'] == 1) {
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'ledgerstatement',
					'ledgerid' => $this->request->data['Report']['ledger_id'],
					'customperiod' => 1,
					'startdate' => $this->request->data['Report']['startdate'],
					'enddate' => $this->request->data['Report']['enddate'],
				));
			} else {
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'ledgerstatement',
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
			$this->Session->setFlash(__d('webzash', 'Ledger not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'ledgerstatement'));
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
			$conditions['Entry.date >='] = dateToSql($this->passedArgs['startdate']);
		}
		if (!empty($this->passedArgs['enddate'])) {
			/* TODO : Validate date */
			$this->request->data['Report']['enddate'] = $this->passedArgs['enddate'];
			$conditions['Entry.date <='] = dateToSql($this->passedArgs['enddate']);
		}

		/* Setup pagination */
		$this->Paginator->settings = array(
			'Entry' => array(
				'fields' => array('Entry.*', 'Entryitem.*'),
				'limit' => $this->Session->read('Wzsetting.row_count'),
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

		$this->set('title_for_layout', __d('webzash', 'Ledger Reconciliation'));

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Ledger");
		$this->Ledger = new Ledger();

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Entry");
		$this->Entry = new Entry();

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Entryitem");
		$this->Entryitem = new Entryitem();

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
					$this->Session->setFlash(__d('webzash', 'Invalid ledger.'), 'danger');
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'reconciliation'));
				}

				if ($this->request->data['Report']['custom_period'] == 1) {
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'reconciliation',
						'ledgerid' => $this->request->data['Report']['ledger_id'],
						'showall' => $this->request->data['Report']['showall'],
						'customperiod' => 1,
						'startdate' => $this->request->data['Report']['startdate'],
						'enddate' => $this->request->data['Report']['enddate'],
					));
				} else {
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'reconciliation',
						'ledgerid' => $this->request->data['Report']['ledger_id'],
						'showall' => $this->request->data['Report']['showall'],
					));
				}

			} else if (!empty($this->request->data['ReportRec']['submitrec'])) {

				/* Check if acccount is locked */
				if (Configure::read('Account.locked') == 1) {
					$this->Session->setFlash(__d('webzash', 'Sorry, no changes are possible since the account is locked.'), 'danger');
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'reconciliation'));
				}

				/* Reconciliation form submitted */
				foreach ($this->request->data['ReportRec'] as $row => $recitem) {
					if (empty($recitem['id'])) {
						continue;
					}
					if (!empty($recitem['recdate'])) {
						$recdate = dateToSql($recitem['recdate']);
						if (!$recdate) {
							$this->Session->setFlash(__d('webzash', 'Invalid reconciliation date.'), 'danger');
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
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'reconciliation'));
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
			$this->Session->setFlash(__d('webzash', 'Ledger not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'reconciliation'));
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
			$conditions['Entry.date >='] = dateToSql($this->passedArgs['startdate']);
		}
		if (!empty($this->passedArgs['enddate'])) {
			/* TODO : Validate date */
			$this->request->data['Report']['enddate'] = $this->passedArgs['enddate'];
			$conditions['Entry.date <='] = dateToSql($this->passedArgs['enddate']);
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
				'limit' => $this->Session->read('Wzsetting.row_count'),
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

	/* Authorization check */
	public function isAuthorized($user) {
		if ($this->action === 'index') {
			return $this->Permission->is_allowed('access reports');
		}

		if ($this->action === 'balancesheet') {
			return $this->Permission->is_allowed('access reports');
		}

		if ($this->action === 'profitloss') {
			return $this->Permission->is_allowed('access reports');
		}

		if ($this->action === 'trialbalance') {
			return $this->Permission->is_allowed('access reports');
		}

		if ($this->action === 'ledgerstatement') {
			return $this->Permission->is_allowed('access reports');
		}

		if ($this->action === 'reconciliation') {
			return $this->Permission->is_allowed('access reports');
		}

		return parent::isAuthorized($user);
	}
}
