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

/**** This file contains common functions used throughout the application ****/

/**
 * Perform a decimal level calculations on two numbers
 *
 * Multiply the float by 100, convert it to integer,
 * Perform the integer operation and then divide the result
 * by 100 and return the result
 *
 * @param1 float number 1
 * @param2 float number 2
 * @op string operation to be performed
 * @return float result of the operation
*/

/* TODO : Use libraries for larger calculations */

function calculate($param1 = 0, $param2 = 0, $op = '') {
	if (extension_loaded('bcmath')) {
		switch ($op)
		{
			case '+':
				return bcadd($param1, $param2, 2);
				break;
			case '-':
				return bcsub($param1, $param2, 2);
				break;
			case '==':
				if (bccomp($param1, $param2, 2) == 0) {
					return TRUE;
				} else {
					return FALSE;
				}
				break;
			case '!=':
				if (bccomp($param1, $param2, 2) == 0) {
					return FALSE;
				} else {
					return TRUE;
				}
				break;
			case '<':
				if (bccomp($param1, $param2, 2) == -1) {
					return TRUE;
				} else {
					return FALSE;
				}
				break;
			case '>':
				if (bccomp($param1, $param2, 2) == 1) {
					return TRUE;
				} else {
					return FALSE;
				}
				break;
			case '>=':
				$temp = bccomp($param1, $param2, 2);
				if ($temp == 1 || $temp == 0) {
					return TRUE;
				} else {
					return FALSE;
				}
				break;
			case 'n':
				return bcmul($param1, -1, 2);
				break;
			default:
				die();
				break;
		}
	} else {
		$result = 0;
		$param1 = $param1 * 100;
		$param2 = $param2 * 100;
		$param1 = (int)round($param1, 0);
		$param2 = (int)round($param2, 0);
		switch ($op)
		{
			case '+':
				$result = $param1 + $param2;
				break;
			case '-':
				$result = $param1 - $param2;
				break;
			case '==':
				if ($param1 == $param2) {
					return TRUE;
				} else {
					return FALSE;
				}
				break;
			case '!=':
				if ($param1 != $param2) {
					return TRUE;
				} else {
					return FALSE;
				}
				break;
			case '<':
				if ($param1 < $param2) {
					return TRUE;
				} else {
					return FALSE;
				}
				break;
			case '>':
				if ($param1 > $param2) {
					return TRUE;
				} else {
					return FALSE;
				}
				break;
			case '>=':
				if ($param1 >= $param2) {
					return TRUE;
				} else {
					return FALSE;
				}
				break;
			case 'n':
				$result = -$param1;
				break;
			default:
				die();
				break;
		}
		$result = $result/100;
		return $result;
	}
}

/**
 * Calculate closing balance of specified ledger account
 *
 * @param1 int ledger id
 * @return array D/C, Amount
*/
function closingBalance($id) {

	if (empty($id)) {
		throw new InternalErrorException(__d('webzash', 'Ledger not specified. Failed to calculate closing balance.'));
	}

	App::import("Webzash.Model", "Ledger");
	$Ledger = new Ledger();

	App::import("Webzash.Model", "Entryitem");
	$Entryitem = new Entryitem();

	/* Opening balance */
	$op = $Ledger->find('first', array(
		'conditions' => array('Ledger.id' => $id)
	));
	if (!$op) {
		throw new InternalErrorException(__d('webzash', 'Ledger not found. Failed to calculate closing balance.'));
	}

	if (empty($op['Ledger']['op_balance'])) {
		$op_total = 0;
	} else {
		$op_total = $op['Ledger']['op_balance'];
	}

	$dr_total = 0;
	$cr_total = 0;
	$dr_total_dc = 0;
	$cr_total_dc = 0;

	$Entryitem->virtualFields = array('total' => 'SUM(Entryitem.amount)');

	/* Debit total */
	$total = $Entryitem->find('first', array(
		'fields' => array('total'),
		'conditions' => array('Entryitem.ledger_id' => $id, 'Entryitem.dc' => 'D')
	));
	if (empty($total['Entryitem']['total'])) {
		$dr_total = 0;
	} else {
		$dr_total = $total['Entryitem']['total'];
	}

	/* Credit total */
	$total = $Entryitem->find('first', array(
		'fields' => array('total'),
		'conditions' => array('Entryitem.ledger_id' => $id, 'Entryitem.dc' => 'C')
	));
	if (empty($total['Entryitem']['total'])) {
		$cr_total = 0;
	} else {
		$cr_total = $total['Entryitem']['total'];
	}

	/* Add opening balance */
	if ($op['Ledger']['op_balance_dc'] == 'D') {
		$dr_total_dc = calculate($op_total, $dr_total, '+');
		$cr_total_dc = $cr_total;
	} else {
		$dr_total_dc = $dr_total;
		$cr_total_dc = calculate($op_total, $cr_total, '+');
	}

	/* Calculate and update closing balance */
	$cl = 0;
	$cl_dc = '';
	if (calculate($dr_total_dc, $cr_total_dc, '>')) {
		$cl = calculate($dr_total_dc, $cr_total_dc, '-');
		$cl_dc = 'D';
	} else if (calculate($cr_total_dc, $dr_total_dc, '==')) {
		$cl = 0;
		$cl_dc = $op['Ledger']['op_balance_dc'];
	} else {
		$cl = calculate($cr_total_dc, $dr_total_dc, '-');
		$cl_dc = 'C';
	}

	return array('dc' => $cl_dc, 'balance' => $cl, 'dr_total' => $dr_total, 'cr_total' => $cr_total);
}

