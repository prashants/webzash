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
* Webzash Plugin Wzsetting Model
*
* @package Webzash
* @subpackage Webzash.model
*/
class Wzsetting extends WebzashAppModel {

	public $validationDomain = 'webzash';

	/* Validation rules for the Wzsetting table */
	public $validate = array(

		'sitename' => array(
			'rule1' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Sitename cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'drcr_toby' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'For entry use cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('inList', array('drcr', 'toby')),
				'message' => 'For entry use is not valid',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'enable_logging' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Enable logging cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'boolean',
				'message' => 'Invalid value for enable logging',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'row_count' => array(
			'rule1' => array(
				'rule' => 'numeric',
				'message' => 'Row count is not a valid number',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Row count should be greater than 0',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('comparison', '<=', 100),
				'message' => 'Row count should be less than or equal to 100',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule4' => array(
				'rule' => 'naturalNumber',
				'message' => 'Row count cannot contain a decimal point',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'user_registration' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'User registration cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'boolean',
				'message' => 'Invalid value for user registration',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'admin_verification' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Admin verification cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'boolean',
				'message' => 'Invalid value for admin verification',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'email_verification' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Email verification cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'boolean',
				'message' => 'Invalid value for email verification',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'email_protocol' => array(
			'rule1' => array(
				'rule' => array('inList', array('Smtp', 'Mail')),
				'message' => 'Invalid value for email protocol',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'email_host' => array(
			'rule1' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Hostname cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => true,
			),
		),
		'email_port' => array(
			'rule1' => array(
				'rule' => 'numeric',
				'message' => 'Port is not a valid number',
				'required' => true,
				'allowEmpty' => true,
			),
			'rule2' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Port should be greater than 0',
				'required' => true,
				'allowEmpty' => true,
			),
			'rule3' => array(
				'rule' => array('comparison', '<=', 65000),
				'message' => 'Port should be less than 65000',
				'required'   => true,
				'allowEmpty' => true,
			),
			'rule4' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Port cannot contain a decimal point',
				'required' => true,
				'allowEmpty' => true,
			),
		),
		'email_tls' => array(
			'rule1' => array(
				'rule' => 'boolean',
				'message' => 'Invalid value for Use TLS',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'email_username' => array(
			'rule1' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Username cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => true,
			),
		),
		'email_password' => array(
			'rule1' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Password cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => true,
			),
		),
		'email_from' => array(
			'rule1' => array(
				'rule' => array('maxLength', 255),
				'message' => 'From cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => true,
			),
		),

	);
}
