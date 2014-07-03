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

App::uses('WebzashAppController', 'Webzash.Controller');

/**
 * Webzash Plugin Entries Controller
 *
 * @package Webzash
 * @subpackage Webzash.Controllers
 */
class EntriesController extends WebzashAppController {


	public $helpers = array('Webzash.Generic');

	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {

		/* Setup pagination */
		$this->Paginator->settings = array(
			'Entry' => array(
				'limit' => 10,
				'order' => array('Entry.date' => 'desc'),
			)
		);

		$this->set('entries', $this->Paginator->paginate('Entry'));
		return;
	}

/**
 * show method
 *
 * @param string $entrytypeLabel
 * @return void
 */
	public function show($entrytypeLabel = null) {
		$this->loadModel('Entrytype');

		/* Check for valid entry type */
		if (empty($entrytypeLabel)) {
			$this->Session->setFlash(__('Entry type not specified. Showing all entries.'), 'error');
			return $this->redirect(array('controller' => 'entries', 'action' => 'index'));
		}
		$entrytype = $this->Entrytype->find('first', array('conditions' => array('Entrytype.label' => $entrytypeLabel)));
		if (!$entrytype) {
			$this->Session->setFlash(__('Entry type not found. Showing all entries.'), 'error');
			return $this->redirect(array('controller' => 'entries', 'action' => 'index'));
		}

		$this->set('actionlinks', array(
			array('controller' => 'entries', 'action' => 'add', 'data' => $entrytype['Entrytype']['label'], 'title' => __('Add ') . $entrytype['Entrytype']['name']),
		));

		/* Setup pagination */
		$this->Paginator->settings = array(
			'Entry' => array(
				'limit' => 10,
				'conditions' => array('Entry.entrytype_id' => $entrytype['Entrytype']['id']),
				'order' => array('Entry.date' => 'desc'),
			)
		);

		$this->set('entries', $this->Paginator->paginate('Entry'));

		$this->set('entrytype', $entrytype);

		return;
	}

/**
 * add method
 *
 * @param string $entrytypeLabel
 * @return void
 */
	public function add($entrytypeLabel = null) {

		/* TODO : Test code */
		$this->Session->write('startDate', '2014-04-01 02:00:00');
		$this->Session->write('endDate', '2015-03-31 00:59:00');

		$this->loadModel('Entrytype');
		$this->loadModel('Entryitem');
		$this->loadModel('Ledger');
		$this->loadModel('Tag');
		$this->Ledger->Behaviors->attach('Webzash.Generic');

		/* Check for valid entry type */
		if (!$entrytypeLabel) {
			$this->Session->setFlash(__('Entry type not specified.'), 'error');
			return $this->redirect(array('controller' => 'entries', 'action' => 'all'));
		}
		$entrytype = $this->Entrytype->find('first', array('conditions' => array('Entrytype.label' => $entrytypeLabel)));
		if (!$entrytype) {
			$this->Session->setFlash(__('Entry type not found.'), 'error');
			return $this->redirect(array('controller' => 'entries', 'action' => 'all'));
		}
		$this->set('entrytype', $entrytype);

		/* Fetch all tags and include a 'None' option */
		$rawtags = $this->Tag->find('all', array('fields' => array('id', 'title'), 'order' => 'Tag.title'));
		$tags = array(0 => '(None)');
		foreach ($rawtags as $id => $rawtag) {
			$tags[$rawtag['Tag']['id']] = $rawtag['Tag']['title'];
		}
		$this->set('tags', $tags);

		/* Fetch all ledgers depending on the entry type */
		$rawledgers = null;
		$ledgers[0] = '(Please select..)';
		$ajaxledgertype = '';
		if ($entrytype['Entrytype']['bank_cash_ledger_restriction'] == 4) {
			$rawledgers = $this->Ledger->find('all', array('conditions' => array('Ledger.type' => '1'), 'order' => 'Ledger.name'));
			$ajaxledgertype = 'bankcash';
		} else if ($entrytype['Entrytype']['bank_cash_ledger_restriction'] == 5) {
			$rawledgers = $this->Ledger->find('all', array('conditions' => array('Ledger.type' => '0'), 'order' => 'Ledger.name'));
			$ajaxledgertype = 'nonbankcash';
		} else {
			$rawledgers = $this->Ledger->find('all', array('order' => 'Ledger.name'));
			$ajaxledgertype = 'all';
		}
		foreach ($rawledgers as $row => $rawledger) {
			$ledgers[$rawledger['Ledger']['id']] = $rawledger['Ledger']['name'];
		}
		$this->set('ledgers', $ledgers);
		$this->set('ajaxledgertype', $ajaxledgertype);

		/* Initial entry items present */
		$curEntryitems = array();
		if ($entrytype['Entrytype']['bank_cash_ledger_restriction'] == 3) {
			/* Special case if atleast one Bank or Cash on credit side (3) then 1st item is Cr */
			$curEntryitems[0] = array('dc' => 'C');
			$curEntryitems[1] = array('dc' => 'D');
		} else {
			/* Otherwise 1st item is Dr */
			$curEntryitems[0] = array('dc' => 'D');
			$curEntryitems[1] = array('dc' => 'C');
		}
		$curEntryitems[2] = array('dc' => 'D');
		$curEntryitems[3] = array('dc' => 'D');
		$curEntryitems[4] = array('dc' => 'D');
		$this->set('curEntryitems', $curEntryitems);

		/* On POST */
		if ($this->request->is('post')) {
			if (!empty($this->request->data)) {

				/***************************************************************************/
				/*********************************** ENTRY *********************************/
				/***************************************************************************/

				$entrydata = null;

				/* Entry id */
				unset($this->request->data['Entry']['id']);

				/***** Check and update entry number ******/
				if ($entrytype['Entrytype']['numbering'] == 1) {
					/* Auto */
					if (empty($this->request->data['Entry']['number'])) {
						$entrydata['Entry']['number'] = $this->Entry->nextNumber($entrytype['Entrytype']['id']);
					} else {
						$entrydata['Entry']['number'] = $this->request->data['Entry']['number'];
					}
				} else if ($entrytype['Entrytype']['numbering'] == 2) {
					/* Manual + Required */
					if (empty($this->request->data['Entry']['number'])) {
						$this->Session->setFlash(__('Entry number cannot be empty'), 'error');
						return;
					} else {
						$entrydata['Entry']['number'] = $this->request->data['Entry']['number'];
					}
				} else {
					/* Manual + Optional */
					$entrydata['Entry']['number'] = $this->request->data['Entry']['number'];
				}

				/****** Check entry type *****/
				$entrydata['Entry']['entrytype_id'] = $entrytype['Entrytype']['id'];

				/****** Check tag ******/
				if (empty($this->request->data['Entry']['tag_id'])) {
					$entrydata['Entry']['tag_id'] = null;
				} else {
					$entrydata['Entry']['tag_id'] = $this->request->data['Entry']['tag_id'];
				}

				/***** Narration *****/
				$entrydata['Entry']['narration'] = $this->request->data['Entry']['narration'];

				/***** TODO : Date *****/
				$entrydata['Entry']['date'] = $this->request->data['Entry']['date'];

				/***************************************************************************/
				/***************************** ENTRY ITEMS *********************************/
				/***************************************************************************/

				$entryitemdata = array();
				$dr_total = 0;
				$cr_total = 0;
				$dc_valid = false;

				foreach ($this->request->data['Entryitem'] as $id => $entryitem) {
					if ($entryitem['ledger_id'] <= 0) {
						continue;
					}
					$ledger = $this->Ledger->findById($entryitem['ledger_id']);
					if (!$ledger) {
						$this->Session->setFlash(__('Invalid ledger'), 'error');
						return;
					}

					/* Check ledger restriction */
					if ($entrytype['Entrytype']['bank_cash_ledger_restriction'] == 4) {
						if ($ledger['Ledger']['type'] != 1) {
							$this->Session->setFlash(__('Only bank or cash ledgers are allowed'), 'error');
							return;
						}
					}
					if ($entrytype['Entrytype']['bank_cash_ledger_restriction'] == 5) {
						if ($ledger['Ledger']['type'] == 1) {
							$this->Session->setFlash(__('Bank or cash ledgers are not allowed'), 'error');
							return;
						}

					}

					if ($entryitem['dc'] == 'D') {
						if ($entryitem['dr_amount'] <= 0) {
							$this->Session->setFlash(__('Invalid amount'), 'error');
							return;
						}
						$dr_total = calculate($dr_total, $entryitem['dr_amount'], '+');
						/* Check ledger restriction */
						if ($entrytype['Entrytype']['bank_cash_ledger_restriction'] == 2) {
							if ($ledger['Ledger']['type'] == 1) {
								$dc_valid = true;
							}
						}
					} else if ($entryitem['dc'] == 'C') {
						if ($entryitem['cr_amount'] <= 0) {
							$this->Session->setFlash(__('Invalid amount'), 'error');
							return;
						}
						$cr_total = calculate($cr_total, $entryitem['cr_amount'], '+');
						/* Check ledger restriction */
						if ($entrytype['Entrytype']['bank_cash_ledger_restriction'] == 3) {
							if ($ledger['Ledger']['type'] == 1) {
								$dc_valid = true;
							}
						}
					} else {
						$this->Session->setFlash(__('Invalid Dr/Cr'), 'error');
						return;
					}

					/* Add item to entryitemdata array */
					if ($entryitem['dc'] == 'D') {
						$entryitemdata[] = array('Entryitem' => array('dc' => $entryitem['dc'], 'ledger_id' => $entryitem['ledger_id'], 'amount' => $entryitem['dr_amount']));
					} else {
						$entryitemdata[] = array('Entryitem' => array('dc' => $entryitem['dc'], 'ledger_id' => $entryitem['ledger_id'], 'amount' => $entryitem['cr_amount']));
					}
				}

				/* Check ledger restriction */
				if ($entrytype['Entrytype']['bank_cash_ledger_restriction'] == 2) {
					if (!$dc_valid) {
						$this->Session->setFlash(__('Atleast one bank or cash ledger has to be on debit side'), 'error');
						return;
					}
				}
				if ($entrytype['Entrytype']['bank_cash_ledger_restriction'] == 3) {
					if (!$dc_valid) {
						$this->Session->setFlash(__('Atleast one bank or cash ledger has to be on credit side'), 'error');
						return;
					}
				}

				/* Check if debit and credit total match */
				if (calculate($dr_total, $cr_total, '!=')) {
					$this->Session->setFlash(__('Debit and Credit total do not match'), 'error');
					return;
				}
				$entrydata['Entry']['dr_total'] = $dr_total;
				$entrydata['Entry']['cr_total'] = $cr_total;

				/* Save entry */
				$ds = $this->Entry->getDataSource();
				$ds->begin();

				$this->Entry->create();
				if ($this->Entry->save($entrydata)) {
					/* Save entry items */
					foreach ($entryitemdata as $id => $itemdata) {
						$itemdata['Entryitem']['entry_id'] = $this->Entry->id;
						$this->Entryitem->create();
						if (!$this->Entryitem->save($itemdata)) {
							$ds->rollback();
							$this->Session->setFlash(__('Failed to save entry ledgers'), 'error');
							return;
						}
						/* Update closing balance using Behaviour */
						if (!$this->Ledger->updateClosingBalance($itemdata['Entryitem']['ledger_id'])) {
							$ds->rollback();
							$this->Session->setFlash(__('Failed to update closing balance'), 'error');
							return;
						}
					}
					$ds->commit();
					$this->Session->setFlash(__('The entry has been created.'), 'success');
					return $this->redirect(array('controller' => 'entries', 'action' => 'show', $entrytype['Entrytype']['label']));
				} else {
					$ds->rollback();
					$this->Session->setFlash(__('The entry could not be saved. Please, try again.'), 'error');
					return;
				}
			} else {
				$this->Session->setFlash(__('No data. Please, try again.'), 'error');
				return;
			}
		}
	}


/**
 * edit method
 *
 * @param string $entrytypeLabel
 * @param string $id
 * @return void
 */
	public function edit($entrytypeLabel = null, $id = null) {

	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @param string $entrytypeLabel
 * @param string $id
 * @return void
 */
	public function delete($entrytypeLabel = null, $id = null) {
		$this->loadModel('Entryitem');
		$this->loadModel('Entrytype');

		/* Check for valid entry type */
		if (empty($entrytypeLabel)) {
			$this->Session->setFlash(__('Entry type not specified. Showing all entries.'), 'error');
			return $this->redirect(array('controller' => 'entries', 'action' => 'index'));
		}
		$entrytype = $this->Entrytype->find('first', array('conditions' => array('Entrytype.label' => $entrytypeLabel)));
		if (!$entrytype) {
			$this->Session->setFlash(__('Entry type not found. Showing all entries.'), 'error');
			return $this->redirect(array('controller' => 'entries', 'action' => 'index'));
		}

		/* GET access not allowed */
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}

		/* Check if valid id */
		if (empty($id)) {
			$this->Session->setFlash(__('Entry not specified.'), 'error');
			return $this->redirect(array('controller' => 'entries', 'action' => 'index'));
		}

		/* Check if entry exists */
		if (!$this->Entry->exists($id)) {
			$this->Session->setFlash(__('Entry not found.'), 'error');
			return $this->redirect(array('controller' => 'entries', 'action' => 'index'));
		}

		/* Delete entry items */
		if (!$this->Entryitem->deleteAll(array('Entryitem.entry_id' => $id))) {
			$this->Session->setFlash(__('The entry ledgers could not be deleted. Please, try again.'), 'error');
			return $this->redirect(array('controller' => 'entries', 'action' => 'show', $entrytype['Entrytype']['label']));
		}

		/* Delete entry */
		if ($this->Entry->delete($id)) {
			$this->Session->setFlash(__('The entry has been deleted.'), 'success');
		} else {
			$this->Session->setFlash(__('The entry could not be deleted. Please, try again.'), 'error');
		}

		return $this->redirect(array('controller' => 'entries', 'action' => 'show', $entrytype['Entrytype']['label']));
	}

/**
 * Add a row in the entry via ajax
 *
 * @param string $addType
 * @return void
 */
	function addrow($addType = 'all')
	{
		$this->loadModel('Ledger');
		$this->layout = null;

		$ledgers[0] = '(Please select..)';
		$rawledgers = array();

		if ($addType == 'bankcash') {
			$rawledgers = $this->Ledger->find('all', array('conditions' => array('Ledger.type' => '1'), 'order' => 'Ledger.name'));
		} else if ($addType == 'nonbankcash') {
			$rawledgers = $this->Ledger->find('all', array('conditions' => array('Ledger.type' => '0'), 'order' => 'Ledger.name'));
		} else {
			$rawledgers = $this->Ledger->find('all', array('order' => 'Ledger.name'));
		}

		foreach ($rawledgers as $rawledger) {
			$ledgers[$rawledger['Ledger']['id']] = $rawledger['Ledger']['name'];
		}

		$this->set('ledgers', $ledgers);
	}

}
