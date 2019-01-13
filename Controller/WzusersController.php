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

App::uses('WebzashAppController', 'Webzash.Controller');
App::uses('ConnectionManager', 'Model');
App::uses('TPAuth', 'Webzash.Lib');

/**
 * Webzash Plugin Wzusers Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class WzusersController extends WebzashAppController {

	public $uses = array('Webzash.Wzuser', 'Webzash.Wzaccount',
		'Webzash.Wzuseraccount', 'Webzash.Wzsetting');

	var $layout = 'admin';

/**
 * index method
 *
 * @return void
 */
	public function index() {

		$this->set('title_for_layout', __d('webzash', 'Users'));

		$this->Wzuser->useDbConfig = 'wz';

		$this->set('actionlinks', array(
			array('controller' => 'wzusers', 'action' => 'add', 'title' => __d('webzash', 'Add User')),
			array('controller' => 'admin', 'action' => 'index', 'title' => __d('webzash', 'Back')),
		));

		$this->CustomPaginator->settings = array(
			'Wzuser' => array(
				'limit' => $this->Session->read('Wzsetting.row_count'),
				'order' => array('Wzuser.username' => 'asc'),
			)
		);

		$this->set('wzusers', $this->CustomPaginator->paginate('Wzuser'));

		return;
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {

		$this->set('title_for_layout', __d('webzash', 'Add User'));

		$this->Wzuser->useDbConfig = 'wz';
		$this->Wzaccount->useDbConfig = 'wz';
		$this->Wzuseraccount->useDbConfig = 'wz';
		$this->Wzsetting->useDbConfig = 'wz';

		$wzsetting = $this->Wzsetting->findById(1);
		if (!$wzsetting) {
			$this->Session->setFlash(__d('webzash', 'Please update your settings below before adding any users.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzsettings', 'action' => 'edit'));
		}

		/* Create list of wzaccounts */
		$wzaccounts = array(0 => '(ALL ACCOUNTS)') + $this->Wzaccount->find('list', array(
			'fields' => array('Wzaccount.id', 'Wzaccount.label'),
			'order' => array('Wzaccount.label')
		));
		$this->set('wzaccounts', $wzaccounts);

		/* On POST */
		if ($this->request->is('post')) {
			$this->Wzuser->create();
			if (!empty($this->request->data)) {
				/* Unset ID */
				unset($this->request->data['Wzuser']['id']);

				/* Check length of password */
				if (strlen($this->request->data['Wzuser']['password']) < 4) {
					$this->Session->setFlash(__d('webzash', 'Password should be atleast 4 characters.'), 'danger');
					return;
				}

				$temp_password = $this->request->data['Wzuser']['password'];
				$this->request->data['Wzuser']['password'] = Security::hash($this->request->data['Wzuser']['password'], 'sha1', true);

				$verification_key = Security::hash(uniqid() . uniqid());
				$this->request->data['Wzuser']['verification_key'] = $verification_key;

				/* Check if user is allowed access to all accounts */
				if (!empty($this->request->data['Wzuser']['wzaccount_ids'])) {
					if (in_array(0, $this->request->data['Wzuser']['wzaccount_ids'])) {
						$this->request->data['Wzuser']['all_accounts'] = 1;
					} else {
						$this->request->data['Wzuser']['all_accounts'] = 0;
					}
				} else {
					$this->request->data['Wzuser']['wzaccount_ids'] = array();
					$this->request->data['Wzuser']['all_accounts'] = 0;
				}

				$this->request->data['Wzuser']['retry_count'] = 0;
				$this->request->data['Wzuser']['timezone'] = 'UTC';

				/* Save user */
				$ds = $this->Wzuser->getDataSource();
				$ds->begin();

				if ($this->Wzuser->save($this->request->data)) {

					/* Save user - accounts association */
					if ($this->request->data['Wzuser']['all_accounts'] != 1) {
						if (!empty($this->request->data['Wzuser']['wzaccount_ids'])) {
							$data = array();
							foreach ($this->request->data['Wzuser']['wzaccount_ids'] as $row => $wzaccount_id) {
								if (!$this->Wzaccount->exists($wzaccount_id)) {
									continue;
								}
								$data[] = array('wzuser_id' => $this->Wzuser->id, 'wzaccount_id' => $wzaccount_id, 'role' => '');
							}
							if (!$this->Wzuseraccount->saveMany($data)) {
								$ds->rollback();
								$this->Session->setFlash(__d('webzash', 'Failed to create user account. Please, try again.'), 'danger');
								return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index'));
							}
						}
					}

					/* Sending email */
					$viewVars = array(
						'username' => $this->request->data['Wzuser']['username'],
						'fullname' => $this->request->data['Wzuser']['fullname'],
						'verification_key' => $verification_key,
						'email_verification' => $wzsetting['Wzsetting']['email_verification'],
						'admin_verification' => $wzsetting['Wzsetting']['admin_verification'],
					);
					$this->Generic->sendEmail(
						$this->request->data['Wzuser']['email'],
						'Your registraion details',
						'user_add', $viewVars, true, true
					);

					$ds->commit();
					$this->Session->setFlash(__d('webzash', 'User account created.'), 'success');
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index'));
				} else {
					$this->request->data['Wzuser']['password'] = $temp_password;
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'Failed to create user account. Please, try again.'), 'danger');
					return;
				}
			} else {
				$this->Session->setFlash(__d('webzash', 'No data. Please, try again.'), 'danger');
				return;
			}
		}
	}


