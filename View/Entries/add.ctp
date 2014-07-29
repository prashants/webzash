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
		param1 = param1 * 100;
		param2 = param2 * 100;
		param1 = param1.toFixed(0);
		param2 = param2.toFixed(0);
		param1 = Math.floor(param1);
		param2 = Math.floor(param2);
		var result = 0;
		if (op == '+') {
			result = param1 + param2;
			result = result/100;
			return result;
		}
		if (op == '-') {
			result = param1 - param2;
			result = result/100;
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
					var ledger_bal = parseFloat(data['cl']['balance']);
					if (data['cl']['dc'] == 'D') {
						rowid.parent().parent().next().next().next().next().next().children().text("Dr " + ledger_bal);
					} else if (data['cl']['dc'] == 'C') {
						rowid.parent().parent().next().next().next().next().next().children().text("Cr " + ledger_bal);
					} else {
						rowid.parent().parent().next().next().next().next().next().children().text("");
					}
				}
			});
		} else {
			rowid.parent().parent().next().next().next().next().next().children().text("");
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
		var add_image_url = $(cur_obj).attr('src');
		$(cur_obj).attr('src', '<?php echo $this->Html->assetUrl("Webzash.ajax.gif", array("fullBase" => true, "pathPrefix" => IMAGES_URL)); ?>');
		$.ajax({
			url: '<?php echo $this->Html->url(array("controller" => "entries", "action" => "addrow", $this->Generic->ajaxAddLedger($entrytype["Entrytype"]["restriction_bankcash"]))); ?>',
			success: function(data) {
				$(cur_obj).parent().parent().after(data);
				$(cur_obj).attr('src', add_image_url);
				/* Trigger ledger item change */
				$(cur_obj).parent().parent().next().children().first().next().children().children().trigger('change');
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
});

</script>

<div class="entry add form">
<?php
	echo $this->Form->create('Entry');
	echo $this->Form->input('number', array('label' => __d('webzash', 'Number')));
	echo $this->Form->input('date', array('type' => 'text', 'label' => __d('webzash', 'Date')));

	echo '<table>';

	/* Header */
	echo '<tr>';
	echo '<th>' . __d('webzash', 'Dr/Cr') . '</th>';
	echo '<th>' . __d('webzash', 'Ledger') . '</th>';
	echo '<th>' . __d('webzash', 'Dr Amount') . '</th>';
	echo '<th>' . __d('webzash', 'Cr Amount') . '</th>';
	echo '<th><div id="addinit"></div></th>';
	echo '<th></th>';
	echo '<th>' . __d('webzash', 'Cur Balance') . '</th>';
	echo '</tr>';

	/* Intial rows */
	foreach ($curEntryitems as $row => $entryitem) {
		echo '<tr>';

		if (empty($entryitem['dc'])) {
			echo '<td>' . $this->Form->input('Entryitem.' . $row . '.dc', array('type' => 'select', 'type' => 'select', 'options' => array('D' => 'Dr', 'C' => 'Cr'), 'class' => 'dc-dropdown', 'label' => false)) . '</td>';
		} else {
			echo '<td>' . $this->Form->input('Entryitem.' . $row . '.dc', array('type' => 'select', 'type' => 'select', 'options' => array('D' => 'Dr', 'C' => 'Cr'), 'default' => $entryitem['dc'], 'class' => 'dc-dropdown', 'label' => false)) . '</td>';
		}

		if (empty($entryitem['ledger_id'])) {
			echo '<td>' . $this->Form->input('Entryitem.' . $row . '.ledger_id', array('type' => 'select', 'options' => $this->Generic->ledgerList($entrytype['Entrytype']['restriction_bankcash']), 'class' => 'ledger-dropdown', 'label' => false)) . '</td>';
		} else {
			echo '<td>' . $this->Form->input('Entryitem.' . $row . '.ledger_id', array('type' => 'select', 'options' => $this->Generic->ledgerList($entrytype['Entrytype']['restriction_bankcash']), 'default' => $entryitem['ledger_id'], 'class' => 'ledger-dropdown', 'label' => false)) . '</td>';
		}

		if (empty($entryitem['dr_amount'])) {
			echo '<td>' . $this->Form->input('Entryitem.' . $row . '.dr_amount', array('label' => false, 'class' => 'dr-item')) . '</td>';
		} else {
			echo '<td>' . $this->Form->input('Entryitem.' . $row . '.dr_amount', array('default' => $entryitem['dr_amount'], 'label' => false, 'class' => 'dr-item')) . '</td>';
		}

		if (empty($entryitem['cr_amount'])) {
			echo '<td>' . $this->Form->input('Entryitem.' . $row . '.cr_amount', array('label' => false, 'class' => 'cr-item')) . '</td>';
		} else {
			echo '<td>' . $this->Form->input('Entryitem.' . $row . '.cr_amount', array('default' => $entryitem['cr_amount'], 'label' => false, 'class' => 'cr-item')) . '</td>';
		}

		echo '<td>' . $this->Html->image('Webzash.add.png', array('alt' => 'Add row', 'class' => 'addrow')) . '</td>';
		echo '<td>' . $this->Html->image('Webzash.delete.png', array('alt' => 'Delete row', 'class' => 'deleterow')) . '</td>';
		echo '<td class="ledger-balance"><div></div></td>';
		echo '</tr>';
	}

	/* Total and difference */
	echo '<tr>' . '<td>' . __d('webzash', 'Total') . '</td>' . '<td>' . '</td>' . '<td id="dr-total">' . '</td>' . '<td id="cr-total">' . '</td>' . '<td >' . $this->Html->image('Webzash.gear.png', array('alt' => 'Recalculate', 'class' => 'recalculate')) . '</td>' . '<td>' . '</td>' . '<td>' . '</td>' . '</tr>';
	echo '<tr>' . '<td>' . __d('webzash', 'Difference') . '</td>' . '<td>' . '</td>' . '<td id="dr-diff">' . '</td>' . '<td id="cr-diff">' . '</td>' . '<td>' . '</td>' . '<td>' . '</td>' . '<td>' . '</td>' . '</tr>';

	echo '</table>';

	echo $this->Form->input('narration', array('type' => 'textarea', 'label' => __d('webzash', 'Narration'), 'rows' => '3'));
	echo $this->Form->input('tag_id', array('type' => 'select', 'options' => $this->Generic->tagList(), 'label' => __d('webzash', 'Tag')));
	echo $this->Form->end(__d('webzash', 'Submit'));
	echo $this->Html->link(__d('webzash', 'Back'), array('controller' => 'entries', 'action' => 'index'));
?>
</div>
