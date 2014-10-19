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
	var $g_parent_id = 0;		/* Group specific */
	var $g_affects_gross = 0;	/* Group specific */
	var $l_group_id = 0;		/* Ledger specific */
	var $l_type = 0;		/* Ledger specific */
	var $l_reconciliation = 0;	/* Ledger specific */

	var $op_total = 0;
	var $op_total_dc = 'D';
	var $dr_total = 0;
	var $cr_total = 0;
	var $cl_total = 0;
	var $cl_total_dc = 'D';

	var $children_groups = array();
	var $children_ledgers = array();

	var $counter = 0;

	public static $Group = null;
	public static $Ledger = null;

/**
 * Initializer
 */
	function AccountList()
	{
		/* Setup the Group and Ledger model to use later */
		self::$Group = ClassRegistry::init('Group');
		self::$Ledger = ClassRegistry::init('Ledger');
		self::$Group->useDbConfig = 'wz_accconfig';
		self::$Ledger->useDbConfig = 'wz_accconfig';
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
			$group = self::$Group->find('first', array('conditions' => array('id' => $id)));
			$this->id = $group['Group']['id'];
			$this->name = $group['Group']['name'];
			$this->g_parent_id = $group['Group']['parent_id'];
			$this->g_affects_gross = $group['Group']['affects_gross'];
		}

		$this->op_total = 0;
		$this->op_total_dc = 'D';
		$this->dr_total = 0;
		$this->cr_total = 0;
		$this->cl_total = 0;
		$this->cl_total_dc = 'D';

		$this->add_sub_ledgers();
		$this->add_sub_groups();
	}