/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {

		$this->set('title_for_layout', __d('webzash', 'Edit User'));

		$this->Wzuser->useDbConfig = 'wz';
		$this->Wzaccount->useDbConfig = 'wz';
		$this->Wzuseraccount->useDbConfig = 'wz';
		$this->Wzsetting->useDbConfig = 'wz';

		$wzsetting = $this->Wzsetting->findById(1);
		if (!$wzsetting) {
			$this->Session->setFlash(__d('webzash', 'Please update your settings below before editing any user.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzsettings', 'action' => 'edit'));
		}

		/* Check for valid user */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'User account not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index'));
		}
		$wzuser = $this->Wzuser->findById($id);
		if (!$wzuser) {
			$this->Session->setFlash(__d('webzash', 'User account not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index'));
		}

		/* Create list of wzaccounts */
		$wzaccounts = array(0 => '(ALL ACCOUNTS)') + $this->Wzaccount->find('list', array(
			'fields' => array('Wzaccount.id', 'Wzaccount.label'),
			'order' => array('Wzaccount.label')
		));
		$this->set('wzaccounts', $wzaccounts);

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {
			/* Set user id */
			unset($this->request->data['Wzuser']['id']);

			$this->Wzuser->id = $id;

			/* Check if user is allowed access to all accounts */
			if (!empty($this->request->data['Wzuser']['wzaccount_ids'])) {
				if (in_array(0, $this->request->data['Wzuser']['wzaccount_ids'])) {
					$this->request->data['Wzuser']['all_accounts'] = 1;
				} else {
					$this->request->data['Wzuser']['all_accounts'] = 0;
				}
			} else {
				$this->request->data['Wzuser']['wzaccount_ids'] = array();
				$this->request->data['Wzuser']['all_accounts'] = 0;
			}

			/* Save user */
			$ds = $this->Wzuser->getDataSource();
			$ds->begin();

			$this->request->data['Wzuser']['verification_key'] = Security::hash(uniqid() . uniqid());
			$this->request->data['Wzuser']['retry_count'] = 0;

			if ($this->Wzuser->save($this->request->data, true, array('username', 'fullname', 'email', 'role', 'status', 'email_verified', 'admin_verified', 'verification_key', 'retry_count', 'all_accounts'))) {

				/* Delete existing user - account associations */
				if (!$this->Wzuseraccount->deleteAll(array('Wzuseraccount.wzuser_id' => $id))) {
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'Failed to update user account. Please, try again.'), 'danger');
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index'));
				}

				/* Save user - accounts association */
				if ($this->request->data['Wzuser']['all_accounts'] != 1) {
					if (!empty($this->request->data['Wzuser']['wzaccount_ids'])) {
						$data = array();
						foreach ($this->request->data['Wzuser']['wzaccount_ids'] as $row => $wzaccount_id) {
							if (!$this->Wzaccount->exists($wzaccount_id)) {
								continue;
							}
							$data[] = array('wzuser_id' => $this->Wzuser->id, 'wzaccount_id' => $wzaccount_id, 'role' => '');
						}
						if (!$this->Wzuseraccount->saveMany($data)) {
							$ds->rollback();
							$this->Session->setFlash(__d('webzash', 'Failed to update user account. Please, try again.'), 'danger');
							return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index'));
						}
					}
				}

				$ds->commit();
				$this->Session->setFlash(__d('webzash', 'User account updated.'), 'success');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index'));
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'Failed to update user account. Please, try again.'), 'danger');
				return;
			}
		} else {
			$this->request->data = $wzuser;

			/* Load existing user - account association */
			if ($wzuser['Wzuser']['all_accounts'] == 1) {
				$this->request->data['Wzuser']['wzaccount_ids'] = array('0');
			} else {
				$rawuseraccounts = $this->Wzuseraccount->find('all',
					array('conditions' => array('Wzuseraccount.wzuser_id' => $id))
				);
				$useraccounts = array();
				foreach ($rawuseraccounts as $row => $useraccount) {
					$useraccounts[] = $useraccount['Wzuseraccount']['wzaccount_id'];
				}
				$this->request->data['Wzuser']['wzaccount_ids'] = $useraccounts;
			}
			return;
		}
	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		/* GET access not allowed */
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}

		$this->Wzuser->useDbConfig = 'wz';
		$this->Wzuseraccount->useDbConfig = 'wz';

		/* Check if valid id */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'User account not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index'));
		}

		/* Check if user exists */
		if (!$this->Wzuser->exists($id)) {
			$this->Session->setFlash(__d('webzash', 'User account not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index'));
		}

		/* Cannot delete your own account */
		if ($id == $this->Auth->user('id')) {
			$this->Session->setFlash(__d('webzash', 'Cannot delete own account.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index'));
		}

		/* Delete user */
		$ds = $this->Wzuser->getDataSource();
		$ds->begin();

		if (!$this->Wzuser->delete($id)) {
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'Failed to delete user account. Please, try again.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index'));
		}

		/* Delete user - account association */
		if (!$this->Wzuseraccount->deleteAll(array('Wzuseraccount.wzuser_id' => $id))) {
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'Failed to delete user account. Please, try again.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index'));
		}

		/* Success */
		$ds->commit();
		$this->Session->setFlash(__d('webzash', 'User account deleted.'), 'success');

		return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index'));
	}

