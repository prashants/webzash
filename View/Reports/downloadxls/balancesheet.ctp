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

$xRS = '<Row>';
$xRE = '</Row>' . "\n";
$xCS = '<Cell><Data ss:Type="String">';
$xCE = '</Data></Cell>';

function account_st_short($account, $c = 0, $THIS, $dc_type)
{
	$xRS = '<Row>';
	$xRE = '</Row>';
	$xCS = '<Cell><Data ss:Type="String">';
	$xCE = '</Data></Cell>';

	$counter = $c;
	if ($account->id > 4)
	{
		echo $xRS;
		echo $xCS;
		echo print_space($counter);
		echo h(toCodeWithName($account->code, $account->name));
		echo $xCE;

		echo $xCS . toCurrency($account->cl_total_dc, $account->cl_total) . $xCE;
		echo $xRE;
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
			echo $xRS;
			echo $xCS;
			echo print_space($counter);
			echo h(toCodeWithName($data['code'], $data['name']));
			echo $xCE;

			echo $xCS . toCurrency($data['cl_total_dc'], $data['cl_total']) . $xCE;
			echo $xRE;
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
		echo $xRS;
		echo $xCS . __d('webzash', 'There is a difference in opening balance of ') .
			toCurrency($bsheet['opdiff']['opdiff_balance_dc'], $bsheet['opdiff']['opdiff_balance']) .
			$xCE;
		echo $xRE;
	}

	/* Show difference in liabilities and assets total */
	if (calculate($bsheet['final_liabilities_total'], $bsheet['final_assets_total'], '!=')) {
		$final_total_diff = calculate($bsheet['final_liabilities_total'], $bsheet['final_assets_total'], '-');
		echo $xRS;
		echo $xCS . __d('webzash', 'There is a difference in Total Liabilities and Total Assets of ') .
			toCurrency('X', $final_total_diff) .
			$xCE;
		echo $xRE;
	}

	echo $xRS . $xCS . $subtitle . $xCE . $xRE;
	echo $xRS . $xRE;

	/**************** Assets ****************/
	echo $xRS;
	echo $xCS . __d('webzash', 'Assets (Dr)') . $xCE;
	echo $xCS . __d('webzash', 'Amount') . ' (' . Configure::read('Account.currency_symbol') . ')' . $xCE;
	echo $xRE;
	echo account_st_short($bsheet['assets'], $c = -1, $this, 'D');
	echo $xRS . $xRE;

	/* Assets Total */
	echo $xRS;
	echo $xCS . __d('webzash', 'Total Assets') . $xCE;
	echo $xCS . toCurrency('D', $bsheet['assets_total']) . $xCE;
	echo $xRE;

	/* Net loss */
	if (calculate($bsheet['pandl'], 0, '>=')) {
		/* Do nothing */
	} else {
		echo $xRS;
		echo $xCS . __d('webzash', 'Profit & Loss Account (Net Loss)') . $xCE;
		$positive_pandl = calculate($bsheet['pandl'], 0, 'n');
		echo $xCS . toCurrency('D', $positive_pandl) . $xCE;
		echo $xRE;
	}

	if ($bsheet['is_opdiff']) {
		/* If diff in opening balance is Dr */
		if ($bsheet['opdiff']['opdiff_balance_dc'] == 'D') {
			echo $xRS;
			echo $xCS . __d('webzash', 'Diff in O/P Balance') . $xCE;
			echo $xCS . toCurrency('D', $bsheet['opdiff']['opdiff_balance']) . $xCE;
			echo $xRE;
		}
	}

	/* Total */
	echo $xRS;
	echo $xCS . __d('webzash', 'Total') . $xCE;
	echo $xCS . toCurrency('D', $bsheet['final_assets_total']) . $xCE;
	echo $xRE;
	echo $xRS . $xRE;

	/**************** Liabilities ****************/
	echo $xRS;
	echo $xCS . __d('webzash', 'Liabilities and Owners Equity (Cr)') . $xCE;
	echo $xCS . __d('webzash', 'Amount') . ' (' . Configure::read('Account.currency_symbol') . ')' . $xCE;
	echo $xRE;
	echo account_st_short($bsheet['liabilities'], $c = -1, $this, 'C');
	echo $xRS . $xRE;

	/* Liabilities Total */
	echo $xRS;
	echo $xCS . __d('webzash', 'Total Liability and Owners Equity') . $xCE;
	echo $xCS . toCurrency('C', $bsheet['liabilities_total']) . $xCE;
	echo $xRE;

	/* Net profit */
	if (calculate($bsheet['pandl'], 0, '>=')) {
		echo $xRS;
		echo $xCS . __d('webzash', 'Profit &amp; Loss Account (Net Profit)') . $xCE;
		echo $xCS . toCurrency('C', $bsheet['pandl']) . $xCE;
		echo $xRE;
	}

	if ($bsheet['is_opdiff']) {
		/* If diff in opening balance is Cr */
		if ($bsheet['opdiff']['opdiff_balance_dc'] == 'C') {
			echo $xRS;
			echo $xCS . __d('webzash', 'Diff in O/P Balance') . $xCE;
			echo $xCS . toCurrency('C', $bsheet['opdiff']['opdiff_balance']) . $xCE;
			echo $xRE;
		}
	}

	/* Total */
	echo $xRS;
	echo $xCS . __d('webzash', 'Total') . $xCE;
	echo $xCS . toCurrency('C', $bsheet['final_liabilities_total']) . $xCE;
	echo $xRE;
	echo $xRS . $xRE;
