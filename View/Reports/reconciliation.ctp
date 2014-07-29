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

	/* Calculate date range in javascript */
	startDate = new Date(<?php echo strtotime(Configure::read('Account.startdate')) * 1000; ?>  + (new Date().getTimezoneOffset() * 60 * 1000));
	endDate = new Date(<?php echo strtotime(Configure::read('Account.enddate')) * 1000; ?>  + (new Date().getTimezoneOffset() * 60 * 1000));

	/* On selecting custom period show the start and end date form fields */
	$('#ReportCustomPeriod').change(function() {
		if ($(this).prop('checked')) {
			$('#ledgerst-period').show();
		} else {
			$('#ledgerst-period').hide();
		}
	});
	$('#ReportCustomPeriod').trigger('change');

	/* Setup jQuery datepicker ui */
	$('#ReportStartdate').datepicker({
		minDate: startDate,
		maxDate: endDate,
		dateFormat: '<?php echo Configure::read('Account.dateformatJS'); ?>',
		numberOfMonths: 1,
		onClose: function(selectedDate) {
			if (selectedDate) {
				$("#ReportEnddate").datepicker("option", "minDate", selectedDate);
			} else {
				$("#ReportEnddate").datepicker("option", "minDate", startDate);
			}
		}
	});
	$('#ReportEnddate').datepicker({
		minDate: startDate,
		maxDate: endDate,
		dateFormat: '<?php echo Configure::read('Account.dateformatJS'); ?>',
		numberOfMonths: 1,
		onClose: function(selectedDate) {
			if (selectedDate) {
				$("#ReportStartdate").datepicker("option", "maxDate", selectedDate);
			} else {
				$("#ReportStartdate").datepicker("option", "maxDate", endDate);
			}
		}
	});

	$('.recdate').datepicker({
		minDate: startDate,
		maxDate: endDate,
		dateFormat: '<?php echo Configure::read('Account.dateformatJS'); ?>',
		numberOfMonths: 1,
	});
});
</script>

<div class="reconciliation form">
	<?php
		echo $this->Form->create('Report');
		echo $this->Form->input('ledger_id', array('type' => 'select', 'options' => $ledgers, 'label' => __d('webzash', 'Ledger account')));
		echo $this->Form->input('showall', array('type' => 'checkbox', 'label' => __d('webzash', 'Show all entries')));

		echo $this->Form->input('custom_period', array('type' => 'checkbox', 'label' => __d('webzash', 'Change default period')));
		echo '<fieldset id="ledgerst-period">';
		echo $this->Form->input('startdate', array('label' => __d('webzash', 'Start date')));
		echo $this->Form->input('enddate', array('label' => __d('webzash', 'End date')));
		echo '</fieldset>';

		echo $this->Form->hidden('submitledger', array('value' => '1'));
		echo $this->Form->end(__d('webzash', 'Show'));
	?>
</div>

<?php if ($showEntries) { ?>

<div class="reconciliation form">

	<?php echo $this->Form->create('ReportRec'); ?>

	<table>

	<tr>
	<th><?php echo $this->Paginator->sort('date', __d('webzash', 'Date')); ?></th>
	<th><?php echo $this->Paginator->sort('number', __d('webzash', 'Number')); ?></th>
	<th><?php echo __d('webzash', 'Ledger'); ?></th>
	<th><?php echo $this->Paginator->sort('entrytype_id', __d('webzash', 'Type')); ?></th>
	<th><?php echo $this->Paginator->sort('tag_id', __d('webzash', 'Tag')); ?></th>
	<th><?php echo $this->Paginator->sort('dr_total', __d('webzash', 'Debit Amount')); ?></th>
	<th><?php echo $this->Paginator->sort('cr_total', __d('webzash', 'Credit Amount')); ?></th>
	<th><?php echo $this->Paginator->sort('cr_total', __d('webzash', 'Reconciliation Date')); ?></th>
	</tr>

	<?php
	/* Show the entries table */
	foreach ($entries as $row => $entry) {
		list($entryTypeName, $entryTypeLabel) = $this->Generic->showEntrytype($entry['Entry']['entrytype_id']);
		echo '<tr>';
		echo '<td>' . dateFromSql($entry['Entry']['date']) . '</td>';
		echo '<td>' . $entry['Entry']['number']. '</td>';
		echo '<td>' . '</td>';
		echo '<td>' . $entryTypeName . '</td>';
		echo '<td>' . $this->Generic->showTag($entry['Entry']['tag_id']) . '</td>';

		if ($entry['Entryitem']['dc'] == 'D') {
			echo '<td>Dr ' . $entry['Entryitem']['amount'] . '</td>';
			echo '<td>' . '</td>';
		} else if ($entry['Entryitem']['dc'] == 'C') {
			echo '<td>' . '</td>';
			echo '<td>Cr ' . $entry['Entryitem']['amount'] . '</td>';
		} else {
			echo '<td>Error</td>';
			echo '<td>Error</td>';
		}

		echo '<td>';
		echo $this->Form->hidden('ReportRec.' . $row . '.id', array('value' => $entry['Entryitem']['id']));
		echo $this->Form->input('ReportRec.' . $row . '.recdate', array('label' => false, 'class' => 'recdate',
			'default' => dateFromSql($entry['Entryitem']['reconciliation_date']),
		));
		echo '</td>';
		echo '</tr>';
	}
	?>
	</table>

	<?php echo $this->Form->hidden('submitrec', array('value' => '1')); ?>
	<?php echo $this->Form->end(__d('webzash', 'Update')); ?>

	<?php
		/* Pagination */
		echo "<div class='paging'>";
		echo $this->Paginator->first(__d('webzash', 'First'));
		if ($this->Paginator->hasPrev()) {
			echo $this->Paginator->prev(__d('webzash', 'Prev'));
		}
		echo $this->Paginator->numbers();
		if ($this->Paginator->hasNext()){
			echo $this->Paginator->next(__d('webzash', 'Next'));
		}
		echo $this->Paginator->last(__d('webzash', 'Last'));
		echo ' ' . __d('webzash', 'Entries') . ' ' . $this->Paginator->counter();
		echo "</div>";

	echo "</div>";
}
