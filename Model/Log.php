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
 * Webzash Plugin Log Model
 *
 * @package Webzash
 * @subpackage Webzash.Model
 */
class Log extends WebzashAppModel {

	public $validationDomain = 'webzash';

        /* Validation rules for the Log table */
        public $validate = array(
                'level' => array(
                        'rule1' => array(
                                'rule' => 'notBlank',
                                'message' => 'Level cannot be empty',
                                'required' => true,
                                'allowEmpty' => false,
                        ),
                        'rule2' => array(
                                /* 1 = success, 2 = notice & 3 = failure */
                                'rule' => array('inList', array(1, 2, 3)),
                                'message' => 'Invalid value for level',
                                'required' => true,
                                'allowEmpty' => false,
                        ),
                ),
                'date' => array(
                        'rule1' => array(
                                'rule' => 'notBlank',
                                'message' => 'Date cannot be empty',
                                'required' => true,
                                'allowEmpty' => false,
                        ),
                ),
                'host_ip' => array(
                        'rule1' => array(
                                'rule' => array('ip', 'both'),
                                'message' => 'Invalid IP address',
                                'required' => true,
                                'allowEmpty' => false,
                        ),
                ),
                'user' => array(
                        'rule1' => array(
                                'rule' => array('maxLength', 100),
                                'message' => 'Username cannot be more than 100 characters',
                                'required' => true,
                                'allowEmpty' => false,
                        ),
                ),
                'url' => array(
                        'rule1' => array(
                                'rule' => 'url',
                                'message' => 'Invalid URL',
                                'required' => true,
                                'allowEmpty' => false,
                        ),
                ),
                'user_agent' => array(
                        'rule1' => array(
                                'rule' => array('maxLength', 255),
                                'message' => 'User agent cannot be more than 100 characters',
                                'required' => true,
                                'allowEmpty' => false,
                        ),
                ),
                'message' => array(
                        'rule1' => array(
                                'rule' => array('maxLength', 255),
                                'message' => 'Message cannot be more than 255 characters',
                                'required' => true,
                                'allowEmpty' => false,
                        ),
                ),
        );

        /* Add a Log entry */
        public function add($message, $level) {
                if (CakeSession::read('Wzsetting.enable_logging') != 1) {
                        return true;
                }
                $now = new DateTime();
                $logentry = array('Log' => array(
                        'level' => $level,
                        'date' => $now->format('Y-m-d H:i:s'),
                        'host_ip' => $_SERVER['REMOTE_ADDR'],
                        'user' => CakeSession::read('Auth.User.username'),
                        'url' => Router::url(null, TRUE),
                        'user_agent' => substr(env('HTTP_USER_AGENT'), 0 , 100),
                        'message' => substr($message, 0, 255),
                ));
                $this->create();
                if (!$this->save($logentry)) {
                        return false;
                }
                return true;
        }
}
