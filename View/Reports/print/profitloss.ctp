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
		if ($dc_type == 'D' && $account->cl_total_dc == 'C' && calculate($account->cl_total, 0, '!=')) {
			echo '<tr class="tr-group dc-error">';
		} else if ($dc_type == 'C' && $account->cl_total_dc == 'D' && calculate($account->cl_total, 0, '!=')) {
			echo '<tr class="tr-group dc-error">';
		} else {
			echo '<tr class="tr-group">';
		}

		echo '<td class="td-group">';
		echo print_space($counter);
		echo h(toCodeWithName($account->code, $account->name));
		echo '</td>';

		echo '<td class="text-right">';
		echo toCurrency($account->cl_total_dc, $account->cl_total);
		echo print_space($counter);
		echo '</td>';

		echo '</tr>';
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
			if ($dc_type == 'D' && $data['cl_total_dc'] == 'C' && calculate($data['cl_total'], 0, '!=')) {
				echo '<tr class="tr-ledger dc-error">';
			} else if ($dc_type == 'C' && $data['cl_total_dc'] == 'D' && calculate($data['cl_total'], 0, '!=')) {
				echo '<tr class="tr-ledger dc-error">';
			} else {
				echo '<tr class="tr-ledger">';
			}

			echo '<td class="td-ledger">';
			echo print_space($counter);
			echo h(toCodeWithName($data['code'], $data['name']));
			echo '</td>';

			echo '<td class="text-right">';
			echo toCurrency($data['cl_total_dc'], $data['cl_total']);
			echo print_space($counter);
			echo '</td>';

			echo '</tr>';
		}
	$counter--;
	}
}

function print_space($count)
{
	$html = '';
	for ($i = 1; $i <= $count; $i++) {
		$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	}
	return $html;
}

$gross_total = 0;
$positive_gross_pl = 0;
$net_expense_total = 0;
$net_income_total = 0;
$positive_net_pl = 0;

?>

<div class="subtitle text-center">
	<?php echo $subtitle ?>
</div>