/**
 * login method
 */
	public function login() {

		if (Configure::read('Webzash.ThirdPartyLogin')) {
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'tplogin'));
		}

		$this->set('title_for_layout', __d('webzash', 'User Login'));

		$this->layout = 'user';

		$this->Wzuser->useDbConfig = 'wz';
		$this->Wzsetting->useDbConfig = 'wz';

		$view = new View($this);
		$this->Html = $view->loadHelper('Html');

		$wzsetting = $this->Wzsetting->findById(1);

		/* DANGEROUS ! Reset password if the 'Webzash.ResetAdminPassword' config parameter is set to 'YES PLEASE' */
		if (Configure::read('Webzash.ResetAdminPassword') == 'YES PLEASE') {
			$reset_admin_data = array(
				'Wzuser' => array(
					'id' => 1,
					'password' => "",
					'email' => "",
					'status' => 1,
					'retry_count' => 0,
				),
			);
			if ($this->Wzuser->save($reset_admin_data, FALSE)) {
				die("Successfully reset password for admin user. Please re-comment the parameter in the config file to proceed.");
			} else {
				foreach ($this->Wzuser->validationErrors as $field => $msg) {
					echo $msg[0] . "<br />";
				}
				die("Failed to reset password for admin user");
			}
		}

		/* Check if this is the first time user is using this application */
		$default_password = false;
		$first_login = false;
		$admin_check = $this->Wzuser->find('first', array('conditions' => array(
			'id' => 1,
			'username' => 'admin',
			'password' => '',
		)));
		if ($admin_check) {
			/* Password still not updated for admin user */
			$default_password = true;

			if ($admin_check['Wzuser']['email'] == '') {
				/* This is the first login by user */
				$first_login = true;
			}
		}

		$this->set('first_login', $first_login);
		$this->set('default_password', $default_password);

		if ($this->request->is('post')) {
			/* Check status of user account */
			if ($default_password &&
				$this->request->data['Wzuser']['username'] == 'admin' &&
				$this->request->data['Wzuser']['password'] == 'admin') {
					$password = '';
			} else {
				$password = Security::hash($this->request->data['Wzuser']['password'], 'sha1', true);
			}

			$user = $this->Wzuser->find('first', array('conditions' => array(
				'username' => $this->request->data['Wzuser']['username'],
				'password' => $password
			)));

			if (!$user) {
				/* On failed login attempt, increase the retry count */
				$wzuser = $this->Wzuser->find('first', array(
					'conditions' => array(
						'username' => $this->request->data['Wzuser']['username'],
					),
				));
				if ($wzuser) {
					$this->Wzuser->read(null, $wzuser['Wzuser']['id']);
					/* Use 4 since retry_count starts from 0 */
					if ($wzuser['Wzuser']['retry_count'] >= 4) {
						/* If max retry count reached, disable account */
						$this->Wzuser->saveField('status', 0);
					} else {
						/* Update retry count */
						$this->Wzuser->saveField('retry_count',
							$wzuser['Wzuser']['retry_count'] + 1
						);
					}

					/* Use 4 since retry_count starts from 0 */
					if ($wzuser['Wzuser']['retry_count'] >= 4) {
						$this->Session->setFlash(__d('webzash', 'Login failed. You have exceed 5 login attempts hence your account has been disabled. Please contact your administrator to re-enable the account.'), 'danger');
					} else {
						$this->Session->setFlash(__d('webzash', 'Login failed. You still have %d attempts left out of 5 before the account is disabled.', 4 - $wzuser['Wzuser']['retry_count']), 'danger');
					}
				} else {
					$this->Session->setFlash(__d('webzash', 'Login failed. Please, try again.'), 'danger');
				}
				return;
			}

			if ($user['Wzuser']['status'] == 0) {
				$this->Session->setFlash(__d('webzash', 'User account is diabled. Please contact your administrator.'), 'danger');
				return;
			}
			if (!($wzsetting) || $wzsetting['Wzsetting']['admin_verification'] != 0) {
				 if ($user['Wzuser']['admin_verified'] != 1) {
					$this->Session->setFlash(__d('webzash', 'Administrator approval is pending. Please contact your administrator.'), 'danger');
					return;
				 }
			}
			if (!($wzsetting) || $wzsetting['Wzsetting']['email_verification'] != 0) {
				 if ($user['Wzuser']['email_verified'] != 1) {
					 $resendURL = $this->Html->link(__d('webzash', 'here'), array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'resend'));
					$this->Session->setFlash(__d('webzash', 'Email verification is pending. Please verify your email. To resend verification email click ') . $resendURL . '.', 'danger');
					return;
				 }
			}

			/* Login */
			if ($default_password &&
				$this->request->data['Wzuser']['username'] == 'admin' &&
				$this->request->data['Wzuser']['password'] == 'admin') {
				$login_status = $this->Auth->login($user['Wzuser']);
			} else {
				$login_status = $this->Auth->login();
			}
			if ($login_status) {
				/* Reset retry count on successful login */
				$this->Wzuser->read(null, $this->Auth->user('id'));
				$this->Wzuser->saveField('retry_count', 0);

				if (empty($wzsetting['Wzsetting']['enable_logging'])) {
					$this->Session->write('Wzsetting.enable_logging', 0);
				} else {
					$this->Session->write('Wzsetting.enable_logging', 1);
				}
				if (empty($wzsetting['Wzsetting']['row_count'])) {
					$this->Session->write('Wzsetting.row_count', 10);
				} else {
					$this->Session->write('Wzsetting.row_count', $wzsetting['Wzsetting']['row_count']);
					/* Since CakePHP puts a limit of 100 to the max row count field, we manually reset it if greater than 100 */
					if ($wzsetting['Wzsetting']['row_count'] > 100) {
						$this->Session->write('Wzsetting.row_count', 100);
					}
				}
				if (empty($wzsetting['Wzsetting']['drcr_toby'])) {
					$this->Session->write('Wzsetting.drcr_toby', 'drcr');
				} else {
					$this->Session->write('Wzsetting.drcr_toby', $wzsetting['Wzsetting']['drcr_toby']);
				}

				$this->Session->delete('FirstLogin');

				/* Some basic checks for admin role */
				if ($this->Auth->user('role') == 'admin') {
					if ($this->request->data['Wzuser']['username'] == 'admin' &&
						$this->request->data['Wzuser']['password'] == 'admin' &&
						$this->Auth->user('id') == '1' &&
						$this->Auth->user('email') == '') {
						$this->Session->write('FirstLogin', 1);
						$this->Session->setFlash(__d('webzash', 'Please update your password, fullname and email address to continue.'), 'danger');
						return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'first'));
					}
					if ($this->request->data['Wzuser']['password'] == 'admin') {
						$this->Session->setFlash(__d('webzash', 'Warning ! You are using the default password. Please change your password.'), 'danger');
						return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'changepass'));
					}
				}

				if ($this->Auth->user('role') == 'admin') {
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'admin', 'action' => 'index'));
				} else {
					return $this->redirect($this->Auth->redirectUrl());
				}
			} else {
				$this->Session->setFlash(__d('webzash', 'Login failed. Please, try again.'), 'danger');
			}
		}
	}

