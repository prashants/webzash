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
 * Webzash Plugin Entryitem Model
 *
 * @package Webzash
 * @subpackage Webzash.Model
 */
class Entryitem extends WebzashAppModel {

	public $validationDomain = 'webzash';

	/* Validation rules for the Entryitem table */
	public $validate = array(
		'entry_id' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Entry id cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'numeric',
				'message' => 'Entry id is not a valid number',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 18),
				'message' => 'Entry id length cannot be more than 18',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule4' => array(
				'rule' => 'validEntry',
				'message' => 'Entry id is not valid',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'ledger_id' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Ledger id cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'numeric',
				'message' => 'Ledger id is not a valid number',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 18),
				'message' => 'Ledger id length cannot be more than 18',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule4' => array(
				'rule' => 'validLedger',
				'message' => 'Ledger id is not valid',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'amount' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Amount cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isAmount',
				'message' => 'Amount is not a valid amount',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 28),
				'message' => 'Amount total length cannot be more than 28',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule4' => array(
				'rule' => 'isPositive',
				'message' => 'Amount cannot be less than 0.00',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'dc' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Dr/Cr cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isDC',
				'message' => 'Invalid value for Dr/Cr',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'reconciliation_date' => array(
			'rule1' => array(
				'rule' => 'fullDateTime',
				'message' => 'Invalid value for reconciliation date',
				'required' => false,
				'allowEmpty' => false,
			),
		),
	);

/**
 * Validation - Check if entry is valid
 */
	public function validEntry($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];

		/* Load the Entry model */
		App::import("Webzash.Model", "Entry");
		$Entry = new Entry();

		/* Check if entry exists */
		if ($Entry->exists($value)) {
			return true;
		} else {
			return false;
		}
	}

/**
 * Validation - Check if ledger is valid
 */
	public function validLedger($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];

		/* Load the Group model */
		App::import("Webzash.Model", "Ledger");
		$Ledger = new Ledger();

		/* Check if ledger exists */
		if ($Ledger->exists($value)) {
			return true;
		} else {
			return false;
		}
	}

/**
 * Validation - Check if value is a proper decimal number with 2 decimal places
 */
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

/**
 * Validation - Check if value is a positive value
 */
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

/**
 * Validation - Check if value is either 'D' or 'C'
 */
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

/**
 * Validation - Check if valid datetime
 */
	public function fullDateTime($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];

		$unixtime = strtotime($value);

		if (FALSE !== $unixtime) {
			return true;
		} else {
			return false;
		}
	}

}
