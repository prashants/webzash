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
 * Webzash Plugin Log Model
 *
 * @package Webzash
 * @subpackage Webzash.Model
 */
class Attachment extends WebzashAppModel {

        /* Validation rules for the Log table */
        public $validate = array(
			'entry_id' => array(
				'rule1' => array(
					'rule' => 'notEmpty',
					'message' => 'Entry id cannot be empty',
					'required' => true,
					'allowEmpty' => false,
				),
				'rule2' => array(
					'rule' => 'numeric',
					'message' => 'Entry id is not a valid number',
					'required' => true,
					'allowEmpty' => false,
				),
				'rule3' => array(
					'rule' => array('maxLength', 18),
					'message' => 'Entry id length cannot be more than 18',
					'required' => true,
					'allowEmpty' => false,
				),
				'rule4' => array(
					'rule' => 'validEntry',
					'message' => 'Entry id is not valid',
					'required' => true,
					'allowEmpty' => false,
				),
			),
			'filename' => array(
					'rule1' => array(
							'rule' => array('maxLength', 255),
							'message' => 'Filename cannot be more than 255 characters',
							'required' => true,
							'allowEmpty' => false,
					),
			),
			'relative_path' => array(
					'rule1' => array(
							'rule' => array('maxLength', 255),
							'message' => 'Path of upload folder cannot be more than 255 characters',
							'required' => true,
							'allowEmpty' => false,
					),
			),
			'filetype' => array(
					'rule1' => array(
							'rule' => array('maxLength', 255),
							'message' => 'File type cannot be more than 255 characters',
							'required' => true,
							'allowEmpty' => false,
					),
			),
        );

/**
 * Validation - Check if entry is valid
 */
	public function validEntry($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}

		$value = $values[0];

		/* Load the Entry model */
		App::import("Webzash.Model", "Entry");
		$Entry = new Entry();

		/* Check if entry exists */
		if ($Entry->exists($value)) {
			return true;
		} else {
			return false;
		}
	}
}
