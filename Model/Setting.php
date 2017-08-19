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
* Webzash Plugin Setting Model
*
* @package Webzash
* @subpackage Webzash.model
*/
class Setting extends WebzashAppModel {

	public $validationDomain = 'webzash';

	/* Validation rules for the Setting table */
	public $validate = array(

		'id' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Settings id cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('inList', array('1')),
				'message' => 'Invalid value for settings id',
				'required' => true,
				'allowEmpty' => false,
			),
		),

		'name' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Company / Personal Name name cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Company / Personal Name name cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'address' => array(
			'rule1' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Account address cannot be more than 255 characters',
				'required' => false,
				'allowEmpty' => true,
			),
		),
		'email' => array(
			'rule1' => array(
				'rule' => 'email',
				'message' => 'Invalid email address',
				'required' => false,
				'allowEmpty' => true,
			),
		),
		'fy_start' => array(
			'rule1' => array(
				'rule' => 'fullDateTime',
				'message' => 'Invalid value for financial year start',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'fy_end' => array(
			'rule1' => array(
				'rule' => 'fullDateTime',
				'message' => 'Invalid value for financial year end',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'afterStart',
				'message' => 'Financial year end should be after financial year start',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => 'checkEntries',
				'message' => 'Entries present beyond the financial year start and end dates',
				'required' => true,
				'allowEmpty' => false,
				'on' => 'update',
			),
		),
		'currency_symbol' => array(
			'rule1' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Currency symbol cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => true,
			),
		),
		'currency_format' => array(
			'rule1' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Currency format cannot be more than 255 characters',
				'required' => true,
				'allowEmpty' => true,
			),
		),
		'decimal_places' => array(
			'rule1' => array(
				'rule' => array('inList', array('2', '3')),
				'message' => 'Invalid value for decimal places',
				'required' => true,
				'allowEmpty' => true,
			),
		),
		'date_format' => array(
			'rule1' => array(
				'rule' => array('maxLength', 13),
				'message' => 'Date format cannot be more than 13 characters',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('inList', array('d-M-Y|dd-M-yy', 'M-d-Y|M-dd-yy', 'Y-M-d|yy-M-dd')),
				'message' => 'Invalid option for date format',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'timezone' => array(
			'rule1' => array(
				'rule' => array('maxLength', 100),
				'message' => 'Timezone cannot be more than 100 characters',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'timezone',
				'message' => 'Invalid timezone format',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'manage_inventory' => array(
			'rule1' => array(
				'rule' => 'boolean',
				'message' => 'Invalid value for manage inventory',
				'required' => true,
				'allowEmpty' => false,
			),
		),

		'account_locked' => array(
			'rule1' => array(
				'rule' => 'boolean',
				'message' => 'Invalid value for lock',
				'required' => true,
				'allowEmpty' => false,
			),
		),

		'email_use_default' => array(
			'rule1' => array(
				'rule' => 'boolean',
				'message' => 'Invalid value for use default email settings',
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
				'required' => true,
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

		'print_paper_height' => array(
			'rule1' => array(
				'rule' => 'numeric',
				'message' => 'Height is not a valid number',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Height should be more than 0',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('comparison', '<=', 1000),
				'message' => 'Height should be less than 1000',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'print_paper_width' => array(
			'rule1' => array(
				'rule' => 'numeric',
				'message' => 'Width is not a valid number',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Width should be more than 0',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('comparison', '<=', 1000),
				'message' => 'Width should be less than 1000',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'print_margin_top' => array(
			'rule1' => array(
				'rule' => 'numeric',
				'message' => 'Top margin is not a valid number',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Top margin should be more than 0',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('comparison', '<=', 1000),
				'message' => 'Top margin should be less than 1000',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'print_margin_bottom' => array(
			'rule1' => array(
				'rule' => 'numeric',
				'message' => 'Bottom margin is not a valid number',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Bottom margin should be more than 0',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('comparison', '<=', 1000),
				'message' => 'Bottom margin should be less than 1000',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'print_margin_left' => array(
			'rule1' => array(
				'rule' => 'numeric',
				'message' => 'Left margin is not a valid number',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Left margin should be more than 0',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('comparison', '<=', 1000),
				'message' => 'Left margin should be less than 1000',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'print_margin_right' => array(
			'rule1' => array(
				'rule' => 'numeric',
				'message' => 'Right margin is not a valid number',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Right margin should be more than 0',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('comparison', '<=', 1000),
				'message' => 'Right margin should be less than 1000',
				'required' => true,
				'allowEmpty' => false,
			),
		),

		'print_orientation' => array(
			'rule1' => array(
				'rule' => array('inList', array('P', 'L')),
				'message' => 'Invalid option for orientation',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'print_page_format' => array(
			'rule1' => array(
				'rule' => array('inList', array('H', 'T')),
				'message' => 'Invalid option for page format',
				'required' => true,
				'allowEmpty' => false,
			),
		),
	);

	/* Validation - Check if valid timezone */
	public function timezone($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];
		$zones = DateTimeZone::listIdentifiers();
		return in_array($value, $zones);
	}

	/* Validation - Check if valid datetime */
	public function fullDateTime($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];

		$unixtime = strtotime($value . ' 00:00:00');

		if ($unixtime !== FALSE) {
			return true;
		} else {
			return false;
		}
	}

	/* Validation - Check if financial end if after financial start */
	public function afterStart($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];

		$startdate = strtotime($this->data['Setting']['fy_start'] . ' 00:00:00');
		$enddate = strtotime($value . ' 00:00:00');

		if ($startdate < $enddate) {
			return true;
		} else {
			return false;
		}
	}

	/* Validation - Check if entries present beyond financial year start and end */
	public function checkEntries($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];

		$startdate = dateToSql($this->data['Setting']['fy_start']);
		$enddate = dateToSql($value);

		/* Load the Ledger model */
		App::import("Webzash.Model", "Entry");
		$Entry = new Entry();

		$count = $Entry->find('count', array(
			'conditions' => array(
				'OR' => array(
					'Entry.date <' => $startdate,
					'Entry.date >' => $enddate,
				),
			),
		));

		if ($count != 0) {
			return false;
		} else {
			return true;
		}
	}
}
