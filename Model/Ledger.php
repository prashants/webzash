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

	public $belongsTo = array(
		'Group' => array(
			'className'  => 'Group',
			'foreignKey'  => 'group_id',
		)
	);
	public $hasMany = array(
		'Entryitem' => array(
			'className' => 'Entryitem',
		)
	);

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
		'cl_balance' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Closing balance cannot be empty',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isAmount',
				'message' => 'Closing balance is not a valid amount',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 28),
				'message' => 'Closing balance length cannot be more than 28',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'cl_balance_dc' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Closing balance Dr/Cr cannot be empty',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isDC',
				'message' => 'Closing balance Dr/Cr can only be debit or credit',
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
		if (!isset($data['group_id'])) {
			return false;
		}

		$groupCount = $this->Group->find('count', array(
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

	/**
	 * Update the closing balance of a ledger in a concurrent save way
	 *
	 * Write a unique sha1 to the 'lock_hash' column in the database, then
	 * calculate and update the ledger total, after that again read the
	 * 'lock_hash' from the database to see if it matches the previously
	 * read value. If it does, it safe and no one else has updated it.
	 * If it doesnt match then the closing balance has been updated by
	 * someone else and we have to repeat the entire process again.
	 */
	public function updateClosingBalance($id) {

		while (1) {
			/* Generate random hash */
			$seed = 'JvKnrQWPsThuJteNQAuH';
			$hash = sha1(uniqid($seed . mt_rand(), true));
			$hash = sha1(uniqid($hash . mt_rand(), true));

			/* Save the lock hash string */
			$this->read(null, $id);
			$this->saveField('lock_hash', $hash);

			/* Opening balance */
			$op = $this->find('first', array(
				'conditions' => array('Ledger.id' => $id)
			));
			if (empty($op['Ledger']['op_balance'])) {
				$op_total = 0;
			} else {
				$op_total = $op['Ledger']['op_balance'];
			}

			$dr_total = 0;
			$cr_total = 0;

			$this->Entryitem->virtualFields = array('total' => 'SUM(Entryitem.amount)');

			/* Debit total */
			$total = $this->Entryitem->find('first', array(
				'fields' => array('total'),
				'conditions' => array('Entryitem.ledger_id' => $id, 'Entryitem.dc' => 'D')
			));
			if (empty($total['Entryitem']['total'])) {
				$dr_total = 0;
			} else {
				$dr_total = $total['Entryitem']['total'];
			}

			/* Credit total */
			$total = $this->Entryitem->find('first', array(
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
				$dr_total = calculate($op_total, $dr_total, '+');
			} else {
				$cr_total = calculate($op_total, $cr_total, '+');
			}

			/* Calculate and update closing balance */
			$cl = 0;
			$cl_dc = '';
			if (calculate($dr_total, $cr_total, '>')) {
				$cl = calculate($dr_total, $cr_total, '-');
				$cl_dc = 'D';
			} else if (calculate($cr_total, $dr_total, '==')) {
				$cl = 0;
				$cl_dc = $op['Ledger']['op_balance_dc'];
			} else {
				$cl = calculate($cr_total, $dr_total, '-');
				$cl_dc = 'C';
			}

			$this->saveField('cl_balance', $cl);
			$this->saveField('cl_balance_dc', $cl_dc);

			/* Read the lock_hash from database to check if it has not changed */
			$this->read(null, $id);
			/* If lock_hash is same then we are ok, if not redo all the calculations */
			if ($this->data['Ledger']['lock_hash'] == $hash) {
				break;
			}
		}
		return true;
	}
}

