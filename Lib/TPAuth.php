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

App::uses('JoomlaAuth', 'Webzash.Lib');
App::uses('JoomlaAutoAuth', 'Webzash.Lib');

class TPAuth {

        public function __construct($authlib) {
                $className = $authlib . 'Auth';
                $this->auth = new $className;
        }

        public function checkPassword($username, $password) {
                return $this->auth->checkPassword($username, $password);
        }

        public function getUserDetails() {
                return $this->auth->getUserDetails();
        }

        public function siteURL() {
                return $this->auth->site_url;
        }

        public function loginURL() {
                return $this->auth->login_url;
        }

        public function logoutURL() {
                return $this->auth->logout_url;
        }

        public function default_email() {
                return $this->auth->default_email;
        }

        public function admin_username() {
                return $this->auth->admin_username;
        }
}
