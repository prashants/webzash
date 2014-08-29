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
				<?php echo $this->Html->link(__d('webzash', 'Account settings'), array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'account')); ?>
			</div>
			<div class="settings-desc">
				<?php echo __d('webzash', 'Setup account details, currency, time, etc.'); ?>
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo $this->Html->link(__d('webzash', 'Carry forward'), array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'cf')); ?>
			</div>
			<div class="settings-desc">
				<?php echo __d('webzash', 'Carry forward account to next financial year'); ?>
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo $this->Html->link(__d('webzash', 'Email settings'), array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'email')); ?>
			</div>
			<div class="settings-desc">
				<?php echo __d('webzash', 'Setup outgoing email'); ?>
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo $this->Html->link(__d('webzash', 'Printer settings'), array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'printer')); ?>
			</div>
			<div class="settings-desc">
				<?php echo __d('webzash', 'Setup printing options for entries, reports, etc.'); ?>
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo $this->Html->link(__d('webzash', 'Download backup'), array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'backup')); ?>
			</div>
			<div class="settings-desc">
				<?php echo __d('webzash', 'Download backup of current accounts data'); ?>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="settings-container">
			<div class="settings-title">
				<?php echo $this->Html->link(__d('webzash', 'Tags'), array('plugin' => 'webzash', 'controller' => 'tags', 'action' => 'index')); ?>
			</div>
			<div class="settings-desc">
				<?php echo __d('webzash', 'Manage tags'); ?>
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo $this->Html->link(__d('webzash', 'Entry Types'), array('plugin' => 'webzash', 'controller' => 'entrytypes', 'action' => 'index')); ?>
			</div>
			<div class="settings-desc">
				<?php echo __d('webzash', 'Manage entry types'); ?>
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo $this->Html->link(__d('webzash', 'Lock account'), array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'lock')); ?>
			</div>
			<div class="settings-desc">
				<?php echo __d('webzash', 'Lock account to prevent further changes'); ?>
			</div>
		</div>
	</div>
</div>
