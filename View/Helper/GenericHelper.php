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
* Webzash Plugin Generic Helper
*
* @package Webzash
* @subpackage Webzash.View
*/
class GenericHelper extends AppHelper {

/**
 * Helper method to return the tag
 */
	function showTag($id) {
		if (empty($id)) {
			return '';
		}

		/* Load the Tag model */
		App::import("Webzash.Model", "Tag");
		$model = new Tag();

		/* Find and return the tag */
		$tag = $model->findById($id);
		if (empty($tag)) {
			return '';
		} else {
			/* TODO Return tag in html format */
			return $tag['Tag']['title'];
		}
	}

/**
 * Helper method to return the entry type
 */
	function showEntrytype($id) {
		if (empty($id)) {
			return array('(Unknown)', '');
		}

		/* Load the Entry type model */
		App::import("Webzash.Model", "Entrytype");
		$model = new Entrytype();

		/* Find and return the entry type */
		$entrytype = $model->findById($id);
		if (empty($entrytype)) {
			return array('(Unknown)', '');
		} else {
			return array($entrytype['Entrytype']['name'], $entrytype['Entrytype']['label']);
		}
	}
}
