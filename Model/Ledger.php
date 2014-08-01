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

App::uses('WebzashAppModel', 'Webzash.Model');

/**
* Webzash Plugin Ledger Model
*
* @package Webzash
* @subpackage Webzash.model
*/
class Ledger extends WebzashAppModel {

	/* Validation rules for the Ledger table */
	public $validate = array(

		'group_id' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Parent group cannot be empty',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'numeric',
				'message' => 'Parent group is not a valid number',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => 'groupValid',
				'message' => 'Parent group is not valid',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule4' => array(
				'rule' => array('maxLength', 11),
				'message' => 'Parent group id length cannot be more than 11',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'name' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Ledger name cannot be empty',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isUnique',
				'message' => 'Ledger name is already in use',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 100),
				'message' => 'Ledger name cannot be more than 100 characters',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'op_balance' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Opening balance cannot be empty',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isAmount',
				'message' => 'Opening balance is not a valid amount',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 28),
				'message' => 'Opening balance length cannot be more than 28',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'op_balance_dc' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Opening balance Dr/Cr cannot be empty',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isDC',
				'message' => 'Opening balance Dr/Cr can only be debit or credit',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'type' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Bank or cash account cannot be empty',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'boolean',
				'message' => 'Incorrect value for bank or cash account',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 2),
				'message' => 'Bank or cash account cannot be more than 2 integers',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'reconciliation' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Reconciliation cannot be empty',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'boolean',
				'message' => 'Incorrect value for reconciliation',
				'required'   => true,
				'allowEmpty' => false,
			),
		),

	);

	/* Validation - Check if group_id is a valid id */
	public function groupValid($data) {

		/* Load the Group model */
		App::import("Webzash.Model", "Group");
		$Group = new Group();

		if (!isset($data['group_id'])) {
			return false;
		}

		$groupCount = $Group->find('count', array(
		    'conditions' => array('id' => $data['group_id']),
		));

		if ($groupCount < 1) {
			return false;
		} else {
			return true;
		}

	}

	/* Validation - Check if value is either 'D' or 'C' */
	public function isDC($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];
		if ($value == 'D' || $value == 'C') {
			return true;
		} else {
			return false;
		}
	}

	/* Validation - Check if value is a proper decimal number with 2 decimal places */
	public function isAmount($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];
		if (preg_match('/^[0-9]{0,23}+(\.[0-9]{0,2})?$/', $value)) {
			return true;
		} else {
			return false;
		}
	}

	/* Calculate difference in opening balance */
	public function getOpeningDiff() {
		$total_op = 0;
		$ledgers = $this->find('all');
		foreach ($ledgers as $row => $ledger)
		{
			if ($ledger['Ledger']['op_balance_dc'] == 'D')
			{
				$total_op = calculate($total_op, $ledger['Ledger']['op_balance'], '+');
			} else {
				$total_op = calculate($total_op, $ledger['Ledger']['op_balance'], '-');
			}
		}
		if (calculate($total_op, 0, '>=')) {
			return array('opdiff_balance_dc' => 'D', 'opdiff_balance' => $total_op);
		} else {
			return array('opdiff_balance_dc' => 'C', 'opdiff_balance' => calculate($total_op, 0, 'n'));
		}
	}
}

