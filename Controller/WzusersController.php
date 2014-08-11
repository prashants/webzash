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

/**
 * Webzash Plugin Wzusers Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class WzusersController extends WebzashAppController {

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

		$this->Paginator->settings = array(
			'Wzuser' => array(
				'limit' => 10,
				'order' => array('Wzuser.username' => 'desc'),
			)
		);

		$this->set('wzusers', $this->Paginator->paginate('Wzuser'));

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

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Wzaccount");
		$this->Wzaccount = new Wzaccount();
		$this->Wzaccount->useDbConfig = 'wz';

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Wzuseraccount");
		$this->Wzuseraccount = new Wzuseraccount();
		$this->Wzuseraccount->useDbConfig = 'wz';

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Wzsetting");
		$this->Wzsetting = new Wzsetting();
		$this->Wzsetting->useDbConfig = 'wz';

		$wxsetting = $this->Wzsetting->findById(1);
		if (!$wxsetting) {
			$this->Session->setFlash(__d('webzash', 'Please update your setting below before adding any user'), 'error');
			return $this->redirect(array('controller' => 'wzsettings', 'action' => 'edit'));
		}

		/* Create list of wzaccounts */
		$wzaccounts = array(0 => '(ALL ACCOUNTS)') + $this->Wzaccount->find('list', array(
			'fields' => array('Wzaccount.id', 'Wzaccount.name'),
			'order' => array('Wzaccount.name')
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
					$this->Session->setFlash(__d('webzash', 'Password should be atleast 4 characters'), 'error');
					return;
				}

				$temp_password = $this->request->data['Wzuser']['password'];
				$this->request->data['Wzuser']['password'] = Security::hash($this->request->data['Wzuser']['password'], 'sha1', true);

				$this->request->data['Wzuser']['verification_key'] = Security::hash(uniqid() . uniqid());

				if (!empty($this->request->data['Wzuser']['account_ids'])) {
					if (in_array(0, $this->request->data['Wzuser']['account_ids'])) {
						$this->request->data['Wzuser']['all_accounts'] = 1;
					} else {
						$this->request->data['Wzuser']['all_accounts'] = 0;
					}
				} else {
					$this->request->data['Wzuser']['account_ids'] = array();
					$this->request->data['Wzuser']['all_accounts'] = 0;
				}

				/* Save user */
				$ds = $this->Wzuser->getDataSource();
				$ds->begin();

				if ($this->Wzuser->save($this->request->data)) {

					/* Save user - accounts association */
					if ($this->request->data['Wzuser']['all_accounts'] != 1) {
						if (!empty($this->request->data['Wzuser']['account_ids'])) {
							$data = array();
							foreach ($this->request->data['Wzuser']['account_ids'] as $row => $account_id) {
								if (!$this->Wzaccount->exists($account_id)) {
									continue;
								}
								$data[] = array('user_id' => $this->Wzuser->id, 'account_id' => $account_id);
							}
							if (!$this->Wzuseraccount->saveMany($data)) {
								$ds->rollback();
								$this->Session->setFlash(__d('webzash', 'The user account could not be saved. Please, try again.'), 'error');
								return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
							}
						}
					}

					$ds->commit();
					$this->Session->setFlash(__d('webzash', 'The user account has been created.'), 'success');
					return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
				} else {
					$this->request->data['Wzuser']['password'] = $temp_password;
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'The user account could not be saved. Please, try again.'), 'error');
					return;
				}
			} else {
				$this->Session->setFlash(__d('webzash', 'No data. Please, try again.'), 'error');
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

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Wzaccount");
		$this->Wzaccount = new Wzaccount();
		$this->Wzaccount->useDbConfig = 'wz';

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Wzsetting");
		$this->Wzsetting = new Wzsetting();
		$this->Wzsetting->useDbConfig = 'wz';

		$wzsetting = $this->Wzsetting->findById(1);
		if (!$wzsetting) {
			$this->Session->setFlash(__d('webzash', 'Please update your setting below before editing any user'), 'error');
			return $this->redirect(array('controller' => 'wzsettings', 'action' => 'edit'));
		}

		/* Check for valid user */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'User account not specified.'), 'error');
			return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
		}
		$wzuser = $this->Wzuser->findById($id);
		if (!$wzuser) {
			$this->Session->setFlash(__d('webzash', 'User account not found.'), 'error');
			return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
		}

		/* Create list of wzaccounts */
		$wzaccounts = $this->Wzaccount->find('list', array(
			'fields' => array('Wzaccount.id', 'Wzaccount.name'),
			'order' => array('Wzaccount.name')
		));
		$this->set('wzaccounts', $wzaccounts);

		/* on POST */
		if ($this->request->is('post') || $this->request->is('put')) {
			/* Set user id */
			unset($this->request->data['Wzuser']['id']);

			$this->Wzuser->id = $id;

			/* Save user */
			$ds = $this->Wzuser->getDataSource();
			$ds->begin();

			$this->request->data['Wzuser']['verification_key'] = Security::hash(uniqid() . uniqid());

			if ($this->Wzuser->save($this->request->data, true, array('username', 'fullname', 'email', 'role', 'status', 'email_verified', 'admin_verified', 'verification_key'))) {
				$ds->commit();
				$this->Session->setFlash(__d('webzash', 'The user account has been updated.'), 'success');
				return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
			} else {
				$this->request->data['Wzuser']['password'] = $temp_password;
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'The user account could not be updated. Please, try again.'), 'error');
				return;
			}
		} else {
			$this->request->data = $wzuser;
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

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Wzuseraccount");
		$this->Wzuseraccount = new Wzuseraccount();
		$this->Wzuseraccount->useDbConfig = 'wz';

		/* Check if valid id */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'User account not specified.'), 'error');
			return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
		}

		/* Check if user exists */
		if (!$this->Wzuser->exists($id)) {
			$this->Session->setFlash(__d('webzash', 'User account not found.'), 'error');
			return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
		}

		/* Cannot delete your own account */
		if ($id == $this->Auth->user('id')) {
			$this->Session->setFlash(__d('webzash', 'Cannot delete own account.'), 'error');
			return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
		}

		/* Delete user */
		$ds = $this->Wzuser->getDataSource();
		$ds->begin();

		if (!$this->Wzuser->delete($id)) {
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'The user account could not be deleted. Please, try again.'), 'error');
			return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
		}


		/* Delete user - account association */
		if (!$this->Wzuseraccount->deleteAll(array('Wzuseraccount.user_id' => $id))) {
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'The user account could not be deleted. Please, try again.'), 'error');
			return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
		}

		/* Success */
		$ds->commit();
		$this->Session->setFlash(__d('webzash', 'The user account has been deleted.'), 'success');

		return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
	}

