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

	/* javascript floating point operations */
	var jsFloatOps = function(param1, param2, op) {
		<?php if (Configure::read('Account.decimal_places') == 2) { ?>
			param1 = param1 * 100;
			param2 = param2 * 100;
		<?php } else if (Configure::read('Account.decimal_places') == 3) { ?>
			param1 = param1 * 1000;
			param2 = param2 * 1000;
		<?php } ?>
		param1 = param1.toFixed(0);
		param2 = param2.toFixed(0);
		param1 = Math.floor(param1);
		param2 = Math.floor(param2);
		var result = 0;
		if (op == '+') {
			result = param1 + param2;
			<?php if (Configure::read('Account.decimal_places') == 2) { ?>
				result = result/100;
			<?php } else if (Configure::read('Account.decimal_places') == 3) { ?>
				result = result/1000;
			<?php } ?>
			return result;
		}
		if (op == '-') {
			result = param1 - param2;
			<?php if (Configure::read('Account.decimal_places') == 2) { ?>
				result = result/100;
			<?php } else if (Configure::read('Account.decimal_places') == 3) { ?>
				result = result/1000;
			<?php } ?>
			return result;
		}
		if (op == '!=') {
			if (param1 != param2)
				return true;
			else
				return false;
		}
		if (op == '==') {
			if (param1 == param2)
				return true;
			else
				return false;
		}
		if (op == '>') {
			if (param1 > param2)
				return true;
			else
				return false;
		}
		if (op == '<') {
			if (param1 < param2)
				return true;
			else
				return false;
		}
	}

	/* Calculating Dr and Cr total */
	$(document).on('change', '.dr-item', function() {
		var drTotal = 0;
		$("table tr .dr-item").each(function() {
			var curDr = $(this).prop('value');
			curDr = parseFloat(curDr);
			if (isNaN(curDr))
				curDr = 0;
			drTotal = jsFloatOps(drTotal, curDr, '+');
		});
		$("table tr #dr-total").text(drTotal);
		var crTotal = 0;
		$("table tr .cr-item").each(function() {
			var curCr = $(this).prop('value');
			curCr = parseFloat(curCr);
			if (isNaN(curCr))
				curCr = 0;
			crTotal = jsFloatOps(crTotal, curCr, '+');
		});
		$("table tr #cr-total").text(crTotal);

		if (jsFloatOps(drTotal, crTotal, '==')) {
			$("table tr #dr-total").css("background-color", "#FFFF99");
			$("table tr #cr-total").css("background-color", "#FFFF99");
			$("table tr #dr-diff").text("-");
			$("table tr #cr-diff").text("");
		} else {
			$("table tr #dr-total").css("background-color", "#FFE9E8");
			$("table tr #cr-total").css("background-color", "#FFE9E8");
			if (jsFloatOps(drTotal, crTotal, '>')) {
				$("table tr #dr-diff").text("");
				$("table tr #cr-diff").text(jsFloatOps(drTotal, crTotal, '-'));
			} else {
				$("table tr #dr-diff").text(jsFloatOps(crTotal, drTotal, '-'));
				$("table tr #cr-diff").text("");
			}
		}
	});

	$(document).on('change', '.cr-item', function() {
		var drTotal = 0;
		$("table tr .dr-item").each(function() {
			var curDr = $(this).prop('value')
			curDr = parseFloat(curDr);
			if (isNaN(curDr))
				curDr = 0;
			drTotal = jsFloatOps(drTotal, curDr, '+');
		});
		$("table tr #dr-total").text(drTotal);
		var crTotal = 0;
		$("table tr .cr-item").each(function() {
			var curCr = $(this).prop('value')
			curCr = parseFloat(curCr);
			if (isNaN(curCr))
				curCr = 0;
			crTotal = jsFloatOps(crTotal, curCr, '+');
		});
		$("table tr #cr-total").text(crTotal);

		if (jsFloatOps(drTotal, crTotal, '==')) {
			$("table tr #dr-total").css("background-color", "#FFFF99");
			$("table tr #cr-total").css("background-color", "#FFFF99");
			$("table tr #dr-diff").text("-");
			$("table tr #cr-diff").text("");
		} else {
			$("table tr #dr-total").css("background-color", "#FFE9E8");
			$("table tr #cr-total").css("background-color", "#FFE9E8");
			if (jsFloatOps(drTotal, crTotal, '>')) {
				$("table tr #dr-diff").text("");
				$("table tr #cr-diff").text(jsFloatOps(drTotal, crTotal, '-'));
			} else {
				$("table tr #dr-diff").text(jsFloatOps(crTotal, drTotal, '-'));
				$("table tr #cr-diff").text("");
			}
		}
	});

	/* Dr - Cr dropdown changed */
	$(document).on('change', '.dc-dropdown', function() {
		var drValue = $(this).parent().parent().next().next().children().children().prop('value');
		var crValue = $(this).parent().parent().next().next().next().children().children().prop('value');

		if ($(this).parent().parent().next().children().children().val() == "0") {
			return;
		}

		drValue = parseFloat(drValue);
		if (isNaN(drValue))
			drValue = 0;

		crValue = parseFloat(crValue);
		if (isNaN(crValue))
			crValue = 0;

		if ($(this).prop('value') == "D") {
			if (drValue == 0 && crValue != 0) {
				$(this).parent().parent().next().next().children().children().prop('value', crValue);
			}
			$(this).parent().parent().next().next().next().children().children().prop('value', "");
			$(this).parent().parent().next().next().next().children().children().prop('disabled', 'disabled');
			$(this).parent().parent().next().next().children().children().prop('disabled', '');
		} else {
			if (crValue == 0 && drValue != 0) {
				$(this).parent().parent().next().next().next().children().prop('value', drValue);
			}
			$(this).parent().parent().next().next().children().children().prop('value', "");
			$(this).parent().parent().next().next().children().children().prop('disabled', 'disabled');
			$(this).parent().parent().next().next().next().children().children().prop('disabled', '');
		}
		/* Recalculate Total */
		$('.dr-item:first').trigger('change');
		$('.cr-item:first').trigger('change');
	});

	/* Ledger dropdown changed */
	$(document).on('change', '.ledger-dropdown', function() {
		if ($(this).val() == "0") {
			/* Reset and diable dr and cr amount */
			$(this).parent().parent().next().children().children().prop('value', "");
			$(this).parent().parent().next().next().children().children().prop('value', "");
			$(this).parent().parent().next().children().children().prop('disabled', 'disabled');
			$(this).parent().parent().next().next().children().children().prop('disabled', 'disabled');
		} else {
			/* Enable dr and cr amount and trigger Dr/Cr change */
			$(this).parent().parent().next().children().children().prop('disabled', '');
			$(this).parent().parent().next().next().children().children().prop('disabled', '');
			$(this).parent().parent().prev().children().children().trigger('change');
		}
		/* Trigger dr and cr change */
		$(this).parent().parent().next().children().children().trigger('change');
		$(this).parent().parent().next().next().children().children().trigger('change');

		var ledgerid = $(this).val();
		var rowid = $(this);
		if (ledgerid > 0) {
			$.ajax({
				url: '<?php echo $this->Html->url(array("controller" => "ledgers", "action" => "cl")); ?>',
				data: 'id=' + ledgerid,
				dataType: 'json',
				success: function(data)
				{
					var ledger_bal = parseFloat(data['cl']['amount']);

					var prefix = '';
					var suffix = '';
					if (data['cl']['status'] == 'neg') {
						prefix = '<span class="error-text">';
						suffix = '</span>';
					}

					if (data['cl']['dc'] == 'D') {
						rowid.parent().parent().next().next().next().next().children().html(prefix + "Dr " + ledger_bal + suffix);
					} else if (data['cl']['dc'] == 'C') {
						rowid.parent().parent().next().next().next().next().children().html(prefix + "Cr " + ledger_bal + suffix);
					} else {
						rowid.parent().parent().next().next().next().next().children().html("");
					}
				}
			});
		} else {
			rowid.parent().parent().next().next().next().next().children().text("");
		}
	});

	/* Recalculate Total */
	$(document).on('click', 'table td .recalculate', function() {
		/* Recalculate Total */
		$('.dr-item:first').trigger('change');
		$('.cr-item:first').trigger('change');
	});

	/* Delete ledger row */
	$(document).on('click', '.deleterow', function() {
		$(this).parent().parent().remove();
		/* Recalculate Total */
		$('.dr-item:first').trigger('change');
		$('.cr-item:first').trigger('change');
	});

	/* Add ledger row */
	$(document).on('click', '.addrow', function() {
		var cur_obj = this;
		$.ajax({
			url: '<?php echo $this->Html->url(array("controller" => "entries", "action" => "addrow", $entrytype["Entrytype"]["restriction_bankcash"])); ?>',
			success: function(data) {
				$(cur_obj).parent().parent().after(data);
				/* Trigger ledger item change */
					$(cur_obj).parent().parent().next().children().first().next().children().children().children().trigger('change');

				$("tr.ajax-add .ledger-dropdown").select2({width:'100%'});
			}
		});
	});

	/* On page load initiate all triggers */
	$('.dc-dropdown').trigger('change');
	$('.ledger-dropdown').trigger('change');
	$('.dr-item:first').trigger('change');
	$('.cr-item:first').trigger('change');

	/* Calculate date range in javascript */
	startDate = new Date(<?php echo strtotime(Configure::read('Account.startdate')) * 1000; ?>  + (new Date().getTimezoneOffset() * 60 * 1000));
	endDate = new Date(<?php echo strtotime(Configure::read('Account.enddate')) * 1000; ?>  + (new Date().getTimezoneOffset() * 60 * 1000));

	/* Setup jQuery datepicker ui */
	$('#EntryDate').datepicker({
		minDate: startDate,
		maxDate: endDate,
		dateFormat: '<?php echo Configure::read('Account.dateformatJS'); ?>',
		numberOfMonths: 1,
	});

	$(".ledger-dropdown").select2({width:'100%'});

	/**************** References ***************/

	/* Setup jQuery datepicker ui */
	$('#reference-date').datepicker({
		minDate: startDate,
		maxDate: endDate,
		dateFormat: '<?php echo Configure::read('Account.dateformatJS'); ?>',
		numberOfMonths: 1,
	});

	/* Handle model show */
	$('#referenceModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget) /* Button that triggered the modal */

		/* Set the entryitem row id in the reference-id field */
		var rowID = button.data('id');
		$('#reference-id').val(rowID);
		$('#reference-text').val($('#Entryitem' + rowID + 'ReferenceText').val());
		$('#reference-number').val($('#Entryitem' + rowID + 'ReferenceNumber').val());
		$('#reference-date').val($('#Entryitem' + rowID + 'ReferenceDate').val());
	})

	/* Handle "Submit" button clicked */
	$('#reference-submit').click(function(e) {
		e.preventDefault();

		/* Fetch values */
		var rowID = $('#reference-id').val();
		var referenceText = $('#reference-text').val();
		var referenceNumber = $('#reference-number').val();
		var referenceDate = $('#reference-date').val();

		/* Save the values to hidden parent form fields */
		$('#Entryitem' + rowID + 'ReferenceText').val(referenceText);
		$('#Entryitem' + rowID + 'ReferenceNumber').val(referenceNumber);
		$('#Entryitem' + rowID + 'ReferenceDate').val(referenceDate);

		/* Generate reference string to show to user */
		htmlRefStr = '';
		if (referenceText.length < 1 && referenceNumber.length < 1 && referenceDate.length < 1) {
			htmlRefStr = '';
		} else if (referenceText.length > 1 && referenceNumber.length < 1 && referenceDate.length < 1) {
			htmlRefStr = referenceText;
		} else if (referenceText.length < 1 && referenceNumber.length > 1 && referenceDate.length < 1) {
			htmlRefStr = referenceNumber;
		} else if (referenceText.length < 1 && referenceNumber.length < 1 && referenceDate.length > 1) {
			htmlRefStr = referenceDate;
		} else if (referenceText.length > 1 && referenceNumber.length > 1 && referenceDate.length < 1) {
			htmlRefStr = referenceText + ' / ' + referenceNumber;
		} else if (referenceText.length < 1 && referenceNumber.length > 1 && referenceDate.length > 1) {
			htmlRefStr = referenceNumber + ' / ' + referenceDate;
		} else if (referenceText.length < 1 && referenceNumber.length < 1 && referenceDate.length > 1) {
			htmlRefStr = referenceText + ' / ' + referenceDate;
		} else if (referenceText.length > 1 && referenceNumber.length > 1 && referenceDate.length > 1) {
			htmlRefStr = referenceText + ' / ' + referenceNumber + ' / ' + referenceDate;
		}

		/* Reset the modal form data */
		$('#reference-data-' + rowID).html(htmlRefStr);
		$('#reference-text').val("");
		$('#reference-number').val("");
		$('#reference-date').val("");
		$('#referenceModal').modal('hide');
	});

	/* Handle "Close" button clicked */
	$('#reference-close').click(function(e) {
		/* Reset the modal form data */
		$('#reference-text').val("");
		$('#reference-number').val("");
		$('#reference-date').val("");
	});
});

