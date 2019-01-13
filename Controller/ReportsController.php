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
App::uses('LedgerTree', 'Webzash.Lib');
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
	public $uses = array('Webzash.Group', 'Webzash.Ledger', 'Webzash.Entry',
		'Webzash.Entryitem', 'Webzash.Tag');

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

		/* POST */
		if ($this->request->is('post')) {
			if ($this->request->data['Balancesheet']['opening'] == 1) {
				return $this->redirect(array(
					'plugin' => 'webzash',
					'controller' => 'reports',
					'action' => 'balancesheet',
					'options' => 1,
					'opening' => 1,
				));
			} else {
				if (!empty($this->request->data['Balancesheet']['startdate']) || !empty($this->request->data['Balancesheet']['enddate'])) {
					return $this->redirect(array(
						'plugin' => 'webzash',
						'controller' => 'reports',
						'action' => 'balancesheet',
						'options' => 1,
						'opening' => 0,
						'startdate' => $this->request->data['Balancesheet']['startdate'],
						'enddate' => $this->request->data['Balancesheet']['enddate']
					));
				} else {
					return $this->redirect(array(
						'plugin' => 'webzash',
						'controller' => 'reports',
						'action' => 'balancesheet'
					));
				}
			}
		}

		$only_opening = false;
		$startdate = null;
		$enddate = null;

		if (empty($this->passedArgs['options'])) {
			$this->set('options', false);

			/* Sub-title*/
			$this->set('subtitle', __d('webzash', 'Closing Balance Sheet as on ') .
				dateFromSql(Configure::read('Account.enddate')));
		} else {
			$this->set('options', true);
			if (!empty($this->passedArgs['opening'])) {
				$only_opening = true;
				$this->request->data['Balancesheet']['opening'] = '1';

				/* Sub-title*/
				$this->set('subtitle', __d('webzash', 'Opening Balance Sheet as on ') .
					dateFromSql(Configure::read('Account.startdate')));
			} else {
				if (!empty($this->passedArgs['startdate'])) {
					$startdate = dateToSQL($this->passedArgs['startdate']);
					$this->request->data['Balancesheet']['startdate'] =
						$this->passedArgs['startdate'];
				}
				if (!empty($this->passedArgs['enddate'])) {
					$enddate = dateToSQL($this->passedArgs['enddate']);
					$this->request->data['Balancesheet']['enddate'] =
						$this->passedArgs['enddate'];
				}

				/* Sub-title*/
				if (!empty($this->passedArgs['startdate']) &&
					!empty($this->passedArgs['enddate'])) {
					$this->set('subtitle', __d('webzash', 'Balance Sheet from ' .
						dateFromSql(dateToSQL($this->passedArgs['startdate'])) . ' to ' .
						dateFromSql(dateToSQL($this->passedArgs['enddate']))
					));
				} else if (!empty($this->passedArgs['startdate'])) {
					$this->set('subtitle', __d('webzash', 'Balance Sheet from ' .
						dateFromSql(dateToSQL($this->passedArgs['startdate'])) . ' to ' .
						dateFromSql(Configure::read('Account.enddate'))
					));
				} else if (!empty($this->passedArgs['enddate'])) {
					$this->set('subtitle', __d('webzash', 'Balance Sheet from ' .
						dateFromSql(Configure::read('Account.startdate')) . ' to ' .
						dateFromSql(dateToSQL($this->passedArgs['enddate']))
					));
				}
			}
		}

		/**********************************************************************/
		/*********************** BALANCESHEET CALCULATIONS ********************/
		/**********************************************************************/

		/* Liabilities */
		$liabilities = new AccountList();
		$liabilities->Group = &$this->Group;
		$liabilities->Ledger = &$this->Ledger;
		$liabilities->only_opening = $only_opening;
		$liabilities->start_date = $startdate;
		$liabilities->end_date = $enddate;
		$liabilities->affects_gross = -1;
		$liabilities->start(2);

		$bsheet['liabilities'] = $liabilities;

		$bsheet['liabilities_total'] = 0;
		if ($liabilities->cl_total_dc == 'C') {
			$bsheet['liabilities_total'] = $liabilities->cl_total;
		} else {
			$bsheet['liabilities_total'] = calculate($liabilities->cl_total, 0, 'n');
		}

		/* Assets */
		$assets = new AccountList();
		$assets->Group = &$this->Group;
		$assets->Ledger = &$this->Ledger;
		$assets->only_opening = $only_opening;
		$assets->start_date = $startdate;
		$assets->end_date = $enddate;
		$assets->affects_gross = -1;
		$assets->start(1);

		$bsheet['assets'] = $assets;

		$bsheet['assets_total'] = 0;
		if ($assets->cl_total_dc == 'D') {
			$bsheet['assets_total'] = $assets->cl_total;
		} else {
			$bsheet['assets_total'] = calculate($assets->cl_total, 0, 'n');
		}

		/* Profit and loss calculations */
		$income = new AccountList();
		$income->Group = &$this->Group;
		$income->Ledger = &$this->Ledger;
		$income->only_opening = $only_opening;
		$income->start_date = $startdate;
		$income->end_date = $enddate;
		$income->affects_gross = -1;
		$income->start(3);

		$expense = new AccountList();
		$expense->Group = &$this->Group;
		$expense->Ledger = &$this->Ledger;
		$expense->only_opening = $only_opening;
		$expense->start_date = $startdate;
		$expense->end_date = $enddate;
		$expense->affects_gross = -1;
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

		/**** Final balancesheet total ****/
		$bsheet['final_liabilities_total'] = $bsheet['liabilities_total'];
		$bsheet['final_assets_total'] = $bsheet['assets_total'];

		/* If net profit add to liabilities, if net loss add to assets */
		if (calculate($bsheet['pandl'], 0, '>=')) {
			$bsheet['final_liabilities_total'] = calculate(
				$bsheet['final_liabilities_total'],
				$bsheet['pandl'], '+');
		} else {
			$positive_pandl = calculate($bsheet['pandl'], 0, 'n');
			$bsheet['final_assets_total'] = calculate(
				$bsheet['final_assets_total'],
				$positive_pandl, '+');
		}

		/**
		 * If difference in opening balance is Dr then subtract from
		 * assets else subtract from liabilities
		 */
		if ($bsheet['is_opdiff']) {
			if ($bsheet['opdiff']['opdiff_balance_dc'] == 'D') {
				$bsheet['final_assets_total'] = calculate(
					$bsheet['final_assets_total'],
					$bsheet['opdiff']['opdiff_balance'], '+');
			} else {
				$bsheet['final_liabilities_total'] = calculate(
					$bsheet['final_liabilities_total'],
					$bsheet['opdiff']['opdiff_balance'], '+');
			}
		}

		$this->set('bsheet', $bsheet);

		/* Download report */
		if (isset($this->passedArgs['downloadcsv'])) {
			Configure::write('Account.currency_format', 'none');
			$this->layout = false;
			$view = new View($this, false);
			$response =  $view->render('Reports/downloadcsv/balancesheet');
			$this->response->body($response);
			$this->response->type('text/csv');
			$this->response->download('balancesheet.csv');
			return $this->response;
		}

		/* Download report */
		if (isset($this->passedArgs['downloadxls'])) {
			Configure::write('Account.currency_format', 'none');
			$this->layout = 'xls';
			$view = new View($this, false);
			$response =  $view->render('Reports/downloadxls/balancesheet');
			$this->response->body($response);
			$this->response->type('application/vnd.ms-excel');
			$this->response->download('balancesheet.xls');
			return $this->response;
		}

		/* Print report */
		if (isset($this->passedArgs['print'])) {
			$this->layout = 'print';
			$view = new View($this, false);
			$response =  $view->render('Reports/print/balancesheet');
			$this->response->body($response);
			return $this->response;
		}

		return;
	}