/**
 * login method
 */
	public function login() {

		$this->set('title_for_layout', __d('webzash', 'User Login'));

		$this->layout = 'user';

		$this->Wzuser->useDbConfig = 'wz';

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Wzsetting");
		$this->Wzsetting = new Wzsetting();
		$this->Wzsetting->useDbConfig = 'wz';

		$wzsetting = $this->Wzsetting->findById(1);

		if ($this->request->is('post')) {

			/* Check status of user account */
			$user = $this->Wzuser->find('first', array('conditions' => array(
				'username' => $this->request->data['Wzuser']['username'],
				'password' => Security::hash($this->request->data['Wzuser']['password'], 'sha1', true)
			)));
			if (!$user) {
				$this->Session->setFlash(__d('webzash', 'Login failed. Please, try again.'), 'error');
				return;
			}

			if ($user['Wzuser']['status'] == 0) {
				$this->Session->setFlash(__d('webzash', 'User account is diabled. Please contact your administrator.'), 'error');
				return;
			}
			if (!($wzsetting) || $wzsetting['Wzsetting']['admin_verification'] != 0) {
				 if ($user['Wzuser']['admin_verified'] != 1) {
					$this->Session->setFlash(__d('webzash', 'Admin approval is pending. Please contact your admin.'), 'error');
					return;
				 }
			}
			if (!($wzsetting) || $wzsetting['Wzsetting']['email_verification'] != 0) {
				 if ($user['Wzuser']['email_verified'] != 1) {
					$this->Session->setFlash(__d('webzash', 'Email verification is pending. Please verify your email.'), 'error');
					return;
				 }
			}

			/* Login */
			if ($this->Auth->login()) {
				if ($this->Auth->user('role') == 'admin') {
					return $this->redirect(array('controller' => 'admin', 'action' => 'index'));
				} else {
					return $this->redirect($this->Auth->redirectUrl());
				}
			} else {
				$this->Session->setFlash(__d('webzash', 'Login failed. Please, try again.'), 'error');
			}
		}
	}

