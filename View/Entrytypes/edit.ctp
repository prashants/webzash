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
<div class="entrytypes edit form">
	<?php
		echo $this->Form->create('Entrytype', array(
			'inputDefaults' => array(
				'div' => 'form-group',
				'wrapInput' => false,
				'class' => 'form-control',
			),
		));

		echo $this->Form->input('label', array('label' => __d('webzash', 'Label')));
		echo $this->Form->input('name', array('label' => __d('webzash', 'Name')));
		echo $this->Form->input('description', array('type' => 'textarea', 'label' => __d('webzash', 'Description'), 'rows' => '3'));
		echo $this->Form->input('numbering', array(
			'type' => 'select',
			'options' => $this->Generic->entrytype_numbering_options(),
			'label' => __d('webzash', 'Numbering'),
			'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : How the entry numbering is handled.') . '</span>',
		));
		echo $this->Form->input('prefix', array(
			'label' => __d('webzash', 'Prefix'),
			'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : Prefix to add before entry numbers.') . '</span>',
		));
		echo $this->Form->input('suffix', array(
			'label' => __d('webzash', 'Suffix'),
			'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : Suffix to add after entry numbers.') . '</span>',
		));
		echo $this->Form->input('zero_padding', array(
			'label' => __d('webzash', 'Zero Padding'),
			'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : Number of zeros to pad before entry numbers.') . '</span>',
		));
		echo $this->Form->input('restriction_bankcash', array(
			'type' => 'select',
			'options' => $this->Generic->entrytype_restriction_options(),
			'label' => __d('webzash', 'Restrictions'),
			'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : Restrictions to be placed on the ledgers selected in entry.') . '</span>',
		));

		echo '<div class="form-group">';
		echo $this->Form->submit(__d('webzash', 'Submit'), array(
			'div' => false,
			'class' => 'btn btn-primary'
		));
		echo $this->Html->tag('span', '', array('class' => 'link-pad'));
		echo $this->Html->link(__d('webzash', 'Cancel'), array('plugin' => 'webzash', 'controller' => 'entrytypes', 'action' => 'index'), array('class' => 'btn btn-default'));
		echo '</div>';

		echo $this->Form->end();
	?>
</div>
