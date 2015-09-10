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
 * This function formats the currency as per the currency format in account settings
 *
 * $input format is xxxxxxx.xx
 */
function curreny_format($input) {
	switch (Configure::read('Account.currency_format')) {
		case 'none':
			return $input;
		case '##,###.##':
			return _currency_2_3_style($input);
			break;
		case '##,##.##':
			return _currency_2_2_style($input);
			break;
		case "###,###.##":
			return _currency_3_3_style($input);
			break;
		default:
			die("Invalid curreny format selected.");
	}
}

/*********************** ##,###.## FORMAT ***********************/
function _currency_2_3_style($num)
{
	$decimal_places = Configure::read('Account.decimal_places');

	$pos = strpos((string)$num, ".");
	if ($pos === false) {
		if ($decimal_places == 2) {
			$decimalpart = "00";
		} else {
			$decimalpart = "000";
		}
	} else {
		$decimalpart = substr($num, $pos + 1, $decimal_places);
		$num = substr($num, 0, $pos);
	}

	if (strlen($num) > 3) {
		$last3digits = substr($num, -3);
		$numexceptlastdigits = substr($num, 0, -3 );
		$formatted = _currency_2_3_style_makecomma($numexceptlastdigits);
		$stringtoreturn = $formatted . "," . $last3digits . "." . $decimalpart ;
	} elseif (strlen($num) <= 3) {
		$stringtoreturn = $num . "." . $decimalpart;
	}

	if (substr($stringtoreturn, 0, 2) == "-,") {
		$stringtoreturn = "-" . substr($stringtoreturn, 2);
	}
	return $stringtoreturn;
}

function _currency_2_3_style_makecomma($input)
{
	if (strlen($input) <= 2) {
		return $input;
	}
	$length = substr($input, 0, strlen($input) - 2);
	$formatted_input = _currency_2_3_style_makecomma($length) . "," . substr($input, -2);
	return $formatted_input;
}

/*********************** ##,##.## FORMAT ***********************/
function _currency_2_2_style($num)
{
	$decimal_places = Configure::read('Account.decimal_places');

	$pos = strpos((string)$num, ".");
	if ($pos === false) {
		if ($decimal_places == 2) {
			$decimalpart = "00";
		} else {
			$decimalpart = "000";
		}
	} else {
		$decimalpart = substr($num, $pos + 1, $decimal_places);
		$num = substr($num, 0, $pos);
	}

	if (strlen($num) > 2) {
		$last2digits = substr($num, -2);
		$numexceptlastdigits = substr($num, 0, -2);
		$formatted = _currency_2_2_style_makecomma($numexceptlastdigits);
		$stringtoreturn = $formatted . "," . $last2digits . "." . $decimalpart;
	} elseif (strlen($num) <= 2) {
		$stringtoreturn = $num . "." . $decimalpart ;
	}

	if (substr($stringtoreturn, 0, 2) == "-,") {
		$stringtoreturn = "-" . substr($stringtoreturn, 2);
	}
	return $stringtoreturn;
}

function _currency_2_2_style_makecomma($input)
{
	if (strlen($input) <= 2) {
		return $input;
	}
	$length = substr($input, 0, strlen($input) - 2);
	$formatted_input = _currency_2_2_style_makecomma($length) . "," . substr($input, -2);
	return $formatted_input;
}

/*********************** ###,###.## FORMAT ***********************/
function _currency_3_3_style($num)
{
	$decimal_places = Configure::read('Account.decimal_places');
	return number_format($num,$decimal_places,'.',',');
}