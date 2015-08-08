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
 * Webzash Plugin Search Controller
 *
 * @package Webzash
 * @subpackage Webzash.controllers
 */
class SearchController extends WebzashAppController {

	public $uses = array('Webzash.Group', 'Webzash.Ledger', 'Webzash.Entry',
		'Webzash.Entryitem', 'Webzash.Entrytype', 'Webzash.Tag');

/**
 * index method
 *
 * @return void
 */
	public function index() {

		$this->set('title_for_layout', __d('webzash', 'Search'));

		$this->set('showEntries', false);

		/* Ledger selection */
		$ledgers = new LedgerTree();
		$ledgers->Group = &$this->Group;
		$ledgers->Ledger = &$this->Ledger;
		$ledgers->current_id = -1;
		$ledgers->restriction_bankcash = 1;
		$ledgers->default_text = '(ALL)';
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

		/* Entrytypes */
		$entrytype_options = array();
		$entrytype_options[0] = '(ALL)';
		$rawentrytypes = $this->Entrytype->find('all', array(
			'order' => 'Entrytype.id'
		));
		foreach ($rawentrytypes as $row => $rawentrytype) {
			$entrytype_options[$rawentrytype['Entrytype']['id']] = h($rawentrytype['Entrytype']['name']);
		}
		$this->set('entrytype_options', $entrytype_options);

		/* Tags */
		$tag_options = array();
		$tag_options[0] = '(ALL)';
		$rawtags = $this->Tag->find('all', array(
			'order' => 'Tag.title'
		));
		foreach ($rawtags as $row => $rawtag) {
			$tag_options[$rawtag['Tag']['id']] = h($rawtag['Tag']['title']);
		}
		$this->set('tag_options', $tag_options);

		if ($this->request->is('post') || $this->request->is('put')) {

			$ledger_ids = '';
			if (empty($this->request->data['Search']['ledger_ids'])) {
				$ledger_ids = '0';
			} else {
				if (in_array('0', $this->request->data['Search']['ledger_ids'])) {
					$ledger_ids = '0';
				} else {
					$ledger_ids = implode(',', $this->request->data['Search']['ledger_ids']);
				}
			}

			$entrytype_ids = '';
			if (empty($this->request->data['Search']['entrytype_ids'])) {
				$entrytype_ids = '0';
			} else {
				if (in_array('0', $this->request->data['Search']['entrytype_ids'])) {
					$entrytype_ids = '0';
				} else {
					$entrytype_ids = implode(',', $this->request->data['Search']['entrytype_ids']);
				}
			}

			$tag_ids = '';
			if (empty($this->request->data['Search']['tag_ids'])) {
				$tag_ids = '0';
			} else {
				if (in_array('0', $this->request->data['Search']['tag_ids'])) {
					$tag_ids = '0';
				} else {
					$tag_ids = implode(',', $this->request->data['Search']['tag_ids']);
				}
			}

			return $this->redirect(array(
				'plugin' => 'webzash', 'controller' => 'search', 'action' => 'index',
				'search' => 1,
				'ledger_ids' => $ledger_ids,
				'entrytype_ids' => $entrytype_ids,
				'entrynumber_restriction' => $this->request->data['Search']['entrynumber_restriction'],
				'entrynumber1' => $this->request->data['Search']['entrynumber1'],
				'entrynumber2' => $this->request->data['Search']['entrynumber2'],
				'amount_dc' => $this->request->data['Search']['amount_dc'],
				'amount_restriction' => $this->request->data['Search']['amount_restriction'],
				'amount1' => $this->request->data['Search']['amount1'],
				'amount2' => $this->request->data['Search']['amount2'],
				'fromdate' => $this->request->data['Search']['fromdate'],
				'todate' => $this->request->data['Search']['todate'],
				'tag_ids' => $tag_ids,
				'narration' => $this->request->data['Search']['narration']
			));
		}

		/* Check if search is active */
		if (empty($this->passedArgs['search']) || $this->passedArgs['search'] != 1) {
			return;
		}

		/* Initialize data from passedArgs */
		$this->request->data['Search']['ledger_ids'] =
			explode(',', $this->passedArgs['ledger_ids']);
		$this->request->data['Search']['entrytype_ids'] =
			explode(',', $this->passedArgs['entrytype_ids']);
		$this->request->data['Search']['entrynumber_restriction'] =
			$this->passedArgs['entrynumber_restriction'];
		$this->request->data['Search']['entrynumber1'] =
			$this->passedArgs['entrynumber1'];
		$this->request->data['Search']['entrynumber2'] =
			$this->passedArgs['entrynumber2'];
		$this->request->data['Search']['amount_dc'] =
			$this->passedArgs['amount_dc'];
		$this->request->data['Search']['amount_restriction'] =
			$this->passedArgs['amount_restriction'];
		$this->request->data['Search']['amount1'] =
			$this->passedArgs['amount1'];
		$this->request->data['Search']['amount2'] =
			$this->passedArgs['amount2'];
		$this->request->data['Search']['fromdate'] =
			$this->passedArgs['fromdate'];
		$this->request->data['Search']['todate'] =
			$this->passedArgs['todate'];
		$this->request->data['Search']['tag_ids'] =
			explode(',', $this->passedArgs['tag_ids']);
		$this->request->data['Search']['narration'] =
			$this->passedArgs['narration'];

		/* Setup search conditions */
		$conditions = array();

		if (!empty($this->passedArgs['ledger_ids'])) {
			if (!in_array('0', $this->request->data['Search']['ledger_ids'])) {
				$conditions['Entryitem.ledger_id'] =
					$this->request->data['Search']['ledger_ids'];
			}
		}

		if (!empty($this->passedArgs['entrytype_ids'])) {
			if (!in_array('0', $this->request->data['Search']['entrytype_ids'])) {
				$conditions['Entry.entrytype_id'] =
					$this->request->data['Search']['entrytype_ids'];
			}
		}

		if (!empty($this->passedArgs['entrynumber1'])) {
			if ($this->passedArgs['entrynumber_restriction'] == 1) {
				/* Equal to */
				$conditions['Entry.number'] = $this->passedArgs['entrynumber1'];
			} else if ($this->passedArgs['entrynumber_restriction'] == 2) {
				/* Less than or equal to */
				$conditions['Entry.number <='] = $this->passedArgs['entrynumber1'];
			} else if ($this->passedArgs['entrynumber_restriction'] == 3) {
				/* Greater than or equal to */
				$conditions['Entry.number >='] = $this->passedArgs['entrynumber1'];
			} else if ($this->passedArgs['entrynumber_restriction'] == 4) {
				/* In between */
				if (!empty($this->passedArgs['entrynumber2'])) {
					$conditions['Entry.number >='] = $this->passedArgs['entrynumber1'];
					$conditions['Entry.number <='] = $this->passedArgs['entrynumber2'];
				} else {
					$conditions['Entry.number >='] = $this->passedArgs['entrynumber1'];
				}
			}
		}

		if ($this->passedArgs['amount_dc'] == 'D') {
			/* Dr */
			$conditions['Entryitem.dc'] = 'D';
		} else if ($this->passedArgs['amount_dc'] == 'C') {
			/* Cr */
			$conditions['Entryitem.dc'] = 'C';
		}

		if (!empty($this->passedArgs['amount1'])) {
			if ($this->passedArgs['amount_restriction'] == 1) {
				/* Equal to */
				$conditions['Entryitem.amount'] = $this->passedArgs['amount1'];
			} else if ($this->passedArgs['amount_restriction'] == 2) {
				/* Less than or equal to */
				$conditions['Entryitem.amount <='] = $this->passedArgs['amount1'];
			} else if ($this->passedArgs['amount_restriction'] == 3) {
				/* Greater than or equal to */
				$conditions['Entryitem.amount >='] = $this->passedArgs['amount1'];
			} else if ($this->passedArgs['amount_restriction'] == 4) {
				/* In between */
				if (!empty($this->passedArgs['amount2'])) {
					$conditions['Entryitem.amount >='] = $this->passedArgs['amount1'];
					$conditions['Entryitem.amount <='] = $this->passedArgs['amount2'];
				} else {
					$conditions['Entryitem.amount >='] = $this->passedArgs['amount1'];
				}
			}
		}

		if (!empty($this->passedArgs['fromdate'])) {
			/* TODO : Validate date */
			$fromdate = dateToSql($this->passedArgs['fromdate']);
			$conditions['Entry.date >='] = $fromdate;
		}

		if (!empty($this->passedArgs['todate'])) {
			/* TODO : Validate date */
			$todate = dateToSql($this->passedArgs['todate']);
			$conditions['Entry.date <='] = $todate;
		}

		if (!empty($this->passedArgs['tag_ids'])) {
			if (!in_array('0', $this->request->data['Search']['tag_ids'])) {
				$conditions['Entry.tag_id'] =
					$this->request->data['Search']['tag_ids'];
			}
		}

		if (!empty($this->passedArgs['narration'])) {
			$conditions['Entry.narration LIKE'] = '%' . $this->passedArgs['narration'] . '%';
		}

		/* Pass varaibles to view which are used in Helpers */
		$this->set('allTags', $this->Tag->fetchAll());

		/* Setup pagination */
		$this->CustomPaginator->settings = array(
			'Entry' => array(
				'fields' => array(
					'Entry.id', 'Entry.tag_id', 'Entry.entrytype_id', 'Entry.number', 'Entry.date', 'Entry.dr_total', 'Entry.cr_total', 'Entry.narration',
					'Entryitem.id', 'Entryitem.entry_id', 'Entryitem.ledger_id', 'Entryitem.amount', 'Entryitem.dc', 'Entryitem.reconciliation_date',
				),
				'limit' => $this->Session->read('Wzsetting.row_count'),
				'order' => array('Entry.date' => 'asc'),
				'conditions' => $conditions,
				'joins' => array(
					array(
						'table' => 'entryitems',
						'alias' => 'Entryitem',
						'conditions' => array(
							'Entry.id = Entryitem.entry_id'
						)
					),
				),
			),
		);

		$this->set('entries', $this->CustomPaginator->paginate('Entry'));
		$this->set('showEntries', true);

		return;
	}

	public function beforeFilter() {
		parent::beforeFilter();

		/* Skip the ajax/javascript fields from Security component to prevent request being blackholed */
		$this->Security->unlockedFields = array('ledger_ids');
	}

	/* Authorization check */
	public function isAuthorized($user) {

		if ($this->action === 'index') {
			return $this->Permission->is_allowed('search');
		}

		return parent::isAuthorized($user);
	}
}
