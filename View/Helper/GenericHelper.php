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

/**
* Webzash Plugin Generic Helper
*
* @package Webzash
* @subpackage Webzash.View
*/
class GenericHelper extends AppHelper {

	var $helpers = array('Html', 'Session');

/**
 * Helper method to return the tag
 */
	function showTag($id) {

		if (empty($id)) {
			return '';
		}

		if (empty($this->_View->viewVars['allTags'])) {
			return '';
		}

		$tags = $this->_View->viewVars['allTags'];

		if (empty($tags[$id])) {
			return __d('webzash', 'ERROR');
		}

		$tag = $tags[$id];

		return '<span class="tag" style="color:#' . h($tag['color']) .
			'; background-color:#' . h($tag['background']) . ';">' .
			$this->Html->link($tag['title'], array(
					'plugin' => 'webzash',
					'controller' => 'entries',
					'action' => 'index',
					'tag' => $tag['id']
				),
				array('style' => 'color:#' . h($tag['color']) . ';')
			) . '</span>';
	}

/**
 * Show the entry ledger details
 */
	function entryLedgers($id) {
		/* Load the Entry model */
		App::import("Webzash.Model", "Entry");
		$Entry = new Entry();
		return $Entry->entryLedgers($id);
	}

/**
 * Add a row to excel sheet
 */
	function xlsAddRow($row)
	{
		$cells = "";
		foreach ($row as $k => $v) {
			$type = 'String';
			if (is_numeric($v)) {
				$type = 'Number';
			}
			$v = h($v);
			$cells .= "<Cell><Data ss:Type=\"$type\">" . $v . "</Data></Cell>\n";
		}
		return "<Row>\n" . $cells . "</Row>\n";
	}


/**
 * Wzuser return status string
 */
	function wzuser_status($status) {
		switch ($status) {
			case '0': return __d('webzash', 'Disabled');
			case '1': return __d('webzash', 'Enabled');
			default: return __d('webzash', 'ERROR');
		}
	}
/**
 * Wzuser return status options
 */
	function wzuser_status_options() {
		return array(
			'0' => __d('webzash', 'Disabled'),
			'1' => __d('webzash', 'Enabled'),
		);
	}

/**
 * Wzuser return status string
 */
	function wzuser_role($role) {
		switch ($role) {
			case 'admin': return __d('webzash', 'Administrator');
			case 'manager': return __d('webzash', 'Manager');
			case 'accountant': return __d('webzash', 'Accountant');
			case 'dataentry': return __d('webzash', 'Data entry operator');
			case 'guest': return __d('webzash', 'Guest');
			default: return __d('webzash', 'ERROR');
		}
	}
/**
 * Wzuser return status options
 */
	function wzuser_role_options() {
		return array(
			'admin' => __d('webzash', 'Administrator'),
			'manager' => __d('webzash', 'Manager'),
			'accountant' => __d('webzash', 'Accountant'),
			'dataentry' => __d('webzash', 'Data entry operator'),
			'guest' => __d('webzash', 'Guest'),
		);
	}

/**
 * Wzaccount return database type string
 */
	function wzaccount_dbtype($dbtype) {
		switch ($dbtype) {
			case 'Database/Mysql': return __d('webzash', 'MySQL');
			case 'Database/Sqlserver': return __d('webzash', 'MS SQL Server');
			case 'Database/Postgres': return __d('webzash', 'Postgres SQL (BETA)');
			default: return __d('webzash', 'ERROR');
		}
	}

/**
 * Wzaccount return database type options
 */
	function wzaccount_dbtype_options() {
		return array(
			'Database/Mysql' => __d('webzash', 'MySQL'),
			'Database/Sqlserver' => __d('webzash', 'MS SQL Server'),
			'Database/Postgres' => __d('webzash', 'Postgres SQL (BETA)'),
		);
	}

/**
 * Wzsetting return row count options
 */
	function wzsetting_row_count_options() {
		return array(
			'10' => '10',
			'25' => '25',
			'50' => '50',
			'100' => '100',
		);
	}

/**
 * Wzsetting return drcr or toby options
 */
	function wzsetting_drcr_toby_options() {
		return array(
			'drcr' => __d('webzash', 'Dr / Cr'),
			'toby' => __d('webzash', 'To / By'),
		);
	}

/**
 * Group return net or gross options
 */
	function group_netgross_options() {
		return array(
			1 => __d('webzash', 'Gross Profit & Loss'),
			0 => __d('webzash', 'Net Profit & Loss'),
		);
	}

/**
 * Entrytype return numbering options
 */
	function entrytype_numbering_options() {
		return array(
			'1' => __d('webzash', 'Auto'),
			'2' => __d('webzash', 'Manual (required)'),
			'3' => __d('webzash', 'Manual (optional)'),
		);
	}

/**
 * Entrytype return restriction options
 */
	function entrytype_restriction_options() {
		return array(
			'1' => __d('webzash', 'Unrestricted'),
			'2' => __d('webzash', 'Atleast one Bank or Cash account must be present on Debit side'),
			'3' => __d('webzash', 'Atleast one Bank or Cash account must be present on Credit side'),
			'4' => __d('webzash', 'Only Bank or Cash account can be present on both Debit and Credit side'),
			'5' => __d('webzash', 'Only NON Bank or Cash account can be present on both Debit and Credit side'),
		);
	}

/**
 * Search return range options
 */
	function search_range_options() {
		return array(
			'1' => __d('webzash', 'Equal to'),
			'2' => __d('webzash', 'Less than or Equal to'),
			'3' => __d('webzash', 'Greater than or equal to'),
			'4' => __d('webzash', 'In between'),
		);
	}

/**
 * Settings return printer orientation options
 */
	function printer_orientation_options() {
		return array(
			'P' => __d('webzash', 'Potrait'),
			'L' => __d('webzash', 'Landscape'),
		);
	}

/**
 * Settings return printer page format options
 */
	function printer_pageformat_options() {
		return array(
			'H' => __d('webzash', 'HTML'),
			'T' => __d('webzash', 'Text'),
		);
	}

/**
 * return mail protocol options
 */
	function mail_protocol_options() {
		return array(
			'Smtp' => __d('webzash', 'smtp'),
			'Mail' => __d('webzash', 'mail'),
		);
	}

/**
 * return date format options
 *
 * Date format : PHP Format|Javascript Format
 */
	function dateformat_options() {
		return array(
			'd-M-Y|dd-M-yy' => __d('webzash', 'Day-Month-Year'),
			'M-d-Y|M-dd-yy' => __d('webzash', 'Month-Day-Year'),
			'Y-M-d|yy-M-dd' => __d('webzash', 'Year-Month-Day'),
		);
	}

/**
 * return decimal places options
 */
	function decimal_places_options() {
		return array(
			'2' => '2',
			'3' => '3',
		);
	}

/**
 * return currency format options
 */
	function currency_format_options() {
		return array(
			'none' => '(NONE)',
			'##,###.##' => '##,###.##',
			'##,##.##' => '##,##.##',
			'###,###.##' => '###,###.##',
		);
	}
}
