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
<div class="ledgers add form">
	<?php
		echo $this->Form->create('Ledger');
		echo $this->Form->input('name', array('label' => __('Ledger name')));
		echo $this->Form->input('group_id', array('type' => 'select', 'options' => $parents, 'label' => __('Parent group')));
		echo $this->Form->input('op_balance_dc', array('type' => 'select', 'options' => array('D' => 'Dr', 'C' => 'Cr'), 'label' => __('Opening balance')));
		echo $this->Form->input('op_balance', array('label' => false, 'required' => false));
		echo $this->Form->input('type', array('type' => 'checkbox', 'label' => __('Bank or cash account')));
		echo $this->Form->input('reconciliation', array('type' => 'checkbox', 'label' => __('Reconciliation')));
		echo $this->Form->end(__('Submit'));
		echo $this->Html->link(__('Back'), array('controller' => 'accounts', 'action' => 'show'));
	?>
</div>
