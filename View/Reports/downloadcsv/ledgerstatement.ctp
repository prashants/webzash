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

<?php
	echo $subtitle;
	echo "\n";
	echo "\n";

	echo '"' . $opening_title . '",';
	echo '"' . toCurrency($op['dc'], $op['amount']) . '"';
	echo "\n";
	echo '"' . $closing_title . '",';
	echo '"' . toCurrency($cl['dc'], $cl['amount']) . '"';
	echo "\n";
	echo "\n";

	echo '"' . __d('webzash', 'Date') . '",';
	echo '"' . __d('webzash', 'Number') . '",';
	echo '"' . __d('webzash', 'Ledger') . '",';
	echo '"' . __d('webzash', 'Type') . '",';
	echo '"' . __d('webzash', 'Debit Amount') . ' (' . Configure::read('Account.currency_symbol') . ')' . '",';
	echo '"' . __d('webzash', 'Credit Amount') . ' (' . Configure::read('Account.currency_symbol') . ')' . '",';
	echo '"' . __d('webzash', 'Balance') . ' (' . Configure::read('Account.currency_symbol') . ')' . '"';
	echo "\n";

	/* Current opening balance */
	$entry_balance['amount'] = $current_op['amount'];
	$entry_balance['dc'] = $current_op['dc'];
	echo '"", "",';
	echo '"' . __d('webzash', 'Current opening balance') . '",';
	echo '"","","",';
	echo '"' . toCurrency($current_op['dc'], $current_op['amount']) . '"';
	echo "\n";

	/* Show the entries table */
	foreach ($entries as $entry) {
		$entryTypeName = Configure::read('Account.ET.' . $entry['Entry']['entrytype_id'] . '.name');
		$entryTypeLabel = Configure::read('Account.ET.' . $entry['Entry']['entrytype_id'] . '.label');

		echo '"' . dateFromSql($entry['Entry']['date']) . '",';
		echo '"' . h(toEntryNumber($entry['Entry']['number'], $entry['Entry']['entrytype_id'])) . '",';
		echo '"' . h($this->Generic->entryLedgersReport($entry['Entry']['id'])) . '",';
		echo '"' . h($entryTypeName) . '",';

		if ($entry['Entryitem']['dc'] == 'D') {
			echo '"' . toCurrency('D', $entry['Entryitem']['amount']) . '",';
			echo '"",';
		} else if ($entry['Entryitem']['dc'] == 'C') {
			echo '"",';
			echo '"' . toCurrency('C', $entry['Entryitem']['amount']) . '",';
		} else {
			echo '"' . __d('webzash', 'ERROR') . '",';
			echo '"' . __d('webzash', 'ERROR') . '",';
		}

		/* Calculate current entry balance */
		$entry_balance = calculate_withdc(
			$entry_balance['amount'], $entry_balance['dc'],
			$entry['Entryitem']['amount'], $entry['Entryitem']['dc']
		);
		echo '"' . toCurrency($entry_balance['dc'], $entry_balance['amount']) . '"';

		echo "\n";
	}

	/* Current closing balance */
	echo '"", "",';
	echo '"' . __d('webzash', 'Current closing balance') . '",';
	echo '"","","",';
	echo '"' . toCurrency($entry_balance['dc'], $entry_balance['amount']) . '"';
	echo "\n";