/**
 * Third party login method
 */
	public function tplogin() {

		if (!Configure::read('Webzash.ThirdPartyLogin')) {
			$this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'login'));
		}

		$this->set('title_for_layout', __d('webzash', 'User Login'));

		$this->layout = 'user';

		$view = new View($this);
		$this->Html = $view->loadHelper('Html');

		$this->Wzuser->useDbConfig = 'wz';
		$this->Wzsetting->useDbConfig = 'wz';

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {

			$tpauth = new TPAuth(Configure::read('Webzash.ThirdPartyLoginSystem'));
			$login_status = $tpauth->checkPassword(
				$this->request->data['Wzuser']['username'],
				$this->request->data['Wzuser']['password']);

			if ($login_status) {

				$wzuser = $this->Wzuser->find('first', array('conditions' => array(
					'username' => $this->request->data['Wzuser']['username'],
				)));

				$user_data = array();
				if ($wzuser) {
					$user_data = array(
						'id' => $wzuser['Wzuser']['id'],
						'username' => $wzuser['Wzuser']['username'],
						'role' => $wzuser['Wzuser']['role'],
					);
				} else {
					/* Disable validations for fullname and email */
					$this->Wzuser->validate['fullname'] = array();
					$this->Wzuser->validate['email'] = array();

					$user_details = $tpauth->getUserDetails();

					$new_user['Wzuser'] = array();

					/* If user not found create a account */
					if ($user_details['status'] == TRUE) {
						/* Get user details from tpauth library */
						$new_user['Wzuser'] = array(
							'username' => $user_details['username'],
							'password' => '*',
							'fullname' => $user_details['fullname'],
							'email' => $user_details['email'],
							'timezone' => 'UTC',
							'role' => 'guest',
							'status' => 0,
							'verification_key' => '',
							'email_verified' => 0,
							'admin_verified' => 0,
							'retry_count' => 0,
							'all_accounts' => 0,
						);
					} else {
						/* Get user details from webzash */
						$new_user['Wzuser'] = array(
							'username' => $this->request->data['Wzuser']['username'],
							'password' => '*',
							'fullname' => '',
							'email' => '',
							'timezone' => 'UTC',
							'role' => 'guest',
							'status' => 0,
							'verification_key' => '',
							'email_verified' => 0,
							'admin_verified' => 0,
							'retry_count' => 0,
							'all_accounts' => 0,
						);
					}

					/* Create user */
					$this->Wzuser->create();
					if (!$this->Wzuser->save($new_user)) {
						$this->Session->setFlash(__d('webzash', 'Failed to create user.'), 'danger');
						return;
					}

					$user_data = array(
						'id' => $this->Wzuser->id,
						'username' => $this->Wzuser->username,
						'role' => $this->Wzuser->role,
					);
				}

				$this->Auth->login($user_data);

				$wzsetting = $this->Wzsetting->findById(1);

				if (empty($wzsetting['Wzsetting']['enable_logging'])) {
					$this->Session->write('Wzsetting.enable_logging', 0);
				} else {
					$this->Session->write('Wzsetting.enable_logging', 1);
				}
				if (empty($wzsetting['Wzsetting']['row_count'])) {
					$this->Session->write('Wzsetting.row_count', 10);
				} else {
					$this->Session->write('Wzsetting.row_count', $wzsetting['Wzsetting']['row_count']);
				}
				if (empty($wzsetting['Wzsetting']['drcr_toby'])) {
					$this->Session->write('Wzsetting.drcr_toby', 'drcr');
				} else {
					$this->Session->write('Wzsetting.drcr_toby', $wzsetting['Wzsetting']['drcr_toby']);
				}

				if ($this->Auth->user('role') == 'admin') {
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'admin', 'action' => 'index'));
				} else {
					return $this->redirect($this->Auth->redirectUrl());
				}

			} else {
				$this->Session->setFlash(__d('webzash', 'Login failed. Please, try again.'), 'danger');
			}
		}
	}

/**
 * logout method
 */
	public function logout() {

		if (Configure::read('Webzash.ThirdPartyLogin')) {
			$this->Auth->logout();
			$tpauth = new TPAuth(Configure::read('Webzash.ThirdPartyLoginSystem'));
			return $this->redirect($tpauth->logoutURL());
		}

		$this->Session->destroy();
		return $this->redirect($this->Auth->logout());
	}