<table>

	<tr>
		<!-- Gross Expenses -->
		<td class="table-top width-50">
			<table class="stripped">
				<tr>
					<th><?php echo __d('webzash', 'Gross Expenses'); ?></th>
					<th class="text-right"><?php echo __d('webzash', '(Dr) Amount'); ?></th>
				</tr>
				<?php echo account_st_short($pandl['gross_expenses'], $c = -1, $this, 'D'); ?>
			</table>
		</td>

		<!-- Gross Incomes -->
		<td class="table-top width-50">
			<table class="stripped">
				<tr>
					<th><?php echo __d('webzash', 'Gross Incomes'); ?></th>
					<th class="text-right"><?php echo __d('webzash', '(Cr) Amount'); ?></th>
				</tr>
				<?php echo account_st_short($pandl['gross_incomes'], $c = -1, $this, 'C'); ?>
			</table>
		</td>
	</tr>

	<tr>
		<td class="table-top width-50">
			<div class="report-tb-pad"></div>
			<table class="stripped">
				<?php
				/* Gross Expense Total */
				$gross_total = $pandl['gross_expense_total'];
				if (calculate($pandl['gross_expense_total'], 0, '>=')) {
					echo '<tr class="bold-text">';
					echo '<td>' . __d('webzash', 'Total Gross Expenses') . '</td>';
					echo '<td class="text-right">' . toCurrency('D', $pandl['gross_expense_total']) . '</td>';
					echo '</tr>';
				} else {
					echo '<tr class="dc-error bold-text">';
					echo '<td>' . __d('webzash', 'Total Gross Expenses') . '</td>';
					echo '<td class="text-right show-tooltip" data-toggle="tooltip" data-original-title="Expecting Dr Balance">' . toCurrency('D', $pandl['gross_expense_total']) . '</td>';
					echo '</tr>';
				}
				?>
				<tr class="bold-text">
					<?php
					/* Gross Profit C/D */
					if (calculate($pandl['gross_pl'], 0, '>=')) {
						echo '<td>' . __d('webzash', 'Gross Profit C/D') . '</td>';
						echo '<td class="text-right">' . toCurrency('', $pandl['gross_pl']) . '</td>';
						$gross_total = calculate($gross_total, $pandl['gross_pl'], '+');
					} else {
						echo '<td>&nbsp</td>';
						echo '<td>&nbsp</td>';
					}
					?>
				</tr>
				<tr class="bold-text bg-filled">
					<td><?php echo __d('webzash', 'Total'); ?></td>
					<td class="text-right"><?php echo toCurrency('D', $gross_total); ?></td>
				</tr>
			</table>
		</td>

		<td class="table-top width-50">
			<div class="report-tb-pad"></div>
			<table class="stripped">
				<?php
				/* Gross Income Total */
				$gross_total = $pandl['gross_income_total'];
				if (calculate($pandl['gross_income_total'], 0, '>=')) {
					echo '<tr class="bold-text">';
					echo '<td>' . __d('webzash', 'Total Gross Incomes') . '</td>';
					echo '<td class="text-right">' . toCurrency('C', $pandl['gross_income_total']) . '</td>';
					echo '</tr>';
				} else {
					echo '<tr class="dc-error bold-text">';
					echo '<td>' . __d('webzash', 'Total Gross Incomes') . '</td>';
					echo '<td class="text-right show-tooltip" data-toggle="tooltip" data-original-title="Expecting Cr Balance">' . toCurrency('C', $pandl['gross_income_total']) . '</td>';
					echo '</tr>';
				}
				?>
				<tr class="bold-text">
					<?php
					/* Gross Loss C/D */
					if (calculate($pandl['gross_pl'], 0, '>=')) {
						echo '<td>&nbsp</td>';
						echo '<td>&nbsp</td>';
					} else {
						echo '<td>' . __d('webzash', 'Gross Loss C/D') . '</td>';
						$positive_gross_pl = calculate($pandl['gross_pl'], 0, 'n');
						echo '<td class="text-right">' . toCurrency('', $positive_gross_pl) . '</td>';
						$gross_total = calculate($gross_total, $positive_gross_pl, '+');
					}
					?>
				</tr>
				<tr class="bold-text bg-filled">
					<td><?php echo __d('webzash', 'Total'); ?></td>
					<td class="text-right"><?php echo toCurrency('C', $gross_total); ?></td>
				</tr>
			</table>
		</td>
	</tr>

	<!-- Net Profit and Loss -->
	<tr>
		<td class="table-top width-50">
			<div class="report-tb-pad"></div>
			<table class="stripped">
				<tr>
					<th><?php echo __d('webzash', 'Net Expenses'); ?></th>
					<th class="text-right"><?php echo __d('webzash', '(Dr) Amount'); ?></th>
				</tr>
				<?php echo account_st_short($pandl['net_expenses'], $c = -1, $this, 'D'); ?>
			</table>
		</td>

		<td class="table-top width-50">
			<div class="report-tb-pad"></div>
			<table class="stripped">
				<tr>
					<th><?php echo __d('webzash', 'Net Incomes'); ?></th>
					<th class="text-right"><?php echo __d('webzash', '(Cr) Amount'); ?></th>
				</tr>
				<?php echo account_st_short($pandl['net_incomes'], $c = -1, $this, 'C'); ?>
			</table>
		</td>
	</tr>

	<tr>
		<td class="table-top width-50">
			<div class="report-tb-pad"></div>
			<table class="stripped">
				<?php
				/* Net Expense Total */
				$net_expense_total = $pandl['net_expense_total'];
				if (calculate($pandl['net_expense_total'], 0, '>=')) {
					echo '<tr class="bold-text">';
					echo '<td>' . __d('webzash', 'Total Expenses') . '</td>';
					echo '<td class="text-right">' . toCurrency('D', $pandl['net_expense_total']) . '</td>';
					echo '</tr>';
				} else {
					echo '<tr class="dc-error bold-text">';
					echo '<td>' . __d('webzash', 'Total Expenses') . '</td>';
					echo '<td class="text-right show-tooltip" data-toggle="tooltip" data-original-title="Expecting Dr Balance">' . toCurrency('D', $pandl['net_expense_total']) . '</td>';
					echo '</tr>';
				}
				?>
				<tr class="bold-text">
					<?php
					/* Gross Loss B/D */
					if (calculate($pandl['gross_pl'], 0, '>=')) {
						echo '<td>&nbsp</td>';
						echo '<td>&nbsp</td>';
					} else {
						echo '<td>' . __d('webzash', 'Gross Loss B/D') . '</td>';
						$positive_gross_pl = calculate($pandl['gross_pl'], 0, 'n');
						echo '<td class="text-right">' . toCurrency('', $positive_gross_pl) . '</td>';
						$net_expense_total = calculate($net_expense_total, $positive_gross_pl, '+');
					}
					?>
				</tr>
				<tr class="bold-text ok-text">
					<?php
					/* Net Profit */
					if (calculate($pandl['net_pl'], 0, '>=')) {
						echo '<td>' . __d('webzash', 'Net Profit') . '</td>';
						echo '<td class="text-right">' . toCurrency('', $pandl['net_pl']) . '</td>';
						$net_expense_total = calculate($net_expense_total, $pandl['net_pl'], '+');
					} else {
						echo '<td>&nbsp</td>';
						echo '<td>&nbsp</td>';
					}
					?>
				</tr>
				<tr class="bold-text bg-filled">
					<td><?php echo __d('webzash', 'Total'); ?></td>
					<td class="text-right"><?php echo toCurrency('D', $net_expense_total); ?></td>
				</tr>
			</table>
		</td>

		<td class="table-top width-50">
			<div class="report-tb-pad"></div>
			<table class="stripped">
				<?php
				/* Net Income Total */
				$net_income_total = $pandl['net_income_total'];
				if (calculate($pandl['net_income_total'], 0, '>=')) {
					echo '<tr class="bold-text">';
					echo '<td>' . __d('webzash', 'Total Incomes') . '</td>';
					echo '<td class="text-right">' . toCurrency('C', $pandl['net_income_total']) . '</td>';
					echo '</tr>';
				} else {
					echo '<tr class="dc-error bold-text">';
					echo '<td>' . __d('webzash', 'Total Incomes') . '</td>';
					echo '<td class="text-right show-tooltip" data-toggle="tooltip" data-original-title="Expecting Cr Balance">' . toCurrency('C', $pandl['net_income_total']) . '</td>';
					echo '</tr>';
				}
				?>
				<tr class="bold-text">
					<?php
					/* Gross Profit B/D */
					if (calculate($pandl['gross_pl'], 0, '>=')) {
						$net_income_total = calculate($net_income_total, $pandl['gross_pl'], '+');
						echo '<td>' . __d('webzash', 'Gross Profit B/D') . '</td>';
						echo '<td class="text-right">' .  toCurrency('', $pandl['gross_pl']) . '</td>';
					} else {
						echo '<td>&nbsp</td>';
						echo '<td>&nbsp</td>';
					}
					?>
				</tr>
				<tr class="bold-text ok-text">
					<?php
					/* Net Loss */
					if (calculate($pandl['net_pl'], 0, '>=')) {
						echo '<td>&nbsp</td>';
						echo '<td>&nbsp</td>';
					} else {
						echo '<td>' . __d('webzash', 'Net Loss') . '</td>';
						$positive_net_pl = calculate($pandl['net_pl'], 0, 'n');
						echo '<td class="text-right">' . toCurrency('', $positive_net_pl) . '</td>';
						$net_income_total = calculate($net_income_total, $positive_net_pl, '+');
					}
					?>
				</tr>
				<tr class="bold-text bg-filled">
					<td><?php echo __d('webzash', 'Total'); ?></td>
					<td class="text-right"><?php echo toCurrency('C', $net_income_total); ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