/**
 * Find and add subgroups as objects
 */
	function add_sub_groups()
	{
		$child_group_q = self::$Group->find('all', array('conditions' => array('parent_id' => $this->id)));
		$counter = 0;
		foreach ($child_group_q as $row)
		{
			$this->children_groups[$counter] = new AccountList();
			$this->children_groups[$counter]->start($row['Group']['id']);

			/* Calculating opening balance total for the group */
			if ($this->op_total_dc == 'D' && $this->children_groups[$counter]->op_total_dc == 'D') {
				$this->op_total = calculate($this->op_total, $this->children_groups[$counter]->op_total, '+');
				$this->op_total_dc = 'D';
			} else if ($this->op_total_dc == 'C' && $this->children_groups[$counter]->op_total_dc == 'C') {
				$this->op_total = calculate($this->op_total, $this->children_groups[$counter]->op_total, '+');
				$this->op_total_dc = 'C';
			} else {
				if (calculate($this->op_total, $this->children_groups[$counter]->op_total, '>')) {
					$this->op_total = calculate($this->op_total, $this->children_groups[$counter]->op_total, '-');
					$this->op_total_dc = $this->op_total_dc;
				} else {
					$this->op_total = calculate($this->children_groups[$counter]->op_total, $this->op_total, '-');
					$this->op_total_dc = $this->children_groups[$counter]->op_total_dc;
				}
			}

			/* Calculating group total */
			if ($this->cl_total_dc == 'D' && $this->children_groups[$counter]->cl_total_dc == 'D') {
				$this->cl_total = calculate($this->cl_total, $this->children_groups[$counter]->cl_total, '+');
				$this->cl_total_dc = 'D';
			} else if ($this->cl_total_dc == 'C' && $this->children_groups[$counter]->cl_total_dc == 'C') {
				$this->cl_total = calculate($this->cl_total, $this->children_groups[$counter]->cl_total, '+');
				$this->cl_total_dc = 'C';
			} else {
				if (calculate($this->cl_total, $this->children_groups[$counter]->cl_total, '>')) {
					$this->cl_total = calculate($this->cl_total, $this->children_groups[$counter]->cl_total, '-');
					$this->cl_total_dc = $this->cl_total_dc;
				} else {
					$this->cl_total = calculate($this->children_groups[$counter]->cl_total, $this->cl_total, '-');
					$this->cl_total_dc = $this->children_groups[$counter]->cl_total_dc;
				}
			}

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
		$child_ledger_q = self::$Ledger->find('all', array('conditions' => array('group_id' => $this->id)));
		$counter = 0;
		foreach ($child_ledger_q as $row)
		{
			$this->children_ledgers[$counter]['id'] = $row['Ledger']['id'];
			$this->children_ledgers[$counter]['name'] = $row['Ledger']['name'];
			$this->children_ledgers[$counter]['l_group_id'] = $row['Ledger']['group_id'];
			$this->children_ledgers[$counter]['l_type'] = $row['Ledger']['type'];
			$this->children_ledgers[$counter]['l_reconciliation'] = $row['Ledger']['reconciliation'];

			$this->children_ledgers[$counter]['op_total'] = $row['Ledger']['op_balance'];
			$this->children_ledgers[$counter]['op_total_dc'] = $row['Ledger']['op_balance_dc'];

			/* Calculating opening balance total */
			if ($this->op_total_dc == 'D' && $this->children_ledgers[$counter]['op_total_dc'] == 'D') {
				$this->op_total = calculate($this->op_total, $this->children_ledgers[$counter]['op_total'], '+');
				$this->op_total_dc = 'D';
			} else if ($this->op_total_dc == 'C' && $this->children_ledgers[$counter]['op_total_dc'] == 'C') {
				$this->op_total = calculate($this->op_total, $this->children_ledgers[$counter]['op_total'], '+');
				$this->op_total_dc = 'C';
			} else {
				if (calculate($this->op_total, $this->children_ledgers[$counter]['op_total'], '>')) {
					$this->op_total = calculate($this->op_total, $this->children_ledgers[$counter]['op_total'], '-');
					$this->op_total_dc = $this->op_total_dc;
				} else {
					$this->op_total = calculate($this->children_ledgers[$counter]['op_total'], $this->op_total, '-');
					$this->op_total_dc = $this->children_ledgers[$counter]['op_total_dc'];
				}
			}

			$cl = closingBalance($row['Ledger']['id']);

			$this->children_ledgers[$counter]['dr_total'] = $cl['dr_total'];
			$this->children_ledgers[$counter]['cr_total'] = $cl['cr_total'];

			$this->children_ledgers[$counter]['cl_total'] = $cl['balance'];
			$this->children_ledgers[$counter]['cl_total_dc'] = $cl['dc'];

			/* Calculating ledger closing balance total */
			if ($this->cl_total_dc == 'D' && $this->children_ledgers[$counter]['cl_total_dc'] == 'D') {
				$this->cl_total = calculate($this->cl_total, $this->children_ledgers[$counter]['cl_total'], '+');
				$this->cl_total_dc = 'D';
			} else if ($this->cl_total_dc == 'C' && $this->children_ledgers[$counter]['cl_total_dc'] == 'C') {
				$this->cl_total = calculate($this->cl_total, $this->children_ledgers[$counter]['cl_total'], '+');
				$this->cl_total_dc = 'C';
			} else {
				if (calculate($this->cl_total, $this->children_ledgers[$counter]['cl_total'], '>')) {
					$this->cl_total = calculate($this->cl_total, $this->children_ledgers[$counter]['cl_total'], '-');
					$this->cl_total_dc = $this->cl_total_dc;
				} else {
					$this->cl_total = calculate($this->children_ledgers[$counter]['cl_total'], $this->cl_total, '-');
					$this->cl_total_dc = $this->children_ledgers[$counter]['cl_total_dc'];
				}
			}

			/* Calculate Dr and Cr total */
			$this->dr_total = calculate($this->dr_total, $this->children_ledgers[$counter]['dr_total'], '+');
			$this->cr_total = calculate($this->cr_total, $this->children_ledgers[$counter]['cr_total'], '+');

			$counter++;
		}
	}
}
