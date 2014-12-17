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

<script type="text/javascript">
$(document).ready(function() {

	/* If use default email is checked then disable rest of the fields */
	$('#SettingEmailUseDefault').change(function() {
		if ($(this).is(':checked')) {
			$('#SettingEmailProtocol').prop('disabled', true);
			$('#SettingEmailHost').prop('disabled', true);
			$('#SettingEmailPort').prop('disabled', true);
			$('#SettingEmailTls').prop('disabled', true);
			$('#SettingEmailUsername').prop('disabled', true);
			$('#SettingEmailPassword').prop('disabled', true);
			$('#SettingEmailFrom').prop('disabled', true);
		} else {
			$('#SettingEmailProtocol').prop('disabled', false);
			$('#SettingEmailHost').prop('disabled', false);
			$('#SettingEmailPort').prop('disabled', false);
			$('#SettingEmailTls').prop('disabled', false);
			$('#SettingEmailUsername').prop('disabled', false);
			$('#SettingEmailPassword').prop('disabled', false);
			$('#SettingEmailFrom').prop('disabled', false);
		}
	});
	$('#SettingEmailUseDefault').trigger('change');
});
</script>

<div class="email form">
<?php

	echo $this->Form->create('Setting', array(
		'inputDefaults' => array(
			'div' => 'form-group',
			'wrapInput' => false,
			'class' => 'form-control',
		),
	));

	echo $this->Form->input('email_use_default', array(
		'type' => 'checkbox',
		'label' => __d('webzash', 'Use default email settings'),
		'class' => 'checkbox',
		'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : If selected the default email settings in the Administer > General Settings will be used.') . '</span>',
	));
	echo $this->Form->input('email_protocol', array('type' => 'select', 'options' => $this->Generic->mail_protocol_options(), 'label' => __d('webzash', 'Protocol')));
	echo $this->Form->input('email_host', array('label' => __d('webzash', 'Hostname')));
	echo $this->Form->input('email_port', array('label' => __d('webzash', 'Port')));
	echo $this->Form->input('email_tls', array('type' => 'checkbox', 'label' => __d('webzash', 'Use TLS'), 'class' => 'checkbox'));
	echo $this->Form->input('email_username', array('label' => __d('webzash', 'Username')));
	echo $this->Form->input('email_password', array('type' => 'password', 'label' => __d('webzash', 'Password')));
	echo $this->Form->input('email_from', array('label' => __d('webzash', 'From')));

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
