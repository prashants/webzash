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
	$xRE = '</Row>' . "\n";
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

$gross_total = 0;
$positive_gross_pl = 0;
$net_expense_total = 0;
$net_income_total = 0;
$positive_net_pl = 0;

?>

<?php
	echo $xRS . $xCS . $subtitle . $xCE . $xRE;
	echo $xRS . $xRE;

	/* Gross Expense */
	echo $xRS;
	echo $xCS . __d('webzash', 'Gross Expenses') . $xCE;
	echo $xCS . __d('webzash', '(Dr) Amount') . $xCE;
	echo $xRE;
	echo account_st_short($pandl['gross_expenses'], $c = -1, $this, 'D');
	echo $xRS . $xRE;

	/* Gross Expense Total */
	echo $xRS;
	$gross_total = $pandl['gross_expense_total'];
	echo $xCS . __d('webzash', 'Total Gross Expenses') . $xCE;
	echo $xCS . toCurrency('D', $pandl['gross_expense_total']) . $xCE;
	echo $xRE;

	/* Gross Profit C/D */
	if (calculate($pandl['gross_pl'], 0, '>=')) {
		echo $xRS;
		echo $xCS . __d('webzash', 'Gross Profit C/D') . $xCE;
		echo $xCS . toCurrency('', $pandl['gross_pl']) . $xCE;
		$gross_total = calculate($gross_total, $pandl['gross_pl'], '+');
		echo $xRE;
	}

	echo $xRS;
	echo $xCS . __d('webzash', 'Total') . $xCE;
	echo $xCS . toCurrency('D', $gross_total) . $xCE;
	echo $xRE;
	echo $xRS . $xRE;

	/* Gross Incomes */
	echo $xRS;
	echo $xCS . __d('webzash', 'Gross Incomes') . $xCE;
	echo $xCS . __d('webzash', '(Cr) Amount') . $xCE;
	echo $xRE;
	echo account_st_short($pandl['gross_incomes'], $c = -1, $this, 'C');
	echo $xRS . $xRE;

	/* Gross Income Total */
	echo $xRS;
	$gross_total = $pandl['gross_income_total'];
	echo $xCS . __d('webzash', 'Total Gross Incomes') . $xCE;
	echo $xCS . toCurrency('C', $pandl['gross_income_total']) . $xCE;
	echo $xRE;

	/* Gross Loss C/D */
	if (calculate($pandl['gross_pl'], 0, '>=')) {
		/* Do nothing */
	} else {
		echo $xRS;
		echo $xCS . __d('webzash', 'Gross Loss C/D') . $xCE;
		$positive_gross_pl = calculate($pandl['gross_pl'], 0, 'n');
		echo $xCS . toCurrency('', $positive_gross_pl) . $xCE;
		$gross_total = calculate($gross_total, $positive_gross_pl, '+');
		echo $xRE;
	}

	echo $xRS;
	echo $xCS . __d('webzash', 'Total') . $xCE;
	echo $xCS . toCurrency('C', $gross_total) . $xCE;
	echo $xRE;
	echo $xRS . $xRE;

	/* Net Expenses */
	echo $xRS;
	echo $xCS . __d('webzash', 'Net Expenses') . $xCE;
	echo $xCS . __d('webzash', '(Dr) Amount'). $xCE;
	echo $xRE;
	echo account_st_short($pandl['net_expenses'], $c = -1, $this, 'D');
	echo $xRS . $xRE;

	/* Net Expense Total */
	echo $xRS;
	$net_expense_total = $pandl['net_expense_total'];
	echo $xCS . __d('webzash', 'Total Expenses') . $xCE;
	echo $xCS . toCurrency('D', $pandl['net_expense_total']) . $xCE;
	echo $xRE;

	/* Gross Loss B/D */
	if (calculate($pandl['gross_pl'], 0, '>=')) {
		/* Do nothing */
	} else {
		echo $xRS;
		echo $xCS . __d('webzash', 'Gross Loss B/D') . $xCE;
		$positive_gross_pl = calculate($pandl['gross_pl'], 0, 'n');
		echo $xCS . toCurrency('', $positive_gross_pl) . $xCE;
		$net_expense_total = calculate($net_expense_total, $positive_gross_pl, '+');
		echo $xRE;
	}

	/* Net Profit */
	if (calculate($pandl['net_pl'], 0, '>=')) {
		echo $xRS;
		echo $xCS . __d('webzash', 'Net Profit') . $xCE;
		echo $xCS . toCurrency('', $pandl['net_pl']) . $xCE;
		$net_expense_total = calculate($net_expense_total, $pandl['net_pl'], '+');
		echo $xRE;
	}

	echo $xRS;
	echo $xCS . __d('webzash', 'Total') . $xCE;
	echo $xCS . toCurrency('D', $net_expense_total) . $xCE;
	echo $xRE;
	echo $xRS . $xRE;

	/* Net Income */
	echo $xRS;
	echo $xCS . __d('webzash', 'Net Incomes') . $xCE;
	echo $xCS . __d('webzash', '(Cr) Amount') . $xCE;
	echo $xRE;
	echo account_st_short($pandl['net_incomes'], $c = -1, $this, 'C');
	echo $xRS . $xRE;

	/* Net Income Total */
	echo $xRS;
	$net_income_total = $pandl['net_income_total'];
	echo $xCS . __d('webzash', 'Total Incomes') . $xCE;
	echo $xCS . toCurrency('C', $pandl['net_income_total']) . $xCE;
	echo $xRE;

	/* Gross Profit B/D */
	if (calculate($pandl['gross_pl'], 0, '>=')) {
		echo $xRS;
		$net_income_total = calculate($net_income_total, $pandl['gross_pl'], '+');
		echo $xCS . __d('webzash', 'Gross Profit B/D') . $xCE;
		echo $xCS .  toCurrency('', $pandl['gross_pl']) . $xCE;
		echo $xRE;
	}

	/* Net Loss */
	if (calculate($pandl['net_pl'], 0, '>=')) {
		/* Do nothing */
	} else {
		echo $xRS;
		echo $xCS . __d('webzash', 'Net Loss') . $xCE;
		$positive_net_pl = calculate($pandl['net_pl'], 0, 'n');
		echo $xCS . toCurrency('', $positive_net_pl) . $xCE;
		$net_income_total = calculate($net_income_total, $positive_net_pl, '+');
		echo $xRE;
	}

	echo $xRS;
	echo $xCS . __d('webzash', 'Total') . $xCE;
	echo $xCS . toCurrency('C', $net_income_total) . $xCE;
	echo $xRE;
	echo $xRS . $xRE;
