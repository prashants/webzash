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
 * Webzash Plugin Dashboard Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class DashboardController extends WebzashAppController {

	public $uses = array('Webzash.Group', 'Webzash.Ledger', 'Webzash.Entry',
		'Webzash.Entryitem', 'Webzash.Tag', 'Webzash.Log');

/**
 * index method
 *
 * @return void
 */
	public function index() {

		$this->set('title_for_layout', __d('webzash', 'Account Dashboard'));

		/**** Start initial check if all tables are present ****/

		try {
			$this->Group->find('first');
		} catch (Exception $e) {
			CakeSession::delete('ActiveAccount.id');
			CakeSession::delete('ActiveAccount.account_role');
			$this->Session->setFlash(__d('webzash', 'Groups table is missing. Please check whether this is a valid account database.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'account'));
		}

		try {
			$this->Ledger->find('first');
		} catch (Exception $e) {
			CakeSession::delete('ActiveAccount.id');
			CakeSession::delete('ActiveAccount.account_role');
			$this->Session->setFlash(__d('webzash', 'Ledgers table is missing. Please check whether this is a valid account database.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'account'));
		}

		try {
			$this->Entry->find('first');
		} catch (Exception $e) {
			CakeSession::delete('ActiveAccount.id');
			CakeSession::delete('ActiveAccount.account_role');
			$this->Session->setFlash(__d('webzash', 'Entries table is missing. Please check whether this is a valid account database.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'account'));
		}

		try {
			$this->Entryitem->find('first');
		} catch (Exception $e) {
			CakeSession::delete('ActiveAccount.id');
			CakeSession::delete('ActiveAccount.account_role');
			$this->Session->setFlash(__d('webzash', 'Entryitems table is missing. Please check whether this is a valid account database.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'account'));
		}

		try {
			$this->Tag->find('first');
		} catch (Exception $e) {
			CakeSession::delete('ActiveAccount.id');
			CakeSession::delete('ActiveAccount.account_role');
			$this->Session->setFlash(__d('webzash', 'Tags table is missing. Please check whether this is a valid account database.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'account'));
		}

		try {
			$this->Log->find('first');
		} catch (Exception $e) {
			CakeSession::delete('ActiveAccount.id');
			CakeSession::delete('ActiveAccount.account_role');
			$this->Session->setFlash(__d('webzash', 'Logs table is missing. Please check whether this is a valid account database.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'account'));
		}
		/* End intial check */

		/* Cash and bank sumary */
		$ledgers = '';
		try {
			$ledgers = $this->Ledger->find('all', array(
				'order' => array('Ledger.name'),
				'conditions' => array('Ledger.type' => 1),
			));
		} catch (Exception $e) {
			CakeSession::delete('ActiveAccount.id');
			CakeSession::delete('ActiveAccount.account_role');
			$this->Session->setFlash(__d('webzash', 'Ledgers table is missing. Please check whether this is a valid account database.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'account'));
		}

		$ledgersCB = array();
		foreach ($ledgers as $ledger) {
			$ledgersCB[] = array(
				'name' => h(toCodeWithName($ledger['Ledger']['code'], $ledger['Ledger']['name'])),
				'balance' => $this->Ledger->closingBalance($ledger['Ledger']['id']),
			);
		}
		$this->set('ledgers', $ledgersCB);

		/* Account summary */
		$assets = new AccountList();
		$assets->Group = &$this->Group;
		$assets->Ledger = &$this->Ledger;
		$assets->only_opening = false;
		$assets->start_date = null;
		$assets->end_date = null;
		$assets->affects_gross = -1;
		$assets->start(1);

		$liabilities = new AccountList();
		$liabilities->Group = &$this->Group;
		$liabilities->Ledger = &$this->Ledger;
		$liabilities->only_opening = false;
		$liabilities->start_date = null;
		$liabilities->end_date = null;
		$liabilities->affects_gross = -1;
		$liabilities->start(2);

		$income = new AccountList();
		$income->Group = &$this->Group;
		$income->Ledger = &$this->Ledger;
		$income->only_opening = false;
		$income->start_date = null;
		$income->end_date = null;
		$income->affects_gross = -1;
		$income->start(3);

		$expense = new AccountList();
		$expense->Group = &$this->Group;
		$expense->Ledger = &$this->Ledger;
		$expense->only_opening = false;
		$expense->start_date = null;
		$expense->end_date = null;
		$expense->affects_gross = -1;
		$expense->start(4);

		$accsummary = array(
			'assets_total_dc' => $assets->cl_total_dc,
			'assets_total' => $assets->cl_total,
			'liabilities_total_dc' => $liabilities->cl_total_dc,
			'liabilities_total' => $liabilities->cl_total,
			'income_total_dc' => $income->cl_total_dc,
			'income_total' => $income->cl_total,
			'expense_total_dc' => $expense->cl_total_dc,
			'expense_total' => $expense->cl_total,
		);
		$this->set('accsummary', $accsummary);

		$logs = $this->Log->find('all', array('limit' => 17, 'order' => array('Log.date DESC')));
		$this->set('logs', $logs);

		return;
	}

	/* Authorization check */
	public function isAuthorized($user) {

		if ($this->action === 'index') {
			return $this->Permission->is_registered_allowed();
		}

		return parent::isAuthorized($user);
	}
}
