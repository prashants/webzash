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

App::uses('Component', 'Controller');

class PermissionComponent extends Component {

	public $components = array('Session');

/**
 * Check if a role stored in session is allow a particular action
 *
 * @return boolean return true is access is allow, false otherwise
 */
	public function allow($action_name)
	{
		$role = $this->Session->read('role');

		$permissions['manager'] = array(
			'view entry',
			'create entry',
			'edit entry',
			'delete entry',
			'print entry',
			'email entry',
			'download entry',
			'create ledger',
			'edit ledger',
			'delete ledger',
			'create group',
			'edit group',
			'delete group',
			'create tag',
			'edit tag',
			'delete tag',
			'view reports',
			'view log',
			'clear log',
			'change account settings',
			'cf account',
			'backup account',
		);
		$permissions['accountant'] = array(
			'view entry',
			'create entry',
			'edit entry',
			'delete entry',
			'print entry',
			'email entry',
			'download entry',
			'create ledger',
			'edit ledger',
			'delete ledger',
			'create group',
			'edit group',
			'delete group',
			'create tag',
			'edit tag',
			'delete tag',
			'view reports',
			'view log',
			'clear log',
		);
		$permissions['dataentry'] = array(
			'view entry',
			'create entry',
			'edit entry',
			'delete entry',
			'print entry',
			'email entry',
			'download entry',
			'create ledger',
			'edit ledger',
		);
		$permissions['guest'] = array(
			'view entry',
			'print entry',
			'email entry',
			'download entry',
		);

		if (!isset($role)) {
			return FALSE;
		}

		/* If user is admin then always allow full access */
		if ($role == "admin") {
			return TRUE;
		}

		/* If invaid user role then deny access */
		if (!isset($permissions[$role])) {
			return FALSE;
		}

		/* Check if the user role is allowed access */
		if (in_array($action_name, $permissions[$role])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
