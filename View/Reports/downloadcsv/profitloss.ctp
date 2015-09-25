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

$gross_total = 0;
$positive_gross_pl = 0;
$net_expense_total = 0;
$net_income_total = 0;
$positive_net_pl = 0;

?>

<?php
	echo $subtitle;
	echo "\n";
	echo "\n";

	/* Gross Expense */
	echo '"' . __d('webzash', 'Gross Expenses') . '",';
	echo '"' . __d('webzash', '(Dr) Amount') . '"';
	echo "\n";
	echo account_st_short($pandl['gross_expenses'], $c = -1, $this, 'D');
	echo "\n";

	/* Gross Expense Total */
	$gross_total = $pandl['gross_expense_total'];
	echo '"' . __d('webzash', 'Total Gross Expenses') . '",';
	echo '"' . toCurrency('D', $pandl['gross_expense_total']) . '"';
	echo "\n";

	/* Gross Profit C/D */
	if (calculate($pandl['gross_pl'], 0, '>=')) {
		echo '"' . __d('webzash', 'Gross Profit C/D') . '",';
		echo '"' . toCurrency('', $pandl['gross_pl']) . '"';
		$gross_total = calculate($gross_total, $pandl['gross_pl'], '+');
		echo "\n";
	}

	echo '"' . __d('webzash', 'Total') . '",';
	echo '"' . toCurrency('D', $gross_total) . '"';
	echo "\n";
	echo "\n";

	/* Gross Incomes */
	echo '"' . __d('webzash', 'Gross Incomes') . '",';
	echo '"' . __d('webzash', '(Cr) Amount') . '"';
	echo "\n";
	echo account_st_short($pandl['gross_incomes'], $c = -1, $this, 'C');
	echo "\n";

	/* Gross Income Total */
	$gross_total = $pandl['gross_income_total'];
	echo '"' . __d('webzash', 'Total Gross Incomes') . '",';
	echo '"' . toCurrency('C', $pandl['gross_income_total']) . '"';
	echo "\n";

	/* Gross Loss C/D */
	if (calculate($pandl['gross_pl'], 0, '>=')) {
		/* Do nothing */
	} else {
		echo '"' . __d('webzash', 'Gross Loss C/D') . '",';
		$positive_gross_pl = calculate($pandl['gross_pl'], 0, 'n');
		echo '"' . toCurrency('', $positive_gross_pl) . '"';
		$gross_total = calculate($gross_total, $positive_gross_pl, '+');
		echo "\n";
	}

	echo '"' . __d('webzash', 'Total') . '",';
	echo '"' . toCurrency('C', $gross_total) . '"';
	echo "\n";
	echo "\n";

	/* Net Expenses */
	echo '"' . __d('webzash', 'Net Expenses') . '",';
	echo '"' . __d('webzash', '(Dr) Amount'). '"';
	echo "\n";
	echo account_st_short($pandl['net_expenses'], $c = -1, $this, 'D');
	echo "\n";

	/* Net Expense Total */
	$net_expense_total = $pandl['net_expense_total'];
	echo '"' . __d('webzash', 'Total Expenses') . '",';
	echo '"' . toCurrency('D', $pandl['net_expense_total']) . '"';
	echo "\n";

	/* Gross Loss B/D */
	if (calculate($pandl['gross_pl'], 0, '>=')) {
		/* Do nothing */
	} else {
		echo '"' . __d('webzash', 'Gross Loss B/D') . '",';
		$positive_gross_pl = calculate($pandl['gross_pl'], 0, 'n');
		echo '"' . toCurrency('', $positive_gross_pl) . '"';
		$net_expense_total = calculate($net_expense_total, $positive_gross_pl, '+');
		echo "\n";
	}

	/* Net Profit */
	if (calculate($pandl['net_pl'], 0, '>=')) {
		echo '"' . __d('webzash', 'Net Profit') . '",';
		echo '"' . toCurrency('', $pandl['net_pl']) . '"';
		$net_expense_total = calculate($net_expense_total, $pandl['net_pl'], '+');
		echo "\n";
	}

	echo '"' . __d('webzash', 'Total') . '",';
	echo '"' . toCurrency('D', $net_expense_total) . '"';
	echo "\n";
	echo "\n";

	/* Net Income */
	echo '"' . __d('webzash', 'Net Incomes') . '",';
	echo '"' . __d('webzash', '(Cr) Amount') . '"';
	echo "\n";
	echo account_st_short($pandl['net_incomes'], $c = -1, $this, 'C');
	echo "\n";

	/* Net Income Total */
	$net_income_total = $pandl['net_income_total'];
	echo '"' . __d('webzash', 'Total Incomes') . '",';
	echo '"' . toCurrency('C', $pandl['net_income_total']) . '"';
	echo "\n";

	/* Gross Profit B/D */
	if (calculate($pandl['gross_pl'], 0, '>=')) {
		$net_income_total = calculate($net_income_total, $pandl['gross_pl'], '+');
		echo '"' . __d('webzash', 'Gross Profit B/D') . '",';
		echo '"' .  toCurrency('', $pandl['gross_pl']) . '"';
		echo "\n";
	}

	/* Net Loss */
	if (calculate($pandl['net_pl'], 0, '>=')) {
		/* Do nothing */
	} else {
		echo '"' . __d('webzash', 'Net Loss') . '",';
		$positive_net_pl = calculate($pandl['net_pl'], 0, 'n');
		echo '"' . toCurrency('', $positive_net_pl) . '"';
		$net_income_total = calculate($net_income_total, $positive_net_pl, '+');
		echo "\n";
	}

	echo '"' . __d('webzash', 'Total') . '",';
	echo '"' . toCurrency('C', $net_income_total) . '"';
	echo "\n";
