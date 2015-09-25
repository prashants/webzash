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
			echo $THIS->Html->link(toCodeWithName($data['code'], $data['name']), array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'ledgerstatement', 'ledgerid' => $data['id']));
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

<script type="text/javascript">
$(document).ready(function() {

	$("#accordion").accordion({
		collapsible: true,
		<?php
			if ($options == false) {
				echo 'active: false';
			}
		?>
	});

	$('.show-tooltip').tooltip({trigger: 'manual'}).tooltip('show');

	/* Calculate date range in javascript */
	startDate = new Date(<?php echo strtotime(Configure::read('Account.startdate')) * 1000; ?>  + (new Date().getTimezoneOffset() * 60 * 1000));
	endDate = new Date(<?php echo strtotime(Configure::read('Account.enddate')) * 1000; ?>  + (new Date().getTimezoneOffset() * 60 * 1000));

	/* On selecting custom period show the start and end date form fields */
	$('#ProfitlossOpening').change(function() {
		if ($(this).prop('checked')) {
			$('#ProfitlossStartdate').prop('disabled', true);
			$('#ProfitlossEnddate').prop('disabled', true);
		} else {
			$('#ProfitlossStartdate').prop('disabled', false);
			$('#ProfitlossEnddate').prop('disabled', false);
		}
	});
	$('#ProfitlossOpening').trigger('change');

	/* Setup jQuery datepicker ui */
	$('#ProfitlossStartdate').datepicker({
		minDate: startDate,
		maxDate: endDate,
		dateFormat: '<?php echo Configure::read('Account.dateformatJS'); ?>',
		numberOfMonths: 1,
		onClose: function(selectedDate) {
			if (selectedDate) {
				$("#ProfitlossEnddate").datepicker("option", "minDate", selectedDate);
			} else {
				$("#ProfitlossEnddate").datepicker("option", "minDate", startDate);
			}
		}
	});
	$('#ProfitlossEnddate').datepicker({
		minDate: startDate,
		maxDate: endDate,
		dateFormat: '<?php echo Configure::read('Account.dateformatJS'); ?>',
		numberOfMonths: 1,
		onClose: function(selectedDate) {
			if (selectedDate) {
				$("#ProfitlossStartdate").datepicker("option", "maxDate", selectedDate);
			} else {
				$("#ProfitlossStartdate").datepicker("option", "maxDate", endDate);
			}
		}
	});
});

</script>

<div id="accordion">
	<h3>Options</h3>

	<div class="profitandloss form">
	<?php
		echo $this->Form->create('Profitloss', array(
			'inputDefaults' => array(
				'div' => 'form-group',
				'wrapInput' => false,
				'class' => 'form-control',
			),
		));

		echo $this->Form->input('opening', array(
			'type' => 'checkbox',
			'label' => __d('webzash', 'Show Opening Profit and Loss Statement'),
			'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : In opening Profit and Loss Statement all ledgers and groups balance must be zero.') . '</span>',
			'class' => 'checkbox',
		));

		echo $this->Form->input('startdate', array(
			'label' => __d('webzash', 'Start date'),
			'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : Leave start date as empty if you want statement from the start of the financial year.') . '</span>',
		));
		echo $this->Form->input('enddate', array(
			'label' => __d('webzash', 'End date'),
			'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : Leave end date as empty if you want statement till the end of the financial year.') . '</span>',
		));

		echo '<div class="form-group">';
		echo $this->Form->submit(__d('webzash', 'Submit'), array(
			'div' => false,
			'class' => 'btn btn-primary'
		));
		echo $this->Html->tag('span', '', array('class' => 'link-pad'));
		echo $this->Html->link(__d('webzash', 'Clear'), array('plugin' => 'webzash', 'controller' => 'reports', 'action' => 'profitloss'), array('class' => 'btn btn-default'));
		echo '</div>';

		echo $this->Form->end();
	?>
	</div>
</div>
<br />

<?php
	echo '<div class="btn-group" role="group">';

	echo $this->Html->link(
		__d('webzash', 'DOWNLOAD .CSV'),
		'/' . $this->params->url . '/downloadcsv:true',
		array('class' => 'btn btn-default btn-sm')
	);

	echo $this->Html->link(
		__d('webzash', 'DOWNLOAD .XLS'),
		'/' . $this->params->url . '/downloadxls:true',
		array('class' => 'btn btn-default btn-sm')
	);

	echo $this->Html->link(__d('webzash', 'PRINT'), '',
		array(
			'class' => 'btn btn-default btn-sm',
			'onClick' => "window.open('" . $this->Html->url('/' . $this->params->url . '/print:true') . "', 'windowname','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=1000,height=600'); return false;"
		)
	);

	echo '</div>';
	echo '<br /><br />';
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
					<th><?php echo __d('webzash', 'Gross Expenses (Dr)'); ?></th>
					<th class="text-right"><?php echo __d('webzash', 'Amount'); ?><?php echo ' (' . Configure::read('Account.currency_symbol') . ')'; ?></th>
				</tr>
				<?php echo account_st_short($pandl['gross_expenses'], $c = -1, $this, 'D'); ?>
			</table>
		</td>

		<!-- Gross Incomes -->
		<td class="table-top width-50">
			<table class="stripped">
				<tr>
					<th><?php echo __d('webzash', 'Gross Incomes (Cr)'); ?></th>
					<th class="text-right"><?php echo __d('webzash', 'Amount'); ?><?php echo ' (' . Configure::read('Account.currency_symbol') . ')'; ?></th>
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
					<th><?php echo __d('webzash', 'Net Expenses (Dr)'); ?></th>
					<th class="text-right"><?php echo __d('webzash', 'Amount'); ?><?php echo ' (' . Configure::read('Account.currency_symbol') . ')'; ?></th>
				</tr>
				<?php echo account_st_short($pandl['net_expenses'], $c = -1, $this, 'D'); ?>
			</table>
		</td>

		<td class="table-top width-50">
			<div class="report-tb-pad"></div>
			<table class="stripped">
				<tr>
					<th><?php echo __d('webzash', 'Net Incomes (Cr)'); ?></th>
					<th class="text-right"><?php echo __d('webzash', 'Amount'); ?><?php echo ' (' . Configure::read('Account.currency_symbol') . ')'; ?></th>
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
