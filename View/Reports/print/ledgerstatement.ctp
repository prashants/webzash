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

<?php if ($showEntries) { ?>

<div class="subtitle">
	<?php echo $subtitle; ?>
</div>

	<table class="summary stripped table-condensed">
		<tr>
			<td class="td-fixwidth-summary"><?php echo $opening_title; ?></td>
			<td><?php echo toCurrency($op['dc'], $op['amount']); ?></td>
		</tr>
		<tr>
			<td class="td-fixwidth-summary"><?php echo $closing_title; ?></td>
			<td><?php echo toCurrency($cl['dc'], $cl['amount']); ?></td>
		</tr>
	</table>
	<br />

	<table class="stripped">

	<tr>
	<th><?php echo __d('webzash', 'Date'); ?></th>
	<th><?php echo __d('webzash', 'Number'); ?></th>
	<th><?php echo __d('webzash', 'Ledger'); ?></th>
	<th><?php echo __d('webzash', 'Type'); ?></th>
	<th><?php echo __d('webzash', 'Tag'); ?></th>
	<th><?php echo __d('webzash', 'Debit Amount'); ?><?php echo ' (' . Configure::read('Account.currency_symbol') . ')'; ?></th>
	<th><?php echo __d('webzash', 'Credit Amount'); ?><?php echo ' (' . Configure::read('Account.currency_symbol') . ')'; ?></th>
	<th><?php echo __d('webzash', 'Balance'); ?><?php echo ' (' . Configure::read('Account.currency_symbol') . ')'; ?></th>
	</tr>

	<?php
		/* Current opening balance */
		$entry_balance['amount'] = $current_op['amount'];
		$entry_balance['dc'] = $current_op['dc'];
		echo '<tr class="tr-highlight">';
		echo '<td colspan="7">';
		echo __d('webzash', 'Current opening balance');
		echo '</td>';
		echo '<td>' . toCurrency($current_op['dc'], $current_op['amount']) . '</td>';
		echo '</tr>';
	?>

	<?php
	/* Show the entries table */
	foreach ($entries as $entry) {
		/* Calculate current entry balance */
		$entry_balance = calculate_withdc(
			$entry_balance['amount'], $entry_balance['dc'],
			$entry['Entryitem']['amount'], $entry['Entryitem']['dc']
		);

		$entryTypeName = Configure::read('Account.ET.' . $entry['Entry']['entrytype_id'] . '.name');
		$entryTypeLabel = Configure::read('Account.ET.' . $entry['Entry']['entrytype_id'] . '.label');

		/* Negative balance if its a cash or bank account and balance is Cr */
		if ($ledger['Ledger']['type'] == 1) {
			if ($entry_balance['dc'] == 'C' && $entry_balance['amount'] != '0.00') {
				echo '<tr class="error-text">';
			} else {
				echo '<tr>';
			}
		} else {
			echo '<tr>';
		}

		echo '<td>' . dateFromSql($entry['Entry']['date']) . '</td>';
		echo '<td>' . h(toEntryNumber($entry['Entry']['number'], $entry['Entry']['entrytype_id'])) . '</td>';
		echo '<td>' . h($this->Generic->entryLedgers($entry['Entry']['id'])) . '</td>';
		echo '<td>' . h($entryTypeName) . '</td>';
		echo '<td>' . $this->Generic->showTag($entry['Entry']['tag_id'])  . '</td>';

		if ($entry['Entryitem']['dc'] == 'D') {
			echo '<td>' . toCurrency('D', $entry['Entryitem']['amount']) . '</td>';
			echo '<td>' . '</td>';
		} else if ($entry['Entryitem']['dc'] == 'C') {
			echo '<td>' . '</td>';
			echo '<td>' . toCurrency('C', $entry['Entryitem']['amount']) . '</td>';
		} else {
			echo '<td>' . __d('webzash', 'ERROR') . '</td>';
			echo '<td>' . __d('webzash', 'ERROR') . '</td>';
		}

		echo '<td>' . toCurrency($entry_balance['dc'], $entry_balance['amount']) . '</td>';
		echo '</tr>';
	}
	?>

	<?php
		/* Current closing balance */
		echo '<tr class="tr-highlight">';
		echo '<td colspan="7">';
		echo __d('webzash', 'Current closing balance');
		echo '</td>';
		echo '<td>' . toCurrency($entry_balance['dc'], $entry_balance['amount']) . '</td>';
		echo '</tr>';
	?>

	</table>
<?php }