/**
 * profitloss method
 *
 * @return void
 */
	public function profitloss() {

		$this->set('title_for_layout', __d('webzash', 'Trading and Profit & Loss Statement'));
		$this->set('subtitle', '');

		/* POST */
		if ($this->request->is('post')) {
			if ($this->request->data['Profitloss']['opening'] == 1) {
				return $this->redirect(array(
					'plugin' => 'webzash',
					'controller' => 'reports',
					'action' => 'profitloss',
					'options' => 1,
					'opening' => 1,
				));
			} else {
				if (!empty($this->request->data['Profitloss']['startdate']) || !empty($this->request->data['Profitloss']['enddate'])) {
					return $this->redirect(array(
						'plugin' => 'webzash',
						'controller' => 'reports',
						'action' => 'profitloss',
						'options' => 1,
						'opening' => 0,
						'startdate' => $this->request->data['Profitloss']['startdate'],
						'enddate' => $this->request->data['Profitloss']['enddate']
					));
				} else {
					return $this->redirect(array(
						'plugin' => 'webzash',
						'controller' => 'reports',
						'action' => 'profitloss'
					));
				}
			}
		}

		$only_opening = false;
		$startdate = null;
		$enddate = null;

		if (empty($this->passedArgs['options'])) {
			$this->set('options', false);

			/* Sub-title*/
			$this->set('subtitle', __d('webzash', 'Closing Trading and Profit & Loss Statement as on ') .
				dateFromSql(Configure::read('Account.enddate')));
		} else {
			$this->set('options', true);
			if (!empty($this->passedArgs['opening'])) {
				$only_opening = true;
				$this->request->data['Profitloss']['opening'] = '1';

				/* Sub-title*/
				$this->set('subtitle', __d('webzash', 'Opening Trading and Profit & Loss Statement as on ') .
					dateFromSql(Configure::read('Account.startdate')));
			} else {
				if (!empty($this->passedArgs['startdate'])) {
					$startdate = dateToSQL($this->passedArgs['startdate']);
					$this->request->data['Profitloss']['startdate'] = $this->passedArgs['startdate'];
				}
				if (!empty($this->passedArgs['enddate'])) {
					$enddate = dateToSQL($this->passedArgs['enddate']);
					$this->request->data['Profitloss']['enddate'] = $this->passedArgs['enddate'];
				}

				/* Sub-title*/
				if (!empty($this->passedArgs['startdate']) &&
					!empty($this->passedArgs['enddate'])) {
					$this->set('subtitle', __d('webzash', 'Trading and Profit & Loss Statement from ' .
						dateFromSql(dateToSQL($this->passedArgs['startdate'])) . ' to ' .
						dateFromSql(dateToSQL($this->passedArgs['enddate']))
					));
				} else if (!empty($this->passedArgs['startdate'])) {
					$this->set('subtitle', __d('webzash', 'Trading and Profit & Loss Statement from ' .
						dateFromSql(dateToSQL($this->passedArgs['startdate'])) . ' to ' .
						dateFromSql(Configure::read('Account.enddate'))
					));
				} else if (!empty($this->passedArgs['enddate'])) {
					$this->set('subtitle', __d('webzash', 'Trading and Profit & Loss Statement from ' .
						dateFromSql(Configure::read('Account.startdate')) . ' to ' .
						dateFromSql(dateToSQL($this->passedArgs['enddate']))
					));
				}
			}
		}

		/**********************************************************************/
		/*********************** GROSS CALCULATIONS ***************************/
		/**********************************************************************/

		/* Gross P/L : Expenses */
		$gross_expenses = new AccountList();
		$gross_expenses->Group = &$this->Group;
		$gross_expenses->Ledger = &$this->Ledger;
		$gross_expenses->only_opening = $only_opening;
		$gross_expenses->start_date = $startdate;
		$gross_expenses->end_date = $enddate;
		$gross_expenses->affects_gross = 1;
		$gross_expenses->start(4);

		$pandl['gross_expenses'] = $gross_expenses;

		$pandl['gross_expense_total'] = 0;
		if ($gross_expenses->cl_total_dc == 'D') {
			$pandl['gross_expense_total'] = $gross_expenses->cl_total;
		} else {
			$pandl['gross_expense_total'] = calculate($gross_expenses->cl_total, 0, 'n');
		}

		/* Gross P/L : Incomes */
		$gross_incomes = new AccountList();
		$gross_incomes->Group = &$this->Group;
		$gross_incomes->Ledger = &$this->Ledger;
		$gross_incomes->only_opening = $only_opening;
		$gross_incomes->start_date = $startdate;
		$gross_incomes->end_date = $enddate;
		$gross_incomes->affects_gross = 1;
		$gross_incomes->start(3);

		$pandl['gross_incomes'] = $gross_incomes;

		$pandl['gross_income_total'] = 0;
		if ($gross_incomes->cl_total_dc == 'C') {
			$pandl['gross_income_total'] = $gross_incomes->cl_total;
		} else {
			$pandl['gross_income_total'] = calculate($gross_incomes->cl_total, 0, 'n');
		}

		/* Calculating Gross P/L */
		$pandl['gross_pl'] = calculate($pandl['gross_income_total'], $pandl['gross_expense_total'], '-');

		/**********************************************************************/
		/************************* NET CALCULATIONS ***************************/
		/**********************************************************************/

		/* Net P/L : Expenses */
		$net_expenses = new AccountList();
		$net_expenses->Group = &$this->Group;
		$net_expenses->Ledger = &$this->Ledger;
		$net_expenses->only_opening = $only_opening;
		$net_expenses->start_date = $startdate;
		$net_expenses->end_date = $enddate;
		$net_expenses->affects_gross = 0;
		$net_expenses->start(4);

		$pandl['net_expenses'] = $net_expenses;

		$pandl['net_expense_total'] = 0;
		if ($net_expenses->cl_total_dc == 'D') {
			$pandl['net_expense_total'] = $net_expenses->cl_total;
		} else {
			$pandl['net_expense_total'] = calculate($net_expenses->cl_total, 0, 'n');
		}

		/* Net P/L : Incomes */
		$net_incomes = new AccountList();
		$net_incomes->Group = &$this->Group;
		$net_incomes->Ledger = &$this->Ledger;
		$net_incomes->only_opening = $only_opening;
		$net_incomes->start_date = $startdate;
		$net_incomes->end_date = $enddate;
		$net_incomes->affects_gross = 0;
		$net_incomes->start(3);

		$pandl['net_incomes'] = $net_incomes;

		$pandl['net_income_total'] = 0;
		if ($net_incomes->cl_total_dc == 'C') {
			$pandl['net_income_total'] = $net_incomes->cl_total;
		} else {
			$pandl['net_income_total'] = calculate($net_incomes->cl_total, 0, 'n');
		}

		/* Calculating Net P/L */
		$pandl['net_pl'] = calculate($pandl['net_income_total'], $pandl['net_expense_total'], '-');
		$pandl['net_pl'] = calculate($pandl['net_pl'], $pandl['gross_pl'], '+');

		$this->set('pandl', $pandl);

		/* Download report */
		if (isset($this->passedArgs['downloadcsv'])) {
			Configure::write('Account.currency_format', 'none');
			$this->layout = false;
			$view = new View($this, false);
			$response =  $view->render('Reports/downloadcsv/profitloss');
			$this->response->body($response);
			$this->response->type('text/csv');
			$this->response->download('profitloss.csv');
			return $this->response;
		}

		/* Download report */
		if (isset($this->passedArgs['downloadxls'])) {
			Configure::write('Account.currency_format', 'none');
			$this->layout = 'xls';
			$view = new View($this, false);
			$response =  $view->render('Reports/downloadxls/profitloss');
			$this->response->body($response);
			$this->response->type('application/vnd.ms-excel');
			$this->response->download('profitloss.xls');
			return $this->response;
		}

		/* Print report */
		if (isset($this->passedArgs['print'])) {
			$this->layout = 'print';
			$view = new View($this, false);
			$response =  $view->render('Reports/print/profitloss');
			$this->response->body($response);
			return $this->response;
		}

		return;
	}

