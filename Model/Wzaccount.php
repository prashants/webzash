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
* Webzash Plugin Wzaccount Model
*
* @package Webzash
* @subpackage Webzash.model
*/
class Wzaccount extends WebzashAppModel {

	/* Validation rules for the Wzaccount table */
	public $validate = array(
		'label' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Account label cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isUnique',
				'message' => 'Account label is already in use',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Account label cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule4' => array(
				'rule' => 'alphaNumeric',
				'message' => 'Account label can only be alpha-numeric',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'db_datasource' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Database type cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('inList', array('Database/Mysql', 'Database/Sqlserver', 'Database/Postgres', 'Database/Sqlite')),
				'message' => 'Database type is not valid',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'db_name' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Database name cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Database name cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'db_hostname' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Database hostname cannot be empty',
				'required' => true,
				'allowEmpty' => true,
			),
			'rule2' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Database hostname cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => true,
			),
		),
		'db_port' => array(
			'rule1' => array(
				'rule' => 'numeric',
				'message' => 'Invalid number',
				'required'   => true,
				'allowEmpty' => true,
			),
			'rule2' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Port outside valid range',
				'required'   => true,
				'allowEmpty' => true,
			),
			'rule3' => array(
				'rule' => array('comparison', '<=', 65000),
				'message' => 'Port outside valid range',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule4' => array(
				'rule'    => 'naturalNumber',
				'message' => 'Port address is invalid',
				'required'   => true,
				'allowEmpty' => true,
			),
		),
		'db_prefix' => array(
			'rule1' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Database name cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => true,
			),
		),
		'db_username' => array(
			'rule1' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Database username cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => true,
			),
		),
		'db_password' => array(
			'rule1' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Database password cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => true,
			),
		),
		'db_persistent' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Database persistent connection cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'boolean',
				'message' => 'Incorrect value for database persistent connection',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'db_schema' => array(
			'rule1' => array(
				'rule' => array('maxLength', 10),
				'message' => 'Database schema cannot be more than 255 characters',
				'required' => false,
				'allowEmpty' => true,
			),
		),
		'db_unixsocket' => array(
			'rule1' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Database unixsocket cannot be more than 255 characters',
				'required' => false,
				'allowEmpty' => true,
			),
		),
		'db_settings' => array(
			'rule1' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Database settings cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => true,
			),
		),
	);
}
