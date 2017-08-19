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

App::uses('WebzashAppModel', 'Webzash.Model');

/**
* Webzash Plugin Tag Model
*
* @package Webzash
* @subpackage Webzash.model
*/
class Tag extends WebzashAppModel {

	public $validationDomain = 'webzash';

	/* Validation rules for the Tag table */
	public $validate = array(
		'title' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Title cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isUnique',
				'message' => 'Title is already in use',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 100),
				'message' => 'Title cannot be more than 100 characters',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'color' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Color cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('between', 6, 6),
				'message' => 'Color has to be exactly 6 characters',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => 'hex',
				'message' => 'Color is not a valid hexadecimal color value',
				'required' => true,
				'allowEmpty' => false,
			),

		),
		'background' => array(
			'rule1' => array(
				'rule' => 'notBlank',
				'message' => 'Background cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('between', 6, 6),
				'message' => 'Background has to be exactly 6 characters',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => 'hex',
				'message' => 'Background is not a valid hexadecimal color value',
				'required' => true,
				'allowEmpty' => false,
			),
		),
	);

	/* Validate hex value */
	public function hex($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];

		return ctype_xdigit($value);
	}

/**
 * Method to return the tags in list form
 */
	function listAll() {
		$rawtags = $this->find('all', array('fields' => array('id', 'title'),
			'order' => 'Tag.title'));

		$tags = array(0 => '(None)');
		foreach ($rawtags as $id => $rawtag) {
			$tags[$rawtag['Tag']['id']] = h($rawtag['Tag']['title']);
		}

		return $tags;
	}

/**
 * Method to return all the tags
 */
	function fetchAll() {
		$rawtags = $this->find('all');

		$tags = array();
		foreach ($rawtags as $id => $rawtag) {
			$tags[$rawtag['Tag']['id']] = array(
				'id' => $rawtag['Tag']['id'],
				'title' => h($rawtag['Tag']['title']),
				'color' => $rawtag['Tag']['color'],
				'background' => $rawtag['Tag']['background'],
			);
		}

		return $tags;
	}
}
