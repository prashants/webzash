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
		<div class="reports-container">
			<div class="reports-title">
				<?php echo $this->Html->link(__d('webzash', 'Balance sheet'), array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'balancesheet')); ?>
			</div>
			<div class="reports-desc">
			</div>
		</div>
		<div class="reports-container">
			<div class="reports-title">
				<?php echo $this->Html->link(__d('webzash', 'Profit and Loss statement'), array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'profitloss')); ?>
			</div>
			<div class="reports-desc">
			</div>
		</div>
		<div class="reports-container">
			<div class="reports-title">
				<?php echo $this->Html->link(__d('webzash', 'Trial balance'), array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'trialbalance')); ?>
			</div>
			<div class="reports-desc">
			</div>
		</div>
		<div class="reports-container">
			<div class="reports-title">
				<?php echo $this->Html->link(__d('webzash', 'Ledger statement'), array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'ledgerstatement')); ?>
			</div>
			<div class="reports-desc">
			</div>
		</div>
		<div class="reports-container">
			<div class="reports-title">
				<?php echo $this->Html->link(__d('webzash', 'Reconciliation'), array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'reconciliation')); ?>
			</div>
			<div class="reports-desc">
			</div>
		</div>
	</div>
</div>
