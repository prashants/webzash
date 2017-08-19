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
* Webzash Plugin Wzuser Model
*
* @package Webzash
* @subpackage Webzash.model
*/
class Wzuser extends WebzashAppModel {

	public $validationDomain = 'webzash';

	/* Validation rules for the Wzuser table */
	public $validate = array(
		'username' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Username cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isUnique',
				'message' => 'Username is already in use',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Username cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'fullname' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Fullname cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Fullname cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'password' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Password cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'email' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Email cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'email',
				'message' => 'Email is not a valid email address',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => 'isUnique',
				'message' => 'Email is already in use',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'timezone' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Timezone cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'role' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Role cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('inList', array('admin', 'manager', 'accountant', 'dataentry', 'guest')),
				'message' => 'Invalid value for role',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'status' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Status cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'boolean',
				'message' => 'Invalid value for status',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'email_verified' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Email verified cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'boolean',
				'message' => 'Invalid value for email verified',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'admin_verified' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Administrator approved cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'boolean',
				'message' => 'Invalid value for administrator approved',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'retry_count' => array(
			'rule1' => array(
				'rule' => 'numeric',
				'message' => 'Retry count is not a valid number',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Retry count cannot contain a decimal point',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'all_accounts' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'All accounts access cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'boolean',
				'message' => 'Invalid value for all accounts access',
				'required' => true,
				'allowEmpty' => false,
			),
		),
	);
}