</script>

<div class="entry add form">
<?php
	if ($this->Session->read('Wzsetting.drcr_toby') == 'toby') {
		$drcr_options = array(
			'D' => 'By',
			'C' => 'To',
		);
	} else {
		$drcr_options = array(
			'D' => 'Dr',
			'C' => 'Cr',
		);
	}

	echo $this->Form->create('Entry', array(
		'inputDefaults' => array(
			'div' => 'form-group',
			'wrapInput' => false,
			'class' => 'form-control',
		),
	));

	$prefixNumber = '';
	$suffixNumber = '';
	if ((Configure::read('Account.ET.' . $entrytype['Entrytype']['id'] . '.prefix') != '') &&
		(Configure::read('Account.ET.' . $entrytype['Entrytype']['id'] . '.suffix') != '')) {
		$prefixNumber = '<div class="input-group"><span class="input-group-addon">' .
			h(Configure::read('Account.ET.' . $entrytype['Entrytype']['id'] . '.prefix')) .
			'</span>';
		$suffixNumber = '<span class="input-group-addon">' .
			h(Configure::read('Account.ET.' . $entrytype['Entrytype']['id'] . '.suffix')) .
			'</span></div>';
	} else if (Configure::read('Account.ET.' . $entrytype['Entrytype']['id'] . '.prefix') != '') {
		$prefixNumber = '<div class="input-group"><span class="input-group-addon">' .
			h(Configure::read('Account.ET.' . $entrytype['Entrytype']['id'] . '.prefix')) .
			'</span>';
		$suffixNumber = '</div>';
	} else if (Configure::read('Account.ET.' . $entrytype['Entrytype']['id'] . '.suffix') != '') {
			$prefixNumber = '<div class="input-group">';
			$suffixNumber = '<span class="input-group-addon">' .
				h(Configure::read('Account.ET.' . $entrytype['Entrytype']['id'] . '.suffix')) .
				'</span></div>';
	}

	echo $this->Form->input('number', array(
		'label' => array('text' => __d('webzash', 'Number')),
		'beforeInput' =>  $prefixNumber,
		'afterInput' => $suffixNumber,
	));

	echo $this->Form->input('date', array('type' => 'text', 'label' => __d('webzash', 'Date')));

	echo '<table class="stripped extra">';

	/* Header */
	echo '<tr>';
	if ($this->Session->read('Wzsetting.drcr_toby') == 'toby') {
		echo '<th>' . __d('webzash', 'To/By') . '</th>';
	} else {
		echo '<th>' . __d('webzash', 'Dr/Cr') . '</th>';
	}
	echo '<th>' . __d('webzash', 'Ledger') . '</th>';
	echo '<th>' . __d('webzash', 'Dr Amount') . ' (' . Configure::read('Account.currency_symbol') . ')' . '</th>';
	echo '<th>' . __d('webzash', 'Cr Amount') . ' (' . Configure::read('Account.currency_symbol') . ')' . '</th>';
	echo '<th>' . __d('webzash', 'Actions') . '</th>';
	echo '<th>' . __d('webzash', 'Cur Balance') . ' (' . Configure::read('Account.currency_symbol') . ')' . '</th>';
	echo '</tr>';

	/* Intial rows */
	foreach ($curEntryitems as $row => $entryitem) {
		echo '<tr>';

		if (empty($entryitem['dc'])) {
			echo '<td>' . $this->Form->input('Entryitem.' . $row . '.dc', array('type' => 'select', 'options' => array('D' => 'Dr', 'C' => 'Cr'), 'class' => 'dc-dropdown form-control', 'label' => false, 'div' => array('class' => 'form-group-entryitem'))) . '</td>';
		} else {
			echo '<td>' . $this->Form->input('Entryitem.' . $row . '.dc', array('type' => 'select', 'options' => $drcr_options, 'default' => $entryitem['dc'], 'class' => 'dc-dropdown form-control', 'label' => false, 'div' => array('class' => 'form-group-entryitem'))) . '</td>';
		}

		if (empty($entryitem['ledger_id'])) {
			echo '<td>' . $this->Form->input('Entryitem.' . $row . '.ledger_id', array('type' => 'select', 'options' => $ledger_options, 'escape' => false, 'disabled' => $ledgers_disabled, 'class' => 'ledger-dropdown form-control', 'label' => false, 'div' => array('class' => 'form-group-entryitem'))) . '<span id="reference-data-' . $row . '"></span></td>';
		} else {
			echo '<td>' . $this->Form->input('Entryitem.' . $row . '.ledger_id', array('type' => 'select', 'options' => $ledger_options, 'default' => $entryitem['ledger_id'], 'escape' => false, 'disabled' => $ledgers_disabled, 'class' => 'ledger-dropdown form-control', 'label' => false, 'div' => array('class' => 'form-group-entryitem'))) . '<span id="reference-data-' . $row . '"></span></td>';
		}

		if (empty($entryitem['dr_amount'])) {
			echo '<td>' . $this->Form->input('Entryitem.' . $row . '.dr_amount', array('label' => false, 'class' => 'dr-item form-control', 'div' => array('class' => 'form-group-entryitem'))) . '</td>';
		} else {
			echo '<td>' . $this->Form->input('Entryitem.' . $row . '.dr_amount', array('default' => $entryitem['dr_amount'], 'label' => false, 'class' => 'dr-item form-control', 'div' => array('class' => 'form-group-entryitem'))) . '</td>';
		}

		if (empty($entryitem['cr_amount'])) {
			echo '<td>' . $this->Form->input('Entryitem.' . $row . '.cr_amount', array('label' => false, 'class' => 'cr-item form-control', 'div' => array('class' => 'form-group-entryitem'))) . '</td>';
		} else {
			echo '<td>' . $this->Form->input('Entryitem.' . $row . '.cr_amount', array('default' => $entryitem['cr_amount'], 'label' => false, 'class' => 'cr-item form-control', 'div' => array('class' => 'form-group-entryitem'))) . '</td>';
		}

		echo '<td>';
		echo $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'glyphicon glyphicon-plus')) . __d('webzash', ' Add'), array('class' => 'addrow', 'escape' => false));
		echo $this->Html->tag('span', '', array('class' => 'link-pad'));
		echo $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'glyphicon glyphicon-trash')) . __d('webzash', ' Delete'), array('class' => 'deleterow', 'escape' => false));
		echo $this->Html->tag('span', '', array('class' => 'link-pad'));
		echo $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'glyphicon glyphicon-flash')) . __d('webzash', ' Reference'), array('class' => 'referencerow', 'escape' => false, 'data-id' => $row, 'data-toggle' => 'modal', 'data-target' => '#referenceModal'));
		/* Hidden elements */
		echo $this->Form->input('Entryitem.' . $row . '.reference_text', array('type' => 'hidden', 'class' => 'ref-text'));
		echo $this->Form->input('Entryitem.' . $row . '.reference_number', array('type' => 'hidden', 'class' => 'ref-number'));
		echo $this->Form->input('Entryitem.' . $row . '.reference_date', array('type' => 'hidden', 'class' => 'ref-date'));
		echo '</td>';

		echo '<td class="ledger-balance"><div></div></td>';
		echo '</tr>';
	}

	/* Total and difference */
	echo '<tr class="bold-text">' . '<td>' . __d('webzash', 'Total') . '</td>' . '<td>' . '</td>' . '<td id="dr-total">' . '</td>' . '<td id="cr-total">' . '</td>' . '<td >' . $this->Html->tag('span', $this->Html->tag('i', '', array('class' => 'glyphicon glyphicon-refresh')), array('class' => 'recalculate', 'escape' => false)) . '</td>' . '<td>' . '</td>' . '</tr>';
	echo '<tr class="bold-text">' . '<td>' . __d('webzash', 'Difference') . '</td>' . '<td>' . '</td>' . '<td id="dr-diff">' . '</td>' . '<td id="cr-diff">' . '</td>' . '<td>' . '</td>' . '<td>' . '</td>' . '</tr>';

	echo '</table>';

	echo '<br />';

	echo $this->Form->input('narration', array('type' => 'textarea', 'label' => __d('webzash', 'Narration'), 'rows' => '3'));
	echo $this->Form->input('tag_id', array('type' => 'select', 'options' => $tag_options, 'label' => __d('webzash', 'Tag')));

	echo '<div class="form-group">';
	echo $this->Form->submit(__d('webzash', 'Submit'), array(
		'div' => false,
		'class' => 'btn btn-primary'
	));
	echo $this->Html->tag('span', '', array('class' => 'link-pad'));
	echo $this->Html->link(__d('webzash', 'Cancel'), array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'), array('class' => 'btn btn-default'));
	echo '</div>';

	echo $this->Form->end();
?>

	<!-- Reference Modal -->
	<div class="modal fade" id="referenceModal" tabindex="-1" role="dialog" aria-labelledby="References">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Manage References</h4>
				</div>
				<div class="modal-body">
					<form id="reference-form">
						Text<br />
						<input type="text" id="reference-text" name="reference-text" value="" class="form-control" /><br />
						Number<br />
						<input type="text" id="reference-number" name="reference-number" value="" class="form-control" /><br />
						Date<br />
						<input type="text" id="reference-date" name="reference-date" value="" class="form-control" /><br />
						<input type="hidden" id="reference-id" name="reference-id" value="" /><br />
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" id="reference-close" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" id="reference-submit">Save changes</button>
				</div>
			</div>
		</div>
	</div>

</div>
