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
App::uses('LedgerTree', 'Webzash.Lib');

/**
 * Webzash Plugin Entries Controller
 *
 * @package Webzash
 * @subpackage Webzash.Controllers
 */
class EntriesController extends WebzashAppController {

	public $uses = array('Webzash.Entry', 'Webzash.Group', 'Webzash.Ledger',
		'Webzash.Entrytype', 'Webzash.Entryitem', 'Webzash.Tag', 'Webzash.Log');

/**
 * index method
 *
 * @return void
 */
	public function index() {

		$this->set('title_for_layout', __d('webzash', 'List Of Entries'));

		$conditions = array();

		/* Filter by entry type */
		if (isset($this->passedArgs['show'])) {
			$entrytype = $this->Entrytype->find('first', array('conditions' => array('Entrytype.label' => $this->passedArgs['show'])));
			if (!$entrytype) {
				$this->Session->setFlash(__d('webzash', 'Entry type not found. Showing all entries.'), 'danger');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
			}

			$conditions['Entry.entrytype_id'] = $entrytype['Entrytype']['id'];
		}

		/* Filter by tag */
		if (isset($this->passedArgs['tag'])) {
			$conditions['Entry.tag_id'] = $this->passedArgs['tag'];
		}

		/* Setup pagination */
		$this->CustomPaginator->settings = array(
			'Entry' => array(
				'limit' => $this->Session->read('Wzsetting.row_count'),
				'conditions' => $conditions,
				'order' => array('Entry.date' => 'desc'),
			)
		);

		if ($this->request->is('post')) {
			if (empty($this->request->data['Entry']['show'])) {
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
			} else {
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index', 'show' => $this->request->data['Entry']['show']));
			}
		}

		if (empty($this->passedArgs['show'])) {
			$this->request->data['Entry']['show'] = '0';
		} else {
			$this->request->data['Entry']['show'] = $this->passedArgs['show'];
		}

		/* Pass varaibles to view which are used in Helpers */
		$this->set('allTags', $this->Tag->fetchAll());

		$this->set('entries', $this->CustomPaginator->paginate('Entry'));
		return;
	}

/**
 * view method
 *
 * @param string $entrytypeLabel
 * @param string $id
 * @return void
 */
	public function view($entrytypeLabel = null, $id = null) {

		/* Check for valid entry type */
		if (!$entrytypeLabel) {
			$this->Session->setFlash(__d('webzash', 'Entry type not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}
		$entrytype = $this->Entrytype->find('first', array('conditions' => array('Entrytype.label' => $entrytypeLabel)));
		if (!$entrytype) {
			$this->Session->setFlash(__d('webzash', 'Entry type not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}
		$this->set('entrytype', $entrytype);

		$this->set('title_for_layout', __d('webzash', 'View %s Entry', $entrytype['Entrytype']['name']));

		/* Check for valid entry id */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'Entry not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}
		$entry = $this->Entry->findById($id);
		if (!$entry) {
			$this->Session->setFlash(__d('webzash', 'Entry not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}

		/* Initial data */
		$curEntryitems = array();
		$curEntryitemsData = $this->Entryitem->find('all', array(
			'conditions' => array('Entryitem.entry_id' => $id),
		));
		foreach ($curEntryitemsData as $row => $data) {
			if ($data['Entryitem']['dc'] == 'D') {
				$curEntryitems[$row] = array(
					'dc' => $data['Entryitem']['dc'],
					'ledger_id' => $data['Entryitem']['ledger_id'],
					'ledger_name' => $this->Ledger->getName($data['Entryitem']['ledger_id']),
					'dr_amount' => $data['Entryitem']['amount'],
					'cr_amount' => '',
				);
			} else {
				$curEntryitems[$row] = array(
					'dc' => $data['Entryitem']['dc'],
					'ledger_id' => $data['Entryitem']['ledger_id'],
					'ledger_name' => $this->Ledger->getName($data['Entryitem']['ledger_id']),
					'dr_amount' => '',
					'cr_amount' => $data['Entryitem']['amount'],
				);
			}
		}
		$this->set('curEntryitems', $curEntryitems);

		/* Pass varaibles to view which are used in Helpers */
		$this->set('allTags', $this->Tag->fetchAll());

		$this->set('entry', $entry);

		return;
	}

/**
 * add method
 *
 * @param string $entrytypeLabel
 * @return void
 */
	public function add($entrytypeLabel = null) {

		/* Check for valid entry type */
		if (!$entrytypeLabel) {
			$this->Session->setFlash(__d('webzash', 'Entry type not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}
		$entrytype = $this->Entrytype->find('first', array('conditions' => array('Entrytype.label' => $entrytypeLabel)));
		if (!$entrytype) {
			$this->Session->setFlash(__d('webzash', 'Entry type not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}
		$this->set('entrytype', $entrytype);

		$this->set('title_for_layout', __d('webzash', 'Add %s Entry', $entrytype['Entrytype']['name']));

		$this->set('tag_options', $this->Tag->listAll());

		/* Ledger selection */
		$ledgers = new LedgerTree();
		$ledgers->Group = &$this->Group;
		$ledgers->Ledger = &$this->Ledger;
		$ledgers->current_id = -1;
		$ledgers->restriction_bankcash = $entrytype['Entrytype']['restriction_bankcash'];
		$ledgers->build(0);
		$ledgers->toList($ledgers, -1);
		$ledgers_disabled = array();
		foreach ($ledgers->ledgerList as $row => $data) {
			if ($row < 0) {
				$ledgers_disabled[] = $row;
			}
		}
		$this->set('ledger_options', $ledgers->ledgerList);
		$this->set('ledgers_disabled', $ledgers_disabled);

		/* Initial data */
		if ($this->request->is('post')) {
			$curEntryitems = array();
			foreach ($this->request->data['Entryitem'] as $row => $entryitem) {
				$curEntryitems[$row] = array(
					'dc' => $entryitem['dc'],
					'ledger_id' => $entryitem['ledger_id'],
					'dr_amount' => isset($entryitem['dr_amount']) ? $entryitem['dr_amount'] : '',
					'cr_amount' => isset($entryitem['cr_amount']) ? $entryitem['cr_amount'] : '',
				);
			}
			$this->set('curEntryitems', $curEntryitems);
		} else {
			$curEntryitems = array();
			if ($entrytype['Entrytype']['restriction_bankcash'] == 3) {
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
		}

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
						$this->Session->setFlash(__d('webzash', 'Entry number cannot be empty.'), 'danger');
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

				/***** Date *****/
				$entrydata['Entry']['date'] = dateToSql($this->request->data['Entry']['date']);

				/***************************************************************************/
				/***************************** ENTRY ITEMS *********************************/
				/***************************************************************************/

				/* Check ledger restriction */
				$dc_valid = false;
				foreach ($this->request->data['Entryitem'] as $row => $entryitem) {
					if ($entryitem['ledger_id'] <= 0) {
						continue;
					}
					$ledger = $this->Ledger->findById($entryitem['ledger_id']);
					if (!$ledger) {
						$this->Session->setFlash(__d('webzash', 'Invalid ledger selected.'), 'danger');
						return;
					}

					if ($entrytype['Entrytype']['restriction_bankcash'] == 4) {
						if ($ledger['Ledger']['type'] != 1) {
							$this->Session->setFlash(__d('webzash', 'Only bank or cash ledgers are allowed for this entry type.'), 'danger');
							return;
						}
					}
					if ($entrytype['Entrytype']['restriction_bankcash'] == 5) {
						if ($ledger['Ledger']['type'] == 1) {
							$this->Session->setFlash(__d('webzash', 'Bank or cash ledgers are not allowed for this entry type.'), 'danger');
							return;
						}
					}

					if ($entryitem['dc'] == 'D') {
						if ($entrytype['Entrytype']['restriction_bankcash'] == 2) {
							if ($ledger['Ledger']['type'] == 1) {
								$dc_valid = true;
							}
						}
					} else if ($entryitem['dc'] == 'C') {
						if ($entrytype['Entrytype']['restriction_bankcash'] == 3) {
							if ($ledger['Ledger']['type'] == 1) {
								$dc_valid = true;
							}
						}
					}
				}
				if ($entrytype['Entrytype']['restriction_bankcash'] == 2) {
					if (!$dc_valid) {
						$this->Session->setFlash(__d('webzash', 'Atleast one bank or cash ledger has to be on debit side for this entry type.'), 'danger');
						return;
					}
				}
				if ($entrytype['Entrytype']['restriction_bankcash'] == 3) {
					if (!$dc_valid) {
						$this->Session->setFlash(__d('webzash', 'Atleast one bank or cash ledger has to be on credit side for this entry type.'), 'danger');
						return;
					}
				}

				$dr_total = 0;
				$cr_total = 0;

				/* Check equality of debit and credit total */
				foreach ($this->request->data['Entryitem'] as $row => $entryitem) {
					if ($entryitem['ledger_id'] <= 0) {
						continue;
					}

					if ($entryitem['dc'] == 'D') {
						if ($entryitem['dr_amount'] <= 0) {
							$this->Session->setFlash(__d('webzash', 'Invalid amount specified. Amount cannot be negative or zero.'), 'danger');
							return;
						}
						if (countDecimal($entryitem['dr_amount']) > Configure::read('Account.decimal_places')) {
							$this->Session->setFlash(__d('webzash', 'Invalid amount specified. Maximum %s decimal places allowed.', Configure::read('Account.decimal_places')), 'danger');
							return;
						}
						$dr_total = calculate($dr_total, $entryitem['dr_amount'], '+');
					} else if ($entryitem['dc'] == 'C') {
						if ($entryitem['cr_amount'] <= 0) {
							$this->Session->setFlash(__d('webzash', 'Invalid amount specified. Amount cannot be negative or zero.'), 'danger');
							return;
						}
						if (countDecimal($entryitem['cr_amount']) > Configure::read('Account.decimal_places')) {
							$this->Session->setFlash(__d('webzash', 'Invalid amount specified. Maximum %s decimal places allowed.', Configure::read('Account.decimal_places')), 'danger');
							return;
						}
						$cr_total = calculate($cr_total, $entryitem['cr_amount'], '+');
					} else {
						$this->Session->setFlash(__d('webzash', 'Invalid Dr/Cr option selected.'), 'danger');
						return;
					}
				}
				if (calculate($dr_total, $cr_total, '!=')) {
					$this->Session->setFlash(__d('webzash', 'Debit and Credit total do not match.'), 'danger');
					return;
				}

				$entrydata['Entry']['dr_total'] = $dr_total;
				$entrydata['Entry']['cr_total'] = $cr_total;

				/* Add item to entryitemdata array if everything is ok */
				$entryitemdata = array();
				foreach ($this->request->data['Entryitem'] as $row => $entryitem) {
					if ($entryitem['ledger_id'] <= 0) {
						continue;
					}
					if ($entryitem['dc'] == 'D') {
						$entryitemdata[] = array(
							'Entryitem' => array(
								'dc' => $entryitem['dc'],
								'ledger_id' => $entryitem['ledger_id'],
								'amount' => $entryitem['dr_amount'],
							)
						);
					} else {
						$entryitemdata[] = array(
							'Entryitem' => array(
								'dc' => $entryitem['dc'],
								'ledger_id' => $entryitem['ledger_id'],
								'amount' => $entryitem['cr_amount'],
							)
						);
					}
				}

				/* Save entry */
				$ds = $this->Entry->getDataSource();
				$ds->begin();

				$this->Entry->create();
				if ($this->Entry->save($entrydata)) {
					/* Save entry items */
					foreach ($entryitemdata as $row => $itemdata) {
						$itemdata['Entryitem']['entry_id'] = $this->Entry->id;
						$this->Entryitem->create();
						if (!$this->Entryitem->save($itemdata)) {
							foreach ($this->Entryitem->validationErrors as $field => $msg) {
								$errmsg = $msg[0];
								break;
							}
							$ds->rollback();
							$this->Session->setFlash(__d('webzash', 'Failed to save entry ledgers. Error is : %s', $errmsg), 'danger');
							return;
						}
					}

					$tempentry = $this->Entry->read(null, $this->Entry->id);
					if (!$tempentry) {
						$this->Session->setFlash(__d('webzash', 'Oh snap ! Failed to create entry. Please, try again.'), 'danger');
						$ds->rollback();
						return;
					}
					$entryNumber = h(toEntryNumber(
						$tempentry['Entry']['number'],
						$entrytype['Entrytype']['id']
					));

					$this->Log->add('Added ' . $entrytype['Entrytype']['name'] . ' entry numbered ' . $entryNumber, 1);
					$ds->commit();

					$this->Session->setFlash(__d('webzash',
						'%s entry numbered "%s" created.',
						$entrytype['Entrytype']['name'],
						$entryNumber), 'success');

					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
				} else {
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'Failed to create entry. Please, try again.'), 'danger');
					return;
				}
			} else {
				$this->Session->setFlash(__d('webzash', 'No data. Please, try again.'), 'danger');
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

		/* Check for valid entry type */
		if (!$entrytypeLabel) {
			$this->Session->setFlash(__d('webzash', 'Entry type not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}
		$entrytype = $this->Entrytype->find('first', array('conditions' => array('Entrytype.label' => $entrytypeLabel)));
		if (!$entrytype) {
			$this->Session->setFlash(__d('webzash', 'Entry type not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}
		$this->set('entrytype', $entrytype);

		$this->set('title_for_layout', __d('webzash', 'Edit %s Entry', $entrytype['Entrytype']['name']));

		$this->set('tag_options', $this->Tag->listAll());

		/* Ledger selection */
		$ledgers = new LedgerTree();
		$ledgers->Group = &$this->Group;
		$ledgers->Ledger = &$this->Ledger;
		$ledgers->current_id = -1;
		$ledgers->restriction_bankcash = $entrytype['Entrytype']['restriction_bankcash'];
		$ledgers->build(0);
		$ledgers->toList($ledgers, -1);
		$ledgers_disabled = array();
		foreach ($ledgers->ledgerList as $row => $data) {
			if ($row < 0) {
				$ledgers_disabled[] = $row;
			}
		}
		$this->set('ledger_options', $ledgers->ledgerList);
		$this->set('ledgers_disabled', $ledgers_disabled);

		/* Check for valid entry id */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'Entry not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}
		$entry = $this->Entry->findById($id);
		if (!$entry) {
			$this->Session->setFlash(__d('webzash', 'Entry not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}

		/* Initial data */
		if ($this->request->is('post') || $this->request->is('put')) {
			$curEntryitems = array();
			foreach ($this->request->data['Entryitem'] as $row => $entryitem) {
				$curEntryitems[$row] = array(
					'dc' => $entryitem['dc'],
					'ledger_id' => $entryitem['ledger_id'],
					'dr_amount' => isset($entryitem['dr_amount']) ? $entryitem['dr_amount'] : '',
					'cr_amount' => isset($entryitem['cr_amount']) ? $entryitem['cr_amount'] : '',
				);
			}
			$this->set('curEntryitems', $curEntryitems);
		} else {
			$curEntryitems = array();
			$curEntryitemsData = $this->Entryitem->find('all', array(
				'conditions' => array('Entryitem.entry_id' => $id),
			));
			foreach ($curEntryitemsData as $row => $data) {
				if ($data['Entryitem']['dc'] == 'D') {
					$curEntryitems[$row] = array(
						'dc' => $data['Entryitem']['dc'],
						'ledger_id' => $data['Entryitem']['ledger_id'],
						'dr_amount' => $data['Entryitem']['amount'],
						'cr_amount' => '',
					);
				} else {
					$curEntryitems[$row] = array(
						'dc' => $data['Entryitem']['dc'],
						'ledger_id' => $data['Entryitem']['ledger_id'],
						'dr_amount' => '',
						'cr_amount' => $data['Entryitem']['amount'],
					);
				}
			}
			$curEntryitems[] = array('dc' => 'D');
			$curEntryitems[] = array('dc' => 'D');
			$curEntryitems[] = array('dc' => 'D');
			$this->set('curEntryitems', $curEntryitems);
		}

		/* On POST */
		if ($this->request->is('post') || $this->request->is('put')) {
			if (!empty($this->request->data)) {

				/* Check if acccount is locked */
				if (Configure::read('Account.locked') == 1) {
					$this->Session->setFlash(__d('webzash', 'Sorry, no changes are possible since the account is locked.'), 'danger');
					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
				}

				/***************************************************************************/
				/*********************************** ENTRY *********************************/
				/***************************************************************************/

				$entrydata = null;

				/* Entry id */
				unset($this->request->data['Entry']['id']);
				$this->Entry->id = $id;
				$entrydata['Entry']['id'] = $id;

				/***** Entry number ******/
				$entrydata['Entry']['number'] = $this->request->data['Entry']['number'];

				/****** Entrytype remains the same *****/
				$entrydata['Entry']['entrytype_id'] = $entrytype['Entrytype']['id'];

				/****** Check tag ******/
				if (empty($this->request->data['Entry']['tag_id'])) {
					$entrydata['Entry']['tag_id'] = null;
				} else {
					$entrydata['Entry']['tag_id'] = $this->request->data['Entry']['tag_id'];
				}

				/***** Narration *****/
				$entrydata['Entry']['narration'] = $this->request->data['Entry']['narration'];

				/***** Date *****/
				$entrydata['Entry']['date'] = dateToSql($this->request->data['Entry']['date']);

				/***************************************************************************/
				/***************************** ENTRY ITEMS *********************************/
				/***************************************************************************/

				/* Check ledger restriction */
				$dc_valid = false;
				foreach ($this->request->data['Entryitem'] as $row => $entryitem) {
					if ($entryitem['ledger_id'] <= 0) {
						continue;
					}
					$ledger = $this->Ledger->findById($entryitem['ledger_id']);
					if (!$ledger) {
						$this->Session->setFlash(__d('webzash', 'Invalid ledger selected.'), 'danger');
						return;
					}

					if ($entrytype['Entrytype']['restriction_bankcash'] == 4) {
						if ($ledger['Ledger']['type'] != 1) {
							$this->Session->setFlash(__d('webzash', 'Only bank or cash ledgers are allowed for this entry type.'), 'danger');
							return;
						}
					}
					if ($entrytype['Entrytype']['restriction_bankcash'] == 5) {
						if ($ledger['Ledger']['type'] == 1) {
							$this->Session->setFlash(__d('webzash', 'Bank or cash ledgers are not allowed for this entry type.'), 'danger');
							return;
						}
					}

					if ($entryitem['dc'] == 'D') {
						if ($entrytype['Entrytype']['restriction_bankcash'] == 2) {
							if ($ledger['Ledger']['type'] == 1) {
								$dc_valid = true;
							}
						}
					} else if ($entryitem['dc'] == 'C') {
						if ($entrytype['Entrytype']['restriction_bankcash'] == 3) {
							if ($ledger['Ledger']['type'] == 1) {
								$dc_valid = true;
							}
						}
					}
				}
				if ($entrytype['Entrytype']['restriction_bankcash'] == 2) {
					if (!$dc_valid) {
						$this->Session->setFlash(__d('webzash', 'Atleast one bank or cash ledger has to be on debit side for this entry type.'), 'danger');
						return;
					}
				}
				if ($entrytype['Entrytype']['restriction_bankcash'] == 3) {
					if (!$dc_valid) {
						$this->Session->setFlash(__d('webzash', 'Atleast one bank or cash ledger has to be on credit side for this entry type.'), 'danger');
						return;
					}
				}

				$dr_total = 0;
				$cr_total = 0;

				/* Check equality of debit and credit total */
				foreach ($this->request->data['Entryitem'] as $row => $entryitem) {
					if ($entryitem['ledger_id'] <= 0) {
						continue;
					}

					if ($entryitem['dc'] == 'D') {
						if ($entryitem['dr_amount'] <= 0) {
							$this->Session->setFlash(__d('webzash', 'Invalid amount specified. Amount cannot be negative or zero.'), 'danger');
							return;
						}
						if (countDecimal($entryitem['dr_amount']) > Configure::read('Account.decimal_places')) {
							$this->Session->setFlash(__d('webzash', 'Invalid amount specified. Maximum %s decimal places allowed.', Configure::read('Account.decimal_places')), 'danger');
							return;
						}
						$dr_total = calculate($dr_total, $entryitem['dr_amount'], '+');
					} else if ($entryitem['dc'] == 'C') {
						if ($entryitem['cr_amount'] <= 0) {
							$this->Session->setFlash(__d('webzash', 'Invalid amount specified. Amount cannot be negative or zero.'), 'danger');
							return;
						}
						if (countDecimal($entryitem['cr_amount']) > Configure::read('Account.decimal_places')) {
							$this->Session->setFlash(__d('webzash', 'Invalid amount specified. Maximum %s decimal places allowed.', Configure::read('Account.decimal_places')), 'danger');
							return;
						}
						$cr_total = calculate($cr_total, $entryitem['cr_amount'], '+');
					} else {
						$this->Session->setFlash(__d('webzash', 'Invalid Dr/Cr option selected.'), 'danger');
						return;
					}
				}
				if (calculate($dr_total, $cr_total, '!=')) {
					$this->Session->setFlash(__d('webzash', 'Debit and Credit total do not match.'), 'danger');
					return;
				}

				$entrydata['Entry']['dr_total'] = $dr_total;
				$entrydata['Entry']['cr_total'] = $cr_total;

				/* Add item to entryitemdata array if everything is ok */
				$entryitemdata = array();
				foreach ($this->request->data['Entryitem'] as $row => $entryitem) {
					if ($entryitem['ledger_id'] <= 0) {
						continue;
					}
					if ($entryitem['dc'] == 'D') {
						$entryitemdata[] = array(
							'Entryitem' => array(
								'dc' => $entryitem['dc'],
								'ledger_id' => $entryitem['ledger_id'],
								'amount' => $entryitem['dr_amount'],
							)
						);
					} else {
						$entryitemdata[] = array(
							'Entryitem' => array(
								'dc' => $entryitem['dc'],
								'ledger_id' => $entryitem['ledger_id'],
								'amount' => $entryitem['cr_amount'],
							)
						);
					}
				}

				/* Save entry */
				$ds = $this->Entry->getDataSource();
				$ds->begin();

				if ($this->Entry->save($entrydata)) {

					/* Delete all original entryitems */
					if (!$this->Entryitem->deleteAll(array('Entryitem.entry_id' => $id))) {
						$ds->rollback();
						$this->Session->setFlash(__d('webzash', 'Previous entry items could not be deleted.'), 'danger');
						return;
					}

					/* Save new entry items */
					foreach ($entryitemdata as $id => $itemdata) {
						$itemdata['Entryitem']['entry_id'] = $this->Entry->id;
						$this->Entryitem->create();
						if (!$this->Entryitem->save($itemdata)) {
							foreach ($this->Entryitem->validationErrors as $field => $msg) {
								$errmsg = $msg[0];
								break;
							}
							$ds->rollback();
							$this->Session->setFlash(__d('webzash', 'Failed to save entry ledgers. Error is : %s', $errmsg), 'danger');
							return;
						}
					}

					$tempentry = $this->Entry->read(null, $this->Entry->id);
					if (!$tempentry) {
						$this->Session->setFlash(__d('webzash', 'Oh snap ! Failed to update entry. Please, try again.'), 'danger');
						$ds->rollback();
						return;
					}
					$entryNumber = h(toEntryNumber(
						$tempentry['Entry']['number'],
						$entrytype['Entrytype']['id']
					));

					$this->Log->add('Edited ' . $entrytype['Entrytype']['name'] . ' entry numbered ' . $entryNumber, 1);
					$ds->commit();

					$this->Session->setFlash(__d('webzash',
						'%s entry numbered "%s" updated.',
						$entrytype['Entrytype']['name'],
						$entryNumber), 'success');

					return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
				} else {
					$ds->rollback();
					$this->Session->setFlash(__d('webzash', 'Failed to update entry. Please, try again.'), 'danger');
					return;
				}
			} else {
				$this->Session->setFlash(__d('webzash', 'No data. Please, try again.'), 'danger');
				return;
			}
		} else {
			$entry['Entry']['date'] = dateFromSql($entry['Entry']['date']);
			$this->request->data = $entry;
			return;
		}
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
		/* Check for valid entry type */
		if (empty($entrytypeLabel)) {
			$this->Session->setFlash(__d('webzash', 'Entry type not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}
		$entrytype = $this->Entrytype->find('first', array('conditions' => array('Entrytype.label' => $entrytypeLabel)));
		if (!$entrytype) {
			$this->Session->setFlash(__d('webzash', 'Entry type not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}

		/* GET access not allowed */
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}

		/* Check if valid id */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'Entry not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}

		/* Check if entry exists */
		$entry = $this->Entry->findById($id);
		if (!$entry) {
			$this->Session->setFlash(__d('webzash', 'Entry not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}

		$ds = $this->Entry->getDataSource();
		$ds->begin();

		/* Delete entry items */
		if (!$this->Entryitem->deleteAll(array('Entryitem.entry_id' => $id))) {
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'Failed to delete entry items. Please, try again.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'show', $entrytype['Entrytype']['label']));
		}

		/* Delete entry */
		if (!$this->Entry->delete($id)) {
			$ds->rollback();
			$this->Session->setFlash(__d('webzash', 'Failed to delete entry. Please, try again.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'show', $entrytype['Entrytype']['label']));
		}

		$entryNumber = h(toEntryNumber($entry['Entry']['number'], $entrytype['Entrytype']['id']));

		$this->Log->add('Deleted ' . $entrytype['Entrytype']['name'] . ' entry numbered ' . $entryNumber, 1);
		$ds->commit();

		$this->Session->setFlash(__d('webzash', '%s entry numbered "%s" deleted.',
			$entrytype['Entrytype']['name'], $entryNumber), 'success');

		/* Return to the original URL from where the delete method was called */
		$orig_return_url = $this->referer();
		if (strpos($orig_return_url, '/entries/view') !== false) {
			/* If delete called within entry view, then return to entires index page */
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		} else {
			$return_url = preg_replace('/\/page:(\d+)/', '', $orig_return_url, -1);
			return $this->redirect($return_url);
		}
	}

/**
 * email method
 *
 * @param string $entrytypeLabel
 * @param string $id
 * @return void
 */
	public function email($id = null) {

		App::uses('Validation', 'Utility');

		$this->layout = false;

		/* GET access not allowed */
		if ($this->request->is('get')) {
			$data = array(
				'status' => 'error',
				'msg' => __d('webzash', 'Method not allowed.'),
			);
			$this->set('data', $data);
			return;
		}

		/* Check if valid id */
		if (empty($id)) {
			$data = array(
				'status' => 'error',
				'msg' => __d('webzash', 'Entry not specified.'),
			);
			$this->set('data', $data);
			return;
		}

		/* Check if entry exists */
		$entry = $this->Entry->findById($id);
		if (!$entry) {
			$data = array(
				'status' => 'error',
				'msg' => __d('webzash', 'Entry not found.'),
			);
			$this->set('data', $data);
			return;
		}

		/* On POST */
		if ($this->request->is('post') || $this->request->is('put')) {
			if (!empty($this->request->data)) {
				if (!Validation::email($this->request->data['email'])) {
					$data = array(
						'status' => 'error',
						'msg' => __d('webzash', 'Invalid email specified.'),
					);
					$this->set('data', $data);
					return;
				}

				/* Get entry type */
				$entrytype = $this->Entrytype->findById($entry['Entry']['entrytype_id']);
				if (!$entrytype) {
					$data = array(
						'status' => 'error',
						'msg' => __d('webzash', 'Invalid entry type.'),
					);
					$this->set('data', $data);
					return;
				}

				/* Get entry items */
				$entryitems = array();
				$rawentryitems = $this->Entryitem->find('all', array(
					'conditions' => array('Entryitem.entry_id' => $id),
				));
				foreach ($rawentryitems as $row => $entryitem) {
					if ($entryitem['Entryitem']['dc'] == 'D') {
						$entryitems[$row] = array(
							'dc' => 'D',
							'ledger_id' => $entryitem['Entryitem']['ledger_id'],
							'ledger_name' => $this->Ledger->getName($entryitem['Entryitem']['ledger_id']),
							'dr_amount' => toCurrency('D', $entryitem['Entryitem']['amount']),
							'cr_amount' => '',
						);
					} else {
						$entryitems[$row] = array(
							'dc' => 'C',
							'ledger_id' => $entryitem['Entryitem']['ledger_id'],
							'ledger_name' => $this->Ledger->getName($entryitem['Entryitem']['ledger_id']),
							'dr_amount' => '',
							'cr_amount' => toCurrency('C', $entryitem['Entryitem']['amount']),
						);
					}
				}

				/* Sending email */
				$viewVars = array(
					'entry' => $entry,
					'entryitems' => $entryitems,
					'entrytype' => $entrytype,
				);
				$email_status = $this->Generic->sendEmail(
					$this->request->data['email'],
					h($entrytype['Entrytype']['name']) . ' Number ' .
					$this->getEntryNumber($entry['Entry']['number'], $entry['Entry']['entrytype_id']),
					'entry_email', $viewVars,
					Configure::read('Account.email_use_default'), false
				);
				if ($email_status) {
					$data = array(
						'status' => 'success',
						'msg' => __d('webzash', 'Email sent.'),
					);
				} else {
					$data = array(
						'status' => 'error',
						'msg' => __d('webzash', 'Failed to send email. Please check your email settings.'),
					);
				}
				$this->set('data', $data);
				return;
			} else {
				$data = array(
					'status' => 'error',
					'msg' => __d('webzash', 'No data. Please, try again.'),
				);
				$this->set('data', $data);
				return;
			}
		}
		return;
	}

/**
 * download method
 *
 * @param string $id
 * @return void
 */
	public function download($id = null) {

		$this->layout = false;

		/* Check if valid id */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'Entry not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}

		/* Check if entry exists */
		$entry = $this->Entry->findById($id);
		if (!$entry) {
			$this->Session->setFlash(__d('webzash', 'Entry not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}

		/* Get entry type */
		$entrytype = $this->Entrytype->findById($entry['Entry']['entrytype_id']);
		if (!$entrytype) {
			$this->Session->setFlash(__d('webzash', 'Invalid entry type.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}

		/* Get entry items */
		$entryitems = array();
		$rawentryitems = $this->Entryitem->find('all', array(
			'conditions' => array('Entryitem.entry_id' => $id),
		));
		foreach ($rawentryitems as $row => $entryitem) {
			if ($entryitem['Entryitem']['dc'] == 'D') {
				$entryitems[$row] = array(
					'dc' => 'D',
					'ledger_id' => $entryitem['Entryitem']['ledger_id'],
					'ledger_name' => $this->Ledger->getName($entryitem['Entryitem']['ledger_id']),
					'dr_amount' => toCurrency('D', $entryitem['Entryitem']['amount']),
					'cr_amount' => '',
				);
			} else {
				$entryitems[$row] = array(
					'dc' => 'C',
					'ledger_id' => $entryitem['Entryitem']['ledger_id'],
					'ledger_name' => $this->Ledger->getName($entryitem['Entryitem']['ledger_id']),
					'dr_amount' => '',
					'cr_amount' => toCurrency('C', $entryitem['Entryitem']['amount']),
				);
			}
		}

		$entryNumber = $this->getEntryNumber($entry['Entry']['number'], $entry['Entry']['entrytype_id']);

		$this->set('entry', $entry);
		$this->set('entrytype', $entrytype);
		$this->set('entryitems', $entryitems);

		/* Download */
		$this->layout = false;
		$view = new View($this, false);
		$response =  $view->render('download');
		$this->response->body($response);
		$this->response->type('text/html');
		$this->response->download($entrytype['Entrytype']['name'] . '_' . $entryNumber . '.html');

		return $this->response;
	}

/**
 * print preview method
 *
 * @param string $id
 * @return void
 */
	public function printpreview($id = null) {

		$this->layout = false;

		/* Check if valid id */
		if (empty($id)) {
			$this->Session->setFlash(__d('webzash', 'Entry not specified.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}

		/* Check if entry exists */
		$entry = $this->Entry->findById($id);
		if (!$entry) {
			$this->Session->setFlash(__d('webzash', 'Entry not found.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}

		/* Get entry type */
		$entrytype = $this->Entrytype->findById($entry['Entry']['entrytype_id']);
		if (!$entrytype) {
			$this->Session->setFlash(__d('webzash', 'Invalid entry type.'), 'danger');
			return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
		}

		/* Get entry items */
		$entryitems = array();
		$rawentryitems = $this->Entryitem->find('all', array(
			'conditions' => array('Entryitem.entry_id' => $id),
		));
		foreach ($rawentryitems as $row => $entryitem) {
			if ($entryitem['Entryitem']['dc'] == 'D') {
				$entryitems[$row] = array(
					'dc' => 'D',
					'ledger_id' => $entryitem['Entryitem']['ledger_id'],
					'ledger_name' => $this->Ledger->getName($entryitem['Entryitem']['ledger_id']),
					'dr_amount' => toCurrency('D', $entryitem['Entryitem']['amount']),
					'cr_amount' => '',
				);
			} else {
				$entryitems[$row] = array(
					'dc' => 'C',
					'ledger_id' => $entryitem['Entryitem']['ledger_id'],
					'ledger_name' => $this->Ledger->getName($entryitem['Entryitem']['ledger_id']),
					'dr_amount' => '',
					'cr_amount' => toCurrency('C', $entryitem['Entryitem']['amount']),
				);
			}
		}

		$entryNumber = $this->getEntryNumber($entry['Entry']['number'], $entry['Entry']['entrytype_id']);

		$this->set('entry', $entry);
		$this->set('entrytype', $entrytype);
		$this->set('entryitems', $entryitems);

		return;
	}

/**
 * Return full entry number with padding, prefix and suffix
 *
 * @param string $number Entry number
 * @param string $entrytype_id Entry type id
 * @return string Full entry number with padding, prefix and suffix
 */
	public function getEntryNumber($number, $entrytype_id) {
		return Configure::read('Account.ET.' . $entrytype_id . '.prefix') .
			str_pad($number, Configure::read('Account.ET.' . $entrytype_id . '.zero_padding'), '0', STR_PAD_LEFT) .
			Configure::read('Account.ET.' . $entrytype_id . '.suffix');
	}

/**
 * Add a row in the entry via ajax
 *
 * @param string $addType
 * @return void
 */
	function addrow($restriction_bankcash) {

		$this->layout = null;

		/* Ledger selection */
		$ledgers = new LedgerTree();
		$ledgers->Group = &$this->Group;
		$ledgers->Ledger = &$this->Ledger;
		$ledgers->current_id = -1;
		$ledgers->restriction_bankcash = $restriction_bankcash;
		$ledgers->build(0);
		$ledgers->toList($ledgers, -1);
		$ledgers_disabled = array();
		$this->set('ledger_options', $ledgers->ledgerList);
	}

	public function beforeFilter() {
		parent::beforeFilter();

		/* Skip the ajax/javascript fields from Security component to prevent request being blackholed */
		$this->Security->unlockedFields = array('Entryitem');

		$this->Security->unlockedActions = array('email');

		/* Check if acccount is locked */
		if (Configure::read('Account.locked') == 1) {
			if ($this->action == 'add' || $this->action == 'delete') {
				$this->Session->setFlash(__d('webzash', 'Sorry, no changes are possible since the account is locked.'), 'danger');
				return $this->redirect(array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index'));
			}
		}
	}

	/* Authorization check */
	public function isAuthorized($user) {
		if ($this->action === 'index') {
			return $this->Permission->is_allowed('view entry');
		}

		if ($this->action === 'show') {
			return $this->Permission->is_allowed('view entry');
		}

		if ($this->action === 'view') {
			return $this->Permission->is_allowed('view entry');
		}

		if ($this->action === 'add') {
			return $this->Permission->is_allowed('add entry');
		}

		if ($this->action === 'edit') {
			return $this->Permission->is_allowed('edit entry');
		}

		if ($this->action === 'delete') {
			return $this->Permission->is_allowed('delete entry');
		}

		if ($this->action === 'email') {
			return $this->Permission->is_allowed('view entry');
		}

		if ($this->action === 'download') {
			return $this->Permission->is_allowed('view entry');
		}

		if ($this->action === 'printpreview') {
			return $this->Permission->is_allowed('view entry');
		}

		if ($this->action === 'addrow') {
			if ($this->Permission->is_allowed('add entry') ||
				$this->Permission->is_allowed('edit entry')) {
				return true;
			} else {
				return false;
			}
		}

		return parent::isAuthorized($user);
	}
}
