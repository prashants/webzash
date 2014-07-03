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
 * Webzash Plugin Generic Behavior
 *
 * @package Webzash
 * @subpackage Webzash.Model
 */

class GenericBehavior extends ModelBehavior {

	/**
	 * Update the closing balance of a ledger in a concurrent save way
	 *
	 * Write a unique sha1 to the 'lock_hash' column in the database, then
	 * calculate and update the ledger total, after that again read the
	 * 'lock_hash' from the database to see if it matches the previously
	 * read value. If it does, it safe and no one else has updated it.
	 * If it doesnt match then the closing balance has been updated by
	 * someone else and we have to repeat the entire process again.
	 */
	public function updateClosingBalance(Model $Model, $id = null) {

		if (empty($id)) {
			return false;
		}

		App::import("Webzash.Model", "Ledger");
		$Ledger = new Ledger();

		App::import("Webzash.Model", "Entryitem");
		$Entryitem = new Entryitem();

		/* TODO : return false if error, try for 10 times */
		while (1) {
			/* Generate random hash */
			$seed = 'JvKnrQWPsThuJteNQAuH';
			$hash = sha1(uniqid($seed . mt_rand(), true));
			$hash = sha1(uniqid($hash . mt_rand(), true));

			/* Save the lock hash string */
			$Ledger->read(null, $id);
			$Ledger->saveField('lock_hash', $hash);

			/* Opening balance */
			$op = $Ledger->find('first', array(
				'conditions' => array('Ledger.id' => $id)
			));
			if (empty($op['Ledger']['op_balance'])) {
				$op_total = 0;
			} else {
				$op_total = $op['Ledger']['op_balance'];
			}

			$dr_total = 0;
			$cr_total = 0;

			$Entryitem->virtualFields = array('total' => 'SUM(Entryitem.amount)');

			/* Debit total */
			$total = $Entryitem->find('first', array(
				'fields' => array('total'),
				'conditions' => array('Entryitem.ledger_id' => $id, 'Entryitem.dc' => 'D')
			));
			if (empty($total[0]['Entryitem__total'])) {
				$dr_total = 0;
			} else {
				$dr_total = $total[0]['Entryitem__total'];
			}

			/* Credit total */
			$total = $Entryitem->find('first', array(
				'fields' => array('total'),
				'conditions' => array('Entryitem.ledger_id' => $id, 'Entryitem.dc' => 'C')
			));
			if (empty($total[0]['Entryitem__total'])) {
				$cr_total = 0;
			} else {
				$cr_total = $total[0]['Entryitem__total'];
			}

			/* Add opening balance */
			if ($op['Ledger']['op_balance_dc'] == 'D') {
				$dr_total = calculate($op_total, $dr_total, '+');
			} else {
				$cr_total = calculate($op_total, $cr_total, '+');
			}

			/* Calculate and update closing balance */
			$cl = 0;
			$cl_dc = '';
			if (calculate($dr_total, $cr_total, '>')) {
				$cl = calculate($dr_total, $cr_total, '-');
				$cl_dc = 'D';
			} else if (calculate($cr_total, $dr_total, '==')) {
				$cl = 0;
				$cl_dc = $op['Ledger']['op_balance_dc'];
			} else {
				$cl = calculate($cr_total, $dr_total, '-');
				$cl_dc = 'C';
			}

			$Ledger->saveField('cl_balance', $cl);
			$Ledger->saveField('cl_balance_dc', $cl_dc);

			/* Read the lock_hash from database to check if it has not changed */
			$Ledger->read(null, $id);
			/* If lock_hash is same then we are ok, if not redo all the calculations */
			if ($Ledger->data['Ledger']['lock_hash'] == $hash) {
				break;
			}
		}
		return true;
	}

}

