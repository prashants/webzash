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

/**
 * Display chart of accounts
 *
 * @account AccountList group account
 * @c int counter for number of level deep the account is
 * @THIS this $this CakePHP object passed inside function
 */
function print_account_chart($account, $c = 0, $THIS)
{
	$counter = $c;

	/* Print groups */
	if ($account->id != 0) {
		echo '<tr class="tr-group">';
		echo '<td class="td-group">';
		echo print_space($counter);
		/* If group id less than 4 bold the primary groups */
		if ($account->id <= 4) {
			echo '<strong>' .  $account->name. '</strong>';
		} else {
			echo $account->name;
		}
		echo '</td>';
		echo '<td>Group</td>';
		echo '<td>-</td>';
		echo '<td>-</td>';

		/* If group id less than 4 dont show edit and delete links */
		if ($account->id <= 4) {
			echo '<td class="td-actions"></td><td class="td-actions"></td>';
		} else {
			echo '<td class="td-actions">' . $THIS->Html->link(__d('webzash', 'Edit'), array('controller' => 'groups', 'action' => 'edit', $account->id)) . '</td>';
			echo '<td class="td-actions">' . $THIS->Form->postLink(__d('webzash', 'Delete'), array('controller' => 'groups', 'action' => 'delete', $account->id), array('confirm' => __d('webzash', 'Are you sure ?'))) . '</td>';
		}
		echo '</tr>';
	}

	/* Print child ledgers */
	if (count($account->children_ledgers) > 0) {
		$counter++;
		foreach ($account->children_ledgers as $id => $data) {
			echo '<tr class="tr-ledger">';
			echo '<td class="td-ledger">';
			echo print_space($counter);
			echo $THIS->Html->link($data['name'], array('controller' => 'report', 'action' => 'ledgerst', $data['id']));
			echo '</td>';
			echo '<td>Ledger</td>';
			echo '<td>';

			if ($data['op_total_dc'] == 'D') {
				echo 'Dr';
			} else {
				echo 'Cr';
			}
			echo ' ';
			echo $data['op_total'];
			echo '</td>';

			echo '<td>';
			if ($data['cl_total_dc'] == 'D') {
				echo 'Dr';
			} else {
				echo 'Cr';
			}
			echo ' ';
			echo $data['cl_total'];
			echo '</td>';

			echo '<td class="td-actions">' . $THIS->Html->link(__d('webzash', 'Edit'), array('controller' => 'ledgers', 'action' => 'edit', $data['id'])) . '</td>';
			echo '<td class="td-actions">' . $THIS->Form->postLink(__d('webzash', 'Delete'), array('controller' => 'ledgers', 'action' => 'delete', $data['id']), array('confirm' => __d('webzash', 'Are you sure ?'))) . '</td>';
			echo '</tr>';
		}
		$counter--;
	}

	/* Print child groups recursively */
	foreach ($account->children_groups as $id => $data) {
		$counter++;
		print_account_chart($data, $counter, $THIS);
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

echo '<table>';
	echo '<th>' . __d('webzash', 'Account Name') . '</th>';
	echo '<th>' . __d('webzash', 'Type') . '</th>';
	echo '<th>' . __d('webzash', 'O/P Balance') . '</th>';
	echo '<th>' . __d('webzash', 'C/L Balance') . '</th>';
	echo '<th></th><th></th>';
	print_account_chart($accountlist, 0, $this);
echo '</table>';
