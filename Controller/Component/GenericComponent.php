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
 * Adds a flash message.
 * Updates "messages" session content (to enable multiple messages of one type).
 *
 * https://github.com/dereuromark/cakephp-tools
 * The MIT License
 *
 * @param string $message Message to output.
 * @param string $type Type ('error', 'warning', 'success', 'info' or custom class).
 * @return void
 */
	public function flashMessage($message, $type = null) {
		if (!$type) {
			$type = 'info';
		}

		$old = (array)$this->Session->read('messages');
		if (isset($old[$type]) && count($old[$type]) > 99) {
			array_shift($old[$type]);
		}
		$old[$type][] = $message;
		$this->Session->write('messages', $old);
	}

/**
 * Adds a transient flash message.
 * These flash messages that are not saved (only available for current view),
 * will be merged into the session flash ones prior to output.
 *
 * https://github.com/dereuromark/cakephp-tools
 * The MIT License
 *
 * @param string $message Message to output.
 * @param string $type Type ('error', 'warning', 'success', 'info' or custom class).
 * @return void
 */
	public static function transientFlashMessage($message, $type = null) {
		if (!$type) {
			$type = 'info';
		}

		$old = (array)Configure::read('messages');
		if (isset($old[$type]) && count($old[$type]) > 99) {
			array_shift($old[$type]);
		}
		$old[$type][] = $message;
		Configure::write('messages', $old);
	}

/**
 * Send email
 */
	public function sendEmail($to = '', $subject = '', $view = '', $viewVars = array()) {
		App::import("Webzash.Model", "Wzsetting");
		$this->Wzsetting = new Wzsetting();
		$this->Wzsetting->useDbConfig = 'wz';

		$wzsetting = $this->Wzsetting->findById(1);

		if (!$wzsetting) {
			return;
		}

		$viewVars['sitename'] = $wzsetting['Wzsetting']['sitename'];

		$master = array(
			'host' => $wzsetting['Wzsetting']['email_host'],
			'port' => $wzsetting['Wzsetting']['email_port'],
			'username' => $wzsetting['Wzsetting']['email_username'],
			'password' => $wzsetting['Wzsetting']['email_password'],
			'transport' => $wzsetting['Wzsetting']['email_protocol'],
		);

		$Email = new CakeEmail();
		$Email->config($master);
		$Email->from(array($wzsetting['Wzsetting']['email_username'] => $wzsetting['Wzsetting']['email_from']))
			->template('Webzash.' . $view, 'Webzash.email')
			->viewVars($viewVars)
			->emailFormat('both')
			->to($to)
			->subject($subject)
			->send();

		return;
	}

/**
 * Called after the Controller::beforeRender(), after the view class is loaded, and before the
 * Controller::render()
 *
 * https://github.com/dereuromark/cakephp-tools
 * The MIT License
 *
 * @param object $Controller Controller with components to beforeRender
 * @return void
 */
	public function beforeRender(Controller $Controller) {
		if ($messages = $this->Session->read('Message')) {
			foreach ($messages as $message) {
				$this->flashMessage($message['message'], $message['element']);
			}
			$this->Session->delete('Message');
		}
	}
}