/**
 * verifiy email method
 */
	public function verify() {

		$this->set('title_for_layout', __d('webzash', 'User Email Verification'));

		$this->layout = 'user';

		$this->Wzuser->useDbConfig = 'wz';
		$this->Wzsetting->useDbConfig = 'wz';

		$wzsetting = $this->Wzsetting->findById(1);

		$this->Auth->logout();

		$this->set('success', false);

		/* Check whether key is present in GET requets */
		if (empty($this->params['url']['u'])) {
			$this->set('success', false);
			$this->Session->setFlash(__d('webzash', 'Email verification failed. Please, try again.'), 'danger');
			return;
		}
		if (empty($this->params['url']['k'])) {
			$this->set('success', false);
			$this->Session->setFlash(__d('webzash', 'Email verification failed. Please, try again.'), 'danger');
			return;
		}

		/* Get user count */
		$wzuser = $this->Wzuser->find('first', array('conditions' => array(
			'username' => $this->params['url']['u'],
			'verification_key' => $this->params['url']['k']
		)));

		if (empty($wzuser)) {
			$this->set('success', false);
			$this->Session->setFlash(__d('webzash', 'Email verification failed. Please, try again.'), 'danger');
			return;
		}

		/* Set email as verified */
		$ds = $this->Wzuser->getDataSource();
		$ds->begin();

		$this->Wzuser->id = $wzuser['Wzuser']['id'];

		if ($this->Wzuser->saveField('email_verified', '1')) {
			$this->set('success', true);
			$ds->commit();

			/* Sending email */
			$viewVars = array(
				'fullname' => $wzuser['Wzuser']['fullname'],
			);
			$this->Generic->sendEmail(
				$wzuser['Wzuser']['email'],
				'Account verified',
				'user_verify', $viewVars, true, true
			);

			$this->Session->setFlash(__d('webzash', 'User account verified.'), 'success');
		} else {
			$this->set('success', false);
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'Email verification failed. Please, try again.'), 'danger');
		}
		return;
	}

/**
 * resend verification email method
 */
	public function resend() {

		$this->set('title_for_layout', __d('webzash', 'Resend Verification Email'));

		$this->layout = 'user';

		$this->Wzuser->useDbConfig = 'wz';

		$this->Auth->logout();

		if ($this->request->is('post')) {
			$wzuser = $this->Wzuser->find('first', array('conditions' => array(
				'username' => $this->request->data['Wzuser']['userinfo']
			)));
			if (empty($wzuser)) {
				$wzuser = $this->Wzuser->find('first', array('conditions' => array(
					'email' => $this->request->data['Wzuser']['userinfo']
				)));
			}
			if (empty($wzuser)) {
				$this->Session->setFlash(__d('webzash', 'Invalid username or email. Please, try again.'), 'danger');
				return;
			} else {
				/* Sending email */
				$viewVars = array(
					'username' => $wzuser['Wzuser']['username'],
					'fullname' => $wzuser['Wzuser']['fullname'],
					'verification_key' => $wzuser['Wzuser']['verification_key'],
				);
				$email_status = $this->Generic->sendEmail(
					$wzuser['Wzuser']['email'],
					'Account verification required',
					'user_resend', $viewVars, true, true
				);

				if ($email_status) {
					$this->Session->setFlash(__d('webzash', 'Verification email sent. Please check your email.'), 'success');
				}
			}
		}
	}

/**
 * user profile method
 */
	public function profile() {

		$this->set('title_for_layout', __d('webzash', 'Update Profile'));

		if ($this->Auth->user('role') == 'admin') {
			$this->layout = 'admin';
		} else {
			$this->layout = 'default';
		}

		$this->Wzuser->useDbConfig = 'wz';

		$wzuser = $this->Wzuser->findById($this->Auth->user('id'));
		if (!$wzuser) {
			$this->Session->setFlash(__d('webzash', 'User account not found.'), 'danger');
			$this->redirect($this->Auth->logout());
		}

		$prev_email = $wzuser['Wzuser']['email'];

		if ($this->request->is('post') || $this->request->is('put')) {

			$this->Wzuser->id = $this->Auth->user('id');

			/* Update profile user */
			$ds = $this->Wzuser->getDataSource();
			$ds->begin();

			if ($this->Wzuser->save($this->request->data, true, array('fullname', 'email'))) {
				$ds->commit();

				/* If email changed, reset email verification */
				if ($this->request->data['Wzuser']['email'] != $prev_email) {
					$this->Wzuser->saveField('email_verified', '0');
					$this->Wzuser->saveField('verification_key', Security::hash(uniqid() . uniqid()));
					$this->Session->setFlash(__d('webzash', 'Profile updated. You need to verify your new email address, please check your email for verification details.'), 'success');
				} else {
					$this->Session->setFlash(__d('webzash', 'Profile updated.'), 'success');
				}

				if ($this->Auth->user('role') == 'admin') {
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'admin', 'action' => 'index'));
				} else {
					return $this->redirect($this->Auth->redirectUrl());
				}
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'Failed to update profile. Please, try again.'), 'danger');
				return;
			}
		} else {
			$this->request->data = $wzuser;
			return;
		}
	}

