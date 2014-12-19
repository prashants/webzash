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
<div class="row">
	<div class="col-md-4">
		<div class="settings-container">
			<div class="settings-title">
				<?php echo $this->Html->link(__d('webzash', 'Create account'), array('plugin' => 'webzash', 'controller' => 'wzaccounts', 'action' => 'create')); ?>
			</div>
			<div class="settings-desc">
				<?php echo __d('webzash', 'Create a new account '); ?>
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo $this->Html->link(__d('webzash', 'Manage accounts'), array('plugin' => 'webzash', 'controller' => 'wzaccounts', 'action' => 'index')); ?>
			</div>
			<div class="settings-desc">
				<?php echo __d('webzash', 'Manage existing accounts '); ?>
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo $this->Html->link(__d('webzash', 'Manage users'), array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'index')); ?>
			</div>
			<div class="settings-desc">
				<?php echo __d('webzash', 'Manage users and permissions'); ?>
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo $this->Html->link(__d('webzash', 'General settings'), array('plugin' => 'webzash', 'controller' => 'wzsettings', 'action' => 'edit')); ?>
			</div>
			<div class="settings-desc">
				<?php echo __d('webzash', 'General application settings'); ?>
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo $this->Html->link(__d('webzash', 'System information'), array('plugin' => 'webzash', 'controller' => 'wzsettings', 'action' => 'sysinfo')); ?>
			</div>
			<div class="settings-desc">
				<?php echo __d('webzash', 'General system information'); ?>
			</div>
		</div>
	</div>
</div>
