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
<div class="lock form">
<?php
	echo $this->Form->create('Setting', array(
		'inputDefaults' => array(
			'div' => 'form-group',
			'wrapInput' => false,
			'class' => 'form-control',
		),
	));

	if ($locked == '1') {
		echo $this->Form->label('Setting.lock', __d('webzash', 'Currently this account is locked'));
	} else {
		echo $this->Form->label('Setting.lock', __d('webzash', 'Currently this account is unlocked'));
	}
	echo $this->Form->input('account_locked', array(
		'type' => 'checkbox',
		'checked' => $locked,
		'label' => __d('webzash', 'Lock account'),
		'class' => 'checkbox',
		'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : Once a account is locked no further changes will be permitted. You will have to unlock it to make changes.') . '</span>',
	));

	echo '<div class="form-group">';
	echo $this->Form->submit(__d('webzash', 'Submit'), array(
		'div' => false,
		'class' => 'btn btn-primary'
	));
	echo $this->Html->tag('span', '', array('class' => 'link-pad'));
	echo $this->Html->link(__d('webzash', 'Cancel'), array('plugin' => 'webzash', 'controller' => 'settings', 'action' => 'index'), array('class' => 'btn btn-default'));
	echo '</div>';

	echo $this->Form->end();
?>
</div>
