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
* Webzash Plugin EntryType Model
*
* @package Webzash
* @subpackage Webzash.model
*/
class Entrytype extends WebzashAppModel {

	/* Validation rules for the Entrytypes table */
	public $validate = array(
		'label' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Entry type label cannot be empty',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isUnique',
				'message' => 'Entry type label is already in use',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 100),
				'message' => 'Entry type label cannot be more than 100 characters',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule4' => array(
				'rule' => 'alphaNumeric',
				'message' => 'Entry type label can contain only letter and digits',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule5' => array(
				'rule' => array('comparison', '!=', 0),
				'message' => 'Entry type label cannot be "0"',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'name' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Entry type name cannot be empty',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isUnique',
				'message' => 'Entry type name is already in use',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 100),
				'message' => 'Entry type name cannot be more than 100 characters',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'description' => array(
			'rule1' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Entry type description cannot be more than 255 characters',
				'required'   => true,
				'allowEmpty' => true,
			),
		),
		'base_type' => array(
			'rule1' => array(
				'rule' => array('inList', array('1')),
				'message' => 'Invalid option',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'numbering' => array(
			'rule1' => array(
				'rule' => array('inList', array('1', '2', '3')),
				'message' => 'Invalid option',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'prefix' => array(
			'rule1' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Entry type prefix cannot be more than 255 characters',
				'required'   => true,
				'allowEmpty' => true,
			),
		),
		'suffix' => array(
			'rule1' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Entry type suffix cannot be more than 255 characters',
				'required'   => true,
				'allowEmpty' => true,
			),
		),
		'zero_padding' => array(
			'rule1' => array(
				'rule' => 'numeric',
				'message' => 'Invalid number',
				'required'   => true,
				'allowEmpty' => true,
			),
			'rule2' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Number outside valid range',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('comparison', '<=', 99),
				'message' => 'Number outside valid range',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule4' => array(
				'rule'    => array('naturalNumber', true),
				'message' => 'Invalid number',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'restriction_bankcash' => array(
			'rule1' => array(
				'rule' => array('inList', array('1', '2', '3', '4', '5')),
				'message' => 'Invalid option',
				'required'   => true,
				'allowEmpty' => false,
			),
		),

	);

}