/**
 * trialbalance method
 *
 * @return void
 */
	public function trialbalance() {

		$this->set('title_for_layout', __d('webzash', 'Trial Balance'));

		/* Sub-title*/
		$this->set('subtitle', __d('webzash', 'Trial Balance from %s to %s',
			dateFromSql(Configure::read('Account.startdate')),
			dateFromSql(Configure::read('Account.enddate'))
		));

		$accountlist = new AccountList();
		$accountlist->Group = &$this->Group;
		$accountlist->Ledger = &$this->Ledger;
		$accountlist->only_opening = false;
		$accountlist->start_date = null;
		$accountlist->end_date = null;
		$accountlist->affects_gross = -1;

		$accountlist->start(0);

		$this->set('accountlist', $accountlist);

		/* Download report */
		if (isset($this->passedArgs['downloadcsv'])) {
			Configure::write('Account.currency_format', 'none');
			$this->layout = false;
			$view = new View($this, false);
			$response =  $view->render('Reports/downloadcsv/trialbalance');
			$this->response->body($response);
			$this->response->type('text/csv');
			$this->response->download('trialbalance.csv');
			return $this->response;
		}

		/* Download report */
		if (isset($this->passedArgs['downloadxls'])) {
			Configure::write('Account.currency_format', 'none');
			$this->layout = 'xls';
			$view = new View($this, false);
			$response =  $view->render('Reports/downloadxls/trialbalance');
			$this->response->body($response);
			$this->response->type('application/vnd.ms-excel');
			$this->response->download('trialbalance.xls');
			return $this->response;
		}

		/* Print report */
		if (isset($this->passedArgs['print'])) {
			$this->layout = 'print';
			$view = new View($this, false);
			$response =  $view->render('Reports/print/trialbalance');
			$this->response->body($response);
			return $this->response;
		}

		return;
	}

