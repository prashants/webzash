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

class WebzashSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $groups = array(
		'id' => array('type' => 'biginteger', 'null' => false,
			'unsigned' => true, 'length' => 18, 'key' => 'primary'),
		'parent_id' => array('type' => 'biginteger', 'null' => true,
			'default' => 0, 'unsigned' => true, 'length' => 18, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'key' => 'unique'),
		'code' => array('type' => 'string', 'null' => true,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'key' => 'unique'),
		'affects_gross' => array('type' => 'integer', 'null' => false,
			'default' => 0, 'unsigned' => true, 'length' => 1),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'unique_id' => array('column' => 'id', 'unique' => 1),
			'name' => array('column' => 'name', 'unique' => 1),
			'code' => array('column' => 'code', 'unique' => 1),
			'id' => array('column' => 'id', 'unique' => 0),
			'parent_id' => array('column' => 'parent_id', 'unique' => 0)

		),
		'tableParameters' => array('charset' => 'utf8',
			'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB'
		)
	);

	public $ledgers = array(
		'id' => array('type' => 'biginteger', 'null' => false,
			'unsigned' => true, 'length' => 18, 'key' => 'primary'),
		'group_id' => array('type' => 'biginteger', 'null' => false,
			'unsigned' => true, 'length' => 18, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'key' => 'unique'),
		'code' => array('type' => 'string', 'null' => true,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'key' => 'unique'),
		'op_balance' => array('type' => 'decimal', 'null' => false,
			'default' => '0.00', 'unsigned' => true, 'length' => '25,2'),
		'op_balance_dc' => array('type' => 'string', 'null' => false,
			'length' => 1,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'type' => array('type' => 'integer', 'null' => false,
			'default' => '0', 'unsigned' => true, 'length' => 2),
		'reconciliation' => array('type' => 'integer', 'null' => false,
			'default' => '0', 'unsigned' => true, 'length' => 1),
		'notes' => array('type' => 'string', 'null' => false,
			'length' => 500,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'unique_id' => array('column' => 'id', 'unique' => 1),
			'name' => array('column' => 'name', 'unique' => 1),
			'code' => array('column' => 'code', 'unique' => 1),
			'id' => array('column' => 'id', 'unique' => 0),
			'group_id' => array('column' => 'group_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8',
			'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB'
		)
	);

	public $entrytypes = array(
		'id' => array('type' => 'biginteger', 'null' => false,
			'unsigned' => true, 'length' => 18, 'key' => 'primary'),
		'label' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'key' => 'unique'),
		'name' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'base_type' => array('type' => 'integer', 'null' => false,
			'default' => '0', 'unsigned' => true, 'length' => 2),
		'numbering' => array('type' => 'integer', 'null' => false,
			'default' => '1', 'unsigned' => true, 'length' => 2),
		'prefix' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'suffix' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'zero_padding' => array('type' => 'integer', 'null' => false,
			'default' => '0', 'unsigned' => true, 'length' => 2),
		'restriction_bankcash' => array('type' => 'integer', 'null' => false,
			'default' => '1', 'unsigned' => true, 'length' => 2),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'unique_id' => array('column' => 'id', 'unique' => 1),
			'label' => array('column' => 'label', 'unique' => 1),
			'id' => array('column' => 'id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8',
			'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB'
		)
	);

	public $tags = array(
		'id' => array('type' => 'biginteger', 'null' => false,
			'unsigned' => true, 'length' => 18, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'key' => 'unique'),
		'color' => array('type' => 'string', 'null' => false,
			'length' => 6, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'background' => array('type' => 'string', 'null' => false,
			'length' => 6, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'unique_id' => array('column' => 'id', 'unique' => 1),
			'title' => array('column' => 'title', 'unique' => 1),
			'id' => array('column' => 'id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8',
			'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB'
		)
	);

	public $entries = array(
		'id' => array('type' => 'biginteger', 'null' => false,
			'unsigned' => true, 'length' => 18, 'key' => 'primary'),
		'tag_id' => array('type' => 'biginteger', 'null' => true,
			'unsigned' => true, 'default' => null, 'length' => 18, 'key' => 'index'),
		'entrytype_id' => array('type' => 'biginteger', 'null' => false,
			'unsigned' => true, 'length' => 18, 'key' => 'index'),
		'number' => array('type' => 'biginteger', 'null' => true,
			'unsigned' => true, 'default' => null, 'length' => 18),
		'date' => array('type' => 'date', 'null' => false),
		'dr_total' => array('type' => 'decimal', 'null' => false,
			'unsigned' => true, 'default' => '0.00', 'length' => '25,2'),
		'cr_total' => array('type' => 'decimal', 'null' => false,
			'unsigned' => true, 'default' => '0.00', 'length' => '25,2'),
		'narration' => array('type' => 'string', 'null' => false,
			'length' => 500,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'unique_id' => array('column' => 'id', 'unique' => 1),
			'id' => array('column' => 'id', 'unique' => 0),
			'tag_id' => array('column' => 'tag_id', 'unique' => 0),
			'entrytype_id' => array('column' => 'entrytype_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8',
			'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB'
		)
	);

	public $entryitems = array(
		'id' => array('type' => 'biginteger', 'null' => false,
			'unsigned' => true, 'length' => 18, 'key' => 'primary'),
		'entry_id' => array('type' => 'biginteger', 'null' => false,
			'unsigned' => true, 'length' => 18, 'key' => 'index'),
		'ledger_id' => array('type' => 'biginteger', 'null' => false,
			'unsigned' => true, 'length' => 18, 'key' => 'index'),
		'amount' => array('type' => 'decimal', 'null' => false,
			'unsigned' => true, 'default' => '0.00', 'length' => '25,2'),
		'dc' => array('type' => 'string', 'null' => false,
			'length' => 1,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'reconciliation_date' => array('type' => 'date',
			'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'unique_id' => array('column' => 'id', 'unique' => 1),
			'id' => array('column' => 'id', 'unique' => 0),
			'entry_id' => array('column' => 'entry_id', 'unique' => 0),
			'ledger_id' => array('column' => 'ledger_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8',
			'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB'
		)
	);

	public $settings = array(
		'id' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'length' => 1, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'address' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'email' => array('type' => 'string', 'null' => false,
			'length' => 255, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'fy_start' => array('type' => 'date', 'null' => false),
		'fy_end' => array('type' => 'date', 'null' => false),
		'currency_symbol' => array('type' => 'string', 'null' => false,
			'length' => 100,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'currency_format' => array('type' => 'string', 'null' => false,
			'length' => 100,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'decimal_places' => array('type' => 'integer', 'null' => false,
			'default' => '2', 'unsigned' => true, 'length' => 2),
		'date_format' => array('type' => 'string', 'null' => false,
			'length' => 100,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'timezone' => array('type' => 'string', 'null' => false,
			'length' => 100,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'manage_inventory' => array('type' => 'integer', 'null' => false,
			'default' => '0', 'unsigned' => true, 'length' => 1),
		'account_locked' => array('type' => 'integer', 'null' => false,
			'default' => '0', 'unsigned' => true, 'length' => 1),
		'email_use_default' => array('type' => 'integer', 'null' => false,
			'default' => '0', 'unsigned' => true, 'length' => 1),
		'email_protocol' => array('type' => 'string', 'null' => false,
			'length' => 10,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'email_host' => array('type' => 'string', 'null' => false,
			'length' => 255, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'email_port' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'length' => 5),
		'email_tls' => array('type' => 'integer', 'null' => false,
			'default' => '0', 'unsigned' => true, 'length' => 1),
		'email_username' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'email_password' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'email_from' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'print_paper_height' => array('type' => 'decimal', 'null' => false,
			'default' => '0.000', 'unsigned' => false, 'length' => '10,3'),
		'print_paper_width' => array('type' => 'decimal', 'null' => false,
			'default' => '0.000', 'unsigned' => false, 'length' => '10,3'),
		'print_margin_top' => array('type' => 'decimal', 'null' => false,
			'default' => '0.000', 'unsigned' => false, 'length' => '10,3'),
		'print_margin_bottom' => array('type' => 'decimal', 'null' => false,
			'default' => '0.000', 'unsigned' => false, 'length' => '10,3'),
		'print_margin_left' => array('type' => 'decimal', 'null' => false,
			'default' => '0.000', 'unsigned' => false, 'length' => '10,3'),
		'print_margin_right' => array('type' => 'decimal', 'null' => false,
			'default' => '0.000', 'unsigned' => false, 'length' => '10,3'),
		'print_orientation' => array('type' => 'string', 'null' => false,
			'length' => 1,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'print_page_format' => array('type' => 'string', 'null' => false,
			'length' => 1,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'database_version' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'length' => 10),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'unique_id' => array('column' => 'id', 'unique' => 1),
			'id' => array('column' => 'id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8',
			'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB'
		)
	);

	public $logs = array(
		'id' => array('type' => 'biginteger', 'null' => false,
			'unsigned' => true, 'length' => 18, 'key' => 'primary'),
		'date' => array('type' => 'datetime', 'null' => false),
		'level' => array('type' => 'integer', 'null' => false,
			'unsigned' => true, 'length' => 1),
		'host_ip' => array('type' => 'string', 'null' => false,
			'length' => 25,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'user' => array('type' => 'string', 'null' => false,
			'length' => 25,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'url' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'user_agent' => array('type' => 'string', 'null' => false,
			'length' => 100,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'message' => array('type' => 'string', 'null' => false,
			'length' => 255,
			'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'unique_id' => array('column' => 'id', 'unique' => 1),
			'id' => array('column' => 'id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8',
			'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'
		)
	);
}
