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

function account_st_short($account, $c = 0, $THIS, $dc_type)
{
	$counter = $c;
	if ($account->id > 4)
	{
		echo '"';
		echo print_space($counter);
		echo h(toCodeWithName($account->code, $account->name));
		echo '",';

		echo '"' . toCurrency($account->cl_total_dc, $account->cl_total) . '"';
		echo "\n";
	}
	foreach ($account->children_groups as $id => $data)
	{
		$counter++;
		account_st_short($data, $counter, $THIS, $dc_type);
		$counter--;
	}
	if (count($account->children_ledgers) > 0)
	{
		$counter++;
		foreach ($account->children_ledgers as $id => $data)
		{
			echo '"';
			echo print_space($counter);
			echo h(toCodeWithName($data['code'], $data['name']));
			echo '",';

			echo '"' . toCurrency($data['cl_total_dc'], $data['cl_total']) . '"';
			echo "\n";
		}
	$counter--;
	}
}

function print_space($count)
{
	$html = '';
	for ($i = 1; $i <= $count; $i++) {
		$html .= '      ';
	}
	return $html;
}

?>

<?php
	/* Show difference in opening balance */
	if ($bsheet['is_opdiff']) {
		echo '"' .
			__d('webzash', 'There is a difference in opening balance of ') .
			toCurrency($bsheet['opdiff']['opdiff_balance_dc'], $bsheet['opdiff']['opdiff_balance']) .
			'"' .
			"\n";
			"\n";
	}

	/* Show difference in liabilities and assets total */
	if (calculate($bsheet['final_liabilities_total'], $bsheet['final_assets_total'], '!=')) {
		$final_total_diff = calculate($bsheet['final_liabilities_total'], $bsheet['final_assets_total'], '-');
		echo '"' .
			__d('webzash', 'There is a difference in Total Liabilities and Total Assets of ') .
			toCurrency('X', $final_total_diff) .
			'"' .
			"\n";
			"\n";
	}

	echo $subtitle;
	echo "\n";
	echo "\n";

	/**************** Assets ****************/
	echo '"' . __d('webzash', 'Assets (Dr)') . '",';
	echo '"' . __d('webzash', 'Amount') . ' (' . Configure::read('Account.currency_symbol') . ')' . '"';
	echo "\n";
	echo account_st_short($bsheet['assets'], $c = -1, $this, 'D');
	echo "\n";

	/* Assets Total */
	echo '"' . __d('webzash', 'Total Assets') . '",';
	echo '"' . toCurrency('D', $bsheet['assets_total']) . '"';
	echo "\n";

	/* Net loss */
	if (calculate($bsheet['pandl'], 0, '>=')) {
		/* Do nothing */
	} else {
		echo '"' . __d('webzash', 'Profit & Loss Account (Net Loss)') . '",';
		$positive_pandl = calculate($bsheet['pandl'], 0, 'n');
		echo '"' . toCurrency('D', $positive_pandl) . '"';
		echo "\n";
	}

	if ($bsheet['is_opdiff']) {
		/* If diff in opening balance is Dr */
		if ($bsheet['opdiff']['opdiff_balance_dc'] == 'D') {
			echo '"' . __d('webzash', 'Diff in O/P Balance') . '",';
			echo '"' . toCurrency('D', $bsheet['opdiff']['opdiff_balance']) . '"';
			echo "\n";
		}
	}

	/* Total */
	echo '"' . __d('webzash', 'Total') . '",';
	echo '"' . toCurrency('D', $bsheet['final_assets_total']) . '"';
	echo "\n";
	echo "\n";

	/**************** Liabilities ****************/
	echo '"' . __d('webzash', 'Liabilities and Owners Equity (Cr)') . '",';
	echo '"' . __d('webzash', 'Amount') . ' (' . Configure::read('Account.currency_symbol') . ')' . '"';
	echo "\n";
	echo account_st_short($bsheet['liabilities'], $c = -1, $this, 'C');
	echo "\n";

	/* Liabilities Total */
	echo '"' . __d('webzash', 'Total Liability and Owners Equity') . '",';
	echo '"' . toCurrency('C', $bsheet['liabilities_total']) . '"';
	echo "\n";

	/* Net profit */
	if (calculate($bsheet['pandl'], 0, '>=')) {
		echo '"' . __d('webzash', 'Profit & Loss Account (Net Profit)') . '",';
		echo '"' . toCurrency('C', $bsheet['pandl']) . '"';
		echo "\n";
	}

	if ($bsheet['is_opdiff']) {
		/* If diff in opening balance is Cr */
		if ($bsheet['opdiff']['opdiff_balance_dc'] == 'C') {
			echo '"' . __d('webzash', 'Diff in O/P Balance') . '",';
			echo '"' . toCurrency('C', $bsheet['opdiff']['opdiff_balance']) . '"';
			echo "\n";
		}
	}

	/* Total */
	echo '"' . __d('webzash', 'Total') . '",';
	echo '"' . toCurrency('C', $bsheet['final_liabilities_total']) .	'"';
	echo "\n";
	echo "\n";
