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
	public function entryLedgers($id) {
		/* Load the Entry model */
		App::import("Webzash.Model", "Entry");
		$Entry = new Entry();
		return $Entry->entryLedgers($id);
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
			case 'Database/Postgres': return __d('webzash', 'Postgres SQL');
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
			'Database/Postgres' => __d('webzash', 'Postgres SQL'),
		);
	}

	function search_range_options() {
		return array(
			'1' => __d('webzash', 'Equal to'),
			'2' => __d('webzash', 'Less than or Equal to'),
			'3' => __d('webzash', 'Greater than or equal to'),
			'4' => __d('webzash', 'In between'),
		);
	}

}
