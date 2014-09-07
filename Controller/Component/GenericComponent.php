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
App::uses('CakeEmail', 'Network/Email');

/**
 * Webzash Plugin Generic Component
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class GenericComponent extends Component {

	public $components = array('Session');

/**
 * Send email
 */
	public function sendEmail($to = '', $subject = '', $view = '', $viewVars = array(),
		$useDefault = true, $errorInSesson = false) {
		if ($useDefault == true) {
			App::import("Webzash.Model", "Wzsetting");
			$this->Wzsetting = new Wzsetting();
			$this->Wzsetting->useDbConfig = 'wz';

			$wzsetting = $this->Wzsetting->findById(1);

			if (!$wzsetting) {
				if ($errorInSesson) {
					$this->Session->write('emailError', true);
				}
				return false;
			}

			$viewVars['sitename'] = $wzsetting['Wzsetting']['sitename'];

			$config = array(
				'host' => $wzsetting['Wzsetting']['email_host'],
				'port' => $wzsetting['Wzsetting']['email_port'],
				'username' => $wzsetting['Wzsetting']['email_username'],
				'password' => $wzsetting['Wzsetting']['email_password'],
				'transport' => $wzsetting['Wzsetting']['email_protocol'],
			);
			if ($wzsetting['Wzsetting']['email_tls'] == '1') {
				$config['tls'] = true;
			} else {
				$config['tls'] = false;
			}

			$Email = new CakeEmail();
			$Email->config($config);
			try {
				$Email->from(array($wzsetting['Wzsetting']['email_username'] =>
						$wzsetting['Wzsetting']['email_from']))
					->template('Webzash.' . $view, 'Webzash.email')
					->viewVars($viewVars)
					->emailFormat('both')
					->to($to)
					->subject($subject)
					->send();
			} catch (Exception $e) {
				if ($errorInSesson) {
					$this->Session->write('emailError', true);
				}
				return false;
			}

		} else {
			App::import("Webzash.Model", "Setting");
			$this->Setting = new Setting();

			$setting = $this->Setting->findById(1);

			if (!$setting) {
				if ($errorInSesson) {
					$this->Session->write('emailError', true);
				}
				return false;
			}

			/* TODO : $viewVars['sitename'] = $wzsetting['Wzsetting']['sitename']; */
			$viewVars['name'] = $setting['Setting']['name'];
			$viewVars['address'] = $setting['Setting']['address'];
			$viewVars['email'] = $setting['Setting']['email'];
			$viewVars['currency_symbol'] = $setting['Setting']['currency_symbol'];
			$viewVars['date_format'] = $setting['Setting']['date_format'];

			$config = array(
				'host' => $setting['Setting']['email_host'],
				'port' => $setting['Setting']['email_port'],
				'username' => $setting['Setting']['email_username'],
				'password' => $setting['Setting']['email_password'],
				'transport' => $setting['Setting']['email_protocol'],
			);
			if ($setting['Setting']['email_tls'] == '1') {
				$config['tls'] = true;
			} else {
				$config['tls'] = false;
			}

			$Email = new CakeEmail();
			$Email->config($config);
			try {
				$Email->from(array($setting['Setting']['email_username'] =>
						$setting['Setting']['email_from']))
					->template('Webzash.' . $view, 'Webzash.email')
					->viewVars($viewVars)
					->emailFormat('both')
					->to($to)
					->subject($subject)
					->send();
			} catch (Exception $e) {
				if ($errorInSesson) {
					$this->Session->write('emailError', true);
				}
				return false;
			}
		}

		return true;
	}
}
