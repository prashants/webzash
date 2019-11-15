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
<!-- Static navbar -->
<div class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<?php echo $this->Html->link(
				Configure::read('Webzash.AppName'),
				Configure::read('Webzash.AppURL'),
				array('class' => 'navbar-brand', 'target' => '_blank')
			); ?>
		</div>
		<div class="navbar-collapse collapse">
			<?php if ($this->Session->read('Auth.User')): ?>
			<ul class="nav navbar-nav">

				<li><?php echo $this->Html->link(__d('webzash', 'Dashboard'), array('plugin' => 'webzash', 'controller' => 'dashboard', 'action' => 'index')); ?></li>
				<li><?php echo $this->Html->link(__d('webzash', 'Accounts'), array('plugin' => 'webzash', 'controller' => 'accounts', 'action' => 'show')); ?></li>
				<li><?php echo '<li>' . $this->Html->link(__d('webzash', 'Entries'), array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index')); ?></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Reports <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><?php echo '<li>' . $this->Html->link(__d('webzash', 'Balance Sheet'), array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'balancesheet')); ?></li>
						<li><?php echo '<li>' . $this->Html->link(__d('webzash', 'Profit & Loss'), array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'profitloss')); ?></li>
						<li><?php echo '<li>' . $this->Html->link(__d('webzash', 'Trial Balance'), array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'trialbalance')); ?></li>
						<li><?php echo '<li>' . $this->Html->link(__d('webzash', 'Ledger Statement'), array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'ledgerstatement')); ?></li>
						<li><?php echo '<li>' . $this->Html->link(__d('webzash', 'Ledger Entries'), array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'ledgerentries')); ?></li>
						<li><?php echo '<li>' . $this->Html->link(__d('webzash', 'Reconciliation'), array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'reconciliation')); ?></li>
					</ul>
				</li>
				<li><?php echo $this->Html->link(__d('webzash', 'Search'), array('plugin' => 'webzash', 'controller' => 'search', 'action' => 'index')); ?></li>
				<li><?php echo $this->Html->link(__d('webzash', 'Settings'), array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'index')); ?></li>
				<li><?php echo $this->Html->link(__d('webzash', 'Help'), array('plugin' => 'webzash', 'controller' => 'help', 'action' => 'index')); ?></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<?php if ($this->Session->read('Auth.User.role') == 'admin') : ?>
					<li><span><?php echo $this->Html->link(__d('webzash', 'Administer'), array('plugin' => 'webzash', 'controller' => 'admin', 'action' => 'index'), array('class' => 'btn btn-danger navbar-btn')); ?></span> &nbsp; &nbsp;</li>
				<?php endif; ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Profile <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><?php echo $this->Html->link(__d('webzash', 'Update Profile'), array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'profile')); ?></li>
						<li><?php echo $this->Html->link(__d('webzash', 'Change Password'), array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'changepass')); ?></li>
						<li><?php echo $this->Html->link(__d('webzash', 'Logout'), array('plugin' => 'webzash', 'controller' => 'wzusers', 'action' => 'logout')); ?></li>
					</ul>
				</li>
			</ul>
			<?php endif; ?>
		</div><!--/.nav-collapse -->
	</div><!--/.container-fluid -->
</div>
