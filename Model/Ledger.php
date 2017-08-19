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

App::uses('Entry', 'Webzash.Model');
App::uses('Entryitem', 'Webzash.Model');

/**
* Webzash Plugin Ledger Model
*
* @package Webzash
* @subpackage Webzash.model
*/
class Ledger extends WebzashAppModel {

	public $validationDomain = 'webzash';

	/* Validation rules for the Ledger table */
	public $validate = array(

		'group_id' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Parent group cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'numeric',
				'message' => 'Parent group is not a valid number',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => 'groupValid',
				'message' => 'Parent group is not valid',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule4' => array(
				'rule' => array('maxLength', 18),
				'message' => 'Parent group id length cannot be more than 18',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'name' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Ledger name cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isUnique',
				'message' => 'Ledger name is already in use',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Ledger name cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'code' => array(
			'rule1' => array(
				'rule' => 'isUnique',
				'message' => 'Ledger code is already in use',
				'required' => true,
				'allowEmpty' => true,
			),
			'rule2' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Ledger code cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => true,
			),
			'rule3' => array(
				'rule' => 'isUniqueInGroup',
				'message' => 'Ledger code is already in use by a group account',
				'required' => true,
				'allowEmpty' => true,
			),
		),
		'op_balance' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Opening balance cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isAmount',
				'message' => 'Opening balance is not a valid amount',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 28),
				'message' => 'Opening balance length cannot be more than 28',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule4' => array(
				'rule' => 'isPositive',
				'message' => 'Opening balance cannot be less than 0.00',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'op_balance_dc' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Opening balance Dr/Cr cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isDC',
				'message' => 'Invalid value for opening balance Dr/Cr',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'type' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Bank or cash account cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'boolean',
				'message' => 'Invalid value for bank or cash account',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 2),
				'message' => 'Bank or cash account cannot be more than 2 integers',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'reconciliation' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Reconciliation cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'boolean',
				'message' => 'Invalid value for reconciliation',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'notes' => array(
			'rule1' => array(
				'rule' => array('maxLength', 500),
				'message' => 'Notes length cannot be more than 500',
				'required' => true,
				'allowEmpty' => true,
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

	/* Validation - Check if code is unique across groups and ledgers */
	public function isUniqueInGroup($data) {
		if (empty($data['code'])) {
			return true;
		}

		/* Load the Group model */
		App::import("Webzash.Model", "Group");
		$Group = new Group();

		$count = $Group->find('count', array(
		    'conditions' => array('code' => $data['code']),
		));

		if ($count != 0) {
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
		if (preg_match('/^[0-9]{0,23}+(\.[0-9]{0,' . Configure::read('Account.decimal_places') . '})?$/', $value)) {
			return true;
		} else {
			return false;
		}
	}

	/* Validation - Check if value is a positive value */
	public function isPositive($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];
		if ($value >= 0.00) {
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

		/* Dr is more ==> $total_op >= 0 ==> balancing figure is Cr */
		if (calculate($total_op, 0, '>=')) {
			return array('opdiff_balance_dc' => 'C', 'opdiff_balance' => $total_op);
		} else {
			return array('opdiff_balance_dc' => 'D', 'opdiff_balance' => calculate($total_op, 0, 'n'));
		}
	}

	/* Return ledger name from id */
	public function getName($id) {
		$ledger = $this->findById($id);
		if ($ledger) {
			return toCodeWithName($ledger['Ledger']['code'],
				$ledger['Ledger']['name']);
		} else {
			return __d('webzash', 'ERROR');
		}
	}

/**
 * Calculate opening balance of specified ledger account for the given
 * date range
 *
 * @param1 int ledger id
 * @param2 date start date
 * @return array D/C, Amount
 */
	function openingBalance($id, $start_date = null) {

		if (empty($id)) {
			throw new InternalErrorException(__d('webzash',
				'Ledger not specified. Failed to calculate opening balance.')
			);
		}

		/* Load models that are needed for calculations */
		$Entry = ClassRegistry::init('Webzash.Entry');
		$Entryitem = ClassRegistry::init('Webzash.Entryitem');

		/* Opening balance */
		$op = $this->find('first', array(
			'conditions' => array('Ledger.id' => $id)
		));
		if (!$op) {
			throw new InternalErrorException(__d('webzash',
				'Ledger not found. Failed to calculate opening balance.')
			);
		}

		$op_total = 0;
		if (empty($op['Ledger']['op_balance'])) {
			$op_total = 0;
		} else {
			$op_total = $op['Ledger']['op_balance'];
		}
		$op_total_dc = $op['Ledger']['op_balance_dc'];

		/* If start date is not specified then return here */
		if (is_null($start_date)) {
			return array('dc' => $op_total_dc, 'amount' => $op_total);
		}

		/* Debit total */
		$dr_conditions = array(
			'Entryitem.ledger_id' => $id,
			'Entryitem.dc' => 'D'
		);
		if (!is_null($start_date)) {
			$dr_conditions['Entry.date <'] = $start_date;
		}
		$total = $Entryitem->find('first', array(
			'fields' => array('SUM(Entryitem.amount) as total'),
			'conditions' => $dr_conditions,
			'joins' => array(
				array(
					'table' => 'entries',
					'alias' => 'Entry',
					'type' => 'LEFT',
					'conditions' => array(
						'Entry.id = Entryitem.entry_id'
					)
				),
			),
		));

		if (empty($total[0]['total'])) {
			$dr_total = 0;
		} else {
			$dr_total = $total[0]['total'];
		}

		/* Credit total */
		$cr_conditions = array(
			'Entryitem.ledger_id' => $id,
			'Entryitem.dc' => 'C'
		);
		if (!is_null($start_date)) {
			$cr_conditions['Entry.date <'] = $start_date;
		}
		$total = $Entryitem->find('first', array(
			'fields' => array('SUM(Entryitem.amount) as total'),
			'conditions' => $cr_conditions,
			'joins' => array(
				array(
					'table' => 'entries',
					'alias' => 'Entry',
					'type' => 'LEFT',
					'conditions' => array(
						'Entry.id = Entryitem.entry_id'
					)
				),
			),
		));
		if (empty($total[0]['total'])) {
			$cr_total = 0;
		} else {
			$cr_total = $total[0]['total'];
		}

		/* Add opening balance */
		if ($op_total_dc == 'D') {
			$dr_total_final = calculate($op_total, $dr_total, '+');
			$cr_total_final = $cr_total;
		} else {
			$dr_total_final = $dr_total;
			$cr_total_final = calculate($op_total, $cr_total, '+');
		}

		/* Calculate final opening balance */
		if (calculate($dr_total_final, $cr_total_final, '>')) {
			$op_total = calculate($dr_total_final, $cr_total_final, '-');
			$op_total_dc = 'D';
		} else if (calculate($dr_total_final, $cr_total_final, '==')) {
			$op_total = 0;
			$op_total_dc = $op_total_dc;
		} else {
			$op_total = calculate($cr_total_final, $dr_total_final, '-');
			$op_total_dc = 'C';
		}

		return array('dc' => $op_total_dc, 'amount' => $op_total);
	}

/**
 * Calculate closing balance of specified ledger account for the given
 * date range
 *
 * @param1 int ledger id
 * @param2 date start date
 * @param3 date end date
 * @return array D/C, Amount
 */
	function closingBalance($id, $start_date = null, $end_date = null) {

		if (empty($id)) {
			throw new InternalErrorException(__d('webzash',
				'Ledger not specified. Failed to calculate closing balance.')
			);
		}

		/* Load models that are needed for calculations */
		$Entry = ClassRegistry::init('Webzash.Entry');
		$Entryitem = ClassRegistry::init('Webzash.Entryitem');

		/* Opening balance */
		$op = $this->find('first', array(
			'conditions' => array('Ledger.id' => $id)
		));
		if (!$op) {
			throw new InternalErrorException(__d('webzash',
				'Ledger not found. Failed to calculate closing balance.')
			);
		}

		$op_total = 0;
		$op_total_dc = $op['Ledger']['op_balance_dc'];
		if (is_null($start_date)) {
			if (empty($op['Ledger']['op_balance'])) {
				$op_total = 0;
			} else {
				$op_total = $op['Ledger']['op_balance'];
			}
		}

		$dr_total = 0;
		$cr_total = 0;
		$dr_total_dc = 0;
		$cr_total_dc = 0;

		/* Debit total */
		$dr_conditions = array(
			'Entryitem.ledger_id' => $id,
			'Entryitem.dc' => 'D'
		);
		if (!is_null($start_date)) {
			$dr_conditions['Entry.date >='] = $start_date;
		}
		if (!is_null($end_date)) {
			$dr_conditions['Entry.date <='] = $end_date;
		}
		$total = $Entryitem->find('first', array(
			'fields' => array('SUM(Entryitem.amount) as total'),
			'conditions' => $dr_conditions,
			'joins' => array(
				array(
					'table' => 'entries',
					'alias' => 'Entry',
					'type' => 'LEFT',
					'conditions' => array(
						'Entry.id = Entryitem.entry_id'
					)
				),
			),
		));
		if (empty($total[0]['total'])) {
			$dr_total = 0;
		} else {
			$dr_total = $total[0]['total'];
		}

		/* Credit total */
		$cr_conditions = array(
			'Entryitem.ledger_id' => $id,
			'Entryitem.dc' => 'C'
		);
		if (!is_null($start_date)) {
			$cr_conditions['Entry.date >='] = $start_date;
		}
		if (!is_null($end_date)) {
			$cr_conditions['Entry.date <='] = $end_date;
		}
		$total = $Entryitem->find('first', array(
			'fields' => array('SUM(Entryitem.amount) as total'),
			'conditions' => $cr_conditions,
			'joins' => array(
				array(
					'table' => 'entries',
					'alias' => 'Entry',
					'type' => 'LEFT',
					'conditions' => array(
						'Entry.id = Entryitem.entry_id'
					)
				),
			),
		));
		if (empty($total[0]['total'])) {
			$cr_total = 0;
		} else {
			$cr_total = $total[0]['total'];
		}

		/* Add opening balance */
		if ($op_total_dc == 'D') {
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
			$cl_dc = $op_total_dc;
		} else {
			$cl = calculate($cr_total_dc, $dr_total_dc, '-');
			$cl_dc = 'C';
		}

		return array('dc' => $cl_dc, 'amount' => $cl, 'dr_total' => $dr_total, 'cr_total' => $cr_total);
	}

/**
 * Calculate reconciliation pending of specified ledger account for the given
 * date range
 *
 * @param1 int ledger id
 * @param2 date start date
 * @param3 date end date
 * @return array Debit_Amount, Credit_Amount
 */
	function reconciliationPending($id, $start_date = null, $end_date = null) {

		if (empty($id)) {
			throw new InternalErrorException(__d('webzash',
				'Ledger not specified. Failed to calculate closing balance.')
			);
		}

		/* Load models that are needed for calculations */
		$Entry = ClassRegistry::init('Webzash.Entry');
		$Entryitem = ClassRegistry::init('Webzash.Entryitem');

		$dr_total = 0;
		$cr_total = 0;

		/* Debit total */
		$dr_conditions = array(
			'Entryitem.ledger_id' => $id,
			'Entryitem.dc' => 'D',
			'Entryitem.reconciliation_date' => null
		);
		if (!is_null($start_date)) {
			$dr_conditions['Entry.date >='] = $start_date;
		}
		if (!is_null($end_date)) {
			$dr_conditions['Entry.date <='] = $end_date;
		}
		$total = $Entryitem->find('first', array(
			'fields' => array('SUM(Entryitem.amount) as total'),
			'conditions' => $dr_conditions,
			'joins' => array(
				array(
					'table' => 'entries',
					'alias' => 'Entry',
					'type' => 'LEFT',
					'conditions' => array(
						'Entry.id = Entryitem.entry_id'
					)
				),
			),
		));
		if (empty($total[0]['total'])) {
			$dr_total = 0;
		} else {
			$dr_total = $total[0]['total'];
		}

		/* Credit total */
		$cr_conditions = array(
			'Entryitem.ledger_id' => $id,
			'Entryitem.dc' => 'C',
			'Entryitem.reconciliation_date' => null
		);
		if (!is_null($start_date)) {
			$cr_conditions['Entry.date >='] = $start_date;
		}
		if (!is_null($end_date)) {
			$cr_conditions['Entry.date <='] = $end_date;
		}
		$total = $Entryitem->find('first', array(
			'fields' => array('SUM(Entryitem.amount) as total'),
			'conditions' => $cr_conditions,
			'joins' => array(
				array(
					'table' => 'entries',
					'alias' => 'Entry',
					'type' => 'LEFT',
					'conditions' => array(
						'Entry.id = Entryitem.entry_id'
					)
				),
			),
		));
		if (empty($total[0]['total'])) {
			$cr_total = 0;
		} else {
			$cr_total = $total[0]['total'];
		}

		return array('dr_total' => $dr_total, 'cr_total' => $cr_total);

	}
}
