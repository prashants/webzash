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
	$('#SettingFyStart').datepicker({
		dateFormat: $("#SettingDateFormat").val().split('|')[1],	/* Read the Javascript date format value */
		numberOfMonths: 1,
		onClose: function(selectedDate) {
			$("#SettingFyEnd").datepicker("option", "minDate", selectedDate);
		}
	});
	$('#SettingFyEnd').datepicker({
		dateFormat: $("#SettingDateFormat").val().split('|')[1],	/* Read the Javascript date format value */
		numberOfMonths: 1,
		onClose: function(selectedDate) {
			$("#SettingFyStart").datepicker("option", "maxDate", selectedDate);
		}
	});

	$("#SettingDateFormat").change(function() {
		/* Read the Javascript date format value */
		dateFormat = $(this).val().split('|')[1];
		$("#SettingFyStart").datepicker("option", "dateFormat", dateFormat);
		$("#SettingFyEnd").datepicker("option", "dateFormat", dateFormat);
	});
});
</script>

<div class="account form">
<?php
	echo $this->Form->create('Setting', array(
		'inputDefaults' => array(
			'div' => 'form-group',
			'wrapInput' => false,
			'class' => 'form-control',
		),
	));

	echo $this->Form->input('name', array('label' => __d('webzash', 'Company / Personal Name')));
	echo $this->Form->input('address', array('type' => 'textarea', 'label' => __d('webzash', 'Address'), 'rows' => '3'));
	echo $this->Form->input('email', array('label' => __d('webzash', 'Email')));
	echo $this->Form->input('currency_symbol', array('label' => __d('webzash', 'Currency symbol')));
	echo $this->Form->input('currency_format', array('type' => 'select', 'options' => $this->Generic->currency_format_options(), 'label' => __d('webzash', 'Currency format')));
	echo $this->Form->input('date_format', array('type' => 'select', 'options' => $this->Generic->dateformat_options(), 'label' => __d('webzash', 'Date format')));
	echo $this->Form->input('fy_start', array('type' => 'text', 'label' => __d('webzash', 'Financial year start')));
	echo $this->Form->input('fy_end', array('type' => 'text', 'label' => __d('webzash', 'Financial year end')));

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