/**
 * change password method
 */
	public function changepass() {

		$this->set('title_for_layout', __d('webzash', 'Change Password'));

		if ($this->Auth->user('role') == 'admin') {
			$this->layout = 'admin';
		} else {
			$this->layout = 'default';
		}

		$this->Wzuser->useDbConfig = 'wz';

		$wzuser = $this->Wzuser->findById($this->Auth->user('id'));
		if (!$wzuser) {
			$this->Session->setFlash(__d('webzash', 'User account not found.'), 'danger');
			$this->redirect($this->Auth->logout());
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			/* Check length of password */
			if (strlen($this->request->data['Wzuser']['new_password']) < 4) {
				$this->Session->setFlash(__d('webzash', 'Password should be atleast 4 characters.'), 'danger');
				return;
			}

			/* Check if existing passwords match */
			if ($wzuser['Wzuser']['password'] != Security::hash($this->request->data['Wzuser']['existing_password'], 'sha1', true)) {
				$this->Session->setFlash(__d('webzash', 'Your existing password does not match. Please, try again.'), 'danger');
				return;
			}

			$this->Wzuser->id = $this->Auth->user('id');

			/* Update user password */
			$ds = $this->Wzuser->getDataSource();
			$ds->begin();

			if ($this->Wzuser->saveField('password', Security::hash($this->request->data['Wzuser']['new_password'], 'sha1', true))) {
				$ds->commit();

				$this->Session->setFlash(__d('webzash', 'Password updated.'), 'success');

				/* Sending email */
				$viewVars = array(
					'username' => $wzuser['Wzuser']['username'],
					'fullname' => $wzuser['Wzuser']['fullname'],
				);
				$this->Generic->sendEmail(
					$wzuser['Wzuser']['email'],
					'Password changed',
					'user_changepass', $viewVars, true, true
				);

				if ($this->Auth->user('role') == 'admin') {
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'admin', 'action' => 'index'));
				} else {
					return $this->redirect($this->Auth->redirectUrl());
				}
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'Failed to update password. Please, try again.'), 'danger');
				return;
			}
		} else {
			return;
		}
	}

/**
 * reset user password by admin method
 */
	public function resetpass() {

		$this->set('title_for_layout', __d('webzash', 'Reset Password'));

		$this->Wzuser->useDbConfig = 'wz';

		if (empty($this->passedArgs['userid'])) {
			$this->Session->setFlash(__d('webzash', 'User account not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index'));
		}

		$userid = $this->passedArgs['userid'];

		$wzuser = $this->Wzuser->findById($userid);
		if (!$wzuser) {
			$this->Session->setFlash(__d('webzash', 'User account not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index'));
		}

		$this->set('username', $wzuser['Wzuser']['username']);

		if ($this->request->is('post') || $this->request->is('put')) {
			/* Check length of password */
			if (strlen($this->request->data['Wzuser']['new_password']) < 4) {
				$this->Session->setFlash(__d('webzash', 'Password should be atleast 4 characters.'), 'danger');
				return;
			}

			$this->Wzuser->id = $wzuser['Wzuser']['id'];

			/* Update user password */
			$ds = $this->Wzuser->getDataSource();
			$ds->begin();

			if ($this->Wzuser->saveField('password', Security::hash($this->request->data['Wzuser']['new_password'], 'sha1', true))) {
				$ds->commit();

				/* Sending email */
				$viewVars = array(
					'username' => $wzuser['Wzuser']['username'],
					'fullname' => $wzuser['Wzuser']['fullname'],
					'password' => $this->request->data['Wzuser']['new_password'],
				);
				$email_status = $this->Generic->sendEmail(
					$wzuser['Wzuser']['email'],
					'Password changed by admin',
					'user_resetpass', $viewVars, true, true
				);

				if ($email_status) {
					$this->Session->setFlash(__d('webzash', 'User password updated. Email sent to user with the new password.'), 'success');
				} else {
					$this->Session->setFlash(__d('webzash', 'User password updated.'), 'success');
				}
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index'));
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'Failed to update user password. Please, try again.'), 'danger');
				return;
			}
		} else {
			return;
		}
	}

/**
 * forgot password method
 */
	public function forgot() {

		$this->set('title_for_layout', __d('webzash', 'Forgot Password'));

		$this->layout = 'user';

		$this->Auth->logout();

		$this->Wzuser->useDbConfig = 'wz';

		if ($this->request->is('post') || $this->request->is('put')) {

			$wzuser = $this->Wzuser->find('first', array('conditions' => array(
				'username' => $this->request->data['Wzuser']['userinfo']
			)));
			if (empty($wzuser)) {
				$wzuser = $this->Wzuser->find('first', array('conditions' => array(
					'email' => $this->request->data['Wzuser']['userinfo']
				)));
			}
			if (empty($wzuser)) {
				$this->Session->setFlash(__d('webzash', 'Invalid username or email. Please, try again.'), 'danger');
				return;
			}

			$random_password = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);

			$this->Wzuser->id = $wzuser['Wzuser']['id'];

			/* Update user password */
			$ds = $this->Wzuser->getDataSource();
			$ds->begin();

			if ($this->Wzuser->saveField('password', Security::hash($random_password, 'sha1', true))) {
				$ds->commit();

				/* Sending email */
				$viewVars = array(
					'username' => $wzuser['Wzuser']['username'],
					'fullname' => $wzuser['Wzuser']['fullname'],
					'password' => $random_password,
				);
				$email_status = $this->Generic->sendEmail(
					$wzuser['Wzuser']['email'],
					'Your login details',
					'user_forgot', $viewVars, true, true
				);

				if ($email_status) {
					$this->Session->setFlash(__d('webzash', 'Password reset. Please check your email for more details on how to reset password.'), 'success');
				}
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'login'));
			}
		} else {
			return;
		}
	}

