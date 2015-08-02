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
 * Class to store the entire group tree
 */
class LedgerTree
{
	var $id = 0;
	var $name = '';
	var $code = '';

	var $children_groups = array();
	var $children_ledgers = array();

	var $counter = 0;

	var $current_id = -1;

	var $restriction_bankcash = 1;

	var $default_text = 'Please select...';

	var $Group = null;
	var $Ledger = null;

/**
 * Initializer
 */
	function LedgerTree()
	{
		return;
	}

/**
 * Setup which group id to start from
 */
	function build($id)
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
		}

		$this->add_sub_ledgers();
		$this->add_sub_groups();
	}

/**
 * Find and add subgroups as objects
 */
	function add_sub_groups()
	{
		$conditions = array('Group.parent_id' => $this->id);

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
			$this->children_groups[$counter] = new LedgerTree();

			/* Initial setup */
			$this->children_groups[$counter]->Group = &$this->Group;
			$this->children_groups[$counter]->Ledger = &$this->Ledger;
			$this->children_groups[$counter]->current_id = $this->current_id;

			$this->children_groups[$counter]->build($row['Group']['id']);

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
			$this->children_ledgers[$counter]['type'] = $row['Ledger']['type'];
			$counter++;
		}
	}

	var $ledgerList = array();

	/* Convert ledger tree to a list */
	public function toList($tree, $c = 0)
	{
		/* Add group name to list */
		if ($tree->id != 0) {
			/* Set the group id to negative value since we want to disable it */
			$this->ledgerList[-$tree->id] = $this->space($c) .
				h(toCodeWithName($tree->code, $tree->name));
		} else {
			$this->ledgerList[0] = __d('webzash', $this->default_text);
		}

		/* Add child ledgers */
		if (count($tree->children_ledgers) > 0) {
			$c++;
			foreach ($tree->children_ledgers as $id => $data) {
				$ledger_name = h(toCodeWithName($data['code'], $data['name']));
				/* Add ledgers as per restrictions */
				if ($this->restriction_bankcash == 1 ||
					$this->restriction_bankcash == 2 ||
					$this->restriction_bankcash == 3) {
					/* All ledgers */
					$this->ledgerList[$data['id']] = $this->space($c) . $ledger_name;
				} else if ($this->restriction_bankcash == 4) {
					/* Only bank or cash ledgers */
					if ($data['type'] == 1) {
						$this->ledgerList[$data['id']] = $this->space($c) . $ledger_name;
					}

				} else if ($this->restriction_bankcash == 5) {
					/* Only NON bank or cash ledgers */
					if ($data['type'] == 0) {
						$this->ledgerList[$data['id']] = $this->space($c) . $ledger_name;
					}
				}
			}
			$c--;
		}

		/* Process child groups recursively */
		foreach ($tree->children_groups as $id => $data) {
			$c++;
			$this->toList($data, $c);
			$c--;
		}
	}

	function space($count)
	{
		$str = '';
		for ($i = 1; $i <= $count; $i++) {
			$str .= '&nbsp;&nbsp;&nbsp;';
		}
		return $str;
	}
}