/**
 * ledgerstatement method
 *
 * @return void
 */
	public function ledgerstatement() {

		$this->set('title_for_layout', __d('webzash', 'Ledger Statement'));

		/* Create list of ledgers to pass to view */
		$ledgers = new LedgerTree();
		$ledgers->Group = &$this->Group;
		$ledgers->Ledger = &$this->Ledger;
		$ledgers->current_id = -1;
		$ledgers->restriction_bankcash = 1;
		$ledgers->build(0);
		$ledgers->toList($ledgers, -1);
		$ledgers_disabled = array();
		foreach ($ledgers->ledgerList as $row => $data) {
			if ($row < 0) {
				$ledgers_disabled[] = $row;
			}
		}
		$this->set('ledgers', $ledgers->ledgerList);
		$this->set('ledgers_disabled', $ledgers_disabled);

		if ($this->request->is('post')) {
			/* If valid data then redirect with POST values are URL parameters so that pagination works */
			if (empty($this->request->data['Report']['ledger_id'])) {
				$this->Session->setFlash(__d('webzash', 'Invalid ledger.'), 'danger');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'ledgerstatement'));
			}

			if (!empty($this->request->data['Report']['startdate']) ||
				!empty($this->request->data['Report']['enddate'])) {
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'ledgerstatement',
					'ledgerid' => $this->request->data['Report']['ledger_id'],
					'options' => 1,
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
		$this->set('options', false);

		/* Check if ledger id is set in parameters, if not return and end view here */
		if (empty($this->passedArgs['ledgerid'])) {
			return;
		}

		$ledgerId = $this->passedArgs['ledgerid'];

		/* Check if ledger exists */
		$ledger = $this->Ledger->findById($ledgerId);
		if (!$ledger) {
			$this->Session->setFlash(__d('webzash', 'Ledger not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'ledgerstatement'));
		}

		$this->set('ledger', $ledger);

		$this->request->data['Report']['ledger_id'] = $ledgerId;

		/* Set the approprite search conditions */
		$conditions = array();
		$conditions['Entryitem.ledger_id'] = $ledgerId;

		/* Set the approprite search conditions if custom date is selected */
		$startdate = null;
		$enddate = null;
		if (empty($this->passedArgs['options'])) {
			$this->set('options', false);

			/* Sub-title*/
			$this->set('subtitle', __d('webzash', 'Ledger statement for %s from %s to %s',
				h(toCodeWithName($ledger['Ledger']['code'], $ledger['Ledger']['name'])),
				dateFromSql(Configure::read('Account.startdate')),
				dateFromSql(Configure::read('Account.enddate'))
			));
		} else {
			$this->set('options', true);

			if (!empty($this->passedArgs['startdate'])) {
				/* TODO : Validate date */
				$startdate = dateToSql($this->passedArgs['startdate']);
				$this->request->data['Report']['startdate'] = $this->passedArgs['startdate'];
				$conditions['Entry.date >='] = $startdate;
			}
			if (!empty($this->passedArgs['enddate'])) {
				/* TODO : Validate date */
				$enddate = dateToSql($this->passedArgs['enddate']);
				$this->request->data['Report']['enddate'] = $this->passedArgs['enddate'];
				$conditions['Entry.date <='] = $enddate;
			}

			/* Sub-title*/
			if (!empty($this->passedArgs['startdate']) &&
				!empty($this->passedArgs['enddate'])) {
				$this->set('subtitle', __d('webzash', 'Ledger statement for %s from %s to %s',
					h($ledger['Ledger']['name']),
					dateFromSql(dateToSQL($this->passedArgs['startdate'])),
					dateFromSql(dateToSQL($this->passedArgs['enddate']))
				));
			} else if (!empty($this->passedArgs['startdate'])) {
				$this->set('subtitle', __d('webzash', 'Ledger statement for %s from %s to %s',
					h($ledger['Ledger']['name']),
					dateFromSql(dateToSQL($this->passedArgs['startdate'])),
					dateFromSql(Configure::read('Account.enddate'))
				));
			} else if (!empty($this->passedArgs['enddate'])) {
				$this->set('subtitle', __d('webzash', 'Ledger statement for %s from %s to %s',
					h($ledger['Ledger']['name']),
					dateFromSql(Configure::read('Account.startdate')),
					dateFromSql(dateToSQL($this->passedArgs['enddate']))
				));
			}
		}

		/* Opening and closing titles */
		if (is_null($startdate)) {
			$this->set('opening_title', __d('webzash', 'Opening balance as on %s',
				dateFromSql(Configure::read('Account.startdate'))));
		} else {
			$this->set('opening_title', __d('webzash', 'Opening balance as on %s',
				dateFromSql($startdate)));
		}
		if (is_null($enddate)) {
			$this->set('closing_title', __d('webzash', 'Closing balance as on %s',
				dateFromSql(Configure::read('Account.enddate'))));
		} else {
			$this->set('closing_title', __d('webzash', 'Closing balance as on %s',
				dateFromSql($enddate)));
		}

		/* Calculating opening balance */
		$op = $this->Ledger->openingBalance($ledgerId, $startdate);
		$this->set('op', $op);

		/* Calculating closing balance */
		$cl = $this->Ledger->closingBalance($ledgerId, null, $enddate);
		$this->set('cl', $cl);

		/* Calculate current page opening balance */
		if (!isset($this->passedArgs['page']) || $this->passedArgs['page'] <= 1) {
			/* If 1st page then current page opening balance is opening balance */
			$current_op = $op;
		} else {
			/* Setup limit that selects all previous entryitems */
			$cur_limit = (($this->passedArgs['page'] - 1) *
				$this->Session->read('Wzsetting.row_count'));

			/* Find all previous entryitems */
			$prev_entries = $this->Entry->find('all', array(
				'fields' => array(
					'Entry.id', 'Entry.tag_id', 'Entry.entrytype_id', 'Entry.number', 'Entry.date', 'Entry.dr_total', 'Entry.cr_total', 'Entry.narration',
					'Entryitem.id', 'Entryitem.entry_id', 'Entryitem.ledger_id', 'Entryitem.amount', 'Entryitem.dc', 'Entryitem.reconciliation_date',
				),
				'limit' => $cur_limit,
				'order' => array('Entry.date' => 'asc'),
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
			));

			/* Initially set as opening balance */
			$temp['amount'] = $op['amount'];
			$temp['dc'] = $op['dc'];

			/* Loop through each previous entryitem and add the amount */
			foreach ($prev_entries as $prev_entry) {
				$temp = calculate_withdc(
					$temp['amount'],
					$temp['dc'],
					$prev_entry['Entryitem']['amount'],
					$prev_entry['Entryitem']['dc']
				);
			}
			$current_op['amount'] = $temp['amount'];
			$current_op['dc'] = $temp['dc'];
		}
		/* Set the current page opening balance */
		$this->set('current_op', $current_op);

		/* Setup pagination */
		if (isset($this->passedArgs['downloadcsv']) ||
			isset($this->passedArgs['downloadxls']) ||
			isset($this->passedArgs['print'])) {
			$this->CustomPaginator->settings = array(
				'Entry' => array(
					'fields' => array(
						'Entry.id', 'Entry.tag_id', 'Entry.entrytype_id', 'Entry.number', 'Entry.date', 'Entry.dr_total', 'Entry.cr_total', 'Entry.narration',
						'Entryitem.id', 'Entryitem.entry_id', 'Entryitem.ledger_id', 'Entryitem.amount', 'Entryitem.dc', 'Entryitem.reconciliation_date',
					),
					'maxLimit' => 100000000000,	/* Max limit */
					'limit' => 100000000000,	/* Max limit */
					'order' => array('Entry.date' => 'asc'),
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
		} else {
			$this->CustomPaginator->settings = array(
				'Entry' => array(
					'fields' => array(
						'Entry.id', 'Entry.tag_id', 'Entry.entrytype_id', 'Entry.number', 'Entry.date', 'Entry.dr_total', 'Entry.cr_total', 'Entry.narration',
						'Entryitem.id', 'Entryitem.entry_id', 'Entryitem.ledger_id', 'Entryitem.amount', 'Entryitem.dc', 'Entryitem.reconciliation_date',
					),
					'limit' => $this->Session->read('Wzsetting.row_count'),
					'order' => array('Entry.date' => 'asc'),
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
		}

		/* Pass varaibles to view which are used in Helpers */
		$this->set('allTags', $this->Tag->fetchAll());

		$this->set('entries', $this->CustomPaginator->paginate('Entry'));
		$this->set('showEntries', true);

		/* Download report */
		if (isset($this->passedArgs['downloadcsv'])) {
			Configure::write('Account.currency_format', 'none');
			$this->layout = false;
			$view = new View($this, false);
			$response =  $view->render('Reports/downloadcsv/ledgerstatement');
			$this->response->body($response);
			$this->response->type('text/csv');
			$this->response->download('ledgerstatement.csv');
			return $this->response;
		}

		/* Download report */
		if (isset($this->passedArgs['downloadxls'])) {
			Configure::write('Account.currency_format', 'none');
			$this->layout = 'xls';
			$view = new View($this, false);
			$response =  $view->render('Reports/downloadxls/ledgerstatement');
			$this->response->body($response);
			$this->response->type('application/vnd.ms-excel');
			$this->response->download('ledgerstatement.xls');
			return $this->response;
		}

		/* Print report */
		if (isset($this->passedArgs['print'])) {
			$this->layout = 'print';
			$view = new View($this, false);
			$response =  $view->render('Reports/print/ledgerstatement');
			$this->response->body($response);
			return $this->response;
		}

		return;
	}


/**
 * ledgerentries method
 *
 * @return void
 */
	public function ledgerentries() {

		$this->set('title_for_layout', __d('webzash', 'Ledger Entries'));

		/* Create list of ledgers to pass to view */
		$ledgers = new LedgerTree();
		$ledgers->Group = &$this->Group;
		$ledgers->Ledger = &$this->Ledger;
		$ledgers->current_id = -1;
		$ledgers->restriction_bankcash = 1;
		$ledgers->build(0);
		$ledgers->toList($ledgers, -1);
		$ledgers_disabled = array();
		foreach ($ledgers->ledgerList as $row => $data) {
			if ($row < 0) {
				$ledgers_disabled[] = $row;
			}
		}
		$this->set('ledgers', $ledgers->ledgerList);
		$this->set('ledgers_disabled', $ledgers_disabled);

		if ($this->request->is('post')) {
			/* If valid data then redirect with POST values are URL parameters so that pagination works */
			if (empty($this->request->data['Report']['ledger_id'])) {
				$this->Session->setFlash(__d('webzash', 'Invalid ledger.'), 'danger');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'ledgerentries'));
			}

			if (!empty($this->request->data['Report']['startdate']) ||
				!empty($this->request->data['Report']['enddate'])) {
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'ledgerentries',
					'ledgerid' => $this->request->data['Report']['ledger_id'],
					'options' => 1,
					'startdate' => $this->request->data['Report']['startdate'],
					'enddate' => $this->request->data['Report']['enddate'],
				));
			} else {
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'ledgerentries',
					'ledgerid' => $this->request->data['Report']['ledger_id'],
				));
			}
		}

		$this->set('showEntries', false);
		$this->set('options', false);

		/* Check if ledger id is set in parameters, if not return and end view here */
		if (empty($this->passedArgs['ledgerid'])) {
			return;
		}

		$ledgerId = $this->passedArgs['ledgerid'];

		/* Check if ledger exists */
		$ledger = $this->Ledger->findById($ledgerId);
		if (!$ledger) {
			$this->Session->setFlash(__d('webzash', 'Ledger not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'ledgerentries'));
		}

		$this->set('ledger', $ledger);

		$this->request->data['Report']['ledger_id'] = $ledgerId;

		/* Set the approprite search conditions */
		$conditions = array();
		$conditions['Entryitem.ledger_id'] = $ledgerId;

		/* Set the approprite search conditions if custom date is selected */
		$startdate = null;
		$enddate = null;
		if (empty($this->passedArgs['options'])) {
			$this->set('options', false);

			/* Sub-title*/
			$this->set('subtitle', __d('webzash', 'Ledger entries for %s from %s to %s',
				h(toCodeWithName($ledger['Ledger']['code'], $ledger['Ledger']['name'])),
				dateFromSql(Configure::read('Account.startdate')),
				dateFromSql(Configure::read('Account.enddate'))
			));
		} else {
			$this->set('options', true);

			if (!empty($this->passedArgs['startdate'])) {
				/* TODO : Validate date */
				$startdate = dateToSql($this->passedArgs['startdate']);
				$this->request->data['Report']['startdate'] = $this->passedArgs['startdate'];
				$conditions['Entry.date >='] = $startdate;
			}
			if (!empty($this->passedArgs['enddate'])) {
				/* TODO : Validate date */
				$enddate = dateToSql($this->passedArgs['enddate']);
				$this->request->data['Report']['enddate'] = $this->passedArgs['enddate'];
				$conditions['Entry.date <='] = $enddate;
			}

			/* Sub-title*/
			if (!empty($this->passedArgs['startdate']) &&
				!empty($this->passedArgs['enddate'])) {
				$this->set('subtitle', __d('webzash', 'Ledger entries for %s from %s to %s',
					h($ledger['Ledger']['name']),
					dateFromSql(dateToSQL($this->passedArgs['startdate'])),
					dateFromSql(dateToSQL($this->passedArgs['enddate']))
				));
			} else if (!empty($this->passedArgs['startdate'])) {
				$this->set('subtitle', __d('webzash', 'Ledger entries for %s from %s to %s',
					h($ledger['Ledger']['name']),
					dateFromSql(dateToSQL($this->passedArgs['startdate'])),
					dateFromSql(Configure::read('Account.enddate'))
				));
			} else if (!empty($this->passedArgs['enddate'])) {
				$this->set('subtitle', __d('webzash', 'Ledger entries for %s from %s to %s',
					h($ledger['Ledger']['name']),
					dateFromSql(Configure::read('Account.startdate')),
					dateFromSql(dateToSQL($this->passedArgs['enddate']))
				));
			}
		}

		/* Opening and closing titles */
		if (is_null($startdate)) {
			$this->set('opening_title', __d('webzash', 'Opening balance as on %s',
				dateFromSql(Configure::read('Account.startdate'))));
		} else {
			$this->set('opening_title', __d('webzash', 'Opening balance as on %s',
				dateFromSql($startdate)));
		}
		if (is_null($enddate)) {
			$this->set('closing_title', __d('webzash', 'Closing balance as on %s',
				dateFromSql(Configure::read('Account.enddate'))));
		} else {
			$this->set('closing_title', __d('webzash', 'Closing balance as on %s',
				dateFromSql($enddate)));
		}

		/* Calculating opening balance */
		$op = $this->Ledger->openingBalance($ledgerId, $startdate);
		$this->set('op', $op);

		/* Calculating closing balance */
		$cl = $this->Ledger->closingBalance($ledgerId, null, $enddate);
		$this->set('cl', $cl);

		/* Setup pagination */
		if (isset($this->passedArgs['download']) ||
			isset($this->passedArgs['downloadxls']) ||
			isset($this->passedArgs['print'])) {
			$this->CustomPaginator->settings = array(
				'Entry' => array(
					'fields' => array(
						'Entry.id', 'Entry.tag_id', 'Entry.entrytype_id', 'Entry.number', 'Entry.date', 'Entry.dr_total', 'Entry.cr_total', 'Entry.narration',
						'Entryitem.id', 'Entryitem.entry_id', 'Entryitem.ledger_id', 'Entryitem.amount', 'Entryitem.dc', 'Entryitem.reconciliation_date',
					),
					'maxLimit' => 100000000000,	/* Max limit */
					'limit' => 100000000000,	/* Max limit */
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
		} else {
			$this->CustomPaginator->settings = array(
				'Entry' => array(
					'fields' => array(
						'Entry.id', 'Entry.tag_id', 'Entry.entrytype_id', 'Entry.number', 'Entry.date', 'Entry.dr_total', 'Entry.cr_total', 'Entry.narration',
						'Entryitem.id', 'Entryitem.entry_id', 'Entryitem.ledger_id', 'Entryitem.amount', 'Entryitem.dc', 'Entryitem.reconciliation_date',
					),
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
		}

		/* Pass varaibles to view which are used in Helpers */
		$this->set('allTags', $this->Tag->fetchAll());

		$this->set('entries', $this->CustomPaginator->paginate('Entry'));
		$this->set('showEntries', true);

		/* Download report */
		if (isset($this->passedArgs['downloadcsv'])) {
			Configure::write('Account.currency_format', 'none');
			$this->layout = false;
			$view = new View($this, false);
			$response =  $view->render('Reports/downloadcsv/ledgerentries');
			$this->response->body($response);
			$this->response->type('text/csv');
			$this->response->download('ledgerentries.csv');
			return $this->response;
		}

		/* Download report */
		if (isset($this->passedArgs['downloadxls'])) {
			Configure::write('Account.currency_format', 'none');
			$this->layout = 'xls';
			$view = new View($this, false);
			$response =  $view->render('Reports/downloadxls/ledgerentries');
			$this->response->body($response);
			$this->response->type('application/vnd.ms-excel');
			$this->response->download('ledgerentries.xls');
			return $this->response;
		}

		/* Print report */
		if (isset($this->passedArgs['print'])) {
			$this->layout = 'print';
			$view = new View($this, false);
			$response =  $view->render('Reports/print/ledgerentries');
			$this->response->body($response);
			return $this->response;
		}

		return;
	}

/**
 * reconciliation method
 *
 * @return void
 */
	public function reconciliation() {

		$this->set('title_for_layout', __d('webzash', 'Ledger Reconciliation'));

		/* Create list of ledgers to pass to view */
		$ledgers_q = $this->Ledger->find('all', array(
			'fields' => array('Ledger.id', 'Ledger.name', 'Ledger.code'),
			'order' => array('Ledger.name'),
			'conditions' => array('Ledger.reconciliation' => '1'),
		));
		$ledgers = array(0 => __d('webzash', 'Please select...'));
		foreach ($ledgers_q as $row) {
			$ledgers[$row['Ledger']['id']] = toCodeWithName(
				$row['Ledger']['code'], $row['Ledger']['name']
			);
		}
		$this->set('ledgers', $ledgers);

		if ($this->request->is('post')) {

			/* Ledger selection form submitted */
			if (!empty($this->request->data['Report']['submitledger'])) {

				/* If valid data then redirect with POST values are URL parameters so that pagination works */
				if (empty($this->request->data['Report']['ledger_id'])) {
					$this->Session->setFlash(__d('webzash', 'Invalid ledger.'), 'danger');
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'reconciliation'));
				}

				if (!empty($this->request->data['Report']['startdate']) ||
					!empty($this->request->data['Report']['enddate']) ||
					!empty($this->request->data['Report']['showall'])) {
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'reconciliation',
						'ledgerid' => $this->request->data['Report']['ledger_id'],
						'options' => 1,
						'showall' => $this->request->data['Report']['showall'],
						'startdate' => $this->request->data['Report']['startdate'],
						'enddate' => $this->request->data['Report']['enddate'],
					));
				} else {
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'reconciliation',
						'ledgerid' => $this->request->data['Report']['ledger_id']
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
		$this->set('options', false);

		/* Check if ledger id is set in parameters, if not return and end view here */
		if (empty($this->passedArgs['ledgerid'])) {
			return;
		}

		$ledgerId = $this->passedArgs['ledgerid'];

		/* Check if ledger exists */
		$ledger = $this->Ledger->findById($ledgerId);
		if (!$ledger) {
			$this->Session->setFlash(__d('webzash', 'Ledger not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'reconciliation'));
		}

		$this->set('ledger', $ledger);

		$this->request->data['Report']['ledger_id'] = $ledgerId;

		/* Set the approprite search conditions */
		$conditions = array();
		$conditions['Entryitem.ledger_id'] = $ledgerId;

		/* Set the approprite search conditions if custom date is selected */
		$startdate = null;
		$enddate = null;

		if (empty($this->passedArgs['options'])) {
			$this->set('options', false);

			/* Sub-title*/
			$this->set('subtitle', __d('webzash', 'Reconciliation report for %s from %s to %s',
				h(toCodeWithName($ledger['Ledger']['code'], $ledger['Ledger']['name'])),
				dateFromSql(Configure::read('Account.startdate')),
				dateFromSql(Configure::read('Account.enddate'))
			));
		} else {
			$this->set('options', true);

			if (!empty($this->passedArgs['showall'])) {
				$this->request->data['Report']['showall'] = 1;
			}
			if (!empty($this->passedArgs['startdate'])) {
				/* TODO : Validate date */
				$startdate = dateToSql($this->passedArgs['startdate']);
				$this->request->data['Report']['startdate'] = $this->passedArgs['startdate'];
				$conditions['Entry.date >='] = $startdate;
			}
			if (!empty($this->passedArgs['enddate'])) {
				/* TODO : Validate date */
				$enddate = dateToSql($this->passedArgs['enddate']);
				$this->request->data['Report']['enddate'] = $this->passedArgs['enddate'];
				$conditions['Entry.date <='] = $enddate;
			}

			/* Sub-title*/
			if (!empty($this->passedArgs['startdate']) &&
				!empty($this->passedArgs['enddate'])) {
				$this->set('subtitle', __d('webzash', 'Reconciliation report for %s from %s to %s',
					h($ledger['Ledger']['name']),
					dateFromSql(dateToSQL($this->passedArgs['startdate'])),
					dateFromSql(dateToSQL($this->passedArgs['enddate']))
				));
			} else if (!empty($this->passedArgs['startdate'])) {
				$this->set('subtitle', __d('webzash', 'Reconciliation report for %s from %s to %s',
					h($ledger['Ledger']['name']),
					dateFromSql(dateToSQL($this->passedArgs['startdate'])),
					dateFromSql(Configure::read('Account.enddate'))
				));
			} else if (!empty($this->passedArgs['enddate'])) {
				$this->set('subtitle', __d('webzash', 'Reconciliation report for %s from %s to %s',
					h($ledger['Ledger']['name']),
					dateFromSql(Configure::read('Account.startdate')),
					dateFromSql(dateToSQL($this->passedArgs['enddate']))
				));
			} else if (empty($this->passedArgs['startdate']) &&
				empty($this->passedArgs['enddate'])) {
				$this->set('subtitle', __d('webzash', 'Reconciliation report for %s from %s to %s',
					h($ledger['Ledger']['name']),
					dateFromSql(Configure::read('Account.startdate')),
					dateFromSql(Configure::read('Account.enddate'))
				));
			}
		}

		if (!empty($this->passedArgs['showall'])) {
			/* Nothing to do */
		} else {
			$conditions['Entryitem.reconciliation_date'] = NULL;
		}

		/* Opening and closing titles */
		if (is_null($startdate)) {
			$this->set('opening_title', __d('webzash', 'Opening balance as on %s',
				dateFromSql(Configure::read('Account.startdate'))));
		} else {
			$this->set('opening_title', __d('webzash', 'Opening balance as on %s',
				dateFromSql($startdate)));
		}
		if (is_null($enddate)) {
			$this->set('closing_title', __d('webzash', 'Closing balance as on %s',
				dateFromSql(Configure::read('Account.enddate'))));
		} else {
			$this->set('closing_title', __d('webzash', 'Closing balance as on %s',
				dateFromSql($enddate)));
		}
		/* Reconciliation pending title */
		$this->set('recpending_title', '');
		if (is_null($startdate) && is_null($enddate)) {
			$this->set('recpending_title', __d('webzash', 'Reconciliation pending from %s to %s',
				dateFromSql(Configure::read('Account.startdate')),
				dateFromSql(Configure::read('Account.enddate'))
			));
		} else if (!is_null($startdate) && !is_null($enddate)) {
			$this->set('recpending_title', __d('webzash', 'Reconciliation pending from %s to %s',
				dateFromSql($startdate), dateFromSql($enddate)
			));
		} else if (is_null($startdate)) {
			$this->set('recpending_title', __d('webzash', 'Reconciliation pending from %s to %s',
				dateFromSql(Configure::read('Account.startdate')),
				dateFromSql($enddate)
			));
		} else if (is_null($enddate)) {
			$this->set('recpending_title', __d('webzash', 'Reconciliation pending from %s to %s',
				dateFromSql($startdate),
				dateFromSql(Configure::read('Account.enddate'))
			));
		}

		/* Calculating opening balance */
		$op = $this->Ledger->openingBalance($ledgerId, $startdate);
		$this->set('op', $op);

		/* Calculating closing balance */
		$cl = $this->Ledger->closingBalance($ledgerId, null, $enddate);
		$this->set('cl', $cl);

		/* Calculating reconciliation pending balance */
		$rp = $this->Ledger->reconciliationPending($ledgerId, $startdate, $enddate);
		$this->set('rp', $rp);

		/* Setup pagination */
		if (isset($this->passedArgs['download']) ||
			isset($this->passedArgs['downloadxls']) ||
			isset($this->passedArgs['print'])) {
			$this->CustomPaginator->settings = array(
				'Entry' => array(
					'fields' => array(
						'Entry.id', 'Entry.tag_id', 'Entry.entrytype_id', 'Entry.number', 'Entry.date', 'Entry.dr_total', 'Entry.cr_total', 'Entry.narration',
						'Entryitem.id', 'Entryitem.entry_id', 'Entryitem.ledger_id', 'Entryitem.amount', 'Entryitem.dc', 'Entryitem.reconciliation_date',
					),
					'maxLimit' => 100000000000,	/* Max limit */
					'limit' => 100000000000,	/* Max limit */
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
		} else {
			$this->CustomPaginator->settings = array(
				'Entry' => array(
					'fields' => array(
						'Entry.id', 'Entry.tag_id', 'Entry.entrytype_id', 'Entry.number', 'Entry.date', 'Entry.dr_total', 'Entry.cr_total', 'Entry.narration',
						'Entryitem.id', 'Entryitem.entry_id', 'Entryitem.ledger_id', 'Entryitem.amount', 'Entryitem.dc', 'Entryitem.reconciliation_date',
					),
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
		}

		/* Pass varaibles to view which are used in Helpers */
		$this->set('allTags', $this->Tag->fetchAll());

		$this->set('entries', $this->CustomPaginator->paginate('Entry'));
		$this->set('showEntries', true);

		/* Download report */
		if (isset($this->passedArgs['downloadcsv'])) {
			Configure::write('Account.currency_format', 'none');
			$this->layout = false;
			$view = new View($this, false);
			$response =  $view->render('Reports/downloadcsv/reconciliation');
			$this->response->body($response);
			$this->response->type('text/csv');
			$this->response->download('reconciliation.csv');
			return $this->response;
		}

		/* Download report */
		if (isset($this->passedArgs['downloadxls'])) {
			Configure::write('Account.currency_format', 'none');
			$this->layout = 'xls';
			$view = new View($this, false);
			$response =  $view->render('Reports/downloadxls/reconciliation');
			$this->response->body($response);
			$this->response->type('application/vnd.ms-excel');
			$this->response->download('reconciliation.xls');
			return $this->response;
		}

		/* Print report */
		if (isset($this->passedArgs['print'])) {
			$this->layout = 'print';
			$view = new View($this, false);
			$response =  $view->render('Reports/print/reconciliation');
			$this->response->body($response);
			return $this->response;
		}

		return;
	}

	public function beforeFilter() {
		parent::beforeFilter();

		/* Skip the ajax/javascript fields from Security component to prevent request being blackholed */
		$this->Security->unlockedFields = array('startdate', 'enddate',
			'ledger_id');
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

		if ($this->action === 'ledgerentries') {
			return $this->Permission->is_allowed('access reports');
		}

		if ($this->action === 'reconciliation') {
			return $this->Permission->is_allowed('access reports');
		}

		return parent::isAuthorized($user);
	}
}
