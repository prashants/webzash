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
		$this->loadModel('Ledger');

		/* Cash and bank sumary */
		$ledgers = $this->Ledger->find('all', array(
			'order' => array('Ledger.name'),
			'conditions' => array('Ledger.type' => 1),
		));

		$ledgersCB = array();
		foreach ($ledgers as $ledger) {
			$ledgersCB[] = array(
				'name' => $ledger['Ledger']['name'],
				'balance' => closingBalance($ledger['Ledger']['id']),
			);
		}
		$this->set('ledgers', $ledgersCB);

		/* Account summary */
		$assets = new AccountList();
		$assets->start(1);
		$liabilities = new AccountList();
		$liabilities->start(2);
		$income = new AccountList();
		$income->start(3);
		$expense = new AccountList();
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

		return;
	}

}
