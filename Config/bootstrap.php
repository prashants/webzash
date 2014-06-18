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

/**** This file contains common functions used throughout the application ****/

/**
 * Perform a decimal level calculations on two numbers
 *
 * Multiply the float by 100, convert it to integer,
 * Perform the integer operation and then divide the result
 * by 100 and return the result
 *
 * @param1 float number 1
 * @param2 float number 2
 * @op string operation to be performed
 * @return float result of the operation
*/

function calculate($param1 = 0, $param2 = 0, $op = '') {
	$result = 0;
	$param1 = $param1 * 100;
	$param2 = $param2 * 100;
	$param1 = (int)round($param1, 0);
	$param2 = (int)round($param2, 0);
	switch ($op)
	{
		case '+':
			$result = $param1 + $param2;
			break;
		case '-':
			$result = $param1 - $param2;
			break;
		case '==':
			if ($param1 == $param2) {
				return TRUE;
			} else {
				return FALSE;
			}
			break;
		case '!=':
			if ($param1 != $param2) {
				return TRUE;
			} else {
				return FALSE;
			}
			break;
		case '<':
			if ($param1 < $param2) {
				return TRUE;
			} else {
				return FALSE;
			}
			break;
		case '>':
			if ($param1 > $param2) {
				return TRUE;
			} else {
				return FALSE;
			}
			break;
	}
	$result = $result/100;
	return $result;
}
