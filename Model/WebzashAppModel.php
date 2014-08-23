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

App::uses('AppModel', 'Model');

/**
 * Webzash App Model
 *
 * @package Webzash
 * @subpackage Webzash.models
 */
class WebzashAppModel extends AppModel {

	function __construct($id = false, $table = null, $ds = null) {

		/* Read the URL to get the controller name */
		$url_params = Router::getParams();
		if (empty($url_params)) {
			parent::__construct($id, $table, $ds);
			return;
		}

		/* Activate account database based on the controller name. If admin section use the 'wz' master database */
		if ($url_params['controller'] == 'admin' || $url_params['controller'] == 'wzusers' ||
			$url_params['controller'] == 'wzaccounts' || $url_params['controller'] == 'wzsettings') {
			$this->useDbConfig = 'wz';
		} else {
			$this->useDbConfig = 'wz_accconfig';
		}

		parent::__construct($id, $table, $ds);
		return;
	}

}