/**
 * register user method
 */
	public function register() {

		$this->set('title_for_layout', __d('webzash', 'User Registration'));

		$this->layout = 'user';

		$this->Wzuser->useDbConfig = 'wz';
		$this->Wzsetting->useDbConfig = 'wz';

		$wzsetting = $this->Wzsetting->findById(1);

		if ($wzsetting['Wzsetting']['user_registration'] != 1) {
			$this->set('registration', false);
			return;
		}

		$this->set('registration', true);

		/* On POST */
		if ($this->request->is('post')) {
			$this->Wzuser->create();
			if (!empty($this->request->data)) {
				/* Unset ID */
				unset($this->request->data['Wzuser']['id']);
				unset($this->request->data['Wzuser']['timezone']);
				unset($this->request->data['Wzuser']['role']);
				unset($this->request->data['Wzuser']['status']);
				unset($this->request->data['Wzuser']['verification_key']);
				unset($this->request->data['Wzuser']['email_verified']);
				unset($this->request->data['Wzuser']['admin_verified']);

				/* Check length of password */
				if (strlen($this->request->data['Wzuser']['password']) < 4) {
					$this->Session->setFlash(__d('webzash', 'Password should be atleast 4 characters.'), 'danger');
					return;
				}

				$verification_key = Security::hash(uniqid() . uniqid());

				$user = array('Wzuser' => array(
					'username' => $this->request->data['Wzuser']['username'],
					'password' => Security::hash($this->request->data['Wzuser']['password'], 'sha1', true),
					'fullname' => $this->request->data['Wzuser']['fullname'],
					'email' => $this->request->data['Wzuser']['email'],
					'timezone' => 'UTC',
					'role' => 'guest',
					'status' => '1',
					'verification_key' => $verification_key,
					'email_verified' => '0',
					'admin_verified' => '0',
					'retry_count' => '0',
					'all_accounts' => '0',
				));

				/* Save user */
				$ds = $this->Wzuser->getDataSource();
				$ds->begin();

				if ($this->Wzuser->save($user)) {
					$ds->commit();
					$this->Session->setFlash(__d('webzash', 'User account created.'), 'success');

					/* Sending email */
					$viewVars = array(
						'username' => $this->request->data['Wzuser']['username'],
						'fullname' => $this->request->data['Wzuser']['fullname'],
						'verification_key' => $verification_key,
						'email_verification' => $wzsetting['Wzsetting']['email_verification'],
						'admin_verification' => $wzsetting['Wzsetting']['admin_verification'],
					);
					$this->Generic->sendEmail(
						$this->request->data['Wzuser']['email'],
						'Your registraion details',
						'user_register', $viewVars, true, true
					);

					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index'));
				} else {
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'Failed to create user account. Please, try again.'), 'danger');
					return;
				}
			} else {
				$this->Session->setFlash(__d('webzash', 'No data. Please, try again.'), 'danger');
				return;
			}
		}
	}

/**
 * first time login for admin user
 */
	public function first() {

		$this->set('title_for_layout', __d('webzash', 'First time login'));

		$this->layout = 'admin';

		/* Validate access to this method */
		if ($this->Auth->user('role') != 'admin') {
			$this->Session->setFlash(__d('webzash', 'Access denied.'), 'danger');
			return $this->redirect($this->Auth->logout());
		}
		if ($this->Session->read('FirstLogin') != 1) {
			$this->Session->setFlash(__d('webzash', 'Access denied.'), 'danger');
			return $this->redirect($this->Auth->logout());
		}
		if ($this->Auth->user('id') != '1') {
			$this->Session->setFlash(__d('webzash', 'Access denied.'), 'danger');
			return $this->redirect($this->Auth->logout());
		}
		if ($this->Auth->user('username') != 'admin') {
			$this->Session->setFlash(__d('webzash', 'Access denied.'), 'danger');
			return $this->redirect($this->Auth->logout());
		}

		/* On POST */
		if ($this->request->is('post') || $this->request->is('put')) {

			$this->Wzuser->id = $this->Auth->user('id');

			$user = array('Wzuser' => array(
				'id' => $this->Auth->user('id'),
				'password' => Security::hash($this->request->data['Wzuser']['password'], 'sha1', true),
				'fullname' => $this->request->data['Wzuser']['fullname'],
				'email' => $this->request->data['Wzuser']['email'],
			));

			/* Save user */
			$ds = $this->Wzuser->getDataSource();
			$ds->begin();

			if ($this->Wzuser->save($user, true, array('password', 'fullname', 'email'))) {
				$ds->commit();
				$this->Session->setFlash(__d('webzash', 'Profile updated.'), 'success');
				$this->Session->delete('FirstLogin');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'admin', 'action' => 'index'));
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'Failed to update profile. Please, try again.'), 'danger');
				return;
			}
		}
	}

