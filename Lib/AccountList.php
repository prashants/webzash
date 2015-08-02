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
 * Class to store the entire account tree with the details
 */
class AccountList
{
	var $id = 0;
	var $name = '';
	var $code = '';

	var $g_parent_id = 0;		/* Group specific */
	var $g_affects_gross = 0;	/* Group specific */
	var $l_group_id = 0;		/* Ledger specific */
	var $l_type = 0;		/* Ledger specific */
	var $l_reconciliation = 0;	/* Ledger specific */
	var $l_notes = '';		/* Ledger specific */

	var $op_total = 0;
	var $op_total_dc = 'D';
	var $dr_total = 0;
	var $cr_total = 0;
	var $cl_total = 0;
	var $cl_total_dc = 'D';

	var $children_groups = array();
	var $children_ledgers = array();

	var $counter = 0;

	var $only_opening = false;
	var $start_date = null;
	var $end_date = null;
	var $affects_gross = -1;

	var $Group = null;
	var $Ledger = null;

/**
 * Initializer
 */
	function AccountList()
	{
		return;
	}

/**
 * Setup which group id to start from
 */
	function start($id)
	{
		if ($id == 0)
		{
			$this->id = NULL;
			$this->name = "None";
		} else {
			$group = $this->Group->find('first', array('conditions' => array('Group.id' => $id)));
			$this->id = $group['Group']['id'];
			$this->name = $group['Group']['name'];
			$this->code = $group['Group']['code'];
			$this->g_parent_id = $group['Group']['parent_id'];
			$this->g_affects_gross = $group['Group']['affects_gross'];
		}

		$this->op_total = 0;
		$this->op_total_dc = 'D';
		$this->dr_total = 0;
		$this->cr_total = 0;
		$this->cl_total = 0;
		$this->cl_total_dc = 'D';

		/* If affects_gross set, add sub-ledgers to only affects_gross == 0 */
		if ($this->affects_gross == 1) {
			/* Skip adding sub-ledgers if affects_gross is set and value == 1 */
		} else if ($this->affects_gross == 0) {
			/* Add sub-ledgers if affects_gross is set and value == 0 */
			$this->add_sub_ledgers();
		} else {
			/* Add sub-ledgers if affects_gross is not set == -1 */
			$this->add_sub_ledgers();
		}

		$this->add_sub_groups();
	}

/**
 * Find and add subgroups as objects
 */
	function add_sub_groups()
	{
		$conditions = array('Group.parent_id' => $this->id);

		/* Check if net or gross restriction is set */
		if ($this->affects_gross == 0) {
			$conditions['Group.affects_gross'] = 0;
		}
		if ($this->affects_gross == 1) {
			$conditions['Group.affects_gross'] = 1;
		}
		/* Reset is since its no longer needed below 1st level of sub-groups */
		$this->affects_gross = -1;

		/* If primary group sort by id else sort by name */
		if ($this->id == NULL) {
			$child_group_q = $this->Group->find('all', array(
				'conditions' => $conditions,
				'order' => array('Group.id'),
			));
		} else {
			$child_group_q = $this->Group->find('all', array(
				'conditions' => $conditions,
				'order' => array('Group.name'),
			));
		}

		$counter = 0;
		foreach ($child_group_q as $row)
		{
			/* Create new AccountList object */
			$this->children_groups[$counter] = new AccountList();

			/* Initial setup */
			$this->children_groups[$counter]->Group = &$this->Group;
			$this->children_groups[$counter]->Ledger = &$this->Ledger;
			$this->children_groups[$counter]->only_opening = $this->only_opening;
			$this->children_groups[$counter]->start_date = $this->start_date;
			$this->children_groups[$counter]->end_date = $this->end_date;
			$this->children_groups[$counter]->affects_gross = -1; /* No longer needed in sub groups */

			$this->children_groups[$counter]->start($row['Group']['id']);

			/* Calculating opening balance total for all the child groups */
			$temp1 = calculate_withdc(
				$this->op_total,
				$this->op_total_dc,
				$this->children_groups[$counter]->op_total,
				$this->children_groups[$counter]->op_total_dc
			);
			$this->op_total = $temp1['amount'];
			$this->op_total_dc = $temp1['dc'];

			/* Calculating closing balance total for all the child groups */
			$temp2 = calculate_withdc(
				$this->cl_total,
				$this->cl_total_dc,
				$this->children_groups[$counter]->cl_total,
				$this->children_groups[$counter]->cl_total_dc
			);
			$this->cl_total = $temp2['amount'];
			$this->cl_total_dc = $temp2['dc'];

			/* Calculate Dr and Cr total */
			$this->dr_total = calculate($this->dr_total, $this->children_groups[$counter]->dr_total, '+');
			$this->cr_total = calculate($this->cr_total, $this->children_groups[$counter]->cr_total, '+');

			$counter++;
		}
	}

/**
 * Find and add subledgers as array items
 */
	function add_sub_ledgers()
	{
		$child_ledger_q = $this->Ledger->find('all', array(
			'conditions' => array('Ledger.group_id' => $this->id),
			'order' => array('Ledger.name'),
		));
		$counter = 0;
		foreach ($child_ledger_q as $row)
		{
			$this->children_ledgers[$counter]['id'] = $row['Ledger']['id'];
			$this->children_ledgers[$counter]['name'] = $row['Ledger']['name'];
			$this->children_ledgers[$counter]['code'] = $row['Ledger']['code'];
			$this->children_ledgers[$counter]['l_group_id'] = $row['Ledger']['group_id'];
			$this->children_ledgers[$counter]['l_type'] = $row['Ledger']['type'];
			$this->children_ledgers[$counter]['l_reconciliation'] = $row['Ledger']['reconciliation'];
			$this->children_ledgers[$counter]['l_notes'] = $row['Ledger']['notes'];

			/* If start date is specified dont use the opening balance since its not applicable */
			if (is_null($this->start_date)) {
				$this->children_ledgers[$counter]['op_total'] = $row['Ledger']['op_balance'];
				$this->children_ledgers[$counter]['op_total_dc'] = $row['Ledger']['op_balance_dc'];
			} else {
				$this->children_ledgers[$counter]['op_total'] = 0.00;
				$this->children_ledgers[$counter]['op_total_dc'] = $row['Ledger']['op_balance_dc'];
			}

			/* Calculating current group opening balance total */
			$temp3 = calculate_withdc(
				$this->op_total,
				$this->op_total_dc,
				$this->children_ledgers[$counter]['op_total'],
				$this->children_ledgers[$counter]['op_total_dc']
			);
			$this->op_total = $temp3['amount'];
			$this->op_total_dc = $temp3['dc'];

			if ($this->only_opening == true) {
				/* If calculating only opening balance */
				$this->children_ledgers[$counter]['dr_total'] = 0;
				$this->children_ledgers[$counter]['cr_total'] = 0;

				$this->children_ledgers[$counter]['cl_total'] =
					$this->children_ledgers[$counter]['op_total'];
				$this->children_ledgers[$counter]['cl_total_dc'] =
					$this->children_ledgers[$counter]['op_total_dc'];
			} else {
				$cl = $this->Ledger->closingBalance(
					$row['Ledger']['id'],
					$this->start_date,
					$this->end_date
				);

				$this->children_ledgers[$counter]['dr_total'] = $cl['dr_total'];
				$this->children_ledgers[$counter]['cr_total'] = $cl['cr_total'];

				$this->children_ledgers[$counter]['cl_total'] = $cl['amount'];
				$this->children_ledgers[$counter]['cl_total_dc'] = $cl['dc'];
			}

			/* Calculating current group closing balance total */
			$temp4 = calculate_withdc(
				$this->cl_total,
				$this->cl_total_dc,
				$this->children_ledgers[$counter]['cl_total'],
				$this->children_ledgers[$counter]['cl_total_dc']
			);
			$this->cl_total = $temp4['amount'];
			$this->cl_total_dc = $temp4['dc'];

			/* Calculate Dr and Cr total */
			$this->dr_total = calculate($this->dr_total, $this->children_ledgers[$counter]['dr_total'], '+');
			$this->cr_total = calculate($this->cr_total, $this->children_ledgers[$counter]['cr_total'], '+');

			$counter++;
		}
	}
}
