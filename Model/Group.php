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
* Webzash Plugin Group Model
*
* @package Webzash
* @subpackage Webzash.model
*/
class Group extends WebzashAppModel {

	public $validationDomain = 'webzash';

	/* Validation rules for the Group table */
	public $validate = array(
		'parent_id' => array(
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
				'rule' => 'parentValid',
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
				'message' => 'Group name cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isUnique',
				'message' => 'Group name is already in use',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Group name cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'code' => array(
			'rule1' => array(
				'rule' => 'isUnique',
				'message' => 'Group code is already in use',
				'required' => true,
				'allowEmpty' => true,
			),
			'rule2' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Group code cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => true,
			),
			'rule3' => array(
				'rule' => 'isUniqueInLedger',
				'message' => 'Group code is already in use by a ledger account',
				'required' => true,
				'allowEmpty' => true,
			),
		),
		'affects_gross' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Affects Gross or Net Profit & Loss cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'boolean',
				'message' => 'Invalid value for whether the account group affects Gross or Net Profit & Loss',
				'required' => true,
				'allowEmpty' => false,
			),
		),
	);

	/* Validation - Check if parent_id is a valid id */
	public function parentValid($data) {
		if (empty($data['parent_id'])) {
			return false;
		}

		$parentCount = $this->find('count', array(
		    'conditions' => array('id' => $data['parent_id']),
		));

		if ($parentCount < 1) {
			return false;
		} else {
			return true;
		}
	}

	/* Validation - Check if code is unique across groups and ledgers */
	public function isUniqueInLedger($data) {
		if (empty($data['code'])) {
			return true;
		}

		/* Load the Ledger model */
		App::import("Webzash.Model", "Ledger");
		$Ledger = new Ledger();

		$count = $Ledger->find('count', array(
		    'conditions' => array('code' => $data['code']),
		));

		if ($count != 0) {
			return false;
		} else {
			return true;
		}
	}
}
