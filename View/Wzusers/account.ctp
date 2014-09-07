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
?>
<div class="wzuser account form">
	<?php
		if ($wzaccounts_count < 1) {
			if ($this->Session->read('Auth.User.role') == 'admin') {
				echo $this->Form->label('create', __d('webzash', 'Sorry, no accounts are available. Please create a new account ') . $this->Html->link(__d('webzash', 'here'), array('plugin' => 'webzash', 'controller' => 'wzaccounts', 'action' => 'create')) . '.');
			} else {
				echo $this->Form->label('create', __d('webzash', 'Sorry, no accounts are available. Please contact your administrator.'));
			}
		} else if (!$wzaccounts) {
			echo $this->Form->label('create', __d('webzash', 'Sorry, no accounts are available. Please contact your administrator.'));
		} else if (sizeof($wzaccounts) < 2) {
			if ($this->Session->read('Auth.User.role') == 'admin') {
				echo $this->Form->label('create', __d('webzash', 'Sorry, you do not have access to any accounts. You can manage your accounts ') . $this->Html->link(__d('webzash', 'here'), array('plugin' => 'webzash', 'controller' => 'wzaccounts', 'action' => 'index')) . '.');
			} else {
				echo $this->Form->label('create', __d('webzash', 'Sorry, you do not have access to any accounts. Please contact your administrator.'));
			}
		} else {
			echo $this->Form->create('Wzuser', array(
				'inputDefaults' => array(
					'div' => 'form-group',
					'wrapInput' => false,
					'class' => 'form-control',
				),
			));

			echo $this->Form->label('active', __d('webzash', 'Currently active account : "%s"', h($curActiveAccount)));
			echo $this->Form->input('wzaccount_id', array(
				'type' => 'select',
				'options' => $wzaccounts,
				'label' => __d('webzash', 'Select account'),
				'multiple' => false,
				'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : If you wish to use multiple accounts simultaneously, please use different browsers for each. Also, please select (NONE) if you wish to deactivate all accounts.') . '</span>'
			));

			echo '<div class="form-group">';
			echo $this->Form->submit(__d('webzash', 'Activate'), array(
				'div' => false,
				'class' => 'btn btn-primary'
			));
			echo $this->Html->tag('span', '', array('class' => 'link-pad'));
			echo $this->Html->link(__d('webzash', 'Cancel'), array('plugin' => 'webzash', 'controller' => 'dashboard', 'action' => 'index'), array('class' => 'btn btn-default'));
			echo '</div>';

			echo $this->Form->end();
		}
	?>
</div>
