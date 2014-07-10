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
<div class="entrytypes add form">
	<?php
		$numbering_options = array(
			'1' => __('Auto'),
			'2' => __('Manual (required)'),
			'3' => __('Manual (optional)'),
		);
		$restriction_options = array(
			'1' => __('Unrestricted'),
			'2' => __('Atleast one Bank or Cash account must be present on Debit side'),
			'3' => __('Atleast one Bank or Cash account must be present on Credit side'),
			'4' => __('Only Bank or Cash account can be present on both Debit and Credit side'),
			'5' => __('Only NON Bank or Cash account can be present on both Debit and Credit side'),
		);
		echo $this->Form->create('Entrytype');
		echo $this->Form->input('label', array('label' => __d('webzash', 'Label')));
		echo $this->Form->input('name', array('label' => __d('webzash', 'Name')));
		echo $this->Form->input('description', array('type' => 'textarea', 'label' => __d('webzash', 'Description'), 'rows' => '3'));
		echo $this->Form->input('numbering', array('type' => 'select', 'options' => $numbering_options, 'label' => __d('webzash', 'Numbering')));
		echo $this->Form->input('prefix', array('label' => __d('webzash', 'Prefix')));
		echo $this->Form->input('suffix', array('label' => __d('webzash', 'Suffix')));
		echo $this->Form->input('zero_padding', array('label' => __d('webzash', 'Zero Padding')));
		echo $this->Form->input('restriction_bankcash', array('type' => 'select', 'options' => $restriction_options, 'label' => __d('webzash', 'Restrictions')));
		echo $this->Form->end(__d('webzash', 'Submit'));
		echo $this->Html->link(__d('webzash', 'Back'), array('controller' => 'entrytypes', 'action' => 'index'));
	?>
</div>