/**
 * logout method
 */
	public function logout() {
		return $this->redirect($this->Auth->logout());
	}

/**
 * verifiy email method
 */
	public function verify() {

		$this->set('title_for_layout', __d('webzash', 'User Email Verification'));

		$this->layout = 'user';

		$this->Wzuser->useDbConfig = 'wz';

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Wzsetting");
		$this->Wzsetting = new Wzsetting();
		$this->Wzsetting->useDbConfig = 'wz';

		$wzsetting = $this->Wzsetting->findById(1);

		$this->Auth->logout();

		$this->set('success', false);

		/* Check whether key is present in GET requets */
		if (empty($this->params['url']['u'])) {
			$this->set('success', false);
			$this->Session->setFlash(__d('webzash', 'Email verification failed. Please, try again.'), 'error');
			return;
		}
		if (empty($this->params['url']['k'])) {
			$this->set('success', false);
			$this->Session->setFlash(__d('webzash', 'Email verification failed. Please, try again.'), 'error');
			return;
		}

		/* Get user count */
		$user = $this->Wzuser->find('first', array('conditions' => array(
			'username' => $this->params['url']['u'],
			'verification_key' => $this->params['url']['k']
		)));

		if (empty($user)) {
			$this->set('success', false);
			$this->Session->setFlash(__d('webzash', 'Email verification failed. Please, try again.'), 'error');
			return;
		}

		/* Set email as verified */
		$ds = $this->Wzuser->getDataSource();
		$ds->begin();

		$this->Wzuser->id = $user['Wzuser']['id'];

		if ($this->Wzuser->saveField('email_verified', '1')) {
			$this->set('success', true);
			$ds->commit();
			$this->Session->setFlash(__d('webzash', 'User account is now verified'), 'success');
		} else {
			$this->set('success', false);
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'Email verification failed. Please, try again.'), 'error');
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
				$this->Session->setFlash(__d('webzash', 'Invalid username or email. Please, try again.'), 'error');
				return;
			} else {
				/* TODO : Send verification email */
				$this->Session->setFlash(__d('webzash', 'Verification email sent. Please check your email.'), 'success');
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
			$this->Session->setFlash(__d('webzash', 'User account not found.'), 'error');
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
					$this->Session->setFlash(__d('webzash', 'Your profile has been updated. You need to verify your new email, please check your email for verification details.'), 'success');
				} else {
					$this->Session->setFlash(__d('webzash', 'Your profile has been updated.'), 'success');
				}

				if ($this->Auth->user('role') == 'admin') {
					return $this->redirect(array('controller' => 'admin', 'action' => 'index'));
				} else {
					return $this->redirect($this->Auth->redirectUrl());
				}
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'Your profile could not be updated. Please, try again.'), 'error');
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
			$this->Session->setFlash(__d('webzash', 'User account not found.'), 'error');
			$this->redirect($this->Auth->logout());
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			/* Check length of password */
			if (strlen($this->request->data['Wzuser']['new_password']) < 4) {
				$this->Session->setFlash(__d('webzash', 'Password should be atleast 4 characters'), 'error');
				return;
			}

			/* Check if existing passwords match */
			if ($wzuser['Wzuser']['password'] != Security::hash($this->request->data['Wzuser']['existing_password'], 'sha1', true)) {
				$this->Session->setFlash(__d('webzash', 'Your existing password does not match. Please, try again.'), 'error');
				return;
			}

			$this->Wzuser->id = $this->Auth->user('id');

			/* Update user password */
			$ds = $this->Wzuser->getDataSource();
			$ds->begin();

			if ($this->Wzuser->saveField('password', Security::hash($this->request->data['Wzuser']['new_password'], 'sha1', true))) {
				$ds->commit();

				$this->Session->setFlash(__d('webzash', 'Your password has been updated.'), 'success');

				if ($this->Auth->user('role') == 'admin') {
					return $this->redirect(array('controller' => 'admin', 'action' => 'index'));
				} else {
					return $this->redirect($this->Auth->redirectUrl());
				}
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'Your password could not be updated. Please, try again.'), 'error');
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
			$this->Session->setFlash(__d('webzash', 'User account not specified.'), 'error');
			return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
		}

		$userid = $this->passedArgs['userid'];

		$wzuser = $this->Wzuser->findById($userid);
		if (!$wzuser) {
			$this->Session->setFlash(__d('webzash', 'User account not found.'), 'error');
			return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
		}

		$this->set('username', $wzuser['Wzuser']['username']);

		if ($this->request->is('post') || $this->request->is('put')) {
			/* Check length of password */
			if (strlen($this->request->data['Wzuser']['new_password']) < 4) {
				$this->Session->setFlash(__d('webzash', 'Password should be atleast 4 characters'), 'error');
				return;
			}

			$this->Wzuser->id = $wzuser['Wzuser']['id'];

			/* Update user password */
			$ds = $this->Wzuser->getDataSource();
			$ds->begin();

			if ($this->Wzuser->saveField('password', Security::hash($this->request->data['Wzuser']['new_password'], 'sha1', true))) {
				$ds->commit();
				$this->Session->setFlash(__d('webzash', 'User password has been updated.'), 'success');
				return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
			} else {
				$ds->rollback();
				$this->Session->setFlash(__d('webzash', 'User password could not be updated. Please, try again.'), 'error');
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
				$this->Session->setFlash(__d('webzash', 'Invalid username or email. Please, try again.'), 'error');
				return;
			}

			$random_password = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);

			$this->Wzuser->id = $wzuser['Wzuser']['id'];

			/* Update user password */
			$ds = $this->Wzuser->getDataSource();
			$ds->begin();

			if ($this->Wzuser->saveField('password', Security::hash($random_password, 'sha1', true))) {
				$ds->commit();

				/* TODO : Send reset password email */
				$this->Session->setFlash(__d('webzash', 'Password reset. Please check your email for more details on how to reset password.'), 'success');
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

		/* TODO : Switch to loadModel() */
		App::import("Webzash.Model", "Wzsetting");
		$this->Wzsetting = new Wzsetting();
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
				unset($this->request->data['Wzuser']['role']);
				unset($this->request->data['Wzuser']['status']);
				unset($this->request->data['Wzuser']['verification_key']);
				unset($this->request->data['Wzuser']['email_verified']);
				unset($this->request->data['Wzuser']['admin_verified']);

				/* Check length of password */
				if (strlen($this->request->data['Wzuser']['password']) < 4) {
					$this->Session->setFlash(__d('webzash', 'Password should be atleast 4 characters'), 'error');
					return;
				}

				$user = array('Wzuser' => array(
					'username' => $this->request->data['Wzuser']['username'],
					'password' => Security::hash($this->request->data['Wzuser']['password'], 'sha1', true),
					'fullname' => $this->request->data['Wzuser']['fullname'],
					'email' => $this->request->data['Wzuser']['email'],
					'role' => 'guest',
					'status' => '1',
					'verification_key' => Security::hash(uniqid() . uniqid()),
					'email_verified' => '0',
					'admin_verified' => '0',
				));

				/* Save user */
				$ds = $this->Wzuser->getDataSource();
				$ds->begin();

				if ($this->Wzuser->save($user)) {
					$ds->commit();
					$this->Session->setFlash(__d('webzash', 'User account has been created.'), 'success');
					return $this->redirect(array('controller' => 'wzusers', 'action' => 'index'));
				} else {
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'User account could not be created. Please, try again.'), 'error');
					return;
				}
			} else {
				$this->Session->setFlash(__d('webzash', 'No data. Please, try again.'), 'error');
				return;
			}
		}
	}

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('login', 'logout', 'verify', 'resend', 'forgot', 'register');
	}

	/* Authorization check */
	public function isAuthorized($user) {
		if ($this->action === 'index') {
			return $this->Permission->is_allowed('access admin section', $user['role']);
		}

		if ($this->action === 'add') {
			return $this->Permission->is_allowed('access admin section', $user['role']);
		}

		if ($this->action === 'edit') {
			return $this->Permission->is_allowed('access admin section', $user['role']);
		}

		if ($this->action === 'delete') {
			return $this->Permission->is_allowed('access admin section', $user['role']);
		}

		if ($this->action === 'profile') {
			return $this->Permission->is_allowed('registered', $user['role']);
		}

		if ($this->action === 'changepass') {
			return $this->Permission->is_allowed('registered', $user['role']);
		}

		if ($this->action === 'resetpass') {
			return $this->Permission->is_allowed('access admin section', $user['role']);
		}

		return parent::isAuthorized($user);
	}
}
