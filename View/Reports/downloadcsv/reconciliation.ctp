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
	echo '"' . __d('webzash', 'Debit ') . $recpending_title . '",';
	echo '"' . toCurrency('D', $rp['dr_total']) . '"';
	echo "\n";
	echo '"' . __d('webzash', 'Credit ') . $recpending_title . '",';
	echo '"' . toCurrency('C', $rp['cr_total']) . '"';
	echo "\n";
	echo "\n";

	echo '"' . __d('webzash', 'Date') . '",';
	echo '"' . __d('webzash', 'Number') . '",';
	echo '"' . __d('webzash', 'Ledger') . '",';
	echo '"' . __d('webzash', 'Type') . '",';
	echo '"' . __d('webzash', 'Debit Amount') . ' (' . Configure::read('Account.currency_symbol') . ')' . '",';
	echo '"' . __d('webzash', 'Credit Amount') . ' (' . Configure::read('Account.currency_symbol') . ')' . '",';
	echo '"' . __d('webzash', 'Reconciliation Date') . '"';
	echo "\n";

	/* Show the entries table */
	foreach ($entries as $row => $entry) {
		$entryTypeName = Configure::read('Account.ET.' . $entry['Entry']['entrytype_id'] . '.name');
		echo '"' . dateFromSql($entry['Entry']['date']) . '",';
		echo '"' . h(toEntryNumber($entry['Entry']['number'], $entry['Entry']['entrytype_id'])) . '",';
		echo '"' . h($this->Generic->entryLedgers($entry['Entry']['id'])) . '",';
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

		if ($entry['Entryitem']['reconciliation_date']) {
			echo '"' . dateFromSql($entry['Entryitem']['reconciliation_date']) . '"';
		} else {
			echo '""';
		}
		echo "\n";
	}