/**
 * change active account
 */
	public function account() {

		$this->set('title_for_layout', __d('webzash', 'Select account to activate'));

		$this->layout = 'default';

		$this->Wzuser->useDbConfig = 'wz';
		$this->Wzaccount->useDbConfig = 'wz';
		$this->Wzuseraccount->useDbConfig = 'wz';

		$wzuser = $this->Wzuser->findById($this->Auth->user('id'));
		if (!$wzuser) {
			$this->Session->setFlash(__d('webzash', 'User not found.'), 'danger');
			return;
		}

		/* Currently active account */
		$curActiveAccount = $this->Wzaccount->findById($this->Session->read('ActiveAccount.id'));
		if ($curActiveAccount) {
			$this->set('curActiveAccount', $curActiveAccount['Wzaccount']['label']);
		} else {
			$this->set('curActiveAccount', '(NONE)');
		}

		$wzaccounts_count = $this->Wzaccount->find('count');
		$this->set('wzaccounts_count', $wzaccounts_count);

		/* Create list of wzaccounts */
		if ($wzuser['Wzuser']['all_accounts'] == 1) {
			$wzaccounts = $this->Wzaccount->find('list', array(
				'fields' => array('Wzaccount.id', 'Wzaccount.label'),
				'order' => array('Wzaccount.label')
			));
			$wzaccounts = array(0 => '(NONE)') + $wzaccounts;
		} else {
			$wzaccounts = array();
			$rawwzaccounts = $this->Wzuseraccount->find('all', array(
				'conditions' => array('Wzuseraccount.wzuser_id' => $this->Auth->user('id')),
			));
			foreach ($rawwzaccounts as $row => $wzaccount) {
				$account = $this->Wzaccount->findById($wzaccount['Wzuseraccount']['wzaccount_id']);
				if ($account) {
					$wzaccounts[$account['Wzaccount']['id']] = $account['Wzaccount']['label'];
				}
			}
			$wzaccounts = array(0 => '(NONE)') + $wzaccounts;
		}
		$this->set('wzaccounts', $wzaccounts);

		if ($this->Session->read('ActiveAccount.failed')) {
			$this->Session->setFlash(__d('webzash', 'Failed to connect to account database. Please check your connection settings.'), 'danger');
			$this->Session->delete('ActiveAccount.failed');
			return;
		}

		/* On POST */
		if ($this->request->is('post') || $this->request->is('put')) {

			/* Check if NONE selected */
			if ($this->request->data['Wzuser']['wzaccount_id'] == 0) {
				$this->Session->delete('ActiveAccount.id');
				$this->Session->delete('ActiveAccount.account_role');
				$this->Session->setFlash(__d('webzash', 'All accounts deactivated.'), 'success');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'account'));
			}

			/* Check if user is allowed to access the account */
			$activateAccount = FALSE;
			$account_role = '';
			if ($wzuser['Wzuser']['all_accounts'] == 1) {
				$activateAccount = TRUE;
				/* Read account role */
				$temp = $this->Wzuseraccount->find('first', array(
					'conditions' => array(
						'Wzuseraccount.wzaccount_id' => $this->request->data['Wzuser']['wzaccount_id'],
					),
				));
				if ($temp) {
					$account_role = $temp['Wzuseraccount']['role'];
				} else {
					$account_role = '';
				}
			} else {
				$temp = $this->Wzuseraccount->find('first', array(
					'conditions' => array(
						'Wzuseraccount.wzuser_id' => $this->Auth->user('id'),
						'Wzuseraccount.wzaccount_id' => $this->request->data['Wzuser']['wzaccount_id'],
					),
				));
				if ($temp) {
					$activateAccount = TRUE;
					$account_role = $temp['Wzuseraccount']['role'];
				} else {
					$account_role = '';
				}
			}
			if ($activateAccount) {
				$temp = $this->Wzaccount->findById($this->request->data['Wzuser']['wzaccount_id']);
				if (!$temp) {
					$this->Session->delete('ActiveAccount.id');
					$this->Session->delete('ActiveAccount.account_role');
					$this->Session->setFlash(__d('webzash', 'Account not found.'), 'danger');
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'account'));
				}

				/* Setup account role */
				$basic_roles = array('admin', 'manager', 'accountant', 'dataentry', 'guest');
				if (in_array($account_role, $basic_roles)) {
					$this->Session->write('ActiveAccount.account_role', $account_role);
				} else {
					/* Set the account role as per user profile */
					$this->Session->write('ActiveAccount.account_role', $this->Auth->user('role'));
				}

				$this->Session->write('ActiveAccount.id', $temp['Wzaccount']['id']);
				$this->Session->setFlash(__d('webzash', 'Account "%s" activated.', $temp['Wzaccount']['label']), 'success');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'dashboard', 'action' => 'index'));
			} else {
				$this->Session->delete('ActiveAccount.id');
				$this->Session->delete('ActiveAccount.account_role');
				$this->Session->setFlash(__d('webzash', 'Failed to activate account. Please, try again.'), 'danger');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'account'));
			}
		} else {
			if ($curActiveAccount) {
				$this->request->data['Wzuser']['wzaccount_id'] = $this->Session->read('ActiveAccount.id');
			} else {
				$this->request->data['Wzuser']['wzaccount_id'] = 0;
			}
		}
	}

	public function beforeFilter() {
		parent::beforeFilter();

		Configure::load('Webzash.config', 'default' , false);

		/* If third party login is active then disable methods by redirecting */
		if (Configure::read('Webzash.ThirdPartyLogin')) {
			if ($this->action == 'add' || $this->action == 'verify' ||
				$this->action == 'resend' || $this->action == 'profile' ||
				$this->action == 'changepass' || $this->action == 'resetpass' ||
				$this->action == 'forgot' || $this->action == 'register' ||
				$this->action == 'first') {
				$tpauth = new TPAuth(Configure::read('Webzash.ThirdPartyLoginSystem'));
				return $this->redirect($tpauth->siteURL());
			}
		}

		$this->Auth->allow('login', 'tplogin', 'logout', 'verify',
			'resend', 'forgot', 'register');
	}

	/* Authorization check */
	public function isAuthorized($user) {
		if ($this->action === 'index') {
			return $this->Permission->is_admin_allowed();
		}

		if ($this->action === 'add') {
			return $this->Permission->is_admin_allowed();
		}

		if ($this->action === 'edit') {
			return $this->Permission->is_admin_allowed();
		}

		if ($this->action === 'delete') {
			return $this->Permission->is_admin_allowed();
		}

		if ($this->action === 'profile') {
			return $this->Permission->is_registered_allowed();
		}

		if ($this->action === 'changepass') {
			return $this->Permission->is_registered_allowed();
		}

		if ($this->action === 'resetpass') {
			return $this->Permission->is_admin_allowed();
		}

		if ($this->action === 'first') {
			return $this->Permission->is_admin_allowed();
		}

		if ($this->action === 'account') {
			return $this->Permission->is_registered_allowed();
		}

		return parent::isAuthorized($user);
	}
}