/**
 * This function converts the date and time string to valid SQL datetime value
 */
function dateToSql($indate, $intime = '00:00:00') {
	$unixTimestamp = strtotime($indate . ' ' . $intime);
	if (!$unixTimestamp) {
		return false;
	}
	return date("Y-m-d H:i:s", $unixTimestamp);
}

/**
 * This function converts the SQL datetime value to PHP date and time string
 */
function dateFromSql($sqldatetime) {
	$unixTimestamp = strtotime($sqldatetime);
	if (!$unixTimestamp) {
		return false;
	}
	return date(Configure::read('Account.dateformatPHP'), $unixTimestamp);
}

function toCurrency($dc, $amount) {
	if (calculate($amount, 0, '==')) {
		return number_format(0, 2, '.', '');
	}
	if ($dc == 'D') {
		if (calculate($amount, 0, '>')) {
			return 'Dr ' . number_format($amount, 2, '.', '');
		} else {
			return 'Cr ' . number_format(calculate($amount, 0, 'n'), 2, '.', '');
		}
	}
	if ($dc == 'C') {
		if (calculate($amount, 0, '>')) {
			return 'Cr ' . number_format($amount, 2, '.', '');
		} else {
			return 'Dr ' . number_format(calculate($amount, 0, 'n'), 2, '.', '');
		}
	}
	if ($dc == '') {
		return number_format($amount, 2, '.', '');
	}
	return 'ERROR';
}

/**
 * Read all account settings from database
 */
function init_account() {
	App::import("Webzash.Model", "Setting");
	$Setting = new Setting();

	App::import("Webzash.Model", "Entrytype");
	$Entrytype = new Entrytype();

	$setting = $Setting->findById(1);
	if (!$setting) {
		throw new InternalErrorException(__d('webzash', 'Account settings not found.'));
	}

	Configure::write('Account.name', $setting['Setting']['name']);
	Configure::write('Account.email', $setting['Setting']['email']);
	Configure::write('Account.currency_symbol', $setting['Setting']['currency_symbol']);
	$dateFormat = explode('|', $setting['Setting']['date_format']);
	Configure::write('Account.dateformatPHP', $dateFormat[0]);
	Configure::write('Account.dateformatJS', $dateFormat[1]);
	Configure::write('Account.startdate', $setting['Setting']['fy_start']);
	Configure::write('Account.enddate', $setting['Setting']['fy_end']);
	Configure::write('Account.locked', $setting['Setting']['account_locked']);
	Configure::write('Account.email_use_default', $setting['Setting']['email_use_default']);

	$rawentrytypes = $Entrytype->find('all');
	$entrytypes = array();
	foreach ($rawentrytypes as $entrytype) {
		$entrytypes[$entrytype['Entrytype']['id']] = array(
			'prefix' => $entrytype['Entrytype']['prefix'],
			'suffix' => $entrytype['Entrytype']['suffix'],
			'zero_padding' => $entrytype['Entrytype']['zero_padding'],
			'label' => $entrytype['Entrytype']['label'],
			'name' => $entrytype['Entrytype']['name'],
		);
	}
	Configure::write('Account.ET', $entrytypes);
}
init_account();

