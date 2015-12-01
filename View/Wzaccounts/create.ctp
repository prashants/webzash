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
	/* Setup jQuery datepicker ui */
	$('#WzaccountFyStart').datepicker({
		dateFormat: $("#WzaccountDateFormat").val().split('|')[1],	/* Read the Javascript date format value */
		numberOfMonths: 1,
		onClose: function(selectedDate) {
			$("#WzaccountFyEnd").datepicker("option", "minDate", selectedDate);
		}
	});
	$('#WzaccountFyEnd').datepicker({
		dateFormat: $("#WzaccountDateFormat").val().split('|')[1],	/* Read the Javascript date format value */
		numberOfMonths: 1,
		onClose: function(selectedDate) {
			$("#WzaccountFyStart").datepicker("option", "maxDate", selectedDate);
		}
	});

	$("#WzaccountDateFormat").change(function() {
		/* Read the Javascript date format value */
		dateFormat = $(this).val().split('|')[1];
		$("#WzaccountFyStart").datepicker("option", "dateFormat", dateFormat);
		$("#WzaccountFyEnd").datepicker("option", "dateFormat", dateFormat);
	});
});
</script>

<div class="wzaccount create form">
	<?php

	echo $this->Form->create('Wzaccount', array(
		'inputDefaults' => array(
			'div' => 'form-group',
			'wrapInput' => false,
			'class' => 'form-control',
		),
	));

	echo $this->Form->input('label', array(
		'label' => __d('webzash', 'Label'),
		'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : It is recommended to use a descriptive label like "sample20142105" which includes both a short name and the accounting year.') . '</span>',
	));
	echo $this->Form->input('name', array('required' => 'required', 'div' => 'form-group required', 'label' => __d('webzash', 'Company / Personal Name')));
	echo $this->Form->input('address', array('type' => 'textarea', 'label' => __d('webzash', 'Address'), 'rows' => '3'));
	echo $this->Form->input('email', array('label' => __d('webzash', 'Email')));
	echo $this->Form->input('currency_symbol', array('label' => __d('webzash', 'Currency symbol')));
	echo $this->Form->input('currency_format', array(
			'type' => 'select',
			'options' => $this->Generic->currency_format_options(),
			'label' => __d('webzash', 'Currency format'),
			'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : Check the wiki if you want to create a custom format for currency.') . '</span>',
	));
	echo $this->Form->input('decimal_places', array(
			'type' => 'select',
			'options' => $this->Generic->decimal_places_options(),
			'label' => __d('webzash', 'Decimal places'),
			'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : This options cannot be changed later on.') . '</span>',
	));
	echo $this->Form->input('date_format', array('type' => 'select', 'options' => $this->Generic->dateformat_options(), 'required' => 'required', 'div' => 'form-group required', 'label' => __d('webzash', 'Date format')));
	echo $this->Form->input('fy_start', array('type' => 'text', 'required' => 'required', 'div' => 'form-group required', 'label' => __d('webzash', 'Financial year start')));
	echo $this->Form->input('fy_end', array('type' => 'text', 'required' => 'required', 'div' => 'form-group required', 'label' => __d('webzash', 'Financial year end')));

	echo "<fieldset><legend>Database Settings</legend>";
	// TODO echo $this->Form->input('create_db', array('type' => 'checkbox', 'label' => __d('webzash', 'Create database if it does not exists')));
	echo $this->Form->input('db_datasource', array('type' => 'select', 'options' => $this->Generic->wzaccount_dbtype_options(), 'label' => __d('webzash', 'Database type')));
	echo $this->Form->input('db_database', array('label' => __d('webzash', 'Database name')));
	echo $this->Form->input('db_schema', array(
		'label' => __d('webzash', 'Database schema'),
		'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : Database schema is required for Postgres database connection. Leave it blank for MySQL connections.') . '</span>',
	));
	echo $this->Form->input('db_host', array('label' => __d('webzash', 'Database host'), 'default' => 'localhost'));
	echo $this->Form->input('db_port', array('label' => __d('webzash', 'Database port'), 'default' => '3306' ));
	echo $this->Form->input('db_login', array('label' => __d('webzash', 'Database login')));
	echo $this->Form->input('db_password', array('label' => __d('webzash', 'Database password')));
	echo $this->Form->input('db_prefix', array(
		'label' => __d('webzash', 'Database prefix'),
		'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : Database table prefix to use (optional). All tables for this account will be created with this prefix, useful if you have only one database available and want to use multiple accounts.') . '</span>',
	));
	echo $this->Form->input('db_persistent', array('type' => 'checkbox', 'label' => __d('webzash', 'Use persistent connection'), 'class' => 'checkbox'));
	echo $this->Form->input('db_settings', array(
		'label' => __d('webzash', 'Database settings'),
		'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : Any additional settings to pass on to the database connection.') . '</span>',
	));

	echo "</fieldset>";

	echo '<div class="form-group">';
	echo $this->Form->submit(__d('webzash', 'Submit'), array(
		'div' => false,
		'class' => 'btn btn-primary'
	));
	echo $this->Html->tag('span', '', array('class' => 'link-pad'));
	echo $this->Html->link(__d('webzash', 'Cancel'), array('plugin' => 'webzash', 'controller' => 'admin', 'action' => 'index'), array('class' => 'btn btn-default'));
	echo '</div>';

	echo $this->Form->end();
	?>
</div>
