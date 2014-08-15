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

App::uses('AppController', 'Controller');

/**
 * Webzash App Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class WebzashAppController extends AppController {

	public $helpers = array('Webzash.Menu', 'Webzash.Generic');

	public $components = array(
		'Security', 'Session', 'Paginator', 'Webzash.Permission',
		'Auth' => array(
			'loginRedirect' => array(
				'plugin' => 'webzash',
				'controller' => 'dashboard',
				'action' => 'index'
			),
			'logoutRedirect' => array(
				'plugin' => 'webzash',
				'controller' => 'wzusers',
				'action' => 'login'
			),
			'loginAction' => array(
				'plugin' => 'webzash',
				'controller' => 'wzusers',
				'action' => 'login'
			),
			'authenticate' => array(
				'Form' => array(
					'fields' => array('username' => 'username', 'password' => 'password'),
					'userModel' => 'Wzuser',
				)
			),
			'authorize' => array('Controller'),
		)
	);

	public function isAuthorized($user) {
		/* Admin can access every action */
		if (isset($user['role']) && $user['role'] === 'admin') {
			return true;
		}

		/* Default deny */
		return false;
	}
}
