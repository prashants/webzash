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

	/* Validation rules for the Wzuser table */
	public $validate = array(
		'username' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'User name cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isUnique',
				'message' => 'User name is already in use',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 255),
				'message' => 'User name cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'fullname' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Full name cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('maxLength', 255),
				'message' => 'User name cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'password' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Password cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'email' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Email cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('email', true),
				'message' => 'Email is not a valid email address',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'role' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Role cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('inList', array('admin', 'manager', 'accountant', 'dataentry', 'guest')),
				'message' => 'Role is not valid',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'status' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Status cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('inList', array('0', '1', '2', '3')),
				'message' => 'Status is not valid',
				'required' => true,
				'allowEmpty' => false,
			),
		),
	);
}
