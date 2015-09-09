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
	$xRS = '<Row>';
	$xRE = '</Row>' . "\n";
	$xCS = '<Cell><Data ss:Type="String">';
	$xCE = '</Data></Cell>';

	echo $xRS . $xCS . $subtitle . $xCE . $xRE;
	echo $xRS . $xRE;

	echo $xRS;
	echo $xCS . $opening_title . $xCE;
	echo $xCS . toCurrency($op['dc'], $op['amount']) . $xCE;
	echo $xRE;

	echo $xRS;
	echo $xCS . $closing_title . $xCE;
	echo $xCS . toCurrency($cl['dc'], $cl['amount']) . $xCE;
	echo $xRE;
	echo $xRS . $xRE;

	echo $xRS;
	echo $xCS . __d('webzash', 'Date') . $xCE;
	echo $xCS . __d('webzash', 'Number') . $xCE;
	echo $xCS . __d('webzash', 'Ledger') . $xCE;
	echo $xCS . __d('webzash', 'Type') . $xCE;
	echo $xCS . __d('webzash', 'Debit Amount') . ' (' . Configure::read('Account.currency_symbol') . ')' . $xCE;
	echo $xCS . __d('webzash', 'Credit Amount') . ' (' . Configure::read('Account.currency_symbol') . ')' . $xCE;
	echo $xCS . __d('webzash', 'Balance') . ' (' . Configure::read('Account.currency_symbol') . ')' . $xCE;
	echo $xRE;

	/* Current opening balance */
	echo $xRS;
	$entry_balance['amount'] = $current_op['amount'];
	$entry_balance['dc'] = $current_op['dc'];
	echo $xCS . $xCE . $xCS . $xCE;
	echo $xCS . __d('webzash', 'Current opening balance') . $xCE;
	echo $xCS . $xCE . $xCS . $xCE . $xCS . $xCE;
	echo $xCS . toCurrency($current_op['dc'], $current_op['amount']) . $xCE;
	echo $xRE;

	/* Show the entries table */
	foreach ($entries as $entry) {
		$entryTypeName = Configure::read('Account.ET.' . $entry['Entry']['entrytype_id'] . '.name');
		$entryTypeLabel = Configure::read('Account.ET.' . $entry['Entry']['entrytype_id'] . '.label');

		echo $xRS;
		echo $xCS . dateFromSql($entry['Entry']['date']) . $xCE;
		echo $xCS . h(toEntryNumber($entry['Entry']['number'], $entry['Entry']['entrytype_id'])) . $xCE;
		echo $xCS . h($this->Generic->entryLedgers($entry['Entry']['id'])) . $xCE;
		echo $xCS . h($entryTypeName) . $xCE;

		if ($entry['Entryitem']['dc'] == 'D') {
			echo $xCS . toCurrency('D', $entry['Entryitem']['amount']) . $xCE;
			echo $xCS . $xCE;
		} else if ($entry['Entryitem']['dc'] == 'C') {
			echo $xCS . $xCE;
			echo $xCS . toCurrency('C', $entry['Entryitem']['amount']) . $xCE;
		} else {
			echo $xCS . __d('webzash', 'ERROR') . $xCE;
			echo $xCS . __d('webzash', 'ERROR') . $xCE;
		}

		/* Calculate current entry balance */
		$entry_balance = calculate_withdc(
			$entry_balance['amount'], $entry_balance['dc'],
			$entry['Entryitem']['amount'], $entry['Entryitem']['dc']
		);
		echo $xCS . toCurrency($entry_balance['dc'], $entry_balance['amount']) . $xCE;

		echo $xRE;
	}

	/* Current closing balance */
	echo $xRS;
	echo $xCS . $xCE . $xCS . $xCE;
	echo $xCS . __d('webzash', 'Current closing balance') . $xCE;
	echo $xCS . $xCE . $xCS . $xCE . $xCS . $xCE;
	echo $xCS . toCurrency($entry_balance['dc'], $entry_balance['amount']) . $xCE;
	echo $xRE;
